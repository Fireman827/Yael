<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Zonas extends CI_Controller
{

    private $tabla = "zona";
    private $tablaZonaMesa = "zonaMesa";
    private $controlador = "Zonas";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Zonas";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fa fa-map",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'ZonasAgregar',
                        'txt' => 'Agregar Zonas',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => true,
                        'id'=>'zonaAgregar'
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 3,
                    "Capacidad" => 2,
                    "Utilizado" => 2,
                    "Visible" => 2,
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
                    "scripts/zonas.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function ZonasMostrar(){
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
            0 => 'capacidadZona',
            1 => 'nombreZona',
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
		$condicion = array('idSucursalZona' => $sucursal, 'estadoZona !=' => 'Borrado');
		$Zonas = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        // print_r($Zonas);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($Zonas != 0) {
            $datosMostrar = array();
            foreach ($Zonas as $Zona) {
                $estadoZona = $Zona->estadoZona;
                $visibleZona = $Zona->visibleZona;
                $visibleSpan = ($visibleZona == 1) ? "<span class='badge badge-primary font-bold'>Si<span>": "<span class='badge badge-danger font-bold'>No<span>";
                if ($estadoZona == 'Activo') {
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

                $funcion = "ZonasEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Editar' idZona=" . md5($Zona->idZona) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "ZonasCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idZona=" . md5($Zona->idZona) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "ZonasMesas";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Mesas' idZona=" . md5($Zona->idZona) . "><i class='fa fa-chair'></i> Mesas</a>";
                }
                $funcion = "ZonasEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idZona=" . md5($Zona->idZona) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "
				</div>
				</div>";
                $nEspacios = 0;
                $ocupado = TraerDatos($this->tablaZonaMesa,array("idZona" => $Zona->idZona, "estadoZonaMesa !="=>"Borrado"));
                if($ocupado){
                    foreach($ocupado as $o){
                        $nEspacios += $o->capacidadZonaMesa;
                    }
                }
                $datosMostrar[] = array(
                    $Zona->idZona,
                    $Zona->nombreZona,
                    $Zona->capacidadZona,
                    $nEspacios,
                    $visibleSpan,
                    $estadoSpan,
                    $menuOpciones,
                );
            }
            $totalZonas = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalZonas,
                "recordsFiltered" => $totalZonas,
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
    function ZonasAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $this->load->view("zonas/ZonasPermisos");
        } else {
            if($this->input->method(TRUE) == "GET") {
                $titulo = "Mesas por Zona";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fa fa-map",
                    "controlador" => "Zonas",
                    "proceso" => "Agregar",
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/zonas.js"
                    ),
                );
                $this->load->view("zonas/ZonasAgregar",$datosVista);
                // GblPlantilla("zonas/ZonasAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreZona = $this->input->post("nombreZona");
                $capacidadZona = $this->input->post("capacidadZona");
                $tipoAumentoZona = $this->input->post("tipoAumentoZona");
                $aumentoZona = $this->input->post("aumentoZona");
                $visibleZona = !is_null( $this->input->post("visibleZona") )  ? 1 : 0;
                $condicionExiste = array(
                    "nombreZona" => $nombreZona,
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosZonaTipo = array(
                        "idSucursalZona" => $this->session->idSucursal,
                        "nombreZona" => $nombreZona,
                        "capacidadZona" => $capacidadZona,
                        "tipoAumentoZona" => $tipoAumentoZona,
                        "aumentoZona" => $aumentoZona,
                        "visibleZona" => $visibleZona,
                        "estadoZona" => 'Activo',
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosZonaTipo);
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

                $existe = ExistenDatos($this->tabla, $condicionExiste);
                echo json_encode($datosRespuesta);
            }
        }
    }
    function ZonasEditar($idZona = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $this->load->view("zonas/ZonasPermisos");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosZona = TraerUnDato($this->tabla, array('md5(idZona)' => $idZona));
                $titulo = "Editar Zona";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fa fa-map",
                    "controlador" => "Zonas",
                    "idZona" => $idZona,
                    "proceso" => "Editar",
                    "datosZona" => $datosZona
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/zonas.js"
                    ),
                );
                $this->load->view("zonas/ZonasEditar",$datosVista);
                //GblPlantilla("zonas/ZonasEditar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $idZona = $this->input->post("idZona");
                $nombreZona = $this->input->post("nombreZona");
                $capacidadZona = $this->input->post("capacidadZona");
                $tipoAumentoZona = $this->input->post("tipoAumentoZona");
                $aumentoZona = $this->input->post("aumentoZona");
                $visibleZona = !is_null( $this->input->post("visibleZona") )  ? 1 : 0;
                $condicionExiste = array(
                    "nombreZona" => $nombreZona,
                    "md5(idZona) !="=> $idZona
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                    if ($existe == 0) {
                       $datosZonaTipo = array(
                            "idSucursalZona" => $this->session->idSucursal,
                            "nombreZona" => $nombreZona,
                            "capacidadZona" => $capacidadZona,
                            "tipoAumentoZona" => $tipoAumentoZona,
                            "aumentoZona" => $aumentoZona,
                            "visibleZona" => $visibleZona,
                            'aleatorioZona' => uniqid(),
                        );
                        IniciarTransaccion();
                        $condicion = array("md5(idZona)" => $idZona);
                        $guardar = EditarDatos($this->tabla, $datosZonaTipo,$condicion);
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
    function ZonasCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idZona = $this->input->post("idZona");
                $condicionDatos = array(
                    'md5(idZona)' => $idZona,
                    'estadoZona' => 'Activo',
                );
                $activoZona = ExistenDatos($this->tabla, $condicionDatos);

                ($activoZona == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosZona = array(
                    "estadoZona" => $nuevoEstado
                );
                $condicion = array("md5(idZona)" => $idZona);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosZona, $condicion);
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
    function ZonasEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idZona = $this->input->post("idZona");
                $datosZona = array(
                    "estadoZona" => 'Borrado'
                );
                $condicion = array("md5(idZona)" => $idZona);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosZona, $condicion);
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
    function ZonasMesas($id){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $this->load->view("zonasMesas/ZonasMesasPermisos");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $idZona = TraerUnDatoIndividual($this->tabla,'idZona',array('md5(idZona)'=>$id));
                $mesas = TraerDatos('zonaMesa',array('idZona'=>$idZona[0]['idZona'],'estadoZonaMesa !='=>'Borrado'),'nombreZonaMesa ASC');
                $titulo = "Mesas por Zona";
                $zonas = TraerDatos('zona',array("estadoZona !=" => "Borrado","idZona !=" => $idZona[0]['idZona']));
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fa fa-chair",
                    "controlador" => "ZonasMesas",
                    "proceso" => "Mesas",
                    "idZona" => $idZona[0]['idZona'],
                    "mesas" => $mesas,
                    "zonas" => $zonas,
                    "botones" => array(
                        array(
                            "id" => "agregarZonaMesa",
                            "txt" => "Agregar Mesa",
                            "url" => "ZonasMesasAgregar",
                            "controlador" => "Zonas",
                            "clases" => "",
                            "icono" => "fa fa-plus"
                        ),
                        array(
                            "id" => "editarZonaMesa",
                            "txt" => "Editar Mesa",
                            "url" => "ZonasMesasEditar",
                            "controlador" => "Zonas",
                            "clases" => "btn-accion",
                            "icono" => "fa fa-edit"
                        ),
                        array(
                            "id" => "eliminarZonaMesa",
                            "txt" => "Eliminar Mesa",
                            "url" => "ZonasMesasEliminar",
                            "controlador" => "Zonas",
                            "clases" => "btn-accion",
                            "icono" => "fa fa-trash"
                        ),
                        array(
                            "id" => "trasladarZonaMesa",
                            "txt" => "Trasladar Mesa",
                            "url" => "ZonasMesasTrasladar",
                            "controlador" => "Zonas",
                            "clases" => "btn-accion",
                            "icono" => "fa fa-arrows-alt-h"
                        ),
                    )
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/zonas.js"
                    ),
                );
                $this->load->view("zonasMesas/ZonasMesas",$datosVista);
                // GblPlantilla("zonas/ZonasAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreZona = $this->input->post("nombreZona");
                $capacidadZona = $this->input->post("capacidadZona");
                $tipoAumentoZona = $this->input->post("tipoAumentoZona");
                $aumentoZona = $this->input->post("aumentoZona");
                $visibleZona = !is_null( $this->input->post("visibleZona") )  ? 1 : 0;
                $condicionExiste = array(
                    "nombreZona" => $nombreZona,
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosZonaTipo = array(
                        "idSucursalZona" => $this->session->idSucursal,
                        "nombreZona" => $nombreZona,
                        "capacidadZona" => $capacidadZona,
                        "tipoAumentoZona" => $tipoAumentoZona,
                        "aumentoZona" => $aumentoZona,
                        "visibleZona" => $visibleZona,
                        "estadoZona" => 'Activo',
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosZonaTipo);
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

                $existe = ExistenDatos($this->tabla, $condicionExiste);
                echo json_encode($datosRespuesta);
            }
        }
    }
    function ZonasMesasAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $this->load->view("zonasMesas/ZonasMesasPermisos");
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idZona = $this->input->post("idZona");
                $maxNombreMesa = TraerMaxValor($this->tablaZonaMesa,'nombreZonaMesa',array("idZona"=>$idZona ,"estadoZonaMesa !="=>"Borrado"));
                $nombreMesa = ($maxNombreMesa != false ) ? $maxNombreMesa + 1 : 1 ;
                
                $valores = $this->input->post("valores");
                $arrayValores= json_decode($valores,true);
               foreach($arrayValores as $valores){
                    $cantidad = $valores['cantidad'];
                    $capacidad = $valores['capacidad'];
                    for($i = 0; $i < $cantidad ; $i++){

                        $datosZonaMesa = array(
                            "idZona" => $idZona,
                            "nombreZonaMesa" => $nombreMesa,
                            "capacidadZonaMesa" => $capacidad
                        );
                        $condicionExiste = array("nombreZonaMesa" => $nombreMesa ,"idZona" => $idZona,"estadoZonaMesa !="=>"Borrado");
                        $existe = ExistenDatos($this->tablaZonaMesa, $condicionExiste);
                        
                        if ($existe == 0) {
                            IniciarTransaccion();
                            $guardar = GuardarDatos($this->tablaZonaMesa, $datosZonaMesa);
                            ($guardar == false) ? $error = true : $error = false;
                            if ($error) {
                                DeshacerTransaccion();
                                $datosRespuesta["codigo"] = 500;
                                break 2;
                            } else {
                                EjecutarTransaccion();
                                $datosRespuesta["codigo"] = 200;
                            }
                        } else {
                            DeshacerTransaccion();
                            $datosRespuesta["codigo"] = 400;
                            break 2;
                        }
                        $nombreMesa++;
                    }
               }
                echo json_encode($datosRespuesta);
            }
        }
    }
    function ZonasMesasEditar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $this->load->view("zonasMesas/ZonasMesasPermisos");
        } else {
            if ($this->input->method(TRUE) == "POST") {
                
                $Mesas = $this->input->post();
                if(!empty($Mesas)){
                    IniciarTransaccion();
                    foreach($Mesas as $nombre => $valor){
                        // $id = str_replace('mesa','',$nombre);
                        $id = $nombre;
                        $datosZonaMesa = array(
                            'capacidadZonaMesa' => $valor,
                            'aleatorioZonaMesa' => uniqid()
                        );
                        $condicion = array(
                            'idZonaMesa' => $id
                        );
                        $guardar = EditarDatos($this->tablaZonaMesa, $datosZonaMesa, $condicion);
                        ($guardar == false) ? ($error = true ) : $error = false;
                        if ($error) {
                            DeshacerTransaccion();
                            $datosRespuesta["codigo"] = 500;
                            break 1;
                        } else {
                            EjecutarTransaccion();
                            $datosRespuesta["codigo"] = 200;
                        }
                    }
                    echo json_encode($datosRespuesta);
                }
            }
        }
    }
    function ZonasMesasEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $this->load->view("zonasMesas/ZonasMesasPermisos");
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $Mesas = $this->input->post();
                if(!empty($Mesas)){
                    IniciarTransaccion();
                    foreach($Mesas as $nombre => $valor){
                        $id = str_replace('mesa','',$nombre);
                        $datosZonaMesa = array(
                            'estadoZonaMesa' => 'Borrado',
                        );
                        $condicion = array(
                            'idZonaMesa' => $id
                        );
                        $guardar = EditarDatos($this->tablaZonaMesa, $datosZonaMesa, $condicion);
                        ($guardar == false) ? ($error = true ) : $error = false;
                        if ($error) {
                            DeshacerTransaccion();
                            $datosRespuesta["codigo"] = 500;
                            break 1;
                        } else {
                            EjecutarTransaccion();
                            $datosRespuesta["codigo"] = 200;
                        }
                    }
                    echo json_encode($datosRespuesta);
                }
            }
        }
    }
    function ZonasMesasTrasladar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $this->load->view("zonasMesas/ZonasMesasPermisos");
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $mesas = $this->input->post();
                $zonaDestino = $this->input->post('zonaDestino');
                unset($mesas['zonaDestino']);
                if(!empty($mesas)){
                    IniciarTransaccion();
                    foreach($mesas as $nombre => $valor){
                        $id = str_replace('mesa','',$nombre);
                        $datosZonaMesa = array(
                            'idZona' => $zonaDestino,
                            'nombreZonaMesa' => $valor,
                            'aleatorioZonaMesa' => uniqid()
                        );
                        $condicion = array(
                            'idZonaMesa' => $id
                        );
                        $guardar = EditarDatos($this->tablaZonaMesa, $datosZonaMesa, $condicion);
                        ($guardar == false) ? ($error = true ) : $error = false;
                    }
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 500;
                    } else {
                        EjecutarTransaccion();
                        $datosRespuesta["codigo"] = 200;
                    }
                    echo json_encode($datosRespuesta);
                }
            }
        }
    }
    function ZonasMesasNuevoNombre(){
        if($this->input->method(TRUE) == "GET") {
            $idZona = $this->input->get('idZona');
            $nombreMesa = $this->input->get('nombreZonaMesa');
            //var_dump($nombreMesa);
            $condicion = array(
                "idZona" => $idZona,
                "nombreZonaMesa" => $nombreMesa,
                "estadoZonaMesa !=" => "Borrado",
            );
            $dato = TraerTotalDatos($this->tablaZonaMesa,$condicion);
            //($dato == false) ? $error = true : $error = false;
            if($dato == false){
                $datosRespuesta["codigo"] = 200;
            }else{
                $datosRespuesta["codigo"] = 400;
            }
            echo json_encode($datosRespuesta);
        }
    }

}
/* End of file Zonas.php */
