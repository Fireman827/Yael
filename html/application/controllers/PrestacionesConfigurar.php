<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PrestacionesConfigurar extends CI_Controller
{

    private $tabla = "cotizacion";
    private $controlador = "PrestacionesConfigurar";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $idCotizacion = md5($this->session->idSucursal);
                $condicionDatos = array('md5(idCotizacion)' => $idCotizacion);
				$datosPrestacionesConfigurar = TraerUnDato($this->tabla, $condicionDatos);
				if($datosPrestacionesConfigurar !== false && $idCotizacion!=""){					
					$titulo = "Administrar Porcentaje de Cotización";
					$datosVista = array(
						"datosPrestacionesConfigurar"=> $datosPrestacionesConfigurar,
						"controlador" => $this->controlador,
						"idCotizacion" => $idCotizacion,
						"titulo" => $titulo,
						"proceso" => "Editar"
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/prestacionesConfigurar.js"
						),
					);
					GblPlantilla("prestacionesConfigurar/PrestacionConfigurar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
                
            } else if ($this->input->method(TRUE) == "POST") {
                $idCotizacion = $this->input->post("idCotizacion");
                $isssCotizacion = $this->input->post("isssCotizacion");
                $afpCotizacion = $this->input->post("afpCotizacion");
                $techoIsssCotizacion = $this->input->post("techoIsssCotizacion");

                if (true) {
                    $datosPrestacionesConfigurar = array(
                        "isssCotizacion" => $isssCotizacion,
                        "afpCotizacion" => $afpCotizacion,
                        "techoIsssCotizacion" => $techoIsssCotizacion,
                        'aleatorioCotizacion' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idSucursalCotizacion)" => $this->session->idSucursal);
                    $guardar = EditarDatos($this->tabla, $datosPrestacionesConfigurar,$condicion);
                    ($guardar == false) ? $error = true : $error = false;
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 500;
                    } else {
                        EjecutarTransaccion();
                        $datosRespuesta["codigo"] = 200;
                    }
                } else {
                    $datosRespuesta["codigo"] = 400;
                }
                echo json_encode($datosRespuesta);
            }          
        }
    }

    function PrestacionesConfigurarEditar($idCotizacion=""){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$condicionDatos = array('md5(idSucursalCotizacion)' => $this->session->idSucursal);
				$datosPrestacionesConfigurar = TraerUnDato($this->tabla, $condicionDatos);
				if($datosPrestacionesConfigurar !== false && $idCotizacion!=""){					
					$titulo = "Administrar Porcentaje Cotización";
					$datosVista = array(
						"datosPrestacionesConfigurar"=> $datosPrestacionesConfigurar,
						"controlador" => $this->controlador,
						"idCotizacion" => $idCotizacion,
						"titulo" => $titulo,
						"proceso" => "Editar"
					);
					$extras = array(
						'css' => array(
						),
						'js' => array(
							"scripts/prestacionesConfigurar.js"
						),
					);
					GblPlantilla("prestacionesConfigurar/PrestacionConfigurarEditar",$datosVista,$extras,$titulo);
				} else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
			} else if($this->input->method(TRUE) == "POST"){
				$idCotizacion = $this->input->post("idCotizacion");
                $isssCotizacion = $this->input->post("isssCotizacion");
                $afpCotizacion = $this->input->post("afpCotizacion");
                $techoIsssCotizacion = $this->input->post("techoIsssCotizacion");
				
				if(true){
					$datosCotizacion = array(
                        "isssCotizacion" => $isssCotizacion,
                        "afpCotizacion" => $afpCotizacion,
                        "techoIsssCotizacion" => $techoIsssCotizacion,
                        'aleatorioCotizacion' => uniqid()
					);
					IniciarTransaccion();
					$condicion = array("idSucursalCotizacion" => $this->session->idSucursal);
					$editar = EditarDatos($this->tabla,$datosCotizacion,$condicion);
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

}
/* End of file PrestacionesConfigurar.php */
