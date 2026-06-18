    <?php
defined('BASEPATH') or exit('No direct script access allowed');

class Servicios extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "servicio";
	private $tablaDetalle = "servicioDetalle";
	private $tablaCategoria = "servicioCategoria";
	private $tablaCategoriaSenorita = "senoritaCategoria";
	private $controlador = "Servicio";
	function __construct() 	{
		parent::__construct();
		$this->load->Model('CoreModel', "core");
	}

	public function index()	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			$titulo = "Servicios ";
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fa fa-user-clock",
				"botones" => array(
					array(
						"icono" => "fa fa-user-clock",
						'controlador' => $this->controlador,
						'url' => 'ServicioAgregar',
						'txt' => 'Agregar Servicio',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
						'modal' => false,
						'id' => 'ServicioAgregar'
					),
				),
				"encabezados" => array(
					"ID" => 1,
					"Categoria" => 3,
					"Tiempo" => 1,
					"Descripcion" => 5,
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
					"scripts/servicio.js"
				),
			);
			GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
		}
	}
	function ServicioMostrar() {
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
			0 => 'idServicio',
			1 => 'tiempoServicio',
            2 => 'descripcionServicio'
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
		$condicion = array('estadoServicio !=' => "Borrado", 'idSucursalServicio' => $sucursal);
		// $joins = array(array('tabla' => 'cliente', 'condicion' => 'cliente.idCliente = membrecia.idClienteMembrecia'));
		$Servicios = TraerDatosTabla($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
		// print_r($Membrecias);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Servicios != 0) {
			$datosMostrar = array();
			foreach ($Servicios as $Servicio) {
				$estadoServicio = $Servicio->estadoServicio;
				if ($estadoServicio == "Activo") {
					$estadoTxt = "Desactivar";
					$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
					$estadoIcon = "fa fa fa-toggle-on";
				} else if ($estadoServicio == "Inactivo") {
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

				$funcion = "ServicioEditar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($Servicio->idServicio)."'><i class='fa fa-edit' ></i> Editar</a>";
				}
				$funcion = "ServicioCambiarEstado";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idServicio=" . md5($Servicio->idServicio) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
				}
				$funcion = "ServicioEliminar";
				if (GblPermisos($this, $funcion, $this->controlador)) {
					$menuOpciones .= "<a class='dropdown-item " . $funcion . "' idServicio=" . md5($Servicio->idServicio) . "><i class='fa fa-trash'></i> Eliminar</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";

				$categoria = TraerUnDato($this->tablaCategoria,array('idServicioCategoria' => $Servicio->idServicioCategoria));

				$datosMostrar[] = array(
					$Servicio->idServicio,
					$categoria->nombreServicioCategoria,
					$Servicio->tiempoServicio,
					$Servicio->descripcionServicio,
					$estadoSpan,
					$menuOpciones,
				);
			}
			$totalServicio = TraerTotalDatos($this->tabla, $condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalServicio,
				"recordsFiltered" => $totalServicio,
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
	function ServicioAgregar() {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$categoria = TraerDatos($this->tablaCategoria,array('estadoServicioCategoria'=>'Activo'));
				$senoritas = TraerDatos($this->tablaCategoriaSenorita,array('estadoSenoritaCategoria'=>'Activo'));
				$titulo = "Agregar Servicio ";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-user-clock",
					"controlador" => "Servicios",
					"proceso" => "Agregar",
					"categorias" => $categoria,
					"senoritas" => $senoritas,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/servicio.js"
					),
				);
				GblPlantilla("servicios/ServicioAgregar",$datosVista,$extras,$titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$tiempoServicio = $this->input->post("tiempoServicio");
				$descripcionServicio = $this->input->post("descripcionServicio");
				$categoriaServicio = $this->input->post("categoriaServicio");
				$tablaServicio = json_decode($this->input->post("datosTablaSenoritas"));
				$sucursalServicio  = $this->session->idSucursal;

				$condicionExiste = array('tiempoServicio' => $tiempoServicio,'idServicioCategoria'=>$categoriaServicio, 'estadoServicio !=' => 'Borrado');
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosServicio = array(
						"idServicioCategoria" => $categoriaServicio,
						"idSucursalServicio" => $sucursalServicio,
						"descripcionServicio" => $descripcionServicio,
						"tiempoServicio" => $tiempoServicio,
						"estadoServicio" => "Activo",
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla, $datosServicio);
					if ($guardar) {
                        $idServicio = $guardar;
                        $n = 0;
                        for ($i=0; $i < count($tablaServicio); $i++) { 
                            $datosServicioDetalle = array(
                                "idServicio" => $idServicio,
                                "idSenoritaCategoriaServicioDetalle" => $tablaServicio[$i][0],
                                "montoServicioDetalle" =>  $tablaServicio[$i][1],
                                "porcentajeSenoritaServicioDetalle" =>  $tablaServicio[$i][2],
                                "estadoServicioDetalle" => "Activo",
                            );
                            $guardarDetalle = GuardarDatos($this->tablaDetalle, $datosServicioDetalle);
                            if($guardarDetalle){
                                $n++;
                            }
                        }
                        if($n == count($tablaServicio)){
                            EjecutarTransaccion();
                            $datosRespuesta["codigo"] = 200;
                        }else {
                            DeshacerTransaccion();
                            $datosRespuesta["codigo"] = 501;
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
	function ServicioEditar($idServicio = '') {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
		} else {
			if ($this->input->method(TRUE) == "GET") {
				$condicion = array(
					$this->tablaCategoriaSenorita.".estadoSenoritaCategoria"=>"Activo",
				);				
				$senoritas2 = TraerDatos($this->tablaCategoriaSenorita,$condicion);
				$servicioDetalle2 = TraerDatos($this->tablaDetalle,array("md5(idServicio)"=>$idServicio));
				$nServ = array();
				foreach($senoritas2 as $sen){
					$sen->montoServicioDetalle = '';
					$sen->porcentajeSenoritaServicioDetalle = '';
					foreach($servicioDetalle2 as $ser2){
						if($sen->idSenoritaCategoria == $ser2->idSenoritaCategoriaServicioDetalle){
							$sen->montoServicioDetalle = $ser2->montoServicioDetalle;
							$sen->porcentajeSenoritaServicioDetalle = $ser2->porcentajeSenoritaServicioDetalle;
						}
					}
					array_push($nServ,$sen);
				}
				
                $servicio = TraerUnDato($this->tabla,array('md5(idServicio)' => $idServicio));
                $servicioDetalle = TraerDatos($this->tablaDetalle,array('md5(idServicio)' => $idServicio,'estadoServicioDetalle !='=>'Borrado'));
				$categoria = TraerDatos($this->tablaCategoria,array('estadoServicioCategoria'=>'Activo'));
				$titulo = "Editar Servicio ";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-user-clock",
					"controlador" => "Servicios",
					"proceso" => "Editar",
                    "servicio" => $servicio,
                    "servicioDetalles" => $servicioDetalle,
					"categorias" => $categoria,
					"senoritas" => $nServ,
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/servicio.js"
					),
				);
                GblPlantilla("servicios/ServicioEditar",$datosVista,$extras,$titulo);
			} else if ($this->input->method(TRUE) == "POST") {
				$idServicio = $this->input->post("idServicio");
				$tiempoServicio = $this->input->post("tiempoServicio");
				$descripcionServicio = $this->input->post("descripcionServicio");
				$categoriaServicio = $this->input->post("categoriaServicio");
				$tablaServicio = json_decode($this->input->post("datosTablaSenoritas"));

				$condicionExiste = array(
					'idServicio !=' => $idServicio ,
					'idServicioCategoria' => $categoriaServicio ,
					'tiempoServicio' => $tiempoServicio, 
					'estadoServicio !=' => 'Borrado'
				);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if ($existe == 0) {
					$datosServicio = array(
						"idServicioCategoria" => $categoriaServicio,
						"descripcionServicio" => $descripcionServicio,
						"tiempoServicio" => $tiempoServicio,
						"aleatorioServicio" => uniqid(),
					);
					$condicion = array(
						'idServicio' => $idServicio,
					);
					IniciarTransaccion();
					$guardar = EditarDatos($this->tabla, $datosServicio,$condicion);
					if ($guardar) {
						$borrarDetalleAnterior = EditarDatos(
													$this->tablaDetalle,//tabla
													array("estadoServicioDetalle" =>"Borrado"),//datos
													array("idServicio"=>$idServicio)//condicion
												);
						if($borrarDetalleAnterior){
							$n = 0;
							for ($i=0; $i < count($tablaServicio); $i++) { 
								$datosServicioDetalle = array(
									"idServicio" => $idServicio,
									"idSenoritaCategoriaServicioDetalle" => $tablaServicio[$i][0],
									"montoServicioDetalle" =>  $tablaServicio[$i][1],
									"porcentajeSenoritaServicioDetalle" =>  $tablaServicio[$i][2],
								);
								$guardarDetalle = GuardarDatos($this->tablaDetalle, $datosServicioDetalle);
								if($guardarDetalle){
									$n++;
								}
							}
							if($n == count($tablaServicio)){
								EjecutarTransaccion();
								$datosRespuesta["codigo"] = 200;
							} else {
								DeshacerTransaccion();
								$datosRespuesta["codigo"] = 502;
							}
						} else{
							DeshacerTransaccion();
                            $datosRespuesta["codigo"] = 501;
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
	function ServicioCambiarEstado() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idServicio = $this->input->post("idServicio");
				$condicionDatos = array(
					'md5(idServicio)' => $idServicio,
					'estadoServicio' => 'Activo',
				);
				$activoServicio = ExistenDatos($this->tabla, $condicionDatos);

				($activoServicio == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

				$datosServicios = array(
					"estadoServicio" => $nuevoEstado
				);
				$condicion = array("md5(idServicio)" => $idServicio);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosServicios, $condicion);
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
	function ServicioEliminar() 	{
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
			$datosRespuesta["codigo"] = 403;
		} else {
			if ($this->input->method(TRUE) == "POST") {
				$idServicio = $this->input->post("idServicio");
				$nuevoEstado = 'Borrado';

				$datosServicios = array(
					"estadoServicio" => $nuevoEstado
				);
				$condicion = array("md5(idServicio)" => $idServicio);
				IniciarTransaccion();
				$editar = EditarDatos($this->tabla, $datosServicios, $condicion);
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
