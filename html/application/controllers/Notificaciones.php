<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificaciones extends CI_Controller {

    public $controlador = 'Notificaciones';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('CoreModel', 'core');
    }

    /* =====================================================
       LISTADO GENERAL (PANTALLA)
       listarNotificacionesUsuario ya filtra por rol
       internamente (admin ve todo, usuario ve lo suyo)
    ===================================================== */
    public function index($pagina = 1)
    {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla('plantilla/permiso', array(), array(), 'No autorizado');
            return;
        }

        $idUsuario  = $this->session->idUsuario;
        $idSucursal = $this->session->idSucursal;

        $pagina = max((int) $pagina, 1);
        $limite = 15;
        $offset = ($pagina - 1) * $limite;

        // listarNotificacionesUsuario ya maneja admin vs usuario internamente
        $todasNotif = $this->core->listarNotificacionesUsuario($idUsuario);

        $total = count($todasNotif);

        // Calendario — usa TraerPagosCalendario del CoreModel (corregido)
        $pagosCalendario = $this->core->TraerPagosCalendario($idSucursal);

        $data = array(
            'notificaciones'  => array_slice($todasNotif, $offset, $limite),
            'pagina'          => $pagina,
            'totalPaginas'    => (int) ceil($total / $limite),
            'pagosCalendario' => $pagosCalendario ? $pagosCalendario : array(),
        );

        $extras = array(
            'css' => array('vendors/fullcalendar/main.min.css'),
            'js'  => array(
                'vendors/fullcalendar/main.min.js',
                'vendors/fullcalendar/locales/es.js',
                'scripts/notificaciones.js',
            ),
        );

        GblPlantilla('notificaciones/index', $data, $extras, 'Notificaciones');
    }

    /* =====================================================
       MARCAR UNA NOTIFICACIÓN COMO LEÍDA (AJAX)
    ===================================================== */
    public function marcarLeidaAjax()
    {
        $id = (int) $this->input->post('idNotificacion');

        if ($id <= 0) {
            echo json_encode(array('ok' => 0, 'error' => 'ID inválido'));
            return;
        }

        $ok = $this->core->marcarNotificacionLeida($id);
        echo json_encode(array('ok' => $ok ? 1 : 0));
    }

    /* =====================================================
       MARCAR TODAS COMO LEÍDAS (AJAX) — scope sucursal
    ===================================================== */
    public function marcarTodasAjax()
    {
        $idSucursal = $this->session->idSucursal;
        $this->core->marcarTodasLeidasSucursal($idSucursal);
        echo json_encode(array('ok' => 1));
    }

    /* =====================================================
       CONTADOR CAMPANA (AJAX)
    ===================================================== */
    public function ajaxContador()
    {
        $idSucursal = $this->session->idSucursal;
        echo json_encode(array(
            'total' => (int) $this->core->contarNotificacionesSucursal($idSucursal)
        ));
    }
}
