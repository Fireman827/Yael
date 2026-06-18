<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {

	private $tabla = "proveedor";
	private $controlador = "Proveedores";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Proveedores";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-cubes",
				"botones" => array(
					array(
						"icono"=> "fa fa-cube",
						'controlador' => $this->controlador,
						'url' => 'ProveedorAgregar',
						'txt' => 'Agregar Proveedor (Rápido)',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
					array(
						"icono"=> "fa fa-cube",
						'controlador' => $this->controlador,
						'url' => 'ProveedorAgregarAvanzado',
						'txt' => 'Agregar Proveedor (Avanzado)',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Nombre"=>3,
					"NRC"=>2,
					"NIT"=>2,
					"Dirección"=>2,
					"Teléfono"=>1,
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
					"scripts/proveedores.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function ProveedorMostrar(){
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
			0 => 'idProveedor',
			1 => 'nombreProveedor',
			2 => 'nrcProveedor',
			3 => 'nitProveedor',
			4 => 'direccionProveedor',
            5 => 'telefonoProveedor',
            6 => 'activoProveedor'
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
		$condicion = array('idSucursalProveedor' => $sucursal , "estadoProveedor !="=>"Borrado");
		$proveedores = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
		//print_r($proveedores);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($proveedores != 0){
			$datosMostrar = array();
			foreach ($proveedores as $proveedor){
				$estadoProveedor = $proveedor->estadoProveedor;
				if($estadoProveedor=="Activo"){
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
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion ="ProveedorEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					if ($proveedor->avanzadoProveedor == 1)
					{
						$menuOpciones .= "<a class='dropdown-item' href='". base_url()."ProveedorEditarAvanzado/".md5($proveedor->idProveedor)."'><i class='far fa-edit' ></i> Editar (A)</a>";
					}
					else
					{
						$menuOpciones .= "<a class='dropdown-item' href='". base_url()."ProveedorEditarAvanzado/".md5($proveedor->idProveedor)."'><i class='far fa-edit' ></i> Editar (A)</a>";
						$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($proveedor->idProveedor)."'><i class='fas fa-edit' ></i> Editar (N)</a>";
					}
				}

				$funcion = "ProveedorCambiarEstado";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' data-accion='$estadoTxt' idProveedor=".md5($proveedor->idProveedor)."><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ProveedorEliminar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' idProveedor=".md5($proveedor->idProveedor)."><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";
				$datosMostrar[] = array(
					$proveedor->idProveedor,
					$proveedor->nombreProveedor,
					$proveedor->nrcProveedor,
					$proveedor->nitProveedor,
					$proveedor->direccionProveedor,
                    $proveedor->telefonoProveedor,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalProveedores = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalProveedores,
				"recordsFiltered" => $totalProveedores,
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

	function ProveedorAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Agregar Proveedor";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-users",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"back"=> $this->input->get("back") ?? ""
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/proveedores.js"
					),
				);
				GblPlantilla("proveedores/ProveedorAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreProveedor = $this->input->post("nombreProveedor");
				$nrcProveedor = $this->input->post("nrcProveedor");
				$nitProveedor = $this->input->post("nitProveedor");
				$direccionProveedor = $this->input->post("direccionProveedor");
				$telefonoProveedor = $this->input->post("telefonoProveedor");
				$correoProveedor = $this->input->post("correoProveedor");

				$condicionExiste = array('nrcProveedor' => $nrcProveedor, 'estadoProveedor !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosProveedor  = array(
						"nombreProveedor"=>$nombreProveedor,
						"nrcProveedor"=>$nrcProveedor,
						"nitProveedor"=>$nitProveedor,
						"telefonoProveedor"=>$telefonoProveedor,
						"direccionProveedor"=>$direccionProveedor,
						"correoProveedor"=>$correoProveedor,
						"estadoProveedor"=>"Activo",
						"idSucursalProveedor"=>$this->session->idSucursal
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosProveedor);
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

	function ProveedorAgregarAvanzado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {

			if($this->input->method(TRUE) == "GET"){
				$datosDepartamentos = TraerDatos('departamento');
				$departamentosOption = "<option value='' >Seleccione un departamento</option>";

				foreach ($datosDepartamentos as $departamentos):
					$departamentosOption .= "<option value='".$departamentos->idDepartamento."' >".$departamentos->nombreDepartamento."</option>";
				endforeach;

				$titulo = "Agregar Proveedor";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-users",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"departamentos"=> $departamentosOption
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/proveedores.js"
					),
				);
				GblPlantilla("proveedores/ProveedorAgregarAvanzado",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){

				$nombreProveedor = $this->input->post("nombreProveedor");
				$nrcProveedor = $this->input->post("nrcProveedor");
				$nitProveedor = $this->input->post("nitProveedor");
				$direccionProveedor = $this->input->post("direccionProveedor");
				$telefonoProveedor = $this->input->post("telefonoProveedor");
				$correoProveedor = $this->input->post("correoProveedor");
				$razonSocialProveedor = $this->input->post("razonSocialProveedor");
				$departamentoProveedor = $this->input->post("departamentoProveedor");
				$municipioProveedor = $this->input->post("municipioProveedor");
				$categoriaProveedor = $this->input->post("categoriaProveedor");
				$giroProveedor = $this->input->post("giroProveedor");
				$datosContactos = json_decode($this->input->post("datosContactos"));

				$bancoProveedor = $this->input->post("bancoProveedor");
				$cuentaProveedor = $this->input->post("cuentaProveedor");
				$bancoProveedor2 = $this->input->post("bancoProveedor2");
				$cuentaProveedor2 = $this->input->post("cuentaProveedor2");

				$condicionExiste = array('nrcProveedor' => $nrcProveedor, 'estadoProveedor !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosProveedor  = array(
						"nombreProveedor"=>$nombreProveedor,
						"nrcProveedor"=>$nrcProveedor,
						"nitProveedor"=>$nitProveedor,
						"telefonoProveedor"=>$telefonoProveedor,
						"direccionProveedor"=>$direccionProveedor,
						"correoProveedor"=>$correoProveedor,
						"razonSocialProveedor"=>$razonSocialProveedor,
						"departamentoProveedor"=>$departamentoProveedor,
						"municipioProveedor"=>$municipioProveedor,
						"categoriaProveedor"=>$categoriaProveedor,
						"giroProveedor"=>$giroProveedor,
						"bancoProveedor"=>$bancoProveedor,
						"cuentaProveedor"=>$cuentaProveedor,
						"bancoProveedor2"=>$bancoProveedor2,
						"cuentaProveedor2"=>$cuentaProveedor2,
						"avanzadoProveedor"=>1,
						"estadoProveedor"=>"Activo",
						"idSucursalProveedor"=>$this->session->idSucursal
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosProveedor);
					if($guardar){
						if(count($datosContactos)!=0){
							foreach ($datosContactos as $contacto):
								$datosContacto = array(
									"nombreContactoProveedor" => mb_strtoupper($contacto[0],'UTF-8'),
									"cargoContactoProveedor" => mb_strtoupper($contacto[1],'UTF-8'),
									"telefonoContactoProveedor" => $contacto[2],
									"correoContactoProveedor" => $contacto[3],
									"idProveedor" => $guardar
								);
								$guardarContacto = GuardarDatos("proveedorContactos",$datosContacto);
							endforeach;
						}
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

	function ProveedorEditar($idProveedor=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//$idProveedor = $this->uri->segment(3);
				$condicionDatos = array('md5(idProveedor)' => $idProveedor);
				$datosProveedor = TraerUnDato($this->tabla, $condicionDatos);
				if($datosProveedor !== false && $idProveedor!=""){
					$titulo = "Editar Proveedor";
					$datosVista = array(
						"datosProveedor"=> $datosProveedor,
						"controlador" => $this->controlador,
						"idProveedor" => $idProveedor,
						"titulo" => $titulo,
						"proceso" => "Editar",
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/proveedores.js"
						),
					);
					GblPlantilla("proveedores/ProveedorEditar",$datosVista,$extras,$titulo);
				} else{
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idProveedor = $this->input->post("idProveedor");
				$nombreProveedor = $this->input->post("nombreProveedor");
				$nrcProveedor = $this->input->post("nrcProveedor");
				$nitProveedor = $this->input->post("nitProveedor");
				$direccionProveedor = $this->input->post("direccionProveedor");
				$telefonoProveedor = $this->input->post("telefonoProveedor");
				$correoProveedor = $this->input->post("correoProveedor");


				$condicionExiste = array(
					'nrcProveedor' => $nrcProveedor,
					'nitProveedor' => $nitProveedor,
					'idSucursalProveedor' => $this->session->idSucursal,
					'md5(idProveedor)!=' => $idProveedor,
					'estadoProveedor !=' => 'Borrado'
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosProveedor = array(
						"nombreProveedor"=>$nombreProveedor,
						"nrcProveedor"=>$nrcProveedor,
						"nitProveedor"=>$nitProveedor,
						"direccionProveedor"=>$direccionProveedor,
						"telefonoProveedor"=>$telefonoProveedor,
						"correoProveedor"=>$correoProveedor,
						"idSucursalProveedor"=>$this->session->idSucursal,
						"aleatorioProveedor"=>uniqid(),
					);
					IniciarTransaccion();
					$condicion = array("md5(idProveedor)" => $idProveedor);
					$editar = EditarDatos($this->tabla,$datosProveedor,$condicion);
					if($editar){
						//La acción se realizo con éxito
						EjecutarTransaccion();
						$datosRespuesta["codigo"]=200;
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

	function ProveedorEditarAvanzado($idProveedor=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//$idProveedor = $this->uri->segment(3);
				$condicionDatos = array('md5(idProveedor)' => $idProveedor);
				$datosProveedor = TraerUnDato($this->tabla, $condicionDatos);
				if($datosProveedor !== false && $idProveedor!=""){
					//CONSULTA PARA TRAER LOS DEPARTAMENTOS
					$datosDepartamentos = TraerDatos('departamento');
					$departamentosOption = "<option value='' >Seleccione un departamento</option>";

					if($datosProveedor->departamentoProveedor==0){
						foreach ($datosDepartamentos as $departamentos):
							if($departamentos->idDepartamento == 13) $departamentosOption .= "<option value='".$departamentos->idDepartamento."' selected >".$departamentos->nombreDepartamento."</option>";
							else $departamentosOption .= "<option value='".$departamentos->idDepartamento."' >".$departamentos->nombreDepartamento."</option>";
						endforeach;
					} else {
						foreach ($datosDepartamentos as $departamentos):
							if($departamentos->idDepartamento == $datosProveedor->departamentoProveedor) $departamentosOption .= "<option value='".$departamentos->idDepartamento."' selected >".$departamentos->nombreDepartamento."</option>";
							else $departamentosOption .= "<option value='".$departamentos->idDepartamento."' >".$departamentos->nombreDepartamento."</option>";
						endforeach;
					}

					//CONSULTA PARA TRAER LOS MUNICIPIOS
					$datosMunicipios = TraerDatos('municipio',array("idDepartamento"=> ($datosProveedor->departamentoProveedor != 0) ? $datosProveedor->departamentoProveedor : 13 ));
					$municipiosOption = "<option value='' >Seleccione un municipio</option>";

					foreach ($datosMunicipios as $municipios):
						if($datosProveedor->municipioProveedor==$municipios->idMunicipio) $municipiosOption .= "<option value='".$municipios->idMunicipio."' selected >".$municipios->nombreMunicipio."</option>";
						else $municipiosOption .= "<option value='".$municipios->idMunicipio."' >".$municipios->nombreMunicipio."</option>";
					endforeach;

					//CONSULTA PARA TRAER LOS CONTACTOS DEL PROVEEDOR A EDITAR
					$contactosTabla = "";
					$existe = ExistenDatos('proveedorContactos', array("idProveedor" => $datosProveedor->idProveedor));
					if($existe!=0){
						$datosContactos = TraerDatos('proveedorContactos',array("idProveedor"=>$datosProveedor->idProveedor));

						foreach ($datosContactos as $contactos):
							$contactosTabla .= "<tr>";
							$contactosTabla .= "<td><input type='text' class='form-control nombreContacto text-uppercase upper' placeholder='Nombre' value='".$contactos->nombreContactoProveedor."'></td>";
							$contactosTabla .= "<td><input type='text' class='form-control cargoContacto text-uppercase upper' placeholder='Cargo' value='".$contactos->cargoContactoProveedor."'></td>";
							$contactosTabla .= "<td><input type='text' class='form-control telefonoContacto' placeholder='0000-0000' data-mask='0000-0000' value='".$contactos->telefonoContactoProveedor."'></td>";
							$contactosTabla .= "<td><input type='text' class='form-control correoContacto' placeholder='alias@dominio.com' value='".$contactos->correoContactoProveedor."' email ></td>";
							$contactosTabla .= "<td><a class='btn btn-block btn-danger borrarContacto' role='button'><i class='fa fa-trash'></i></a></td>";
							$contactosTabla .= "</tr>";
						endforeach;
					}

					$titulo = "Editar Proveedor Avanzado";
					$datosVista = array(
						"datosProveedor"=> $datosProveedor,
						"controlador" => $this->controlador,
						"idProveedor" => $idProveedor,
						"titulo" => $titulo,
						"proceso" => "Editar",
						"departamentos" => $departamentosOption,
						"municipios" => $municipiosOption,
						"contactos" => $contactosTabla
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/proveedores.js"
						),
					);
					GblPlantilla("proveedores/ProveedorEditarAvanzado",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idProveedor = $this->input->post("idProveedor");
				$nombreProveedor = $this->input->post("nombreProveedor");
				$nrcProveedor = $this->input->post("nrcProveedor");
				$nitProveedor = $this->input->post("nitProveedor");
				$direccionProveedor = $this->input->post("direccionProveedor");
				$telefonoProveedor = $this->input->post("telefonoProveedor");
				$correoProveedor = $this->input->post("correoProveedor");
				$razonSocialProveedor = $this->input->post("razonSocialProveedor");
				$departamentoProveedor = $this->input->post("departamentoProveedor");
				$municipioProveedor = $this->input->post("municipioProveedor");
				$categoriaProveedor = $this->input->post("categoriaProveedor");
				$giroProveedor = $this->input->post("giroProveedor");
				$datosContactos = json_decode($this->input->post("datosContactos"));
				$bancoProveedor = $this->input->post("bancoProveedor");
				$cuentaProveedor = $this->input->post("cuentaProveedor");

				$bancoProveedor2 = $this->input->post("bancoProveedor2");
				$cuentaProveedor2 = $this->input->post("cuentaProveedor2");




				$condicionExiste = array(
					'nrcProveedor' => $nrcProveedor,
					'nitProveedor' => $nitProveedor,
					'idSucursalProveedor' => $this->session->idSucursal,
					'md5(idProveedor)!=' => $idProveedor,
					'estadoProveedor !=' => 'Borrado'
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){

					$condicionDatos = array('md5(idProveedor)' => $idProveedor);
					$dataProveedor = TraerUnDato($this->tabla, $condicionDatos);

					$datosProveedor = array(
						"nombreProveedor"=>$nombreProveedor,
						"nrcProveedor"=>$nrcProveedor,
						"nitProveedor"=>$nitProveedor,
						"direccionProveedor"=>$direccionProveedor,
						"telefonoProveedor"=>$telefonoProveedor,
						"correoProveedor"=>$correoProveedor,
						"razonSocialProveedor"=>$razonSocialProveedor,
						"departamentoProveedor"=>$departamentoProveedor,
						"municipioProveedor"=>$municipioProveedor,
						"categoriaProveedor"=>$categoriaProveedor,
						"giroProveedor"=>$giroProveedor,
						"bancoProveedor"=>$bancoProveedor,
						"cuentaProveedor"=>$cuentaProveedor,

						"bancoProveedor2"=>$bancoProveedor2,
						"cuentaProveedor2"=>$cuentaProveedor2,
						"idSucursalProveedor"=>$this->session->idSucursal,
						"avanzadoProveedor"=>1,
						"aleatorioProveedor"=>uniqid(),
					);
					IniciarTransaccion();
					$condicion = array("md5(idProveedor)" => $idProveedor);
					$editar = EditarDatos($this->tabla,$datosProveedor,$condicion);
					if($editar){

						//PROCESO PARA ELIMINAR LOS DATOS ANTERIORES DE LA TABLA proveedorContactos
						//$existe = ExistenDatos('proveedorContactos', array("idProveedor" => $datosProveedor->idProveedor));
						$existeContactos = ExistenDatos('proveedorContactos', array("md5(idProveedor)" => $idProveedor));
						if($existeContactos!=0){
							$eliminar = EliminarDatos('proveedorContactos',array("md5(idProveedor)"=>$idProveedor));
							if($eliminar){
								//$datosProveedorEditado = TraerUnDato('proveedor', array("md5(idProveedor)" => $idProveedor));
								foreach ($datosContactos as $contacto):
									$datosContacto = array(
										"nombreContactoProveedor" => mb_strtoupper($contacto[0],'UTF-8'),
										"cargoContactoProveedor" => mb_strtoupper($contacto[1],'UTF-8'),
										"telefonoContactoProveedor" => $contacto[2],
										"correoContactoProveedor" => $contacto[3],
										"idProveedor" => $dataProveedor->idProveedor,
									);
									$guardarContacto = GuardarDatos("proveedorContactos",$datosContacto);
								endforeach;
								//La acción se realizo con éxito
								EjecutarTransaccion();
								$datosRespuesta["codigo"] = 200;
							}
						} else {
							foreach ($datosContactos as $contacto):
								$datosContacto = array(
									"nombreContactoProveedor" => mb_strtoupper($contacto[0],'UTF-8'),
									"cargoContactoProveedor" => mb_strtoupper($contacto[1],'UTF-8'),
									"telefonoContactoProveedor" => $contacto[2],
									"correoContactoProveedor" => $contacto[3],
									"idProveedor" => $dataProveedor->idProveedor
								);
								$guardarContacto = GuardarDatos("proveedorContactos",$datosContacto);
							endforeach;
							//La acción se realizo con éxito
							EjecutarTransaccion();
							$datosRespuesta["codigo"] = 200;
						}
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

	function ProveedorEliminar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			//No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"] = 403;
			echo json_encode($datosRespuesta);
		} else {
			if($this->input->method(TRUE) == "POST"){
				$idProveedor = $this->input->post("idProveedor");
				$datosProveedor = array(
					"estadoProveedor" => "Borrado"
				);

				$condicion = array("md5(idProveedor)" => $idProveedor);
				IniciarTransaccion();
				$eliminar = EditarDatos($this->tabla,$datosProveedor,$condicion);
				if($eliminar){
					//La acción se realizo con éxito
					EjecutarTransaccion();
					$datosRespuesta["codigo"]=200;
				} else {
					//La acción no pudo ser realizada
					DeshacerTransaccion();
					$datosRespuesta["codigo"]=402;
				}
			}
		}
		echo json_encode($datosRespuesta);
	}

	function ProveedorCambiarEstado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			//No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"]=403;
		} else {
			if($this->input->method(TRUE) == "POST"){
				$idProveedor = $this->input->post("idProveedor");
				$condicionDatos = array(
					'md5(idProveedor)' => $idProveedor,
					'estadoProveedor' => 'Activo',
					'idSucursalProveedor' => $this->session->idSucursal
				);
				$activoProveedor = ExistenDatos($this->tabla,$condicionDatos);
				if($activoProveedor==0){
					$nuevoEstado = 'Activo';
				} else{
					$nuevoEstado = 'Inactivo';
				}
				$datosProveedor = array(
					"estadoProveedor" => $nuevoEstado
				);

				$condicion = array("md5(idProveedor)" => $idProveedor);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosProveedor,$condicion);
				if($editar){
					//La acción se realizo con exito
					EjecutarTransaccion();
					$datosRespuesta["codigo"] = 200;
				} else {
					//La acción no pudo ser realizada
					DeshacerTransaccion();
					$datosRespuesta["codigo"] = 402;
				}
			}
		}
		echo json_encode($datosRespuesta);
	}

	function ProveedorMunicipios(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			//No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"]=403;
		} else {
			if($this->input->method(TRUE) == "POST"){
				$idDepartamento = $this->input->post("idDepartamento");
				$datosMunicipios = TraerDatos('municipio',array('idDepartamento' => $idDepartamento));
				$municipiosOption = "<option value='' >Seleccione un municipio</option>";
				foreach ($datosMunicipios as $municipios):
					$municipiosOption .= "<option value='".$municipios->idMunicipio."' >".$municipios->nombreMunicipio."</option>";
				endforeach;
				$datosRespuesta["municipios"] = $municipiosOption;
				echo json_encode($datosRespuesta);
			}
		}
	}
}
/* End of file Proveedores.php */
