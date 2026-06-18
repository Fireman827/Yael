<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Corte extends CI_Controller {

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

			$TraerAperturaActual = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("estadoCorte" => "Vigente"));
			$TraerAperturaActual1 = TraerUnDatoIndividual("corteCaja","montoApertura",array("estadoCorte" => "Vigente"));
			$aperturaActual = $TraerAperturaActual[0]["idCorteCaja"];
			$montoApertura = $TraerAperturaActual1[0]["montoApertura"];
			$regular = TraerUnDatoIndividual("pedidoDetalle","SUM(cantidadPedidoDetalle*precioPedidoDetalle) as dato",array("idCorte" => $aperturaActual, "tipoPedido" => "Producto"))[0]["dato"];
			$senorita = TraerUnDatoIndividual("pedidoDetalle","SUM(cantidadPedidoDetalle*precioPedidoDetalle) as dato",array("idCorte" => $aperturaActual, "tipoPedido" => "Producto Especial"))[0]["dato"];
			$empleados = TraerUnDatoIndividual("pedidoDetalle","SUM(cantidadPedidoDetalle*precioPedidoDetalle) as dato",array("idCorte" => $aperturaActual, "tipoPedido" => "Producto Empleado"))[0]["dato"];
			$servicios = TraerUnDatoIndividual("factura","SUM(totalFactura) as dato",array("idCorte" => $aperturaActual, "tipoFactura" => "Servicio"))[0]["dato"];
			$propina = TraerUnDatoIndividual("factura","SUM(propinaFactura) as dato",array("idCorte" => $aperturaActual))[0]["dato"];
			$subtotal = $regular + $propina;
			$totalventa = $subtotal + $senorita + $empleados;
			$total = $totalventa + $servicios + $montoApertura;
			if ($aperturaActual !=0) {
				$titulo = "Cortes de Caja";
			}else {
				$titulo = "Apertura de Caja";
			}
			$corte = array(
				'regular' => $regular,
				'senorita' => $senorita,
				'empleado' => $empleados,
				'subtotal' => $subtotal,
				'propina' => $propina,
				'montoApertura' => $montoApertura,
				'totalventa' => $totalventa,
				'servicios' => $servicios,
				'total' => $total,
				'idCorteCaja' => $aperturaActual,
			);
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-money",
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
				"corte"=>$corte,
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/cortes.js"
				),
			);
			GblPlantilla2("corte/Corte",$datosVista,$extras,$titulo);
		}
	}

	public function HistorialCorte(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Cortes de Caja";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-money",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'CorteAgregar',
						'txt' => 'Realizar Corte',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
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
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/cortes.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
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
				// $estadoUsuario = $usuario->activoUsuario;
				// if($estadoUsuario==1){
				// 	$estadoTxt = "Desactivar";
				// 	$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
				// 	$estadoIcon = "fa fa fa-toggle-on";
				// } else{
				// 	$estadoTxt = "Activar";
				// 	$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
				// 	$estadoIcon = "fa fa-toggle-off";
				// }
				// if($usuario->adminUsuario==1){
				// 	$tipoUsuario = "<span class='badge badge-warning font-weight-bold'>Administrador<span>";
				// } else{
				// 	$tipoUsuario = "<span class='badge badge-info font-weight-bold'>Normal<span>";
				// }
				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion ="CorteEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($corte->idCorteCaja)."'><i class='fas fa-edit' ></i> Editar (N)</a>";
				}

				$funcion = "CortePdf";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' idCorteCaja=".md5($corte->idCorteCaja)."><i class='fa fa-fil-pdf-o'></i> PDF</a>";
				}

				$funcion = "CorteImprimir";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' idCorteCaja=".md5($corte->idCorteCaja)."><i class='fa fa-print'></i> Imprimir</a>";
				}

				$menuOpciones .= "
				</div>
				</div>";
				$nombre = TraerUnDatoIndividual("usuario","nombreUsuario",array("idUsuario" => $corte->empleadoCorte))[0]["nombreUsuario"];

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

	function CorteAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){

				GblPlantilla("cortes/CorteAgregar",$datosVista,$extras,$titulo);
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
	function RealizarAperturar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){

				GblPlantilla("cortes/CorteMostrar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$monto = $this->input->post("monto");

					$datosCorte = array(
						"montoApertura"=>$monto,
						"idUsuarioCorte"=>$this->session->idUsuario,
						"fechaCorte"=>Date('Y-m-d'),
						"estadoCorte"=>'Vigente',

					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosCorte);
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

	function CorteEditar($idCorteCaja=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){

				$condicionDatos = array('md5(idCorteCaja)' => $idCorteCaja);
				$datosCorte = TraerUnDato($this->tabla, $condicionDatos);

				//TRAER CATEGORIAS DE CLIENTES
				$categoriasCorte = TraerDatos('corteCategoria',array('idSucursalCorteCategoria'=>$this->session->idSucursal));
				$categoriasCorteOption = "<option value='' >Seleccione una categoría de corte</option>";

				foreach ($categoriasCorte as $categoria) {
					if ($categoria->idCorteCajaCategoria==$datosCorte->idCategoriaCorte) {
						$categoriasCorteOption .= "<option value='".$categoria->idCorteCajaCategoria."' selected >".$categoria->nombreCorteCategoria."</option>";
					} else {
						$categoriasCorteOption .= "<option value='".$categoria->idCorteCajaCategoria."' >".$categoria->nombreCorteCategoria."</option>";
					}
				}

				if($datosCorte !== false && $idCorteCaja!=""){
					$titulo = "Editar Corte";
					$datosVista = array(
						"datosCorte"=> $datosCorte,
						"controlador" => $this->controlador,
						"idCorteCaja" => $idCorteCaja,
						"titulo" => $titulo,
						"proceso" => "Editar",
						"categoriasCorteOption" => $categoriasCorteOption
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/cortes.js"
						),
					);
					GblPlantilla("cortes/CorteEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idCorteCaja = $this->input->post("idCorteCaja");
				$nombreCorte = $this->input->post("nombreCorte");
				$telefonoCorte = $this->input->post("telefonoCorte");
				$direccionCorte = $this->input->post("direccionCorte");
				$referenciaCorte = $this->input->post("referenciaCorte");
				$idCategoriaCorte = $this->input->post("idCategoriaCorte");

				$condicionExiste = array(
					'nombreCorte' => $nombreCorte,
					'telefonoCorte' => $telefonoCorte,
					'md5(idCorteCaja)!=' => $idCorteCaja
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosCorte = array(
						"idSucursalCorte"=>$this->session->idSucursal,
						"nombreCorte"=>$nombreCorte,
						"telefonoCorte"=>$telefonoCorte,
						"direccionCorte"=>$direccionCorte,
						"referenciaCorte"=>$referenciaCorte,
						"idCategoriaCorte"=>$idCategoriaCorte,
						"aleatorioCorte"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("md5(idCorteCaja)" => $idCorteCaja);
					$editar = EditarDatos($this->tabla,$datosCorte,$condicion);
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

	function CorteAgregarAvanzado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//TRAER DEPARTAMENTOS
				$datosDepartamentos = TraerDatos('departamento');
				$departamentosOption = "<option value='' >Seleccione un departamento</option>";

				foreach ($datosDepartamentos as $departamentos):
					$departamentosOption .= "<option value='".$departamentos->idDepartamento."' >".$departamentos->nombreDepartamento."</option>";
				endforeach;

				//TRAER CATEGORIAS DE CLIENTES
				$categoriasCorte = TraerDatos('corteCategoria',array('idSucursalCorteCategoria'=>$this->session->idSucursal));
				$categoriasCorteOption = "<option value='' >Seleccione una categoría de corte</option>";

				foreach ($categoriasCorte as $categoria) {
					$categoriasCorteOption .= "<option value='".$categoria->idCorteCajaCategoria."'>".$categoria->nombreCorteCategoria."</option>";
				}

				$roles = TraerDatos('usuarioRoles');
				$titulo = "Agregar Corte";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-users",
					"controlador"=> "Cortes",
					"proceso"=> "Agregar",
					"departamentos"=> $departamentosOption,
					"categoriasCorteOption"=> $categoriasCorteOption
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/cortes.js"
					),
				);
				GblPlantilla("cortes/CorteAgregarAvanzado",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreCorte = $this->input->post("nombreCorte");
				$telefonoCorte = $this->input->post("telefonoCorte");
				$direccionCorte = $this->input->post("direccionCorte");
				$referenciaCorte = $this->input->post("referenciaCorte");
				$nitCorte = $this->input->post("nitCorte");
				$duiCorte = $this->input->post("duiCorte");
				$departamentoCorte = $this->input->post("departamentoCorte");
				$municipioCorte = $this->input->post("municipioCorte");
				$nrcCorte = $this->input->post("nrcCorte");
				$idCategoriaCorte = $this->input->post("idCategoriaCorte");

				$condicionExiste = array('nitCorte' => $nitCorte,'duiCorte' => $duiCorte);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosCorte = array(
						"idSucursalCorte"=>$this->session->idSucursal,
						"nombreCorte"=>$nombreCorte,
						"telefonoCorte"=>$telefonoCorte,
						"direccionCorte"=>$direccionCorte,
						"referenciaCorte"=>$referenciaCorte,
						"duiCorte"=>$duiCorte,
						"nitCorte"=>$nitCorte,
						"correoCorte"=>$correoCorte,
						"departamentoCorte"=>$departamentoCorte,
						"municipioCorte"=>$municipioCorte,
						"nrcCorte"=>$nrcCorte,
						"idCategoriaCorte"=>$idCategoriaCorte,
						"aleatorioCorte"=>uniqid()
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosCorte);
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

	function CorteEditarAvanzado($idCorteCaja=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//$idCorteCaja = $this->uri->segment(3);
				$condicionDatos = array('md5(idCorteCaja)' => $idCorteCaja);
				$datosCorte = TraerUnDato($this->tabla, $condicionDatos);
				if($datosCorte !== false && $idCorteCaja!=""){
					//CONSULTA PARA TRAER LOS DEPARTAMENTOS
					$datosDepartamentos = TraerDatos('departamento');
					$departamentosOption = "<option value='' >Seleccione un departamento</option>";

					if($datosCorte->departamentoCorte==0){
						foreach ($datosDepartamentos as $departamentos):
							if($departamentos->idDepartamento == 13) $departamentosOption .= "<option value='".$departamentos->idDepartamento."' selected >".$departamentos->nombreDepartamento."</option>";
							else $departamentosOption .= "<option value='".$departamentos->idDepartamento."' >".$departamentos->nombreDepartamento."</option>";
						endforeach;
					} else {
						foreach ($datosDepartamentos as $departamentos):
							if($departamentos->idDepartamento == $datosCorte->departamentoCorte) $departamentosOption .= "<option value='".$departamentos->idDepartamento."' selected >".$departamentos->nombreDepartamento."</option>";
							else $departamentosOption .= "<option value='".$departamentos->idDepartamento."' >".$departamentos->nombreDepartamento."</option>";
						endforeach;
					}

					//CONSULTA PARA TRAER LOS MUNICIPIOS
					$datosMunicipios = TraerDatos('municipio',array("idDepartamento"=> ($datosCorte->departamentoCorte != 0) ? $datosCorte->departamentoCorte : 13 ));
					$municipiosOption = "<option value='' >Seleccione un municipio</option>";

					foreach ($datosMunicipios as $municipios):
						if($datosCorte->municipioCorte==$municipios->idMunicipio) $municipiosOption .= "<option value='".$municipios->idMunicipio."' selected >".$municipios->nombreMunicipio."</option>";
						else $municipiosOption .= "<option value='".$municipios->idMunicipio."' >".$municipios->nombreMunicipio."</option>";
					endforeach;

					//TRAER CATEGORIAS DE CLIENTES
					$categoriasCorte = TraerDatos('corteCategoria',array('idSucursalCorteCategoria'=>$this->session->idSucursal));
					$categoriasCorteOption = "<option value='' >Seleccione una categoría de corte</option>";

					foreach ($categoriasCorte as $categoria) {
						if ($categoria->idCorteCajaCategoria==$datosCorte->idCategoriaCorte) {
							$categoriasCorteOption .= "<option value='".$categoria->idCorteCajaCategoria."' selected >".$categoria->nombreCorteCategoria."</option>";
						} else {
							$categoriasCorteOption .= "<option value='".$categoria->idCorteCajaCategoria."' >".$categoria->nombreCorteCategoria."</option>";
						}
					}

					$titulo = "Editar Corte Avanzado";
					$datosVista = array(
						"datosCorte"=> $datosCorte,
						"controlador" => $this->controlador,
						"idCorteCaja" => $idCorteCaja,
						"titulo" => $titulo,
						"proceso" => "Editar",
						"departamentos" => $departamentosOption,
						"municipios" => $municipiosOption,
						"categoriasCorteOption" => $categoriasCorteOption
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/cortes.js"
						),
					);
					GblPlantilla("cortes/CorteEditarAvanzado",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idCorteCaja = $this->input->post("idCorteCaja");
				$nombreCorte = $this->input->post("nombreCorte");
				$telefonoCorte = $this->input->post("telefonoCorte");
				$direccionCorte = $this->input->post("direccionCorte");
				$referenciaCorte = $this->input->post("referenciaCorte");
				$nitCorte = $this->input->post("nitCorte");
				$duiCorte = $this->input->post("duiCorte");
				$emailCorte = $this->input->post("emailCorte");
				$departamentoCorte = $this->input->post("departamentoCorte");
				$municipioCorte = $this->input->post("municipioCorte");
				$nrcCorte = $this->input->post("nrcCorte");
				$idCategoriaCorte = $this->input->post("idCategoriaCorte");

				$condicionExiste = array(
					'nrcCorte' => $nrcCorte,
					'nitCorte' => $nitCorte,
					'md5(idCorteCaja)!=' => $idCorteCaja
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosCorte = array(
						"idSucursalCorte"=>$this->session->idSucursal,
						"nombreCorte"=>$nombreCorte,
						"telefonoCorte"=>$telefonoCorte,
						"direccionCorte"=>$direccionCorte,
						"referenciaCorte"=>$referenciaCorte,
						"duiCorte"=>$duiCorte,
						"nitCorte"=>$nitCorte,
						"emailCorte"=>$emailCorte,
						"departamentoCorte"=>$departamentoCorte,
						"municipioCorte"=>$municipioCorte,
						"nrcCorte"=>$nrcCorte,
						"idCategoriaCorte"=>$idCategoriaCorte,
						"aleatorioCorte"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("md5(idCorteCaja)" => $idCorteCaja);
					$editar = EditarDatos($this->tabla,$datosCorte,$condicion);
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

	function CorteMunicipios(){
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
/* End of file Usuarios.php */
