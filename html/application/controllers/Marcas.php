<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Marcas extends CI_Controller
{

    private $tabla = "marca";
    private $controlador = "Marcas";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){
            GblValidarSesion($this);
            $titulo = "Marcacion";
            $datosVista = array(
                "titulo" => $titulo,
                "icono" => "far fa-clock",
                "botones" => array(
                    array(
                        "icono" => "fa fa-plus",
                        'controlador' => $this->controlador,
                        'url' => '',
                        'txt' => 'Marcar Entrada',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => false,
                        'id'=>'marcaEntrada'
                    ),
                    array(
                        "icono" => "fa fa-minus",
                        'controlador' => $this->controlador,
                        'url' => '',
                        'txt' => 'Marcar Salida',
                        'posicion' => 'right', // left, right
                        'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
                        'modal' => false,
                        'id'=>'marcaSalida'
                    ),
                ),
                "buscador" => true,
                "encabezados" => array(
                    "ID" => 1,
                    "Usuario" => 6,
                    "Hora Entrada" => 2,
                    "Hora Salida" => 2  ,
                    "Estado" => 1,
                    //"Acciones" => 1,
                ),
				//"admin"=>$this->session->admin,
				//"idSucursal"=>$this->session->idSucursal,
				//"sucursales"=>TraerDatos('sucursal'),
            );
            $extras = array(
                'css' => array(
                    "vendors/plugins/ckeditor_full/skins/moono-lisa/editor_gecko.css"
                ),
                'js' => array(
                    "scripts/marcas.js",
                    "vendors/plugins/ckeditor_full/ckeditor.js"
                ),
            );
            $encabezadoDatos = array(
                'titulo' => $titulo,
            );
            $this->load->view('plantilla2/encabezado',$encabezadoDatos);
            $this->load->view('marca/marca', $datosVista);
            $this->load->view('plantilla2/pie');
            $this->load->view('plantilla2/extras',$extras);
    }
    function MarcaMostrar(){
        GblValidarSesion($this);
        // Espacio propio del plugin data tabla
        //print_r($this->session);
        $draw = intval($this->input->post("draw"));
        $desdeFilas = intval($this->input->post("start"));
        $cantidadFilas = intval($this->input->post("length"));

        $buscador = $this->input->post("buscador");
        $buscadorTexto = $this->input->post("busqueda");

        $order = $this->input->post("order");
        $busquedaAreglo = $this->input->post("search");
        $busquedaParametro = ($buscador == "1") ? $buscadorTexto : $busquedaAreglo['value'];

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
            0 => 'idMarca',
            1 => 'nombreUsuario',
            2 => 'fechaHoraEntradaMarca',
            3 => 'fechaHoraSalidaMarca',
        );
        //Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
        if (!isset($columnasValidas[$col])) {
            $ordenCampos = null;
        } else {
            $ordenCampos = $columnasValidas[$col];
        }
        // Fin espacio del data tabla

        $joins = array(
            array(
                "tabla"=>"usuario",
                "condicion"=>"idUsuarioMarca = usuario.idUsuario",
                "tipo"=>"left",
                "campos"=>"nombreUsuario,fechaHoraEntradaMarca AS fechaEntrada,fechaHoraSalidaMarca AS fechaSalida "
            ),
        );
		$condicion = array( 'estadoMarca !=' => 'Borrado','fechaHoraEntradaMarca >='=>date("Y-m-d 00:00:01"));
		$Marcacion = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$joins);
        //print_r($Marcacion);
        //Lectura de datos de la base para mostrar en el datatabl$this->session->idSucursala
        if ($Marcacion != 0) {
            //print_r($Marcacion);
            $datosMostrar = array();
            foreach ($Marcacion as $marca) {

                $estadomarca = $marca->estadoMarca;
                if ($estadomarca == 'Activo') {
                    $estadoSpan = "<span class='badge badge-primary font-bold'>Vigente<span>";
                } else {
                    $estadoSpan = "<span class='badge badge-danger font-bold'>Finalizado<span>";
                }
                list($fechaE,$horaE) = explode(" ",$marca->fechaEntrada);
                list($fechaS,$horaS) = explode(" ",$marca->fechaSalida);
                $datosMostrar[] = array(
                    $marca->idMarca,
                    $marca->nombreUsuario,
                    hora($horaE),
                    hora($horaS),
                    // $marca->fechaSalida,
                    $estadoSpan,
                );
            }
            $totalMarcacion = TraerTotalDatos($this->tabla);
            $output = array(
                "draw" => $draw,
                "recordsTotal" => $totalMarcacion,
                "recordsFiltered" => $totalMarcacion,
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
    function MarcaEntrada(){
        if ($this->input->method(TRUE) == "POST") {
            $clave = $this->input->post("clave");
            $error = false;
            $usuario = TraerUnDato("usuario",array("codigoAutorizacionUsuario"=>$clave,"activoUsuario"=>1));
            if(!$usuario){
                $usuario = TraerUnDato("usuario",array("codigoUsuario"=>$clave,"activoUsuario"=>1));
                if(!$usuario){
                    $error = true;
                    $datosRespuesta["codigo"] = 500;
                }
            }
            $marcas = TraerUnDato("marca",array("idUsuarioMarca"=>$usuario->idUsuario,"estadoMarca"=>"Activo"));
            if($marcas){
                $error = true;
                $datosRespuesta["codigo"] = 501;
                $idMarca = $marcas->idMarca;
            }
            if(!$error){
                $datos = array(
                    //"idSucursalMarca"=>$this->session->idSucursal,
                    "idUsuarioMarca"=>$usuario->idUsuario,
                    "fechaHoraEntradaMarca"=>date("Y-m-d H:i:s"),
                    "estadoMarca"=>"Activo"
                );
                $marca = GuardarDatos("marca",$datos);
                ($marca) ? $error = false : $error = true;
            }
            if(!$error){
                $datosRespuesta['codigo'] = 200;
            } else {
                $datosRespuesta['codigo'] = 502;
            }
            echo json_encode($datosRespuesta);
        }
    }
    function MarcaSalida(){
        if ($this->input->method(TRUE) == "POST") {
            $clave = $this->input->post("clave");
            $error = false;
            $usuario = TraerUnDato("usuario",array("codigoAutorizacionUsuario"=>$clave,"activoUsuario"=>1));
            if(!$usuario){
                $usuario = TraerUnDato("usuario",array("codigoUsuario"=>$clave,"activoUsuario"=>1));
                if(!$usuario){
                    $error = true;
                    $datosRespuesta["codigo"] = 500;
                }
            }
            $marca = TraerUnDato("marca",array("idUsuarioMarca"=>$usuario->idUsuario,"estadoMarca"=>"Activo"));
            if($marca){
                $idMarca = $marca->idMarca;
            } else {
                $error = true;
                $datosRespuesta["codigo"] = 501;
            }
            IniciarTransaccion();
            if(!$error){
                $datos = array(
                    "fechaHoraSalidaMarca" => date("Y-m-d H:i:s"),
                    "estadoMarca" => 'Inactivo',
                );
                $condicion = array("idMarca" => $idMarca);
                $editar = EditarDatos($this->tabla, $datos, $condicion);
                ($editar) ? $error = false : $error = true;
            }

            if (!$error) {
                EjecutarTransaccion();
                $datosRespuesta["codigo"] = 200;
            } else {
                DeshacerTransaccion();
                $datosRespuesta["codigo"] = 502;
            }
            echo json_encode($datosRespuesta);
        }
    }

    // function MarcaVerificarEntrada(){
    //     if ($this->input->method(TRUE) == "POST") {

    //         $condicion = array(
    //             "estadoMarca" => 'Activo',
    //             "idUsuarioMarca" => $this->session->idUsuario,
    //             "idSucursalMarca"=>$this->session->idSucursal,
    //         );
    //         $existe = ExistenDatos("marca",$condicion);
    //         if ($existe) {
    //             $datosRespuesta["codigo"] = 500;
    //         } else {
    //             $datosRespuesta["codigo"] = 200;
    //         }
    //     }
    //     echo json_encode($datosRespuesta);
    // }

}
/* End of file Marcacion.php */
