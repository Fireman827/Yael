<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContratosClausula extends CI_Controller {

	private $tabla = "contratoClausula";
	private $controlador = "ContratosClausula";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}

	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			$titulo = "Clausulas de Contratos";
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fas fa-file",
				"botones" => array(
					array(
						"icono"=> "fa fa-plus",
						'controlador' => $this->controlador,
						'url' => 'ContratosClausulaAgregar',
						'txt' => 'Agregar Clausula',
						'posicion' => 'right', // left, right
						'tipo' => GblTraerConfiguracion('colorComponentes'),//primary, success, info, warning, danger
						'modal' => false,
						'id' => ''
					),
				),
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
					"scripts/contratosClausula.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function ContratosClausulaMostrar(){
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
			0 => 'nombreContratoClausula'
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
		$condicion = array('idSucursalContratoClausula' => $sucursal,'estadoContratoClausula!=' => 'Borrado');
		$ContratosClausula = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
		//print_r($ContratosClausula);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($ContratosClausula!= 0){
			$datosMostrar = array();
			foreach ($ContratosClausula as $ContratoClausula) {
                $estadoContratoClausula = $ContratoClausula->estadoContratoClausula;
                if ($estadoContratoClausula == 'Activo') {
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

                $funcion = "ContratosClausulaEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($ContratoClausula->idContratoClausula)."' ><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "ContratosClausulaCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idContratoClausula=" . md5($ContratoClausula->idContratoClausula) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "ContratosClausulaEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idContratoClausula=" . md5($ContratoClausula->idContratoClausula) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $ContratoClausula->idContratoClausula,
                    $ContratoClausula->nombreContratoClausula,
                    $estadoSpan,
                    $menuOpciones
                );
            }
			$totalContratosClausula = TraerTotalDatos($this->tabla,$condicion);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalContratosClausula,
				"recordsFiltered" => $totalContratosClausula,
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

	function ContratosClausulaAgregar(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			
			if($this->input->method(TRUE) == "GET"){
	
				$titulo = "Agregar Clausula de Contrato";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-file",
					"controlador"=> $this->controlador,
					"proceso"=> "Agregar"
				);
				$extras = array(
					'css' => array(
						"vendors/plugins/ckeditor_full/skins/moono-lisa/editor_gecko.css"
					),
					'js' => array(
						"scripts/contratosClausula.js",
                        "vendors/plugins/ckeditor_full/ckeditor.js"
					),
				);
				GblPlantilla("contratosClausula/ContratoClausulaAgregar",$datosVista,$extras,$titulo);
			} else if($this->input->method(TRUE) == "POST"){
                $idSucursalContratoClausula = $this->session->idSucursal;
				$nombreContratoClausula = $this->input->post("nombreContratoClausula");
				$descripcionContratoClausula = $this->input->post("descripcionContratoClausula");

				$condicionExiste = array('nombreContratoClausula' => $nombreContratoClausula,'idSucursalContratoClausula'=>$idSucursalContratoClausula);
				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosContratoClausula  = array(
						"nombreContratoClausula"=>$nombreContratoClausula,
						"descripcionContratoClausula"=>$descripcionContratoClausula,
						"estadoContratoClausula"=> 'Activo',
						"aleatorioContratoClausula" => uniqid(),
						"idSucursalContratoClausula"=>$this->session->idSucursal
					);
					IniciarTransaccion();
					$guardar = GuardarDatos($this->tabla,$datosContratoClausula);
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

	function ContratosClausulaEditar($idContratoClausula=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idContratoClausula)' => $idContratoClausula);
				$datosContratosClausula = TraerUnDato($this->tabla, $condicionDatos);
				if($datosContratosClausula !== false && $idContratoClausula!=""){					
					$titulo = "Editar Clausula";
					$datosVista = array(
						"datosContratosClausula"=> $datosContratosClausula,
						"controlador" => $this->controlador,
						"idContratoClausula" => $idContratoClausula,
						"titulo" => $titulo,
						"proceso" => "Editar"
					);
					$extras = array(
						'css' => array(
							"vendors/plugins/ckeditor_full/skins/moono-lisa/editor_gecko.css"
						),
						'js' => array(
							"scripts/contratosClausula.js",
                            "vendors/plugins/ckeditor_full/ckeditor.js"
						),
					);
					GblPlantilla("contratosClausula/ContratoClausulaEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idContratoClausula = $this->input->post("idContratoClausula");
				$nombreContratoClausula = $this->input->post("nombreContratoClausula");
				$descripcionContratoClausula = $this->input->post("descripcionContratoClausula");
				
				$condicionExiste = array(
					'nombreContratoClausula' => $nombreContratoClausula,
					'idSucursalContratoClausula' => $this->session->idSucursal,
					'idContratoClausula!=' => $idContratoClausula
				);

				$existe = ExistenDatos($this->tabla, $condicionExiste);
				if($existe==0){
					$datosContratoClausula = array(
						"nombreContratoClausula"=>$nombreContratoClausula,
						"descripcionContratoClausula"=>$descripcionContratoClausula,
						"idSucursalContratoClausula"=>$this->session->idSucursal,
						"aleatorioContratoClausula"=>uniqid()
					);
					IniciarTransaccion();
					$condicion = array("idContratoClausula" => $idContratoClausula);
					$editar = EditarDatos($this->tabla,$datosContratoClausula,$condicion);
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

	function ContratosClausulaCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContratoClausula = $this->input->post("idContratoClausula");
                $condicionDatos = array(
                    'md5(idContratoClausula)' => $idContratoClausula,
                    'estadoContratoClausula' => 'Activo',
                );
                $activoContratoClausula = ExistenDatos($this->tabla, $condicionDatos);

                ($activoContratoClausula == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosContratosClausula = array(
                    "estadoContratoClausula" => $nuevoEstado
                );
                $condicion = array("md5(idContratoClausula)" => $idContratoClausula);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosContratosClausula, $condicion);
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

    function ContratosClausulaEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContratoClausula = $this->input->post("idContratoClausula");
                $datosContratosClausula = array(
                    "estadoContratoClausula" => 'Borrado'
                );
                $condicion = array("md5(idContratoClausula)" => $idContratoClausula);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosContratosClausula, $condicion);
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
/* End of file ContratosClausula.php */
