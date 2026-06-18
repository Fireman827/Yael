<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Senorita extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "senorita";
	private $tablaCategoria = "senoritaCategoria";
	private $controlador = "Senorita";
	function __construct() 	{
		parent::__construct();
		$this->load->Model('CoreModel', "core");
	}

	public function index()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			$titulo = "Señorita ";
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fa fa-female",
				"botones" => array(
					array(
						"icono" => "fa fa-female",
						'controlador' => $this->controlador,
						'url' => 'SenoritaAgregar',
						'txt' => 'Agregar Senorita',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
						'modal' => false,
						'id' => 'senoritaAgregar'
					),
				),
				"encabezados" => array(
					"ID" => 1,
					"Nombre" => 3,
					"Apodo" => 3,
					"Categoria" => 2,
					"Nacionalidad" => 2,
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
					"scripts/senorita.js"
				),
			);
			GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
		}
	}
	function SenoritaMostrar() {
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
			0 => 'idSenorita',
			1 => 'nombreSenorita',
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
		$condicion = array('estadoSenorita !=' => "Borrado", 'idSucursalSenorita' => $sucursal);
		// $joins = array(array('tabla' => 'cliente', 'condicion' => 'cliente.idCliente = membrecia.idClienteMembrecia'));
		$Senoritas = TraerDatosTabla($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($Membrecias);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Senoritas != 0) {
			$datosMostrar = array();
			foreach ($Senoritas as $Senorita) {
				$estadoSenorita = $Senorita->estadoSenorita;
				if ($estadoSenorita == "Activo") {
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else if ($estadoSenorita == "Inactivo") {
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

				$funcion = "SenoritaEditar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($Senorita->idSenorita)."'><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "SenoritaCambiarEstado";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idSenorita=" . md5($Senorita->idSenorita) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "SenoritaEliminar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' idSenorita=" . md5($Senorita->idSenorita) . "><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";

				$categoria = TraerUnDato($this->tablaCategoria,array('idSenoritaCategoria' => $Senorita->idSenoritaCategoria));

				$datosMostrar[] = array(
					$Senorita->idSenorita,
					$Senorita->nombreSenorita,
					$Senorita->apodoSenorita,
					$categoria->nombreSenoritaCategoria,
					$Senorita->nacionalidadSenorita,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalSenorita = TraerTotalDatos($this->tabla, $condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalSenorita,
				"recordsFiltered" => $totalSenorita,
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
	function SenoritaAgregar() {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$categoria = TraerDatos($this->tablaCategoria,array('estadoSenoritaCategoria'=>'Activo'));
				$titulo = "Agregar Señorita ";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-female",
					"controlador" => "Senorita",
					"proceso" => "Agregar",
					"categorias" => $categoria,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/senorita.js"
					),
				);
				GblPlantilla("senorita/SenoritaAgregar",$datosVista,$extras,$titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$nombreSenorita = $this->input->post("nombreSenorita");
				$apodoSenorita = $this->input->post("apodoSenorita");
				$categoriaSenorita = $this->input->post("categoriaSenorita");
				$alturaSenorita = $this->input->post("alturaSenorita");
				$pesoSenorita = $this->input->post("pesoSenorita");
				$edadSenorita = $this->input->post("edadSenorita");
				$nacionalidadSenorita = $this->input->post("nacionalidadSenorita");
				$extraSenorita = $this->input->post("extraSenorita");
				$sucursalSenorita  = $this->session->idSucursal;

				$condicionExiste = array('nombreSenorita' => $nombreSenorita, 'estadoSenorita !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosSenorita = array(
						"idSucursalSenorita" => $sucursalSenorita,
						"nombreSenorita" => $nombreSenorita,
						"apodoSenorita" => $apodoSenorita,
						"idSenoritaCategoria" => $categoriaSenorita,
						"alturaSenorita" => $alturaSenorita,
						"pesoSenorita" => $pesoSenorita,
						"edadSenorita" => $edadSenorita,
						"nacionalidadSenorita" => $nacionalidadSenorita,
						"extraSenorita" => $extraSenorita,
						"estadoSenorita" => "Activo",
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla, $datosSenorita);
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
	function SenoritaEditar($idSenorita = '') {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$categoria = TraerDatos($this->tablaCategoria,array('estadoSenoritaCategoria'=>'Activo'));
				$senorita = TraerUnDato($this->tabla,array("md5(idSenorita)" => $idSenorita));
				$titulo = "Editar Senorita ";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-female",
					"controlador" => "Senorita",
					"senorita" => $senorita,
					"categorias" => $categoria,
					"proceso" => "Editar",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/senorita.js"
					),
				);
				GblPlantilla("senorita/SenoritaEditar",$datosVista,$extras,$titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$idSenorita = $this->input->post("idSenorita");
				$nombreSenorita = $this->input->post("nombreSenorita");
				$apodoSenorita = $this->input->post("apodoSenorita");
				$categoriaSenorita = $this->input->post("categoriaSenorita");
				$alturaSenorita = $this->input->post("alturaSenorita");
				$pesoSenorita = $this->input->post("pesoSenorita");
				$edadSenorita = $this->input->post("edadSenorita");
				$nacionalidadSenorita = $this->input->post("nacionalidadSenorita");
				$extraSenorita = $this->input->post("extraSenorita");
				$sucursalSenorita  = $this->session->idSucursal;


				$condicionExiste = array(
					'nombreSenorita' => $nombreSenorita, 
					'idSenorita !=' => $idSenorita ,
					'estadoSenorita !=' => 'Borrado'
				);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosSenorita = array(
						"idSucursalSenorita" => $sucursalSenorita,
						"nombreSenorita" => $nombreSenorita,
						"apodoSenorita" => $apodoSenorita,
						"idSenoritaCategoria" => $categoriaSenorita,
						"alturaSenorita" => $alturaSenorita,
						"pesoSenorita" => $pesoSenorita,
						"edadSenorita" => $edadSenorita,
						"nacionalidadSenorita" => $nacionalidadSenorita,
						"extraSenorita" => $extraSenorita,
						"aleatorioSenorita" => uniqid(),
					);
					$condicion = array(
						'idSenorita' => $idSenorita,
					);
					IniciarTransaccion();
					$guardar = EditarDatos($this->tabla, $datosSenorita,$condicion);
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
	function SenoritaCambiarEstado() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idSenorita = $this->input->post("idSenorita");
				$condicionDatos = array(
					'md5(idSenorita)' => $idSenorita,
					'estadoSenorita' => 'Activo',
				);
				$activoSenorita = ExistenDatos($this->tabla, $condicionDatos);

				($activoSenorita == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

				$datosSenoritas = array(
					"estadoSenorita" => $nuevoEstado
				);
				$condicion = array("md5(idSenorita)" => $idSenorita);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosSenoritas, $condicion);
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
	function SenoritaEliminar() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idSenorita = $this->input->post("idSenorita");
				$nuevoEstado = 'Borrado';

				$datosSenoritas = array(
					"estadoSenorita" => $nuevoEstado
				);
				$condicion = array("md5(idSenorita)" => $idSenorita);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosSenoritas, $condicion);
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
/* End of file s.php */
