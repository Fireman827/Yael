<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmpleadosBono extends CI_Controller {

	private $tabla = "empleadoBono";
	private $controlador = "EmpleadosBono";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
		$this->load->helper('core_helper');
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Bonos a Empleados";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fas fa-plus-circle",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'EmpleadosBonoAgregar',
						'txt' => 'Agregar Bono',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
				"encabezados"=>array(
					"Fecha"=>1,
					"Empleado"=>2,
					"Monto"=>1,
					"Concepto"=>3,
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
					"scripts/empleadosBono.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function EmpleadosBonoMostrar(){
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
			0 => 'empleadoBono.fechaRegistroEmpleadoBono',
			1 => 'empleado.nombreEmpleado',
			2 => 'empleado.apellidoEmpleado',
			3 => 'empleadoBono.descripcionEmpleadoBono'
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
		$condicion = array('empleadoBono.idSucursalEmpleadoBono' => $sucursal,'empleadoBono.estadoEmpleadoBono!=' => 'Borrado');
		$joins = array(array('tabla' => 'empleado', 'condicion' => 'empleado.idEmpleado = empleadoBono.idEmpleadoEmpleadoBono'));
		$EmpleadosBono = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$joins);
		
		//Lectura de datos de la base para mostrar en el datatabla
		if ($EmpleadosBono!= 0){
			$datosMostrar = array();
			foreach ($EmpleadosBono as $EmpleadoBono) {
                //EXTRAER PERIODO EN LETRAS
				$condicionDatos = array('idPeriodoPlanilla' => $EmpleadoBono->idPeriodoEmpleadoBono);
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
				
                $estadoEmpleadoBono = $EmpleadoBono->estadoEmpleadoBono;
                if ($estadoEmpleadoBono == 'Activo') {
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

                $funcion = "EmpleadosBonoEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($EmpleadoBono->idEmpleadoBono)."' ><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "EmpleadosBonoCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idEmpleadoBono=" . md5($EmpleadoBono->idEmpleadoBono) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "EmpleadosBonoEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idEmpleadoBono=" . md5($EmpleadoBono->idEmpleadoBono) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";

				//FECHA EN FORMATO d-m-Y
				$fechaRegistro = date_create($EmpleadoBono->fechaRegistroEmpleadoBono);

                $datosMostrar[] = array(
					date_format($fechaRegistro, 'd-m-Y'),
                    $EmpleadoBono->nombreEmpleado." ".$EmpleadoBono->apellidoEmpleado,
                    $EmpleadoBono->montoEmpleadoBono,
                    $EmpleadoBono->descripcionEmpleadoBono,
                    $periodoVigente,
                    $EmpleadoBono->aplicadoEmpleadoBono,
                    $estadoSpan,
                    $menuOpciones
                );
            }
			$totalEmpleadosBono = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalEmpleadosBono,
				"recordsFiltered" => $totalEmpleadosBono,
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

	function EmpleadosBonoAgregar(){
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
	
				$titulo = "Agregar Bono del Empleado";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fas fa-plus-circle",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"periodoVigente" => $periodoVigente,
                    "idPeriodoPlanilla" => $datosPeriodoPlanilla ? $datosPeriodoPlanilla->idPeriodoPlanilla : ""
				);
				$extras = array(
					'css' => array(
						"vendors/plugins/TypeAhead/typeahead.css"
					),
					'js' => array(
						"scripts/empleadosBono.js",
						"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
					),
				);
				GblPlantilla("empleadosBono/EmpleadoBonoAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalEmpleadoBono = $this->session->idSucursal;
				$idEmpleadoEmpleadoBono = $this->input->post("idEmpleadoEmpleadoBono");
				$montoEmpleadoBono = $this->input->post("montoEmpleadoBono");
				$descripcionEmpleadoBono = $this->input->post("descripcionEmpleadoBono");
				$idPeriodoEmpleadoBono = $this->input->post("idPeriodoEmpleadoBono");

				$condicionExiste = array(
					'idEmpleadoEmpleadoBono' => $idEmpleadoEmpleadoBono,
					'idPeriodoEmpleadoBono' => $idPeriodoEmpleadoBono,
					'idSucursalEmpleadoBono'=>$idSucursalEmpleadoBono
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if(true){
					$datosEmpleadosBono  = array(
						"idEmpleadoEmpleadoBono"=>$idEmpleadoEmpleadoBono,
						"montoEmpleadoBono"=>$montoEmpleadoBono,
						"descripcionEmpleadoBono"=>$descripcionEmpleadoBono,
						"idPeriodoEmpleadoBono"=>$idPeriodoEmpleadoBono,
						"fechaRegistroEmpleadoBono"=> date('Y-m-d'),
						"aplicadoEmpleadoBono"=>"NO",
						"estadoEmpleadoBono"=> 'Activo',
						"aleatorioEmpleadoBono" => uniqid(),
						"idSucursalEmpleadoBono"=>$idSucursalEmpleadoBono
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosEmpleadosBono);
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

	function EmpleadosBonoEditar($idEmpleadoBono=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idEmpleadoBono)' => $idEmpleadoBono);
				$datosEmpleadosBono = TraerUnDato($this->tabla, $condicionDatos);
				if($datosEmpleadosBono !== false && $idEmpleadoBono!=""){
					//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
					$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$datosEmpleadosBono->idEmpleadoEmpleadoBono));
					$empleadoEmpleadoBono = $datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado;

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
			
					$titulo = "Editar Bono del Empleado";
					$datosVista = array(
						"datosEmpleadosBono"=> $datosEmpleadosBono,
						"controlador" => $this->controlador,
						"idEmpleadoBono" => $idEmpleadoBono,
						"titulo" => $titulo,
						"proceso" => "Editar",
                        "icono" => "fas fa-plus-circle",
						"periodoVigente" => $periodoVigente,
                    	"idPeriodoPlanilla" => $datosPeriodoPlanilla->idPeriodoPlanilla,
						"empleadoEmpleadoBono" => $empleadoEmpleadoBono
					);
					$extras = array(
						'css' => array(
							"vendors/plugins/TypeAhead/typeahead.css"
						),
						'js' => array(
							"scripts/empleadosBono.js",
							"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
						),
					);
					GblPlantilla("empleadosBono/EmpleadoBonoEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idEmpleadoBono = $this->input->post("idEmpleadoBono");
				$idEmpleadoEmpleadoBono = $this->input->post("idEmpleadoEmpleadoBono");
				$idSucursalEmpleadoBono = $this->session->idSucursal;
				$montoEmpleadoBono = $this->input->post("montoEmpleadoBono");
				$descripcionEmpleadoBono = $this->input->post("descripcionEmpleadoBono");
				$idPeriodoEmpleadoBono = $this->input->post("idPeriodoEmpleadoBono");
				
				$condicionExiste = array(
					'idEmpleadoEmpleadoBono' => $idEmpleadoEmpleadoBono,
					'idPeriodoEmpleadoBono' => $idPeriodoEmpleadoBono,
					'idSucursalEmpleadoBono'=>$idSucursalEmpleadoBono
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if(true){
					$datosEmpleadosBono = array(
						"idEmpleadoEmpleadoBono"=>$idEmpleadoEmpleadoBono,
						"montoEmpleadoBono"=>$montoEmpleadoBono,
						"descripcionEmpleadoBono"=>$descripcionEmpleadoBono,
						"idPeriodoEmpleadoBono"=>$idPeriodoEmpleadoBono,
						"aleatorioEmpleadoBono" => uniqid(),
						"idSucursalEmpleadoBono"=>$idSucursalEmpleadoBono
					);
					IniciarTransaccion();
					$condicion = array("md5(idEmpleadoBono)" => $idEmpleadoBono);
					$editar = EditarDatos($this->tabla,$datosEmpleadosBono,$condicion);
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

	function EmpleadosBonoCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleadoBono = $this->input->post("idEmpleadoBono");
                $condicionDatos = array(
                    'md5(idEmpleadoBono)' => $idEmpleadoBono,
                    'estadoEmpleadoBono' => 'Activo',
                );
                $activoEmpleadoBono = ExistenDatos($this->tabla, $condicionDatos);

                ($activoEmpleadoBono == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosEmpleadoBono = array(
                    "estadoEmpleadoBono" => $nuevoEstado
                );
                $condicion = array("md5(idEmpleadoBono)" => $idEmpleadoBono);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleadoBono, $condicion);
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
    
    function EmpleadosBonoEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idEmpleadoBono = $this->input->post("idEmpleadoBono");
                $datosEmpleadoBono = array(
                    "estadoEmpleadoBono" => 'Borrado'
                );
                $condicion = array("md5(idEmpleadoBono)" => $idEmpleadoBono);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosEmpleadoBono, $condicion);
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

	function EmpleadosBonoAutocompleteEmpleado()
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
/* End of file EmpleadosBono.php */
