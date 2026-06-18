<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empleados extends CI_Controller {

	private $tabla = "empleado";
	private $controlador = "Empleados";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Empleados";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fas fas fa-user-tie",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'EmpleadosAgregar',
						'txt' => 'Agregar Empleado (Avanzado)',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'EmpleadosAgregarRapido',
						'txt' => 'Agregar Empleado (Rápido)',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Empleado"=>4,
					"DUI"=>1,
					"Departamento"=>2,
					"Cargo"=>2,
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
					"scripts/empleados.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function EmpleadosMostrar(){
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
			0 => 'idEmpleado',
			1 => 'nombreEmpleado',
			2 => 'duiEmpleado' 
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
		$condicion = array('idSucursalEmpleado' => $sucursal,'estadoEmpleado!=' => 'Borrado');
		$Empleados = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
		$datosCargo = TraerDatos('cargo',array("estadoCargo"=>"Activo"));
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Empleados!= 0){
			$datosMostrar = array();
			foreach ($Empleados as $Empleado) {
                $empleado = $Empleado->nombreEmpleado." ".$Empleado->apellidoEmpleado;
				$cargoEmpleado = "SIN ASIGNAR";
				foreach($datosCargo as $cargo){
					if($cargo->idCargo==$Empleado->idCargoEmpleado){
						$cargoEmpleado = $cargo->nombreCargo;
					} 						
				}
                $estadoEmpleado = $Empleado->estadoEmpleado;
                if ($estadoEmpleado == 'Activo') {
                    $estadoTxt = "Desactivar";
                    $estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
                    $estadoIcon = "fa fa fa-toggle-on";
                } else {
                    $estadoTxt = "Activar";
                    $estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
                    $estadoIcon = "fa fa-toggle-off";
                }
                $menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

                $funcion = "EmpleadosEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
					$letra = ($Empleado->avanzadoEmpleado == 0) ? "(A)" : "";
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($Empleado->idEmpleado)."' ><i class='fa fa-edit' ></i> Editar ".$letra."</a>";
                } 
				$funcion = "EmpleadosEditarRapido";
                if (GblPermisos($this, $funcion, $this->controlador) && $Empleado->avanzadoEmpleado == 0) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($Empleado->idEmpleado)."' ><i class='fa fa-edit' ></i> Editar (R)</a>";
                }
                $funcion = "EmpleadosCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idEmpleado=" . md5($Empleado->idEmpleado) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "EmpleadosEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idEmpleado=" . md5($Empleado->idEmpleado) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $Empleado->idEmpleado,
                    $empleado,
                    $Empleado->duiEmpleado,
                    $Empleado->departamentoEmpleado,
                    $cargoEmpleado,
                    $estadoSpan,
                    $menuOpciones
                );
            }
			$totalEmpleados = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalEmpleados,
				"recordsFiltered" => $totalEmpleados,
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

	function EmpleadosAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			
			if($this->input->method(TRUE) == "GET"){
				//CONSULTA PARA TRAER TODAS LOS CARGOS
				$datosCargo = TraerDatos('cargo',array("estadoCargo"=>"Activo"));
				$cargosOpciones = "";
				if($datosCargo){
					foreach ($datosCargo as $cargo){
						$cargosOpciones .= "<option value='".$cargo->idCargo."' >".$cargo->nombreCargo."</option>"; 						
					}
				}
				$roles = TraerDatos('usuarioRoles');
	
				$titulo = "Agregar Empleado";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fas fa-user-tie",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"cargosOpciones" => $cargosOpciones,
					"roles" => $roles
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/empleados.js"
					),
				);
				GblPlantilla("empleados/EmpleadoAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalEmpleado = $this->session->idSucursal;
				$nombreEmpleado = $this->input->post("nombreEmpleado");
				$apellidoEmpleado = $this->input->post("apellidoEmpleado");
				$direccionEmpleado = $this->input->post("direccionEmpleado");
				$residenciaEmpleado = $this->input->post("residenciaEmpleado");
				$nacionalidadEmpleado = $this->input->post("nacionalidadEmpleado");
				$fechaNacimientoEmpleado = $this->input->post("fechaNacimientoEmpleado");
				$sexoEmpleado = $this->input->post("sexoEmpleado");
				$estadoCivilEmpleado = $this->input->post("estadoCivilEmpleado");
				$profesionOficioEmpleado = $this->input->post("profesionOficioEmpleado");
				$nitEmpleado = $this->input->post("nitEmpleado");
				$duiEmpleado = $this->input->post("duiEmpleado");
				$expedicionDuiEmpleado = $this->input->post("expedicionDuiEmpleado");
				$telefono1Empleado = $this->input->post("telefono1Empleado");
				$telefono2Empleado = $this->input->post("telefono2Empleado");
				$emailEmpleado = $this->input->post("emailEmpleado");
				$sangreEmpleado = $this->input->post("sangreEmpleado");
				$idCargoEmpleado = $this->input->post("idCargoEmpleado");
				$salarioBaseEmpleado = $this->input->post("salarioBaseEmpleado");
				$departamentoEmpleado = $this->input->post("departamentoEmpleado");
				$modalidadEmpleado = $this->input->post("modalidadEmpleado");
				$documentoEmpleado = $this->input->post("documentoEmpleado");
				$fechaContratacionEmpleado = $this->input->post("fechaContratacionEmpleado");
				$fechaCeseEmpleado = $this->input->post("fechaCeseEmpleado");
				$afpEmpleado = $this->input->post("afpEmpleado");
				$isssEmpleado = $this->input->post("isssEmpleado");
				$rentaEmpleado = $this->input->post("rentaEmpleado");
				$afiliadoAfpEmpleado = $this->input->post("afiliadoAfpEmpleado");
				$formaPagoEmpleado = $this->input->post("formaPagoEmpleado");
				$familiaresEmpleado = $this->input->post("familiaresEmpleado");

				$condicionExiste = array('nitEmpleado' => $nitEmpleado,'duiEmpleado' => $duiEmpleado,'idSucursalEmpleado'=>$idSucursalEmpleado);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosEmpleados  = array(
						"avanzadoEmpleado"=>1,
						"nombreEmpleado"=>$nombreEmpleado,
						"apellidoEmpleado"=>$apellidoEmpleado,
						"direccionEmpleado"=>$direccionEmpleado,
						"residenciaEmpleado"=>$residenciaEmpleado,
						"nacionalidadEmpleado"=>$nacionalidadEmpleado,
						"fechaNacimientoEmpleado"=>$fechaNacimientoEmpleado,
						"sexoEmpleado"=>$sexoEmpleado,
						"estadoCivilEmpleado"=>$estadoCivilEmpleado,
						"profesionOficioEmpleado"=>$profesionOficioEmpleado,
						"nitEmpleado"=>$nitEmpleado,
						"duiEmpleado"=>$duiEmpleado,
						"expedicionDuiEmpleado"=>$expedicionDuiEmpleado,
						"telefono1Empleado"=>$telefono1Empleado,
						"telefono2Empleado"=>$telefono2Empleado,
						"emailEmpleado"=>$emailEmpleado,
						"sangreEmpleado"=>$sangreEmpleado,
						"idCargoEmpleado"=>$idCargoEmpleado,
						"salarioBaseEmpleado"=>$salarioBaseEmpleado,
						"departamentoEmpleado"=>$departamentoEmpleado,
						"modalidadEmpleado"=>$modalidadEmpleado,
						"documentoEmpleado"=>$documentoEmpleado,
						"fechaContratacionEmpleado"=>$fechaContratacionEmpleado,
						"fechaCeseEmpleado"=>$fechaCeseEmpleado,
						"afpEmpleado"=>$afpEmpleado,
						"isssEmpleado"=>$isssEmpleado,
						"rentaEmpleado"=>$rentaEmpleado,
						"afiliadoAfpEmpleado"=>$afiliadoAfpEmpleado,
						"formaPagoEmpleado"=>$formaPagoEmpleado,
						"familiaresEmpleado"=>$familiaresEmpleado,
						"estadoEmpleado"=> 'Activo',
						"aleatorioEmpleado" => uniqid(),
						"idSucursalEmpleado"=>$this->session->idSucursal
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosEmpleados);
					if($guardar){
						//La acción se realizo con éxito						
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					} else {
						//La acción no pudo ser realizada
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 402;
					}
				} else {
					//La acción no se pudo realizar porque ya existe un registro con los mismos datos
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function EmpleadosAgregarRapido(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			
			if($this->input->method(TRUE) == "GET"){
				//CONSULTA PARA TRAER TODAS LOS CARGOS
				$datosCargo = TraerDatos('cargo',array("estadoCargo"=>"Activo"));
				$cargosOpciones = "";
				if($datosCargo){
					foreach ($datosCargo as $cargo){
						$cargosOpciones .= "<option value='".$cargo->idCargo."' >".$cargo->nombreCargo."</option>"; 						
					}
				}
	
				$titulo = "Agregar Empleado";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fas fa-user-tie",
					"controlador"=> $this->controlador,
					"proceso"=> "AgregarRapido",
					"cargosOpciones" => $cargosOpciones
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/empleados.js"
					),
				);
				GblPlantilla("empleados/EmpleadoAgregarRapido",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalEmpleado = $this->session->idSucursal;
				$nombreEmpleado = $this->input->post("nombreEmpleado");
				$apellidoEmpleado = $this->input->post("apellidoEmpleado");
				$direccionEmpleado = $this->input->post("direccionEmpleado");
				$sexoEmpleado = $this->input->post("sexoEmpleado");
				$estadoCivilEmpleado = $this->input->post("estadoCivilEmpleado");
				$nitEmpleado = $this->input->post("nitEmpleado");
				$duiEmpleado = $this->input->post("duiEmpleado");
				$telefono1Empleado = $this->input->post("telefono1Empleado");
				$emailEmpleado = $this->input->post("emailEmpleado");
				$idCargoEmpleado = $this->input->post("idCargoEmpleado");
				$salarioBaseEmpleado = $this->input->post("salarioBaseEmpleado");
				

				$condicionExiste = array('nitEmpleado' => $nitEmpleado,'duiEmpleado' => $duiEmpleado,'idSucursalEmpleado'=>$idSucursalEmpleado);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosEmpleados  = array(
						"avanzadoEmpleado"=>0,
						"nombreEmpleado"=>$nombreEmpleado,
						"apellidoEmpleado"=>$apellidoEmpleado,
						"direccionEmpleado"=>$direccionEmpleado,
						"sexoEmpleado"=>$sexoEmpleado,
						"estadoCivilEmpleado"=>$estadoCivilEmpleado,
						"nitEmpleado"=>$nitEmpleado,
						"duiEmpleado"=>$duiEmpleado,
						"telefono1Empleado"=>$telefono1Empleado,
						"emailEmpleado"=>$emailEmpleado,
						"idCargoEmpleado"=>$idCargoEmpleado,
						"salarioBaseEmpleado"=>$salarioBaseEmpleado,
						"estadoEmpleado"=> 'Activo',
						"idSucursalEmpleado"=>$this->session->idSucursal
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosEmpleados);
					if($guardar){
						//La acción se realizo con éxito						
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					} else {
						//La acción no pudo ser realizada
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 402;
					}
				} else {
					//La acción no se pudo realizar porque ya existe un registro con los mismos datos
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function EmpleadosEditar($idEmpleado=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idEmpleado)' => $idEmpleado);
				$datosEmpleados = TraerUnDato($this->tabla, $condicionDatos);
				if($datosEmpleados !== false && $idEmpleado!=""){
					$datosCargo = TraerDatos('cargo',array("estadoCargo"=>"Activo"));
					$cargosOpciones = "";
					if($datosCargo){
						foreach ($datosCargo as $cargo){
							if($cargo->idCargo==$datosEmpleados->idCargoEmpleado){
								$cargosOpciones .= "<option value='".$cargo->idCargo."' selected >".$cargo->nombreCargo."</option>"; 
							} else {
								$cargosOpciones .= "<option value='".$cargo->idCargo."' >".$cargo->nombreCargo."</option>"; 
							}								           						
                    	}
					}

					//LISTA DE FAMILIARES
					$familiaresEmpleado = "";
					$campos = explode("#", $datosEmpleados->familiaresEmpleado);
					$ncampos = count($campos);
					for($i=0; $i<($ncampos-1); $i++)
					{
						list($nombre, $apellido, $parentesco) = explode("/",$campos[$i]);
						$familiaresEmpleado .= "<tr><td class='nombre'>$nombre</td><td class='apellido'>$apellido</td><td class='parentesco'>$parentesco</td><td class='text-center'><a class='btn btn-danger btn-block  EmpleadosFamiliaBorrar' type='button'> <span class='fa fa-trash'></span> </a></td></tr>";
					}
												
					$titulo = "Editar Empleado";
					$datosVista = array(
						"datosEmpleados"=> $datosEmpleados,
						"controlador" => $this->controlador,
						"idEmpleado" => $idEmpleado,
						"titulo" => $titulo,
						"proceso" => "Editar",
                        "icono" => "fas fas fa-user-tie",
						"cargosOpciones" => $cargosOpciones,
						"familiaresEmpleado" => $familiaresEmpleado
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/empleados.js"
						),
					);
					GblPlantilla("empleados/EmpleadoEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idEmpleado = $this->input->post("idEmpleado");
				$idSucursalEmpleado = $this->session->idSucursal;
				$nombreEmpleado = $this->input->post("nombreEmpleado");
				$apellidoEmpleado = $this->input->post("apellidoEmpleado");
				$direccionEmpleado = $this->input->post("direccionEmpleado");
				$residenciaEmpleado = $this->input->post("residenciaEmpleado");
				$nacionalidadEmpleado = $this->input->post("nacionalidadEmpleado");
				$fechaNacimientoEmpleado = $this->input->post("fechaNacimientoEmpleado");
				$sexoEmpleado = $this->input->post("sexoEmpleado");
				$estadoCivilEmpleado = $this->input->post("estadoCivilEmpleado");
				$profesionOficioEmpleado = $this->input->post("profesionOficioEmpleado");
				$nitEmpleado = $this->input->post("nitEmpleado");
				$duiEmpleado = $this->input->post("duiEmpleado");
				$expedicionDuiEmpleado = $this->input->post("expedicionDuiEmpleado");
				$telefono1Empleado = $this->input->post("telefono1Empleado");
				$telefono2Empleado = $this->input->post("telefono2Empleado");
				$emailEmpleado = $this->input->post("emailEmpleado");
				$sangreEmpleado = $this->input->post("sangreEmpleado");
				$idCargoEmpleado = $this->input->post("idCargoEmpleado");
				$salarioBaseEmpleado = $this->input->post("salarioBaseEmpleado");
				$departamentoEmpleado = $this->input->post("departamentoEmpleado");
				$modalidadEmpleado = $this->input->post("modalidadEmpleado");
				$documentoEmpleado = $this->input->post("documentoEmpleado");
				$fechaContratacionEmpleado = $this->input->post("fechaContratacionEmpleado");
				$fechaCeseEmpleado = $this->input->post("fechaCeseEmpleado");
				$afpEmpleado = $this->input->post("afpEmpleado");
				$isssEmpleado = $this->input->post("isssEmpleado");
				$rentaEmpleado = $this->input->post("rentaEmpleado");
				$afiliadoAfpEmpleado = $this->input->post("afiliadoAfpEmpleado");
				$formaPagoEmpleado = $this->input->post("formaPagoEmpleado");
				$familiaresEmpleado = $this->input->post("familiaresEmpleado");
			
				$condicionExiste = array(
					'nombreEmpleado' => $nombreEmpleado,
					'apellidoEmpleado' => $apellidoEmpleado,
					'duiEmpleado' => $duiEmpleado,
					'nitEmpleado' => $nitEmpleado,
					'idSucursalEmpleado' => $this->session->idSucursal,
					'md5(idEmpleado)!=' => $idEmpleado
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosEmpleados = array(
						"avanzadoEmpleado"=>1,
						"nombreEmpleado"=>$nombreEmpleado,
						"apellidoEmpleado"=>$apellidoEmpleado,
						"direccionEmpleado"=>$direccionEmpleado,
						"residenciaEmpleado"=>$residenciaEmpleado,
						"nacionalidadEmpleado"=>$nacionalidadEmpleado,
						"fechaNacimientoEmpleado"=>$fechaNacimientoEmpleado,
						"sexoEmpleado"=>$sexoEmpleado,
						"estadoCivilEmpleado"=>$estadoCivilEmpleado,
						"profesionOficioEmpleado"=>$profesionOficioEmpleado,
						"nitEmpleado"=>$nitEmpleado,
						"duiEmpleado"=>$duiEmpleado,
						"expedicionDuiEmpleado"=>$expedicionDuiEmpleado,
						"telefono1Empleado"=>$telefono1Empleado,
						"telefono2Empleado"=>$telefono2Empleado,
						"emailEmpleado"=>$emailEmpleado,
						"sangreEmpleado"=>$sangreEmpleado,
						"idCargoEmpleado"=>$idCargoEmpleado,
						"salarioBaseEmpleado"=>$salarioBaseEmpleado,
						"departamentoEmpleado"=>$departamentoEmpleado,
						"modalidadEmpleado"=>$modalidadEmpleado,
						"documentoEmpleado"=>$documentoEmpleado,
						"fechaContratacionEmpleado"=>$fechaContratacionEmpleado,
						"fechaCeseEmpleado"=>$fechaCeseEmpleado,
						"afpEmpleado"=>$afpEmpleado,
						"isssEmpleado"=>$isssEmpleado,
						"rentaEmpleado"=>$rentaEmpleado,
						"afiliadoAfpEmpleado"=>$afiliadoAfpEmpleado,
						"formaPagoEmpleado"=>$formaPagoEmpleado,
						"familiaresEmpleado"=>$familiaresEmpleado,
						"idSucursalEmpleado"=>$this->session->idSucursal,
						"aleatorioEmpleado"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("md5(idEmpleado)" => $idEmpleado);
					$editar = EditarDatos($this->tabla,$datosEmpleados,$condicion);
					if($editar){
						//La acción se realizo con éxito						
                        EjecutarTransaccion();
                        $datosRespuesta["codigo"] = 200;
					} else {
						//La acción no pudo ser realizada
						DeshacerTransaccion();						
						$datosRespuesta["codigo"]=402;
					}
				} else {
					//La acción no se pudo realizar porque ya existe un registro con los mismos datos					
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function EmpleadosEditarRapido($idEmpleado=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idEmpleado)' => $idEmpleado);
				$datosEmpleados = TraerUnDato($this->tabla, $condicionDatos);
				if($datosEmpleados !== false && $idEmpleado!=""){
					$datosCargo = TraerDatos('cargo',array("estadoCargo"=>"Activo"));
					$cargosOpciones = "";
					if($datosCargo){
						foreach ($datosCargo as $cargo){
							if($cargo->idCargo==$datosEmpleados->idCargoEmpleado){
								$cargosOpciones .= "<option value='".$cargo->idCargo."' selected >".$cargo->nombreCargo."</option>"; 
							} else {
								$cargosOpciones .= "<option value='".$cargo->idCargo."' >".$cargo->nombreCargo."</option>"; 
							}								           						
                    	}
					}

					//LISTA DE FAMILIARES
					$familiaresEmpleado = "";
					$campos = explode("#", $datosEmpleados->familiaresEmpleado);
					$ncampos = count($campos);
					for($i=0; $i<($ncampos-1); $i++)
					{
						list($nombre, $apellido, $parentesco) = explode("/",$campos[$i]);
						$familiaresEmpleado .= "<tr><td class='nombre'>$nombre</td><td class='apellido'>$apellido</td><td class='parentesco'>$parentesco</td><td class='text-center'><a class='EmpleadosFamiliaBorrar' type='button'> <span class='fa fa-trash'></span> </a></td></tr>";
					}
												
					$titulo = "Editar Empleado";
					$datosVista = array(
						"datosEmpleados"=> $datosEmpleados,
						"controlador" => $this->controlador,
						"idEmpleado" => $idEmpleado,
						"titulo" => $titulo,
						"proceso" => "EditarRapido",
                        "icono" => "fas fas fa-user-tie",
						"cargosOpciones" => $cargosOpciones,
						"familiaresEmpleado" => $familiaresEmpleado
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/empleados.js"
						),
					);
					GblPlantilla("empleados/EmpleadoEditarRapido",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idEmpleado = $this->input->post("idEmpleado");
				$nombreEmpleado = $this->input->post("nombreEmpleado");
				$apellidoEmpleado = $this->input->post("apellidoEmpleado");
				$direccionEmpleado = $this->input->post("direccionEmpleado");
				$fechaNacimientoEmpleado = $this->input->post("fechaNacimientoEmpleado");
				$sexoEmpleado = $this->input->post("sexoEmpleado");
				$estadoCivilEmpleado = $this->input->post("estadoCivilEmpleado");
				$nitEmpleado = $this->input->post("nitEmpleado");
				$duiEmpleado = $this->input->post("duiEmpleado");
				$telefono1Empleado = $this->input->post("telefono1Empleado");
				$emailEmpleado = $this->input->post("emailEmpleado");
				$idCargoEmpleado = $this->input->post("idCargoEmpleado");
				$salarioBaseEmpleado = $this->input->post("salarioBaseEmpleado");
			
				$condicionExiste = array(
					'nombreEmpleado' => $nombreEmpleado,
					'apellidoEmpleado' => $apellidoEmpleado,
					'duiEmpleado' => $duiEmpleado,
					'nitEmpleado' => $nitEmpleado,
					'idSucursalEmpleado' => $this->session->idSucursal,
					'md5(idEmpleado)!=' => $idEmpleado
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosEmpleados = array(
						"avanzadoEmpleado"=>0,
						"nombreEmpleado"=>$nombreEmpleado,
						"apellidoEmpleado"=>$apellidoEmpleado,
						"direccionEmpleado"=>$direccionEmpleado,
						"fechaNacimientoEmpleado"=>$fechaNacimientoEmpleado,
						"sexoEmpleado"=>$sexoEmpleado,
						"estadoCivilEmpleado"=>$estadoCivilEmpleado,
						"nitEmpleado"=>$nitEmpleado,
						"duiEmpleado"=>$duiEmpleado,
						"telefono1Empleado"=>$telefono1Empleado,
						"emailEmpleado"=>$emailEmpleado,
						"idCargoEmpleado"=>$idCargoEmpleado,
						"salarioBaseEmpleado"=>$salarioBaseEmpleado,
						"aleatorioEmpleado"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("md5(idEmpleado)" => $idEmpleado);
					$editar = EditarDatos($this->tabla,$datosEmpleados,$condicion);
					if($editar){
						//La acción se realizo con éxito						
                        EjecutarTransaccion();
                        $datosRespuesta["codigo"] = 200;
					} else {
						//La acción no pudo ser realizada
						DeshacerTransaccion();						
						$datosRespuesta["codigo"]=402;
					}
				} else {
					//La acción no se pudo realizar porque ya existe un registro con los mismos datos					
					$datosRespuesta["codigo"]=400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function EmpleadosCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleado = $this->input->post("idEmpleado");
                $condicionDatos = array(
                    'md5(idEmpleado)' => $idEmpleado,
                    'estadoEmpleado' => 'Activo',
                );
                $activoEmpleado = ExistenDatos($this->tabla, $condicionDatos);

                ($activoEmpleado == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosEmpleado = array(
                    "estadoEmpleado" => $nuevoEstado
                );
                $condicion = array("md5(idEmpleado)" => $idEmpleado);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleado, $condicion);
                if ($editar) {
                    EjecutarTransaccion();
                    $datosRespuesta["codigo"] = 200;
                } else {
                    DeshacerTransaccion();
                    $datosRespuesta["codigo"] = 500;
                }
            }
        }
        echo json_encode($datosRespuesta);
    }

    function EmpleadosEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleado = $this->input->post("idEmpleado");
                $datosEmpleado = array(
                    "estadoEmpleado" => 'Borrado'
                );
                $condicion = array("md5(idEmpleado)" => $idEmpleado);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleado, $condicion);
                if ($editar) {
                    EjecutarTransaccion();
                    $datosRespuesta["codigo"] = 200;
                } else {
                    DeshacerTransaccion();
                    $datosRespuesta["codigo"] = 500;
                }
            }
        }
        echo json_encode($datosRespuesta);
    }
    
}
/* End of file Empleados.php */
