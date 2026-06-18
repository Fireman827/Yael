<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cargar extends CI_Controller
{
    private $tabla = "inventario_bebidas";
    private $controlador = "Cargar";
    function __construct()
    {
        parent::__construct();
        $this->load->Model('CoreModel', "core");
    }

    public function index(){

    }

    public function CategoriaCargar(){
        GblValidarSesion($this);
        if($this->input->method(TRUE) == "POST"){
            $categorias = SimpleQuery("SELECT DISTINCT(COL3) AS cat FROM `".$this->tabla."` ");
            $error = false ;
            IniciarTransaccion();
            if($categorias){
                foreach($categorias as $c){
                    $datos = array(
                        "nombreInsumoCategoria" => $c->cat,
                        "idSucursalInsumoCategoria" => $this->session->idSucursal,
                        "descripcionInsumoCategoria" => $c->cat,
                        "estadoInsumoCategoria" => "Activo"
                    );
                    $idCategoria = GuardarDatos("insumoCategoria",$datos);
                    if(!$idCategoria){
                        $datosRespuesta["codigo"] = 501;
                        $error = true ;
                        break;
                        // $insumo = SimpleQuery("SELECT *  FROM `".$this->tabla."` WHERE COL3= '".$c->cat."'");
                        // if($insumo){
                        //     foreach($insumo as $i){
                        //         $nombre = $i->COL1;
                        //         $nombre = $i->COL1;
                        //         $nombre = $i->COL1;
                        //         $datos = array(
                        //             "nombreInsumoCategoria" => $c->cat,
                        //             "idSucursalInsumoCategoria" => $this->session->idSucursal,
                        //             "descripcionInsumoCategoria" => $c->cat,
                        //             "estadoInsumoCategoria" => "Activo"
                        //         );
                        //     }
                        // }
                    }
                }
            } else {
                $datosRespuesta["codigo"] = 500;
                $error = true ;
            }

            if(!$error){
                $datosRespuesta["codigo"] = 200;
                EjecutarTransaccion();
            } else {
                DeshacerTransaccion();
            }
            echo json_encode($datosRespuesta);
        }
    }
    public function PresentacionCargar(){
        GblValidarSesion($this);
        if($this->input->method(TRUE) == "POST"){
            $categorias = SimpleQuery("SELECT DISTINCT(COL3) AS cat FROM `".$this->tabla."` ");
            $error = false ;
            IniciarTransaccion();
            if($categorias){
                foreach($categorias as $c){
                    $datos = array(
                        "nombreInsumoCategoria" => $c->cat,
                        "idSucursalInsumoCategoria" => $this->session->idSucursal,
                        "descripcionInsumoCategoria" => $c->cat,
                        "estadoInsumoCategoria" => "Activo"
                    );
                    $idCategoria = GuardarDatos("insumoCategoria",$datos);
                    if(!$idCategoria){
                        $datosRespuesta["codigo"] = 501;
                        $error = true ;
                        break;
                        // $insumo = SimpleQuery("SELECT *  FROM `".$this->tabla."` WHERE COL3= '".$c->cat."'");
                        // if($insumo){
                        //     foreach($insumo as $i){
                        //         $nombre = $i->COL1;
                        //         $nombre = $i->COL1;
                        //         $nombre = $i->COL1;
                        //         $datos = array(
                        //             "nombreInsumoCategoria" => $c->cat,
                        //             "idSucursalInsumoCategoria" => $this->session->idSucursal,
                        //             "descripcionInsumoCategoria" => $c->cat,
                        //             "estadoInsumoCategoria" => "Activo"
                        //         );
                        //     }
                        // }
                    }
                }
            } else {
                $datosRespuesta["codigo"] = 500;
                $error = true ;
            }
            
            if(!$error){
                $datosRespuesta["codigo"] = 200;
                EjecutarTransaccion();
            } else {
                DeshacerTransaccion();
            }
            echo json_encode($datosRespuesta);
        }
    }
}
/* End of file Cargar.php */
