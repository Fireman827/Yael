<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contratos extends CI_Controller {

	private $tabla = "contrato";
	private $controlador = "Contratos";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
		$this->load->helper('mpdf_helper');
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Contratos";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-file-alt",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'ContratosAgregar',
						'txt' => 'Agregar Contrato',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
				"encabezados"=>array(
					"ID"=>1,
					"Empleado"=>2,
					"Desde"=>1,
					"Hasta"=>1,
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
					"scripts/contratos.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function ContratosMostrar(){
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));

		$order = $this->input->post("order");
		$busquedaAreglo = $this->input->post("search");
		$busquedaParametro = $busquedaAreglo['value'];
		$col = 0;
		$ordenDireccion = "desc";
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
			0 => 'contrato.idContrato',
			1 => 'empleado.nombreEmpleado',
			2 => 'empleado.apellidoEmpleado'
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
		$condicion = array('contrato.idSucursalContrato' => $sucursal,'contrato.estadoContrato!=' => 'Borrado');
		$joins = array(array('tabla' => 'empleado', 'condicion' => 'empleado.idEmpleado = contrato.idEmpleadoContrato'));
		$Contratos = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$joins);
		
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Contratos!= 0){
			$datosMostrar = array();
			foreach ($Contratos as $Contrato) {
                $estadoContrato = $Contrato->estadoContrato;
                if ($estadoContrato == 'Activo') {
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

                $funcion = "ContratosEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($Contrato->idContrato)."' ><i class='fa fa-edit' ></i> Editar</a>";
                }
				$funcion = "ContratosPdf";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item ".$funcion."' idContrato='".md5($Contrato->idContrato)."' ><i class='fa fa-file-pdf' ></i> Contrato PDF </a>";
                }
                $funcion = "ContratosCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idContrato=" . md5($Contrato->idContrato) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "ContratosEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idContrato=" . md5($Contrato->idContrato) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $Contrato->idContrato,
                    $Contrato->nombreEmpleado." ".$Contrato->apellidoEmpleado,
                    $Contrato->desdeContrato,
                    $Contrato->hastaContrato,
                    $estadoSpan,
                    $menuOpciones
                );
            }
			$totalContratos = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalContratos,
				"recordsFiltered" => $totalContratos,
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

	function ContratosAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				//CONSULTA PARA TRAER TODAS LAS CLAUSULAS
				$datosContratoTipo = TraerDatos('contratoTipo',array("estadoContratoTipo"=>"Activo"));

				$contratoTipoOpciones = "";
				if ($datosContratoTipo) {
					foreach ($datosContratoTipo as $contratoTipo){
						$contratoTipoOpciones .= "<option value='".$contratoTipo->idContratoTipo."' >".$contratoTipo->nombreContratoTipo."</option>";
					}
				}
	
				$titulo = "Agregar Contrato";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-file-alt",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"contratoTipoOpciones" => $contratoTipoOpciones
				);
				$extras = array(
					'css' => array(
						"vendors/plugins/TypeAhead/typeahead.css"
					),
					'js' => array(
						"scripts/contratos.js",
						"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
					),
				);
				GblPlantilla("contratos/ContratoAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalContrato = $this->session->idSucursal;
				$idEmpleadoContrato = $this->input->post("idEmpleadoContrato");
				$idContratoTipoContrato = $this->input->post("idContratoTipoContrato");
				$duiContrato = $this->input->post("duiContrato");
				$nitContrato = $this->input->post("nitContrato");
				$desdeContrato = $this->input->post("desdeContrato");
				$hastaContrato = $this->input->post("hastaContrato");
				$horarioContrato = $this->input->post("horarioContrato");

				$condicionExiste = array(
					'idEmpleadoContrato' => $idEmpleadoContrato,
					'idContratoTipoContrato' => $idContratoTipoContrato,
					'idSucursalContrato'=>$idSucursalContrato,
					'duiContrato' => $duiContrato,
					'nitContrato' => $nitContrato,
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosContratos  = array(
						"idEmpleadoContrato"=>$idEmpleadoContrato,
						"idContratoTipoContrato"=>$idContratoTipoContrato,
						"duiContrato"=>$duiContrato,
						"nitContrato"=>$nitContrato,
						"desdeContrato"=>$desdeContrato,
						"hastaContrato"=>$hastaContrato,
						"horarioContrato"=>$horarioContrato,
						"estadoContrato"=> 'Activo',
						"aleatorioContrato" => uniqid(),
						"idSucursalContrato"=>$idSucursalContrato
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosContratos);
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

	function ContratosEditar($idContrato=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idContrato)' => $idContrato);
				$datosContratos = TraerUnDato($this->tabla, $condicionDatos);
				if($datosContratos !== false && $idContrato!=""){
					//CONSULTA PARA TRAER LOS DATOS DEL EMPLEADO
					$datosEmpleado = TraerUnDato('empleado',array("idEmpleado"=>$datosContratos->idEmpleadoContrato));
					$empleadoContrato = $datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado;

                    //CONSULTA PARA TRAER LOS TIPOS DE CONTRATO
					$datosContratoTipo = TraerDatos('contratoTipo',array("estadoContratoTipo"=>"Activo"));
					$contratoTipoOpciones = "";

					if(count($datosContratoTipo)!=0){
						foreach ($datosContratoTipo as $contratoTipo){
							if($datosContratos->idContratoTipoContrato==$contratoTipo->idContratoTipo){
								$contratoTipoOpciones .= "<option value='".$contratoTipo->idContratoTipo."' selected >".$contratoTipo->nombreContratoTipo."</option>"; 
							} else {
								$contratoTipoOpciones .= "<option value='".$contratoTipo->idContratoTipo."' >".$contratoTipo->nombreContratoTipo."</option>"; 
							}						
						}                         																							
					} 
			
					$titulo = "Editar Contrato";
					$datosVista = array(
						"datosContratos"=> $datosContratos,
						"controlador" => $this->controlador,
						"idContrato" => $idContrato,
						"titulo" => $titulo,
						"proceso" => "Editar",
                        "icono" => "fa-file-alt",
						"contratoTipoOpciones" => $contratoTipoOpciones,
						"empleadoContrato" => $empleadoContrato
					);
					$extras = array(
						'css' => array(
							"vendors/plugins/TypeAhead/typeahead.css"
						),
						'js' => array(
							"scripts/contratos.js",
							"vendors/plugins/TypeAhead/typeahead.jquery.min.js"
						),
					);
					GblPlantilla("contratos/ContratoEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idContrato = $this->input->post("idContrato");
				$idSucursalContrato = $this->session->idSucursal;
				$idEmpleadoContrato = $this->input->post("idEmpleadoContrato");
				$idContratoTipoContrato = $this->input->post("idContratoTipoContrato");
				$duiContrato = $this->input->post("duiContrato");
				$nitContrato = $this->input->post("nitContrato");
				$desdeContrato = $this->input->post("desdeContrato");
				$hastaContrato = $this->input->post("hastaContrato");
				$horarioContrato = $this->input->post("horarioContrato");
				
				$condicionExiste = array(
					'idEmpleadoContrato' => $idEmpleadoContrato,
					'idContratoTipoContrato' => $idContratoTipoContrato,
					'duiContrato' => $duiContrato,
					'nitContrato' => $nitContrato,
					'idSucursalContrato' => $idSucursalContrato,
					'md5(idContrato)!=' => $idContrato
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosContratos = array(
						"idEmpleadoContrato"=>$idEmpleadoContrato,
						"idContratoTipoContrato"=>$idContratoTipoContrato,
						"duiContrato"=>$duiContrato,
						"nitContrato"=>$nitContrato,
						"desdeContrato"=>$desdeContrato,
						"hastaContrato"=>$hastaContrato,
						"horarioContrato"=>$horarioContrato,
						"idSucursalContrato"=>$idSucursalContrato,
						"aleatorioContrato"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("md5(idContrato)" => $idContrato);
					$editar = EditarDatos($this->tabla,$datosContratos,$condicion);
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

	function ContratosCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContrato = $this->input->post("idContrato");
                $condicionDatos = array(
                    'md5(idContrato)' => $idContrato,
                    'estadoContrato' => 'Activo',
                );
                $activoContrato = ExistenDatos($this->tabla, $condicionDatos);

                ($activoContrato == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosContrato = array(
                    "estadoContrato" => $nuevoEstado
                );
                $condicion = array("md5(idContrato)" => $idContrato);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosContrato, $condicion);
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
    
    function ContratosEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContrato = $this->input->post("idContrato");
                $datosContrato = array(
                    "estadoContrato" => 'Borrado'
                );
                $condicion = array("md5(idContrato)" => $idContrato);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosContrato, $condicion);
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

	function ContratosAutocompleteEmpleado(){
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

	function ContratosPdf($idContrato=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {

			date_default_timezone_set('America/El_Salvador');
			$fecha_actual=date("Y-m-d");

			mpdf();
			$mpdf = new \Mpdf();
			$id = $this->uri->segment(4);

			$stylesheet = file_get_contents(base_url("vendors/core/css/css_mpdf.css"));
			$mpdf->WriteHTML($stylesheet,1);

			$id_contrato = 1;
			$id_sucursal = $this->session->idSucursal;
			$nombre_empresa = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'nombreEmpresa','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$direccion_empresa = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'direccionEmpresa','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$telefono_empresa = TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'telefonoEmpresa','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"];
			$logo = TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'logoEmpresa','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"]; 
			$nombre_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'nombrePatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$direccion_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'domicilioPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$residencia_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'residenciaPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$nacionalidad_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'nacionalidadPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$edad_patron = TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'fechaNacimientoPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"];
			$edad_patron = calcular_edad($edad_patron); 
			$sexo_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'sexoPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$estado_civil_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'estadoCivilPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$profesion_oficio_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'profesionOficioPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');
			$dui_patron = TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'duiPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"];
			$expedicion_dui_patron = mb_strtoupper(TraerUnDatoIndividual("configuraciones","valorConfiguracion",array('parametroConfiguracion' => 'expedicionDuiPatrono','idSucursalConfiguracion' => $id_sucursal))[0]["valorConfiguracion"],'UTF-8');

			//TRAER DATOS DEL CONTRATO
			$datosContrato = TraerUnDato('contrato',array('md5(idContrato)' => $idContrato));
			$empleado=$datosContrato->idEmpleadoContrato;
			$desde=$datosContrato->desdeContrato;
			$hasta=$datosContrato->hastaContrato;
			$horario=$datosContrato->horarioContrato;
			$idContratoTipo=$datosContrato->idContratoTipoContrato;

			$tiempo=numero_anios($desde,$hasta);
			$tiempo_letra = "";
			$tipo = "";
			if($tiempo==0){
				$tiempo = numero_meses($desde,$hasta);

				if($tiempo==1){
					$tipo="MES";
				} else {
					$tipo="MESES";
				}
				if($tiempo==0){
					$tiempo_letra="";
				} else {
					$tiempo_letra = strtoupper("TIEMPO ".num2letras($tiempo)." ".$tipo);
				}

			} else {
				if($tiempo==1){
					$tipo="AÑO";
				} else {
					$tipo="AÑOS";
				}
				$tiempo_letra = strtoupper("TIEMPO ".num2letras($tiempo)." ".$tipo);
			}

			list($a,$m,$d) = explode("-", $desde);
			list($a1,$m1,$d1) = explode("-", $hasta);
			$fecha_inicial=$d." DE ".meses($m)." DE $a";
			$fecha_final=$d1." DE ".meses($m1)." DE $a1";
			list($a2,$m2,$d2) = explode("-", $fecha_actual);
			$fecha_actual_l=$d2." DE ".meses($m2)." DE $a2";

			$datosEmpleado = TraerUnDato('empleado',array('idEmpleado' => $empleado));
			$nombre_emple = $datosEmpleado->nombreEmpleado." ".$datosEmpleado->apellidoEmpleado;
			$edad = calcular_edad($datosEmpleado->fechaNacimientoEmpleado);
			$edad = calcular_edad($edad);
			$sexo = $datosEmpleado->sexoEmpleado;
			$estado_civil = $datosEmpleado->estadoCivilEmpleado;
			$profesion_oficio = $datosEmpleado->profesionOficioEmpleado;
			$domicilio = $datosEmpleado->direccionEmpleado;
			$residencia = $datosEmpleado->residenciaEmpleado;
			$nacionalidad = $datosEmpleado->nacionalidadEmpleado;
			$dui = $datosEmpleado->duiEmpleado;
			$expedicion_dui = $datosEmpleado->expedicionDuiEmpleado;
			$familiares = $datosEmpleado->familiaresEmpleado;
			$cargo = $datosEmpleado->idCargoEmpleado;
			$salario_base = $datosEmpleado->salarioBaseEmpleado;
			$pago_diario = number_format($salario_base/12,2);

			$evaluacion= explode(".", $salario_base);
			$entero=$evaluacion[0];
			$decimal=$evaluacion[1];
			if($decimal==""){
				$decimal=0;
			}
			if($entero==""){
				$entero=0;
			}
			$pago_letra = strtoupper("$ ".$salario_base." MENSUALES (".num2letras($entero)." ".$decimal."/100");

			$datosCargo = TraerUnDato('cargo',array('idCargo' => $cargo));
			$nombre_puesto = $datosCargo->nombreCargo;
			$descripcion_puesto = $datosCargo->descripcionCargo;
			$funciones_puesto = $datosCargo->funcionesCargo;

			$ante_nombre="";
			if($sexo_patron=="MASCULINO"){
				$ante_nombre="EL SEÑOR";
			}
			if($sexo_patron=="FEMENINO"){
				$ante_nombre="LA SEÑORA";
			}

			$html2 = "<page backtop='22mm' backbottom='15mm' backleft='10mm' backright='10mm' style='font-size: 10pt' backimgx='center' backimgy='bottom' backimgw='100%'>
			<page_header>
			  <table class='page_header'>
				<tr >
				  <td  style='width:50%;'>
					<img style='width:15%;' src='".base_url("vendors/core/img/dms.png")."'>
				  </td>
				</tr>
			  </table>
			</page_header>";
			$html2 .= "<table style='width:100%; font-size:10pt;'>
				<tr>
					<th align=center style='width:90%;'> <p style='text-align:center'><strong><u>C O N T R A T O&nbsp;&nbsp;&nbsp; I N D I V I D U A L&nbsp;&nbsp;&nbsp; D E&nbsp;&nbsp;&nbsp; T R A B A J O</u></strong></p><br></th>
				</tr>
			</table>
			<br>
			<table style='width:100%; font-size:10pt;'>
				<thead>
			
				<tr >
					<th style='width:50%; text-align:left'><p><strong><u>GENERALES&nbsp;&nbsp; DEL&nbsp; CONTRATANTE&nbsp;&nbsp; PATRONAL</u></strong></p></th>
					<th style='width:50%; text-align:left'><p><u><strong>GENERALES&nbsp;&nbsp; DEL(A)&nbsp; TRABAJADOR(A)</strong></u></p></th>
				</tr>
				</thead>
				<tbody >
				<tr>
					<td style='width:50%; text-align:left' ><p>Nombre: <strong>".$nombre_patron."</strong></p></td>
					<td style='width:50%; text-align:left'><p>Nombre: <strong>".$nombre_emple."</strong></p></td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left'><p>Edad: ".$edad_patron." años</p> </td>
					<td style='width:50%; text-align:left'><p>Edad: ".$edad." años</p> </td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left' ><p>Sexo:  ".$sexo_patron."</p></td>
					<td style='width:50%; text-align:left'><p>Sexo: ".$sexo."</p></td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left' ><p>Profesión u Oficio: ".$profesion_oficio_patron."</p></td>
					<td style='width:50%; text-align:left'><p>Profesión u Oficio: ".$profesion_oficio."</p></td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left' ><p>Domicilio: ".$direccion_patron."</p></td>
					<td style='width:50%; text-align:left'><p>Domicilio: ".$domicilio."</p></td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left' ><p>Residencia: ".$residencia_patron."</p></td>
					<td style='width:50%; text-align:left'><p>Residencia: ".$residencia."</p></td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left' ><p>Nacionalidad: ".$nacionalidad_patron."</p></td>
					<td style='width:50%; text-align:left'><p>Nacionalidad: ".$nacionalidad."</p></td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left' ><p>DUI No.: ".$dui_patron."</p></td>
					<td style='width:50%; text-align:left'><p>DUI No.: ".$dui."</p></td>
				</tr>
				<tr>
					<td style='width:50%; text-align:left'><p>Expedido en: ".$expedicion_dui_patron."</p></td>
					<td style='width:50%; text-align:left'><p>Expedido en: ".$expedicion_dui."</p></td>
				</tr>
				</tbody>
			</table>
			";

			$html2 .= "<table style='width:100%; font-size:10pt;'>
				<tr>
					<td style='width:100%; text-align:left' ><br><br><p>En representación de:  <strong>NUEVOPOS, S.A. DE C.V., REPRESENTADA LEGALMENTE POR ".$ante_nombre." ".$nombre_patron."</strong> </p></td>
				</tr>
			</table>
			<table style='width:100%; font-size:10pt;'>
				<tr>
					<td style='width:100%; text-align:left'><br><p>NOSOTROS: <strong>".$nombre_patron." Y ".$nombre_emple."</strong> De las generales antes expresadas convenimos en celebrar el presente Contrato Individual de Trabajo sujeto a las estipulaciones siguientes:</p></td>
				</tr>
			</table>";
			
			$datosContratoTipoClausula = TraerDatos('contratoTipoClausula',array('idContratoTipoContratoTipoClausula' => '1'));

			$l=1;
			if($datosContratoTipoClausula!=false){
				foreach($datosContratoTipoClausula as $contratoTipoClausula){
					$id_clausula=$contratoTipoClausula->idContratoClausulaContratoTipoClausula;
	
					$datosContratoClausula = TraerUnDato('contratoClausula',array('idContratoClausula' => $id_clausula,'anexosContratoClausula' => '0'));
	
					if($datosContratoClausula!=false){
						$nombre=$datosContratoClausula->nombreContratoClausula;
						$descripcion=$datosContratoClausula->descripcionContratoClausula;
						$buscar = array('[cargo]','[descripcion_cargo]','[tiempo_trabajo]','[fecha_inicio]','[fecha_final]','[horario]','[nombre_empresa]','[direccion_empresa]','[pago_letra]','[fecha_actual]');
						$remplazo = array($nombre_puesto,$descripcion_puesto,$tiempo_letra,$fecha_inicial,$fecha_final,$horario,$nombre_empresa,$direccion_empresa,$pago_letra,$fecha_inicial);
	
						for($i=0;$i<count($remplazo);$i++) {
							$descripcion = str_replace($buscar[$i], $remplazo[$i], $descripcion);
						}
	
						if($nombre=="OTRAS ESTIPULACIONES"){
							$indic=$l;
							if($familiares==""){
								$indic=$l;
							} else {
								$indic=$l+1;
								$html2 .= "<table style='width:100%; font-size:10pt;'><tr>";
								$html2 .= "<td style='width:100%; text-align:left'><br><p><strong><u>".letras($l).") PERSONAS QUE DEPENDEN ECONÓMICAMENTE DE EL(A) TRABAJADOR(A):</u></strong></p><br></td>";
								$html2 .= "</tr></table>";
								$html2 .= "<table style='width:100%; font-size:10pt; border:1' >
								<tr>
									<td style='width:40%; text-align:left; '>Nombres</td>
									<td style='width:40%; text-align:left; '>Apellidos</td>
									<td style='width:20%; text-align:left; '>Parentesco</td>
								</tr>";
								$campos = explode("#", $familiares);
								$ncampos = count($campos);
								for($i=0; $i<($ncampos-1); $i++){
									list($nombre_f, $apellido_f, $parentesco) = explode("/",$campos[$i]);
									$html2 .= "<tr><td>".$nombre_f."</td><td>".$apellido_f."</td><td>".$parentesco."</td></tr>";
								}
								$html2 .= "</table>";
							}
							$html2 .= "<table style='width:100%; font-size:10pt;'>";
							$html2 .= "<tr>";
							$html2 .=    "<td style='width:100%;' ><br><p><strong><u>".letras($indic).")".$nombre."</u></strong></p><br>".$descripcion."</td>";
							$html2 .= "</tr></table>";
						} else {
							$html2 .= "<table style='width:100%; font-size:10pt;'>";
							$html2 .= "<tr>";
							$html2 .= "<td style='width:100%;' ><br><p><strong><u>".letras($l).")".$nombre."</u></strong></p><br>".$descripcion."</td>";
							$html2 .= "</tr></table>";
						}
						$l++;
					}
				}
			}

			$html2 .= "<table style='width:100%; font-size:10pt;'>
				<tr>
					<td style='width:49%; text-align:left' ><br><br><br><p>(f)_______________________________________</p></td>
					<td style='width:49%; text-align:left' ><br><br><br><p>(f)_______________________________________</p></td>
				</tr>
				<tr>
					<td style='width:49%; text-align:center' ><p>PATRONO</p></td>
					<td style='width:49%; text-align:center' ><p>FIRMA DE EL(LA) TRABAJADOR(A)</p></td>
				</tr>
			</table><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";

			if($funciones_puesto!=""){
				$html2 .= "<table style='width:100%; font-size:10pt;'>";
				$html2 .= "<tr>";
				$html2 .= "<td style='width:100%;' ><br><p><strong><u>FUNCIÓNES DEL ".$nombre_puesto."</u></strong></p><br>".$funciones_puesto."</td>";
				$html2 .= "</tr></table>";
			}
			
			if($datosContratoTipoClausula!=false){
				foreach($datosContratoTipoClausula as $contratoTipoClausula){
					$id_clausula=$contratoTipoClausula->idContratoClausulaContratoTipoClausula;
	
					$datosContratoClausula = TraerUnDato('contratoClausula',array('idContratoClausula' => $id_clausula,'anexosContratoClausula' => '1'));
	
					if($datosContratoClausula!=false){
						$nombre=$datosContratoClausula->nombreContratoClausula;
						$descripcion=$datosContratoClausula->descripcionContratoClausula;
						$html2 .= "<table style='width:100%; font-size:10pt;'>";
						$html2 .= "<tr>";
						$html2 .=    "<td style='width:100%;' ><br><p><strong><u>".$nombre."</u></strong></p><br>".$descripcion."</td>";
						$html2 .= "</tr></table>";
					}
				}
			}
		
			$html2 .= "</page>";

			$mpdf->WriteHTML($html2,2);
			$mpdf->Output('Contrato_Laboral.pdf','I');
		}
	}
    
}
/* End of file Contratos.php */
