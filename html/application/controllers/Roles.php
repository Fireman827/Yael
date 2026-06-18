<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends CI_Controller {

	private $tabla = "usuarioRoles";
	private $tablaDetalle = "usuarioRolesDetalle";
	private $controlador = "Roles";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Roles";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-tasks",
				"botones" => array(
					array(
						"icono"=> "fa fa-check-double",
						'controlador' => $this->controlador,
						'url' => 'RolAgregar',
						'txt' => 'Agregar Rol',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id'=>'',
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Nombre"=>8,
					"Estado"=>2,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/roles.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function RolMostrar(){
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));

		$ordenCampos = $this->input->post("order");
		$busquedaArreglo = $this->input->post("search");
		$busquedaParametro = $busquedaArreglo['value'];
		$col = 0;
		$ordenDireccion = "";
		if (!empty($ordenCampos)){
			foreach ($ordenCampos as $o){
				$col = $o['column'];
				$ordenDireccion = $o['dir'];
			}
		}
		if ($ordenDireccion != "asc" && $ordenDireccion != "desc"){
			$ordenDireccion = "desc";
		}
		//Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		$columnasValidas = array(
			0 => 'idRol',
			1 => 'nombreRol',
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])){
			$ordenCampos = null;
		} else{
			$ordenCampos = $columnasValidas[$col];
		}
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$condicion = array('idSucursalRol' => $sucursal);
		$roles = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);

		//Lectura de datos de la base para mostrar en el datatabla
		if ($roles != 0){
			$datosMostrar = array();
			foreach ($roles as $rol){
				$estadoRol = $rol->activoRol;
				if($estadoRol==1){
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else{
					$estadoTxt = "Activar";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$estadoIcon = "fa fa-toggle-off";
				}

				$menuOpciones = "
				<div class='input-group-prepend'>
					<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
					<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

						$funcion ="RolEditar";
						if(GblPermisos($this,$funcion,$this->controlador)){
							$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($rol->idRol)."'><i class='fa fa-edit' ></i> Editar</a>";
						}
						$funcion = "RolEstado";
						if(GblPermisos($this,$funcion,$this->controlador)){
							$menuOpciones .= "<a class='dropdown-item ".$funcion."' data-accion='$estadoTxt' idRol=".md5($rol->idRol)."><i class='$estadoIcon'></i> $estadoTxt</a>";
						}
						$funcion = "RolEliminar";
						if(GblPermisos($this,$funcion,$this->controlador)){
							$menuOpciones .= "<a class='dropdown-item ".$funcion."' idRol=".md5($rol->idRol)."><i class='fa fa-trash'></i> Eliminar</a>";
						}
						$menuOpciones .= "
					</div>
				</div>";

				$datosMostrar[] = array(
					$rol->idRol,
					$rol->nombreRol,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalRoles = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalRoles,
				"recordsFiltered" => $totalRoles,
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

	function RolAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$menusMostrar = array();
				$menus = $this->core->TraerMenusUsuario(0,1);
				if($menus !== false){
					foreach ($menus as $menu){
						$idMenu = $menu->idMenu;
						$modulos = $this->core->TraerModulosUsuario(0,1,$idMenu,1);
						$menu->modulos = $modulos;
						array_push($menusMostrar,$menu);
					}
				}
				$titulo = "Agregar Rol";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-users",
					"controlador"=> "Roles",
					"proceso"=> "Agregar",
					"menus"=> $menusMostrar,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/roles.js"
					),
				);
				GblPlantilla("roles/RolAgregar",$datosVista,$extras,$titulo);
				// GblPlantilla("roles/Inventario",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreRol = $this->input->post("nombreRol");
				$rutaRol = $this->input->post("rutaRol");
				$listaPermisos = json_decode($this->input->post("listaPermisos"),true);
				$condicionExiste = array('nombreRol' => $nombreRol);
				$existe = ExistenDatos($this->tabla,$condicionExiste);
				$sucursal  = $this->session->idSucursal;
				if($existe==0){
					$datosRol = array(
						"idSucursalRol"=>$this->session->idSucursal,
						"nombreRol"=>$nombreRol,
						"rutaRol"=>$rutaRol,
						"activoRol"=>1,
						"idSucursalRol" =>$sucursal,
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosRol);
					if($guardar){
						$idRol = $guardar;
						$error = false;
						foreach ($listaPermisos as $permiso){
							$datosRolDetalle = array(
								'idRol' => $idRol,
								'idModulo' => $permiso["idModulo"],
							);
							$guardarDetalle = GuardarDatos($this->tablaDetalle,$datosRolDetalle);
							if($guardarDetalle === false){
								$error = true;
							}
						}
						if(!$error){
							EjecutarTransaccion();
							$datosRespuesta["codigo"]=200;
						} else{
							DeshacerTransaccion();
							$datosRespuesta["codigo"]=501;
						}
					} else{
						DeshacerTransaccion();
						$datosRespuesta["codigo"]=500;
					}
				} else{
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function RolEditar($idRol=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				// $idRol = $this->uri->segment(3);
				$condicionDatos = array('md5(idRol)' => $idRol);
				$datosRol = TraerUnDato($this->tabla, $condicionDatos);
				if($datosRol !== false && $idRol!=""){
					$idRolDb = $datosRol->idRol;
					$menusMostrar = array();
					$menus = $this->core->TraerMenusUsuario(0,1);
					if($menus !== false){
						foreach ($menus as $menu){
							$idMenu = $menu->idMenu;
							$modulos = $this->core->TraerModulosUsuario(0,1,$idMenu,1);
							if($modulos !== false){
								$modulosValidados = array();
								foreach ($modulos as $modulo){
									$idModulo = $modulo->idMenuModulo;
									$condicionRoles = array(
										'idRol' => $idRolDb,
										'idModulo' => $idModulo,
									);
									$modulo->existeEnRol = ExistenDatos($this->tablaDetalle,$condicionRoles);
									array_push($modulosValidados,$modulo);
								}
							}
							$menu->modulos = $modulosValidados;
							array_push($menusMostrar,$menu);
						}
					}
					$titulo = "Editar Rol";
					$datosVista = array(
						"datosRol"=> $datosRol,
						"menus"=> $menusMostrar,
						"controlador" => "Roles",
						"idRol" => $idRol,
						"titulo" => $titulo,
						"proceso" => "Editar",
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/roles.js"
						),
					);
					GblPlantilla("roles/RolEditar",$datosVista,$extras,$titulo);
				} else{
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			}
			else if($this->input->method(TRUE) == "POST"){
				$idRol = $this->input->post("idRol");
				$nombreRol = $this->input->post("nombreRol");
				$rutaRol = $this->input->post("rutaRol");
				$listaPermisos = json_decode($this->input->post("listaPermisos"),true);
				$condicionExiste = array(
					'nombreRol' => $nombreRol,
					'rutaRol' => $rutaRol,
					'md5(idRol) !=' => $idRol,
				);
				$existe = ExistenDatos($this->tabla,$condicionExiste);
				$aleatorioRol = uniqid();
				if($existe==0){
					$datosRol = array(
						"nombreRol"=>$nombreRol,
						'rutaRol' => $rutaRol,
						"aleatorioRol"=>$aleatorioRol,
					);
					IniciarTransaccion();
					$condicionRol = array("md5(idRol)" => $idRol);
					$editar = EditarDatos($this->tabla,$datosRol,$condicionRol);
					if($editar){
						$datosRolDb = TraerUnDato($this->tabla,$condicionRol);
						$idRolDb = $datosRolDb->idRol;
						$error = false;
						$limpiarDetalles =  EliminarDatos($this->tablaDetalle,$condicionRol);
						if(!$limpiarDetalles){
							$error = true;
						}
						foreach ($listaPermisos as $permiso){
							$datosRolDetalle = array(
								'idRol' => $idRolDb,
								'idModulo' => $permiso["idModulo"],
							);
							$guardarDetalle = GuardarDatos($this->tablaDetalle,$datosRolDetalle);
							if($guardarDetalle === false){
								$error = true;
							}
						}
						if(!$error){
							EjecutarTransaccion();
							$datosRespuesta["codigo"]=200;
						} else{
							DeshacerTransaccion();
							$datosRespuesta["codigo"]=501;
						}
					} else{
						DeshacerTransaccion();
						$datosRespuesta["codigo"]=500;
					}
				} else{
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function RolEliminar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"]="error";
			$datosRespuesta['titulo']='Advertencia';
			$datosRespuesta['subtitulo']='';
			$datosRespuesta["mensaje"]="No tiene permisos para ejecutar esta accion";
			echo json_encode($datosRespuesta);
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idRol = $this->input->post("idRol");
				$condicion = array("md5(idRol)" => $idRol);
				IniciarTransaccion();
				$eliminar =  EliminarDatos($this->tabla,$condicion);
				if($eliminar){
					$eliminarDetalle =  EliminarDatos($this->tablaDetalle,$condicion);
					if($eliminarDetalle){
						EjecutarTransaccion();
						$datosRespuesta["codigo"]=200;
					} else{
						DeshacerTransaccion();
						$datosRespuesta["codigo"]=501;
					}
				} else{
					DeshacerTransaccion();
					$datosRespuesta["codigo"]=500;
				}
			}
		}
		echo json_encode($datosRespuesta);
	}

	function RolCambiarEstado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"]="error";
			$datosRespuesta['titulo']='Advertencia';
			$datosRespuesta['subtitulo']='';
			$datosRespuesta["mensaje"]="No tiene permisos para ejecutar esta accion";
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idRol = $this->input->post("idRol");
				$condicionDatos = array(
					'md5(idRol)' => $idRol,
					'activoRol' => 1,
				);
				$activoRol = ExistenDatos($this->tabla,$condicionDatos);
				if($activoRol==0){
					$nuevoEstado = 1;
					$estadoTxt = 'activado';
					$estadoTxtInverso = 'desactivado';
				} else{
					$nuevoEstado = 0;
					$estadoTxt = 'desactivado';
					$estadoTxtInverso = 'activado';
				}
				$datosRol = array(
					"activoRol" => $nuevoEstado
				);
				$condicion = array("md5(idRol)" => $idRol);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosRol,$condicion);
				if($editar){
					EjecutarTransaccion();
					$datosRespuesta["codigo"]=200;
				} else{
					DeshacerTransaccion();
					$datosRespuesta["codigo"]=500;
				}
			}
		}
		echo json_encode($datosRespuesta);
	}
}

/* End of file Roles.php */
