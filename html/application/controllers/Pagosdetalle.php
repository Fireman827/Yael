<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PagosDetalle extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model("PagosDetalle_Model");
    }

    public function PagosDetalleAgregar(){
        GblValidarSesion($this);
        $idPago = $this->input->post("idPago");
        $descripcion = $this->input->post("descripcion");
        $cantidad = $this->input->post("cantidad");
        $precio = $this->input->post("precio");

        $data = array(
            "idPago" => $idPago,
            "descripcion" => $descripcion,
            "cantidad" => $cantidad,
            "precio" => $precio,
            "subtotal" => $cantidad * $precio
        );

        $r = $this->PagosDetalle_Model->Agregar($data);

        echo json_encode(array("codigo"=>$r ? 200 : 400));
    }

    public function PagosDetalleEditar(){
        GblValidarSesion($this);
        $idPagoDetalle = $this->input->post("idPagoDetalle");
        $detalle = $this->PagosDetalle_Model->ObtenerFila($idPagoDetalle);

        if($detalle){
            // la vista espera devolver TR completo
            $html = "
                <tr>
                    <td><input type='text' class='form-control form-control-sm descripcion' value='{$detalle->descripcion}'></td>
                    <td><input type='number' class='form-control form-control-sm cantidad' value='{$detalle->cantidad}'></td>
                    <td><input type='number' class='form-control form-control-sm precio decimal' value='{$detalle->precio}'></td>
                    <td><button class='btn btn-danger PagosDetalleBorrar' idPagoDetalle='{$detalle->idPagoDetalle}' type='button'><i class='fa fa-trash'></i></button></td>
                </tr>";

            echo json_encode([
                "codigo" => 200,
                "pagoDetalle" => $html
            ]);
        } else {
            echo json_encode(["codigo"=>400]);
        }
    }

    public function PagosDetalleEliminar(){
        GblValidarSesion($this);
        $idPagoDetalle = $this->input->post("idPagoDetalle");
        $r = $this->PagosDetalle_Model->Eliminar($idPagoDetalle);

        echo json_encode(["codigo" => $r ? 200 : 400]);
    }

    public function PagosDetalleCambiarEstado(){
        GblValidarSesion($this);
        $idPagoDetalle = $this->input->post("idPagoDetalle");
        $r = $this->PagosDetalle_Model->CambiarEstado($idPagoDetalle);

        echo json_encode(["codigo" => $r ? 200 : 400]);
    }
}
