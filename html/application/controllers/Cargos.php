<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cargos extends CI_Controller
{

    private $tabla = "cargo";
    private $controlador = "Cargos";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Cargos";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-id-card-alt",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'CargosAgregar',
                        'txt' => 'Agregar Cargo',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'cargoAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 2,
                    "Descripcion" => 4,
                    "Estado" => 1,
                    "Acciones" => 1,
                ),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
            );
            $extras = array(
                'css' => array(
                    "vendors/plugins/ckeditor_full/skins/moono-lisa/editor_gecko.css"
                ),
                'js' => array(
                    "scripts/cargos.js",
                    "vendors/plugins/ckeditor_full/ckeditor.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function CargosMostrar(){
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
            0 => 'nombreCargo',
            1 => 'descripcionCargo'
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
		$condicion = array('idSucursalCargo' => $sucursal, 'estadoCargo !=' => 'Borrado');
		$Cargos = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($Cargos);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($Cargos != 0) {
            $datosMostrar = array();
            foreach ($Cargos as $Cargo) {
                $estadoCargo = $Cargo->estadoCargo;
                if ($estadoCargo == 'Activo') {
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

                $funcion = "CargosEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idCargo=" . md5($Cargo->idCargo) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "CargosCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idCargo=" . md5($Cargo->idCargo) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "CargosEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idCargo=" . md5($Cargo->idCargo) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $Cargo->idCargo,
                    $Cargo->nombreCargo,
                    $Cargo->descripcionCargo,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalCargos = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalCargos,
                "recordsFiltered" => $totalCargos,
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
    function CargosAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Cargo";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-id-card-alt",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(
                        "vendors/plugins/ckeditor_full/skins/moono-lisa/editor_gecko.css"
                    ),
                    'js' => array(
                        "scripts/cargos.js",
                        "vendors/plugins/ckeditor_full/ckeditor.js"
                    ),
                );
                $this->load->view("cargos/CargoAgregar",$datosVista);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreCargo = $this->input->post("nombreCargo");
                $descripcionCargo = $this->input->post("descripcionCargo");
                $funcionesCargo = $this->input->post("funcionesCargo");
                $condicionExiste = array(
                    "nombreCargo" => $nombreCargo,
                    "idSucursalCargo" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosCargos = array(
                        "idSucursalCargo" => $this->session->idSucursal,
                        "nombreCargo" => $nombreCargo,
                        "descripcionCargo" => $descripcionCargo,
                        "funcionesCargo" => $funcionesCargo,
                        "estadoCargo" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosCargos);
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
    function CargosEditar($idCargo = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosCargos = TraerUnDato($this->tabla, array('md5(idCargo)' => $idCargo));
                $titulo = "Editar Cargo";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-id-card-alt",
                    "controlador" => $this->controlador,
                    "idCargo" => $idCargo,
                    "proceso" => "Editar",
                    "datosCargos" => $datosCargos
                );
                $extras = array(
                    'css' => array(
                        "vendors/plugins/ckeditor_full/skins/moono-lisa/editor_gecko.css"
                    ),
                    'js' => array(
                        "scripts/cargos.js",
                        "vendors/plugins/ckeditor_full/ckeditor.js"
                    ),
                );
                $this->load->view("cargos/CargoEditar",$datosVista);              
            } else if ($this->input->method(TRUE) == "POST") {
                $idCargo = $this->input->post("idCargo");
                $nombreCargo = $this->input->post("nombreCargo");
                $funcionesCargo = $this->input->post("funcionesCargo");
                $descripcionCargo = $this->input->post("descripcionCargo");
                $condicionExiste = array(
                    "nombreCargo" => $nombreCargo,
                    "idSucursalCargo" => $this->session->idSucursal,
                    "md5(idCargo) !="=> $idCargo
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosCargos = array(
                        "idSucursalCargo" => $this->session->idSucursal,
                        "nombreCargo" => $nombreCargo,
                        "descripcionCargo" => $descripcionCargo,
                        "funcionesCargo" => $funcionesCargo,
                        'aleatorioCargo' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idCargo)" => $idCargo);
                    $guardar = EditarDatos($this->tabla, $datosCargos,$condicion);
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
    function CargosCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCargo = $this->input->post("idCargo");
                $condicionDatos = array(
                    'md5(idCargo)' => $idCargo,
                    'estadoCargo' => 'Activo',
                );
                $activoCargo = ExistenDatos($this->tabla, $condicionDatos);

                ($activoCargo == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosCargos = array(
                    "estadoCargo" => $nuevoEstado
                );
                $condicion = array("md5(idCargo)" => $idCargo);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosCargos, $condicion);
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
    function CargosEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idCargo = $this->input->post("idCargo");
                $datosCargos = array(
                    "estadoCargo" => 'Borrado'
                );
                $condicion = array("md5(idCargo)" => $idCargo);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosCargos, $condicion);
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
/* End of file Cargos.php */
