<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Impresoras extends CI_Controller
{
    private $tabla = "impresora";
    private $controlador = "Impresoras";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Impresoras";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-print",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'ImpresorasAgregar',
                        'txt' => 'Agregar Impresora',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'impresoraAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 3,
                    "Recurso Compartido" => 3,
                    "IP Impresora" => 3,
                    "Tipo Conexion" => 3,
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
                    "scripts/impresoras.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function ImpresorasMostrar(){
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
            0 => 'idImpresora',
            1 => 'nombreImpresora'
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
		$condicion = array('idSucursalImpresora' => $sucursal, 'estadoImpresora !=' => 'Borrado');
		$Impresoras = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($Impresoras);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($Impresoras != 0) {
            $datosMostrar = array();
            foreach ($Impresoras as $Impresora) {
                $estadoImpresora = $Impresora->estadoImpresora;
                if ($estadoImpresora == 'Activo') {
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

                $funcion = "ImpresorasTest";
                $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Test' idImpresora=" . md5($Impresora->idImpresora) . "><i class='fa fa-print' ></i> Impresion de Prueba</a>";
                $funcion = "ImpresorasEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idImpresora=" . md5($Impresora->idImpresora) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "ImpresorasCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idImpresora=" . md5($Impresora->idImpresora) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "ImpresorasEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idImpresora=" . md5($Impresora->idImpresora) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $Impresora->idImpresora,
                    $Impresora->nombreImpresora,
                    $Impresora->recursoCompartidoImpresora,
                    $Impresora->IpImpresora,
                    $Impresora->tipoImpresora,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalImpresoras = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalImpresoras,
                "recordsFiltered" => $totalImpresoras,
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
    function ImpresorasAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Impresora";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-print",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/impresoras.js"
                    ),
                );
                $this->load->view("impresoras/ImpresoraAgregar",$datosVista);
                // GblPlantilla("Impresoras/ImpresorasAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreImpresora = $this->input->post("nombreImpresora");
                $recursoCompartidoImpresora = $this->input->post("recursoCompartidoImpresora");
                $IpImpresora = $this->input->post("IpImpresora");
                $tipoImpresora = $this->input->post("tipoImpresora");
                $servidorImpresora = $this->input->post("servidorImpresora");
                $cuentasImpresora = (!is_null($this->input->post("cuentaImpresora"))) ? 1 : 0 ;
                $cocinaImpresora = (!is_null($this->input->post("cocinaImpresora"))) ? 1 : 0 ;
                $cobroImpresora = (!is_null($this->input->post("cobroImpresora"))) ? 1 : 0 ;
                $condicionExiste = array(
                    "nombreImpresora" => $nombreImpresora,
                    "idSucursalImpresora" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosImpresoras = array(
                        "idSucursalImpresora" => $this->session->idSucursal,
                        "nombreImpresora" => $nombreImpresora,
                        "recursoCompartidoImpresora" => $recursoCompartidoImpresora,
                        "IpImpresora" => $IpImpresora,
                        "tipoImpresora" => $tipoImpresora,
                        "servidorImpresora" => $servidorImpresora,
                        "cuentaImpresora" => $cuentasImpresora,
                        "cocinaImpresora" => $cocinaImpresora,
                        "cobroImpresora" => $cobroImpresora,
                        "estadoImpresora" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosImpresoras);
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
    function ImpresorasEditar($idImpresora = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosImpresoras = TraerUnDato($this->tabla, array('md5(idImpresora)' => $idImpresora));
                $titulo = "Editar Impresora";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-print",
                    "controlador" => $this->controlador,
                    "idImpresora" => $idImpresora,
                    "proceso" => "Editar",
                    "datosImpresoras" => $datosImpresoras
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/impresoras.js"
                    ),
                );
                $this->load->view("impresoras/ImpresoraEditar",$datosVista);
                //GblPlantilla("Impresoras/ImpresorasEditar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $idImpresora = $this->input->post("idImpresora");
                $nombreImpresora = $this->input->post("nombreImpresora");
                $recursoCompartidoImpresora = $this->input->post("recursoCompartidoImpresora");
                $IpImpresora = $this->input->post("IpImpresora");
                $tipoImpresora = $this->input->post("tipoImpresora");
                $servidorImpresora = $this->input->post("servidorImpresora");
                $cuentasImpresora = (!is_null($this->input->post("cuentaImpresora"))) ? 1 : 0 ;
                $cocinaImpresora = (!is_null($this->input->post("cocinaImpresora"))) ? 1 : 0 ;
                $cobroImpresora = (!is_null($this->input->post("cobroImpresora"))) ? 1 : 0 ;
                $condicionExiste = array(
                    "nombreImpresora" => $nombreImpresora,
                    "idSucursalImpresora" => $this->session->idSucursal,
                    "md5(idImpresora) !="=> $idImpresora
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosImpresoras = array(
                        "idSucursalImpresora" => $this->session->idSucursal,
                        "nombreImpresora" => $nombreImpresora,
                        "recursoCompartidoImpresora" => $recursoCompartidoImpresora,
                        "IpImpresora" => $IpImpresora,
                        "tipoImpresora" => $tipoImpresora,
                        "cuentaImpresora" => $cuentasImpresora,
                        "cocinaImpresora" => $cocinaImpresora,
                        "cobroImpresora" => $cobroImpresora,
                        "servidorImpresora" => $servidorImpresora,
                        'aleatorioImpresora' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("md5(idImpresora)" => $idImpresora);
                    $guardar = EditarDatos($this->tabla, $datosImpresoras,$condicion);
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
    function ImpresorasCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idImpresora = $this->input->post("idImpresora");
                $condicionDatos = array(
                    'md5(idImpresora)' => $idImpresora,
                    'estadoImpresora' => 'Activo',
                );
                $activoImpresora = ExistenDatos($this->tabla, $condicionDatos);

                ($activoImpresora == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosImpresoras = array(
                    "estadoImpresora" => $nuevoEstado
                );
                $condicion = array("md5(idImpresora)" => $idImpresora);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosImpresoras, $condicion);
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
    function ImpresorasEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idImpresora = $this->input->post("idImpresora");
                $datosImpresoras = array(
                    "estadoImpresora" => 'Borrado'
                );
                $condicion = array("md5(idImpresora)" => $idImpresora);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosImpresoras, $condicion);
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
    function ImpresorasTest(){
        if ($this->input->method(TRUE) == "POST") {
            $idImpresora = $this->input->post("idImpresora");
            $condicion=  array(
                "md5(idImpresora)" => $idImpresora,
                "estadoImpresora" => "Activo",
            );
            $im = TraerUnDato($this->tabla,$condicion);
            if($im){
                $datos = array(
                  "idImpresora" => $im->idImpresora,
                  "nombreImpresora" => $im->nombreImpresora,
                  "recursoCompartido" => $im->recursoCompartidoImpresora,
                  "IpImpresora" => $im->IpImpresora,
                  "tipo" => $im->tipoImpresora,
                );
                $datosRespuesta["servidor"] = $im->servidorImpresora;
                $datosRespuesta["codigo"] = 200;
                $datosRespuesta["datos"] = $datos;
            } else {
                $datosRespuesta["codigo"] = 500;
            }
            echo json_encode($datosRespuesta);
        }
    }
}
/* End of file Impresoras.php */
