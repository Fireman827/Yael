<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InstitucionFinanciera extends CI_Controller
{

    private $tabla = "empleadoInstitucionFinanciera";
    private $controlador = "InstitucionFinanciera";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Institucion Financiera";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-landmark",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'InstitucionFinancieraAgregar',
                        'txt' => 'Agregar Institución Financiera',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'institucionFinancieraAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 2,
                    "Nombre" => 3,
                    "Descripción" => 3,
                    "Estado" => 2,
                    "Acciones" => 2,
                ),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
            );
            $extras = array(
                'css' => array(),
                'js' => array(
                    "scripts/institucionFinanciera.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function InstitucionFinancieraMostrar(){
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
            0 => 'idInstitucionFinanciera',
            1 => 'nombreInstitucionFinanciera'
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
		$condicion = array('idSucursalInstitucionFinanciera' => $sucursal, 'estadoInstitucionFinanciera !=' => 'Borrado');
		$InstitucionesFinanciera = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($InstitucionesFinanciera);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($InstitucionesFinanciera != 0) {
            $datosMostrar = array();
            foreach ($InstitucionesFinanciera as $InstitucionFinanciera) {
                $estadoInstitucionFinanciera = $InstitucionFinanciera->estadoInstitucionFinanciera;
                if ($estadoInstitucionFinanciera == 'Activo') {
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

                $funcion = "InstitucionFinancieraEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idInstitucionFinanciera=" . md5($InstitucionFinanciera->idInstitucionFinanciera) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "InstitucionFinancieraCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idInstitucionFinanciera=" . md5($InstitucionFinanciera->idInstitucionFinanciera) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "InstitucionFinancieraEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idInstitucionFinanciera=" . md5($InstitucionFinanciera->idInstitucionFinanciera) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $InstitucionFinanciera->idInstitucionFinanciera,
                    $InstitucionFinanciera->nombreInstitucionFinanciera,
                    $InstitucionFinanciera->descripcionInstitucionFinanciera,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalInstitucionFinanciera = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalInstitucionFinanciera,
                "recordsFiltered" => $totalInstitucionFinanciera,
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
    function InstitucionFinancieraAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Presentación";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-landmark",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/institucionFinanciera.js"
                    ),
                );
                $this->load->view("institucionFinanciera/InstitucionFinancieraAgregar",$datosVista);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreInstitucionFinanciera = $this->input->post("nombreInstitucionFinanciera");
                $descripcionInstitucionFinanciera = $this->input->post("descripcionInstitucionFinanciera");
                $condicionExiste = array(
                    "nombreInstitucionFinanciera" => $nombreInstitucionFinanciera,
                    "idSucursalInstitucionFinanciera" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosInstitucionFinanciera = array(
                        "idSucursalInstitucionFinanciera" => $this->session->idSucursal,
                        "nombreInstitucionFinanciera" => $nombreInstitucionFinanciera,
                        "descripcionInstitucionFinanciera" => $descripcionInstitucionFinanciera,
                        "estadoInstitucionFinanciera" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosInstitucionFinanciera);
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
    function InstitucionFinancieraEditar($idInstitucionFinanciera = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosInstitucionFinanciera = TraerUnDato($this->tabla, array('md5(idInstitucionFinanciera)' => $idInstitucionFinanciera));
                $titulo = "Editar Institución Financiera";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-landmark",
                    "controlador" => $this->controlador,
                    "idInstitucionFinanciera" => $idInstitucionFinanciera,
                    "proceso" => "Editar",
                    "datosInstitucionFinanciera" => $datosInstitucionFinanciera
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/institucionFinanciera.js"
                    ),
                );
                $this->load->view("institucionFinanciera/InstitucionFinancieraEditar",$datosVista);
            } else if ($this->input->method(TRUE) == "POST") {
                $idInstitucionFinanciera = $this->input->post("idInstitucionFinanciera");
                $nombreInstitucionFinanciera = $this->input->post("nombreInstitucionFinanciera");
                $descripcionInstitucionFinanciera = $this->input->post("descripcionInstitucionFinanciera");
                $condicionExiste = array(
                    "nombreInstitucionFinanciera" => $nombreInstitucionFinanciera,
                    "idSucursalInstitucionFinanciera" => $this->session->idSucursal,
                    "md5(idInstitucionFinanciera) !="=> $idInstitucionFinanciera
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosInstitucionFinanciera = array(
                        "idSucursalInstitucionFinanciera" => $this->session->idSucursal,
                        "nombreInstitucionFinanciera" => $nombreInstitucionFinanciera,
                        "descripcionInstitucionFinanciera" => $descripcionInstitucionFinanciera,
                        'aleatorioInstitucionFinanciera' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idInstitucionFinanciera)" => $idInstitucionFinanciera);
                    $guardar = EditarDatos($this->tabla, $datosInstitucionFinanciera,$condicion);
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
    function InstitucionFinancieraCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idInstitucionFinanciera = $this->input->post("idInstitucionFinanciera");
                $condicionDatos = array(
                    'md5(idInstitucionFinanciera)' => $idInstitucionFinanciera,
                    'estadoInstitucionFinanciera' => 'Activo',
                );
                $activoInstitucionFinanciera = ExistenDatos($this->tabla, $condicionDatos);

                ($activoInstitucionFinanciera == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosInstitucionFinanciera = array(
                    "estadoInstitucionFinanciera" => $nuevoEstado
                );
                $condicion = array("md5(idInstitucionFinanciera)" => $idInstitucionFinanciera);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosInstitucionFinanciera, $condicion);
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
    function InstitucionFinancieraEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idInstitucionFinanciera = $this->input->post("idInstitucionFinanciera");
                $datosInstitucionFinanciera = array(
                    "estadoInstitucionFinanciera" => 'Borrado'
                );
                $condicion = array("md5(idInstitucionFinanciera)" => $idInstitucionFinanciera);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosInstitucionFinanciera, $condicion);
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
/* End of file InstitucionFinanciera.php */
