<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Respaldos extends CI_Controller
{

  private $tabla = "baseDatos";
  private $controlador = "Respaldos";
  function __construct()
  {
    parent::__construct();
    $this->load->Model('CoreModel', "core");
    // $this->load->Model('LotesModel',"lotes");
    $this->load->dbutil();
    $this->load->helper('file');
	// $this->load->library('unzip');
    $this->load->helper('download');
    $this->load->add_package_path(APPPATH . 'third_party/upload_file');
    $this->load->library('uploadFile');
  }

  public function index(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      $titulo = "Crear Respaldo";
      $titulo1 = "Restaurar Respaldo";
      $datosVista = array(
        "titulo1" => $titulo1,
        "titulo" => $titulo,
        "icono" => "fas fa-cogs",
        "controlador" => $this->controlador,
      );
      $extras = array(
        'css' => array(),
        'js' => array(
          "scripts/respaldos.js?q=".uniqid(),
        ),
      );
      GblPlantilla("respaldos/respaldoHacer", $datosVista, $extras, $titulo);
    }
  }
  function RespaldoDescargar(){
	$bkname = date(dmYHis);
	$prefs = array(
	  'ignore'        => array("FE_CAT_019_CodigodeActividadEco"),
	  'format'        => 'zip',
	  'filename'      => $bkname.'.sql',
	  'add_drop'      => TRUE,
	);
	$backup = $this->dbutil->backup($prefs);
	force_download($bkname.'.zip',$backup);
  }
  function RespaldoHacer(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      $datosRespuesta["codigo"] = 403;
    } else {
      if ($this->input->method(TRUE) == "POST") {
		$backupFile = '';
		if ($_FILES['backupFile']['name'] != "") {
			$backupFileName = $_FILES['backupFile']['name'];
			if(file_exists('db/respaldo/'.$backupFileName)){
				unlink('db/respaldo/'.$backupFileName);
			}
			$this->backup = new UploadFile();
			$backup = $this->backup->subirArchivo('backupFile',$backupFileName,"./db/respaldo/");
			if ($backup['response']) {
				$backupFile = 'db/respaldo/'.$backup['info']['file_name'];
			}
		}
		if($backupFile != ""){
			$zip = new ZipArchive;
			if ($zip->open($backupFile) === TRUE) {
				$zip->extractTo('db/respaldo/'.$backup['info']['raw_name']."/");
				$zip->close();
			}
			$dbfilename = 'db/respaldo/'.$backup['info']['raw_name']."/".$backup['info']['raw_name'].".sql";
			if(file_exists($dbfilename)){
				$schema = htmlspecialchars(file_get_contents($dbfilename));
				$query = rtrim( trim($schema), "\n;");
				$query_list = explode(";", $query);
				$error = false;
				IniciarTransaccion();
				foreach($query_list as $query){
					$this->db->query($query);
				}
				if (!$error) {
					EjecutarTransaccion();
					$datosRespuesta["codigo"]=200;
				}else {
					DeshacerTransaccion();
					$datosRespuesta["codigo"]=500;
				}
			} else {
				$datosRespuesta["codigo"]=404;
			}
		} else {
			$datosRespuesta["codigo"]=502;
		}
		echo json_encode($datosRespuesta);
	  }
	}
  }
  function LoadData(){
    $datosPlantilla = TraerDatos("plantilla");
    if($datosPlantilla != ""){
      $idSucursal = $this->session->idSucursal;
      IniciarTransaccion();
      $idCargaInicial = "00000-0000-0000000";
      $cargas = 0;
      $totalCarga = 0;
      foreach ($datosPlantilla as $dato) {
        $nombre = $dato->NOMBRE;
        $marca = $dato->MARCA;
        $barcode = $dato->BARCODE;
        $proveedor = $dato->PROVEEDOR;
        $categoria = $dato->CATEGORIA;
        $presentacion = $dato->PRESENTACION;
        $unidad = $dato->UNIDAD;
        $existencia = $dato->EXISTENCIAS;
        ////////////////////////////////////////////////////////
        ///////////////////////CASO ESPECIAL////////////////////
        ////////////////////////////////////////////////////////
        $modelo = $dato->ID;
        ////////////////////////////////////////////////////////
        ///////////////////////CASO ESPECIAL////////////////////
        ////////////////////////////////////////////////////////
        $costo = trim(str_replace("$","",$dato->COSTO));
        $precioIVA = trim(str_replace("$","",$dato->PRECIO));
        $precioIVA1 = trim(str_replace("$","",$dato->PRECIO1));
        $precio = 0;
        $precio1 = 0;
        // echo $precioIVA;
        // echo $precioIVA1;
        if($precioIVA != "" && $precioIVA > 0){
          $precio = round($precioIVA / (1 + (GblTraerConfiguracion("iva")/100)),8);
        }
        $precio1 = 0;
        if($precioIVA1 != "" && $precioIVA1 > 0){
          $precio1 = round($precioIVA1 / (1 + (GblTraerConfiguracion("iva")/100)),8);
        }
        $costoIVA = round($costo * (1 + (GblTraerConfiguracion("iva")/100)),8);
        /////////////////////////
        /////////////////////////
        $Producto = TraerUnDato("producto", array("nombreProducto" => $nombre, "codProducto" => $barcode));
        if($Producto !== false){
          $idProducto = $Producto->idProducto;
          $unidadInventario = 0;
        } else {
          $unidadInventario = 1;
          //////////////////////////////////////////////////////////////
          $Categoria = TraerUnDato("productoCategoria",array("nombreProductoCategoria" => $categoria));
          if($Categoria !== false){
            $idCategoria = $Categoria->idProductoCategoria;
          } else {
            $datosCategoria = array(
              "idSucursalProductoCategoria"=>$idSucursal,
              "nombreProductoCategoria" => $categoria,
              "estadoProductoCategoria" => "Activo",
            );
            $idCategoria = GuardarDatos("productoCategoria",$datosCategoria);
          }
          //////////////////////////////////////////////////////////////

          //////////////////////////////////////////////////////////////
          $Proveedor = TraerUnDato("proveedor",array("nombreProveedor" => $proveedor));
          if($Proveedor !== false){
            $idProveedor = $Proveedor->idProveedor;
          } else {
            $datosProveedor  = array(
              "nombreProveedor"=>$proveedor,
              "estadoProveedor"=>"Activo",
              "idSucursalProveedor"=>$idSucursal
            );
            $idProveedor = GuardarDatos("proveedor",$datosProveedor);
          }
          //////////////////////////////////////////////////////////////

          //////////////////////////////////////////////////////////////
          //////////////////////////////////////////////////////////////
          $datosProducto = array(
            'idSucursalProducto' =>  $idSucursal,
            'codProducto' =>  $barcode,
            'nombreProducto' =>  $nombre,
            'idCategoria' =>  $idCategoria,
            'idProveedor' =>  $idProveedor,
            'marcaProducto' =>  $marca,
            'modeloProducto' =>  $modelo,
            'costoSinIva' =>  $costo,
            'costoConIva' =>  $costoIVA,
            'costoPromedioProducto' =>  $costo,
            'precioSinIva' =>  $precio,
            'precioConIva' =>  $precioIVA,
            'estadoProducto' =>  'Activo',
          );
          $idProducto = GuardarDatos("producto", $datosProducto);
        }
        if($idProducto !== false){
            $precios = array();
            $margen = 0;
            if($precio > 0){
              $margen = round(100*(1- ($costo/$precioIVA)),2);
            }
            $precios[] = array(
              'precio' => $precio,
              'iva' => $precioIVA,
              'margen' => $margen,
            );
          if($precio1 > 0){
            $margen = 0;
            $margen = round(100*(1- ($costo/$precioIVA1)),2);
            $precios[] = array(
              'precio' => $precio1,
              'iva' => $precioIVA1,
              'margen' => $margen,
            );
          }

          $datosPresentacion= array(
            'idProducto' => $idProducto,
            'idSucursalProductoPresentacion' => $idSucursal,
            'idUnidadMedida' => GblTraerConfiguracion('UnidadMedidaPredeterminada'),
            'unidadInventarioProductoPresentacion' => $unidadInventario,
            'unidadProductoPresentacion' => $unidad,
            'descripcionProductoPresentacion' => $presentacion,
            'costoProductoPresentacion' => $costo,
            'costoIVAProductoPresentacion' => $costoIVA,
            'precioProductoPresentacion' => json_encode($precios),
            'estadoProductoPresentacion' => 'Activo',
          );
          $idPresentacion = GuardarDatos("productoPresentacion",$datosPresentacion);
          if($idPresentacion !== false){
            if($existencia != "" && $existencia >0){
              $datosDetalle = array(
                'idCarga' => $idCargaInicial,
                'idProducto' => $idProducto,
                'cant' => $existencia,
                'idProductoPresentacion' => $idPresentacion,
                'idSucursalCargaInventarioDetalle' => $idSucursal,
                'costoSinIva' => $costo,
                'costoConIva' => $costoIVA,
                'subtotal' => round($costo * $existencia,4),
              );
              $idDetalle = GuardarDatos("cargaDeInventario_detalles",$datosDetalle);
              if($idDetalle !== null){
                $totalCarga += round($costo * $existencia,4);
                $cargas ++;
                $cantidadreal = $unidad * $existencia;
                $response_lotes = $this->lotes->CargaLote(
                  "Carga",
                  $idDetalle,
                  $idProducto,
                  $idPresentacion,
                  $idSucursal,
                  $existencia,
                  $cantidadreal,
                  $unidad,
                  $costo,
                  $precioIVA,
                  "",
                  "");
                }
              } else {
                $cargas ++;
              }
            }
          }
        }
        if($cargas > 0){
          $datosCarga = array();
          $datosCarga['idCargaDescargaTipo']=GblTraerConfiguracion("tipoCargaDescargaPredeterminado");
          $datosCarga['idUsuario']=$this->session->idUsuario;
          $datosCarga['idSucursalCarga']=$idSucursal;
          $datosCarga['fecha']=date('Y-m-d');
          $datosCarga['hora']=date("H:i:s");
          $datosCarga['total']=$totalCarga;
          $datosCarga['estadoCarga']='Activo';
          $datosCarga['aleatorioCarga']=uniqid();
          $datosCarga['concepto']="CARGA POR DEFECTO DEL SISTEMA";
          $idCarga = GuardarDatos("cargaDeInventario",$datosCarga,$idCargaInicial);
          EjecutarTransaccion();
          echo "EXITO!!!";
        } else {
          DeshacerTransaccion();
          echo "ERROR";
        }
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////
      }
    }
  }
  /* End of file Cargos.php */
