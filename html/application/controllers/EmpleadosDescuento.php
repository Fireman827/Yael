<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpleadosDescuento extends CI_Controller {

	private $tabla = "empleadoDescuento";
	private $controlador = "EmpleadosDescuento";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
		$this->load->helper('core_helper');
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Descuentos a Empleados";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fas fa-minus-circle",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'EmpleadosDescuentoAgregar',
						'txt' => 'Agregar Descuento',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
				"encabezados"=>array(
					"Fecha"=>1,
					"Empleado"=>2,
					"Tipo"=>1,
					"Monto"=>1,
					"Concepto"=>2,
					"Periodo"=>2,
					"Aplicado"=>1,
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
					"scripts/empleadosDescuento.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function EmpleadosDescuentoMostrar(){
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
			0 => 'empleadoDescuento.fechaRegistroEmpleadoDescuento',
			1 => 'empleado.nombreEmpleado',
			2 => 'empleado.apellidoEmpleado',
			3 => 'empleadoDescuento.descripcionEmpleadoDescuento'
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
		$condicion = array('empleadoDescuento.idSucursalEmpleadoDescuento' => $sucursal,'empleadoDescuento.estadoEmpleadoDescuento!=' => 'Borrado');
		$joins = array(array('tabla' => 'empleado', 'condicion' => 'empleado.idEmpleado = empleadoDescuento.idEmpleadoEmpleadoDescuento'));
		$EmpleadosDescuento = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$joins);
		
		//Lectura de datos de la base para mostrar en el datatabla
		if ($EmpleadosDescuento!= 0){
			$datosMostrar = array();
			foreach ($EmpleadosDescuento as $EmpleadoDescuento) {
                //EXTRAER PERIODO EN LETRAS
				$condicionDatos = array('idPeriodoPlanilla' => $EmpleadoDescuento->idPeriodoEmpleadoDescuento);
                $datosPeriodoPlanilla = TraerUnDato('periodoPlanilla',$condicionDatos);
				$periodoVigente = "";
				
                if($datosPeriodoPlanilla!==false){
                    list($a,$m,$d) = explode("-", $datosPeriodoPlanilla->desdePeriodoPlanilla);
                    list($a1,$m1,$d1) = explode("-", $datosPeriodoPlanilla->hastaPeriodoPlanilla);
                    if($a == $a1){
                        if($m==$m1){
                            if($d==$d1){
                                $periodoVigente="$d1 DE ".meses($m)." DE $a";
                            } else {
                                $periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
                            }
                        } else {
                            $periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
                        }
                    } else {
                        $periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
                    }
                } else $periodoVigente = "PROXIMO PERIODO";
				

                $estadoEmpleadoDescuento = $EmpleadoDescuento->estadoEmpleadoDescuento;
                if ($estadoEmpleadoDescuento == 'Activo') {
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

                $funcion = "EmpleadosDescuentoEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($EmpleadoDescuento->idEmpleadoDescuento)."' ><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "EmpleadosDescuentoCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idEmpleadoDescuento=" . md5($EmpleadoDescuento->idEmpleadoDescuento) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "EmpleadosDescuentoEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idEmpleadoDescuento=" . md5($EmpleadoDescuento->idEmpleadoDescuento) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";

				//FECHA EN FORMATO d-m-Y
				$fechaRegistro = date_create($EmpleadoDescuento->fechaRegistroEmpleadoDescuento);

                $datosMostrar[] = array(
					date_format($fechaRegistro, 'd-m-Y'),
                    $EmpleadoDescuento->nombreEmpleado." ".$EmpleadoDescuento->apellidoEmpleado,
                    $EmpleadoDescuento->tipoEmpleadoDescuento,
                    $EmpleadoDescuento->montoEmpleadoDescuento,
                    $EmpleadoDescuento->descripcionEmpleadoDescuento,
                    $periodoVigente,
                    $EmpleadoDescuento->aplicadoEmpleadoDescuento,
                    $estadoSpan,
                    $menuOpciones
                );
            }
			$totalEmpleadosDescuento = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalEmpleadosDescuento,
				"recordsFiltered" => $totalEmpleadosDescuento,
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

	function EmpleadosDescuentoAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//CONSULTA PARA TRAER EL PERIODO DE PLANILLA VIGENTE
				$condicionDatos = array('vencidoPeriodoPlanilla' => 'Vigente');
				$datosPeriodoPlanilla = TraerUnDato('periodoPlanilla',$condicionDatos);
				$periodoVigente = "";
				
                if($datosPeriodoPlanilla!==false){
                    list($a,$m,$d) = explode("-", $datosPeriodoPlanilla->desdePeriodoPlanilla);
                    list($a1,$m1,$d1) = explode("-", $datosPeriodoPlanilla->hastaPeriodoPlanilla);
                    if($a == $a1){
                        if($m==$m1){
                            if($d==$d1){
                                $periodoVigente="$d1 DE ".meses($m)." DE $a";
                            } else {
                                $periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
                            }
                        } else {
                            $periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
                        }
                    } else {
                        $periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
                    }
                } else {
					$periodoVigente = "PROXIMO";
				}
	
				$titulo = "Agregar Descuento del Empleado";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fas fa-minus-circle",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"periodoVigente" => $periodoVigente,
                    "idPeriodoPlanilla" => $datosPeriodoPlanilla->idPeriodoPlanilla
				);
				$extras = array(
					'css' => array(
						"vendors/plugins/TypeAhead/typeahead.css"
					),
					'js' => array(
						"scripts/empleadosDescuento.js",
						"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
					),
				);
				GblPlantilla("empleadosDescuento/EmpleadoDescuentoAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalEmpleadoDescuento = $this->session->idSucursal;
				$idEmpleadoEmpleadoDescuento = $this->input->post("idEmpleadoEmpleadoDescuento");
				$tipoEmpleadoDescuento = $this->input->post("tipoEmpleadoDescuento");
				$montoEmpleadoDescuento = $this->input->post("montoEmpleadoDescuento");
				$descripcionEmpleadoDescuento = $this->input->post("descripcionEmpleadoDescuento");
				$idPeriodoEmpleadoDescuento = $this->input->post("idPeriodoEmpleadoDescuento");

				$condicionExiste = array(
					'idEmpleadoEmpleadoDescuento' => $idEmpleadoEmpleadoDescuento,
					'idPeriodoEmpleadoDescuento' => $idPeriodoEmpleadoDescuento,
					'idSucursalEmpleadoDescuento'=>$idSucursalEmpleadoDescuento
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if(true){
					$datosEmpleadosDescuento  = array(
						"idEmpleadoEmpleadoDescuento"=>$idEmpleadoEmpleadoDescuento,
						"tipoEmpleadoDescuento"=>$tipoEmpleadoDescuento,
						"montoEmpleadoDescuento"=>$montoEmpleadoDescuento,
						"descripcionEmpleadoDescuento"=>$descripcionEmpleadoDescuento,
						"idPeriodoEmpleadoDescuento"=>$idPeriodoEmpleadoDescuento,
						"fechaRegistroEmpleadoDescuento"=> date('Y-m-d'),
						"aplicadoEmpleadoDescuento"=>"NO",
						"estadoEmpleadoDescuento"=> 'Activo',
						"aleatorioEmpleadoDescuento" => uniqid(),
						"idSucursalEmpleadoDescuento"=>$idSucursalEmpleadoDescuento
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosEmpleadosDescuento);
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

	function EmpleadosDescuentoEditar($idEmpleadoDescuento=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idEmpleadoDescuento)' => $idEmpleadoDescuento);
				$datosEmpleadosDescuento = TraerUnDato($this->tabla, $condicionDatos);
				if($datosEmpleadosDescuento !== false && $idEmpleadoDescuento!=""){
					//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
					$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$datosEmpleadosDescuento->idEmpleadoEmpleadoDescuento));
					$empleadoEmpleadoDescuento = $datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado;

                    //CONSULTA PARA TRAER EL PERIODO DE PLANILLA VIGENTE
					$datosPeriodoPlanilla = TraerUnDato('periodoPlanilla',array("vencidoPeriodoPlanilla"=>"Vigente"));
					$periodoVigente = "";
					
					if($datosPeriodoPlanilla!==false){
						list($a,$m,$d) = explode("-", $datosPeriodoPlanilla->desdePeriodoPlanilla);
						list($a1,$m1,$d1) = explode("-", $datosPeriodoPlanilla->hastaPeriodoPlanilla);
						if($a == $a1){
							if($m==$m1){
								if($d==$d1){
									$periodoVigente="$d1 DE ".meses($m)." DE $a";
								} else {
									$periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
								}
							} else {
								$periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
							}
						} else {
							$periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
						}
					} else {
						$periodoVigente = "PROXIMO";
					}
			
					$titulo = "Editar Descuento del Empleado";
					$datosVista = array(
						"datosEmpleadosDescuento"=> $datosEmpleadosDescuento,
						"controlador" => $this->controlador,
						"idEmpleadoDescuento" => $idEmpleadoDescuento,
						"titulo" => $titulo,
						"proceso" => "Editar",
                        "icono" => "fas fa-minus-circle",
						"periodoVigente" => $periodoVigente,
                    	"idPeriodoPlanilla" => $datosPeriodoPlanilla->idPeriodoPlanilla,
						"empleadoEmpleadoDescuento" => $empleadoEmpleadoDescuento
					);
					$extras = array(
						'css' => array(
							"vendors/plugins/TypeAhead/typeahead.css"
						),
						'js' => array(
							"scripts/empleadosDescuento.js",
							"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
						),
					);
					GblPlantilla("empleadosDescuento/EmpleadoDescuentoEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idEmpleadoDescuento = $this->input->post("idEmpleadoDescuento");
				$idEmpleadoEmpleadoDescuento = $this->input->post("idEmpleadoEmpleadoDescuento");
				$idSucursalEmpleadoDescuento = $this->session->idSucursal;
				$tipoEmpleadoDescuento = $this->input->post("tipoEmpleadoDescuento");
				$montoEmpleadoDescuento = $this->input->post("montoEmpleadoDescuento");
				$descripcionEmpleadoDescuento = $this->input->post("descripcionEmpleadoDescuento");
				$idPeriodoEmpleadoDescuento = $this->input->post("idPeriodoEmpleadoDescuento");
				
				$condicionExiste = array(
					'idEmpleadoEmpleadoDescuento' => $idEmpleadoEmpleadoDescuento,
					'idPeriodoEmpleadoDescuento' => $idPeriodoEmpleadoDescuento,
					'idSucursalEmpleadoDescuento'=>$idSucursalEmpleadoDescuento
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if(true){
					$datosEmpleadosDescuento = array(
						"idEmpleadoEmpleadoDescuento"=>$idEmpleadoEmpleadoDescuento,
						"tipoEmpleadoDescuento"=>$tipoEmpleadoDescuento,
						"montoEmpleadoDescuento"=>$montoEmpleadoDescuento,
						"descripcionEmpleadoDescuento"=>$descripcionEmpleadoDescuento,
						"idPeriodoEmpleadoDescuento"=>$idPeriodoEmpleadoDescuento,
						"aleatorioEmpleadoDescuento" => uniqid(),
						"idSucursalEmpleadoDescuento"=>$idSucursalEmpleadoDescuento
					);
					IniciarTransaccion();
					$condicion = array("md5(idEmpleadoDescuento)" => $idEmpleadoDescuento);
					$editar = EditarDatos($this->tabla,$datosEmpleadosDescuento,$condicion);
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

	function EmpleadosDescuentoCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleadoDescuento = $this->input->post("idEmpleadoDescuento");
                $condicionDatos = array(
                    'md5(idEmpleadoDescuento)' => $idEmpleadoDescuento,
                    'estadoEmpleadoDescuento' => 'Activo',
                );
                $activoEmpleadoDescuento = ExistenDatos($this->tabla, $condicionDatos);

                ($activoEmpleadoDescuento == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosEmpleadoDescuento = array(
                    "estadoEmpleadoDescuento" => $nuevoEstado
                );
                $condicion = array("md5(idEmpleadoDescuento)" => $idEmpleadoDescuento);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleadoDescuento, $condicion);
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
    
    function EmpleadosDescuentoEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleadoDescuento = $this->input->post("idEmpleadoDescuento");
                $datosEmpleadoDescuento = array(
                    "estadoEmpleadoDescuento" => 'Borrado'
                );
                $condicion = array("md5(idEmpleadoDescuento)" => $idEmpleadoDescuento);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleadoDescuento, $condicion);
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

	function EmpleadosDescuentoAutocompleteEmpleado()
    {
		if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
			echo json_encode($datosRespuesta);
        } else {
			if($this->input->method(TRUE) == "POST"){
				$busquedaParametro = $this->input->post("query");
				//Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
				$columnasValidas = array(
					0 => 'idEmpleado',
					1 => 'nombreEmpleado',
					2 => 'apellidoEmpleado'
				);

				// Fin espacio del data tabla
				$sucursal = $this->session->idSucursal;
				$condicionWhere = array('idSucursalEmpleado' => $sucursal,'estadoEmpleado!=' => 'Borrado');
				$condicionLike = array('nombreEmpleado' => $busquedaParametro);
				$Empleados = TraerDatosComo("empleado",$condicionWhere,$condicionLike);
				echo json_encode($Empleados);
			}
            
        }
    }
    
}
/* End of file EmpleadosDescuento.php */
