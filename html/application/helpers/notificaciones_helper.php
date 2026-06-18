<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* =====================================================
   CREAR NOTIFICACIÓN (BASE)
   Campos BD: idNotificacion, tipo, nivel, referencia_id,
   titulo, mensaje, url, idUsuario, idSucursal, estado,
   enviarCorreo, correoDestino, adjunto, fechaCreacion,
   fechaActualizacion, leida, fecha, correoEnviado,
   fechaUltimoCorreo
===================================================== */
function CrearNotificacion(array $data)
{
    $CI =& get_instance();

    $insert = [
        'tipo'               => $data['tipo'],
        'nivel'              => $data['nivel'] ?? 'warning',       // enum: warning | danger
        'referencia_id'      => $data['referencia_id'] ?? null,
        'titulo'             => $data['titulo'],
        'mensaje'            => $data['mensaje'],
        'url'                => $data['url'] ?? null,
        'idUsuario'          => $data['idUsuario'] ?? -1,           // -1 = global / sucursal
        'idSucursal'         => $data['idSucursal'] ?? null,
        'estado'             => 'pendiente',                        // enum: pendiente | leida | cerrada
        'leida'              => 0,
        'enviarCorreo'       => $data['enviarCorreo'] ?? 0,
        'correoDestino'      => $data['correoDestino'] ?? null,     // columna real en BD
        'adjunto'            => $data['adjunto'] ?? null,
        'correoEnviado'      => 0,
        'fechaUltimoCorreo'  => null,
        'fechaCreacion'      => date('Y-m-d H:i:s'),
        'fechaActualizacion' => null,
        'fecha'              => date('Y-m-d H:i:s'),
    ];

    return $CI->db->insert('notificaciones', $insert);
}

/* =====================================================
   MARCAR UNA NOTIFICACIÓN COMO LEÍDA
===================================================== */
function MarcarNotificacionLeida($idNotificacion)
{
    $CI =& get_instance();

    return $CI->db
        ->where('idNotificacion', $idNotificacion)
        ->update('notificaciones', [
            'estado'             => 'leida',
            'leida'              => 1,
            'fechaActualizacion' => date('Y-m-d H:i:s'),
        ]);
}

/* =====================================================
   CERRAR NOTIFICACIÓN (CIERRE LÓGICO, SIN ELIMINAR)
===================================================== */
function CerrarNotificacion($tipo, $referenciaId, $idSucursal = null)
{
    $CI =& get_instance();

    $CI->db
        ->where('tipo', $tipo)
        ->where('referencia_id', $referenciaId)
        ->where('estado !=', 'cerrada');

    if ($idSucursal !== null) {
        $CI->db->where('idSucursal', $idSucursal);
    }

    return $CI->db->update('notificaciones', [
        'estado'             => 'cerrada',
        'fechaActualizacion' => date('Y-m-d H:i:s'),
    ]);
}

/* =====================================================
   SEVERIDAD DE STOCK
   Retorna 'danger' | 'warning' | null
===================================================== */
function SeveridadStock($cantidad, $minimo)
{
    if ($cantidad <= 0) {
        return 'danger';   // Sin stock
    }

    if ($cantidad <= $minimo) {
        return 'warning';  // Bajo stock
    }

    return null; // OK, no notificar
}

/* =====================================================
   SEVERIDAD DE PAGOS
   Retorna 'danger' | 'warning' | null
===================================================== */
function SeveridadPago($dias)
{
    if ($dias < 0) {
        return 'danger';  // Vencido
    }

    if ($dias <= 3) {
        return 'warning'; // Por vencer
    }

    return null; // Sin urgencia
}

/* =====================================================
   UI DE NOTIFICACIONES (SOLO PRESENTACIÓN)
===================================================== */
function NotificacionUI($tipo, $nivel = 'warning', $estado = 'pendiente')
{
    $iconos = [
        'STOCK_BAJO'       => 'fas fa-box',
        'PAGO'             => 'fas fa-file-invoice-dollar',
        'PAGO_VENCIDO'     => 'fas fa-exclamation-circle',
        'PAGO_ADVERTENCIA' => 'fas fa-clock',
        'INFO'             => 'fas fa-info-circle',
    ];

    return [
        'icono'    => $iconos[$tipo] ?? 'fas fa-info-circle',
        'color'    => $nivel,
        'opacidad' => ($estado === 'leida') ? '0.5' : '1',
    ];
}

/* =====================================================
   PROCESAR NOTIFICACIONES DE STOCK BAJO
   (se llama desde scheduler / cron)
===================================================== */
function ProcesarNotificacionesStock()
{
    $CI =& get_instance();
    $CI->load->model('CoreModel');

    $sucursales = $CI->db
        ->select('idSucursal')
        ->group_by('idSucursal')
        ->get('insumostock')
        ->result();

    foreach ($sucursales as $sucursal) {

        $insumos = $CI->CoreModel->obtenerInsumosBajoStockPorSucursal($sucursal->idSucursal);

        foreach ($insumos as $i) {

            $nivel = SeveridadStock($i->cantidadInsumoStock, $i->stockMinimoInsumo);
            if (!$nivel) continue;

            // Evitar duplicados activos
            if ($CI->CoreModel->existeNotificacionActiva('STOCK_BAJO', $i->idInsumo, $sucursal->idSucursal)) {
                continue;
            }

            CrearNotificacion([
                'tipo'          => 'STOCK_BAJO',
                'nivel'         => $nivel,
                'referencia_id' => $i->idInsumo,
                'titulo'        => 'Insumo con stock bajo',
                'mensaje'       => "{$i->nombreInsumo} tiene {$i->cantidadInsumoStock} en stock",
                'url'           => 'Insumos',
                'idSucursal'    => $sucursal->idSucursal,
                'enviarCorreo'  => 1,
            ]);
        }
    }
}

/* =====================================================
   PROCESAR NOTIFICACIONES DE PAGOS PRÓXIMOS
   (se llama desde scheduler / cron)
===================================================== */
function ProcesarNotificacionesPagos()
{
    $CI =& get_instance();
    $CI->load->model('CoreModel');

    $pagos = $CI->CoreModel->obtenerPagosProximos();

    foreach ($pagos as $pago) {

        $fechaPago = new DateTime($pago->fechaPago);
        $hoy       = new DateTime();
        $dias      = (int) $hoy->diff($fechaPago)->format('%r%a');

        $nivel = SeveridadPago($dias);
        if (!$nivel) continue;

        if ($CI->CoreModel->existeNotificacionActiva('PAGO', $pago->idPago, $pago->idSucursalPago)) {
            continue;
        }

        $mensaje = $dias < 0
            ? "Pago '{$pago->nombrePago}' venció hace " . abs($dias) . " día(s)"
            : "Pago '{$pago->nombrePago}' vence en {$dias} día(s)";

        CrearNotificacion([
            'tipo'          => 'PAGO',
            'nivel'         => $nivel,
            'referencia_id' => $pago->idPago,
            'titulo'        => 'Pago de servicio pendiente',
            'mensaje'       => $mensaje,
            'url'           => 'Pagos/Editar/' . $pago->idPago,
            'idSucursal'    => $pago->idSucursalPago,
            'enviarCorreo'  => 1,
        ]);
    }
}
