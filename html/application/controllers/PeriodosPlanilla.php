<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PeriodosPlanilla extends CI_Controller
{

    private $tabla = "periodoPlanilla";
    private $controlador = "PeriodosPlanilla";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            /*
            $datosPeriodosPlanilla = TraerUnDato($this->tabla, array('vencidoPeriodoPlanilla' => 'Vigente'));
            $idPeriodoPlanilla = md5('0');
            if($datosPeriodosPlanilla->idPeriodoPlanilla!=0){
                $idPeriodoPlanilla = md5($datosPeriodosPlanilla->idPeriodoPlanilla);
            }*/
            $titulo = "Administrar Periodo";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-calendar-times",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'PeriodosPlanillaEditar',
                        'txt' => 'Finalizar Periodo',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'periodoPlanilla'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Desde" => 1,
                    "Hasta" => 1,
                    "Estado" => 1,
                    "Acciones" => 1,
                ),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
            );
            $extras = array(
                'css' => array(),
                'js' => array(
                    "scripts/periodosPlanilla.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function PeriodosPlanillaMostrar(){
        // Espacio propio del plugin data tabla
        $draw = intval($this->input->post("draw"));
        $desdeFilas = intval($this->input->post("start"));
        $cantidadFilas = intval($this->input->post("length"));

        $order = $this->input->post("order");
        $busquedaAreglo = $this->input->post("search");
        $busquedaParametro = $busquedaAreglo['value'];
        $col = 1;
        $ordenDireccion = "desc";
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
            0 => 'idPeriodoPlanilla',
            1 => 'desdePeriodoPlanilla',
            2 => 'hastaPeriodoPlanilla',          
            3 => 'vencidoPeriodoPlanilla'          
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
		$condicion = array('idSucursalPeriodoPlanilla' => $sucursal);
		$PeriodosPlanilla = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($PeriodosPlanilla);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($PeriodosPlanilla != 0) {
            $datosMostrar = array();
            foreach ($PeriodosPlanilla as $PeriodoPlanilla) {
                $vencidoPeriodoPlanilla = $PeriodoPlanilla->vencidoPeriodoPlanilla;
                if ($vencidoPeriodoPlanilla == 'Vigente') {
                    $vencidoSpan = "<span class='badge badge-primary font-bold'>Vigente<span>";
                } else {
                    $vencidoSpan = "<span class='badge badge-success font-bold'>Finalizado<span>";
                }
                /*
                $estadoPeriodoPlanilla = $PeriodoPlanilla->estadoPeriodoPlanilla;
                if ($estadoPeriodoPlanilla == 'Activo') {
                    $estadoTxt = "Desactivar";
                    $estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
                    $estadoIcon = "fa fa fa-toggle-on";
                } else {
                    $estadoTxt = "Activar";
                    $estadoSpan = "<span class='badge badge-danger font-bold'>Inactivo<span>";
                    $estadoIcon = "fa fa-toggle-off";
                }
                */
                $menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

                /*$funcion = "PeriodosPlanillaEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idPeriodoPlanilla=" . md5($PeriodoPlanilla->idPeriodoPlanilla) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "PeriodosPlanillaCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idPeriodoPlanilla=" . md5($PeriodoPlanilla->idPeriodoPlanilla) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }*/
                $funcion = "PeriodosPlanillaEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idPeriodoPlanilla=" . md5($PeriodoPlanilla->idPeriodoPlanilla) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $PeriodoPlanilla->idPeriodoPlanilla,
                    $PeriodoPlanilla->desdePeriodoPlanilla,
                    $PeriodoPlanilla->hastaPeriodoPlanilla,
                    $vencidoSpan,
                    $menuOpciones
                );
            }
            $totalPeriodosPlanilla = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalPeriodosPlanilla,
                "recordsFiltered" => $totalPeriodosPlanilla,
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
    function PeriodosPlanillaAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Periodo";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-calendar-times",
                    "controlador" => $this->controlador,
                    "proceso" => "Editar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/periodosPlanilla.js"
                    ),
                );
                $this->load->view("periodosPlanilla/PeriodoPlanillaAgregar",$datosVista);
            } else if ($this->input->method(TRUE) == "POST") {
                $desdePeriodoPlanilla = $this->input->post("desdePeriodoPlanilla");
                $hastaPeriodoPlanilla = $this->input->post("hastaPeriodoPlanilla");
                $fechaInicioPagoPeriodoPlanilla = $this->input->post("fechaInicioPagoPeriodoPlanilla");
                $fechaFinPagoPeriodoPlanilla = $this->input->post("fechaFinPagoPeriodoPlanilla");
                $condicionExiste = array(
                    "hastaPeriodoPlanilla" => $hastaPeriodoPlanilla,
                    "desdePeriodoPlanilla" => $desdePeriodoPlanilla,
                    "idSucursalPeriodoPlanilla" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosPeriodosPlanilla = array(
                        "idSucursalPeriodoPlanilla" => $this->session->idSucursal,
                        "desdePeriodoPlanilla" => $desdePeriodoPlanilla,
                        "hastaPeriodoPlanilla" => $hastaPeriodoPlanilla,
                        "mesPeriodoPlanilla" => date('n', strtotime($desdePeriodoPlanilla)),
                        "anioPeriodoPlanilla" => date('Y', strtotime($desdePeriodoPlanilla)),
                        "descripcionPeriodoPlanilla" => date('F Y', strtotime($desdePeriodoPlanilla)),
                        "fechaInicioPagoPeriodoPlanilla" => $fechaInicioPagoPeriodoPlanilla,
                        "fechaFinPagoPeriodoPlanilla" => $fechaFinPagoPeriodoPlanilla,
                        "vencidoPeriodoPlanilla" => 'Vigente'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosPeriodosPlanilla);
                    ($guardar == false) ? $error = true : $error = false;
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 402;
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
    function PeriodosPlanillaEditar($idPeriodoPlanilla = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $idPeriodoPlanilla = md5($idPeriodoPlanilla);
            if ($this->input->method(TRUE) == "GET") {
                if($idPeriodoPlanilla!=md5('0')){
                    $datosPeriodosPlanilla = TraerUnDato($this->tabla, array('md5(idPeriodoPlanilla)' => $idPeriodoPlanilla));
                    $titulo = "Finalizar Periodo";
                    $datosVista = array(
                        "titulo" => $titulo,
                        "icono" => "fas fa-calendar-times",
                        "controlador" => $this->controlador,
                        "idPeriodoPlanilla" => $idPeriodoPlanilla,
                        "proceso" => "Editar",
                        "datosPeriodosPlanilla" => $datosPeriodosPlanilla
                    );
                    $extras = array(
                        'css' => array(),
                        'js' => array(
                            "scripts/periodosPlanilla.js"
                        ),
                    );
                    $this->load->view("periodosPlanilla/PeriodoPlanillaEditar",$datosVista);
                } else {
                    $titulo = "Agregar Periodo";
                    $datosVista = array(
                        "titulo" => $titulo,
                        "icono" => "fas fa-calendar-times",
                        "controlador" => $this->controlador,
                        "proceso" => "Editar",
                        "idPeriodoPlanilla" => $idPeriodoPlanilla,
                    );
                    $extras = array(
                        'css' => array(),
                        'js' => array(
                            "scripts/periodosPlanilla.js"
                        ),
                    );
                    $this->load->view("periodosPlanilla/PeriodoPlanillaAgregar",$datosVista);
                }
            } else if ($this->input->method(TRUE) == "POST") {
                $idPeriodoPlanilla = $this->input->post("idPeriodoPlanilla");                             
                $fechaInicioPagoPeriodoPlanilla = $this->input->post("fechaInicioPagoPeriodoPlanilla");
                $fechaFinPagoPeriodoPlanilla = $this->input->post("fechaFinPagoPeriodoPlanilla");

                if($idPeriodoPlanilla!=md5('0')){
                    $desdePeriodoPlanilla = $this->input->post("desdePeriodoPlanilla");
                    $hastaPeriodoPlanilla = $this->input->post("hastaPeriodoPlanilla"); 
                    $condicionExiste = array(
                        "desdePeriodoPlanilla" => $desdePeriodoPlanilla,
                        "hastaPeriodoPlanilla" => $hastaPeriodoPlanilla,
                        "idSucursalPeriodoPlanilla" => $this->session->idSucursal
                    );
                    $existe = ExistenDatos($this->tabla, $condicionExiste);
                    if (true) {                   
                        IniciarTransaccion();
                        $condicion = array("md5(idPeriodoPlanilla)" => $idPeriodoPlanilla);
                        $datosPeriodosPlanilla = array(
                            "vencidoPeriodoPlanilla" => 'Finalizado'
                        );
                        $editar = EditarDatos($this->tabla,$datosPeriodosPlanilla,$condicion);
                        if($editar){
                            $datosPeriodosPlanilla = array(
                                "idSucursalPeriodoPlanilla" => $this->session->idSucursal,
                                "desdePeriodoPlanilla" => $fechaInicioPagoPeriodoPlanilla,
                                "hastaPeriodoPlanilla" => $fechaFinPagoPeriodoPlanilla,
                                "mesPeriodoPlanilla" => date('n', strtotime($fechaInicioPagoPeriodoPlanilla)),
                                "anioPeriodoPlanilla" => date('Y', strtotime($fechaInicioPagoPeriodoPlanilla)),
                                "descripcionPeriodoPlanilla" => date('F Y', strtotime($fechaInicioPagoPeriodoPlanilla)),
                                "fechaInicioPagoPeriodoPlanilla" => $fechaInicioPagoPeriodoPlanilla,
                                "fechaFinPagoPeriodoPlanilla" => $fechaFinPagoPeriodoPlanilla,
                                "vencidoPeriodoPlanilla" => 'Vigente'
                            );
                            $guardar = GuardarDatos($this->tabla, $datosPeriodosPlanilla);
                            ($guardar == false) ? $error = true : $error = false;
                            if ($error) {
                                DeshacerTransaccion();
                                $datosRespuesta["codigo"] = 402;
                            } else {                       
                                EjecutarTransaccion();
                                $datosRespuesta["codigo"] = 200;
                            }
                        } else {
                            //La acción no pudo ser realizada
                            DeshacerTransaccion();						
                            $datosRespuesta["codigo"]=402;
                        }                  
                    } else {
                        $datosRespuesta["codigo"] = 400;
                    }
                    echo json_encode($datosRespuesta);
                } else {
                    $datosPeriodosPlanilla = array(
                        "idSucursalPeriodoPlanilla" => $this->session->idSucursal,
                        "desdePeriodoPlanilla" => $fechaInicioPagoPeriodoPlanilla,
                        "hastaPeriodoPlanilla" => $fechaFinPagoPeriodoPlanilla,
                        "mesPeriodoPlanilla" => date('n', strtotime($fechaInicioPagoPeriodoPlanilla)),
                        "anioPeriodoPlanilla" => date('Y', strtotime($fechaInicioPagoPeriodoPlanilla)),
                        "descripcionPeriodoPlanilla" => date('F Y', strtotime($fechaInicioPagoPeriodoPlanilla)),
                        "fechaInicioPagoPeriodoPlanilla" => $fechaInicioPagoPeriodoPlanilla,
                        "fechaFinPagoPeriodoPlanilla" => $fechaFinPagoPeriodoPlanilla,
                        "vencidoPeriodoPlanilla" => 'Vigente'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosPeriodosPlanilla);
                    ($guardar == false) ? $error = true : $error = false;
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 402;
                    } else {
                        EjecutarTransaccion();
                        $datosRespuesta["codigo"] = 200;
                    }
                    echo json_encode($datosRespuesta);
                }             
            } 
        }
    }
    function PeriodosPlanillaCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idPeriodoPlanilla = $this->input->post("idPeriodoPlanilla");
                $condicionDatos = array(
                    'md5(idPeriodoPlanilla)' => $idPeriodoPlanilla,
                    'estadoPeriodoPlanilla' => 'Activo',
                );
                $activoPeriodoPlanilla = ExistenDatos($this->tabla, $condicionDatos);

                ($activoPeriodoPlanilla == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosPeriodosPlanilla = array(
                    "estadoPeriodoPlanilla" => $nuevoEstado
                );
                $condicion = array("md5(idPeriodoPlanilla)" => $idPeriodoPlanilla);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosPeriodosPlanilla, $condicion);
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
    function PeriodosPlanillaEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idPeriodoPlanilla = $this->input->post("idPeriodoPlanilla");
                $datosPeriodosPlanilla = array(
                    "estadoPeriodoPlanilla" => 'Borrado'
                );
                $condicion = array("md5(idPeriodoPlanilla)" => $idPeriodoPlanilla);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosPeriodosPlanilla, $condicion);
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

    function PeriodosPlanillaVigente(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {

                $datosPeriodosPlanilla = TraerUnDato($this->tabla, array('vencidoPeriodoPlanilla' => 'Vigente'));

                if ($datosPeriodosPlanilla) {
                    $datosRespuesta["idPeriodoPlanilla"] = $datosPeriodosPlanilla->idPeriodoPlanilla;
                    $datosRespuesta["codigo"] = 200;
                } else {
                    $datosRespuesta["codigo"] = 500;
                }
            }
        }
        echo json_encode($datosRespuesta);
    }
}
/* End of file PeriodosPlanilla.php */
