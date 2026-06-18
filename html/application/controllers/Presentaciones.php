<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Presentaciones extends CI_Controller
{

    private $tabla = "presentacion";
    private $controlador = "Presentaciones";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Presentaciones";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-at",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'PresentacionesAgregar',
                        'txt' => 'Agregar Presentación',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'presentacionAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 2,
                    "Abreviatura" => 2,
                    "Descripción" => 3,
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
                    "scripts/presentaciones.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function PresentacionesMostrar(){
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
            0 => 'idPresentacion',
            1 => 'nombrePresentacion',
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
		$condicion = array('idSucursalPresentacion' => $sucursal, 'estadoPresentacion !=' => 'Borrado');
		$Presentaciones = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($Presentaciones);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($Presentaciones != 0) {
            $datosMostrar = array();
            foreach ($Presentaciones as $Presentacion) {
                $estadoPresentacion = $Presentacion->estadoPresentacion;
                if ($estadoPresentacion == 'Activo') {
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

                $funcion = "PresentacionesEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idPresentacion=" . md5($Presentacion->idPresentacion) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "PresentacionesCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idPresentacion=" . md5($Presentacion->idPresentacion) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "PresentacionesEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idPresentacion=" . md5($Presentacion->idPresentacion) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $Presentacion->idPresentacion,
                    $Presentacion->nombrePresentacion,
                    $Presentacion->unidadPresentacion,
                    $Presentacion->descripcionPresentacion,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalPresentaciones = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalPresentaciones,
                "recordsFiltered" => $totalPresentaciones,
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
    function PresentacionesAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Presentación";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-at",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/presentaciones.js"
                    ),
                );
                $this->load->view("presentaciones/PresentacionAgregar",$datosVista);
                // GblPlantilla("Presentaciones/PresentacionesAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombrePresentacion = $this->input->post("nombrePresentacion");
                $unidadPresentacion = $this->input->post("unidadPresentacion");
                $descripcionPresentacion = $this->input->post("descripcionPresentacion");
                $condicionExiste = array(
                    "nombrePresentacion" => $nombrePresentacion,
                    "idSucursalPresentacion" => $this->session->idSucursal,
                    "estadoPresentacion !=" => 'Borrado'
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosPresentaciones = array(
                        "idSucursalPresentacion" => $this->session->idSucursal,
                        "nombrePresentacion" => $nombrePresentacion,
                        "unidadPresentacion" => $unidadPresentacion,
                        "descripcionPresentacion" => $descripcionPresentacion,
                        "estadoPresentacion" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosPresentaciones);
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
    function PresentacionesEditar($idPresentacion = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosPresentaciones = TraerUnDato($this->tabla, array('md5(idPresentacion)' => $idPresentacion));
                $titulo = "Editar Presentación";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-at",
                    "controlador" => $this->controlador,
                    "idPresentacion" => $idPresentacion,
                    "proceso" => "Editar",
                    "datosPresentaciones" => $datosPresentaciones
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/presentaciones.js"
                    ),
                );
                $this->load->view("presentaciones/PresentacionEditar",$datosVista);
                //GblPlantilla("Presentaciones/PresentacionesEditar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $idPresentacion = $this->input->post("idPresentacion");
                $nombrePresentacion = $this->input->post("nombrePresentacion");
                $unidadPresentacion = $this->input->post("unidadPresentacion");
                $descripcionPresentacion = $this->input->post("descripcionPresentacion");
                $condicionExiste = array(
                    "nombrePresentacion" => $nombrePresentacion,
                    "idSucursalPresentacion" => $this->session->idSucursal,
                    "md5(idPresentacion) !="=> $idPresentacion,
                    "estadoPresentacion !="=> 'Borrado',
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosPresentaciones = array(
                        "idSucursalPresentacion" => $this->session->idSucursal,
                        "nombrePresentacion" => $nombrePresentacion,
                        "unidadPresentacion" => $unidadPresentacion,
                        "descripcionPresentacion" => $descripcionPresentacion,
                        'aleatorioPresentacion' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idPresentacion)" => $idPresentacion);
                    $guardar = EditarDatos($this->tabla, $datosPresentaciones,$condicion);
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
    function PresentacionesCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idPresentacion = $this->input->post("idPresentacion");
                $condicionDatos = array(
                    'md5(idPresentacion)' => $idPresentacion,
                    'estadoPresentacion' => 'Activo',
                );
                $activoPresentacion = ExistenDatos($this->tabla, $condicionDatos);

                ($activoPresentacion == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosPresentaciones = array(
                    "estadoPresentacion" => $nuevoEstado
                );
                $condicion = array("md5(idPresentacion)" => $idPresentacion);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosPresentaciones, $condicion);
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
    function PresentacionesEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idPresentacion = $this->input->post("idPresentacion");
                $datosPresentaciones = array(
                    "estadoPresentacion" => 'Borrado'
                );
                $condicion = array("md5(idPresentacion)" => $idPresentacion);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosPresentaciones, $condicion);
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
/* End of file Presentaciones.php */
