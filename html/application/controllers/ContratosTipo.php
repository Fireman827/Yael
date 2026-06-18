<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ContratosTipo extends CI_Controller
{

    private $tabla = "contratoTipo";
    private $controlador = "ContratosTipo";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            $titulo = "Tipos de Contrato";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "fas fa-i-cursor",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => 'ContratosTipoAgregar',
                        'txt' => 'Agregar Tipo de Contrato',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => false,
                        'id'=>''
                    ),
                ),
                "encabezados" => array(
                    "ID" => 1,
                    "Nombre" => 3,
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
                    "scripts/contratosTipo.js"
                ),
            );
            GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
        }
    }
    function ContratosTipoMostrar(){
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
            0 => 'nombreContratoTipo'
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
		$condicion = array('idSucursalContratoTipo' => $sucursal, 'estadoContratoTipo !=' => 'Borrado');
		$ContratosTipo = TraerDatosTabla($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion);
        //Lectura de datos de la base para mostrar en el datatabla
        if ($ContratosTipo != 0) {
            $datosMostrar = array();
            foreach ($ContratosTipo as $ContratoTipo) {
                $estadoContratoTipo = $ContratoTipo->estadoContratoTipo;
                if ($estadoContratoTipo == 'Activo') {
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

                $funcion = "ContratosTipoEditar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item' href='".base_url().$funcion."/".md5($ContratoTipo->idContratoTipo)."' ><i class='fa fa-edit' ></i> Editar</a>";
                }
                $funcion = "ContratosTipoCambiarEstado";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$estadoTxt' idContratoTipo=" . md5($ContratoTipo->idContratoTipo) . "><i class='$estadoIcon'></i> $estadoTxt</a>";
                }
                $funcion = "ContratosTipoEliminar";
                if (GblPermisos($this, $funcion, $this->controlador)) {
                    $menuOpciones .= "<a class='dropdown-item " . $funcion . "' idContratoTipo=" . md5($ContratoTipo->idContratoTipo) . "><i class='fa fa-trash'></i> Eliminar</a>";
                }
                $menuOpciones .= "</div></div>";
                $datosMostrar[] = array(
                    $ContratoTipo->idContratoTipo,
                    $ContratoTipo->nombreContratoTipo,
                    $estadoSpan,
                    $menuOpciones
                );
            }
            $totalContratosTipo = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalContratosTipo,
                "recordsFiltered" => $totalContratosTipo,
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
    function ContratosTipoAgregar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if($this->input->method(TRUE) == "GET") {
                //CONSULTA PARA TRAER TODAS LAS CLAUSULAS
				$datosContratoClausula = TraerDatos('contratoClausula',array("estadoContratoClausula"=>"Activo"));
				$contratoClausulaOpciones = "";

                if($datosContratoClausula){
                    foreach ($datosContratoClausula as $contratoClausula){
						$contratoClausulaOpciones .= "<option value='".$contratoClausula->idContratoClausula."' >".$contratoClausula->nombreContratoClausula."</option>";
					}
                }

                $titulo = "Agregar Tipo de Contrato";
                $datosVista = array(
                    "titulo" => $titulo,
                    "icono" => "fas fa-i-cursor",
                    "controlador" => $this->controlador,
                    "proceso" => "Agregar",
                    "contratoClausulaOpciones" => $contratoClausulaOpciones,
                );
                $extras = array(
                    'css' => array(),
                    'js' => array(
                        "scripts/contratosTipo.js"
                    ),
                );
                //$this->load->view("contratosTipo/ContratoTipoAgregar",$datosVista);
                GblPlantilla("contratosTipo/ContratoTipoAgregar",$datosVista,$extras,$titulo);
            } else if ($this->input->method(TRUE) == "POST") {
                $nombreContratoTipo = $this->input->post("nombreContratoTipo");

                $datosContratoTipoClausula = json_decode($this->input->post("datosContratoTipoClausula"));

                $condicionExiste = array(
                    "nombreContratoTipo" => $nombreContratoTipo,
                    "idSucursalContratoTipo" => $this->session->idSucursal
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosContratosTipo = array(
                        "idSucursalContratoTipo" => $this->session->idSucursal,
                        "nombreContratoTipo" => $nombreContratoTipo,
                        "estadoContratoTipo" => 'Activo'
                    );
                    IniciarTransaccion();
                    $guardar = GuardarDatos($this->tabla, $datosContratosTipo);
                    ($guardar == false) ? $error = true : $error = false;
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 402;
                    } else {
                        if(count($datosContratoTipoClausula)!=0){
							foreach ($datosContratoTipoClausula as $contratoTipoClausula){
								$ContratoTipoClausula = array(
									"idContratoClausulaContratoTipoClausula" => $contratoTipoClausula[0],
									"idContratoTipoContratoTipoClausula" => $guardar
								);
								$guardarContratoTipoClausula = GuardarDatos("contratoTipoClausula",$ContratoTipoClausula);
							}
						}
						//La acción se realizo con éxito						
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
    function ContratosTipoEditar($idContratoTipo = ""){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
        } else {
            if ($this->input->method(TRUE) == "GET") {
                $datosContratosTipo = TraerUnDato($this->tabla, array('md5(idContratoTipo)' => $idContratoTipo));
                if($datosContratosTipo !== false && $idContratoTipo!=""){
                    //CONSULTA PARA TRAER TODAS LAS CLAUSULAS
					$datosContratoClausula = TraerDatos('contratoClausula',array("estadoContratoClausula"=>"Activo"));
					$contratoClausulaOpciones = "";
					//CONSULTA PARA TRAER LAS CLAUSULAS QUE LE PERTENECEN A UN TIPO DE CONTRATO A EDITAR
					$contratoTipoClausulaTabla = "";
					$existe = ExistenDatos('contratoTipoClausula', array("idContratoTipoContratoTipoClausula" => $datosContratosTipo->idContratoTipo));
					if($existe!=0){
						$datosContratoTipoClausula = TraerDatos('contratoTipoClausula',array("idContratoTipoContratoTipoClausula"=>$datosContratosTipo->idContratoTipo));
						
						foreach ($datosContratoTipoClausula as $contratoTipoClausula){
                            
                            foreach ($datosContratoClausula as $contratoClausula){
								if($contratoClausula->idContratoClausula==$contratoTipoClausula->idContratoClausulaContratoTipoClausula){
									$contratoTipoClausulaTabla .= "<tr>";
									$contratoTipoClausulaTabla .= "<td>".$contratoClausula->idContratoClausula."</td>";
							        $contratoTipoClausulaTabla .= "<td>".$contratoClausula->nombreContratoClausula."</td>";
							        $contratoTipoClausulaTabla .= "<td>".$contratoClausula->descripcionContratoClausula."</td>";
									$contratoTipoClausulaTabla .= "<td><button class='btn btn-danger btn-block ContratosTipoClausulaBorrar' idContratoTipoClausula='".$contratoTipoClausula->idContratoTipoClausula."' idContratoClausula='".$contratoClausula->idContratoClausula."' type='button' ><i class='fa fa-trash'></i></button></td>";													
									$contratoTipoClausulaTabla .= "</tr>";
								}
								$contratoClausulaOpciones .= "<option value='".$contratoClausula->idContratoClausula."' >".$contratoClausula->nombreContratoClausula."</option>"; 						
							}                           																			
						}
					} else {
						foreach ($datosContratoClausula as $contratoClausula){								
							$contratoClausulaOpciones .= "<option value='".$contratoClausula->idContratoClausula."' >".$contratoClausula->nombreContratoClausula."</option>"; 						
						}
					}

                    $titulo = "Editar Tipo de Contrato";
                    $datosVista = array(
                        "titulo" => $titulo,
                        "icono" => "fas fa-i-cursor",
                        "controlador" => $this->controlador,
                        "idContratoTipo" => $idContratoTipo,
                        "proceso" => "Editar",
                        "datosContratosTipo" => $datosContratosTipo,
                        "contratoClausulaOpciones" => $contratoClausulaOpciones,
                        "contratoTipoClausula" => $contratoTipoClausulaTabla,
                    );
                    $extras = array(
                        'css' => array(),
                        'js' => array(
                            "scripts/contratosTipo.js"
                        ),
                    );
                    //$this->load->view("contratosTipo/ContratoTipoEditar",$datosVista);
                    GblPlantilla("contratosTipo/ContratoTipoEditar",$datosVista,$extras,$titulo);
                } else {
					GblPlantilla("plantilla/error",array(),array(),"Error");
				}
            } else if ($this->input->method(TRUE) == "POST") {
                $idContratoTipo = $this->input->post("idContratoTipo");
                $nombreContratoTipo = $this->input->post("nombreContratoTipo");

                $datosContratoTipoClausula = json_decode($this->input->post("datosContratoTipoClausula"));

                $condicionExiste = array(
                    "nombreContratoTipo" => $nombreContratoTipo,
                    "idSucursalContratoTipo" => $this->session->idSucursal,
                    "idContratoTipo!="=> $idContratoTipo
                );
                $existe = ExistenDatos($this->tabla, $condicionExiste);
                if ($existe == 0) {
                    $datosContratosTipo = array(
                        "idSucursalContratoTipo" => $this->session->idSucursal,
                        "nombreContratoTipo" => $nombreContratoTipo,
                        'aleatorioContratoTipo' => uniqid()
                    );
                    IniciarTransaccion();
                    $condicion = array("idContratoTipo" => $idContratoTipo);
                    $guardar = EditarDatos($this->tabla, $datosContratosTipo,$condicion);
                    ($guardar == false) ? $error = true : $error = false;
                    if ($error) {
                        DeshacerTransaccion();
                        $datosRespuesta["codigo"] = 500;
                    } else {
                        foreach ($datosContratoTipoClausula as $contratoTipoClausula){
                            $existeContratoTipoClausula = ExistenDatos('contratoTipoClausula',array("idContratoTipoContratoTipoClausula"=>$idContratoTipo,"idContratoClausulaContratoTipoClausula"=>$contratoTipoClausula[0]));
                            if($existeContratoTipoClausula==0){
                                $ContratoTipoClausula = array(							
                                    "idContratoClausulaContratoTipoClausula" => $contratoTipoClausula[0],								
                                    "idContratoTipoContratoTipoClausula" => $idContratoTipo
                                );
                                $guardarContratoTipoClausula = GuardarDatos("contratoTipoClausula",$ContratoTipoClausula);
                            }					
                        }
                        //La acción se realizo con éxito						
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
    function ContratosTipoCambiarEstado(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContratoTipo = $this->input->post("idContratoTipo");
                $condicionDatos = array(
                    'md5(idContratoTipo)' => $idContratoTipo,
                    'estadoContratoTipo' => 'Activo',
                );
                $activoContratoTipo = ExistenDatos($this->tabla, $condicionDatos);

                ($activoContratoTipo == 0) ? $nuevoEstado = 'Activo' : $nuevoEstado = 'Inactivo';

                $datosContratosTipo = array(
                    "estadoContratoTipo" => $nuevoEstado
                );
                $condicion = array("md5(idContratoTipo)" => $idContratoTipo);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosContratosTipo, $condicion);
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
    function ContratosTipoEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContratoTipo = $this->input->post("idContratoTipo");
                $datosContratosTipo = array(
                    "estadoContratoTipo" => 'Borrado'
                );
                $condicion = array("md5(idContratoTipo)" => $idContratoTipo);
                IniciarTransaccion();
                $editar = EditarDatos($this->tabla, $datosContratosTipo, $condicion);
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

    function ContratosTipoClausula(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContratoClausula = $this->input->post("idContratoClausula");
				$string_tr = "";

				$condicionDatos = array('idContratoClausula' => $idContratoClausula);
				$datos = TraerUnDato("contratoClausula", $condicionDatos);
                if ($datos!="") {
					$string_tr .= "<tr>";
					$string_tr .= "<td>".$datos->idContratoClausula."</td>";
					$string_tr .= "<td>".$datos->nombreContratoClausula."</td>";
					$string_tr .= "<td>".$datos->descripcionContratoClausula."</td>";
					$string_tr .= "<td><button class='btn btn-block btn-sm btn-danger ContratosTipoClausulaBorrar' idContratoTipoClausula='' idContratoClausula='".$datos->idContratoClausula."' type='button'><i class='fa fa-trash'></i></button></td>";
					$string_tr .= "</tr>";
					$datosRespuesta["contratoClausula"] = $string_tr;
                    $datosRespuesta["codigo"] = 200;
                } else {
                    $datosRespuesta["codigo"] = 500;
                }
            }
        }
        echo json_encode($datosRespuesta);
	}

    function ContratosTipoClausulaEliminar(){
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            $datosRespuesta["codigo"] = 403;
        } else {
            if ($this->input->method(TRUE) == "POST") {
                $idContratoTipoClausula = $this->input->post("idContratoTipoClausula");

                $condicion = array("idContratoTipoClausula" => $idContratoTipoClausula);
                IniciarTransaccion();
                $eliminar = EliminarDatos("contratoTipoClausula", $condicion);
                if ($eliminar) {
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
/* End of file ContratosTipo.php */
