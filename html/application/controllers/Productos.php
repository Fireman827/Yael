<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Productos extends CI_Controller
{

	private $tabla = "producto";
	private $tablaProductoCategoria = "productoCategoria";
	private $tablaProductoCategoriaEspecifica = "productoCategoriaEspecifica";
	private $tablaProductoModificador = "productoModificador";
	private $tablaProductoInsumo = "productoInsumo";
	private $tablaInsumoPresentacion = "insumoPresentacion";
	private $tablaProductoModificadorDetalle = "productoModificadorDetalle";
	private $tablaModificador = "modificador";
	private $tablaModificadorTipo = "modificadorTipo";
	private $controlador = "Productos";
	private $imagen;
	function __construct()
	{
		parent::__construct();
		$this->load->Model('CoreModel', "core");
		$this->load->add_package_path(APPPATH . 'third_party/upload_file');
		$this->load->library('uploadFile');
	}

	public function index()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			$campos = array(
				"idProductoCategoria" => "idCategoria",
				"nombreProductoCategoria" => "nombreCategoria"
			);
			$categoria = TraerDatosRenombrados('productoCategoria',$campos ,array("estadoProductoCategoria" => 'Activo'));
			$titulo = "Productos";
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fa fa-shopping-cart",
				"botones" => array(
					array(
						"icono" => "fa fa-cart-plus",
						'controlador' => $this->controlador,
						'url' => 'ProductoAgregar',
						'txt' => 'Agregar Producto',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
						'modal' => false,
						'id'=>''
					),
				),
				"buscador" => true,
				"categorias" => $categoria,
				"encabezados" => array(
					"ID" => 1,
					"Producto" => 2,
					"Descripcion" => 2,
					"Precio" => 2,
					"Estado" => 1,
					"Acciones" => 1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(
					''
				),
				'js' => array(
					"scripts/productos.js"
				),
			);
			GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
		}
	}
	function ProductoMostrar()	{
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));

		$buscador = $this->input->post("buscador");
		$buscadorTexto = $this->input->post("busqueda");

		$order = $this->input->post("order");
		$busquedaAreglo = $this->input->post("search");
		$busquedaParametro = ($buscador == "1") ? $buscadorTexto :$busquedaAreglo['value'];
		$categoria = $this->input->post("categoria");

		$col = 0;
		$ordenDireccion = "";
		if (!empty($order)) {
			foreach ($order as $o) {
				$col = $o['column'];
				$ordenDireccion = $o['dir'];
			}
		}
		if ($ordenDireccion != "asc" && $ordenDireccion != "desc") {
			$ordenDireccion = "desc";
		}
		//Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		$columnasValidas = array(
			0 => 'producto.idProducto',
			1 => 'producto.nombreProducto',
			2 => 'producto.descripcionProducto',
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])) {
			$ordenCampos = null;
		} else {
			$ordenCampos = $columnasValidas[$col];
		}
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$this->session->idSucursal = $sucursal;
		$condicion = array('idSucursalProducto' => $sucursal,"estadoProducto !="=>"Borrado");
		if($categoria != "All"){
			$condicion = array('idSucursalProducto' => $sucursal,"estadoProducto !="=>"Borrado","estadoProductoCategoriaEspecifica !="=>"Borrado","productoCategoriaEspecifica.idProductoCategoria"=>$categoria);
			$join = array(
				array(
					"tabla" => "productoCategoriaEspecifica",
					"condicion" => "productoCategoriaEspecifica.idProducto = producto.idProducto",
				),
			);
			$campos = "productoCategoriaEspecifica.idProductoCategoria,producto.idProducto, producto.nombreProducto, producto.descripcionProducto,producto.estadoProducto,producto.precioVentaProducto";
			$productos = TraerDatosTablaJoinGroup($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$join,$campos,"producto.idProducto");
		}
		else{
			$productos = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
		}
		// print_r($productos);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($productos != 0) {
			$datosMostrar = array();
			foreach ($productos as $producto) {
				$estadoProducto = $producto->estadoProducto;
				if ($estadoProducto == "Activo") {
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

				$funcion = "ProductoEditar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item' href='" . base_url() . $funcion . "/" . md5($producto->idProducto) . "'><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "ProductoCambiarEstado";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idProducto=" . md5($producto->idProducto) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ProductoEliminar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' idProducto=" . md5($producto->idProducto) . "><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";
				$categoriaProducto = "";
				// $condicionCategoria = array("idProductoCategoria" => $producto->idProductoCategoria);
				// $datosCategoria = TraerUnDato("productoCategoria", $condicionCategoria);
				// if ($datosCategoria !== false) {
				// 	$categoriaProducto = $datosCategoria->nombreProductoCategoria;
				// }
				$datosMostrar[] = array(
					$producto->idProducto,
					$producto->nombreProducto,
					$producto->descripcionProducto,
					$producto->precioVentaProducto,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalProductos = TraerTotalDatos($this->tabla);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalProductos,
				"recordsFiltered" => $totalProductos,
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
	function ProductoAgregar()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {

				$categoria = TraerDatos('productoCategoria', array("estadoProductoCategoria" => 'Activo'));
				$modificadores = TraerUnDatoIndividual($this->tablaModificadorTipo,"idModificadorTipo,nombreModificadorTipo" ,array("estadoModificadorTipo" => 'Activo'));
				$impresoras = TraerDatos('impresora',array('cocinaImpresora'=>'1','idSucursalImpresora' =>$this->session->idSucursal,'estadoImpresora' =>'Activo'));
				$titulo = "Agregar Producto";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-users",
					"controlador" => "Productos",
					"proceso" => "Agregar",
					"categorias" => $categoria,
					"modificadores" => $modificadores,
					"impresoras" => $impresoras,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/productos.js"
					),
				);
				GblPlantilla("productos/ProductoAgregar", $datosVista, $extras, $titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$barcodeProducto = $this->input->post("barcodeProducto");
				$nombreProducto = $this->input->post("nombreProducto");
				$descripcionProducto = $this->input->post("descripcionProducto");
				$precioProducto = $this->input->post("precioVentaProducto");
				$precioEspecial = $this->input->post("precioEspecialProducto");
				$precioEmpleado = $this->input->post("precioEmpleadoProducto");
				$impresora = $this->input->post("impresoraProducto");
				$tablaProductoCategoria = $this->input->post("categoriasProducto");
				$tablaComoModificadores = $this->input->post("comoModificadoresProducto");
				//print_r($precioProducto);
				$condicionExiste = array('nombreProducto' => $nombreProducto);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosProducto = array(
						"idSucursalProducto"=>$this->session->idSucursal,
						"barcodeProducto" => $barcodeProducto,
						"nombreProducto" => $nombreProducto,
						"descripcionProducto" => $descripcionProducto,
						"precioVentaProducto" => $precioProducto,
						"precioEspecialProducto" => (null !== ($precioEspecial)) ? $precioEspecial : 0,
						"precioEmpleadoProducto" => (null !== ($precioEmpleado)) ? $precioEmpleado : 0,
						"impresoraProducto" => (null !== ($impresora)) ? $impresora : 0,
						"insumoProducto" => 0,
						"estadoProducto" => "Activo",
					);
					if($_FILES['imagenProducto']['name'] != ""){
						$nombreImagen = "_".uniqid();
						$this->imagen = new UploadFile();
						$subirImagen = $this->imagen->subirArchivo('imagenProducto',$nombreImagen);

						if($subirImagen['response']){
							$datosProducto['imagenProducto'] = 'vendors/core/img/productos/'.$subirImagen['info']['file_name'];
						}
					}

					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla, $datosProducto);
					if ($guardar) {
						$idProducto = $guardar;

						$categorias = json_decode($tablaProductoCategoria);
						$comoModificadores = json_decode($tablaComoModificadores);
						$nArrays = 0; //cantidad de arrays mandados
						$nExitos = 0; //cantidad de existos en inserciones de Arrays
						if (!empty($categorias)) {
							$nArrays ++;
							$n = count($categorias);
							for ($i = 0; $i < $n; $i++) {
								$datosCategoria = array(
									"idProducto" => $idProducto,
									"idProductoCategoria" => $categorias[$i][0],
								);
								$guardarCategoria = GuardarDatos($this->tablaProductoCategoriaEspecifica, $datosCategoria);
							}
							if($guardarCategoria){
								$nExitos++;
							}
							else{
								DeshacerTransaccion();
								$datosRespuesta["codigo"] = 501;
							}
						}
						if (!empty($comoModificadores)) {
							$nArrays++;
							$m = count($comoModificadores);
							for ($j = 0; $j < $m; $j++) {

								$datosComoModificador = array(
									"idModificadorTipo" => $comoModificadores[$j][0],
									"idSucursalModificador"=>$this->session->idSucursal,
									"idProducto" => $idProducto,
									"nombreModificador" => $nombreProducto,
									"precioModificador" => $comoModificadores[$j][2],
									"estadoModificador" => "Activo"
								);
								//var_dump($datosComoModificador);
								$guardarComoModificador = GuardarDatos($this->tablaModificador, $datosComoModificador);
							}
							if($guardarComoModificador){
								$nExitos++;
							}
							else{
								DeshacerTransaccion();
								$datosRespuesta["codigo"] = 502;
							}
						}
						if($nArrays == $nExitos){
							EjecutarTransaccion();
							$datosRespuesta["codigo"] = 200;
							$datosRespuesta["idProducto"] = $idProducto;
						}
					} else {
						DeshacerTransaccion();
						$datosRespuesta["codigo"] = 500;
					}
				} else {
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ProductoAgregarModificador()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idProducto = $this->input->post("idProducto");
				$tablaProductoModificador = json_decode($this->input->post("tablaModificador"));
				//var_dump($tablaProductoModificador);

				//$tablaModificadoresDetalle = json_decode($this->input->post("tablaModificadorDetalle"));
				$nArrays = 0; //cantidad de arrays mandados
				$nExitos = 0; //cantidad de existos en inserciones de Arrays
				IniciarTransaccion();
				if(!empty($tablaProductoModificador)) {
					//$nArrays ++;
					//$n = count($tablaProductoModificador);
					foreach ($tablaProductoModificador as $tpm) {
						$datosCategoria = array(
							"idProducto" => $idProducto,
							"idModificadorTipo" => $tpm->idModTipo,
							"cantidadProductoModificador" =>  $tpm->cantidad,
							"idUnicoProductoModificador" =>  $tpm->idUnico,
							"idUnicoSelectProductoModificador" =>  $tpm->idUnicoSelect,
							"variosProductoModificador" =>  $tpm->varios,
							"multiSeleccionProductoModificador" =>  (isset($tpm->multiSeleccion)) ? $tpm->multiSeleccion : 0,
							"nombreProductoModificador" =>  $tpm->nombre,
						);
						$guardarCategoria = GuardarDatos($this->tablaProductoModificador, $datosCategoria);
						if($guardarCategoria){
							$idProdMod = $guardarCategoria;
							$detalle = $tpm->detalle;
							if(!empty($detalle)){
								foreach($detalle as $dt){
									$datosCatDet = array(
										"idProductoModificador" => $idProdMod,
										"idModificador" => $dt->id,
										"idProducto" => $idProducto,
										"aumentoProductoModificadorDetalle" => $dt->aumento,
										"idUnicoProductoModificadorDetalle" => $dt->idUnico,
										"idUnicoPadreProductoModificadorDetalle" => $dt->idUnicoPadre,
										"idUnicoAbueloProductoModificadorDetalle" => $dt->idUnicoAbuelo,
										"idModificadorTipoProductoModificadorDetalle" => $dt->tipoModificador,
										"variosProductoModificadorDetalle" => $dt->varios,
										"nombreProductoModificadorDetalle" => $dt->nombre,
										"nombrePadreProductoModificadorDetalle" => $dt->nombrePadre,
										"estadoProductoModificadorDetalle" => "Activo"
									);
									$guardarCategoriaDet = GuardarDatos($this->tablaProductoModificadorDetalle, $datosCatDet);
									if($guardarCategoriaDet){
										$error = false;
										$datosRespuesta["codigo"] = 200;
										$datosRespuesta["idProducto"] = $idProducto;
									}
									else{
										$error =  true;
										$datosRespuesta["codigo"] = 501;
										break 2;
									}

								}
							}
						}
						else{
							$error = true;
							$datosRespuesta["codigo"] = 500;
							break;
						}

					}
					($error == false) ? EjecutarTransaccion() : DeshacerTransaccion();

				}
				// if (!empty($tablaModificadoresDetalle)) {
				// 	$nArrays++;
				// 	//var_dump($tablaModificadoresDetalle);
				// 	$m = count($tablaModificadoresDetalle);
				// 	for($i = 0 ; $i < $m ; $i++){

				// 		$ProductoModificador = TraerUnDato($this->tablaProductoModificador,array('idUnicoProductoModificador' => $tablaModificadoresDetalle[$i]->idUnicoPadre));
				// 		//var_dump($ProductoModificador);
				// 		$datosPresentaciones = array(
				// 			"idProducto" => $idProducto,
				// 			"idProductoModificador" => $ProductoModificador->idProductoModificador,
				// 			"idModificador" => $tablaModificadoresDetalle[$i]->id,
				// 			"aumentoProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->aumento,
				// 			"idUnicoProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->idUnico,
				// 			"idUnicoPadreProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->idUnicoPadre,
				// 			"idUnicoAbueloProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->idUnicoAbuelo,
				// 			"idModificadorTipoProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->tipoModificador,
				// 			"variosProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->varios,
				// 			"nombreProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->nombre,
				// 			"nombrePadreProductoModificadorDetalle" => $tablaModificadoresDetalle[$i]->nombrePadre,
				// 			"estadoProductoModificadorDetalle" => "Activo",
				// 		);
				// 		$guardar = GuardarDatos($this->tablaProductoModificadorDetalle, $datosPresentaciones);
				// 		($guardar == false) ? $error = true : $error = false;
				// 		if($guardar){
				// 			$nExitos++;
				// 			EjecutarTransaccion();
				// 			$datosRespuesta["codigo"] = 200;
				// 			$datosRespuesta["idProducto"] = $idProducto;
				// 		}
				// 		else{
				// 			DeshacerTransaccion();
				// 			$datosRespuesta["codigo"] = 501;
				// 		}
				// 	}
				// }
				// if($nArrays == $nExitos){
				// 	EjecutarTransaccion();
				// 	$datosRespuesta["codigo"] = 200;
				// 	$datosRespuesta["idProducto"] = $idProducto;
				// }
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ProductoAgregarInsumoGeneral()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idProducto = $this->input->post("idProducto");
				$tablaProductoInsumo = json_decode($this->input->post("tablaInsumoGeneral"));

				// var_dump($tablaModificadoresDetalle);
				$nArrays = 0; //cantidad de arrays mandados
				$nExitos = 0; //cantidad de existos en inserciones de Arrays
				$error =  false;
				if (!empty($tablaProductoInsumo)) {
					IniciarTransaccion();
					//$n = count($tablaProductoModificador);
					foreach ($tablaProductoInsumo as $ti) {
						$datosCategoria = array(
							"idSucursalProductoInsumo" => $this->session->idSucursal,
							"idInsumo" => $ti->idInsumo,
							"idProducto" => $idProducto,
							"idPresentacionProductoInsumo" => $ti->idPresentacion,
							"cantidadProductoInsumo" => $ti->cantidad,
							"estadoProductoInsumo" => "Activo",
						);
						$guardarCategoria = GuardarDatos($this->tablaProductoInsumo, $datosCategoria);
						if($guardarCategoria){
							$error =  false;
							$datosRespuesta["codigo"] = 200;
							$datosRespuesta["idProducto"] = $idProducto;
						}
						else{
							$error =  true;
							$datosRespuesta["codigo"] = 500;
							break;
						}
					}
					if($error){
						DeshacerTransaccion();
					}
					else{
						EjecutarTransaccion();
					}
				}
				else{
					$datosRespuesta["codigo"] = 600;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ProductoEditar($idProducto){
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {

				/** Editar (General) **/

				$producto = TraerUnDato("producto",array("md5(idProducto)"=>$idProducto));
				$categoriaEspecifica = TraerDatos('productoCategoriaEspecifica', array("md5(idProducto)"=>$idProducto,"estadoProductoCategoriaEspecifica" => 'Activo'));
				$categoria = TraerDatos('productoCategoria', array("estadoProductoCategoria" => 'Activo'));
				$modificadoresTipo = TraerDatos($this->tablaModificadorTipo ,array("estadoModificadorTipo" => 'Activo'));
				$modificadores = TraerDatos($this->tablaModificador ,array("estadoModificador" => 'Activo',"md5(idProducto)"=>$idProducto));
				$impresoras = TraerDatos('impresora',array('cocinaImpresora'=>'1','idSucursalImpresora' =>$this->session->idSucursal,'estadoImpresora' =>'Activo'));


				/** Editar (Modificadores) **/
				$modificadoresProd = TraerDatos($this->tablaProductoModificador,array("estadoProductoModificador" => 'Activo',"md5(idProducto)"=>$idProducto));
				$modificadoresProdDet = TraerDatos($this->tablaProductoModificadorDetalle,array("estadoProductoModificadorDetalle" => 'Activo',"md5(idProducto)"=>$idProducto));
				$in = $jn = 0; //
				$arrayMod = array();
				if($modificadoresProd){
					foreach($modificadoresProd as $mp){
						$nModProd = $mp;
						$idModificadorTipo = $mp->idModificadorTipo;
						$mod = TraerDatos($this->tablaModificador,array("idModificadorTipo"=>$idModificadorTipo,"estadoModificador"=>"Activo"));
						if($mod){
							$nModProd->modificadores = $mod;
						}
						array_push($arrayMod,$nModProd);
					}
				}
				$arrayModDetalle = array();
				if($modificadoresProdDet){
					foreach($modificadoresProdDet as $mp){
						$nModProd = $mp;
						$joinInn = array(
							array(
								"tabla" => "insumo",
								"condicion" => "insumo.idInsumo = productoInsumo.idInsumo",
								"tipo" => "left",
								"campos" => "nombreInsumo"
							),
						);
						$condicion11 = array("idModificador" => $mp->idModificador);
						$idProductoInsumoRes = TraerUnDatoIndividual($this->tablaModificador,"idProducto",$condicion11);
						$idProductoInsumo = ($idProductoInsumoRes) ? $idProductoInsumoRes[0]["idProducto"] : '';
						$condicion111 = array("md5(idProducto)" => $idProducto);
						$idProductoInsumo2 = TraerUnDatoIndividual($this->tablaProductoInsumo,"idProducto",$condicion111);
						$idProductoInsumo2 = ($idProductoInsumo2) ? TraerUnDatoIndividual($this->tablaProductoInsumo,"idProducto",$condicion111)[0]["idProducto"] : '';
						/**insumos Actuales del producto por modificador visualizado */
						$mod = TraerDatosJoin($this->tablaProductoInsumo,array("idProducto"=>$idProductoInsumo2,"productoInsumo.idModificador"=> $mp->idModificador,"estadoProductoInsumo"=>"Activo"),"",$joinInn);
						/**insumos reales de los modificadores */
						$mod1 = TraerDatosJoin($this->tablaProductoInsumo,array("idProducto"=>$idProductoInsumo,"estadoProductoInsumo"=>"Activo"),"",$joinInn);
						$nuevo = 0;
						$viejo = 0;
						$modificados = 0;
						//var_dump($mod);

						if($mod && $mod1) {
							$arInsumos = array();
							foreach($mod1 as $prim){
								$tipo = "nuevo";
								$joinIn = array(
									array(
										"tabla" => "presentacion",
										"condicion" => "insumoPresentacion.idPresentacion = presentacion.idPresentacion",
										"tipo" => "inner",
										"campos" => "nombrePresentacion, unidadPresentacion"
									),
								);
								$idPrimario = $prim->idInsumo;
								$condicion1 = array("idInsumo" => $prim->idInsumo,"estadoInsumoPresentacion"=>"Activo");
								$presentacionesInsumo = TraerDatosJoin($this->tablaInsumoPresentacion,$condicion1,"",$joinIn);

								$arInsumos[$idPrimario] = array(
									"idProductoInsumo" => $prim->idProductoInsumo,
									"idInsumo" => $prim->idInsumo,
									"idModificador" => $prim->idModificador,
									"nombreInsumo" => $prim->nombreInsumo,
									"idPresentacionProductoInsumo" => $prim->idPresentacionProductoInsumo,
									"cantidadProductoInsumo" => $prim->cantidadProductoInsumo,
									"idUnicoProductoInsumo" => $prim->idUnicoProductoInsumo,
									"tipo" => $tipo,
									"presentaciones" =>  ($presentacionesInsumo) ? $presentacionesInsumo : '',
								);
								$nuevo ++;
							}
							foreach($mod as $sec){
								$idSec = $sec->idInsumo;
								$tipo = (array_key_exists($idSec,$arInsumos)) ? "mutuo" : "viejo";
								if($tipo == "viejo"){
									$condicion1 = array("idInsumo" => $sec->idInsumo,"estadoInsumoPresentacion"=>"Activo");
									$presentacionesInsumo = TraerDatosJoin($this->tablaInsumoPresentacion,$condicion1,"",$joinIn);

									$arInsumos[$idSec] = array(
										"idProductoInsumo" => $sec->idProductoInsumo,
										"idInsumo" => $sec->idInsumo,
										"idModificador" => $sec->idModificador,
										"nombreInsumo" => $sec->nombreInsumo,
										"idPresentacionProductoInsumo" => $sec->idPresentacionProductoInsumo,
										"cantidadProductoInsumo" => $sec->cantidadProductoInsumo,
										"idUnicoProductoInsumo" => $sec->idUnicoProductoInsumo,
										"tipo" => $tipo,
										"presentaciones" =>  ($presentacionesInsumo) ? $presentacionesInsumo : '',
									);
									$viejo ++;
								}
								else if($tipo == "mutuo"){
									$nuevo --;
									$idAnterior = $arInsumos[$idSec]["idProductoInsumo"];
									$canAnterior = $arInsumos[$idSec]["cantidadProductoInsumo"];
									$preAnterior = $arInsumos[$idSec]["idPresentacionProductoInsumo"];
									if($preAnterior != $sec->idPresentacionProductoInsumo || $canAnterior != $sec->cantidadProductoInsumo){
										$arInsumos[$idSec]["tipo"] = "modificado";
										$joinInDe = array(
											array(
												"tabla" => "presentacion",
												"condicion" => "productoInsumo.idPresentacionProductoInsumo = presentacion.idPresentacion",
												"tipo" => "inner",
												"campos" => "presentacion.idPresentacion,nombrePresentacion, unidadPresentacion"
											),
										);
										$pre = TraerUnDatoJoin($this->tablaProductoInsumo,array("productoInsumo.idPresentacionProductoInsumo"=>$preAnterior,"estadoPresentacion"),$joinInDe);
										$nombrePre = TraerUnDatoIndividual("presentacion","nombrePresentacion",array("idPresentacion"=>$preAnterior))[0]["nombrePresentacion"];
										$arInsumos[$idSec]["canAnterior"] = $canAnterior;
										$arInsumos[$idSec]["preAnterior"] = $nombrePre;
										// $arInsumos[$idSec]["preAnterior"] = $preAnterior." ".$sec->idPresentacionProductoInsumo." ".$sec->idProductoInsumo." ".$idAnterior;
										//array_push($arInsumos[$idSec]["presentaciones"],$pre);
										$modificados ++;
									}
									else{
										$arInsumos[$idSec]["tipo"] = $tipo;
									}
									$arInsumos[$idSec]["idPresentacionProductoInsumo"] = $sec->idPresentacionProductoInsumo;
									$arInsumos[$idSec]["cantidadProductoInsumo"] = $sec->cantidadProductoInsumo;
								}
							}
						 	$nModProd->modificadoresInsumo = $arInsumos;
							 $nModProd->etiquetaNuevo = ($nuevo > 0 ) ? '<span class="badge badge-warning">Nuevo</span>' : '';
							 $nModProd->etiquetaViejo = ($viejo > 0 ) ? '<span class="badge badge-danger">Viejo</span>' : '';
							 $nModProd->etiquetaModificado = ($modificados > 0 ) ? '<span class="badge badge-info">Modificado</span>' : '';
						}else{
							$nModProd->modificadoresInsumo = "";
						}

						array_push($arrayModDetalle,$nModProd);
					}
				}
				//diferencia entre los insumos por modificador reales y los guardados segun el producto
				$diferencia = $in - $jn;

				/** Editar (Insumos Generales) **/
				$joinIn = array(
					array(
						"tabla" => "insumo",
						"condicion" => "insumo.idInsumo = productoInsumo.idInsumo",
						"tipo" => "inner",
						"campos" => "nombreInsumo"
					),
				);
				$insumos = TraerDatosJoin($this->tablaProductoInsumo,array("md5(idProducto)"=>$idProducto,"productoInsumo.idModificador"=> "0","estadoProductoInsumo"=>"Activo"),"",$joinIn);
				if($insumos){
					foreach($insumos as $index => $m){
						$joinIn = array(
							array(
								"tabla" => "presentacion",
								"condicion" => "insumoPresentacion.idPresentacion = presentacion.idPresentacion",
								"tipo" => "inner",
								"campos" => "nombrePresentacion, unidadPresentacion"
							),
						);
						$condicion1 = array("idInsumo" => $m->idInsumo,"estadoInsumoPresentacion"=>"Activo"	);
						$presentacionesInsumo = TraerDatosJoin($this->tablaInsumoPresentacion,$condicion1,"",$joinIn);
						$insumos[$index]->presentaciones  = ($presentacionesInsumo) ? $presentacionesInsumo : "";
					}
				}
				// var_dump($arrayModDetalle);
				$presentaciones = TraerDatos("presentacion",array("estadoPresentacion"=>"Activo"));

				$titulo = "Editar Producto";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-users",
					"controlador" => "Productos",
					"proceso" => "Editar",
					"idProducto" => $idProducto,
					"producto" => $producto,
					"impresoras" => $impresoras,
					"categorias" => $categoria,
					"categoriasEspecifica" => $categoriaEspecifica,
					"modificadores" => $modificadores,
					"modificadoresTipo" => $modificadoresTipo,
					"modificadoresProd" => $arrayMod,
					"modificadoresProdDet" => $arrayModDetalle,
					"insumos" => $insumos,
					"diferencia" => $diferencia,
					"presentaciones" => $presentaciones,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/productos.js"
					),
				);
				GblPlantilla("productos/ProductoEditar", $datosVista, $extras, $titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$idProducto = $this->input->post("idProducto");
				$barcodeProducto = $this->input->post("barcodeProducto");
				$nombreProducto = $this->input->post("nombreProducto");
				$descripcionProducto = $this->input->post("descripcionProducto");
				$precioProducto = $this->input->post("precioVentaProducto");
				$precioEspecial = $this->input->post("precioEspecialProducto");
				$precioEmpleado = $this->input->post("precioEmpleadoProducto");
				$impresora = $this->input->post("impresoraProducto");
				$tablaProductoCategoria = $this->input->post("categoriasProducto");
				$tablaComoModificadores = $this->input->post("comoModificadoresProducto");
				$error = false;
				$condicionExiste = array('nombreProducto' => $nombreProducto,'md5(idProducto) !='=>$idProducto);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {


					$datosProducto = array(
						"idSucursalProducto"=>$this->session->idSucursal,
						"barcodeProducto" => $barcodeProducto,
						"nombreProducto" => $nombreProducto,
						"descripcionProducto" => $descripcionProducto,
						"precioVentaProducto" => $precioProducto,
						"precioEspecialProducto" => (null !== ($precioEspecial)) ? $precioEspecial : 0,
						"precioEmpleadoProducto" => (null !== ($precioEmpleado)) ? $precioEmpleado : 0,
						"impresoraProducto" => (null !== ($impresora)) ? $impresora : 0,
						"aleatorioProducto" => uniqid(),
					);
					if($_FILES['imagenProducto']['name'] != ""){
						$rutaImagen = TraerUnDato("producto",array("md5(idProducto)"=> $idProducto))->imagenProducto;
						$nombreImagen = $idProducto."_".uniqid();
						$this->imagen = new UploadFile();
						$subirImagen = $this->imagen->subirArchivo('imagenProducto',$nombreImagen);

						if($subirImagen['response']){
							//print_r($subirImagen['info']);
							$datosProducto['imagenProducto'] = 'vendors/core/img/productos/'.$subirImagen['info']['file_name'];
							//unlink($rutaImagen);
						}
					}
					IniciarTransaccion();
					$guardar = EditarDatos($this->tabla, $datosProducto,array("md5(idProducto)"=>$idProducto));
					if ($guardar) {
						$condicionExiste = array('md5(idProducto)'=>$idProducto,'estadoProductoCategoriaEspecifica'=>'Activo');
						$existe = ExistenDatos($this->tablaProductoCategoriaEspecifica, $condicionExiste);
						if($existe){
							$datosCatDet=array(
								"estadoProductoCategoriaEspecifica" =>"Borrado"
							);
							$borrar = EditarDatos($this->tablaProductoCategoriaEspecifica, $datosCatDet,array("md5(idProducto)"=>$idProducto));
							if($borrar){
								$error= false;
							}
							else{
								$error =  true;
							}
						}

						if(!$error){
							$prod = TraerUnDatoIndividual("producto","idProducto",array("md5(idProducto)" => $idProducto));
							$idProd = $prod[0]["idProducto"];
							$categorias = json_decode($tablaProductoCategoria);
							$comoModificadores = json_decode($tablaComoModificadores);
							$nArrays = 0; //cantidad de arrays mandados
							$nExitos = 0; //cantidad de existos en inserciones de Arrays
							if (!empty($categorias)) {
								$nArrays ++;
								$n = count($categorias);
								for ($i = 0; $i < $n; $i++) {
									$datosCategoria = array(
										"idProducto" => $idProd,
										"idProductoCategoria" => $categorias[$i][0],
									);
									$guardarCategoria = GuardarDatos($this->tablaProductoCategoriaEspecifica, $datosCategoria);
									if($guardarCategoria){
										$error = false;
									}
									else{
										$error = true;
										$datosRespuesta["codigo"] = 502;
										break;
									}
								}
								if($error == false){
									if(!$error){
										if (!empty($comoModificadores)) {
											$condicionExiste = array('md5(idProducto)'=>$idProducto,'estadoProductoModificador'=>'Activo');
											$existe = ExistenDatos($this->tablaProductoModificador, $condicionExiste);
											if($existe){
												$datosMod=array(
													"estadoModificador" =>"Borrado"
												);
												$borrar = EditarDatos("modificador", $datosMod,array("md5(idProducto)"=>$idProducto));
												if($borrar){
													$error= false;
												}
												else{
													$error =  true;
												}
											}
											$nArrays++;
											$m = count($comoModificadores);
											for ($j = 0; $j < $m; $j++) {

												$datosComoModificador = array(
													"idModificadorTipo" => $comoModificadores[$j][0],
													"idSucursalModificador"=>$this->session->idSucursal,
													"idProducto" => $idProd,
													"nombreModificador" => $nombreProducto,
													"precioModificador" => $comoModificadores[$j][2],
													"estadoModificador" => "Activo"
												);
												//var_dump($datosComoModificador);
												$guardarComoModificador = GuardarDatos($this->tablaModificador, $datosComoModificador);
												if($guardarComoModificador){
													$error = false;
												}
												else{
													$error = true;
													$datosRespuesta["codigo"] = 504;
													break;
												}
											}
										}
									}
									else{
										$error =  true;
										$datosRespuesta["codigo"] = 503;
									}
								}
							}
						}
						else{
							$error =  true;
							$datosRespuesta["codigo"] = 501;
						}
					} else {
						$error = true;
						$datosRespuesta["codigo"] = 500;
					}
					if($error == true){
						DeshacerTransaccion();
					}
					else{
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
						$datosRespuesta["idProducto"] = $idProducto;
					}
				} else {
					$datosRespuesta["codigo"] = 400;
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ProductoEditarModificador($idProducto)	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idProducto = $this->input->post("idProducto");
				$tablaProductoModificadorr = json_decode($this->input->post("tablaModificador"));
				IniciarTransaccion();

				$editar = true;
				$error = false;
				$condicionExisteMod = array(
					"md5(idProducto) "=>$idProducto,
					"estadoProductoModificador !="=>"Borrado"
				);
				$existeMod = ExistenDatos("productoModificador", $condicionExisteMod);
				if($existeMod){
					$editar = EditarDatos($this->tablaProductoModificador,array("estadoProductoModificador"=>"Borrado"),array("md5(idProducto)"=>$idProducto));
				}
				if($editar){
					$editarDet = true;
					$condicionExisteModDet = array(
						"md5(idProducto) "=>$idProducto,
						"estadoProductoModificadorDetalle !="=>"Borrado"
					);
					$existeModDet = ExistenDatos("productoModificadorDetalle", $condicionExisteModDet);
					if($existeModDet){
						$editarDet = EditarDatos($this->tablaProductoModificadorDetalle,array("estadoProductoModificadorDetalle"=>"Borrado"),array("md5(idProducto)"=>$idProducto));
					}
					if($editarDet){
						if(!empty($tablaProductoModificadorr)) {
							$prod = TraerUnDatoIndividual("producto","idProducto",array("md5(idProducto)" => $idProducto));
							$idProd = $prod[0]["idProducto"];
							foreach ($tablaProductoModificadorr as $tpm) {
								$datosCategoria = array(
									"idProducto" => $idProd,
									"idModificadorTipo" => $tpm->idModTipo,
									"cantidadProductoModificador" =>  $tpm->cantidad,
									"idUnicoProductoModificador" =>  $tpm->idUnico,
									"idUnicoSelectProductoModificador" =>  $tpm->idUnicoSelect,
									"variosProductoModificador" =>  $tpm->varios,
									"multiSeleccionProductoModificador" =>  (isset($tpm->multiSeleccion)) ? $tpm->multiSeleccion : 0,
									"nombreProductoModificador" =>  $tpm->nombre,
								);
								$guardarCategoria = GuardarDatos($this->tablaProductoModificador, $datosCategoria);
								if($guardarCategoria){
									$idProdMod = $guardarCategoria;
									$detalle = $tpm->detalle;
									if(!empty($detalle)){
										foreach($detalle as $dt){
											$datosCatDet = array(
												"idProductoModificador" => $idProdMod,
												"idModificador" => $dt->id,
												"idProducto" => $idProd,
												"aumentoProductoModificadorDetalle" => $dt->aumento,
												"idUnicoProductoModificadorDetalle" => $dt->idUnico,
												"idUnicoPadreProductoModificadorDetalle" => $dt->idUnicoPadre,
												"idUnicoAbueloProductoModificadorDetalle" => $dt->idUnicoAbuelo,
												"idModificadorTipoProductoModificadorDetalle" => $dt->tipoModificador,
												"variosProductoModificadorDetalle" => $dt->varios,
												"nombreProductoModificadorDetalle" => $dt->nombre,
												"nombrePadreProductoModificadorDetalle" => $dt->nombrePadre,
												"estadoProductoModificadorDetalle" => "Activo"
											);
											$guardarCategoriaDet = GuardarDatos($this->tablaProductoModificadorDetalle, $datosCatDet);
											if($guardarCategoriaDet){
												$error = false;
											}
											else{
												$error =  true;
												$datosRespuesta["codigo"] = 503;
												break 2;
											}

										}
									}
								}
								else{
									$error = true;
									$datosRespuesta["codigo"] = 502;
									break;
								}

							}
						}
					}
					else{
						$error = true;
						$datosRespuesta["codigo"] = 501;
					}
				}
				else{
					$error = true;
					$datosRespuesta["codigo"] = 500;
				}
				if($error == false) {
					$datosRespuesta["codigo"] = 200;
					$datosRespuesta["idProducto"] = $idProducto;
					EjecutarTransaccion();
				} else {
					DeshacerTransaccion();
				}
				echo json_encode($datosRespuesta);
			}
		}
	}
	function ProductoEditarInsumoGeneral()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idProducto = $this->input->post("idProducto");
				$tablaProductoInsumo = json_decode($this->input->post("tablaInsumoGeneral"));

				// var_dump($tablaModificadoresDetalle);
				IniciarTransaccion();
				$nArrays = 0; //cantidad de arrays mandados
				$nExitos = 0; //cantidad de existos en inserciones de Arrays
				$error =  false;

				$condicionExiste = array('md5(idProducto)'=>$idProducto,'estadoProductoInsumo'=>'Activo');
				$existe = ExistenDatos($this->tablaProductoInsumo, $condicionExiste);
				if($existe){
					$borrar = EditarDatos($this->tablaProductoInsumo,array("estadoProductoInsumo"=>"Borrado"),array("md5(idProducto)"=>$idProducto));
					if($borrar){
						$error= false;
					}
					else{
						$error =  true;
						$datosRespuesta["codigo"] = 500;
					}
				}
				if($error == false){
					$prod = TraerUnDatoIndividual("producto","idProducto",array("md5(idProducto)" => $idProducto));
					$idProd = $prod[0]["idProducto"];
					if (!empty($tablaProductoInsumo)) {
						//$n = count($tablaProductoModificador);
						foreach ($tablaProductoInsumo as $ti) {
							$datosCategoria = array(
								"idSucursalProductoInsumo" => $this->session->idSucursal,
								"idModificador" => $ti->idMod,
								"idInsumo" => $ti->idInsumo,
								"idProducto" => $idProd,
								"idPresentacionProductoInsumo" => $ti->idPresentacion,
								"cantidadProductoInsumo" => $ti->cantidad,
								"estadoProductoInsumo" => "Activo",
							);
							$guardarCategoria = GuardarDatos($this->tablaProductoInsumo, $datosCategoria);
							if($guardarCategoria){
								$error =  false;
								$datosRespuesta["codigo"] = 200;
								$datosRespuesta["idProducto"] = $idProducto;
							}
							else{
								$error =  true;
								$datosRespuesta["codigo"] = 502;
								break;
							}
						}
					}
					else{
						$error =  true;
						$datosRespuesta["codigo"] = 501;
					}
				}

				if($error){
					DeshacerTransaccion();
				}
				else{
					EjecutarTransaccion();
				}

				echo json_encode($datosRespuesta);
			}
		}
	}
	function ProductoCambiarEstado() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idProducto = $this->input->post("idProducto");
				$condicionDatos = array(
					'md5(idProducto)' => $idProducto,
					'estadoProducto' => 'Activo',
				);
				$activoProducto = ExistenDatos($this->tabla, $condicionDatos);

				($activoProducto == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

				$datosProducto = array(
					"estadoProducto" => $nuevoEstado
				);
				$condicion = array("md5(idProducto)" => $idProducto);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosProducto, $condicion);
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
	function ProductoEliminar() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idProducto = $this->input->post("idProducto");
				$nuevoEstado = 'Borrado';

				$datosProductos = array(
					"estadoProducto" => $nuevoEstado
				);
				$condicion = array("md5(idProducto)" => $idProducto);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosProductos, $condicion);
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
	function ProductoListarModificador()	{

			if ($this->input->method(TRUE) == "POST") {
				$idUnicoSelect = uniqid();
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				$modificadorTipo = TraerDatos('modificadorTipo', array("idSucursalModificadorTipo" => $this->session->idSucursal,"estadoModificadorTipo" => "Activo"));
				/**Si tiene registros  entonces los recorro */
				if ($modificadorTipo !== false) {
					//creo una cariable String con el texto del Select para los Tipos de Modificadores
					$modificadorTipoSelectTd = '<td colspan="3">';
					/**creo una variable String para los option del select con los tipos de modificadores */
					$modificadorTipoSelectTd .= '	<div class="row mt-2">';
					$modificadorTipoSelectTd .= '		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">';
					//Agrego al Select los Option
					$modificadorTipoSelectTd .= "			<select class='select2 form-control productoModificador'>";
					$modificadorTipoOption = '					<option></option>';
					foreach ($modificadorTipo as $tipo) :
						$idUnico = uniqid();
						$modificadorTipoOption .= "				<option idUnico='".$idUnico."' idUnicoSelect='".$idUnicoSelect."' varios='" . $tipo->variosModificadorTipo . "' nombre='" . $tipo->nombreModificadorTipo . "' value='" . $tipo->idModificadorTipo . "'>" . $tipo->nombreModificadorTipo . "</option>";
					endforeach;
					$modificadorTipoSelectTd .= $modificadorTipoOption;
					$modificadorTipoSelectTd .= "			</select>";
					$modificadorTipoSelectTd .= "		</div>";
					$modificadorTipoSelectTd .= '		<div class="col-xl-2 col-lg-2 col-md-2 col-sm-10 col-xs-10">';
					$modificadorTipoSelectTd .= "			<input type='text' class='form-control numeric cantidadModificador' placeholder='0000' title='Cantidad de selecciones / maximo si es seleccion multiple' readonly>";
					$modificadorTipoSelectTd .= "		</div>";
					$modificadorTipoSelectTd .= '		<div class="col-xl-3 col-lg-3 col-md-3 col-sm-10 col-xs-10">';
					$modificadorTipoSelectTd .= '			<div class="icheck-primary d-inline">';
					$modificadorTipoSelectTd .= "				<input type='checkbox' class='multiSeleccionModificador' id='multiSeleccion".$idUnicoSelect."'>";
					$modificadorTipoSelectTd .= "				<label for='multiSeleccion".$idUnicoSelect."'>Seleccion multiple</label>";
					$modificadorTipoSelectTd .= '			</div>';
					$modificadorTipoSelectTd .= "		</div>";
					$modificadorTipoSelectTd .= '		<div class="col-1">';
					$modificadorTipoSelectTd .= '			<a class="btn btn-block btn-danger borrarTr btnBorrarModificador" idUnicoSelect="'.$idUnicoSelect.'" role="button"><i class="fa fa-trash"></i></a>';
					$modificadorTipoSelectTd .= "		</div>";
					$modificadorTipoSelectTd .= "	</div>";
					$modificadorTipoSelectTd .= '	<div class="row">';
					$modificadorTipoSelectTd .= '		<div class="col-12 detalleModificadoresTipo pre-scrollable" style="max-height:500px;">';
					$modificadorTipoSelectTd .= "		</div>";
					$modificadorTipoSelectTd .= "	</div>";
					$modificadorTipoSelectTd .= "</td>";
					//creo un input para la cantidad
					// $modificadorTipoInputTd = '<td class="tdCantidad">';
					// $modificadorTipoInputTd .= "<input type='text' class='form-control numeric cantidadModificador' placeholder='0000' readonly>";
					// $modificadorTipoInputTd .= '</td>';
					// //creo un boton para eliminar esa fila
					// $modificadorTipoBtnTd = '<td>';
					// $modificadorTipoBtnTd .= '<a class="btn btn-block btn-danger borrarTr" role="button"><i class="fa fa-trash"></i></a>';
					// $modificadorTipoBtnTd .= '</td>';
					//Creo una variable para el Tr que contendrá el modificador y le agrego el select, el input y el botón
					$modificadorTipoTr = '<tr>';
					// $modificadorTipoTr .= $modificadorTipoSelectTd . $modificadorTipoInputTd . $modificadorTipoBtnTd;
					$modificadorTipoTr .= $modificadorTipoSelectTd;
					$modificadorTipoTr .= '</tr>';
					//creo un array con el estatus de la accion y con el tr creado
					$datosRespuesta['codigo'] = 200;
					$datosRespuesta['tr'] = $modificadorTipoTr;
				} else {
					$datosRespuesta['codigo'] = 500;
				}
				echo json_encode($datosRespuesta);
			}
	}
	function ProductoListarModificadorDetalle()	{
			if ($this->input->method(TRUE) == "POST") {
				$idModificadorTipo = $this->input->post("idModificadorTipo");
				$idUnicoPadre = $this->input->post("idUnico");
				$nombrePadre = $this->input->post("nombre");
				$varios = $this->input->post("varios");
				$idUnicoSelect = $this->input->post("idUnicoSelect");
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				$modificadores = TraerDatos($this->tablaModificador, array("idModificadorTipo" => $idModificadorTipo,"estadoModificador" => "Activo"));
				/**Si tiene registros  entonces los recorro */
				if ($modificadores !== false) {
					$n = 0;
					//creo una variable String con el texto del Select para los Tipos de Modificadores
					$tablaDetalle = '';

					//$tablaDetalle .= "<table class='table table-sm table-condensed '>";
					$tablaDetalle .= "<div class='row listaDetalleModificadores' >";
					$tablaDetalle .= '	<div class="col-12 mt-2">';
					$tablaDetalle .= '		<div class="row ">';
					foreach($modificadores as $mod):
						$idUnico =  uniqid();
						$tablaDetalle .= '		<div class="col-xl-4 col-lg-4 col-sm-4 col-xs-12  py-1 ">';
						$tablaDetalle .= '			<div class="row grupoDetalleModificador">';
						$tablaDetalle .= '				<div class="col-6">';
						$tablaDetalle .= '					<div class="icheck-'.GblTraerConfiguracion('colorComponentes').' d-inline">';
						$tablaDetalle .= '						<input type="checkbox" idUnicoAbuelo="'.$idUnicoSelect.'" idUnicoPadre="'.$idUnicoPadre.'" idUnico="'.$idUnico.'" varios="'.$varios.'" tipo="'.$idModificadorTipo.'" nombre="'.$mod->nombreModificador.'" nombrePadre="'.$nombrePadre.'" class="modificadorDetalle" id="'.$mod->idModificador.'" >';
						$tablaDetalle .= '						<label for="'.$mod->idModificador.'">'.$mod->nombreModificador.'</label>';
						$tablaDetalle .= '					</div>';
						$tablaDetalle .= '				</div>';
						$tablaDetalle .= '				<div class="col-6">';
						$tablaDetalle .= '					<input type="text" class="form-control decimal aumentoModificadorDetalle" placeholder="Aumento" value="0.00">';
						$tablaDetalle .= '				</div>';
						$tablaDetalle .= '			</div>';
						$tablaDetalle .= '		</div>';
						$n ++;
						if($n == 3){
							$tablaDetalle .= '</div>';
							$tablaDetalle .= '<div class="row">';
							$n = 0;
						}
					endforeach;
					$tablaDetalle .= '		</div>';
					$tablaDetalle .= '	</div>';

					$tablaDetalle .= "</div>";
					//$tablaDetalle .= "</table>";
					//creo un array con el estatus de la accion y con el tr creado
					$datosRespuesta['codigo'] = 200;
					$datosRespuesta['tabla'] = $tablaDetalle;
				} else {
					$datosRespuesta['codigo'] = 500;

				}
				echo json_encode($datosRespuesta);
			}
	}
	function ProductoListarModificadorTipo(){

			if ($this->input->method(TRUE) == "POST") {
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				$condicion1 = array("variosModificadorTipo" => 1,"estadoModificadorTipo" => 'Activo',"idSucursalModificadorTipo"=>$this->session->idSucursal);
				$categorias = TraerUnDatoIndividual($this->tablaModificadorTipo,"idModificadorTipo, nombreModificadorTipo" ,$condicion1);
				/**Si tiene registros  entonces los recorro */

				if ($categorias !== false) {
					$categoriaSelectTd="<td>";
					$categoriaSelectTd.="<select class='modificadorTipo select2 form-control'>";
					foreach ($categorias  as $categoria) :
						$categoriaSelectTd .= "<option value='".$categoria['idModificadorTipo']."'>".$categoria['nombreModificadorTipo']."</option>";
					endforeach;
					$categoriaSelectTd .="</select>";
					$categoriaSelectTd .="</td>";

					$categoriaBtnTd = '<td><a class="btn btn-block btn-danger borrarTr" role="button"><i class="fa fa-trash"></i></a></td>';

					$categoriaTr = '<tr>';
					$categoriaTr .= $categoriaSelectTd . $categoriaBtnTd;
					$categoriaTr .= '</tr>';
					//creo un array con el estatus de la accion y con el tr creado
					$datosRespuesta['codigo'] = 200;
					$datosRespuesta['tr'] = $categoriaTr;
				} else {
					$datosRespuesta['codigo'] = 500;
				}
				echo json_encode($datosRespuesta);
			}
	}
	function ProductoListarInsumoGeneral(){

			if ($this->input->method(TRUE) == "POST") {
				$idUnico = uniqid();
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				$insumos = TraerUnDatoIndividual('insumo', "idInsumo, nombreInsumo",array("estadoInsumo" => "Activo", "idSucursalInsumo" => $this->session->idSucursal));
				/**Si tiene registros  entonces los recorro */
				if ($insumos !== false) {
					//creo una cariable String con el texto del Select para los Tipos de Modificadores
					$insumoSelectTd = '<td>';
					/**creo una variable String para los option del select con los tipos de modificadores */
					$insumoOption = '<option>Seleccione</option>';
					foreach ($insumos as $insumo) :
						$insumoOption .= "<option value='" . $insumo['idInsumo'] . "' idUnico='" . $idUnico . "' nombre='" . $insumo['nombreInsumo'] . "'>" . $insumo['nombreInsumo'] . "</option>";
					endforeach;
					//Agrego al Select los Option
					$insumoSelectTd .= "<select class='select2 form-control productoInsumoGeneral'>";
					$insumoSelectTd .= $insumoOption;
					$insumoSelectTd .= "</select>";
					$insumoSelectTd .= "</td>";
					// //creo un input para la cantidad
					// $insumoInputTd = '<td class="tdCantidad">';
					// $insumoInputTd .= "<input type='text' class='form-control numeric cantidadInsumoGeneral' placeholder='0000'>";
					// $insumoInputTd .= '</td>';
					//creo un boton para eliminar esa fila
					$insumoBtnTd = '<td>';
					$insumoBtnTd .= '<a class="btn btn-block btn-danger borrarTr btnBorrarInsumo"  idUnico="' . $idUnico . '" role="button"><i class="fa fa-trash"></i></a>';
					$insumoBtnTd .= '</td>';
					//Creo una variable para el Tr que contendrá el modificador y le agrego el select, el input y el botón
					$insumoTr = '<tr>';
					$insumoTr .= $insumoSelectTd . $insumoBtnTd;
					$insumoTr .= '</tr>';
					//creo un array con el estatus de la accion y con el tr creado
					$datosRespuesta['codigo'] = 200;
					$datosRespuesta['tr'] = $insumoTr;
				} else {
					$datosRespuesta['codigo'] = 500;
				}
				echo json_encode($datosRespuesta);
			}
	}
	function ProductoListarCategoria()	{

			if ($this->input->method(TRUE) == "POST") {
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				$condicion1 = array("estadoProductoCategoria" => 'Activo');
				$categorias = TraerUnDatoIndividual($this->tablaProductoCategoria,"idProductoCategoria, nombreProductoCategoria" ,$condicion1);
				/**Si tiene registros  entonces los recorro */

				if ($categorias !== false) {
					$categoriaSelectTd="<td>";
					$categoriaSelectTd.="<select class='categoriaProducto select2 form-control'>";
					foreach ($categorias  as $categoria) :
						$categoriaSelectTd .= "<option value='".$categoria['idProductoCategoria']."'>".$categoria['nombreProductoCategoria']."</option>";
					endforeach;
					$categoriaSelectTd .="</select>";
					$categoriaSelectTd .="</td>";

					$categoriaBtnTd = '<td><a class="btn btn-block btn-danger borrarTr" role="button"><i class="fa fa-trash"></i></a></td>';

					$categoriaTr = '<tr>';
					$categoriaTr .= $categoriaSelectTd . $categoriaBtnTd;
					$categoriaTr .= '</tr>';
					//creo un array con el estatus de la accion y con el tr creado
					$datosRespuesta['codigo'] = 200;
					$datosRespuesta['tr'] = $categoriaTr;
				} else {
					$datosRespuesta['codigo'] = 500;
				}
				echo json_encode($datosRespuesta);
			}
	}
	function ProductoListarInsumosPresentacion()	{

			if ($this->input->method(TRUE) == "POST") {
				$idInsumo = $this->input->post("idInsumo");
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				$condicion1 = array("idInsumo" => $idInsumo,"estadoInsumoPresentacion" => 'Activo');
				$presentaciones = TraerDatos('insumoPresentacion',$condicion1);
				/**Si tiene registros  entonces los recorro */

				if ($presentaciones !== false) {
					$presentacionSelect="<select class='select2 form-control col-12'>";
					foreach ($presentaciones  as $categoria) :
						$condicion2 = array("idPresentacion" => $categoria->idPresentacion);
						$detallePresentacion = TraerDatos("presentacion",$condicion2);
						foreach ($detallePresentacion as $dtPre):
							$presentacionSelect .= "<option value='".$dtPre->idPresentacion."'>".$dtPre->nombrePresentacion." (".$dtPre->unidadPresentacion.")</option>";
						endforeach;
					endforeach;
					$presentacionSelect .="</select>";
					//creo un array con el estatus de la accion y con el tr creado
					$datosRespuesta['codigo'] = 200;
					$datosRespuesta['select'] = $presentacionSelect;
				} else {
					$datosRespuesta['codigo'] = 500;
				}
				echo json_encode($datosRespuesta);
			}
	}
	function ProductoAutocompletarInsumo()	{

			if ($this->input->method(TRUE) == "POST") {
				$nombreTipo = $this->input->post("query");
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				//$modificadorTipo = TraerDatos('modificadorTipo',array("nombreModificadorTipo LIKE" => "%".$nombreTipo."%","activoModificadorTipo" => 1));
				$condicion1 = array("estadoInsumo" => "Activo","idSucursalInsumo"=>$this->session->idSucursal);
				$condicion2 = array("nombreInsumo" => $nombreTipo);
				$insumo = TraerDatosComo('insumo', $condicion1, $condicion2);
				//$modificadorTipo = TraerDatos('modificadorTipo',"nombreModificadorTipo LIKE '%".$nombreTipo."%' AND activoModificadorTipo = 1");
				/**Si tiene registros  entonces los recorro */
				$i = 0;
				if ($insumo !== false) {

					foreach ($insumo  as $in) :
						$idInsumo = $in->idInsumo;
						/** llamo a los registros de la tabla modificadorTipo que estén activos */
						$condicion1 = array("idInsumo" => $idInsumo,"estadoInsumoPresentacion" => 'Activo');
						$presentaciones = TraerDatos('insumoPresentacion',$condicion1);
						/**Si tiene registros  entonces los recorro */
						$presentacionSelect = '';
						if ($presentaciones !== false) {
							$presentacionSelect .="<select class='select2 form-control col-12 presentacion'>";
							foreach ($presentaciones  as $categoria) :
								$condicion2 = array("idPresentacion" => $categoria->idPresentacion);
								$detallePresentacion = TraerDatos("presentacion",$condicion2);
								foreach ($detallePresentacion as $dtPre):
									$presentacionSelect .= "<option value='".$dtPre->idPresentacion."'>".$dtPre->nombrePresentacion." (".$dtPre->unidadPresentacion.")</option>";
								endforeach;
							endforeach;
							$presentacionSelect .="</select>";
						}
						$datosRespuesta[$i] = array(
							'id' => $in->idInsumo,
							'nombre' => $in->nombreInsumo,
							'presentaciones' => $presentacionSelect
						);
						$i++;
					endforeach;
				} else {
					$datosRespuesta = [];
				}
				echo json_encode($datosRespuesta);
			}
	}
	function ProductoVerificarInsumosModificador(){

			if ($this->input->method(TRUE) == "POST") {
				$idModificador = $this->input->post("idModificador");
				$idUnico = $this->input->post("idUnico");
				/** llamo a los registros de la tabla modificadorTipo que estén activos */
				$condicion1 = array("idModificador" => $idModificador,"estadoModificador" => 'Activo', "idSucursalModificador" =>$this->session->idSucursal);
				$idProducto = TraerUnDatoIndividual($this->tablaModificador,"idProducto",$condicion1);

				if ($idProducto !== false) {
					$condicion2 = array("idProducto" => $idProducto[0]["idProducto"],"estadoProductoInsumo" => 'Activo', "idSucursalProductoInsumo" =>$this->session->idSucursal);
					$join = array(
						array(
							"tabla"=>"insumo",
							"condicion"=>"insumo.idInsumo = productoInsumo.idInsumo",
							"tipo"=>"inner",
							"campos"=>"insumo.nombreInsumo"
						),
					);
					$tieneInsumo = TraerDatosJoin($this->tablaProductoInsumo,$condicion2,"",$join);
					if($tieneInsumo){
						$insumos = '';
						foreach($tieneInsumo as $in){
							$joinPre = array(
								array(
									"tabla"=>"presentacion",
									"condicion"=>"insumoPresentacion.idPresentacion = presentacion.idPresentacion",
									"tipo" => "inner",
									"campos" => "presentacion.nombrePresentacion, presentacion.unidadPresentacion"
								),
							);
							$presentaciones = TraerDatosJoin("insumoPresentacion",array("idInsumo"=>$in->idInsumo,"estadoInsumoPresentacion"=>"Activo"),"",$joinPre);
							if($presentaciones){
								$insumos .= '<tr idUnico="'.$idUnico.'" idModificador="'.$idModificador.'">';
								$insumos .= '<td class="nombreTipo" idInsumo="'.$in->idInsumo.'">'.$in->nombreInsumo.'</td>';
								$insumos .= '<td><select class="presentacion select2 form-control col-12">';
								foreach($presentaciones as $pre){
									$selected = ($pre->idPresentacion == $in->idPresentacionProductoInsumo) ? 'selected' : '';
									$insumos.='<option value="'.$pre->idPresentacion.'" '.$selected.'>'.$pre->nombrePresentacion.'('.$pre->unidadPresentacion.')'.'</option>';
								}
								$insumos .= '</select></td>';
								$insumos .= '<td><input class="form-control cantidad decimal" value="'.$in->cantidadProductoInsumo.'"></td>';
								$insumos .= '<td><input type="hidden" class="incluirInsumo" value="1"></td>';
								$insumos .= '</tr>';

								$datosRespuesta['insumos'] = $insumos;
								$datosRespuesta['codigo'] = 200;
							}else{
								$datosRespuesta['codigo'] = 502;
								break;
							}
						}
					}
					else{
						$datosRespuesta['codigo'] = 501;
					}
				} else {
					$datosRespuesta['codigo'] = 500;
				}
				echo json_encode($datosRespuesta);
			}
	}
}
/* End of file Productos.php */
