<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "usuario";
	private $tablaPermisos = "usuarioPermisos";
	private $controlador = "Usuarios";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Usuarios";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-users",
				"botones" => array(
					array(
						"icono"=> "fa fa-user-plus",
						'controlador' => $this->controlador,
						'url' => 'UsuarioAgregar',
						'txt' => 'Agregar Usuario',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
                        'id'=>'usuarioAgregar'
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Nombre"=>3,
					"Usuario"=>2,
					"Tipo"=>2,
					"Rol"=>2,
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
					"scripts/usuarios.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function UsuarioMostrar(){
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
			0 => 'idUsuario',
			1 => 'nombreUsuario',
			2 => 'usuarioUsuario',
			3 => 'rolUsuario',
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
		$condicion = array('superAdminUsuario' => 0,'idSucursalUsuario' => $sucursal,"idUSuario >"=>0);
		$usuarios = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($usuarios);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($usuarios != 0){
			$datosMostrar = array();
			foreach ($usuarios as $usuario){
				$estadoUsuario = $usuario->activoUsuario;
				if($estadoUsuario==1){
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else{
					$estadoTxt = "Activar";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$estadoIcon = "fa fa-toggle-off";
				}
				if($usuario->adminUsuario==1){
					$tipoUsuario = "<span class='badge badge-warning font-weight-bold'>Administrador<span>";
				} else{
					$tipoUsuario = "<span class='badge badge-info font-weight-bold'>Normal<span>";
				}
				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion ="UsuarioEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($usuario->idUsuario)."'><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "UsuarioPermisos";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($usuario->idUsuario)."'><i class='fa fa-lock' ></i> Permisos</a>";
				}
				$funcion = "UsuarioCambiarEstado";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' data-accion='$estadoTxt' idUsuario=".md5($usuario->idUsuario)."><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "UsuarioEliminar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' idUsuario=".md5($usuario->idUsuario)."><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";
				$rolUsuario = "";
				$condicionRol = array("idRol" => $usuario->rolUsuario);
				$datosRol = TraerUnDato("usuarioRoles",$condicionRol);
				if($datosRol !== false){
					$rolUsuario = $datosRol->nombreRol;
				}
				$datosMostrar[] = array(
					$usuario->idUsuario,
					$usuario->nombreUsuario,
					$usuario->usuarioUsuario,
					$tipoUsuario,
					$rolUsuario,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalUsuarios = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalUsuarios,
				"recordsFiltered" => $totalUsuarios,
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

	function UsuarioAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$roles = TraerDatos('usuarioRoles', array("idSucursalRol"=> $this->session->idSucursal, "activoRol" => 1));
				$sucursales = TraerDatos('sucursal');
				$superAdmin = $this->session->superAdmin;
				//var_dump($superAdmin);
				$titulo = "Agregar Usuario";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-users",
					"controlador"=> "Usuarios",
					"proceso"=> "Agregar",
					"roles"=> $roles,
					"superAdmin" => $superAdmin,
					"sucursales"=> $sucursales,
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/usuarios.js"
					),
				);
				GblPlantilla("usuarios/UsuarioAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreUsuario = $this->input->post("nombreUsuario");
				$usuarioUsuario = $this->input->post("usuarioUsuario");
				$claveReal = $this->input->post("claveUsuario");
				$codigoReal = $this->input->post("codigoUsuario");
				$rolUsuario = $this->input->post("rolUsuario");
				$sucursalUsuario  = (!is_null($this->input->post("sucursalUsuario"))) ? $this->input->post("sucursalUsuario") : $this->session->idSucursal;
				$claveUsuario = EncriptarClave($claveReal);
				if(!is_null($this->input->post("adminUsuario"))){
					$adminUsuario = 1;
				} else{
					$adminUsuario = 0;
				}
				if(!is_null($this->input->post("autorizadoUsuario"))){
					$autorizadoUsuario = 1;
				} else{
					$autorizadoUsuario = 0;
				}
				$condicionExiste = array('usuarioUsuario' => $usuarioUsuario);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$condicionExiste = array('codigoUsuario' => $codigoReal);
					$existe1 = ExistenDatos($this->tabla, $condicionExiste);
					if($existe1 == 0){
						$datosUsuario = array(
							"nombreUsuario"=>$nombreUsuario,
							"usuarioUsuario"=>$usuarioUsuario,
							"claveUsuario"=>$claveUsuario,
							"codigoUsuario"=>$codigoReal,
							"rolUsuario"=>$rolUsuario,
							"adminUsuario"=>$adminUsuario,
							"autorizadoUsuario"=>$autorizadoUsuario,
							"idSucursalUsuario"=>$sucursalUsuario,
							"activoUsuario"=>1,
						);
						IniciarTransaccion();
						$guardar = GuardarDatos($this->tabla,$datosUsuario);
						if($guardar){
							$idUsuario = $guardar;
							$error = false;
							if($rolUsuario > 0 && !$adminUsuario){
								$condicionDetalle = array("idRol" => $rolUsuario);
								$rolesDetalle = TraerDatos('usuarioRolesDetalle',$condicionDetalle);
								foreach($rolesDetalle as $rol){
									$datosRolDetalle = array(
										"idUsuario"=>$idUsuario,
										"idModulo"=>$rol->idModulo,
									);
									$guardarPermisos = GuardarDatos($this->tablaPermisos,$datosRolDetalle);
									if(!$guardarPermisos){
										$error = true;
									}
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
					}else{
						$datosRespuesta["codigo"]=400;
					}
				} else{
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function UsuarioEditar($idUsuario=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				// $idUsuario = $this->uri->segment(3);
				$condicionDatos = array('md5(idUsuario)' => $idUsuario);
				$datosUsuario = TraerUnDato($this->tabla, $condicionDatos);
				$datosRol = TraerDatos("usuarioRoles");
				$datosRol = TraerDatos('usuarioRoles', array("idSucursalRol"=> $this->session->idSucursal, "activoRol" => 1));

				$sucursales = TraerDatos('sucursal');
				$superAdmin = $this->session->superAdmin;

				if($datosUsuario !== false && $idUsuario!=""){
					$titulo = "Editar Usuario";
					$datosVista = array(
						"datosUsuario"=> $datosUsuario,
						"roles"=> $datosRol,
						"controlador" => "Usuarios",
						"idUsuario" => $idUsuario,
						"titulo" => $titulo,
						"superAdmin" => $superAdmin,
						"sucursales"=> $sucursales,
						"proceso" => "Editar",
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/usuarios.js"
						),
					);
					GblPlantilla("usuarios/UsuarioEditar",$datosVista,$extras,$titulo);
				} else{
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idUsuario = $this->input->post("idUsuario");
				$nombreUsuario = $this->input->post("nombreUsuario");
				$usuarioUsuario = $this->input->post("usuarioUsuario");
				$claveReal = $this->input->post("claveUsuario");
				$codigoReal = $this->input->post("codigoUsuario");
				$rolUsuario = $this->input->post("rolUsuario");
				$sucursalUsuario = $this->input->post("sucursalUsuario");
				$claveUsuario = EncriptarClave($claveReal);
				if(!is_null($this->input->post("adminUsuario"))){
					$adminUsuario = 1;
				} else{
					$adminUsuario = 0;
				}
				if(!is_null($this->input->post("autorizadoUsuario"))){
					$autorizadoUsuario = 1;
				} else{
					$autorizadoUsuario = 0;
				}
				$condicionExiste = array(
					'usuarioUsuario' => $usuarioUsuario,
					'md5(idUsuario) !=' => $idUsuario,
				);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$condicionExiste = array('codigoUsuario' => $codigoReal, "md5(idUsuario) !=" => $idUsuario);
					$existe1 = ExistenDatos($this->tabla, $condicionExiste);
					if($existe1 == 0){
						$datosUsuario = array(
							"nombreUsuario"=>$nombreUsuario,
							"usuarioUsuario"=>$usuarioUsuario,
							"claveUsuario"=>$claveUsuario,
							"codigoUsuario"=>$codigoReal,
							"rolUsuario"=>$rolUsuario,
							"adminUsuario"=>$adminUsuario,
							"autorizadoUsuario"=>$autorizadoUsuario,
							"idSucursalUsuario"=>$sucursalUsuario,
						);
						IniciarTransaccion();
						$condicion = array("md5(idUsuario)" => $idUsuario);
						$editar = EditarDatos($this->tabla,$datosUsuario,$condicion);
						if($editar){
							$error = false;
							$datosUsuarioDb = TraerUnDato($this->tabla,$condicion);
							$idUsuarioDb = $datosUsuarioDb->idUsuario;
							$limpiarDetalles = EliminarDatos($this->tablaPermisos,$condicion);
							// if(!$limpiarDetalles){
							// 	$error = true;
							// }
							if($rolUsuario > 0 && !$adminUsuario){
								$condicionDetalle = array("idRol" => $rolUsuario);
								$rolesDetalle = TraerDatos('usuarioRolesDetalle',$condicionDetalle);
								foreach($rolesDetalle as $rol){
									$datosRolDetalle = array(
										"idUsuario"=>$idUsuarioDb,
										"idModulo"=>$rol->idModulo,
									);
									$guardarPermisos = GuardarDatos($this->tablaPermisos,$datosRolDetalle);
									if(!$guardarPermisos){
										$error = true;
									}
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
					} else {
						$datosRespuesta["codigo"]=400;
					}
				} else{
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function UsuarioEliminar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"]=403;
			echo json_encode($datosRespuesta);
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idUsuario = $this->input->post("idUsuario");
				$condicion = array("md5(idUsuario)" => $idUsuario);
				IniciarTransaccion();
				$eliminar = EliminarDatos($this->tabla,$condicion);
				if($eliminar){
					$eliminarPermisos  = true;
					if(ExistenDatos($this->tablaPermisos,$condicion)){
						$eliminarPermisos = EliminarDatos($this->tablaPermisos,$condicion);
					}
					if($eliminarPermisos){
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

	function UsuarioCambiarEstado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"]=403;
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idUsuario = $this->input->post("idUsuario");
				$condicionDatos = array(
					'md5(idUsuario)' => $idUsuario,
					'activoUsuario' => 1,
				);
				$activoUsuario = ExistenDatos($this->tabla,$condicionDatos);
				if($activoUsuario==0){
					$nuevoEstado = 1;
					$estadoTxt = 'activado';
					$estadoTxtInverso = 'desactivado';
				} else{
					$nuevoEstado = 0;
					$estadoTxt = 'desactivado';
					$estadoTxtInverso = 'activado';
				}
				$datosUsuario = array(
					"activoUsuario" => $nuevoEstado
				);

				$condicion = array("md5(idUsuario)" => $idUsuario);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosUsuario,$condicion);
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

	function UsuarioPermisos($idUsuario=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				// $idUsuario = $this->uri->segment(3);
				$condicionDatos = array('md5(idUsuario)' => $idUsuario);
				$datosUsuario = TraerUnDato($this->tabla,$condicionDatos);
				if($datosUsuario !== false && $idUsuario!=""){
					$idUsuarioDb = $datosUsuario->idUsuario;
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
									$condicionPermisos = array(
										'idUsuario' => $idUsuarioDb,
										'idModulo' => $idModulo,
									);
									$modulo->existeEnPermiso = ExistenDatos($this->tablaPermisos,$condicionPermisos);
									array_push($modulosValidados,$modulo);
								}
							}
							$menu->modulos = $modulosValidados;
							array_push($menusMostrar,$menu);
						}
					}
					$titulo = "Permisos de Usuario";
					$datosVista = array(
						"datosUsuario"=> $datosUsuario,
						"menus"=> $menusMostrar,
						"controlador" => "Usuarios",
						"idUsuario" => $idUsuario,
						"titulo" => $titulo,
						"proceso" => "Editar",
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/usuarios.js"
						),
					);
					GblPlantilla("usuarios/UsuarioPermisos",$datosVista,$extras,$titulo);
				} else{
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			}	else if($this->input->method(TRUE) == "POST"){
				$idUsuario = $this->input->post("idUsuario");
				$adminUsuario = $this->input->post("adminUsuario");
				$listaPermisos = json_decode($this->input->post("listaPermisos"),true);
				$condicion = array("md5(idUsuario)" => $idUsuario);

				$datosUsuario = array(
					'adminUsuario' => $adminUsuario,
					'aleatorioUsuario' => uniqid(),
				);
				IniciarTransaccion();
				$limpiarPermisos = EliminarDatos($this->tablaPermisos,$condicion);
				$editar = EditarDatos($this->tabla,$datosUsuario,$condicion);
				if($editar){
					$error = false;
					if(!$adminUsuario){
						$datosUsuarioDb = TraerUnDato($this->tabla,$condicion);
						$idUsuarioDb = $datosUsuarioDb->idUsuario;
						foreach($listaPermisos as $permiso){
							$datosRolDetalle = array(
								"idUsuario" => $idUsuarioDb,
								"idModulo" => $permiso["idModulo"],
							);
							$guardarPermisos = GuardarDatos($this->tablaPermisos,$datosRolDetalle);
							if(!$guardarPermisos){
								$error = true;
							}
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
				echo json_encode($datosRespuesta);
			}
		}
	}
}
/* End of file Usuarios.php */
