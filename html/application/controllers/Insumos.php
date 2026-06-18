<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Insumos extends CI_Controller
{

    private $tabla = "insumo";
    private $tablaPresentacion = "presentacion";
    private $tablaInsumoPresentacion = "insumoPresentacion";
    private $tablaInsumoCategoria = "insumoCategoria";
    private $controlador = "Insumos";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index()  {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $campos = array(
                "idInsumoCategoria" => "idCategoria",
                "nombreInsumoCategoria" => "nombreCategoria"
            );
            $categoria = TraerDatosRenombrados('insumoCategoria', $campos, array("estadoInsumoCategoria" => 'Activo'));
            $titulo = "Insumos";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fa fa-drumstick-bite",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'InsumosAgregar',
                        'txt' => 'Agregar insumo',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => false,
                        'id' => 'insumoAgregar'
                    ),
                ),
                "buscador" => true,
                "categorias" => $categoria,
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 3,
                    "Descripcion" => 3,
                    "Marca" => 3,
                    "Estado" => 2,
                    "Acciones" => 1,
                ),
                "admin" => $this->session->admin,
                "idSucursal" => $this->session->idSucursal,
                "sucursales" => TraerDatos('sucursal'),
            );
            $extras = array(
                'css' => array(),
                'js' => array(
                    "scripts/insumos.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function InsumosMostrar() {
        // Espacio propio del plugin data tabla
        $draw = intval($this->input->post("draw"));
        $desdeFilas = intval($this->input->post("start"));
        $cantidadFilas = intval($this->input->post("length"));

        $buscador = $this->input->post("buscador");
        $buscadorTexto = $this->input->post("busqueda");

        $order = $this->input->post("order");
        $busquedaAreglo = $this->input->post("search");
        $busquedaParametro = ($buscador == "1") ? $buscadorTexto : $busquedaAreglo['value'];
        $categoria = $this->input->post("categoria");

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
            0 => 'idInsumo',
			1 => 'nombreInsumo'
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
        $condicion = array('idSucursalInsumo' => $sucursal, 'estadoInsumo !=' => 'Borrado');
        if ($categoria != "All") {
            $condicion = array('idSucursalInsumo' => $sucursal, "estadoInsumo !=" => "Borrado", "insumoCategoria.idInsumoCategoria" => $categoria);
            $join = array(
                array(
                    "tabla" => "insumoCategoria",
                    "condicion" => "insumoCategoria.idInsumoCategoria = insumo.idCategoriaInsumo",
                ),
            );
            $campos = "insumoCategoria.idInsumoCategoria,insumo.idInsumo, insumo.nombreInsumo, insumo.descripcionInsumo,insumo.estadoInsumo,insumo.marcaInsumo";
            $insumos = TraerDatosTablaJoinGroup($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion, $join, $campos, "insumo.idInsumo");
        } else {
            $insumos = TraerDatosTabla($this->tabla, $ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion);
        }
        //$insumos = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //print_r($insumos);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($insumos != 0) {
            $datosMostrar = array();
            foreach ($insumos as $insumo) {
                $estadoinsumo = $insumo->estadoInsumo;
                if ($estadoinsumo == 'Activo') {
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

                $funcion = "InsumosEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' href='" . base_url() . $funcion . "/" . md5($insumo->idInsumo) . "' idInsumo=" . md5($insumo->idInsumo) . "><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "InsumosCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idInsumo=" . md5($insumo->idInsumo) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "InsumosEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idInsumo=" . md5($insumo->idInsumo) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $insumo->idInsumo,
                    $insumo->nombreInsumo,
                    $insumo->descripcionInsumo,
                    $insumo->marcaInsumo,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalInsumos = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalInsumos,
                "recordsFiltered" => $totalInsumos,
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
    function InsumosAgregar() {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $titulo = "Agregar Insumo";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fa fa-drumstick-bite",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                    "insumoCategoria" => TraerDatos('insumoCategoria', array("idSucursalInsumoCategoria" => $this->session->idSucursal, "estadoInsumoCategoria !=" => "Borrado")),
                    "proveedores" => TraerDatos('proveedor', array("idSucursalProveedor" => $this->session->idSucursal, "estadoProveedor !=" => "Borrado")),
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/insumos.js"
                    ),
                );
                GblPlantilla("insumos/InsumosAgregar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreInsumos = $this->input->post("nombreInsumos");
                $categoriaInsumos = $this->input->post("categoriaInsumos");
                $marcaInsumos = $this->input->post("marcaInsumos");
                $proveedor1Insumos = $this->input->post("proveedor1Insumos");
                $proveedor2Insumos = $this->input->post("proveedor2Insumos");
                $proveedor3Insumos = $this->input->post("proveedor3Insumos");
                $stockMinimoInsumos = $this->input->post("stockMinimoInsumo");
                $descripcionInsumos = $this->input->post("descripcionInsumos");
                $montoSugeridoInsumos = $this->input->post("montoSugeridoInsumos");
                $advaloremInsumos = (!is_null($this->input->post("advaloremInsumos"))) ? 1 : 0;
                $advaloremTipoInsumos = (!is_null($this->input->post("advaloremTipoInsumos"))) ? $this->input->post("advaloremTipoInsumos") : "";
                $exentoIVAInsumos = (!is_null($this->input->post("exentoIVAInsumos"))) ? 1 : 0;
                $perecederoInsumos = (!is_null($this->input->post("perecederoInsumos"))) ? 1 : 0;
                $revisarInsumos = (!is_null($this->input->post("revisarInsumos"))) ? 1 : 0;
                $valoresTabla = json_decode($this->input->post("valoresTabla"));

                $condicionExiste = array(
                    "idSucursalInsumo" => $this->session->idSucursal,
                    "nombreInsumo" => $nombreInsumos,
                    "idCategoriaInsumo" => $categoriaInsumos,
                    "estadoInsumo !=" => "Borrado"
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosInsumos = array(
                        "idSucursalInsumo" => $this->session->idSucursal,
                        "nombreInsumo" => $nombreInsumos,
                        "idCategoriaInsumo" => $categoriaInsumos,
                        "marcaInsumo" => $marcaInsumos,
                        "proveedor1Insumo" => $proveedor1Insumos,
                        "proveedor2Insumo" => $proveedor2Insumos,
                        "proveedor3Insumo" => $proveedor3Insumos,
                        "stockMinimoInsumo" => $stockMinimoInsumos,
                        "exentoIVAInsumo" => $exentoIVAInsumos,
                        "perecederoInsumo" => $perecederoInsumos,
                        "revisarInsumo" => $revisarInsumos,
                        "advaloremInsumo" => $advaloremInsumos,
                        "advaloremTipoInsumo" => $advaloremTipoInsumos,
                        "montoSugeridoInsumo" => $montoSugeridoInsumos,
                        "descripcionInsumo" => $descripcionInsumos,
                        "estadoInsumo" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosInsumos);
                    ($guardar == false) ? $error = true : $error = false;
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 500;
                    } else {
                        $idInsumo = $guardar;
                        $costoPromedio = 0;
                        for ($i = 0; $i < count($valoresTabla); $i++) {
                            $idPresentacion = $valoresTabla[$i]->presentacion;
                            $descripcion = $valoresTabla[$i]->descripcion;
                            $unidad = $valoresTabla[$i]->unidad;
                            $costo = $valoresTabla[$i]->costo;
                            $precio = $valoresTabla[$i]->precio;
                            $inventario = $valoresTabla[$i]->inventario;
                            
                            if($unidad == "1"){
                                $costoPromedio = $costo;
                            }
                            
                            $datosPresentaciones = array(
                                "idInsumo" => $idInsumo,
                                "idPresentacion" => $idPresentacion,
                                "descripcionInsumoPresentacion" => $descripcion,
                                "unidadInsumoPresentacion" => $unidad,
                                "costoInsumoPresentacion" => $costo,
                                "precioInsumoPresentacion" => $precio,
                                "unidadInventarioInsumoPresentacion" => $inventario,
                            );
                            $guardar = GuardarDatos($this->tablaInsumoPresentacion, $datosPresentaciones);
                            if($guardar) {
                                $error = false;
                            }else{
                                $error = true;
                                $datosRespuesta["codigo"] = 501;
                                break;
                            } 
                        }
                        $datosStock =  array(
                            "idInsumo" => $idInsumo,
                            "idSucursalInsumoStock" => $this->session->idSucursal,
                            "cantidadInsumoStock" => "0",
                            "estadoInsumoStock" => "Activo"
                        );
                        $guardarStock = GuardarDatos("insumoStock", $datosStock);
                        if($guardarStock) {
                            $actualizarCosto = EditarDatos("insumo",array("costoPromedioInsumo"=>$costoPromedio),array("idInsumo" =>$idInsumo));
                            if($actualizarCosto){
                                $error = false;
                                $datosRespuesta["codigo"] = 200;
                            }else{
                                $datosRespuesta["codigo"] = 503;
                                $error = true;  
                            }
                        }else{
                            $datosRespuesta["codigo"] = 502;
                            $error = true;
                        } 

                        if ($error) {
                            DeshacerTransaccion();
                        } else {
                            
                            EjecutarTransaccion();
                        }
                    }
                } else {
                    $datosRespuesta["codigo"] = 400;
                }
                echo json_encode($datosRespuesta);
            }
        }
    }
    function InsumosEditar($idInsumo = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosInsumos = TraerUnDato($this->tabla, array('md5(idInsumo)' => $idInsumo));
                $titulo = "Editar insumo";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fa fa-drumstick-bite",
                    "controlador" => $this->controlador,
                    "idInsumo" => $idInsumo,
                    "proceso" => "Editar",
                    "datosInsumos" => $datosInsumos,
                    "insumoCategoria" => TraerDatos($this->tablaInsumoCategoria, array("idSucursalInsumoCategoria" => $this->session->idSucursal, "estadoInsumoCategoria" => "Activo")),
                    "proveedores" => TraerDatos('proveedor', array("idSucursalProveedor" => $this->session->idSucursal, "estadoProveedor" => "Activo")),
                    "presentaciones" => TraerDatos('presentacion', array("idSucursalPresentacion" => $this->session->idSucursal, "estadoPresentacion" => "Activo")),
                    "presentacionesInsumo" => TraerDatos($this->tablaInsumoPresentacion, array("md5(idInsumo)" => $idInsumo, "estadoInsumoPresentacion" => "Activo")),
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/insumos.js"
                    ),
                );
                GblPlantilla("insumos/InsumosEditar", $datosVista, $extras, $titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $idInsumo = $this->input->post("idInsumo");
                $nombreInsumos = $this->input->post("nombreInsumos");
                $categoriaInsumos = $this->input->post("categoriaInsumos");
                $marcaInsumos = $this->input->post("marcaInsumos");
                $proveedor1Insumos = $this->input->post("proveedor1Insumos");
                $proveedor2Insumos = $this->input->post("proveedor2Insumos");
                $proveedor3Insumos = $this->input->post("proveedor3Insumos");
                $stockMinimoInsumos = $this->input->post("stockMinimoInsumo");
                $descripcionInsumos = $this->input->post("descripcionInsumos");
                $montoSugeridoInsumos = $this->input->post("montoSugeridoInsumos");
                $advaloremInsumos = (!is_null($this->input->post("advaloremInsumos"))) ? 1 : 0;
                $advaloremTipoInsumos = (!is_null($this->input->post("advaloremTipoInsumos"))) ? $this->input->post("advaloremTipoInsumos") : "";
                $exentoIVAInsumos = (!is_null($this->input->post("exentoIVAInsumos"))) ? 1 : 0;
                $perecederoInsumos = (!is_null($this->input->post("perecederoInsumos"))) ? 1 : 0;
                $revisarInsumos = (!is_null($this->input->post("revisarInsumos"))) ? 1 : 0;
                $valoresTabla = json_decode($this->input->post("valoresTabla"));

                $condicionExiste = array(
                    "md5(idInsumo) !=" => $idInsumo,
                    "idSucursalInsumo" => $this->session->idSucursal,
                    "nombreInsumo" => $nombreInsumos,
                    "idCategoriaInsumo" => $categoriaInsumos,
                    "estadoInsumo !=" => "Borrado"
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosInsumos = array(
                        "nombreInsumo" => $nombreInsumos,
                        "idCategoriaInsumo" => $categoriaInsumos,
                        "marcaInsumo" => $marcaInsumos,
                        "proveedor1Insumo" => $proveedor1Insumos,
                        "proveedor2Insumo" => $proveedor2Insumos,
                        "proveedor3Insumo" => $proveedor3Insumos,
                        "stockMinimoInsumo" => $stockMinimoInsumos,
                        "exentoIVAInsumo" => $exentoIVAInsumos,
                        "perecederoInsumo" => $perecederoInsumos,
                        "revisarInsumo" => $revisarInsumos,
                        "advaloremInsumo" => $advaloremInsumos,
                        "advaloremTipoInsumo" => $advaloremTipoInsumos,
                        "montoSugeridoInsumo" => $montoSugeridoInsumos,
                        "descripcionInsumo" => $descripcionInsumos,
                        "aleatorioInsumo" => uniqid()
                    );
                    $condicion = array(
                        "md5(idInsumo)" => $idInsumo
                    );
                    IniciarTransaccion();
                    $guardar = EditarDatos($this->tabla, $datosInsumos, $condicion);
                    ($guardar == false) ? $error = true : $error = false;
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 500;
                    } else {
                        $condicionExistePrese = array(
                            "md5(idInsumo) " => $idInsumo,
                            "estadoInsumoPresentacion !=" => "Borrado"
                        );
                        $existePre = ExistenDatos($this->tablaInsumoPresentacion, $condicionExistePrese);
                        if ($existePre) {

                            $datosPresentaciones = array(
                                "estadoInsumoPresentacion" => "Borrado"
                            );
                            $condicion = array(
                                "md5(idInsumo)" => $idInsumo
                            );
                            $eliminarPresentaciones = EditarDatos($this->tablaInsumoPresentacion, $datosPresentaciones, $condicion);
                            ($eliminarPresentaciones == false) ? $error = true : $error = false;
                        }

                        if ($error) {
                            DeshacerTransaccion();
                            $datosRespuesta["codigo"] = 501;
                        } else {
                            $costoPromedio = 0;
                            $idInsumo = TraerUnDatoIndividual($this->tabla, "idInsumo", array("md5(idInsumo)" => $idInsumo));
                            for ($i = 0; $i < count($valoresTabla); $i++) {
                                $idPresentacion = $valoresTabla[$i]->presentacion;
                                $descripcion = $valoresTabla[$i]->descripcion;
                                $unidad = $valoresTabla[$i]->unidad;
                                $costo = $valoresTabla[$i]->costo;
                                $precio = $valoresTabla[$i]->precio;
                                $inventario = $valoresTabla[$i]->inventario;

                                $datosPresentaciones = array(
                                    "idInsumo" => $idInsumo[0]['idInsumo'],
                                    "idPresentacion" => $idPresentacion,
                                    "descripcionInsumoPresentacion" => $descripcion,
                                    "unidadInsumoPresentacion" => $unidad,
                                    "costoInsumoPresentacion" => $costo,
                                    "precioInsumoPresentacion" => $precio,
                                    "unidadInventarioInsumoPresentacion" => $inventario
                                );
                                $guardar = GuardarDatos($this->tablaInsumoPresentacion, $datosPresentaciones);
                                ($guardar == false) ? $error = true : $error = false;
                                if($unidad == "1"){
                                    $costoPromedio = $costo;
                                }
                            }
                            // $actualizarCosto = EditarDatos("insumo",array("costoPromedioInsumo"=>$costoPromedio),array("idInsumo" => $idInsumo[0]['idInsumo']));
                            // if($actualizarCosto){
                            //     $agregarCosto = GuardarDatos("insumoCosto",array(
                            //         "idInsumo"=>$idInsumo[0]['idInsumo'],
                            //         "costoPromedioInsumoCosto"=>$costoPromedio,
                            //         "fechaRegistroInsumoCosto"=>date('Y-m-d H:i:s'),
                            //         "estadoInsumoCosto"=>"Activo",
                            //         )
                            //     );
                            //     if($agregarCosto){
                            //         $error = false;
                            //         $datosRespuesta["codigo"] = 200;
                            //     } else {
                            //         $datosRespuesta["codigo"] = 504;
                            //         $error = true;    
                            //     }
                            // }else{
                            //     $datosRespuesta["codigo"] = 503;
                            //     $error = true;  
                            // }
                            if ($error) {
                                DeshacerTransaccion();
                                $datosRespuesta["codigo"] = 502;
                            } else {
                                EjecutarTransaccion();
                                $datosRespuesta["codigo"] = 200;
                            }
                        }
                    }
                } else {
                    $datosRespuesta["codigo"] = 400;
                }
                echo json_encode($datosRespuesta);
            }
        }
    }
    function InsumosCambiarEstado() {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idInsumo = $this->input->post("idInsumo");
                $condicionDatos = array(
                    'md5(idInsumo)' => $idInsumo,
                    'estadoInsumo' => 'Activo',
                );
                $activoinsumo = ExistenDatos($this->tabla, $condicionDatos);

                ($activoinsumo == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosinsumos = array(
                    "estadoInsumo" => $nuevoEstado
                );
                $condicion = array("md5(idInsumo)" => $idInsumo);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosinsumos, $condicion);
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
    function InsumosEliminar() {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idinsumo = $this->input->post("idInsumo");
                $datosinsumos = array(
                    "estadoInsumo" => 'Borrado'
                );
                $condicion = array("md5(idInsumo)" => $idinsumo);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosinsumos, $condicion);
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
    function InsumosPresentaciones() {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $id = uniqid();
                $presentaciones = TraerDatos($this->tablaPresentacion, array("estadoPresentacion" => "Activo"));
                $tbody = "";
                $tbody .= '<tr>';
                $tbody .= '<td>';
                $tbody .= '<div class="icheck-'. GblTraerConfiguracion('colorComponentes').' d-inline">';
                $tbody .= '    <input type="radio" id="paraInventario'.$id.'" class="preInventario" name="paraInventario" >';
                $tbody .= '    <label for="paraInventario'.$id.'"></label>';
                $tbody .= '</div>';
                $tbody .= '</td>';
                $tbody .= '<td>';
                $tbody .= '<select class="form-control col-12 select2 presentacion">';
                $tbody .= '<option value="">Seleccionar</option>';
                foreach ($presentaciones as $pre) {
                    $tbody .= '<option value="' . $pre->idPresentacion . '">' . $pre->nombrePresentacion . ' (' . $pre->unidadPresentacion . ')</option>';
                }
                $tbody .= '</select>';
                $tbody .= '</td>';
                $tbody .= '<td><input type="hidden" class="form-control upper text-uppercase descripcion" value="."></td>';
                $tbody .= '<td><input type="text" class="form-control numeric unidad" ></td>';
                $tbody .= '<td><input type="text" class="form-control decimal costo" ></td>';
                $tbody .= '<td><input type="hidden" class="form-control decimal precio" value="0.00"></td>';
                $tbody .= '<td><button type="button" class="btn btn-sm btn-danger btn-block borrarTablaPresentacionInsumos"><i class="fa fa-trash"></i></button></td>';
                $tbody .= '</tr>';

                $datosRespuesta["codigo"] = 200;
                $datosRespuesta["tbody"] = $tbody;
            }
        }
        echo json_encode($datosRespuesta);
    }
    function InsumosVerificarBorrado() {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idInsumo = $this->input->post("idInsumo");
                $idPresentacion = $this->input->post("idPresentacion");

                $verificar = ExistenDatos("productoInsumo", array("md5(idInsumo)" => $idInsumo, "idPresentacionProductoInsumo" => $idPresentacion, "estadoProductoInsumo" => "Activo"));
                if ($verificar) {
                    $datosRespuesta["codigo"] = 500;
                } else {
                    $datosRespuesta["codigo"] = 200;
                }
            }
        }
        echo json_encode($datosRespuesta);
    }
}
/* End of file insumos.php */
