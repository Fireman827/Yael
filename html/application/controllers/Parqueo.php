<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parqueo extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "parqueo";
	private $controlador = "Parqueo";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");

	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Parqueo";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-car",
				"botones" => array(
					array(
						"icono"=> "fa fa-car",
						'controlador' => $this->controlador,
						'url' => 'parqueoAgregar',
						'txt' => 'Agregar parqueo',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
            'id'=>'ParqueoAgregar'
					),
					array(
						"icono"=> "fa fa-money-bill",
						'controlador' => $this->controlador,
						'url' => 'parqueoTarifa',
						'txt' => 'Tarifa',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
            'id'=>'ParqueoTarifa'
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Cliente"=>5,
					"Placa"=>1,
          "Fecha Entrada"=>1,
					"Hora Entrada"=>1,
					"Fecha Salida"=>1,
          "Hora Salida"=>1,
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
					"scripts/parqueo.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function ParqueoMostrar(){
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
			0 => 'idParqueo',
			1 => 'placaParqueo',
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
		$condicion = array('estadoParqueo !=' => "Borrado", 'idSucursalParqueo' => $sucursal);
    	// $joins = array(array('tabla' => 'cliente', 'condicion' => 'cliente.idCliente = membrecia.idClienteMembrecia'));
		$Parqueos = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($Membrecias);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Parqueos != 0){
			$datosMostrar = array();
			foreach ($Parqueos as $Parqueo){
				$estadoParqueo = $Parqueo->estadoParqueo;
				if($estadoParqueo=="Inactivo"){
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				}
        else if($estadoParqueo == "Activo"){
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

				$funcion ="ParqueoEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "'  data-accion='Editar' idParqueo=" .md5($Parqueo->idParqueo). " ><i class='fa fa-edit' ></i> Editar</a>";
				}
				if ($Parqueo->estadoParqueo == 'Pendiente')
				{
					$funcion = "ParqueoCobro";
					if(GblPermisos($this,$funcion,$this->controlador)){
						$menuOpciones .= "<a class='dropdown-item " . $funcion . "'  data-accion='Editar' idParqueo=" .md5($Parqueo->idParqueo). " ><i class='fa fa-money-bill' ></i> Cobrar</a>";
					}
				}
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
        if($Parqueo->idClienteParqueo != 0)
        {
          $condicionCliente = array('idCliente' => $Parqueo->idClienteParqueo,);
          $datosCliente = TraerUnDato("cliente", $condicionCliente);
          $nombreCliete = $datosCliente->nombreCliente;
        }
        else
        {
          $nombreCliete = "Clientes Varios";
        }
				$datosMostrar[] = array(
					$Parqueo->idParqueo,
					$nombreCliete,
          $Parqueo->placaParqueo,
					$Parqueo->fechaEntradaParqueo,
					$Parqueo->horaEntradaParqueo,
					$Parqueo->fechaSalidaParqueo,
					$Parqueo->horaSalidaParqueo,
					$Parqueo->estadoParqueo,
					$menuOpciones,
				);
			}
			$totalParqueo = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalParqueo,
				"recordsFiltered" => $totalParqueo,
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

	function ParqueoAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){


        $titulo = "Agregar Parqueo";
        $clientes = TraerDatos('cliente');
        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-map",
            "controlador" => "Parqueo",
            "proceso" => "Agregar",
            "clientes" => $clientes,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/parqueo.js"
            ),
        );
        $this->load->view("parqueo/ParqueoAgregar",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$idClienteParqueo = $this->input->post("idClienteParqueo");
				$placaParqueo = $this->input->post("placaParqueo");
				$horaParqueo = $this->input->post("horaParqueo");
				$sucursalParqueo  = (!is_null($this->input->post("sucursalParqueo"))) ? $this->input->post("sucursalParqueo") : $this->session->idSucursal;

				$fechaEntradaParqueo = date("Y-m-d");
				// $horaEntradaParqueo = date("H:i:s");
				$condicionExiste = array('placaParqueo' => $placaParqueo, 'estadoParqueo' => 'Pendiente');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosParqueo = array(
						"idClienteParqueo"=>$idClienteParqueo,
						"placaParqueo"=>$placaParqueo,
						"fechaEntradaParqueo"=>$fechaEntradaParqueo,
						"horaEntradaParqueo"=>$horaParqueo,
						"idSucursalParqueo"=>$sucursalParqueo,
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosParqueo);
					if($guardar){
						$idParqueo = $guardar;
						EjecutarTransaccion();
						$datosRespuesta["codigo"]=200;
						$datosRespuesta["idParqueo"]=md5($idParqueo);
						}
					else {
						DeshacerTransaccion();
						$datosRespuesta["codigo"]=500;
					}
				}
				else {
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function ParqueoEditar($idParqueo=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idParqueo)' => $idParqueo);
				$datosParqueo = TraerUnDato($this->tabla, $condicionDatos);

        $titulo = "Editar Parqueo";
        $clientes = TraerDatos('cliente');
        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-map",
            "controlador" => "Parqueo",
            "proceso" => "Agregar",
            "clientes" => $clientes,
            "datos" => $datosParqueo,
						"idParqueo" =>$idParqueo,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/parqueo.js"
            ),
        );
        $this->load->view("parqueo/ParqueoEditar",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$idParqueo = $this->input->post("idParqueo");
				$idClienteParqueo = $this->input->post("idClienteParqueo");
				$placaParqueo = $this->input->post("placaParqueo");
				$horaParqueo = $this->input->post("horaParqueo");
				$sucursalParqueo  = (!is_null($this->input->post("sucursalParqueo"))) ? $this->input->post("sucursalParqueo") : $this->session->idSucursal;

        $fechaEntradaParqueo = date("Y-m-d");
        // $horaEntradaParqueo = date("H:i:s");
				$condicionExiste = array('placaParqueo' => $placaParqueo, 'estadoParqueo' => 'Pendiente', 'md5(idParqueo) !=' => $idParqueo);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosParqueo = array(
						"idClienteParqueo"=>$idClienteParqueo,
						"placaParqueo"=>$placaParqueo,
						"horaEntradaParqueo"=>$horaParqueo,
					);
					IniciarTransaccion();
					$condicion = array('md5(idParqueo)'=> $idParqueo);
					$editar = EditarDatos($this->tabla,$datosParqueo, $condicion);
					if($editar)
          {
						$idUsuario = $editar;
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
	function ParqueoCobro($idParqueo=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idParqueo)' => $idParqueo);
				$datosParqueo = TraerUnDato($this->tabla, $condicionDatos);
				$condicionConf = array('parametroConfiguracion' => 'tarifaParqueo',);
				$datosTarifa = TraerUnDato("configuraciones", $condicionConf);
        $titulo = "Cobro Parqueo";
				if($datosParqueo->idClienteParqueo != 0)
				{
					$condicionCliente = array('idCliente' => $datosParqueo->idClienteParqueo);
					$cliente = TraerUnDato('cliente', $condicionCliente);
					$existe = 1;
				}
				else
				{
					$cliente = "Clientes Varios";
					$existe = 0;
				}

        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-money-bill",
            "controlador" => "Parqueo",
            "proceso" => "Cobrar",
            "cliente" => $cliente,
						'existe' => $existe,
            "datos" => $datosParqueo,
						"idParqueo" =>$idParqueo,
						'tarifa' => $datosTarifa,
        );
        $extras = array(
            'css' => array(),
            'js' => array(
                "scripts/parqueo.js"
            ),
        );
        $this->load->view("parqueo/ParqueoCobro",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$idParqueo = $this->input->post("idParqueo");
				$totalParqueo = $this->input->post("totalParqueo");
				$horaSalidaParqueo = $this->input->post("horaSalidaParqueo");
				$fechaSalidaParqueo = $this->input->post("fechaSalidaParqueo");
				$sucursalParqueo  = (!is_null($this->input->post("sucursalParqueo"))) ? $this->input->post("sucursalParqueo") : $this->session->idSucursal;

        $fechaEntradaParqueo = date("Y-m-d");
        // $horaEntradaParqueo = date("H:i:s");
				$condicionExiste = array('estadoParqueo' => 'Pendiente', 'md5(idParqueo) !=' => $idParqueo);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosParqueo = array(
						"horaSalidaParqueo"=>$horaSalidaParqueo,
						"fechaSalidaParqueo"=>$fechaSalidaParqueo,
						"totalParqueo" => $totalParqueo,
						"estadoParqueo" => "Cobrado",
					);
					IniciarTransaccion();
					$condicion = array('md5(idParqueo)'=> $idParqueo);
					$editar = EditarDatos($this->tabla,$datosParqueo, $condicion);
					if($editar)
          {
						$idUsuario = $editar;
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
	function ParqueoImprimir($idParqueo=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idParqueo)' => $idParqueo);
				$datosParqueo = TraerUnDato($this->tabla, $condicionDatos);
				$condicionConf = array('parametroConfiguracion' => 'tarifaParqueo',);
				$datosTarifa = TraerUnDato("configuraciones", $condicionConf);
				$titulo = "Cobro Parqueo";
				if($datosParqueo->idClienteParqueo != 0)
				{
					$condicionCliente = array('idCliente' => $datosParqueo->idClienteParqueo);
					$cliente = TraerUnDato('cliente', $condicionCliente);
					$existe = 1;
				}
				else
				{
					$cliente = "Clientes Varios";
					$existe = 0;
				}

				$datosVista = array(
						"titulo" => $titulo,
						"icono" => "fa fa-money-bill",
						"controlador" => "Parqueo",
						"proceso" => "Cobrar",
						"cliente" => $cliente,
						'existe' => $existe,
						"datos" => $datosParqueo,
						"idParqueo" =>$idParqueo,
						'tarifa' => $datosTarifa,
				);
				$extras = array(
						'css' => array(),
						'js' => array(
								"scripts/parqueo.js"
						),
				);
				$this->load->view("parqueo/ParqueoCobro",$datosVista);
			}
			else if($this->input->method(TRUE) == "POST")
			{
				// require_once __DIR__ . '/../../imprimir/autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
				// use Mike42\Escpos\EscposImage;
				// use Mike42\Escpos\PrintConnectors\FilePrintConnector;
				// use Mike42\Escpos\Printer;

				// $nombre_impresora = "/dev/usb/lp0";
				// $connector = new FilePrintConnector($nombre_impresora);

				// $printer = new Printer($connector);
				// $printer->setJustification(Printer::JUSTIFY_CENTER);

				// $logo = EscposImage::load("logodi1.jpg", false);
				// $printer->bitImage($logo);

				// $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
				// $printer -> text("QR code demo\n");
				// $printer -> selectPrintMode();

				// // title($printer, "QR code demo\n");
				// $testStr = "Testing 123";
				// $printer -> qrCode($testStr, Printer::QR_ECLEVEL_L, 10);
				// $printer -> text("Most simple example\n");
				// $printer -> feed();		
				
				//   // Cut & close
				// $printer -> cut();
	  			// $printer -> close();

				$idParqueo = $this->input->post("idParqueo");
				$condicionDatos = array('md5(idParqueo)' => $idParqueo);
				$datosParqueo = TraerUnDato($this->tabla, $condicionDatos);
				$condicionConf = array('parametroConfiguracion' => 'tarifaParqueo',);
				$datosTarifa = TraerUnDato("configuraciones", $condicionConf);
				$titulo = "Cobro Parqueo";
				if($datosParqueo->idClienteParqueo != 0)
				{
					$condicionCliente = array('idCliente' => $datosParqueo->idClienteParqueo);
					$cliente = TraerUnDato('cliente', $condicionCliente);
					$existe = 1;
				}
				else
				{
					$cliente = "Clientes Varios";
					$existe = 0;
				}

				


				// $datosVista = array(
				// 		"cliente" => $cliente,
				// 		'existe' => $existe,
				// 		"datos" => $datosParqueo,
				// 		"idParqueo" =>$idParqueo,
				// 		'tarifa' => $datosTarifa,
				// );
				$datosRespuesta["cliente"] = $cliente;
				$datosRespuesta["existe"] = $existe;
				$datosRespuesta["datos"] = $datosParqueo;
				$datosRespuesta["idParqueo"] = $idParqueo;
				$datosRespuesta["tarifa"] = $datosTarifa;
				echo json_encode($datosRespuesta);
			}
		}
	}
}
/* End of file Usuarios.php */
