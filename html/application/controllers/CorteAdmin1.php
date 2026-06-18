<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CorteAdmin extends CI_Controller {

	private $tabla = "corteCaja";
	//private $tablaPermisos = "usuarioPermisos";
	private $controlador = "Corte";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

  public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Corte caja";
			$datos = $this->CorteTraerApertura();
			$existe = 0;
			if($datos != "")
			{
				$existe = 1;
			}

			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-trash",
				"botones" => array(
					array(
						"icono"=> "",
						'controlador' => $this->controlador,
						'url' => 'MovimientosCajaVale',
						'txt' => 'Realizar Apertura',
						'posicion' => 'center', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
            'id'=>'aperturaCaja'
					),
				),
        "encabezados"=>array(
					"ID"=>1,
					"Fecha"=>3,
					"Empleado"=>2,
					"Monto Apertura"=>2,
					"Monto Cierre"=>2,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
				"existe" => $existe,
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/corteAdmin.js"
				),
			);
			GblPlantilla("corte/CorteAdmin",$datosVista,$extras,$titulo);
		}
	}
  function CorteMostrar(){
    // Espacio propio del plugin data tabla
    $draw = intval($this->input->post("draw"));
    $desdeFilas = intval($this->input->post("start"));
    $cantidadFilas = intval($this->input->post("length"));

    $order = $this->input->post("order");
    $busquedaAreglo = $this->input->post("search");
    $busquedaParametro = $busquedaAreglo['value'];
    $col = 0;
    $ordenDireccion = "";
    if (!empty($order)){
      foreach ($order as $o){
        $col = $o['column'];
        $ordenDireccion = $o['dir'];
      }
    }
    if ($ordenDireccion != "asc" && $ordenDireccion != "desc"){
      $ordenDireccion = "desc";
    }
    //Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
    $columnasValidas = array(
      0 => 'idCorteCaja',
      1 => 'fechaCorte',
      2 => 'montoApertura',
      3 => 'montoCorte',
    );
    //Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
    if (!isset($columnasValidas[$col])){
      $ordenCampos = null;
    } else {
      $ordenCampos = $columnasValidas[$col];
    }
    // Fin espacio del data tabla
    $sucursal = $this->input->post("sucursal");
    $this->session->idSucursal = $sucursal;
    $condicion = array('idSucursalCorte' => $sucursal);
    // $join =
    $cortes = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
    // print_r($usuarios);
    //Lectura de datos de la base para mostrar en el datatabla
    if ($cortes != 0){
      $datosMostrar = array();
      foreach ($cortes as $corte){
        $menuOpciones = "
        <div class='input-group-prepend'>
        <button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
        <div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

        $funcion ="CorteTurnosDetalle";
        if(GblPermisos($this,$funcion,$this->controlador)){
          $menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($corte->idCorteCaja)."'><i class='fas fa-eye' ></i> Ver detalles</a>";
        }

        // $funcion = "CortePdf";
        // if(GblPermisos($this,$funcion,$this->controlador)){
        //   $menuOpciones .= "<a class='dropdown-item ".$funcion."' idCorteCaja=".md5($corte->idCorteCaja)."><i class='fa fa-fil-pdf-o'></i> PDF</a>";
        // }
				//
        // $funcion = "CorteImprimir";
        // if(GblPermisos($this,$funcion,$this->controlador)){
        //   $menuOpciones .= "<a class='dropdown-item ".$funcion."' idCorteCaja=".md5($corte->idCorteCaja)."><i class='fa fa-print'></i> Imprimir</a>";
        // }

        $menuOpciones .= "
        </div>
        </div>";
        $nombre = TraerUnDatoIndividual("usuario","nombreUsuario",array("idUsuario" => $corte->idUsuarioCorte))[0]["nombreUsuario"];

        $datosMostrar[] = array(
          $corte->idCorteCaja,
          $corte->fechaCorte." ".$corte->horaCorte,
          $nombre,
          $corte->montoApertura,
          $corte->montoCorte,
          $menuOpciones,
        );
      }
      $totalCortes = TraerTotalDatos($this->tabla,$condicion);
      $output = array(
        "draw" => $draw,
        "recordsTotal" => $totalCortes,
        "recordsFiltered" => $totalCortes,
        "data" => $datosMostrar
      );
    } else {
      $output = array(
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => 0
      );
    }
    echo json_encode($output);
    exit();
  }

	function CorteTraerApertura()
	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador))
    {
        GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    }
    else
    {
      if($this->input->method(TRUE) == "GET")
      {
		    $idSucursal = $this->session->idSucursal;
				$idUsuario = $this->session->idUsuario;
				$condicionExiste = array(
						"estadoCorte" => "Vigente",
						"idUsuarioCorte" => $this->session->idUsuario,
						"idSucursalCorte" => $idSucursal,
				);
				$existe = ExistenDatos("corteCaja", $condicionExiste);
				if($existe > 0)
				{
					$apertura = TraerUnDato("corteCaja", $condicionExiste);
					$idCorteCaja = $apertura->idCorteCaja;
					$idCaja = $apertura->idCaja;
					$idTurnoVigente = $apertura->idTurnoVigente;
					return $apertura;
				}
				else
				{
					return "";
				}
			}
			else if ($this->input->method(TRUE) == "POST")
      {
				$idSucursal = $this->session->idSucursal;
				$idUsuario = $this->session->idUsuario;
				$condicionExiste = array(
						"estadoCorte" => "Vigente",
						"idUsuarioCorte" => $this->session->idUsuario,
						"idSucursalCorte" => $idSucursal,
				);
				$existe = ExistenDatos("corteCaja", $condicionExiste);
				if($existe > 0)
				{
					$apertura = TraerUnDato("corteCaja", $condicionExiste);
					$idCorteCaja = $apertura->idCorteCaja;
					$idCaja = $apertura->idCaja;
					$idTurnoVigente = $apertura->idTurnoVigente;
					print_r($apertura);
				}
				else
				{
					echo "";
				}
			}
		}
	}
	function AperturaCaja(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$idSucursal = $this->session->idSucursal;
				$condicionExiste = array(
						"idSucursalCaja" => $idSucursal,
				);
				$cajas = TraerDatos('caja', $condicionExiste);
				$titulo = "Apertura Caja";
				$datosVista = array(
						"titulo" => $titulo,
						"icono" => "fas fa-plus",
						"controlador" => $this->controlador,
						"proceso" => "Apertura",
						"caja" => $cajas,
				);
				$extras = array(
						'css' => array(),
						'js' => array(
								"scripts/corteAdmin.js"
						),
				);
				$this->load->view("corte/AperturaCaja",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$efectivo = $this->input->post("efectivo");
				$diferencia = $this->input->post("diferencia");
				$total = $this->input->post("total");

					$datosCorte = array(
						"montoCorte"=>$efectivo,
						"diferenciaCorte"=>$diferencia,
						"totalCorte"=>$total,
						"horaCorte"=>date("H:i:s"),
						"estadoCorte"=>"Finalizado",
					);
					$condicionCorte = array('estadoCorte'=>"Vigente");
					IniciarTransaccion();
					$guardar = EditarDatos($this->tabla,$datosCorte,$condicionCorte);
					if($guardar){
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					} else {
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 402;
					}

				echo json_encode($datosRespuesta);
			}
		}
	}
}
/* End of file Usuarios.php */
