<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModificadoresTipo extends CI_Controller {

	private $tabla = "modificadorTipo";
	private $tablaModificador = "modificador";
	private $controlador = "ModificadoresTipo";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

    function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Categoria de Modificadores";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-outdent",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => '',
						'txt' => 'Agregar Categoria',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
						'id' => 'modificadorTipoAgregar'
					),
				),
				"encabezados" => array(
					"ID" => 1,
					"Nombre" => 5,
					"Varios" => 2,
					"Estado" => 1,
					"Acciones" => 1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/modificadoresTipo.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}
	function ModificadoresTipoMostrar(){
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
			0 => 'idModificadorTipo',
			1 => 'nombreModificadorTipo',
			2 => 'activoModificadorTipo'
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
		//$condicion = array('idSucursalModificadorTipo' => $sucursal);
		$condicion = array('idSucursalModificadorTipo' => $sucursal);
		$tipo = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($usuarios);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($tipo != 0){
			$datosMostrar = array();
			foreach ($tipo as $tipo){
				$estadoTipo = $tipo->estadoModificadorTipo;
				if($estadoTipo=="Activo"){
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else{
					$estadoTxt = "Activar";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$estadoIcon = "fa fa-toggle-off";
				}
				if($tipo->variosModificadorTipo==1){
					$tipoTipo = "<span class='badge badge-primary font-weight-bold'>Si<span>";
				} else{
					$tipoTipo = "<span class='badge badge-danger font-weight-bold'>No<span>";
				}
				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion ="ModificadoresTipoEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idTipo=" . md5($tipo->idModificadorTipo) . "><i class='fa fa-edit' ></i> Editar</a>";
					//$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($tipo->idModificadorTipo)."'><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "ModificadoresTipoCambiarEstado";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' data-accion='$estadoTxt' idTipo=".md5($tipo->idModificadorTipo)."><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ModificadoresTipoEliminar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' idTipo=".md5($tipo->idModificadorTipo)."><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";

				$datosMostrar[] = array(
					$tipo->idModificadorTipo,
					$tipo->nombreModificadorTipo,
					$tipoTipo,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalTipo = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalTipo,
				"recordsFiltered" => $totalTipo,
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
	function ModificadoresTipoAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				//$roles = TraerDatos('usuarioRoles');
				$titulo = "Agregar Categoria de Modificador";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-outdent",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar"
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						//"scripts/modificadoresTipo.js"
					),
				);
				//GblPlantilla("modificadoresTipo/ModificadoresTipoAgregar",$datosVista,$extras,$titulo);
				$this->load->view("modificadoresTipo/ModificadoresTipoAgregar",$datosVista);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreTipo = $this->input->post("nombreTipo");
				$varios = $this->input->post("varios");
				$condicionExiste = array('nombreModificadorTipo' => $nombreTipo);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe == 0){
					$datosModificadorTipo = array(
						"idSucursalModificadorTipo"=>$this->session->idSucursal,
						"nombreModificadorTipo" => $nombreTipo,
						"variosModificadorTipo" => $varios,
						"estadoModificadorTipo" => 'Activo'
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosModificadorTipo);
					$error = false;
					($guardar == false) ? $error = true : $idModificadoresTipo = $guardar; 

					if($error){
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 500;
					} else{
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					}
				} else{
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ModificadoresTipoEditar($idModificadorTipo=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				// $idUsuario = $this->uri->segment(3);
				$condicionDatos = array('md5(idModificadorTipo)' => $idModificadorTipo);
				$datosModificadorTipo = TraerUnDato($this->tabla, $condicionDatos);
					$titulo = "Editar Categoria de Modificador";
					$datosVista = array(
						"datosModificadorTipo"=> $datosModificadorTipo,
						"controlador" => $this->controlador,
						"idModificadorTipo" => $idModificadorTipo,
						"titulo" => $titulo,
						"icono"=> "fa fa-outdent",
						"proceso" => "Editar",
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							//"scripts/modificadoresTipo.js"
						),
					);
					$this->load->view("modificadoresTipo/ModificadoresTipoEditar",$datosVista);
					//GblPlantilla("modificadoresTipo/ModificadoresTipoEditar",$datosVista,$extras,$titulo);
				
			} else if($this->input->method(TRUE) == "POST"){
				$idModificadorTipo = $this->input->post("idModificadorTipo");
				$nombreTipo = $this->input->post("nombreTipo");
				$varios = $this->input->post("varios");
				
				$condicionExiste = array(
					'nombreModificadorTipo' => $nombreTipo,
					'md5(idModificadorTipo) !=' => $idModificadorTipo 
				);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				//print_r($existe);
				if($existe == 0){  
					$datosModificadorTipo = array(
						"nombreModificadorTipo" => $nombreTipo,
						"variosModificadorTipo" => $varios,
						'aleatorioModificadorTipo' => uniqid(),
					);
					IniciarTransaccion();
					$condicion = array("md5(idModificadorTipo)" => $idModificadorTipo);
					$editar = EditarDatos($this->tabla,$datosModificadorTipo,$condicion);
					$error = false;
					($editar == false) ? $error = true : $error = false; 

					if($error){
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 500;
					} else{
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					}
				} else{
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ModificadoresTipoEliminar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"] = 403;
		} else{
			if($this->input->method(TRUE) == "POST"){	
				$idModificadorTipo = $this->input->post("idTipo");
				$condicion = array("md5(idModificadorTipo)" => $idModificadorTipo);
				IniciarTransaccion();
				//se buscan registros en otras tablas que dependan del registro a eliminar
				$dependencias = ExistenDatos($this->tablaModificador,$condicion);
				$datosModificador = array(
					"estadoModificadorTipo" => "Borrado"
				);
				IniciarTransaccion();
				if($dependencias == 0){
					$borrar = EditarDatos($this->tabla,$datosModificador,$condicion);
					if($borrar){
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					}
					else{
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 500;
					}
				}
				else{
					$datosRespuesta["codigo"] = 424;
				}				
			}
		}
		echo json_encode($datosRespuesta);
	}
	function ModificadoresTipoCambiarEstado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"] = 403;
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idUsuario = $this->input->post("idTipo");
				$condicionDatos = array(
					'md5(idModificadorTipo)' => $idUsuario,
					'estadoModificadorTipo' => "Inactivo",
				);
				$activoModificadorTipo = ExistenDatos($this->tabla,$condicionDatos);
				
				( $activoModificadorTipo == 0 ) ? $nuevoEstado = 'Inactivo' : $nuevoEstado = 'Activo';

				$datosUsuario = array(
					"estadoModificadorTipo" => $nuevoEstado
				);
				$condicion = array("md5(idModificadorTipo)" => $idUsuario);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosUsuario,$condicion);
				if($editar){
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				} else{
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 500;
				}
			}
		}
		echo json_encode($datosRespuesta);
	}
}
/* End of file Modificadores.php */
