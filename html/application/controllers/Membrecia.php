<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Membrecia extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "membrecia";
	private $controlador = "Membrecia";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Membrecias";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-id-card ",
				"botones" => array(
					array(
						"icono"=> "fa fa-id-card ",
						'controlador' => $this->controlador,
						'url' => 'MembreciaAgregar',
						'txt' => 'Agregar Membrecia',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
            'id'=>'MembreciaAgregar'
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Cliente"=>5,
					"Codigo"=>2,
					"Estado"=>1,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/membrecia.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function MembreciaMostrar(){
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
			0 => 'membrecia.idMembrecia',
			1 => 'membrecia.codigoMembrecia',
			2 => 'cliente.nombreCliente',
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])){
			$ordenCampos = null;
		} else{
			$ordenCampos = $columnasValidas[$col];
		}
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$this->session->idSucursal = $sucursal;
		$condicion = array('membrecia.estadoMembrecia !=' => "Borrado", 'membrecia.idSucursalMembrecia' => $sucursal);
    	$joins = array(array('tabla' => 'cliente', 'condicion' => 'cliente.idCliente = membrecia.idClienteMembrecia'));
		$Membrecias = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion, $joins);
		// print_r($Membrecias);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Membrecias != 0){
			$datosMostrar = array();
			foreach ($Membrecias as $Membrecia){
				$estadoMembrecia = $Membrecia->estadoMembrecia;
				if($estadoMembrecia=="Inactivo"){
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				}
        else if($estadoMembrecia == "Activo"){
					$estadoTxt = "Activar";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$estadoIcon = "fa fa-toggle-off";
				}
        else
        {
          $estadoTxt = "";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Depreciado<span>";
					$estadoIcon = "fa fa-toggle";
        }

				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion ="MembreciaEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "'  data-accion='Editar' idMembrecia=" .md5($Membrecia->idMembrecia). " ><i class='fa fa-edit' ></i> Editar</a>";
				}
				// $funcion = "ActivoFijoDepreciacion";
				// if(GblPermisos($this,$funcion,$this->controlador)){
				// 	$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($activoFijo->idActivoFijo)."'><i class='fa fa-lock' ></i> Depreciación</a>";
				// }
				// $funcion = "UsuarioCambiarEstado";
				// if(GblPermisos($this,$funcion,$this->controlador)){
				// 	$menuOpciones .= "<a class='dropdown-item ".$funcion."' data-accion='$estadoTxt' idUsuario=".md5($usuario->idUsuario)."><i class='$estadoIcon'></i> $estadoTxt</a>";
				// }
				// $funcion = "UsuarioEliminar";
				// if(GblPermisos($this,$funcion,$this->controlador)){
				// 	$menuOpciones .= "<a class='dropdown-item ".$funcion."' idUsuario=".md5($usuario->idUsuario)."><i class='fa fa-trash'></i> Eliminar</a>";
				// }
				$menuOpciones .= "
				</div>
				</div>";

				$datosMostrar[] = array(
					$Membrecia->idMembrecia,
					$Membrecia->nombreCliente,
					$Membrecia->codigoMembrecia,
					$Membrecia->estadoMembrecia,
					$menuOpciones,
				);
			}
			$totalMembrecia = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalMembrecia,
				"recordsFiltered" => $totalMembrecia,
				"data" => $datosMostrar
			);
		} else{
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

	function MembreciaAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){

        $length = 4;
        $number = '0123456789';
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $numbersLength = strlen($number);
        $randomString = '';
        $randomString .= $characters[rand(0, $charactersLength - 1)];
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $number[rand(0, $numbersLength - 1)];
        }
        $randomString .= $characters[rand(0, $charactersLength - 1)];


        $titulo = "Agregar Membrecia";
        $clientes = TraerDatos('cliente');
        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-map",
            "controlador" => "Membrecia",
            "proceso" => "Agregar",
            "clientes" => $clientes,
            "pin" => $randomString,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/membrecia.js"
            ),
        );
        $this->load->view("membrecia/MembreciaAgregar",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$idClienteMembrecia = $this->input->post("idClienteMembrecia");
				$pin = $this->input->post("pin");
				$sucursalMembrecia  = (!is_null($this->input->post("sucursalMembrecia"))) ? $this->input->post("sucursalMembrecia") : $this->session->idSucursal;

				$condicionExiste = array('codigoMembrecia' => $pin);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				$condicionExiste2 = array('idClienteMembrecia' => $idClienteMembrecia, 'estadoMembrecia' => "Activo");
				$existe2 = ExistenDatos($this->tabla, $condicionExiste2);
				if($existe==0 && $existe2 == 0){
					$datosMembrecia = array(
						"idClienteMembrecia"=>$idClienteMembrecia,
						"codigoMembrecia"=>$pin,
						"idSucursalMembrecia"=>$sucursalMembrecia,
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosMembrecia);
					if($guardar)
          {
						$idUsuario = $guardar;
						EjecutarTransaccion();
						$datosRespuesta["codigo"]=200;
					}
          else
          {
						DeshacerTransaccion();
						$datosRespuesta["codigo"]=500;
					}
				}
        else
        {
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

  function MembreciaPin()
  {
    if($this->input->method(TRUE) == "POST")
    {
      $length = 4;
      $number = '0123456789';
      $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $numbersLength = strlen($number);
      $randomString = '';
      $randomString .= $characters[rand(0, $charactersLength - 1)];
      for ($i = 0; $i < $length; $i++)
      {
          $randomString .= $number[rand(0, $numbersLength - 1)];
      }
      $randomString .= $characters[rand(0, $charactersLength - 1)];
      $xdata["pin"] = $randomString;
      echo json_encode($xdata);
    }
  }

	function MembreciaEditar($idMembrecia=""){
    if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){

        $titulo = "Editar Membrecia";
        $clientes = TraerDatos('cliente');
        $condicion = array('md5(idMembrecia)' => $idMembrecia);
        $datos = TraerUnDato('membrecia', $condicion);
        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-map",
            "controlador" => "Membrecia",
            "proceso" => "Editar",
            "clientes" => $clientes,
            "datos" => $datos,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/membrecia.js"
            ),
        );
        $this->load->view("membrecia/MembreciaEditar",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
        $idMembrecia = $this->input->post("idMembrecia");
        $idClienteMembrecia = $this->input->post("idClienteMembrecia");
				$pin = $this->input->post("pin");
				$sucursalMembrecia  = (!is_null($this->input->post("sucursalMembrecia"))) ? $this->input->post("sucursalMembrecia") : $this->session->idSucursal;

				$condicionExiste = array('codigoMembrecia' => $pin, 'md5(idMembrecia) !=' => $idMembrecia);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				$condicionExiste2 = array('idClienteMembrecia' => $idClienteMembrecia, 'estadoMembrecia' => "Activo",  'md5(idMembrecia) !=' => $idMembrecia);
				$existe2 = ExistenDatos($this->tabla, $condicionExiste2);
				if($existe==0 && $existe2 == 0){
					$datosMembrecia = array(
						"codigoMembrecia"=>$pin,
            "aleatorioMembrecia" => uniqid(),
					);
					IniciarTransaccion();
          $condicion = array('md5(idMembrecia)'=> $idMembrecia);
					$guardar = EditarDatos($this->tabla,$datosMembrecia, $condicion);
					if($guardar)
          {
						$idUsuario = $guardar;
						EjecutarTransaccion();
						$datosRespuesta["codigo"]=200;
					}
          else
          {
						DeshacerTransaccion();
						$datosRespuesta["codigo"]=500;
					}
				}
        else
        {
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
}
/* End of file Usuarios.php */
