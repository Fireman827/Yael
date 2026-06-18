<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InsumosCategoria extends CI_Controller
{

    private $tabla = "insumoCategoria";
    private $controlador = "InsumosCategoria";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Categoria Insumos";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-drumstick-bite",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'InsumosCategoriaAgregar',
                        'txt' => 'Agregar Categoria',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'insumosCategoriaAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 2,
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
                    "scripts/insumosCategoria.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function InsumosCategoriaMostrar(){
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
            0 => 'idInsumoCategoria',
            1 => 'nombreInsumoCategoria',
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
		$condicion = array('idSucursalInsumoCategoria' => $sucursal, 'estadoInsumoCategoria !=' => 'Borrado');
		$InsumosCategoria = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($InsumosCategoria);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($InsumosCategoria != 0) {
            $datosMostrar = array();
            foreach ($InsumosCategoria as $insumoCategoria) {
                $estadoinsumoCategoria = $insumoCategoria->estadoInsumoCategoria;
                if ($estadoinsumoCategoria == 'Activo') {
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

                $funcion = "InsumosCategoriaEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idInsumoCategoria=" . md5($insumoCategoria->idInsumoCategoria) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "InsumosCategoriaCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idInsumoCategoria=" . md5($insumoCategoria->idInsumoCategoria) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "InsumosCategoriaEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idInsumoCategoria=" . md5($insumoCategoria->idInsumoCategoria) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $insumoCategoria->idInsumoCategoria,
                    $insumoCategoria->nombreInsumoCategoria,
                    $insumoCategoria->descripcionInsumoCategoria,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalInsumosCategoria = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalInsumosCategoria,
                "recordsFiltered" => $totalInsumosCategoria,
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
    function InsumosCategoriaAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Categoria Insumo";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-drumstick-bite",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/InsumosCategoria.js"
                    ),
                );
                $this->load->view("insumosCategoria/InsumosCategoriaAgregar",$datosVista);
                // GblPlantilla("InsumosCategoria/InsumosCategoriaAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreinsumoCategoria = $this->input->post("nombreInsumosCategoria");
                $descripcioninsumoCategoria = $this->input->post("descripcionInsumosCategoria");
                $condicionExiste = array(
                    "nombreInsumoCategoria" => $nombreinsumoCategoria,
                    "idSucursalInsumoCategoria" => $this->session->idSucursal,
                    "estadoInsumoCategoria !=" => "Borrado"
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosInsumosCategoria = array(
                        "idSucursalInsumoCategoria" => $this->session->idSucursal,
                        "nombreInsumoCategoria" => $nombreinsumoCategoria,
                        "descripcionInsumoCategoria" => $descripcioninsumoCategoria,
                        "estadoInsumoCategoria" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosInsumosCategoria);
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
    function InsumosCategoriaEditar($idinsumoCategoria = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosInsumosCategoria = TraerUnDato($this->tabla, array('md5(idInsumoCategoria)' => $idinsumoCategoria));
                $titulo = "Editar Categoria Insumo";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-drumstick-bite",
                    "controlador" => $this->controlador,
                    "idInsumoCategoria" => $idinsumoCategoria,
                    "proceso" => "Editar",
                    "datosInsumosCategoria" => $datosInsumosCategoria
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/InsumosCategoria.js"
                    ),
                );
                $this->load->view("insumosCategoria/InsumosCategoriaEditar",$datosVista);
                //GblPlantilla("InsumosCategoria/InsumosCategoriaEditar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $idinsumoCategoria = $this->input->post("idInsumoCategoria");
                $nombreinsumoCategoria = $this->input->post("nombreInsumosCategoria");
                $descripcioninsumoCategoria = $this->input->post("descripcionInsumosCategoria");
                $condicionExiste = array(
                    "nombreInsumoCategoria" => $nombreinsumoCategoria,
                    "idSucursalInsumoCategoria" => $this->session->idSucursal,
                    "md5(idinsumoCategoria) !="=> $idinsumoCategoria,
                    "estadoInsumoCategoria !=" => "Borrado"
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosInsumosCategoria = array(
                        "nombreinsumoCategoria" => $nombreinsumoCategoria,
                        "descripcioninsumoCategoria" => $descripcioninsumoCategoria,
                        'aleatorioinsumoCategoria' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idInsumoCategoria)" => $idinsumoCategoria);
                    $guardar = EditarDatos($this->tabla, $datosInsumosCategoria,$condicion);
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
    function InsumosCategoriaCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idinsumoCategoria = $this->input->post("idInsumoCategoria");
                $condicionDatos = array(
                    'md5(idInsumoCategoria)' => $idinsumoCategoria,
                    'estadoInsumoCategoria' => 'Activo',
                );
                $activoinsumoCategoria = ExistenDatos($this->tabla, $condicionDatos);

                ($activoinsumoCategoria == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosInsumosCategoria = array(
                    "estadoInsumoCategoria" => $nuevoEstado
                );
                $condicion = array("md5(idInsumoCategoria)" => $idinsumoCategoria);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosInsumosCategoria, $condicion);
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
    function InsumosCategoriaEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idinsumoCategoria = $this->input->post("idInsumoCategoria");
                $datosInsumosCategoria = array(
                    "estadoInsumoCategoria" => 'Borrado'
                );
                $condicion = array("md5(idInsumoCategoria)" => $idinsumoCategoria);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosInsumosCategoria, $condicion);
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
/* End of file InsumosCategoria.php */
