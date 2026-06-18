<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pagos extends CI_Controller {

    private $tabla        = 'pago';
    private $tablaDetalle = 'pagodetalle';
    private $controlador  = 'Pagos';

    function __construct()
    {
        parent::__construct();
        $this->load->model('CoreModel', 'core');
    }

    /* =====================================================
       LISTADO DE PAGOS (DATATABLE + CALENDARIO)
    ====================================================== */
    public function index()
    {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla('plantilla/permiso', array(), array(), 'No autorizado');
            return;
        }

        $titulo     = 'Pagos';
        $idSucursal = $this->session->idSucursal;

        // Calendario con query directo (columnas reales de tabla pago)
        $pagosCalendario = array();
        $hoy  = date('Y-m-d');
        $rows = $this->db
            ->select('idPago, nombrePago, montoPago, fechaPago, estadoPago')
            ->where('idSucursalPago', $idSucursal)
            ->where('estadoPago !=', 'Borrado')
            ->get('pago')
            ->result();

        foreach ($rows as $p) {
            if (empty($p->fechaPago)) continue;
            $color = '#28a745';
            if ($p->estadoPago === 'Inactivo') $color = '#ffc107';
            if ($p->fechaPago < $hoy && $p->estadoPago === 'Inactivo') $color = '#dc3545';

            $pagosCalendario[] = array(
                'id'    => $p->idPago,
                'title' => $p->nombrePago . ' ($' . number_format($p->montoPago, 2) . ')',
                'start' => $p->fechaPago,
                'color' => $color,
                'allDay'=> true,
                'url'   => base_url('Pagos/PagosEditar/' . md5($p->idPago)),
            );
        }

        $datosVista = array(
            'titulo'            => $titulo,
            'icono'             => 'fa fa-money-bill-alt',
            'mostrarCalendario' => true,
            'pagosCalendario'   => json_encode($pagosCalendario),
            'botones'           => array(
                array(
                    'icono'       => 'fa fa-plus',
                    'controlador' => $this->controlador,
                    'url'         => 'PagosAgregar',
                    'txt'         => 'Agregar Pago',
                    'posicion'    => 'right',
                    'tipo'        => GblTraerConfiguracion('colorComponentes'),
                    'modal'       => false,
                    'id'          => '',
                ),
            ),
            'encabezados' => array(
                'ID'       => 1,
                'Nombre'   => 3,
                'Monto'    => 2,
                'Estado'   => 1,
                'Acciones' => 1,
            ),
            'admin'      => $this->session->admin,
            'idSucursal' => $idSucursal,
            'sucursales' => TraerDatos('sucursal'),
        );

        $extras = array(
            'css' => array('vendors/fullcalendar/main.min.css'),
            'js'  => array(
                'vendors/fullcalendar/main.min.js',
                'vendors/fullcalendar/locales/es.js',
                'scripts/pagos.js',
            ),
        );

        GblPlantilla('plantilla/admin', $datosVista, $extras, $titulo);
    }

    /* =====================================================
       DATATABLE — query directo con $this->db
       TraerDatosTabla causaba "Unknown column" porque
       asumía columnas de tabla 'pagos' en tabla 'pago'
    ====================================================== */
    public function PagosMostrar()
    {
        $draw   = intval($this->input->post('draw'));
        $start  = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));

        $order  = $this->input->post('order');
        $search = isset($this->input->post('search')['value'])
                  ? trim($this->input->post('search')['value']) : '';

        $col = 0;
        $dir = 'asc';

        if (!empty($order)) {
            $col = (int) $order[0]['column'];
            $dir = ($order[0]['dir'] === 'desc') ? 'desc' : 'asc';
        }

        $columnas = array(
            0 => 'idPago',
            1 => 'nombrePago',
            2 => 'montoPago',
            3 => 'estadoPago',
        );
        $ordenCampo = isset($columnas[$col]) ? $columnas[$col] : 'idPago';

        // Sucursal: primero intentar del POST, si no usar la de sesión
        $sucursal = $this->input->post('sucursal');
        if (!empty($sucursal)) {
            $this->session->idSucursal = $sucursal;
        }
        $sucursal = $this->session->idSucursal;

        // ── Contar total ──────────────────────────────────
        $this->db->from('pago');
        $this->db->where('idSucursalPago', $sucursal);
        $this->db->where('estadoPago !=', 'Borrado');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nombrePago', $search);
            $this->db->or_like('estadoPago', $search);
            $this->db->group_end();
        }
        $total = $this->db->count_all_results();   // reset automático

        // ── Traer registros ───────────────────────────────
        $this->db->select('idPago, nombrePago, montoPago, estadoPago, fechaPago');
        $this->db->from('pago');
        $this->db->where('idSucursalPago', $sucursal);
        $this->db->where('estadoPago !=', 'Borrado');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nombrePago', $search);
            $this->db->or_like('estadoPago', $search);
            $this->db->group_end();
        }
        $this->db->order_by($ordenCampo, $dir);
        $this->db->limit($length, $start);
        $pagos = $this->db->get()->result();

        // Log para diagnóstico (quitar en producción)
        log_message('debug', '[PagosMostrar] sucursal=' . $sucursal . ' total=' . $total . ' query=' . $this->db->last_query());

        $data = array();

        foreach ($pagos as $p) {
            $color  = ($p->estadoPago === 'Activo') ? 'success' : 'warning';
            $estado = "<span class='badge badge-{$color}'>{$p->estadoPago}</span>";

            $acciones = "
            <div class='btn-group'>
                <a href='" . base_url('Pagos/PagosEditar/' . md5($p->idPago)) . "'
                   class='btn btn-sm btn-primary' title='Editar'>
                    <i class='fa fa-edit'></i>
                </a>
                <button class='btn btn-sm btn-danger btn-borrar-pago'
                        data-id='{$p->idPago}'
                        data-nombre='" . htmlspecialchars($p->nombrePago) . "'
                        title='Borrar'>
                    <i class='fa fa-trash'></i>
                </button>
            </div>";

            $data[] = array(
                $p->idPago,
                htmlspecialchars($p->nombrePago),
                '$' . number_format($p->montoPago, 2),
                $estado,
                $acciones,
            );
        }

        echo json_encode(array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ));
    }

    /* =====================================================
       AGREGAR PAGO
    ====================================================== */
    public function PagosAgregar()
    {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla('plantilla/permiso', array(), array(), 'No autorizado');
            return;
        }

        if ($this->input->method(TRUE) === 'GET') {
            $datosVista = array(
                'titulo'      => 'Agregar Pago',
                'controlador' => $this->controlador,
                'proceso'     => 'Agregar',
            );
            $extras = array(
                'css' => array('vendors/fullcalendar/main.min.css'),
                'js'  => array(
                    'vendors/fullcalendar/main.min.js',
                    'vendors/fullcalendar/locales/es.js',
                    'scripts/pagos.js',
                ),
            );
            GblPlantilla('pagos/pagosAgregar', $datosVista, $extras, 'Agregar Pago');
            return;
        }

        // POST
        $nombrePago = $this->input->post('nombrePago');
        $montoPago  = $this->input->post('montoPago');
        $fechaPago  = $this->input->post('fechaPago');
        $estadoPago = $this->input->post('estadoPago');

        if (!$nombrePago || !$montoPago || !$fechaPago) {
            log_message('error', '[PagosAgregar] Campos vacíos: nombre=' . $nombrePago . ' monto=' . $montoPago . ' fecha=' . $fechaPago);
            echo json_encode(array('codigo' => 400));
            return;
        }

        $datos = array(
            'idSucursalPago' => $this->session->idSucursal,
            'nombrePago'     => strtoupper($nombrePago),
            'montoPago'      => $montoPago,
            'fechaPago'      => $fechaPago,
            'estadoPago'     => $estadoPago ? $estadoPago : 'Activo',
            'aleatorioPago'  => uniqid('PAGO-'),
        );

        log_message('debug', '[PagosAgregar] datos=' . json_encode($datos));

        $id = GuardarDatos('pago', $datos);

        log_message('debug', '[PagosAgregar] insert_id=' . $id . ' last_query=' . $this->db->last_query());

        echo json_encode(array('codigo' => $id ? 200 : 500));
    }

    /* =====================================================
       EDITAR PAGO
    ====================================================== */
    public function PagosEditar($hash = '')
    {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            GblPlantilla('plantilla/permiso', array(), array(), 'No autorizado');
            return;
        }

        if ($this->input->method(TRUE) === 'GET') {
            $pago = TraerUnDato($this->tabla, array('md5(idPago)' => $hash));
            if (!$pago) {
                GblPlantilla('plantilla/error', array(), array(), 'Pago no encontrado');
                return;
            }

            $datosVista = array(
                'titulo'      => 'Editar Pago',
                'datosPago'   => $pago,
                'controlador' => $this->controlador,
                'proceso'     => 'Editar',
            );
            $extras = array('js' => array('scripts/pagos.js'));
            GblPlantilla('pagos/pagosEditar', $datosVista, $extras, 'Editar Pago');
            return;
        }

        // POST
        $idPago = $this->input->post('idPago');

        EditarDatos($this->tabla, array(
            'nombrePago' => $this->input->post('nombrePago'),
            'montoPago'  => $this->input->post('montoPago'),
            'fechaPago'  => $this->input->post('fechaPago'),
            'estadoPago' => $this->input->post('estadoPago'),
        ), array('idPago' => $idPago));

        echo json_encode(array('codigo' => 200));
    }


    /* =====================================================
       BORRAR PAGO (LÓGICO) — cambia estadoPago a 'Borrado'
       No elimina el registro, mantiene historial.
       POST: idPago
    ===================================================== */
    public function PagosBorrar()
    {
        if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
            echo json_encode(array('codigo' => 403));
            return;
        }

        $idPago = (int) $this->input->post('idPago');

        if ($idPago <= 0) {
            echo json_encode(array('codigo' => 400));
            return;
        }

        EditarDatos($this->tabla,
            array('estadoPago' => 'Borrado'),
            array('idPago'     => $idPago)
        );

        echo json_encode(array('codigo' => 200));
    }

}
/* End of file Pagos.php */

