<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductosCategoria extends CI_Controller {

	private $tabla = "productoCategoria";
	private $tablaProducto = "producto";
	private $controlador = "ProductosCategoria";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

    function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Categorias de Productos";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-cart-plus",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'ProductosCategoriaAgregar',
						'txt' => 'Agregar Categoria',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id'=>'',
					),
				),
				"encabezados" => array(
					"ID" => 1,
					"Nombre" => 5,
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
					"scripts/productosCategoria.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}
	function ProductosCategoriaMostrar(){
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
			0 => 'idProductoCategoria',
			1 => 'nombreProductoCategoria',
			2 => 'EstadoProductoCategoria'
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])){
			$ordenCampos = null;
		} else{
			$ordenCampos = $columnasValidas[$col];
		}
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$condicion = array('idSucursalProductoCategoria' => $sucursal ,'estadoProductoCategoria !='=>"Borrado");
		$tipo = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
		// print_r($usuarios);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($tipo != 0){
			$datosMostrar = array();
			foreach ($tipo as $tipo){
				$estadoProductoCategoria = $tipo->estadoProductoCategoria;
				if($estadoProductoCategoria="Activo"){
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

				$funcion ="ProductosCategoriaEditar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($tipo->idProductoCategoria)."'><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "ProductosCategoriaCambiarEstado";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' data-accion='$estadoTxt' idProductoCategoria=".md5($tipo->idProductoCategoria)."><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ProductosCategoriaEliminar";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' idProductoCategoria=".md5($tipo->idProductoCategoria)."><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";

				$datosMostrar[] = array(
					$tipo->idProductoCategoria,
					$tipo->nombreProductoCategoria,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalProductoCategoria = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalProductoCategoria,
				"recordsFiltered" => $totalProductoCategoria,
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
	function ProductosCategoriaAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				//$roles = TraerDatos('usuarioRoles');
				$titulo = "Agregar Categoria de Producto";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-cart-plus",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar"
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/productosCategoria.js"
					),
				);
				GblPlantilla("productosCategoria/ProductosCategoriaAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
				$nombreProductoCategoria = $this->input->post("nombreCategoria");
				$condicionExiste = array('nombreProductoCategoria' => $nombreProductoCategoria,"estadoProductoCategoria !="=>"Borrado");
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				$sucursal  = $this->session->idSucursal;
				if($existe == 0){
					$datosProductoCategoria = array(
						"idSucursalProductoCategoria"=>$this->session->idSucursal,
						"nombreProductoCategoria" => $nombreProductoCategoria,
						"estadoProductoCategoria" => "Activo",
						"idSucursalProductoCategoria" =>$sucursal,
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosProductoCategoria);

					($guardar == false) ? $error = true : $error = false; 

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
	function ProductosCategoriaEditar($idProductoCategoria=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				// $idProductoCategoria = $this->uri->segment(3);
				$condicionDatos = array('md5(idProductoCategoria)' => $idProductoCategoria);
				$datosProductoCategoria = TraerUnDato($this->tabla, $condicionDatos);
				$datosRol = TraerDatos("usuarioRoles");
				if($datosProductoCategoria !== false && $idProductoCategoria!=""){
					$titulo = "Editar Categoria de Productos";
					$datosVista = array(
						"datosProductoCategoria"=> $datosProductoCategoria,
						"roles"=> $datosRol,
						"controlador" => $this->controlador,
						"idProductoCategoria" => $idProductoCategoria,
						"titulo" => $titulo,
						"proceso" => "Editar",
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/productosCategoria.js"
						),
					);
					GblPlantilla("productosCategoria/ProductosCategoriaEditar",$datosVista,$extras,$titulo);
				} else{
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idProductoCategoria = $this->input->post("idProductoCategoria");
				$nombreProductoCategoria = $this->input->post("nombreCategoria");
				
				$condicionExiste = array(
					'nombreProductoCategoria' => $nombreProductoCategoria,
					'md5(idProductoCategoria) !=' => $idProductoCategoria,
					"estadoProductoCategoria !="=>"Borrado"
				);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				//print_r($existe);
				if($existe == 0){  
					$datosProductoCategoria = array(
						"nombreProductoCategoria" => $nombreProductoCategoria,
						'aleatorioProductoCategoria' => uniqid(),
					);
					IniciarTransaccion();
					$condicion = array("md5(idProductoCategoria)" => $idProductoCategoria);
					$editar = EditarDatos($this->tabla,$datosProductoCategoria,$condicion);
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
	function ProductosCategoriaEliminar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"] = 403;
		} else{
			if($this->input->method(TRUE) == "POST"){	
				$idProductoCategoria = $this->input->post("idCategoria");
				$condicion = array("md5(idProductoCategoria)" => $idProductoCategoria);
				
				$datosProductoCategoria = array(
					"estadoProductoCategoria" => "Borrado"
				);
				$condicion = array("md5(idProductoCategoria)" => $idProductoCategoria);
				IniciarTransaccion();
				//se buscan registros en otras tablas que dependan del registro a eliminar
				$dependencias = ExistenDatos($this->tablaProducto,$condicion);
				
				if($dependencias == 0){
					$borrar = EditarDatos($this->tabla,$datosProductoCategoria,$condicion);
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
	function ProductosCategoriaCambiarEstado(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			$datosRespuesta["codigo"] = 403;
		} else{
			if($this->input->method(TRUE) == "POST"){
				$idProductoCategoria = $this->input->post("idCategoria");
				//var_dump($idProductoCategoria);
				$condicionDatos = array(
					'md5(idProductoCategoria)' => $idProductoCategoria,
					'estadoProductoCategoria' => "Activo",
				);
				$activoProductoCategoria = ExistenDatos($this->tabla,$condicionDatos);
				
				( $activoProductoCategoria == 0 ) ? $nuevoEstado = "Activo" : $nuevoEstado = "Inactivo";

				$datosProductoCategoria = array(
					"estadoProductoCategoria" => $nuevoEstado
				);
				$condicion = array("md5(idProductoCategoria)" => $idProductoCategoria);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla,$datosProductoCategoria,$condicion);
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
