<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TramosRenta extends CI_Controller
{

    private $tabla = "tramoRenta";
    private $controlador = "TramosRenta";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Tramos de la Renta";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-percentage",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'TramosRentaAgregar',
                        'txt' => 'Agregar Tramo de Renta',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'tramoRentaAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Desde" => 1,
                    "Hasta" => 1,
                    "Porcentaje" => 1,
                    "Aplicable Desde" => 1,
                    "Cuota Fija" => 1,
                    "Estado" => 1,
                    "Acciones" => 1
                ),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>TraerDatos('sucursal'),
            );
            $extras = array(
                'css' => array(),
                'js' => array(
                    "scripts/tramosRenta.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function TramosRentaMostrar(){
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
            0 => 'desdeTramoRenta',
            1 => 'hastaTramoRenta'
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
		$condicion = array('idSucursalTramoRenta' => $sucursal, 'estadoTramoRenta !=' => 'Borrado');
		$TramosRenta = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($TramosRenta);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($TramosRenta != 0) {
            $datosMostrar = array();
            foreach ($TramosRenta as $TramoRenta) {
                $estadoTramoRenta = $TramoRenta->estadoTramoRenta;
                if ($estadoTramoRenta == 'Activo') {
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

                $funcion = "TramosRentaEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idTramoRenta=" . md5($TramoRenta->idTramoRenta) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "TramosRentaCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idTramoRenta=" . md5($TramoRenta->idTramoRenta) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "TramosRentaEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idTramoRenta=" . md5($TramoRenta->idTramoRenta) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $TramoRenta->idTramoRenta,
                    $TramoRenta->desdeTramoRenta,
                    $TramoRenta->hastaTramoRenta,
                    $TramoRenta->porcentajeTramoRenta,
                    $TramoRenta->excesoTramoRenta,
                    $TramoRenta->cuotaTramoRenta,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalTramosRenta = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalTramosRenta,
                "recordsFiltered" => $totalTramosRenta,
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
    function TramosRentaAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Tramo de Renta";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-percentage",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/tramosRenta.js"
                    ),
                );
                $this->load->view("tramosRenta/TramoRentaAgregar",$datosVista);
            } else if ($this->input->method(TRUE) == "POST") {
                $desdeTramoRenta = $this->input->post("desdeTramoRenta");
                $hastaTramoRenta = $this->input->post("hastaTramoRenta");
                $porcentajeTramoRenta = $this->input->post("porcentajeTramoRenta");
                $excesoTramoRenta = $this->input->post("excesoTramoRenta");
                $cuotaTramoRenta = $this->input->post("cuotaTramoRenta");
                $condicionExiste = array(
                    "desdeTramoRenta" => $desdeTramoRenta,
                    "hastaTramoRenta" => $hastaTramoRenta,
                    "idSucursalTramoRenta" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosTramosRenta = array(
                        "idSucursalTramoRenta" => $this->session->idSucursal,
                        "desdeTramoRenta" => $desdeTramoRenta,
                        "hastaTramoRenta" => $hastaTramoRenta,
                        "porcentajeTramoRenta" => $porcentajeTramoRenta,
                        "excesoTramoRenta" => $excesoTramoRenta,
                        "cuotaTramoRenta" => $cuotaTramoRenta,
                        "estadoTramoRenta" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosTramosRenta);
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
    function TramosRentaEditar($idTramoRenta = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosTramosRenta = TraerUnDato($this->tabla, array('md5(idTramoRenta)' => $idTramoRenta));
                $titulo = "Editar Tramo de Renta";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-percentage",
                    "controlador" => $this->controlador,
                    "idTramoRenta" => $idTramoRenta,
                    "proceso" => "Editar",
                    "datosTramosRenta" => $datosTramosRenta
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/tramosRenta.js"
                    ),
                );
                $this->load->view("tramosRenta/TramoRentaEditar",$datosVista);
            } else if ($this->input->method(TRUE) == "POST") {
                $idTramoRenta = $this->input->post("idTramoRenta");
                $desdeTramoRenta = $this->input->post("desdeTramoRenta");
                $hastaTramoRenta = $this->input->post("hastaTramoRenta");
                $porcentajeTramoRenta = $this->input->post("porcentajeTramoRenta");
                $excesoTramoRenta = $this->input->post("excesoTramoRenta");
                $cuotaTramoRenta = $this->input->post("cuotaTramoRenta");

                $condicionExiste = array(
                    "desdeTramoRenta" => $desdeTramoRenta,
                    "hastaTramoRenta" => $hastaTramoRenta,
                    "idSucursalTramoRenta" => $this->session->idSucursal,
                    "md5(idTramoRenta) !="=> $idTramoRenta
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosTramosRenta = array(
                        "idSucursalTramoRenta" => $this->session->idSucursal,
                        "desdeTramoRenta" => $desdeTramoRenta,
                        "hastaTramoRenta" => $hastaTramoRenta,
                        "porcentajeTramoRenta" => $porcentajeTramoRenta,
                        "excesoTramoRenta" => $excesoTramoRenta,
                        "cuotaTramoRenta" => $cuotaTramoRenta,
                        'aleatorioTramoRenta' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idTramoRenta)" => $idTramoRenta);
                    $guardar = EditarDatos($this->tabla, $datosTramosRenta,$condicion);
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
    function TramosRentaCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idTramoRenta = $this->input->post("idTramoRenta");
                $condicionDatos = array(
                    'md5(idTramoRenta)' => $idTramoRenta,
                    'estadoTramoRenta' => 'Activo',
                );
                $activoTramoRenta = ExistenDatos($this->tabla, $condicionDatos);

                ($activoTramoRenta == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosTramosRenta = array(
                    "estadoTramoRenta" => $nuevoEstado
                );
                $condicion = array("md5(idTramoRenta)" => $idTramoRenta);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosTramosRenta, $condicion);
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
    function TramosRentaEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idTramoRenta = $this->input->post("idTramoRenta");
                $datosTramosRenta = array(
                    "estadoTramoRenta" => 'Borrado'
                );
                $condicion = array("md5(idTramoRenta)" => $idTramoRenta);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosTramosRenta, $condicion);
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
/* End of file TramosRenta.php */
