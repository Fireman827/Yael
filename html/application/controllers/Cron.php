<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('CoreModel');
        $this->load->helper('notificaciones');
        $this->load->helper('notificaciones_email');
    }

    /* =====================================================
       DESTINATARIOS DE CORREOS DE NOTIFICACIONES
       Configurable en AdminOnline > Configuración (NOTIF_EMAIL_TO,
       separados por coma). Si no hay configuración, usa el correo
       por defecto.
    ===================================================== */
    private function _destinatariosNotificaciones()
    {
        $cfg = TraerUnDato('configuraciones',
            "parametroConfiguracion='NOTIF_EMAIL_TO' AND estadoConfiguracion='Activo'");

        if (!$cfg || empty($cfg->valorConfiguracion)) {
            return ['grupobenoah@gmail.com'];
        }

        return array_filter(array_map('trim', explode(',', $cfg->valorConfiguracion)));
    }

    /*
        EJECUTAR:
        php index.php Cron ejecutar
    */
    public function ejecutar()
    {
        $this->insumosBajoStock();
        $this->pagosServicios();      // ✅ ACTIVO
        // $this->pagosSistema();      // ❌ FUTURO
        $this->enviarCorreosPendientes();
        $this->enviarCorreosProgramados();

        echo "CRON OK\n";
    }

    /* =====================================================
       1️⃣ INSUMOS BAJO STOCK
    ===================================================== */
    private function insumosBajoStock()
    {
        $usuarios = $this->db
            ->where('activoUsuario', 1)
            ->group_start()
                ->where('adminUsuario', 1)
                ->or_where('superAdminUsuario', 1)
            ->group_end()
            ->get('usuario')
            ->result();

        foreach ($usuarios as $u) {

            $idSucursal = $u->idSucursalUsuario;

            $insumos = $this->CoreModel
                ->obtenerInsumosBajoStockPorSucursal($idSucursal) ?? [];

            $idsActivos = [];

            foreach ($insumos as $i) {

                $nivel = SeveridadStock(
                    $i->cantidadInsumoStock,
                    $i->stockMinimoInsumo
                );

                if ($nivel === null) continue;

                $idsActivos[] = $i->idInsumo;

                if (!$this->CoreModel->existeNotificacionActiva(
                    'STOCK_BAJO',
                    $i->idInsumo,
                    $idSucursal
                )) {
                    CrearNotificacion([
                        'tipo'          => 'STOCK_BAJO',
                        'nivel'         => $nivel,
                        'referencia_id' => $i->idInsumo,
                        'titulo'        => 'Stock bajo',
                        'mensaje'       => "Insumo {$i->nombreInsumo} bajo mínimo. Stock actual: {$i->cantidadInsumoStock}",
                        'url'           => 'Insumos',
                        'idSucursal'    => $idSucursal,
                        'enviarCorreo'  => ($nivel === 'danger') ? 1 : 0
                    ]);
                }
            }

            $this->db
                ->where('tipo', 'STOCK_BAJO')
                ->where('idSucursal', $idSucursal)
                ->where('estado !=', 'cerrada');

            if (!empty($idsActivos)) {
                $this->db->where_not_in('referencia_id', $idsActivos);
            }

            $this->db->update('notificaciones', [
                'estado' => 'cerrada',
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /* =====================================================
       2️⃣ PAGOS DE SERVICIOS / PROVEEDORES (ACTIVO)
    ===================================================== */
    private function pagosServicios()
{
    $pagos = $this->db
        ->where('estadoPago', 'pendiente')
        ->get('pago')
        ->result();

    foreach ($pagos as $p) {

        $dias = (new DateTime())->diff(
            new DateTime($p->fechaPago)
        )->days;

        if ($p->fechaPago < date('Y-m-d')) {
            $dias *= -1;
        }

        // 🧠 CALENDARIO DE NOTIFICACIONES
        if (
            !in_array($dias, [-7, -3, 0]) &&
            $dias > 0
        ) {
            continue;
        }

        $nivel = SeveridadPago($dias);
        if ($nivel === null) continue;

        if (!$this->CoreModel->existeNotificacionActiva(
            'PAGO',
            $p->idPago,
            null
        )) {

            // 📝 MENSAJE DINÁMICO
            if ($dias > 0) {
                $msg = "Pago {$p->nombrePago} vence en {$dias} días ({$p->fechaPago})";
            } elseif ($dias == 0) {
                $msg = "Pago {$p->nombrePago} vence HOY ({$p->fechaPago})";
            } else {
                $msg = "Pago {$p->nombrePago} está VENCIDO ({$dias} días)";
            }

            CrearNotificacion([
                'tipo'          => 'PAGO',
                'nivel'         => $nivel,
                'referencia_id' => $p->idPago,
                'titulo'        => 'Pago de servicio',
                'mensaje'       => $msg,
                'url'           => 'Pagos/Editar/'.$p->idPago,
                'enviarCorreo'  => 1
            ]);
        }
    }
}


    /* =====================================================
       PAGOS DEL SISTEMA (DESACTIVADO - FUTURO)
    ===================================================== */
    /*
    private function pagosSistema()
    {
        // Se activará cuando el sistema entre en producción
    }
    */

    /* =====================================================
       3️⃣ ENVÍO DE CORREOS (NO BLOQUEANTE)
    ===================================================== */
    private function enviarCorreosPendientes()
{
    $notificaciones = $this->CoreModel
        ->obtenerNotificacionesParaCorreo();

    if (empty($notificaciones)) {
        return;
    }

    $destinatarios = $this->_destinatariosNotificaciones();

    if (EnviarCorreoNotificacionesPendientes(
        $destinatarios,
        'Notificaciones del sistema',
        $notificaciones
    )) {

        $ids = array_map(
            fn($n) => $n->idNotificacion,
            $notificaciones
        );

        $this->db
            ->where_in('idNotificacion', $ids)
            ->update('notificaciones', [
                'correoEnviado' => 1,
                'fechaActualizacion' => date('Y-m-d H:i:s')
            ]);
    }
}


    /* =====================================================
       4️⃣ ENVÍO PROGRAMADO (NOCTURNO)
    ===================================================== */
    private function enviarCorreosProgramados()
    {
        if ((int)date('H') < 19) return;

        $stock = $this->db
            ->where('tipo', 'STOCK_BAJO')
            ->where('estado', 'pendiente')
            ->where('correoEnviado', 0)
            ->get('notificaciones')
            ->result();

        $destinatarios = $this->_destinatariosNotificaciones();

        foreach ($stock as $n) {
            EnviarCorreoNotificacionesPendientes(
                $destinatarios,
                'Stock bajo',
                [$n]
            );
        }
    }
}
