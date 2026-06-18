<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ServicioCategoria extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "servicioCategoria";
	private $controlador = "ServicioCategoria";
	function __construct() 	{
		parent::__construct();
		$this->load->Model('CoreModel', "core");
	}

	public function index()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			$titulo = "Servicio Categoria";
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fas fa-hand-holding-usd",
				"botones" => array(
					array(
						"icono" => "fas fa-hand-holding-usd",
						'controlador' => $this->controlador,
						'url' => 'ServicioCategoriaAgregar',
						'txt' => 'Agregar Categoria Servicio ',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
						'modal' => true,
						'id' => 'ServicioCategoriaAgregar'
					),
				),
				"encabezados" => array(
					"ID" => 1,
					"Nombre" => 3,
					"Descripcion" => 6,
					"Estado" => 1,
					"Acciones" => 1,
				),
				"admin" => $this->session->admin,
				"idSucursal" => $this->session->idSucursal,
				"sucursales" => TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/servicioCategoria.js"
				),
			);
			GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
		}
	}
	function ServicioCategoriaMostrar() {
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));

		$order = $this->input->post("order");
		$busquedaAreglo = $this->input->post("search");
		$busquedaParametro = $busquedaAreglo['value'];
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
			0 => 'idServicioCategoria',
			1 => 'nombreServicioCategoria',
			2 => 'descripcionServicioCategoria',
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
		$condicion = array('estadoServicioCategoria !=' => "Borrado", 'idSucursalServicioCategoria' => $sucursal);
		// $joins = array(array('tabla' => 'cliente', 'condicion' => 'cliente.idCliente = membrecia.idClienteMembrecia'));
		$ServicioCategorias = TraerDatosTabla($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($Membrecias);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($ServicioCategorias != 0) {
			$datosMostrar = array();
			foreach ($ServicioCategorias as $ServicioCategoria) {
				$estadoServicioCategoria = $ServicioCategoria->estadoServicioCategoria;
				if ($estadoServicioCategoria == "Activo") {
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else if ($estadoServicioCategoria == "Inactivo") {
					$estadoTxt = "Activar";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$estadoIcon = "fa fa-toggle-off";
				} 

				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion = "ServicioCategoriaEditar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "'  data-accion='Editar' idCategoria=" . md5($ServicioCategoria->idServicioCategoria) . "><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "ServicioCategoriaCambiarEstado";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idCategoria=" . md5($ServicioCategoria->idServicioCategoria) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ServicioCategoriaEliminar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' idCategoria=" . md5($ServicioCategoria->idServicioCategoria) . "><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";

				$datosMostrar[] = array(
					$ServicioCategoria->idServicioCategoria,
					$ServicioCategoria->nombreServicioCategoria,
					$ServicioCategoria->descripcionServicioCategoria,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalServicioCategoria = TraerTotalDatos($this->tabla, $condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalServicioCategoria,
				"recordsFiltered" => $totalServicioCategoria,
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
	function ServicioCategoriaAgregar() {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$titulo = "Agregar Servicio Categoria";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fas fa-hand-holding-usd",
					"controlador" => "ServicioCategoria",
					"proceso" => "Agregar",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/servicioCategoria.js"
					),
				);
				$this->load->view("servicioCategoria/servicioCategoriaAgregar", $datosVista);
			} else if ($this->input->method(TRUE) == "POST") {
				$nombreServicioCategoria = $this->input->post("nombreServicioCategoria");
				$descripcionServicioCategoria = $this->input->post("descripcionServicioCategoria");
				$sucursalServicioCategoria  = $this->session->idSucursal;

				$condicionExiste = array('nombreServicioCategoria' => $nombreServicioCategoria, 'estadoServicioCategoria !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosServicioCategoria = array(
						"idSucursalServicioCategoria" => $sucursalServicioCategoria,
						"nombreServicioCategoria" => $nombreServicioCategoria,
						"descripcionServicioCategoria" => $descripcionServicioCategoria,
						"estadoServicioCategoria" => "Activo",
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla, $datosServicioCategoria);
					if ($guardar) {
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
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
	function ServicioCategoriaEditar($idServicioCategoria = '') {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$ServicioCategoria = TraerUnDato($this->tabla,array("md5(idServicioCategoria)" => $idServicioCategoria));

				$titulo = "Editar Servicio Categoria";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fas fa-hand-holding-usd",
					"controlador" => "ServicioCategoria",
					"proceso" => "Editar",
					"servicioCategoria" => $ServicioCategoria
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/servicioCategoria.js"
					),
				);
				$this->load->view("servicioCategoria/servicioCategoriaEditar", $datosVista);
			} else if ($this->input->method(TRUE) == "POST") {
				$idServicioCategoria = $this->input->post("idServicioCategoria");
				$nombreServicioCategoria = $this->input->post("nombreServicioCategoria");
				$descripcionServicioCategoria = $this->input->post("descripcionServicioCategoria");

				$condicionExiste = array(
					'idServicioCategoria !=' => $idServicioCategoria,
					'nombreServicioCategoria' => $nombreServicioCategoria, 
					'estadoServicioCategoria !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosServicioCategoria = array(
						"nombreServicioCategoria" => $nombreServicioCategoria,
						"descripcionServicioCategoria" => $descripcionServicioCategoria,
						"aleatorioServicioCategoria" => uniqid(),
					);
					$condicion = array(
						'idServicioCategoria' => $idServicioCategoria
					);
					IniciarTransaccion();
					$guardar = EditarDatos($this->tabla, $datosServicioCategoria,$condicion);
					if ($guardar) {
						EjecutarTransaccion();
						$datosRespuesta["codigo"] = 200;
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
	function ServicioCategoriaCambiarEstado() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idServicioCategoria = $this->input->post("idCategoria");
				$condicionDatos = array(
					'md5(idServicioCategoria)' => $idServicioCategoria,
					'estadoServicioCategoria' => 'Activo',
				);
				$activoServicioCategoria = ExistenDatos($this->tabla, $condicionDatos);

				($activoServicioCategoria == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

				$datosServicioCategorias = array(
					"estadoServicioCategoria" => $nuevoEstado
				);
				$condicion = array("md5(idServicioCategoria)" => $idServicioCategoria);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosServicioCategorias, $condicion);
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
	function ServicioCategoriaEliminar() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idServicioCategoria = $this->input->post("idCategoria");

				$nuevoEstado = 'Borrado';

				$datosServicioCategorias = array(
					"estadoServicioCategoria" => $nuevoEstado
				);
				$condicion = array("md5(idServicioCategoria)" => $idServicioCategoria);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosServicioCategorias, $condicion);
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
/* End of file Categorias.php */
