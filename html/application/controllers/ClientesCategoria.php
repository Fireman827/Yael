<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ClientesCategoria extends CI_Controller
{

    private $tabla = "clienteCategoria";
    private $controlador = "ClientesCategoria";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Categoría de Clientes";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fa fa-list-alt",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'ClientesCategoriaAgregar',
                        'txt' => 'Agregar Categoría de Cliente',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'clienteCategoriaAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 3,
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
                    "scripts/clientesCategoria.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function ClientesCategoriaMostrar(){
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
            0 => 'nombreClienteCategoria'
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
		$condicion = array('idSucursalClienteCategoria' => $sucursal, 'estadoClienteCategoria !=' => 'Borrado');
		$ClientesCategoria = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($ClientesCategoria);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($ClientesCategoria != 0) {
            $datosMostrar = array();
            foreach ($ClientesCategoria as $ClienteCategoria) {
                $estadoClienteCategoria = $ClienteCategoria->estadoClienteCategoria;
                if ($estadoClienteCategoria == 'Activo') {
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

                $funcion = "ClientesCategoriaEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idClienteCategoria=" . md5($ClienteCategoria->idClienteCategoria) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "ClientesCategoriaCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idClienteCategoria=" . md5($ClienteCategoria->idClienteCategoria) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "ClientesCategoriaEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idClienteCategoria=" . md5($ClienteCategoria->idClienteCategoria) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $ClienteCategoria->idClienteCategoria,
                    $ClienteCategoria->nombreClienteCategoria,
                    $ClienteCategoria->descripcionClienteCategoria,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalClientesCategoria = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalClientesCategoria,
                "recordsFiltered" => $totalClientesCategoria,
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
    function ClientesCategoriaAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Categoría de Cliente";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fa fa-list-alt",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/clientesCategoria.js"
                    ),
                );
                $this->load->view("clientesCategoria/ClientesCategoriaAgregar",$datosVista);
                // GblPlantilla("clientesCategoria/ClientesCategoriaAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreClienteCategoria = $this->input->post("nombreClienteCategoria");
                $descripcionClienteCategoria = $this->input->post("descripcionClienteCategoria");
                $condicionExiste = array(
                    "nombreClienteCategoria" => $nombreClienteCategoria,
                    "estadoClienteCategoria !=" => "Borrado",
                    "idSucursalClienteCategoria" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosClientesCategoria = array(
                        "idSucursalClienteCategoria" => $this->session->idSucursal,
                        "nombreClienteCategoria" => $nombreClienteCategoria,
                        "descripcionClienteCategoria" => $descripcionClienteCategoria,
                        "estadoClienteCategoria" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosClientesCategoria);
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
    function ClientesCategoriaEditar($idClienteCategoria = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosClientesCategoria = TraerUnDato($this->tabla, array('md5(idClienteCategoria)' => $idClienteCategoria));
                $titulo = "Editar Categoría de Cliente";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fa fa-list-alt",
                    "controlador" => $this->controlador,
                    "idClienteCategoria" => $idClienteCategoria,
                    "proceso" => "Editar",
                    "datosClientesCategoria" => $datosClientesCategoria
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/clientesCategoria.js"
                    ),
                );
                $this->load->view("clientesCategoria/ClientesCategoriaEditar",$datosVista);
                //GblPlantilla("clientesCategoria/ClientesCategoriaEditar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $idClienteCategoria = $this->input->post("idClienteCategoria");
                $nombreClienteCategoria = $this->input->post("nombreClienteCategoria");
                $descripcionClienteCategoria = $this->input->post("descripcionClienteCategoria");
                $condicionExiste = array(
                    "nombreClienteCategoria" => $nombreClienteCategoria,
                    "idSucursalClienteCategoria" => $this->session->idSucursal,
                    "md5(idClienteCategoria) !="=> $idClienteCategoria
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosClientesCategoria = array(
                        "idSucursalClienteCategoria" => $this->session->idSucursal,
                        "nombreClienteCategoria" => $nombreClienteCategoria,
                        "descripcionClienteCategoria" => $descripcionClienteCategoria,
                        'aleatorioClienteCategoria' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idClienteCategoria)" => $idClienteCategoria);
                    $guardar = EditarDatos($this->tabla, $datosClientesCategoria,$condicion);
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
    function ClientesCategoriaCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idClienteCategoria = $this->input->post("idClienteCategoria");
                $condicionDatos = array(
                    'md5(idClienteCategoria)' => $idClienteCategoria,
                    'estadoClienteCategoria' => 'Activo',
                );
                $activoClienteCategoria = ExistenDatos($this->tabla, $condicionDatos);

                ($activoClienteCategoria == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosClientesCategoria = array(
                    "estadoClienteCategoria" => $nuevoEstado
                );
                $condicion = array("md5(idClienteCategoria)" => $idClienteCategoria);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosClientesCategoria, $condicion);
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
    function ClientesCategoriaEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idClienteCategoria = $this->input->post("idClienteCategoria");
                $datosClientesCategoria = array(
                    "estadoClienteCategoria" => 'Borrado'
                );
                $condicion = array("md5(idClienteCategoria)" => $idClienteCategoria);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosClientesCategoria, $condicion);
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
/* End of file ClientesCategoria.php */
