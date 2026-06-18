<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Covers extends CI_Controller
{

    private $tabla = "cover";
    private $controlador = "Covers";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Covers";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-print",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'CoversAgregar',
                        'txt' => 'Agregar Cover',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'CoverAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 3,
                    "Recurso Compartido" => 3,
                    "Estado" => 2,
                    "Acciones" => 1,
                ),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
            );
            $extras = array(
                'css' => array(),
                'js' => array(
                    "scripts/Covers.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function CoversMostrar(){
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
            0 => 'nombreCover'
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
		$condicion = array('idSucursalCover' => $sucursal, 'estadoCover !=' => 'Borrado');
		$Covers = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($Covers);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($Covers != 0) {
            $datosMostrar = array();
            foreach ($Covers as $Cover) {
                $estadoCover = $Cover->estadoCover;
                if ($estadoCover == 'Activo') {
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

                $funcion = "CoversEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idCover=" . md5($Cover->idCover) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "CoversCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idCover=" . md5($Cover->idCover) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "CoversEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idCover=" . md5($Cover->idCover) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $Cover->idCover,
                    $Cover->nombreCover,
                    $Cover->recursoCompartidoCover,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalCovers = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalCovers,
                "recordsFiltered" => $totalCovers,
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
    function CoversAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Cover";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-print",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/Covers.js"
                    ),
                );
                $this->load->view("Covers/CoverAgregar",$datosVista);
                // GblPlantilla("Covers/CoversAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreCover = $this->input->post("nombreCover");
                $recursoCompartidoCover = $this->input->post("recursoCompartidoCover");
                $condicionExiste = array(
                    "nombreCover" => $nombreCover,
                    "idSucursalCover" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosCovers = array(
                        "idSucursalCover" => $this->session->idSucursal,
                        "nombreCover" => $nombreCover,
                        "recursoCompartidoCover" => $recursoCompartidoCover,
                        "estadoCover" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosCovers);
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
    function CoversEditar($idCover = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosCovers = TraerUnDato($this->tabla, array('md5(idCover)' => $idCover));
                $titulo = "Editar Cover";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-print",
                    "controlador" => $this->controlador,
                    "idCover" => $idCover,
                    "proceso" => "Editar",
                    "datosCovers" => $datosCovers
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/Covers.js"
                    ),
                );
                $this->load->view("Covers/CoverEditar",$datosVista);
                //GblPlantilla("Covers/CoversEditar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $idCover = $this->input->post("idCover");
                $nombreCover = $this->input->post("nombreCover");
                $recursoCompartidoCover = $this->input->post("recursoCompartidoCover");
                $condicionExiste = array(
                    "nombreCover" => $nombreCover,
                    "idSucursalCover" => $this->session->idSucursal,
                    "md5(idCover) !="=> $idCover
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosCovers = array(
                        "idSucursalCover" => $this->session->idSucursal,
                        "nombreCover" => $nombreCover,
                        "recursoCompartidoCover" => $recursoCompartidoCover,
                        'aleatorioCover' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idCover)" => $idCover);
                    $guardar = EditarDatos($this->tabla, $datosCovers,$condicion);
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
    function CoversCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCover = $this->input->post("idCover");
                $condicionDatos = array(
                    'md5(idCover)' => $idCover,
                    'estadoCover' => 'Activo',
                );
                $activoCover = ExistenDatos($this->tabla, $condicionDatos);

                ($activoCover == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosCovers = array(
                    "estadoCover" => $nuevoEstado
                );
                $condicion = array("md5(idCover)" => $idCover);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosCovers, $condicion);
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
    function CoversEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCover = $this->input->post("idCover");
                $datosCovers = array(
                    "estadoCover" => 'Borrado'
                );
                $condicion = array("md5(idCover)" => $idCover);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosCovers, $condicion);
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
/* End of file Covers.php */
