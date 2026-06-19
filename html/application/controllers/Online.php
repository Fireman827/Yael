<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Online.php (POS) — mantiene solo VerificarOnlinePendientes para el polling de cocina.
 * El storefront completo vive en html/pedidos/application/controllers/Online.php
 */
class Online extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Online_model');
        $this->load->library('session');
    }

    private function _json($codigo, $datos = array(), $mensaje = '') {
        $respuesta = array('codigo' => $codigo);
        if (!empty($datos))   $respuesta = array_merge($respuesta, $datos);
        if (!empty($mensaje)) $respuesta['mensaje'] = $mensaje;
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($respuesta));
    }

    // AJAX para cocina.js — polls every few seconds to show online order badge
    public function VerificarOnlinePendientes() {
        if ($this->input->method(TRUE) !== 'POST') {
            $this->_json(403, array(), 'Acceso denegado.');
            return;
        }
        $cantidad = $this->Online_model->ContarOnlinePendientes(1);
        $this->_json(200, array('cantidad' => (int)$cantidad));
    }
}
