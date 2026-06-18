<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SenoritaCategoria extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "senoritaCategoria";
	private $controlador = "SenoritaCategoria";
	function __construct() 	{
		parent::__construct();
		$this->load->Model('CoreModel', "core");
	}

	public function index()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			$titulo = "Señorita Categoria";
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fa fa-female",
				"botones" => array(
					array(
						"icono" => "fa fa-female",
						'controlador' => $this->controlador,
						'url' => 'SenoritaCategoriaAgregar',
						'txt' => 'Agregar SenoritaCategoria',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
						'modal' => true,
						'id' => 'senoritaCategoriaAgregar'
					),
				),
				"encabezados" => array(
					"ID" => 1,
					"Nombre" => 5,
					"Comision" => 1,
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
					"scripts/senoritaCategoria.js"
				),
			);
			GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
		}
	}
	function SenoritaCategoriaMostrar() {
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
			0 => 'idSenoritaCategoria',
			1 => 'nombreSenoritaCategoria',
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
		$condicion = array('estadoSenoritaCategoria !=' => "Borrado", 'idSucursalSenoritaCategoria' => $sucursal);
		// $joins = array(array('tabla' => 'cliente', 'condicion' => 'cliente.idCliente = membrecia.idClienteMembrecia'));
		$SenoritaCategorias = TraerDatosTabla($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($Membrecias);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($SenoritaCategorias != 0) {
			$datosMostrar = array();
			foreach ($SenoritaCategorias as $SenoritaCategoria) {
				$estadoSenoritaCategoria = $SenoritaCategoria->estadoSenoritaCategoria;
				if ($estadoSenoritaCategoria == "Activo") {
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else if ($estadoSenoritaCategoria == "Inactivo") {
					$estadoTxt = "Activar";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
					$estadoIcon = "fa fa-toggle-off";
				} else {
					$estadoTxt = "";
					$estadoSpan = "<span class='badge badge-danger font-bold'>Depreciado<span>";
					$estadoIcon = "fa fa-toggle";
				}

				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

				$funcion = "SenoritaCategoriaEditar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "'  data-accion='Editar' idCategoria=" . md5($SenoritaCategoria->idSenoritaCategoria) . "><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "SenoritaCategoriaCambiarEstado";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idCategoria=" . md5($SenoritaCategoria->idSenoritaCategoria) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "SenoritaCategoriaEliminar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' idCategoria=" . md5($SenoritaCategoria->idSenoritaCategoria) . "><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";

				$datosMostrar[] = array(
					$SenoritaCategoria->idSenoritaCategoria,
					$SenoritaCategoria->nombreSenoritaCategoria,
					$SenoritaCategoria->comisionSenoritaCategoria,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalSenoritaCategoria = TraerTotalDatos($this->tabla, $condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalSenoritaCategoria,
				"recordsFiltered" => $totalSenoritaCategoria,
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
	function SenoritaCategoriaAgregar() {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$titulo = "Agregar Senorita Categoria";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-female",
					"controlador" => "SenoritaCategoria",
					"proceso" => "Agregar",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/senoritaCategoria.js"
					),
				);
				$this->load->view("senoritaCategoria/senoritaCategoriaAgregar", $datosVista);
			} else if ($this->input->method(TRUE) == "POST") {
				$nombreSenoritaCategoria = $this->input->post("categoriaSenorita");
				$tipoComisionSenoritaCategoria = $this->input->post("tipoComision");
				$comisionSenoritaCategoria = $this->input->post("cantidadComision");
				$sucursalSenoritaCategoria  = $this->session->idSucursal;

				$condicionExiste = array('nombreSenoritaCategoria' => $nombreSenoritaCategoria, 'estadoSenoritaCategoria !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosSenoritaCategoria = array(
						"idSucursalSenoritaCategoria" => $sucursalSenoritaCategoria,
						"nombreSenoritaCategoria" => $nombreSenoritaCategoria,
						"tipoComisionSenoritaCategoria" => $tipoComisionSenoritaCategoria,
						"comisionSenoritaCategoria" => $comisionSenoritaCategoria,
						"estadoSenoritaCategoria" => "Activo",
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla, $datosSenoritaCategoria);
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
	function SenoritaCategoriaEditar($idSenoritaCategoria) {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$senoritaCategoria = TraerUnDato($this->tabla,array("md5(idSenoritaCategoria)" => $idSenoritaCategoria));
				$titulo = "Editar Senorita Categoria";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-female",
					"controlador" => "SenoritaCategoria",
					"senoritaCategoria" => $senoritaCategoria,
					"proceso" => "Editar",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/senoritaCategoria.js"
					),
				);
				$this->load->view("senoritaCategoria/senoritaCategoriaEditar", $datosVista);
			} else if ($this->input->method(TRUE) == "POST") {
				$idSenoritaCategoria = $this->input->post("idSenoritaCategoria");
				$nombreSenoritaCategoria = $this->input->post("categoriaSenorita");
				$tipoComisionSenoritaCategoria = $this->input->post("tipoComision");
				$comisionSenoritaCategoria = $this->input->post("cantidadComision");

				$condicionExiste = array(
					'nombreSenoritaCategoria' => $nombreSenoritaCategoria, 
					'md5(idSenoritaCategoria) !=' => $idSenoritaCategoria ,
					'estadoSenoritaCategoria !=' => 'Borrado'
				);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosSenoritaCategoria = array(
						"nombreSenoritaCategoria" => $nombreSenoritaCategoria,
						"tipoComisionSenoritaCategoria" => $tipoComisionSenoritaCategoria,
						"comisionSenoritaCategoria" => $comisionSenoritaCategoria,
						"aleatorioSenoritaCategoria" => uniqid(),
					);
					$condicion = array(
						'md5(idSenoritaCategoria)' => $idSenoritaCategoria,
					);
					IniciarTransaccion();
					$guardar = EditarDatos($this->tabla, $datosSenoritaCategoria,$condicion);
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
	function SenoritaCategoriaCambiarEstado() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idSenoritaCategoria = $this->input->post("idCategoria");
				$condicionDatos = array(
					'md5(idSenoritaCategoria)' => $idSenoritaCategoria,
					'estadoSenoritaCategoria' => 'Activo',
				);
				$activoSenoritaCategoria = ExistenDatos($this->tabla, $condicionDatos);

				($activoSenoritaCategoria == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

				$datosSenoritaCategorias = array(
					"estadoSenoritaCategoria" => $nuevoEstado
				);
				$condicion = array("md5(idSenoritaCategoria)" => $idSenoritaCategoria);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosSenoritaCategorias, $condicion);
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
	function SenoritaCategoriaEliminar() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idSenoritaCategoria = $this->input->post("idCategoria");

				$nuevoEstado = 'Borrado';

				$datosSenoritaCategorias = array(
					"estadoSenoritaCategoria" => $nuevoEstado
				);
				$condicion = array("md5(idSenoritaCategoria)" => $idSenoritaCategoria);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosSenoritaCategorias, $condicion);
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
