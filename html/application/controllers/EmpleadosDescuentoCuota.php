<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpleadosDescuentoCuota extends CI_Controller {

	private $tabla = "empleadoDescuentoCuota";
	private $controlador = "EmpleadosDescuentoCuota";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
		$this->load->helper('core_helper');
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Descuentos a Empleados por Cuotas";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fas fa-minus-circle",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'EmpleadosDescuentoCuotaAgregar',
						'txt' => 'Agregar Descuento por Cuota',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
				"encabezados"=>array(
					"Id"=>1,
					"Empleado"=>2,
					"Institución"=>2,
					"Monto"=>1,
					"Concepto"=>3,
					"Número"=>1,
                    "Estado"=>1,
					"Acciones"=>1
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/empleadosDescuentoCuota.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function EmpleadosDescuentoCuotaMostrar(){
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
			0 => 'empleadoDescuentoCuota.idEmpleadoDescuentoCuota',
			1 => 'empleado.nombreEmpleado',
			2 => 'empleado.apellidoEmpleado',
			3 => 'empleadoDescuentoCuota.descripcionEmpleadoDescuentoCuota'
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
		$condicion = array('empleadoDescuentoCuota.idSucursalEmpleadoDescuentoCuota' => $sucursal,'empleadoDescuentoCuota.estadoEmpleadoDescuentoCuota!=' => 'Borrado');
		$joins = array(array('tabla' => 'empleado', 'condicion' => 'empleado.idEmpleado = empleadoDescuentoCuota.idEmpleadoEmpleadoDescuentoCuota'));
		$EmpleadosDescuentoCuota = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$joins);
		
		//Lectura de datos de la base para mostrar en el datatabla
		if ($EmpleadosDescuentoCuota!= 0){
			$datosMostrar = array();
			foreach ($EmpleadosDescuentoCuota as $EmpleadoDescuentoCuota) {
                //CONSULTA PARA OBTENER DATOS DE LA INSTITUCIÓN FINANCIERA
				$condicionDatos = array('idInstitucionFinanciera' => $EmpleadoDescuentoCuota->idInstitucionEmpleadoDescuentoCuota);
                $datosInstitucionFinanciera = TraerUnDato('empleadoInstitucionFinanciera',$condicionDatos);
				$InstitucionFinanciera = "";
				
                if($datosInstitucionFinanciera!==false){
                    $InstitucionFinanciera = $datosInstitucionFinanciera->nombreInstitucionFinanciera;
                }

                $estadoEmpleadoDescuentoCuota = $EmpleadoDescuentoCuota->estadoEmpleadoDescuentoCuota;
                if ($estadoEmpleadoDescuentoCuota == 'Activo') {
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

                $funcion = "EmpleadosDescuentoCuotaEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($EmpleadoDescuentoCuota->idEmpleadoDescuentoCuota)."' ><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "EmpleadosDescuentoCuotaCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idEmpleadoDescuentoCuota=" . md5($EmpleadoDescuentoCuota->idEmpleadoDescuentoCuota) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "EmpleadosDescuentoCuotaEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idEmpleadoDescuentoCuota=" . md5($EmpleadoDescuentoCuota->idEmpleadoDescuentoCuota) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";

                $datosMostrar[] = array(			
                    $EmpleadoDescuentoCuota->idEmpleadoDescuentoCuota,
                    $EmpleadoDescuentoCuota->nombreEmpleado." ".$EmpleadoDescuentoCuota->apellidoEmpleado,
                    $InstitucionFinanciera,
                    $EmpleadoDescuentoCuota->montoEmpleadoDescuentoCuota,
                    $EmpleadoDescuentoCuota->descripcionEmpleadoDescuentoCuota,
                    $EmpleadoDescuentoCuota->numeroCuotasEmpleadoDescuentoCuota,
                    $estadoSpan,
                    $menuOpciones
                );
            }
			$totalEmpleadosDescuentoCuota = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalEmpleadosDescuentoCuota,
				"recordsFiltered" => $totalEmpleadosDescuentoCuota,
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

	function EmpleadosDescuentoCuotaAgregar(){
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

                //OBTENER DATOS DE LA INSTITUCIÓN FINANCIERA
				$condicionDatos = array('estadoInstitucionFinanciera' => 'Activo');
                $datosInstitucionFinanciera = TraerDatos('empleadoInstitucionFinanciera',$condicionDatos);
				$InstitucionFinancieraOpciones = "";
				
                if($datosInstitucionFinanciera!==false){
                    foreach ($datosInstitucionFinanciera as $InstitucionFinanciera) {
                        $InstitucionFinancieraOpciones .= "<option value='".$InstitucionFinanciera->idInstitucionFinanciera."'>".$InstitucionFinanciera->nombreInstitucionFinanciera."</option>";
                    }
                }
	
				$titulo = "Agregar Descuento a Empleado por Cuotas";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fas fa-minus-circle",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"periodoVigente" => $periodoVigente,
                    "idPeriodoPlanilla" => $datosPeriodoPlanilla->idPeriodoPlanilla,
                    "InstitucionFinancieraOpciones" => $InstitucionFinancieraOpciones
				);
				$extras = array(
					'css' => array(
						"vendors/plugins/TypeAhead/typeahead.css"
					),
					'js' => array(
						"scripts/empleadosDescuentoCuota.js",
						"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
					),
				);
				GblPlantilla("empleadosDescuentoCuota/EmpleadoDescuentoCuotaAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalEmpleadoDescuentoCuota = $this->session->idSucursal;
				$idEmpleadoEmpleadoDescuentoCuota = $this->input->post("idEmpleadoEmpleadoDescuentoCuota");
				$idInstitucionEmpleadoDescuentoCuota = $this->input->post("idInstitucionEmpleadoDescuentoCuota");
				$montoEmpleadoDescuentoCuota = $this->input->post("montoEmpleadoDescuentoCuota");
				$numeroCuotasEmpleadoDescuentoCuota = $this->input->post("numeroCuotasEmpleadoDescuentoCuota");
				$descripcionEmpleadoDescuentoCuota = $this->input->post("descripcionEmpleadoDescuentoCuota");
				$idPeriodoEmpleadoDescuentoCuota = $this->input->post("idPeriodoEmpleadoDescuentoCuota");

				$condicionExiste = array(
					'idEmpleadoEmpleadoDescuentoCuota' => $idEmpleadoEmpleadoDescuentoCuota,
					'idPeriodoEmpleadoDescuentoCuota' => $idPeriodoEmpleadoDescuentoCuota,
					'idSucursalEmpleadoDescuentoCuota'=>$idSucursalEmpleadoDescuentoCuota
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if(true){
					$datosEmpleadosDescuentoCuota  = array(
						"idEmpleadoEmpleadoDescuentoCuota"=>$idEmpleadoEmpleadoDescuentoCuota,
						"idInstitucionEmpleadoDescuentoCuota"=>$idInstitucionEmpleadoDescuentoCuota,
						"montoEmpleadoDescuentoCuota"=>$montoEmpleadoDescuentoCuota,
						"numeroCuotasEmpleadoDescuentoCuota"=>$numeroCuotasEmpleadoDescuentoCuota,
						"descripcionEmpleadoDescuentoCuota"=>$descripcionEmpleadoDescuentoCuota,
						"idPeriodoEmpleadoDescuentoCuota"=>$idPeriodoEmpleadoDescuentoCuota,
						"fechaInicioEmpleadoDescuentoCuota"=> date('Y-m-d'),
						"pagadasEmpleadoDescuentoCuota"=> 0,
						"restantesEmpleadoDescuentoCuota"=>$numeroCuotasEmpleadoDescuentoCuota,
						"estadoEmpleadoDescuentoCuota"=> "Activo",
						"aleatorioEmpleadoDescuentoCuota" => uniqid(),
						"idSucursalEmpleadoDescuentoCuota"=>$idSucursalEmpleadoDescuentoCuota
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosEmpleadosDescuentoCuota);
					if($guardar){
						$insertarCorrecto = 0;
						for ($i=0; $i < $numeroCuotasEmpleadoDescuentoCuota; $i++) { 
							$datosEmpleadoDescuentoDetalle = array(
								"idEmpleadoEmpleadoDescuentoDetalle" => $idEmpleadoEmpleadoDescuentoCuota,
								"idDescuentoEmpleadoDescuentoDetalle" => $guardar,
								"montoEmpleadoDescuentoDetalle" => $montoEmpleadoDescuentoCuota,
								"idPeriodoEmpleadoDescuentoDetalle" => $idPeriodoEmpleadoDescuentoCuota+$i,
								"aplicadoEmpleadoDescuentoDetalle" => "NO"
							);
							$guardarDescuentoDetalle = GuardarDatos("empleadoDescuentoDetalle",$datosEmpleadoDescuentoDetalle);
							if($guardarDescuentoDetalle!=false){
								$insertarCorrecto = $insertarCorrecto+1;
							}
						}
						if($insertarCorrecto==$numeroCuotasEmpleadoDescuentoCuota){
							//La acción se realizo con éxito						
							EjecutarTransaccion();
							$datosRespuesta["codigo"] = 200;
						} else {
							//La acción no pudo ser realizada
							DeshacerTransaccion();
							$datosRespuesta["codigo"] = 402;
						}
						
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

	function EmpleadosDescuentoCuotaEditar($idEmpleadoDescuentoCuota=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idEmpleadoDescuentoCuota)' => $idEmpleadoDescuentoCuota);
				$datosEmpleadosDescuentoCuota = TraerUnDato($this->tabla, $condicionDatos);
				if($datosEmpleadosDescuentoCuota !== false && $idEmpleadoDescuentoCuota!=""){
					//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
					$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$datosEmpleadosDescuentoCuota->idEmpleadoEmpleadoDescuentoCuota));
					$empleadoEmpleadoDescuentoCuota = $datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado;

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

                    //OBTENER DATOS DE LA INSTITUCIÓN FINANCIERA
                    $condicionDatos = array('estadoInstitucionFinanciera' => 'Activo');
                    $datosInstitucionFinanciera = TraerDatos('empleadoInstitucionFinanciera',$condicionDatos);
                    $InstitucionFinancieraOpciones = "";
                    
                    if($datosInstitucionFinanciera!==false){
                        foreach ($datosInstitucionFinanciera as $InstitucionFinanciera) {
                            if($InstitucionFinanciera->idInstitucionFinanciera==$datosEmpleadosDescuentoCuota->idInstitucionEmpleadoDescuentoCuota){
								$InstitucionFinancieraOpciones .= "<option value='".$InstitucionFinanciera->idInstitucionFinanciera."' selected >".$InstitucionFinanciera->nombreInstitucionFinanciera."</option>";
							} else {
								$InstitucionFinancieraOpciones .= "<option value='".$InstitucionFinanciera->idInstitucionFinanciera."'>".$InstitucionFinanciera->nombreInstitucionFinanciera."</option>";
							}
                        }
                    }
			
					$titulo = "Editar Descuento a Empleado por Cuotas";
					$datosVista = array(
						"datosEmpleadosDescuentoCuota"=> $datosEmpleadosDescuentoCuota,
						"controlador" => $this->controlador,
						"idEmpleadoDescuentoCuota" => $idEmpleadoDescuentoCuota,
						"titulo" => $titulo,
						"proceso" => "Editar",
                        "icono" => "fas fa-minus-circle",
						"periodoVigente" => $periodoVigente,
                    	"idPeriodoPlanilla" => $datosPeriodoPlanilla->idPeriodoPlanilla,
						"empleadoEmpleadoDescuentoCuota" => $empleadoEmpleadoDescuentoCuota,
                        "InstitucionFinancieraOpciones" => $InstitucionFinancieraOpciones
					);
					$extras = array(
						'css' => array(
							"vendors/plugins/TypeAhead/typeahead.css"
						),
						'js' => array(
							"scripts/empleadosDescuentoCuota.js",
							"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
						),
					);
					GblPlantilla("empleadosDescuentoCuota/EmpleadoDescuentoCuotaEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idSucursalEmpleadoDescuentoCuota = $this->session->idSucursal;
				$idEmpleadoDescuentoCuota = $this->input->post("idEmpleadoDescuentoCuota");
				$idEmpleadoEmpleadoDescuentoCuota = $this->input->post("idEmpleadoEmpleadoDescuentoCuota");
				$idInstitucionEmpleadoDescuentoCuota = $this->input->post("idInstitucionEmpleadoDescuentoCuota");
				$montoEmpleadoDescuentoCuota = $this->input->post("montoEmpleadoDescuentoCuota");
				$numeroCuotasEmpleadoDescuentoCuota = $this->input->post("numeroCuotasEmpleadoDescuentoCuota");
				$descripcionEmpleadoDescuentoCuota = $this->input->post("descripcionEmpleadoDescuentoCuota");
				$idPeriodoEmpleadoDescuentoCuota = $this->input->post("idPeriodoEmpleadoDescuentoCuota");
				
				$condicionExiste = array(
					'idEmpleadoEmpleadoDescuentoCuota' => $idEmpleadoEmpleadoDescuentoCuota,
					'idPeriodoEmpleadoDescuentoCuota' => $idPeriodoEmpleadoDescuentoCuota,
					'idSucursalEmpleadoDescuentoCuota'=>$idSucursalEmpleadoDescuentoCuota
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if(true){
					$datosEmpleadosDescuentoCuota = array(
						"idEmpleadoEmpleadoDescuentoCuota"=>$idEmpleadoEmpleadoDescuentoCuota,
						"idInstitucionEmpleadoDescuentoCuota"=>$idInstitucionEmpleadoDescuentoCuota,
						"montoEmpleadoDescuentoCuota"=>$montoEmpleadoDescuentoCuota,
						"numeroCuotasEmpleadoDescuentoCuota"=>$numeroCuotasEmpleadoDescuentoCuota,
						"descripcionEmpleadoDescuentoCuota"=>$descripcionEmpleadoDescuentoCuota,
						"idPeriodoEmpleadoDescuentoCuota"=>$idPeriodoEmpleadoDescuentoCuota,
						"aleatorioEmpleadoDescuentoCuota" => uniqid(),
						"idSucursalEmpleadoDescuentoCuota"=>$idSucursalEmpleadoDescuentoCuota
					);
					IniciarTransaccion();
					$condicion = array("md5(idEmpleadoDescuentoCuota)" => $idEmpleadoDescuentoCuota);
					$editar = EditarDatos($this->tabla,$datosEmpleadosDescuentoCuota,$condicion);
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

	function EmpleadosDescuentoCuotaCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleadoDescuentoCuota = $this->input->post("idEmpleadoDescuentoCuota");
                $condicionDatos = array(
                    'md5(idEmpleadoDescuentoCuota)' => $idEmpleadoDescuentoCuota,
                    'estadoEmpleadoDescuentoCuota' => 'Activo',
                );
                $activoEmpleadoDescuentoCuota = ExistenDatos($this->tabla, $condicionDatos);

                ($activoEmpleadoDescuentoCuota == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosEmpleadoDescuentoCuota = array(
                    "estadoEmpleadoDescuentoCuota" => $nuevoEstado
                );
                $condicion = array("md5(idEmpleadoDescuentoCuota)" => $idEmpleadoDescuentoCuota);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleadoDescuentoCuota, $condicion);
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
    
    function EmpleadosDescuentoCuotaEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleadoDescuentoCuota = $this->input->post("idEmpleadoDescuentoCuota");
                $datosEmpleadoDescuentoCuota = array(
                    "estadoEmpleadoDescuentoCuota" => 'Borrado'
                );
                $condicion = array("md5(idEmpleadoDescuentoCuota)" => $idEmpleadoDescuentoCuota);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleadoDescuentoCuota, $condicion);
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

	function EmpleadosDescuentoCuotaAutocompleteEmpleado()
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
/* End of file EmpleadosDescuentoCuota.php */
