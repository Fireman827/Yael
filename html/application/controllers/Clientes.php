<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends CI_Controller {

	private $tabla = "cliente";
	//private $tablaPermisos = "usuarioPermisos";
	private $controlador = "Clientes";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Clientes";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-users",
				"botones" => array(
					// array(
					// 	"icono"=> "fa fa-user-plus",
					// 	'controlador' => $this->controlador,
					// 	'url' => 'ClienteAgregar',
					// 	'txt' => 'Agregar Cliente (Rápido)',
					// 	'posicion' => 'right', // left, right
					// 	'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
					// 	'modal' => false,
					// ),
					array(
						"icono"=> "fa fa-user-plus",
						'controlador' => $this->controlador,
						'url' => 'ClienteAgregarAvanzado',
						'txt' => 'Agregar Cliente',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Nombre"=>2,
					"DUI/NIT"=>1,
					"Dirección"=>2,
					"Teléfono"=>2,
					"Correo"=>1,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/clientes.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function ClienteMostrar(){
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
			0 => 'idCliente',
			1 => 'nombreCliente',
			2 => 'direccionCliente',
			3 => 'telefonoCliente',
			4 => 'duiCliente',
			5 => 'nitCliente',
			6 => 'emailCliente',
			7 => 'referenciaCliente',
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
		$condicion = array('idSucursalCliente' => $sucursal);
		$clientes = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($usuarios);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($clientes != 0){
			$datosMostrar = array();
			foreach ($clientes as $cliente){
				$estadoCliente = $cliente->estadoCliente;
				if($estadoCliente=="Activo"){
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

				$funcion ="ClienteEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					// if ($cliente->avanzadoCliente == 1){
					// 	$menuOpciones .= "<a class='dropdown-item' href='". base_url()."ClienteEditarAvanzado/".md5($cliente->idCliente)."'><i class='far fa-edit' ></i> Editar (A)</a>";
					// } else {
					$menuOpciones .= "<a class='dropdown-item' href='". base_url()."ClienteEditarAvanzado/".md5($cliente->idCliente)."'><i class='far fa-edit' ></i> Editar</a>";
					// $menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($cliente->idCliente)."'><i class='fas fa-edit' ></i> Editar (N)</a>";
					// }
				}
				$funcion = "ClienteCambiarEstado";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' data-accion='$estadoTxt' idCliente=".md5($cliente->idCliente)."><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ClienteEliminar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' idUsuario=".md5($cliente->idCliente)."><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";
				$documentoCliente = $cliente->nitCliente;
				if($cliente->facturarConCliente == "DUI"){
					$documentoCliente = $cliente->duiCliente;
				}
				$datosMostrar[] = array(
					$cliente->idCliente,
					$cliente->nombreCliente,
					$documentoCliente,
					$cliente->direccionCliente,
					$cliente->telefonoCliente,
					$cliente->emailCliente,
					$menuOpciones,
				);
			}
			$totalClientes = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalClientes,
				"recordsFiltered" => $totalClientes,
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

	function ClienteAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$categoriasCliente = TraerDatos('clienteCategoria',array('idSucursalClienteCategoria'=>$this->session->idSucursal,'estadoClienteCategoria'=>'Activo'));
				$categoriasClienteOption = "<option value='' >Seleccione una categoría de cliente</option>";
				foreach ($categoriasCliente as $categoria) {
					$categoriasClienteOption .= "<option value='".$categoria->idClienteCategoria."'>".$categoria->nombreClienteCategoria."</option>";
				}
				$titulo = "Agregar Cliente";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-users",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"categoriasClienteOption"=> $categoriasClienteOption
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/clientes.js"
					),
				);
				GblPlantilla("clientes/ClienteAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreCliente = $this->input->post("nombreCliente");
				$telefonoCliente = $this->input->post("telefonoCliente");
				$direccionCliente = $this->input->post("direccionCliente");
				$referenciaCliente = $this->input->post("referenciaCliente");
				$idCategoriaCliente = $this->input->post("idCategoriaCliente");
				$condicionExiste = array('nombreCliente'=>$nombreCliente,'telefonoCliente' => $telefonoCliente, 'estadoCliente !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if(!$existe){
					$datosCliente = array(
						"idSucursalCliente"=>$this->session->idSucursal,
						"nombreCliente"=>$nombreCliente,
						"telefonoCliente"=>$telefonoCliente,
						"direccionCliente"=>$direccionCliente,
						"referenciaCliente"=>$referenciaCliente,
						"idCategoriaCliente"=>$idCategoriaCliente
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosCliente);
					if($guardar){
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
					} else {
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 402;
					}
				} else {
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}

	function ClienteEditar($idCliente=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){

				$condicionDatos = array('md5(idCliente)' => $idCliente);
				$datosCliente = TraerUnDato($this->tabla, $condicionDatos);

				//TRAER CATEGORIAS DE CLIENTES
				$categoriasCliente = TraerDatos('clienteCategoria',array('idSucursalClienteCategoria'=>$this->session->idSucursal,'estadoClienteCategoria'=>'Activo'));
				$categoriasClienteOption = "<option value='' >Seleccione una categoría de cliente</option>";

				foreach ($categoriasCliente as $categoria) {
					if ($categoria->idClienteCategoria==$datosCliente->idCategoriaCliente) {
						$categoriasClienteOption .= "<option value='".$categoria->idClienteCategoria."' selected >".$categoria->nombreClienteCategoria."</option>";
					} else {
						$categoriasClienteOption .= "<option value='".$categoria->idClienteCategoria."' >".$categoria->nombreClienteCategoria."</option>";
					}
				}

				if($datosCliente !== false && $idCliente!=""){
					$titulo = "Editar Cliente";
					$datosVista = array(
						"datosCliente"=> $datosCliente,
						"controlador" => $this->controlador,
						"idCliente" => $idCliente,
						"titulo" => $titulo,
						"proceso" => "Editar",
						"categoriasClienteOption" => $categoriasClienteOption
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/clientes.js"
						),
					);
					GblPlantilla("clientes/ClienteEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idCliente = $this->input->post("idCliente");
				$nombreCliente = $this->input->post("nombreCliente");
				$telefonoCliente = $this->input->post("telefonoCliente");
				$direccionCliente = $this->input->post("direccionCliente");
				$referenciaCliente = $this->input->post("referenciaCliente");
				$idCategoriaCliente = $this->input->post("idCategoriaCliente");

				$condicionExiste = array(
					'nombreCliente' => $nombreCliente,
					'telefonoCliente' => $telefonoCliente,
					'md5(idCliente)!=' => $idCliente,
					'estadoCliente !=' => 'Borrado'
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosCliente = array(
						"idSucursalCliente"=>$this->session->idSucursal,
						"nombreCliente"=>$nombreCliente,
						"telefonoCliente"=>$telefonoCliente,
						"direccionCliente"=>$direccionCliente,
						"referenciaCliente"=>$referenciaCliente,
						"idCategoriaCliente"=>$idCategoriaCliente,
						"aleatorioCliente"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("md5(idCliente)" => $idCliente);
					$editar = EditarDatos($this->tabla,$datosCliente,$condicion);
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

	function ClienteAgregarAvanzado($param=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//TRAER DEPARTAMENTOS
				$datosDepartamentos = TraerDatos('FE_CAT_012_Departamento');
				$departamentosOption = "<option value='' >Seleccione un departamento</option>";

				foreach ($datosDepartamentos as $departamentos):
					$departamentosOption .= "<option value='".$departamentos->codigo."' >".$departamentos->valores."</option>";
				endforeach;

				//TRAER CATEGORIAS DE CLIENTES
				$categoriasCliente = TraerDatos('FE_CAT_019_CodigodeActividadEco');
				$categoriasClienteOption = "<option value='' >Seleccione una giro</option>";

				if (!empty($categoriasCliente) && (is_array($categoriasCliente) || is_object($categoriasCliente))) {
    foreach ($categoriasCliente as $categoria) {
        $categoriasClienteOption .= "<option value='".$categoria->codigo."'>".$categoria->codigo." - ".$categoria->valores."</option>";
    }
} else {
    $categoriasClienteOption .= "<option value=''>Sin categorías disponibles</option>";
}

				$titulo = "Agregar Cliente";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-users",
					"controlador"=> "Clientes",
					"proceso"=> "Agregar",
					"departamentos"=> $departamentosOption,
					"param"=> $param,
					"categoriasClienteOption"=> $categoriasClienteOption
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/clientes.js"
					),
				);
				GblPlantilla("clientes/ClienteAgregarAvanzado",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreCliente = $this->input->post("nombreCliente");
				$nitCliente = $this->input->post("nitCliente");
				$duiCliente = $this->input->post("duiCliente");
				$datosCliente = $this->input->post();
				$datosCliente["idSucursalCliente"] = $this->session->idSucursal;
				$condicionExiste = array('nombreCliente' => $nombreCliente,'nitCliente' => $nitCliente,'duiCliente' => $duiCliente, 'estadoCliente !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					// $datosCliente = array(
					// 	"idSucursalCliente"=>$this->session->idSucursal,
					// 	"nombreCliente"=>$nombreCliente,
					// 	"telefonoCliente"=>$telefonoCliente,
					// 	"direccionCliente"=>$direccionCliente,
					// 	"referenciaCliente"=>$referenciaCliente,
					// 	"duiCliente"=>$duiCliente,
					// 	"nitCliente"=>$nitCliente,
					// 	"emailCliente"=>$emailCliente,
					// 	"departamentoCliente"=>$departamentoCliente,
					// 	"municipioCliente"=>$municipioCliente,
					// 	"nrcCliente"=>$nrcCliente,
					// 	"idCategoriaCliente"=>$idCategoriaCliente,
					// 	"avanzadoCliente" => 1,
					// );
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosCliente);
					if($guardar){
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

	function ClienteEditarAvanzado($idCliente="",$param=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//$idCliente = $this->uri->segment(3);
				$condicionDatos = array('md5(idCliente)' => $idCliente);
				$datosCliente = TraerUnDato($this->tabla, $condicionDatos);
				if($datosCliente !== false && $idCliente!=""){
					//CONSULTA PARA TRAER LOS DEPARTAMENTOS
					$datosDepartamentos = TraerDatos('FE_CAT_012_Departamento');
					$departamentosOption = "<option value='' >Seleccione un departamento</option>";

					if($datosDepartamentos) foreach ($datosDepartamentos as $departamentos):
						if($departamentos->codigo == $datosCliente->departamentoCliente) $departamentosOption .= "<option value='".$departamentos->codigo."' selected >".$departamentos->valores."</option>";
						else $departamentosOption .= "<option value='".$departamentos->codigo."' >".$departamentos->valores."</option>";
					endforeach;

					//CONSULTA PARA TRAER LOS MUNICIPIOS
					$datosMunicipios = TraerDatos('FE_CAT_013_Municipio',array("departamento" => $datosCliente->departamentoCliente));
					$municipiosOption = "<option value='' >Seleccione un municipio</option>";
					foreach ($datosMunicipios as $municipios):
						if($datosCliente->municipioCliente==$municipios->codigo) $municipiosOption .= "<option value='".$municipios->codigo."' selected >".$municipios->valores."</option>";
						else $municipiosOption .= "<option value='".$municipios->codigo."' >".$municipios->valores."</option>";
					endforeach;

					//TRAER CATEGORIAS DE CLIENTES
					$categoriasCliente = TraerDatos('FE_CAT_019_CodigodeActividadEco');
					$categoriasClienteOption = "<option value='' >Seleccione un giro</option>";

					if($categoriasCliente) foreach ($categoriasCliente as $categoria) {
						if ($categoria->codigo==$datosCliente->giroCliente) {
							$categoriasClienteOption .= "<option value='".$categoria->codigo."' selected >".$categoria->codigo." - ".$categoria->valores."</option>";
						} else {
							$categoriasClienteOption .= "<option value='".$categoria->codigo."' >".$categoria->codigo." - ".$categoria->valores."</option>";
						}
					}

					$titulo = "Editar Cliente";
					$datosVista = array(
						"datosCliente"=> $datosCliente,
						"controlador" => $this->controlador,
						"idCliente" => $idCliente,
						"titulo" => $titulo,
						"icono"=> "fa fa-users",
						"proceso" => "Editar",
						"departamentos" => $departamentosOption,
						"municipios" => $municipiosOption,
						"categoriasClienteOption" => $categoriasClienteOption,
						"param" => $param
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/clientes.js"
						),
					);
					GblPlantilla("clientes/ClienteEditarAvanzado",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idCliente = $this->input->post("idCliente");
				$nombreCliente = $this->input->post("nombreCliente");
				$telefonoCliente = $this->input->post("telefonoCliente");
				$referenciaCliente = $this->input->post("referenciaCliente");
				$nitCliente = $this->input->post("nitCliente");
				$nrcCliente = $this->input->post("nrcCliente");
				$datosCliente = $this->input->post();
				$datosCliente["aleatorioCliente"] = uniqid();
				unset($datosCliente["idCliente"]);
				$existe = 0;
				if($documentoFacturacionCliente == "CCF" && $nitCliente != "" && $nrcCliente != ""){
					$condicionExiste = array(
						'nombreCliente' => $nombreCliente,
						'nrcCliente' => $nrcCliente,
						'nitCliente' => $nitCliente,
						'md5(idCliente)!=' => $idCliente,
						'estadoCliente !=' => 'Borrado'
					);
					$existe = ExistenDatos($this->tabla, $condicionExiste);
				}
				if($existe==0){
					 $datosCliente = array(
					 	"idSucursalCliente"=>$this->session->idSucursal,
					 	"nombreCliente"=>$nombreCliente,
					 	"telefonoCliente"=>$telefonoCliente,
					 	"direccionCliente"=>$direccionCliente,
					 	"referenciaCliente"=>$referenciaCliente,
					 	"duiCliente"=>$duiCliente,
					 	"nitCliente"=>$nitCliente,
					 	"emailCliente"=>$emailCliente,
					 	"departamentoCliente"=>$departamentoCliente,
					 	"municipioCliente"=>$municipioCliente,
					 	"nrcCliente"=>$nrcCliente,
					 	"idCategoriaCliente"=>$idCategoriaCliente,
					 	"avanzadoCliente" => 1,
					 	"aleatorioCliente"=>uniqid()
					 );
					IniciarTransaccion();
					$condicion = array("md5(idCliente)" => $idCliente);
					$editar = EditarDatos($this->tabla,$datosCliente,$condicion);
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

	function ClienteEliminar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			//No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"] = 403;
			echo json_encode($datosRespuesta);
		} else {
			if($this->input->method(TRUE) == "POST"){
				$idCliente = $this->input->post("idCliente");
				$datosCliente = array(
					"estadoCliente" => "Borrado"
				);

				$condicion = array("md5(idCliente)" => $idCliente);
				IniciarTransaccion();
				$eliminar = EditarDatos($this->tabla,$datosCliente,$condicion);
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

	function ClienteCambiarEstado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			//No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"]=403;
		} else {
			if($this->input->method(TRUE) == "POST"){
				$idCliente = $this->input->post("idCliente");
				$condicionDatos = array(
					'md5(idCliente)' => $idCliente,
					'estadoCliente' => 'Activo',
					'idSucursalCliente' => $this->session->idSucursal
				);
				$activoCliente = ExistenDatos($this->tabla,$condicionDatos);
				if($activoCliente==0){
					$nuevoEstado = 'Activo';
				} else{
					$nuevoEstado = 'Inactivo';
				}
				$datosCliente = array(
					"estadoCliente" => $nuevoEstado
				);

				$condicion = array("md5(idCliente)" => $idCliente);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosCliente,$condicion);
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

	function ClienteMunicipios(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			//No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"]=403;
		} else {
			if($this->input->method(TRUE) == "POST"){
				$idDepartamento = $this->input->post("idDepartamento");
				$datosMunicipios = TraerDatos('FE_CAT_013_Municipio',array('departamento' => $idDepartamento));
				$municipiosOption = "<option value='' >Seleccione un municipio</option>";
				foreach ($datosMunicipios as $municipios):
					$municipiosOption .= "<option value='".$municipios->codigo."' >".$municipios->valores."</option>";
				endforeach;
				$datosRespuesta["municipios"] = $municipiosOption;
				echo json_encode($datosRespuesta);
			}
		}
	}

}
/* End of file Usuarios.php */
