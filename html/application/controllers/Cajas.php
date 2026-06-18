<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cajas extends CI_Controller {

	private $tabla = "caja";
	private $controlador = "Cajas";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$agregarCaja = ($this->session->superAdmin == "1") ? array(
				array(
					"icono"=> "fa fa-plus",
					'controlador' => $this->controlador,
					'url' => '/CajasAgregar',
					'txt' => 'Agregar Caja',
					'posicion' => 'right', // left, right
					'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
					'modal' => false,
					'id' => ''
				)
			) : array();
			$titulo = "Cajas";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fas fa-cash-register",
				"botones" => $agregarCaja,
				"encabezados"=>array(
					"ID"=>1,
					"Nombre"=>3,
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
					"scripts/cajas.js"
				),
			);
			
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function CajasMostrar(){
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
			0 => 'idCaja',
			1 => 'nombreCaja'
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
		$condicion = array('idSucursalCaja' => $sucursal,'estadoCaja!=' => 'Borrado');
		$Cajas = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
		//print_r($Cajas);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($Cajas!= 0){
			$datosMostrar = array();
			foreach ($Cajas as $Caja) {
                $estadoCaja = $Caja->estadoCaja;
                if ($estadoCaja == 'Activo') {
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

                $funcion = "CajasEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($Caja->idCaja)."' ><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "CajasCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idCaja=" . md5($Caja->idCaja) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "CajasEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idCaja=" . md5($Caja->idCaja) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $Caja->idCaja,
                    $Caja->nombreCaja,
                    $estadoSpan,
                    $menuOpciones
                );
            }
			$totalCajas = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalCajas,
				"recordsFiltered" => $totalCajas,
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

	function CajasAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			
			if($this->input->method(TRUE) == "GET"){
				$impresoras = TraerDatos('impresora',array('cuentaImpresora'=>'1','idSucursalImpresora' =>$this->session->idSucursal,'estadoImpresora' =>'Activo'));
	
				$titulo = "Agregar Caja";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-cash-register",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar",
					"impresoras" => $impresoras,
					//"departamentos"=> $departamentosOption
				);
				$extras = array(
					'css' => array(
					),
					'js' => array(
						"scripts/cajas.js"
					),
				);
				if($this->session->superAdmin == "1"){

					GblPlantilla("cajas/CajaAgregar",$datosVista,$extras,$titulo);
				}
				else{
					//var_dump($this->session->superAdmin);
					GblPlantilla("plantilla/404");
				}
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalCaja = $this->session->idSucursal;
				$nombreCaja = $this->input->post("nombreCaja");
				$impresoras = $this->input->post("impresora");

				$datosCajaDocumentos = json_decode($this->input->post("datosCajaDocumento"));

				$condicionExiste = array('nombreCaja' => $nombreCaja,'idSucursalCaja'=>$idSucursalCaja);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosCaja  = array(
						"nombreCaja"=>$nombreCaja,
						"impresoraCaja"=>$impresoras,
						"estadoCaja"=> 'Activo',
						"aleatorioCaja" => uniqid(),
						"idSucursalCaja"=>$this->session->idSucursal
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosCaja);
					if($guardar){
						if(count($datosCajaDocumentos)!=0){
							foreach ($datosCajaDocumentos as $cajaDocumentos):
								$datosCajaDocumento = array(
									"idDocumentoCajaDocumento" => $cajaDocumentos[1],
									"inicioCajaDocumento" => $cajaDocumentos[2],
									"finalCajaDocumento" => $cajaDocumentos[3],
									"actualCajaDocumento" => $cajaDocumentos[4],
									"fechaAutorizacionCajaDocumento" => $cajaDocumentos[5],
									"fechaResolucionCajaDocumento" => $cajaDocumentos[6],
									"numeroResolucionCajaDocumento" => $cajaDocumentos[7],
									"serieCajaDocumento" => $cajaDocumentos[8],
									"aleatorioCajaDocumento" => uniqid(),
									"idCajaCajaDocumento" => $guardar
								);
								$guardarCajaDocumento = GuardarDatos("cajaDocumento",$datosCajaDocumento);
							endforeach;
						}
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

	function CajasEditar($idCaja=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idCaja)' => $idCaja);
				$datosCaja = TraerUnDato($this->tabla, $condicionDatos);
				if($datosCaja !== false && $idCaja!=""){
					//CONSULTA PARA TRAER LOS TIPOS DE DOCUMENTOS
					$datosDocumento = TraerDatos('documento');				

					//CONSULTA PARA TRAER LOS DOCUMENTOS DE LA CAJA A EDITAR
					$cajaDocumento = "";
					$existe = ExistenDatos('cajaDocumento', array("idCajaCajaDocumento" => $datosCaja->idCaja,"estadoCajaDocumento!=" => "Borrado"));
					if($existe!=0){
						$datosCajaDocumento = TraerDatos('cajaDocumento',array("idCajaCajaDocumento"=>$datosCaja->idCaja));
						
						foreach ($datosCajaDocumento as $cajaDocumentos){
							$cajaDocumento .= "<tr><td><select class='select2 idDocumento' name='idDocumento' readonly >";
							foreach ($datosDocumento as $documento){
								if($documento->idDocumento==$cajaDocumentos->idDocumentoCajaDocumento){
									$cajaDocumento .= "<option value='".$documento->idDocumento."' selected>".mb_strtoupper($documento->nombreDocumento."(".$documento->aliasDocumento.")",'UTF-8')."</option>";
								} else {
									$cajaDocumento .= "<option value='".$documento->idDocumento."' >".mb_strtoupper($documento->nombreDocumento."(".$documento->aliasDocumento.")",'UTF-8')."</option>";
								}							
							}
							$cajaDocumento .= "</select></td>";												
							$cajaDocumento .= "<td><input type='text' class='form-control inicio numeric' name='inicio' placeholder='Inicio' value='".$cajaDocumentos->inicioCajaDocumento."' readonly ></td>";
							$cajaDocumento .= "<td><input type='text' class='form-control final numeric' name='final' placeholder='Final' value='".$cajaDocumentos->finalCajaDocumento."' readonly ></td>";
							$cajaDocumento .= "<td><input type='text' class='form-control actual numeric' name='actual' placeholder='Actual' value='".$cajaDocumentos->actualCajaDocumento."' readonly ></td>";
							$cajaDocumento .= "<td><input type='date' class='form-control fechaAutorizacion' name='fechaAutorizacion' placeholder='Fecha de autorización' value='".$cajaDocumentos->fechaAutorizacionCajaDocumento."' readonly ></td>";
							$cajaDocumento .= "<td><input type='date' class='form-control fechaResolucion' name='fechaResolucion' placeholder='Fecha de resolución' value='".$cajaDocumentos->fechaResolucionCajaDocumento."' readonly ></td>";
							$cajaDocumento .= "<td><input type='text' class='form-control numeroResolucion' name='numeroResolucion' placeholder='Número de resolución' value='".$cajaDocumentos->numeroResolucionCajaDocumento."' readonly ></td>";
							$cajaDocumento .= "<td><input type='text' class='form-control serie' name='serie' placeholder='Número de serie' value='".$cajaDocumentos->serieCajaDocumento."' readonly ></td>";

							$estadoCajaDocumento = $cajaDocumentos->estadoCajaDocumento;
							if ($estadoCajaDocumento == 'Activo') {
								$estadoTxt = "Desactivar";
								$estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
								$estadoIcon = "fa fa fa-toggle-on";
							} else {
								$estadoTxt = "Activar";
								$estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
								$estadoIcon = "fa fa-toggle-off";
							}
							$cajaDocumento .= "<td><div class='input-group-prepend'>
							<button data-toggle='dropdown' class='btn btn-primary btn-block dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
							<div class='dropdown-menu dropdown-menu-left' x-placement='top-start'>";
						
							$cajaDocumento .= "<a class='dropdown-item CajaDocumentoEditar' data-accion='Editar' idCajaDocumento=" . md5($cajaDocumentos->idCajaDocumento) . "><i class='fa fa-edit' ></i> Editar</a>";							
							$cajaDocumento .= "<a class='dropdown-item CajaDocumentoCambiarEstado' data-accion='$estadoTxt' idCajaDocumento=" . md5($cajaDocumentos->idCajaDocumento) . "><i class='$estadoIcon'></i> $estadoTxt</a>";							
							$cajaDocumento .= "<a class='dropdown-item CajaDocumentoBorrar' idCajaDocumento='".$cajaDocumentos->idCajaDocumento."' ><i class='fa fa-trash'></i> Eliminar</a>";							
							$cajaDocumento .= "</div></div></td>";							
							$cajaDocumento .= "</tr>";
						}
					}
					$impresoras = TraerDatos('impresora',array('cobroImpresora'=>'1','idSucursalImpresora' =>$this->session->idSucursal,'estadoImpresora' =>'Activo'));

			
					$titulo = "Editar Caja";
					$datosVista = array(
						"datosCaja"=> $datosCaja,
						"controlador" => $this->controlador,
						"idCaja" => $idCaja,
						"titulo" => $titulo,
						"proceso" => "Editar",
						"cajaDocumento" => $cajaDocumento,
						"impresoras" => $impresoras,
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/cajas.js"
						),
					);
					GblPlantilla("cajas/CajaEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idCaja = $this->input->post("idCaja");
				$nombreCaja = $this->input->post("nombreCaja");
				$impresoras = $this->input->post("impresoraCaja");
				$datosCajaDocumentos = json_decode($this->input->post("datosCajaDocumento"));
				
				$condicionExiste = array(
					'nombreCaja' => $nombreCaja,
					'idSucursalCaja' => $this->session->idSucursal,
					'idCaja!=' => $idCaja
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosCaja = array(
						"nombreCaja"=>$nombreCaja,
						"impresoraCaja"=>$impresoras,
						"idSucursalCaja"=>$this->session->idSucursal,
						"aleatorioCaja"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("idCaja" => $idCaja);
					$editar = EditarDatos($this->tabla,$datosCaja,$condicion);
					if($editar){
						if(true){															
							foreach ($datosCajaDocumentos as $cajaDocumentos):
								if($cajaDocumentos[0]!==""){
									$datosCajaDocumento = array(
									"idDocumentoCajaDocumento" => $cajaDocumentos[1],
									"inicioCajaDocumento" => $cajaDocumentos[2],
									"finalCajaDocumento" => $cajaDocumentos[3],
									"actualCajaDocumento" => $cajaDocumentos[4],
									"fechaAutorizacionCajaDocumento" => $cajaDocumentos[5],
									"fechaResolucionCajaDocumento" => $cajaDocumentos[6],
									"numeroResolucionCajaDocumento" => $cajaDocumentos[7],
									"serieCajaDocumento" => $cajaDocumentos[8],
									"aleatorioCajaDocumento" => uniqid(),
									"idCajaCajaDocumento" => $idCaja
									);
									$editarCajaDocumento = EditarDatos("cajaDocumento",$datosCajaDocumento,array("md5(idCajaDocumento)" => $cajaDocumentos[0]));
								} else {
									$datosCajaDocumento = array(
									"idDocumentoCajaDocumento" => $cajaDocumentos[1],
									"inicioCajaDocumento" => $cajaDocumentos[2],
									"finalCajaDocumento" => $cajaDocumentos[3],
									"actualCajaDocumento" => $cajaDocumentos[4],
									"fechaAutorizacionCajaDocumento" => $cajaDocumentos[5],
									"fechaResolucionCajaDocumento" => $cajaDocumentos[6],
									"numeroResolucionCajaDocumento" => $cajaDocumentos[7],
									"serieCajaDocumento" => $cajaDocumentos[8],
									"aleatorioCajaDocumento" => uniqid(),
									"idCajaCajaDocumento" => $idCaja
									);
									$guardarCajaDocumento = GuardarDatos("cajaDocumento",$datosCajaDocumento);
								}
							endforeach;
							//La acción se realizo con éxito						
							EjecutarTransaccion();
							$datosRespuesta["codigo"] = 200;							
						}
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

	function CajasCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCaja = $this->input->post("idCaja");
                $condicionDatos = array(
                    'md5(idCaja)' => $idCaja,
                    'estadoCaja' => 'Activo',
                );
                $activoCaja = ExistenDatos($this->tabla, $condicionDatos);

                ($activoCaja == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosCajas = array(
                    "estadoCaja" => $nuevoEstado
                );
                $condicion = array("md5(idCaja)" => $idCaja);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosCajas, $condicion);
                if ($editar) {
                    EjecutarTransaccion();
                    $datosRespuesta["codigo"] = 200;				
                }

				if ($nuevoEstado == 'Activo') {
				$this->load->helper('vfd_helper');
				vfd_show_welcome();
					}

					
				else {
                    DeshacerTransaccion();
                    $datosRespuesta["codigo"] = 500;
                }
            }
        }
        echo json_encode($datosRespuesta);
    }

	function CajasDocumentoCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCajaDocumento = $this->input->post("idCajaDocumento");
                $condicionDatos = array(
                    'md5(idCajaDocumento)' => $idCajaDocumento,
                    'estadoCajaDocumento' => 'Activo',
                );
                $activoCaja = ExistenDatos("cajaDocumento", $condicionDatos);

                ($activoCaja == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosCajas = array(
                    "estadoCajaDocumento" => $nuevoEstado
                );
                $condicion = array("md5(idCajaDocumento)" => $idCajaDocumento);
                IniciarTransaccion();
                $editar = EditarDatos("cajaDocumento", $datosCajas, $condicion);
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
    
    function CajasEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCaja = $this->input->post("idCaja");
                $datosCajas = array(
                    "estadoCaja" => 'Borrado'
                );
                $condicion = array("md5(idCaja)" => $idCaja);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosCajas, $condicion);
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

	function CajaDocumento(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			//No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"]=403;
		} else {
			if($this->input->method(TRUE) == "POST"){

				$datosDocumento = TraerDatos('documento');
				$documentosOption = "<option value='' >SELECCIONE UN DOCUMENTO</option>";
				foreach ($datosDocumento as $documento):
					$documentosOption .= "<option value='".$documento->idDocumento."' >".mb_strtoupper($documento->nombreDocumento."(".$documento->aliasDocumento.") - ".$documento->tipoDocumento,'UTF-8')."</option>";
				endforeach;
				$datosRespuesta["tiposDocumentos"] = $documentosOption;
				echo json_encode($datosRespuesta);
			}
		}
	}

	function CajasDocumentoEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCajaDocumento = $this->input->post("idCajaDocumento");
                $datosCajas = array(
                    "estadoCajaDocumento" => 'Borrado'
                );
                $condicion = array("idCajaDocumento" => $idCajaDocumento);
                IniciarTransaccion();
                $editar = EditarDatos("cajaDocumento", $datosCajas, $condicion);
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

	function CajasDocumentoEditar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            //No tiene permisos para ejecutar esta acción
			$datosRespuesta["codigo"]=403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
				//CONSULTA PARA TRAER LOS TIPOS DE DOCUMENTOS
				$datosDocumento = TraerDatos('documento');
				//CONSULTAR DOCUMENTO ESPECIFICO DE LA CAJA
				$idCajaDocumento = $this->input->post("idCajaDocumento");
                $datosCajaDocumento = TraerDatos('cajaDocumento', array('md5(idCajaDocumento)' => $idCajaDocumento));

				$cajaDocumento = "";
				foreach ($datosCajaDocumento as $cajaDocumentos){
					$cajaDocumento .= "<tr><td><select class='select2 idDocumento' id='idDocumento' name='idDocumento' >";
					foreach ($datosDocumento as $documento){						
						if($documento->idDocumento==$cajaDocumentos->idDocumentoCajaDocumento){
							$cajaDocumento .= "<option value='".$documento->idDocumento."' selected >".mb_strtoupper($documento->nombreDocumento."(".$documento->aliasDocumento.")",'UTF-8')."</option>";
						} else {
							$cajaDocumento .= "<option value='".$documento->idDocumento."' >".mb_strtoupper($documento->nombreDocumento."(".$documento->aliasDocumento.")",'UTF-8')."</option>";
						}											
					}
					$cajaDocumento .= "</select></td>";												
					$cajaDocumento .= "<td><input type='text' class='form-control inicio numeric' name='inicio' placeholder='Inicio' value='".$cajaDocumentos->inicioCajaDocumento."'></td>";
					$cajaDocumento .= "<td><input type='text' class='form-control final numeric' name='final' placeholder='Final' value='".$cajaDocumentos->finalCajaDocumento."'></td>";
					$cajaDocumento .= "<td><input type='text' class='form-control actual numeric' name='actual' placeholder='Actual' value='".$cajaDocumentos->actualCajaDocumento."'></td>";
					$cajaDocumento .= "<td><input type='date' class='form-control fechaAutorizacion' name='fechaAutorizacion' placeholder='Fecha de autorización' value='".$cajaDocumentos->fechaAutorizacionCajaDocumento."'></td>";
					$cajaDocumento .= "<td><input type='date' class='form-control fechaResolucion' name='fechaResolucion' placeholder='Fecha de resolución' value='".$cajaDocumentos->fechaResolucionCajaDocumento."'></td>";
					$cajaDocumento .= "<td><input type='text' class='form-control numeroResolucion' name='numeroResolucion' placeholder='Número de resolución' value='".$cajaDocumentos->numeroResolucionCajaDocumento."' ></td>";
					$cajaDocumento .= "<td><input type='text' class='form-control serie' name='serie' placeholder='Número de serie' value='".$cajaDocumentos->serieCajaDocumento."' ></td>";
					$cajaDocumento .= "<td><button class='btn btn-block btn-danger CajaDocumentoBorrar' idCajaDocumento='".md5($cajaDocumentos->idCajaDocumento)."' type='button' disabled ><i class='fa fa-trash'></i></button></td>";
					$cajaDocumento .= "</tr>";
				}
				$datosRespuesta["codigo"] = 200;
				$datosRespuesta["cajaDocumento"] = $cajaDocumento;
				echo json_encode($datosRespuesta);
                
            }
        }
    }
    
}
/* End of file Cajas.php */
