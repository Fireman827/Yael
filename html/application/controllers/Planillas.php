<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Planillas extends CI_Controller {

	private $tabla = "planilla";
	private $controlador = "Planillas";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Planillas";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-file-invoice-dollar",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'PlanillasAgregar',
						'txt' => 'Generar Planilla',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
						'id' => 'PlanillasGenerar'
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Periodo"=>2,
                    "Empleados"=>1,
                    "Monto"=>1,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/planillas.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function PlanillasMostrar(){
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
			0 => 'idPeriodoPlanilla',
			1 => 'desdePeriodoPlanilla',
			2 => 'hastaPeriodoPlanilla'
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
		$condicion = array('idSucursalPeriodoPlanilla' => $sucursal);
		$Planillas = TraerDatosTabla("periodoPlanilla",$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
		
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Planillas!= 0){
			$datosMostrar = array();
			foreach ($Planillas as $Planilla) {
				$periodoVigente = "";
				
                list($a,$m,$d) = explode("-", $Planilla->desdePeriodoPlanilla);
                list($a1,$m1,$d1) = explode("-", $Planilla->hastaPeriodoPlanilla);
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
                
                $menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

                $funcion = "PlanillasDetalle";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($Planilla->idPeriodoPlanilla)."' ><i class='far fa-eye' ></i> Detalle</a>";
                }
				$funcion = "PlanillasImprimir";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<button class='dropdown-item ".$funcion."' idPeriodo='".md5($Planilla->idPeriodoPlanilla)."' type='button' ><i class='fa fa-print' ></i> Planilla </button>";
                }
				$funcion = "PlanillasBoletasImprimir";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<button class='dropdown-item ".$funcion."' idPeriodo='".md5($Planilla->idPeriodoPlanilla)."' type='button' ><i class='fa fa-print' ></i> Boletas </button>";
                }
                $funcion = "PlanillasEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idPeriodoPlanilla=" . md5($Planilla->idPeriodoPlanilla) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
				$noMenuOpciones = true;
                $empleados = TraerUnDatoIndividual("planilla","count(idEmpleadoPlanilla) as emp",array("idPeriodoPlanilla" => $Planilla->idPeriodoPlanilla))[0]["emp"];
				if($empleados==0){ $empleados = "<span class='badge badge-warning font-bold'>No Generada<span>"; $noMenuOpciones = false; }
                $total = TraerUnDatoIndividual("planilla","SUM(liquidoPlanilla) as tot",array("idPeriodoPlanilla" => $Planilla->idPeriodoPlanilla))[0]["tot"];
                if($total==0){ $total = "<span class='badge badge-info font-bold'>No Generada<span>"; $noMenuOpciones = false; }
				if($noMenuOpciones==false){ $menuOpciones = ""; }
                $datosMostrar[] = array(
                    $Planilla->idPeriodoPlanilla,
                    $periodoVigente,
                    $empleados,
                    $total,
                    $menuOpciones
                );
            }
			$totalPlanillas = TraerTotalDatos("periodoPlanilla",$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalPlanillas,
				"recordsFiltered" => $totalPlanillas,
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

    function PlanillasDetalle($idPeriodoPlanilla=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Planillas por Periodo";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-file-invoice-dollar",
				"botones" => array(
					array(
						"icono"=> "fas fa-undo",
						'controlador' => $this->controlador,
						'url' => 'Planillas',
						'txt' => 'Regresar',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => true,
						'id' => $idPeriodoPlanilla
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Empleado"=>2,
                    "Sueldo"=>1,
                    "Bonos"=>1,
                    "Isss"=>1,
                    "Afp"=>1,
                    "Renta"=>1,
                    "Descuento"=>1,
                    "Liquido"=>1,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/planillasDetalle.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

    function PlanillasDetalleMostrar($idPeriodoPlanilla=""){
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
			0 => 'planilla.idPlanilla',
			1 => 'empleado.nombreEmpleado',
			2 => 'empleado.apellidoEmpleado'
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])){
			$ordenCampos = null;
		} else {
			$ordenCampos = $columnasValidas[$col];
		}
		// Orden de Campo
		$ordenCampos = "planilla.idPlanilla";
		$ordenDireccion = "DESC";
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$this->session->idSucursal = $sucursal;
		$condicion = array('planilla.idSucursalPlanilla' => $sucursal,'planilla.estadoPlanilla!=' => 'Borrado','md5(planilla.idPeriodoPlanilla)' => $idPeriodoPlanilla);
		$joins = array(array('tabla' => 'empleado', 'condicion' => 'empleado.idEmpleado = planilla.idEmpleadoPlanilla'));
		$PlanillasDetalle = TraerDatosTablaJoin("planilla",$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$joins);
		
		//Lectura de datos de la base para mostrar en el datatabla
		if ($PlanillasDetalle!= 0){
			$datosMostrar = array();
			foreach ($PlanillasDetalle as $PlanillaDetalle) {
                
                $menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

                $funcion = "PlanillasBoletaImprimir";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<button class='dropdown-item ".$funcion."' idPlanilla='".md5($PlanillaDetalle->idPlanilla)."' type='button' ><i class='fa fa-print' ></i> Boleta </button>";
                }

                $menuOpciones .= "</div></div>";
                //$empleados = TraerUnDatoIndividual("planilla","count(idEmpleadoPlanilla) as emp",array("idPeriodoPlanilla" => $Planilla->idPeriodoPlanilla))[0]["emp"];
                //$total = TraerUnDatoIndividual("planilla","SUM(liquidoPlanilla) as tot",array("idPeriodoPlanilla" => $Planilla->idPeriodoPlanilla))[0]["tot"];
                
                $datosMostrar[] = array(
                    $PlanillaDetalle->idPeriodoPlanilla,
                    $PlanillaDetalle->nombreEmpleado." ".$PlanillaDetalle->apellidoEmpleado,
                    $PlanillaDetalle->sueldoPlanilla,
                    $PlanillaDetalle->abonosPlanilla,
                    $PlanillaDetalle->isssPlanilla,
                    $PlanillaDetalle->afpPlanilla,
                    $PlanillaDetalle->rentaPlanilla,
                    $PlanillaDetalle->descuentosPlanilla,
                    $PlanillaDetalle->liquidoPlanilla,
                    $menuOpciones
                );
            }
			$totalPlanillasDetalle = TraerTotalDatos("planilla",$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalPlanillasDetalle,
				"recordsFiltered" => $totalPlanillasDetalle,
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

	function PlanillasGenerar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Agregar Planilla";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-file-invoice-dollar",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar"
				);
				$extras = array(
					'css' => array(
						"vendors/plugins/TypeAhead/typeahead.css"
					),
					'js' => array(
						"scripts/planillas.js",
						"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
					),
				);
				GblPlantilla("planillas/PlanillaAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                
				$datosPeriodoPlanillas = TraerUnDato('periodoPlanilla',array('vencidoPeriodoPlanilla' => 'Vigente'));

                $id_periodo = $datosPeriodoPlanillas->idPeriodoPlanilla;
                $mes = $datosPeriodoPlanillas->mesPeriodoPlanilla;
                $anio = $datosPeriodoPlanillas->anioPeriodoPlanilla;
                $desde = $datosPeriodoPlanillas->desdePeriodoPlanilla;
                $hasta = $datosPeriodoPlanillas->hastaPeriodoPlanilla;

                $existePlanilla = ExistenDatos('planilla', array('idPeriodoPlanilla' => $id_periodo));
                if($existePlanilla){
                    //La acción no se pudo realizar porque ya existe un registro con los mismos datos
					$datosRespuesta["codigo"] = 400;  
                } else {
                    $fecha = date("Y-m-d");
                    $datosCotizacion = TraerUnDato('cotizacion', array('idCotizacion' => '1'));
                    $pisss = round(($datosCotizacion->isssCotizacion/100), 4);
                    $pafp = round(($datosCotizacion->afpCotizacion/100), 4);
                    $tisss = $datosCotizacion->techoIsssCotizacion;

                    $datosEmpleado = TraerDatos('empleado', array('estadoEmpleado' => 'Activo','fechaContratacionEmpleado <=' => $desde,'fechaCeseEmpleado >=' => $hasta));
                    //$sql_emp = _query("SELECT id_empleado_reloj as id_empleado, id_depto_admin as id_departamento, id_sucursal, salario_base, renta, isss, afp, afiliado_afp FROM empleado WHERE inactivo='0' AND fecha_cese>'$desde' AND fecha_contratacion<'$desde'");
                    IniciarTransaccion();
                    foreach($datosEmpleado as $Empleado){
                        $id_empleado = $Empleado->idEmpleado;
                        $id_departamento = $Empleado->departamentoEmpleado;
                        $id_sucursal = $Empleado->idSucursalEmpleado;
                        $salario_base = $Empleado->salarioBaseEmpleado;
                        $salario_base_min = round(($salario_base/60),4);
 
                        $renta = $Empleado->rentaEmpleado;
                        $isss = $Empleado->isssEmpleado;
                        $afp = $Empleado->afpEmpleado;
                        $afiliado_afp = $Empleado->afiliadoAfpEmpleado;
                        $tablaDescuento = "empleadoDescuento";
                        $tablaBono = "empleadoBono";
                        $tablaDescuentoDetalle = "empleadoDescuentoDetalle";
                        
                        $descuentos = TraerUnDatoIndividual("empleadoDescuento","SUM(montoEmpleadoDescuento) as descuento",array('idEmpleadoEmpleadoDescuento' => $id_empleado,'idPeriodoEmpleadoDescuento' => $id_periodo,'aplicadoEmpleadoDescuento' => 'NO'))[0]["descuento"];
						if($descuentos==null) $descuentos = 0;
                        $descuentos_det = TraerUnDatoIndividual("empleadoDescuentoDetalle","SUM(montoEmpleadoDescuentoDetalle) as monto",array('idEmpleadoEmpleadoDescuentoDetalle' => $id_empleado,'idPeriodoEmpleadoDescuentoDetalle' => $id_periodo,'aplicadoEmpleadoDescuentoDetalle' => 'NO'))[0]["monto"];
						if($descuentos_det==null) $descuentos_det = 0;
                        $abonos = TraerUnDatoIndividual("empleadoBono","SUM(montoEmpleadoBono) as abono",array('idEmpleadoEmpleadoBono' => $id_empleado,'idPeriodoEmpleadoBono' => $id_periodo,'aplicadoEmpleadoBono' => 'NO'))[0]["abono"];
						if($abonos==null) $abonos = 0; 
                        $parcial = $salario_base;

                        //$sql_plan  = _query("SELECT max(correlativo) as correlativo FROM planilla");
                        //$row_plan = _fetch_array($sql_plan);
                        //$correlativo = $row_plan["correlativo"]+1;
                        $correlativo = TraerMaxValor('planilla','correlativoPlanilla','');
						$correlativo = $correlativo+1;
                        $afp_calc = 0;
                        $isss_calc = 0;
                        $renta = 0;
                        if($afp){
                            $afp_calc = round(($parcial * $pafp),2);
                        }
                        if($isss){
                            if($parcial<=$tisss){
                                $isss_calc = round(($parcial * $pisss),4);
                            } else {
                                $isss_calc = round(($tisss * $pisss),4);
                            }
                        }
                        $liquido = $parcial - $isss_calc - $afp_calc;
                        if($renta){
                            $datosRenta = TraerUnDato('tramoRenta', array('desdeTramoRenta >=' => $liquido,'hastaTramoRenta <=' => $liquido));
                            //$sql_tramo = _query("SELECT * FROM renta WHERE '$liquido' BETWEEN desde AND hasta");
                            //$datos_tramo = _fetch_array($sql_tramo);
                            $porcentaje = $datosRenta->porcentajeTramoRenta;
                            $exceso = $datosRenta->excesoTramoRenta;
                            $cuota = $datosRenta->cuotaTramoRenta;
                            $renta = (($liquido-$exceso) * $porcentaje) + $cuota;
                        }
                        $liquido = $liquido - $renta;

                        $liquido = $liquido - $descuentos - $descuentos_det;
                        $liquido += $abonos;
                        $descuento =  $descuentos+$descuentos_det;
                        $tabla = "planilla";
                        $datosPlanillas = array(
                            'idEmpleadoPlanilla' => $id_empleado,
                            'correlativoPlanilla' => $correlativo,
                            'idDepartamentoPlanilla' => $id_departamento,
                            'idSucursalPlanilla' => $id_sucursal,
                            'sueldoPlanilla' => $parcial,
                            'isssPlanilla' => $isss_calc,
                            'afpPlanilla' => $afp_calc,
                            'rentaPlanilla' => $renta,
                            'abonosPlanilla' => $abonos,
                            'descuentosPlanilla' => $descuento,
                            'liquidoPlanilla' => $liquido,
                            'idPeriodoPlanilla' => $id_periodo,
                            'fechaRegistroPlanilla' => $fecha,
                            'horasTrabajadasPlanilla' => 0,
                            'minutosTrabajadosPlanilla' => 0,
                            'horasExtraPlanilla' => 0,
                            'minutosExtraPlanilla' => 0,
                        );
                        $guardar = GuardarDatos('planilla',$datosPlanillas);
                        if($guardar){
                            //$datosVarios = array('aplicado' => 1);
                           // $where_da = "id_empleado='$id_empleado' AND id_periodo='$id_periodo' AND aplicado='0'";
							//$nivel = 0;
							$error= false;
                           if($descuento > 0.00){ 
								$editar1 = EditarDatos($tablaDescuento,	array('aplicadoEmpleadoDescuento'=>'SI'),array('idEmpleadoEmpleadoDescuento'=>$id_empleado,'idPeriodoEmpleadoDescuento'=>$id_periodo,'aplicadoEmpleadoDescuento'=>'NO'));
								(!$editar1) ? ($error = true):'';
								(!$editar1) ? ($codigo = 501):'';
								$editar2 = EditarDatos($tablaBono,array('aplicadoEmpleadoBono'=>'SI'),array('idEmpleadoEmpleadoBono'=>$id_empleado,'idPeriodoEmpleadoBono'=>$id_periodo,'aplicadoEmpleadoBono'=>'NO'));
								(!$editar2) ? ($error = true):'';
								(!$editar2) ? ($codigo = 502):'';
								$editar3 = EditarDatos($tablaDescuentoDetalle,array('aplicadoEmpleadoDescuentoDetalle'=>'SI'),array('idEmpleadoEmpleadoDescuentoDetalle'=>$id_empleado,'idPeriodoEmpleadoDescuentoDetalle'=>$id_periodo,'aplicadoEmpleadoDescuentoDetalle'=>'NO'));
								(!$editar3) ? ($error = true):'';
								(!$editar3) ? ($codigo = 503):'';
							}
                            if(!$error){
                                //La acción se realizo con éxito						
                                EjecutarTransaccion();
                                $datosRespuesta["codigo"] = 200;
                            } else {
                                //La acción no pudo ser realizada
                                DeshacerTransaccion();
                                $datosRespuesta["codigo"] = $codigo;
                            }
                        } else {
                            //La acción no pudo ser realizada
						    DeshacerTransaccion();
						    $datosRespuesta["codigo"] = 402;
                        }
                    }
                }
				echo json_encode($datosRespuesta);
			}
		}
	}

	function PlanillasEditar($idPlanilla=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idPlanilla)' => $idPlanilla);
				$datosPlanillas = TraerUnDato($this->tabla, $condicionDatos);
				if($datosPlanillas !== false && $idPlanilla!=""){
					//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
					$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$datosPlanillas->idEmpleadoPlanilla));
					$empleadoPlanilla = $datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado;
			
					$titulo = "Editar Planilla";
					$datosVista = array(
						"datosPlanillas"=> $datosPlanillas,
						"controlador" => $this->controlador,
						"idPlanilla" => $idPlanilla,
						"titulo" => $titulo,
						"proceso" => "Editar",
                        "icono" => "fa fa-file-invoice-dollar",
						"empleadoPlanilla" => $empleadoPlanilla
					);
					$extras = array(
						'css' => array(
							"vendors/plugins/TypeAhead/typeahead.css"
						),
						'js' => array(
							"scripts/planillas.js",
							"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
						),
					);
					GblPlantilla("planillas/PlanillaEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idPlanilla = $this->input->post("idPlanilla");
				$idSucursalPlanilla = $this->session->idSucursal;
				$idEmpleadoPlanilla = $this->input->post("idEmpleadoPlanilla");
				$idPlanillaTipoPlanilla = $this->input->post("idPlanillaTipoPlanilla");
				$duiPlanilla = $this->input->post("duiPlanilla");
				$nitPlanilla = $this->input->post("nitPlanilla");
				$desdePlanilla = $this->input->post("desdePlanilla");
				$hastaPlanilla = $this->input->post("hastaPlanilla");
				$horarioPlanilla = $this->input->post("horarioPlanilla");
				
				$condicionExiste = array(
					'idEmpleadoPlanilla' => $idEmpleadoPlanilla,
					'idPlanillaTipoPlanilla' => $idPlanillaTipoPlanilla,
					'duiPlanilla' => $duiPlanilla,
					'nitPlanilla' => $nitPlanilla,
					'idSucursalPlanilla' => $idSucursalPlanilla,
					'md5(idPlanilla)!=' => $idPlanilla
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosPlanillas = array(
						"idEmpleadoPlanilla"=>$idEmpleadoPlanilla,
						"idPlanillaTipoPlanilla"=>$idPlanillaTipoPlanilla,
						"duiPlanilla"=>$duiPlanilla,
						"nitPlanilla"=>$nitPlanilla,
						"desdePlanilla"=>$desdePlanilla,
						"hastaPlanilla"=>$hastaPlanilla,
						"horarioPlanilla"=>$horarioPlanilla,
						"idSucursalPlanilla"=>$idSucursalPlanilla,
						"aleatorioPlanilla"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("md5(idPlanilla)" => $idPlanilla);
					$editar = EditarDatos($this->tabla,$datosPlanillas,$condicion);
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

	function PlanillasCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idPlanilla = $this->input->post("idPlanilla");
                $condicionDatos = array(
                    'md5(idPlanilla)' => $idPlanilla,
                    'estadoPlanilla' => 'Activo',
                );
                $activoPlanilla = ExistenDatos($this->tabla, $condicionDatos);

                ($activoPlanilla == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosPlanilla = array(
                    "estadoPlanilla" => $nuevoEstado
                );
                $condicion = array("md5(idPlanilla)" => $idPlanilla);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosPlanilla, $condicion);
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
    
    function PlanillasEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idPeriodoPlanilla = $this->input->post("idPeriodoPlanilla");
                $datosPlanilla = array(
                    "estadoPlanilla" => 'Borrado'
                );
                $condicion = array("md5(idPeriodoPlanilla)" => $idPeriodoPlanilla);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosPlanilla, $condicion);
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

	function PlanillasBoletaImprimir($idPlanilla=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
		  GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$datosPlanilla = TraerUnDato($this->tabla, array('md5(idPlanilla)' => $idPlanilla));

			$id_periodo = $datosPlanilla->idPeriodoPlanilla;
			$datosPeriodo = TraerUnDato('periodoPlanilla', array('idPeriodoPlanilla'=>$id_periodo));
			$periodoVigente = "";
				
			list($a,$m,$d) = explode("-", $datosPeriodo->desdePeriodoPlanilla);
			list($a1,$m1,$d1) = explode("-", $datosPeriodo->hastaPeriodoPlanilla);
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

			$this->load->add_package_path(APPPATH . 'third_party/fpdf');
			$this->load->library('pdf');
			$this->fpdf = new Pdf();
			$this->fpdf->SetTopMargin(-5);
			$this->fpdf->SetLeftMargin(10);
			//Numeracion de paginas
			$this->fpdf->AliasNbPages();
			//Salto automatico de pagina margen de 20 mm
			$this->fpdf->SetAutoPageBreak(true,10);
			//Agrega la pagina a trabajar
			$this->fpdf->AddPage('L','LETTER',0);
			//Seteo de fuente Times New Roman 12
			$this->fpdf->SetFont('Helvetica','B',14);
			
			$aumentoX = 0;
			for($i=0;$i<=1;$i++){
				//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
				$id_empleado = $datosPlanilla->idEmpleadoPlanilla;
				$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$datosPlanilla->idEmpleadoPlanilla));
				$empleado = mb_strtoupper($datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado,'UTF-8');

				//CONSULTAR SUCURSAL
				$datosSucursal = TraerUnDato('sucursal',array('idSucursal' => $this->session->idSucursal));
				$sucursal = utf8_decode(mb_strtoupper($datosSucursal->nombreSucursal,'UTF-8'));
	
				//CONSULTA PARA TRAER LOS DATOS DEL DEPARTAMENTO AL QUE PERTENECE EL EMPLEADO
				///$datosDepartamento = TraerUnDato('empleadoDepartamento',array("idEmpleadoDepartamento"=>$datosPlanilla->idDepartamentoPlanilla));
				///$departamento = utf8_decode(mb_strtoupper($datosDepartamento->nombreDepartamento,'UTF-8'));

				$departamento = utf8_decode($datosEmpleado->departamentoEmpleado);
				$cargo = TraerUnDatoIndividual("cargo","nombreCargo",array('idCargo' => $datosEmpleado->idCargoEmpleado,'estadoCargo' => 'Activo'))[0]["nombreCargo"];;
				$salario = number_format($datosEmpleado->salarioBaseEmpleado,2,".","");
				$sueldo = number_format($datosPlanilla->sueldoPlanilla,2,".",",");
				$isss = number_format($datosPlanilla->isssPlanilla,2,".",",");
				$afp = number_format($datosPlanilla->afpPlanilla,2,".",",");
				$renta = number_format($datosPlanilla->rentaPlanilla,2,".",",");
				$abonos = number_format($datosPlanilla->abonosPlanilla,2,".",",");
				$descuentos = number_format($datosPlanilla->descuentosPlanilla,2,".",",");
				$liquido = number_format($datosPlanilla->liquidoPlanilla,2,".",",");
				$forma_pago = $datosEmpleado->formaPagoEmpleado;
				$hnm = $datosPlanilla->horasTrabajadasPlanilla;
				$mnm = $datosPlanilla->minutosTrabajadosPlanilla;
				$hxt = $datosPlanilla->horasExtraPlanilla;
				$mxt = $datosPlanilla->minutosExtraPlanilla;
				$correlativo = $datosPlanilla->correlativoPlanilla;
				$lineas = "----------------------------------------------------------------------------------------------------------------";
				$mm =0;
	
				//CONSULTAR BONOS
				$datosBonos = TraerDatos("empleadoBono",array('idEmpleadoEmpleadoBono' => $id_empleado,'idPeriodoEmpleadoBono' => $id_periodo,'aplicadoEmpleadoBono' => 'SI'));
				if($datosBonos!=false) $nbono = true; else $nbono = false;
	
				//CONSULTAR DESCUENTOS
				$datosDescuentos = TraerDatos("empleadoDescuento",array('idEmpleadoEmpleadoDescuento' => $id_empleado,'idPeriodoEmpleadoDescuento' => $id_periodo,'tipoEmpleadoDescuento' => 'DESCUENTO','aplicadoEmpleadoDescuento' => 'SI'));
				if($datosDescuentos!=false) $ndesc = true; else $ndesc = false;
				
				//CONSULTAR ANTICIPOS
				$datosAnticipos = TraerDatos("empleadoDescuento",array('idEmpleadoEmpleadoDescuento' => $id_empleado,'idPeriodoEmpleadoDescuento' => $id_periodo,'tipoEmpleadoDescuento' => 'ANTICIPO','aplicadoEmpleadoDescuento' => 'SI'));
				if($datosAnticipos!=false) $nant = true; else $nant = false;
				
				//CONSULTAR DETALLE DESCUENTOS
				$condicion = array('empleadoDescuentoDetalle.idEmpleadoEmpleadoDescuentoDetalle' => $id_empleado,'empleadoDescuentoDetalle.idPeriodoEmpleadoDescuentoDetalle' => $id_periodo,'empleadoDescuentoDetalle.aplicadoEmpleadoDescuentoDetalle' => 'SI');
				$join = array(
					array('tabla' => 'empleadoDescuentoCuota', 'condicion' => 'empleadoDescuentoCuota.idEmpleadoDescuentoCuota = empleadoDescuentoDetalle.idDescuentoEmpleadoDescuentoDetalle'),
					array('tabla' => 'empleadoInstitucionFinanciera','condicion' => 'empleadoDescuentoCuota.idInstitucionEmpleadoDescuentoCuota = empleadoInstitucionFinanciera.idInstitucionFinanciera')
				);
				$ordenCampos = "";
				$datosDescuentosDetalle = TraerDatosJoin("empleadoDescuentoDetalle",$condicion,$ordenCampos,$join);
				if($datosDescuentosDetalle!=false) $ndesc_det = true; else $ndesc_det = false;
		
				$setX = 10 + $aumentoX;
  				$setY = 5 ;
				$path = base_url("vendors/core/img/dms.png");
				$this->fpdf->Image($path,$setX,$setY,30,25);
				$setX = 0 + $aumentoX;
  				$setY = 15 ;
				//$this->fpdf->Cell(120,6,utf8_decode("DIGITALS POS"),0,1,"C");
				$this->fpdf->SetFont('Helvetica', 'B', 10);
				$this->fpdf->SetXY($setX, $setY);
				$this->fpdf->Cell(130,6,utf8_decode("BOLETA DE PAGO"),0,1,"C");
				$this->fpdf->SetXY($setX+20, $setY);
				$this->fpdf->Cell(90,6,utf8_decode("N° ".$correlativo),0,1,'R');
		
				$this->fpdf->Ln(15);
	
				$setX = 10 + $aumentoX;
				$setY = 35;
	
				$this->fpdf->SetFont('Helvetica','',8);
				$this->fpdf->SetXY($setX, $setY);
				$this->fpdf->Cell(85,5,"EMPLEADO: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+5);
				$this->fpdf->Cell(85,5,"CARGO: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+10);
				$this->fpdf->Cell(85,5,"SUCURSAL: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+15);
				$this->fpdf->Cell(85,5,"DEPARTAMENTO: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+20);
				$this->fpdf->Cell(85,5,"FORMA DE PAGO: ",0,1,'L');
	
				/*** * ***/
				$setX = 48 + $aumentoX;
				$this->fpdf->SetXY($setX, $setY);
				$this->fpdf->Cell(85,5,$empleado,0,1,'L');
				$this->fpdf->SetXY($setX, $setY+5);
				$this->fpdf->Cell(85,5,$cargo,0,1,'L');
				$this->fpdf->SetXY($setX, $setY+10);
				$this->fpdf->Cell(85,5,$sucursal,0,1,'L');
				$this->fpdf->SetXY($setX, $setY+15);
				$this->fpdf->Cell(85,5,$departamento,0,1,'L');
				$this->fpdf->SetXY($setX, $setY+20);
				$this->fpdf->Cell(85,5,$forma_pago,0,1,'L');
	
				$setX = 10 + $aumentoX;
				$this->fpdf->SetXY($setX, $setY+25);
				$this->fpdf->Cell(120,5,$lineas,0,1,'L');
	
				$setX = 10 + $aumentoX;
				$setY += 25;
				$this->fpdf->SetXY($setX, $setY+5);
				$this->fpdf->Cell(85,5,"SUELDO: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+10);
				$this->fpdf->Cell(85,5,"BONIFICACIONES: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+15);
				$this->fpdf->Cell(85,5,"ISSS: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+20);
				$this->fpdf->Cell(85,5,"AFP: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+25);
				$this->fpdf->Cell(85,5,"RENTA: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+30);
				$this->fpdf->Cell(85,5,"DESCUENTOS: ",0,1,'L');
				$this->fpdf->SetXY($setX, $setY+35);
				$this->fpdf->Cell(85,5,"LIQUIDO: ",0,1,'L');
	
				$this->fpdf->SetXY($setX+60, $setY+5);
				$this->fpdf->MultiCell(60,5,$periodoVigente,0,'J',0);
	
				$setX = 45 + $aumentoX;
				$this->fpdf->SetXY($setX, $setY+5);
				$this->fpdf->Cell(20,5,$sueldo,0,1,'R');
				$this->fpdf->SetXY($setX, $setY+10);
				$this->fpdf->Cell(20,5,"+ ".$abonos,0,1,'R');
				$this->fpdf->SetXY($setX, $setY+15);
				$this->fpdf->Cell(20,5,"- ".$isss,0,1,'R');
				$this->fpdf->SetXY($setX, $setY+20);
				$this->fpdf->Cell(20,5,"- ".$afp,0,1,'R');
				$this->fpdf->SetXY($setX, $setY+25);
				$this->fpdf->Cell(20,5,"- ".$renta,0,1,'R');
				$this->fpdf->SetXY($setX, $setY+30);
				$this->fpdf->Cell(20,5,"- ".$descuentos,"B",1,'R');
				$this->fpdf->SetXY($setX, $setY+35);
				$this->fpdf->Cell(20,5,"= ".$liquido,0,1,'R');
	
				$setX = 10 + $aumentoX;
				$liquido = str_replace(",","",$liquido);
				list($entero, $decimal) = explode(".",$liquido);
				$text = mb_strtoupper(num2letras($entero)." con ".$decimal."/100",'UTF-8');
				$this->fpdf->SetXY($setX, $setY+45);
				$this->fpdf->MultiCell(125,5,$text,0,'J',0);
	
				$this->fpdf->SetXY($setX, $setY+50);
				$this->fpdf->Cell(120,5,$lineas,0,1,'L');
	
				$setY += 55 ;
				$setX = 0 + $aumentoX;
				//BONOS
				if($nbono){
					$setX = 10;
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(120,5,"DETALLE DE BONIFICACIONES",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
					$this->fpdf->SetXY($setX+15, $setY+5);
					$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
					$this->fpdf->SetXY($setX+100, $setY+5);
					$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
					$mm = 5;
					$setY += 5;
					$k = 1;
					$tot = 0;
					foreach ($datosBonos as $Bono) {
						$concepto = $Bono->descripcionEmpleadoBono;
						$tot += $Bono->montoEmpleadoBono;
						$monto = number_format($Bono->montoEmpleadoBono,2,".",",");
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(15,5,$k,0,1,'C');
						$this->fpdf->SetXY($setX+15, $setY+$mm);
						$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,$monto,0,1,'R');
						$k++;
						$mm+=5;
					}
					$this->fpdf->SetXY($setX, $setY+$mm);
					$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
					$this->fpdf->SetXY($setX+100, $setY+$mm);
					$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
					$setY += $mm+10;
					$setX = 0 + $aumentoX;
				}
				//ANTICIPOS
				if($nant){
					$setX = 10;
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(120,5,"DETALLE DE ANTICIPOS",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
					$this->fpdf->SetXY($setX+15, $setY+5);
					$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
					$this->fpdf->SetXY($setX+100, $setY+5);
					$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
					$mm = 5;
					$setY += 5;
					$k = 1;
					$tot = 0;
					foreach ($datosAnticipos as $Anticipo) {
						$concepto = $Anticipo->descripcionEmpleadoDescuento;
						$tot += $Anticipo->montoEmpleadoDescuento;
						$monto = number_format($Anticipo->montoEmpleadoDescuento,2,".",",");
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(15,5,$k,0,1,'C');
						$this->fpdf->SetXY($setX+15, $setY+$mm);
						$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,$monto,0,1,'R');
						$k++;
						$mm+=5;
					}
					$this->fpdf->SetXY($setX, $setY+$mm);
					$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
					$this->fpdf->SetXY($setX+100, $setY+$mm);
					$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
					$setY += $mm+10;
					$setX = 0 + $aumentoX;
				}
				//DESCUENTOS
				if($ndesc){
					$setX = 10;
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(120,5,"DETALLE DE DESCUENTOS",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
					$this->fpdf->SetXY($setX+15, $setY+5);
					$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
					$this->fpdf->SetXY($setX+100, $setY+5);
					$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
					$mm = 5;
					$setY += 5;
					$k = 1;
					$tot = 0;
					foreach ($datosDescuentos as $Descuento) {
						$concepto = $Descuento->descripcionEmpleadoDescuento;
						$tot += $Descuento->montoEmpleadoDescuento;
						$monto = number_format($Descuento->montoEmpleadoDescuento,2,".",",");
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(15,5,$k,0,1,'C');
						$this->fpdf->SetXY($setX+15, $setY+$mm);
						$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,$monto,0,1,'R');
						$k++;
						$mm+=5;
					}
					$this->fpdf->SetXY($setX, $setY+$mm);
					$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
					$this->fpdf->SetXY($setX+100, $setY+$mm);
					$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
					$setY += $mm+10;
					$setX = 0 + $aumentoX;
				}
	
				//DETALLE DESCUENTO
				if($ndesc_det){
					$setX = 10 + $aumentoX;
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(120,5,"ORDENES DE DESCUENTO",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
					$this->fpdf->SetXY($setX+15, $setY+5);
					$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
					$this->fpdf->SetXY($setX+100, $setY+5);
					$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
					$mm = 5;
					$setY += 5;
					$k = 1;
					$tot = 0;
					foreach ($datosDescuentosDetalle as $DescuentoDetalle) {
						$idde = $DescuentoDetalle->idEmpleadoDescuentoCuota;						
						$nsalp = TraerUnDatoIndividual("empleadoDescuentoDetalle","SUM(montoEmpleadoDescuentoDetalle) as total",array('idDescuentoEmpleadoDescuentoDetalle' => $idde,'idPeriodoEmpleadoDescuentoDetalle' => $id_periodo,'aplicadoEmpleadoDescuentoDetalle' => 'NO'))[0]["total"];
						$nombrei = $DescuentoDetalle->nombreInstitucionFinanciera;
						$concepto = $DescuentoDetalle->descripcionEmpleadoDescuentoCuota;
						$tot += $DescuentoDetalle->montoEmpleadoDescuentoDetalle;
						$monto = number_format($DescuentoDetalle->montoEmpleadoDescuentoDetalle,2,".",",");
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(15,5,$k,0,1,'C');
						$this->fpdf->SetXY($setX+15, $setY+$mm);
						$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,$monto,0,1,'R');
						$k++;
						$mm+=5;
						$this->fpdf->SetXY($setX+15, $setY+$mm);
						$this->fpdf->Cell(90,5,"NUEVO SALDO: $".number_format($nsalp,2),0,1,'L');
						$k++;
						$mm+=5;
					}
					$this->fpdf->SetXY($setX, $setY+$mm);
					$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
					$this->fpdf->SetXY($setX+100, $setY+$mm);
					$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
				}

				$setX = 20 + $aumentoX;
				$setY = 180;

				$this->fpdf->SetXY($setX, $setY);
				$this->fpdf->Cell(20,5,"F.__________________________",0,1,'C');
				$setX = 80 + $aumentoX; 
				$this->fpdf->SetXY($setX, $setY);
				$this->fpdf->Cell(20,5,"F.__________________________",0,1,'C');
	
				$setX = 20 + $aumentoX;
				$setY = 185;

				$this->fpdf->SetXY($setX, $setY);
				$this->fpdf->Cell(20,5,"PATRONO",0,1,'C');
				$setX = 80 + $aumentoX; 
				$this->fpdf->SetXY($setX, $setY);
				$this->fpdf->Cell(20,5,"EMPLEADO",0,1,'C');
				$aumentoX= 140;
			}
		  	ob_clean();
			$this->fpdf->Output("boleta_pago.pdf", "I");
		}
	}

	function PlanillasBoletasImprimir($idPeriodo=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
		  GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$datosPlanilla = TraerDatos($this->tabla, array('md5(idPeriodoPlanilla)' => $idPeriodo));

			$datosPeriodo = TraerUnDato('periodoPlanilla', array('idPeriodoPlanilla'=>$idPeriodo));
			$id_periodo = $datosPeriodo->idPeriodoPlanilla;
			$periodoVigente = "";
				
			list($a,$m,$d) = explode("-", $datosPeriodo->desdePeriodoPlanilla);
			list($a1,$m1,$d1) = explode("-", $datosPeriodo->hastaPeriodoPlanilla);
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

			$this->load->add_package_path(APPPATH.'third_party/fpdf');
			$this->load->library('pdf');
			$this->fpdf = new Pdf();
			$this->fpdf->SetTopMargin(-5);
			$this->fpdf->SetLeftMargin(10);
			//Numeracion de paginas
			$this->fpdf->AliasNbPages();
			//Salto automatico de pagina margen de 20 mm
			$this->fpdf->SetAutoPageBreak(true,10);

			//Seteo de fuente Times New Roman 12
			//$this->fpdf->SetFont('Helvetica','B',14);
			
			foreach ($datosPlanilla as $Planilla) {
				//Agrega la pagina a trabajar
				$this->fpdf->AddPage('L','LETTER',0);
				$aumentoX = 0;
				for($i=0;$i<=1;$i++){
					//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
					$id_empleado = $Planilla->idEmpleadoPlanilla;
					$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$Planilla->idEmpleadoPlanilla));
					$empleado = mb_strtoupper($datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado,'UTF-8');

					//CONSULTAR SUCURSAL
					$datosSucursal = TraerUnDato('sucursal',array('idSucursal' => $this->session->idSucursal));
					$sucursal = utf8_decode(mb_strtoupper($datosSucursal->nombreSucursal,'UTF-8'));
		
					//CONSULTA PARA TRAER LOS DATOS DEL DEPARTAMENTO AL QUE PERTENECE EL EMPLEADO
					///$datosDepartamento = TraerUnDato('empleadoDepartamento',array("idEmpleadoDepartamento"=>$Planilla->idDepartamentoPlanilla));
					///$departamento = utf8_decode(mb_strtoupper($datosDepartamento->nombreDepartamento,'UTF-8'));

					$departamento = utf8_decode($datosEmpleado->departamentoEmpleado);
					$cargo = TraerUnDatoIndividual("cargo","nombreCargo",array('idCargo' => $datosEmpleado->idCargoEmpleado,'estadoCargo' => 'Activo'))[0]["nombreCargo"];;
					$salario = number_format($datosEmpleado->salarioBaseEmpleado,2,".","");
					$sueldo = number_format($Planilla->sueldoPlanilla,2,".",",");
					$isss = number_format($Planilla->isssPlanilla,2,".",",");
					$afp = number_format($Planilla->afpPlanilla,2,".",",");
					$renta = number_format($Planilla->rentaPlanilla,2,".",",");
					$abonos = number_format($Planilla->abonosPlanilla,2,".",",");
					$descuentos = number_format($Planilla->descuentosPlanilla,2,".",",");
					$liquido = number_format($Planilla->liquidoPlanilla,2,".",",");
					$forma_pago = $datosEmpleado->formaPagoEmpleado;
					$hnm = $Planilla->horasTrabajadasPlanilla;
					$mnm = $Planilla->minutosTrabajadosPlanilla;
					$hxt = $Planilla->horasExtraPlanilla;
					$mxt = $Planilla->minutosExtraPlanilla;
					$correlativo = $Planilla->correlativoPlanilla;
					$lineas = "----------------------------------------------------------------------------------------------------------------";
					$mm =0;
		
					//CONSULTAR BONOS
					$datosBonos = TraerDatos("empleadoBono",array('idEmpleadoEmpleadoBono' => $id_empleado,'idPeriodoEmpleadoBono' => $id_periodo,'aplicadoEmpleadoBono' => 'SI'));
					if($datosBonos!=false) $nbono = true; else $nbono = false;
		
					//CONSULTAR DESCUENTOS
					$datosDescuentos = TraerDatos("empleadoDescuento",array('idEmpleadoEmpleadoDescuento' => $id_empleado,'idPeriodoEmpleadoDescuento' => $id_periodo,'tipoEmpleadoDescuento' => 'DESCUENTO','aplicadoEmpleadoDescuento' => 'SI'));
					if($datosDescuentos!=false) $ndesc = true; else $ndesc = false;
					
					//CONSULTAR ANTICIPOS
					$datosAnticipos = TraerDatos("empleadoDescuento",array('idEmpleadoEmpleadoDescuento' => $id_empleado,'idPeriodoEmpleadoDescuento' => $id_periodo,'tipoEmpleadoDescuento' => 'ANTICIPO','aplicadoEmpleadoDescuento' => 'SI'));
					if($datosAnticipos!=false) $nant = true; else $nant = false;
					
					//CONSULTAR DETALLE DESCUENTOS
					$condicion = array('empleadoDescuentoDetalle.idEmpleadoEmpleadoDescuentoDetalle' => $id_empleado,'empleadoDescuentoDetalle.idPeriodoEmpleadoDescuentoDetalle' => $id_periodo,'empleadoDescuentoDetalle.aplicadoEmpleadoDescuentoDetalle' => 'SI');
					$join = array(
						array('tabla' => 'empleadoDescuentoCuota', 'condicion' => 'empleadoDescuentoCuota.idEmpleadoDescuentoCuota = empleadoDescuentoDetalle.idDescuentoEmpleadoDescuentoDetalle'),
						array('tabla' => 'empleadoInstitucionFinanciera','condicion' => 'empleadoDescuentoCuota.idInstitucionEmpleadoDescuentoCuota = empleadoInstitucionFinanciera.idInstitucionFinanciera')
					);
					$ordenCampos = "";
					$datosDescuentosDetalle = TraerDatosJoin("empleadoDescuentoDetalle",$condicion,$ordenCampos,$join);
					if($datosDescuentosDetalle!=false) $ndesc_det = true; else $ndesc_det = false;
			
					$setX = 10 + $aumentoX;
					$setY = 5 ;
					$path = base_url("vendors/core/img/dms.png");
					$this->fpdf->Image($path,$setX,$setY,30,25);
					$setX = 0 + $aumentoX;
					$setY = 15 ;
					//$this->fpdf->Cell(120,6,utf8_decode("DIGITALS POS"),0,1,"C");
					$this->fpdf->SetFont('Helvetica', 'B', 10);
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(130,6,utf8_decode("BOLETA DE PAGO"),0,1,"C");
					$this->fpdf->SetXY($setX+20, $setY);
					$this->fpdf->Cell(90,6,utf8_decode("N° ".$correlativo),0,1,'R');
			
					$this->fpdf->Ln(15);
		
					$setX = 10 + $aumentoX;
					$setY = 35;
		
					$this->fpdf->SetFont('Helvetica','',8);
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(85,5,"EMPLEADO: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(85,5,"CARGO: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+10);
					$this->fpdf->Cell(85,5,"SUCURSAL: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+15);
					$this->fpdf->Cell(85,5,"DEPARTAMENTO: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+20);
					$this->fpdf->Cell(85,5,"FORMA DE PAGO: ",0,1,'L');
		
					/*** * ***/
					$setX = 48 + $aumentoX;
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(85,5,$empleado,0,1,'L');
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(85,5,$cargo,0,1,'L');
					$this->fpdf->SetXY($setX, $setY+10);
					$this->fpdf->Cell(85,5,$sucursal,0,1,'L');
					$this->fpdf->SetXY($setX, $setY+15);
					$this->fpdf->Cell(85,5,$departamento,0,1,'L');
					$this->fpdf->SetXY($setX, $setY+20);
					$this->fpdf->Cell(85,5,$forma_pago,0,1,'L');
		
					$setX = 10 + $aumentoX;
					$this->fpdf->SetXY($setX, $setY+25);
					$this->fpdf->Cell(120,5,$lineas,0,1,'L');
		
					$setX = 10 + $aumentoX;
					$setY += 25;
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(85,5,"SUELDO: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+10);
					$this->fpdf->Cell(85,5,"BONIFICACIONES: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+15);
					$this->fpdf->Cell(85,5,"ISSS: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+20);
					$this->fpdf->Cell(85,5,"AFP: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+25);
					$this->fpdf->Cell(85,5,"RENTA: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+30);
					$this->fpdf->Cell(85,5,"DESCUENTOS: ",0,1,'L');
					$this->fpdf->SetXY($setX, $setY+35);
					$this->fpdf->Cell(85,5,"LIQUIDO: ",0,1,'L');
		
					$this->fpdf->SetXY($setX+60, $setY+5);
					$this->fpdf->MultiCell(60,5,$periodoVigente,0,'J',0);
		
					$setX = 45 + $aumentoX;
					$this->fpdf->SetXY($setX, $setY+5);
					$this->fpdf->Cell(20,5,$sueldo,0,1,'R');
					$this->fpdf->SetXY($setX, $setY+10);
					$this->fpdf->Cell(20,5,"+ ".$abonos,0,1,'R');
					$this->fpdf->SetXY($setX, $setY+15);
					$this->fpdf->Cell(20,5,"- ".$isss,0,1,'R');
					$this->fpdf->SetXY($setX, $setY+20);
					$this->fpdf->Cell(20,5,"- ".$afp,0,1,'R');
					$this->fpdf->SetXY($setX, $setY+25);
					$this->fpdf->Cell(20,5,"- ".$renta,0,1,'R');
					$this->fpdf->SetXY($setX, $setY+30);
					$this->fpdf->Cell(20,5,"- ".$descuentos,"B",1,'R');
					$this->fpdf->SetXY($setX, $setY+35);
					$this->fpdf->Cell(20,5,"= ".$liquido,0,1,'R');
		
					$setX = 10 + $aumentoX;
					$liquido = str_replace(",","",$liquido);
					list($entero, $decimal) = explode(".",$liquido);
					$text = mb_strtoupper(num2letras($entero)." con ".$decimal."/100",'UTF-8');
					$this->fpdf->SetXY($setX, $setY+45);
					$this->fpdf->MultiCell(125,5,$text,0,'J',0);
		
					$this->fpdf->SetXY($setX, $setY+50);
					$this->fpdf->Cell(120,5,$lineas,0,1,'L');
		
					$setY += 55 ;
					$setX = 0 + $aumentoX;
					//BONOS
					if($nbono){
						$setX = 10;
						$this->fpdf->SetXY($setX, $setY);
						$this->fpdf->Cell(120,5,"DETALLE DE BONIFICACIONES",0,1,'L');
						$this->fpdf->SetXY($setX, $setY+5);
						$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
						$this->fpdf->SetXY($setX+15, $setY+5);
						$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
						$this->fpdf->SetXY($setX+100, $setY+5);
						$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
						$mm = 5;
						$setY += 5;
						$k = 1;
						$tot = 0;
						foreach ($datosBonos as $Bono) {
							$concepto = $Bono->descripcionEmpleadoBono;
							$tot += $Bono->montoEmpleadoBono;
							$monto = number_format($Bono->montoEmpleadoBono,2,".",",");
							$this->fpdf->SetXY($setX, $setY+$mm);
							$this->fpdf->Cell(15,5,$k,0,1,'C');
							$this->fpdf->SetXY($setX+15, $setY+$mm);
							$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
							$this->fpdf->SetXY($setX+100, $setY+$mm);
							$this->fpdf->Cell(20,5,$monto,0,1,'R');
							$k++;
							$mm+=5;
						}
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
						$setY += $mm+10;
						$setX = 0 + $aumentoX;
					}
					//ANTICIPOS
					if($nant){
						$setX = 10;
						$this->fpdf->SetXY($setX, $setY);
						$this->fpdf->Cell(120,5,"DETALLE DE ANTICIPOS",0,1,'L');
						$this->fpdf->SetXY($setX, $setY+5);
						$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
						$this->fpdf->SetXY($setX+15, $setY+5);
						$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
						$this->fpdf->SetXY($setX+100, $setY+5);
						$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
						$mm = 5;
						$setY += 5;
						$k = 1;
						$tot = 0;
						foreach ($datosAnticipos as $Anticipo) {
							$concepto = $Anticipo->descripcionEmpleadoDescuento;
							$tot += $Anticipo->montoEmpleadoDescuento;
							$monto = number_format($Anticipo->montoEmpleadoDescuento,2,".",",");
							$this->fpdf->SetXY($setX, $setY+$mm);
							$this->fpdf->Cell(15,5,$k,0,1,'C');
							$this->fpdf->SetXY($setX+15, $setY+$mm);
							$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
							$this->fpdf->SetXY($setX+100, $setY+$mm);
							$this->fpdf->Cell(20,5,$monto,0,1,'R');
							$k++;
							$mm+=5;
						}
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
						$setY += $mm+10;
						$setX = 0 + $aumentoX;
					}
					//DESCUENTOS
					if($ndesc){
						$setX = 10;
						$this->fpdf->SetXY($setX, $setY);
						$this->fpdf->Cell(120,5,"DETALLE DE DESCUENTOS",0,1,'L');
						$this->fpdf->SetXY($setX, $setY+5);
						$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
						$this->fpdf->SetXY($setX+15, $setY+5);
						$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
						$this->fpdf->SetXY($setX+100, $setY+5);
						$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
						$mm = 5;
						$setY += 5;
						$k = 1;
						$tot = 0;
						foreach ($datosDescuentos as $Descuento) {
							$concepto = $Descuento->descripcionEmpleadoDescuento;
							$tot += $Descuento->montoEmpleadoDescuento;
							$monto = number_format($Descuento->montoEmpleadoDescuento,2,".",",");
							$this->fpdf->SetXY($setX, $setY+$mm);
							$this->fpdf->Cell(15,5,$k,0,1,'C');
							$this->fpdf->SetXY($setX+15, $setY+$mm);
							$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
							$this->fpdf->SetXY($setX+100, $setY+$mm);
							$this->fpdf->Cell(20,5,$monto,0,1,'R');
							$k++;
							$mm+=5;
						}
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
						$setY += $mm+10;
						$setX = 0 + $aumentoX;
					}
		
					//DETALLE DESCUENTO
					if($ndesc_det){
						$setX = 10 + $aumentoX;
						$this->fpdf->SetXY($setX, $setY);
						$this->fpdf->Cell(120,5,"ORDENES DE DESCUENTO",0,1,'L');
						$this->fpdf->SetXY($setX, $setY+5);
						$this->fpdf->Cell(15,5,utf8_decode("N°"),"B",1,'C');
						$this->fpdf->SetXY($setX+15, $setY+5);
						$this->fpdf->Cell(85,5,utf8_decode("CONCEPTO"),"B",1,'L');
						$this->fpdf->SetXY($setX+100, $setY+5);
						$this->fpdf->Cell(20,5,utf8_decode("MONTO"),"B",1,'R');
						$mm = 5;
						$setY += 5;
						$k = 1;
						$tot = 0;
						foreach ($datosDescuentosDetalle as $DescuentoDetalle) {
							$idde = $DescuentoDetalle->idEmpleadoDescuentoCuota;						
							$nsalp = TraerUnDatoIndividual("empleadoDescuentoDetalle","SUM(montoEmpleadoDescuentoDetalle) as total",array('idDescuentoEmpleadoDescuentoDetalle' => $idde,'idPeriodoEmpleadoDescuentoDetalle' => $id_periodo,'aplicadoEmpleadoDescuentoDetalle' => 'NO'))[0]["total"];
							$nombrei = $DescuentoDetalle->nombreInstitucionFinanciera;
							$concepto = $DescuentoDetalle->descripcionEmpleadoDescuentoCuota;
							$tot += $DescuentoDetalle->montoEmpleadoDescuentoDetalle;
							$monto = number_format($DescuentoDetalle->montoEmpleadoDescuentoDetalle,2,".",",");
							$this->fpdf->SetXY($setX, $setY+$mm);
							$this->fpdf->Cell(15,5,$k,0,1,'C');
							$this->fpdf->SetXY($setX+15, $setY+$mm);
							$this->fpdf->Cell(85,5,utf8_decode($concepto),0,1,'L');
							$this->fpdf->SetXY($setX+100, $setY+$mm);
							$this->fpdf->Cell(20,5,$monto,0,1,'R');
							$k++;
							$mm+=5;
							$this->fpdf->SetXY($setX+15, $setY+$mm);
							$this->fpdf->Cell(90,5,"NUEVO SALDO: $".number_format($nsalp,2),0,1,'L');
							$k++;
							$mm+=5;
						}
						$this->fpdf->SetXY($setX, $setY+$mm);
						$this->fpdf->Cell(100,5,"TOTAL","T",1,'C');
						$this->fpdf->SetXY($setX+100, $setY+$mm);
						$this->fpdf->Cell(20,5,number_format($tot,2,".",","),"T",1,'R');
					}

					$setX = 20 + $aumentoX;
					$setY = 180;

					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(20,5,"F.__________________________",0,1,'C');
					$setX = 80 + $aumentoX; 
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(20,5,"F.__________________________",0,1,'C');
		
					$setX = 20 + $aumentoX;
					$setY = 185;

					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(20,5,"PATRONO",0,1,'C');
					$setX = 80 + $aumentoX; 
					$this->fpdf->SetXY($setX, $setY);
					$this->fpdf->Cell(20,5,"EMPLEADO",0,1,'C');
					$aumentoX= 140;
				}
			}
		  	ob_clean();
			$this->fpdf->Output("boleta_pago.pdf", "I");
		}
	}

	function PlanillasImprimir($idPeriodo=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
		  GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$datosPlanilla = TraerDatos($this->tabla, array('md5(idPeriodoPlanilla)' => $idPeriodo));

			$datosPeriodo = TraerUnDato('periodoPlanilla', array('md5(idPeriodoPlanilla)'=>$idPeriodo));
			$id_periodo = $datosPeriodo->idPeriodoPlanilla;
			$periodoVigente = "";
				
			list($a,$m,$d) = explode("-", $datosPeriodo->desdePeriodoPlanilla);
			list($a1,$m1,$d1) = explode("-", $datosPeriodo->hastaPeriodoPlanilla);
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

			$this->load->add_package_path(APPPATH . 'third_party/fpdf');
			$this->load->library('pdf');
			$this->fpdf = new Pdf();
			$this->fpdf->SetMargins(10,10);
			$this->fpdf->SetTopMargin(-5);
			$this->fpdf->SetLeftMargin(10);
			//Numeracion de paginas
			$this->fpdf->AliasNbPages();
			//Salto automatico de pagina margen de 20 mm
			$this->fpdf->SetAutoPageBreak(true,15);
			//Agrega la pagina a trabajar
			$this->fpdf->AddPage('L','LETTER',0);
			$setX = 10;
			$setY = 5;
			$path = base_url("vendors/core/img/dms.png");
			$this->fpdf->Image($path,$setX,$setY,30,25);

			// Movernos a la derecha
			// Título
			$this->fpdf->SetX(0);
			$this->fpdf->SetXY(235,0);
			$this->fpdf->SetFont('Arial','I',8);
			$this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
			$this->fpdf->SetXY(125,15);
			$this->fpdf->SetFont('Helvetica','', 14);
			$this->fpdf->Cell(30, 4,"PLANILLA DE PAGO", 0, 1, 'C');
			$this->fpdf->SetFont('Helvetica','', 10);
			//$this->fpdf->SetX(0);
			//$this->fpdf->Cell(280, 4,"DIGITALS POS", 0, 1, 'C');
			$this->fpdf->SetXY(125,20);
			$this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');
			
			//$this->fpdf->SetX(10);
			//$this->fpdf->Cell(130, 4,"FECHA: ".$Planilla->fechaRegistroPlanilla, 0, 1, 'L');
			// Salto de línea
			$this->fpdf->Ln(12);
			//LISTA VARIABLES PARA ALMACENAR TOTAL
			$n = 1;
			$trenta = 0;
			$tisss = 0;
			$tafp = 0;
			$tdescuento = 0;
			$tbono = 0;
			$tsueldo = 0;
			$tliquido = 0;
			
			foreach ($datosPlanilla as $Planilla) {
				//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
				$id_empleado = $Planilla->idEmpleadoPlanilla;
				$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$Planilla->idEmpleadoPlanilla));
				$empleado = mb_strtoupper($datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado,'UTF-8');

				//CONSULTAR SUCURSAL
				$datosSucursal = TraerUnDato('sucursal',array('idSucursal' => $this->session->idSucursal));
				$sucursal = utf8_decode(mb_strtoupper($datosSucursal->nombreSucursal,'UTF-8'));

				$departamento = utf8_decode($datosEmpleado->departamentoEmpleado);
				$cargo = TraerUnDatoIndividual("cargo","nombreCargo",array('idCargo' => $datosEmpleado->idCargoEmpleado,'estadoCargo' => 'Activo'))[0]["nombreCargo"];;
				$salario = number_format($datosEmpleado->salarioBaseEmpleado,2,".","");
				$sueldo = number_format($Planilla->sueldoPlanilla,2,".",",");
				$isss = number_format($Planilla->isssPlanilla,2,".",",");
				$afp = number_format($Planilla->afpPlanilla,2,".",",");
				$renta = number_format($Planilla->rentaPlanilla,2,".",",");
				$abonos = number_format($Planilla->abonosPlanilla,2,".",",");
				$descuentos = number_format($Planilla->descuentosPlanilla,2,".",",");
				$liquido = number_format($Planilla->liquidoPlanilla,2,".",",");

				$tsueldo += $Planilla->sueldoPlanilla;
				$tisss += $Planilla->isssPlanilla;
				$tafp += $Planilla->afpPlanilla;
				$trenta += $Planilla->rentaPlanilla;
				$tbono += $Planilla->abonosPlanilla;
				$tdescuento += $Planilla->descuentosPlanilla;
				$tliquido += $Planilla->liquidoPlanilla;

				/***************************************/

				$setY=$this->fpdf->GetY();
				$setX=$this->fpdf->GetX();
				$this->fpdf->SetXY(7, $setY);
				$this->fpdf->SetFont('Helvetica', '', 9);
				$this->fpdf->Cell(10, 5, 'No.', "B", 0, 'C');
				$this->fpdf->Cell(75, 5, 'EMPLEADO', "B", 0, 'L');
				$this->fpdf->Cell(20, 5, 'SALARIO', "B", 0, 'C');
				$this->fpdf->Cell(20, 5, 'BONOS', "B", 0, 'C');
				$this->fpdf->Cell(20, 5, 'ISSS', "B", 0, 'C');
				$this->fpdf->Cell(20, 5, 'AFP', "B", 0, 'C');
				$this->fpdf->Cell(20, 5, 'RENTA', "B", 0, 'C');
				$this->fpdf->Cell(25, 5, 'DESCUENTOS', "B", 0, 'C');
				$this->fpdf->Cell(20, 5, 'LIQUIDO', "B", 0, 'C');
				$this->fpdf->Cell(35, 5, 'FIRMA', "B", 0, 'C');

				$setY=$this->fpdf->GetY();
				$this->fpdf->SetXY(7, $setY+5);

				/**************************************************************/
				$this->fpdf->SetX(7);
				$this->fpdf->Cell(10, 15,$n, "B", 0, 'C');
				$this->fpdf->Cell(75, 5, $empleado, "", 1, 'L');
				$this->fpdf->SetX(17);
				$this->fpdf->Cell(75, 5, $cargo, "", 1, 'L');
				$this->fpdf->SetX(17);
				$this->fpdf->Cell(75, 5, $departamento, "B", 0, 'L');
				$setY = $this->fpdf->GetY();
				$this->fpdf->SetXY(92,$setY-10);
				$this->fpdf->Cell(20, 15, "$".$sueldo, "B", 0, 'R');
				$this->fpdf->Cell(20, 15, "$".$abonos, "B", 0, 'R');
				$this->fpdf->Cell(20, 15, "$".$isss, "B", 0, 'R');
				$this->fpdf->Cell(20, 15, "$".$afp, "B", 0, 'R');
				$this->fpdf->Cell(20, 15, "$".$renta, "B", 0, 'R');
				$this->fpdf->Cell(25, 15, "$".$descuentos, "B", 0, 'R');
				$this->fpdf->Cell(20, 15, "$".$liquido, "B", 0, 'R');
				$this->fpdf->Cell(35, 15, "", "B", 1, 'R');
				$n++;	
			}
			$this->fpdf->SetX(7);
			$this->fpdf->Cell(85, 5,'TOTAL', "", 0, 'C');
			$this->fpdf->Cell(20, 5, "$".number_format($tsueldo,2,".",","), "", 0, 'R');
			$this->fpdf->Cell(20, 5, "$".number_format($tbono,2,".",","), "", 0, 'R');
			$this->fpdf->Cell(20, 5, "$".number_format($tisss,2,".",","), "", 0, 'R');
			$this->fpdf->Cell(20, 5, "$".number_format($tafp,2,".",","), "", 0, 'R');
			$this->fpdf->Cell(20, 5, "$".number_format($trenta,2,".",","), "", 0, 'R');
			$this->fpdf->Cell(25, 5, "$".number_format($tdescuento,2,".",","), "", 0, 'R');
			$this->fpdf->Cell(20, 5, "$".number_format($tliquido,2,".",","), "", 0, 'R');
			$this->fpdf->Cell(35, 5, "", "", 0, 'L');

			// Posición: a 1,5 cm del final
			//$this->fpdf->SetY(80);
			//$this->fpdf->SetX(7);
			// Arial italic 8
			//$this->fpdf->SetFont('Arial','I', 8);
			// Número de página requiere $pdf->AliasNbPages();
			//utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
			//$this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'L');
			//$this->fpdf->Cell(225, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
		  	ob_clean();
			$this->fpdf->Output("PLANILLA_".str_replace(' ','_',$periodoVigente).".pdf", "I");
		}
	}
    
}
/* End of file Planillas.php */
