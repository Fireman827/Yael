<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* =====================================================
   PLANTILLA BASE DE CORREO HTML
===================================================== */
function PlantillaCorreoBase($titulo, $contenido)
{
    return '
    <html>
    <head>
        <style>
            body { font-family: Arial, Helvetica, sans-serif; background-color:#f4f6f9; padding:20px; }
            .contenedor { background:#ffffff; border-radius:6px; padding:20px; max-width:700px; margin:auto; }
            h2 { color:#dc3545; margin-top:0; }
            table { width:100%; border-collapse:collapse; margin-top:15px; }
            table th, table td { border:1px solid #ddd; padding:8px; font-size:13px; }
            table th { background:#343a40; color:#fff; }
            .warning { color:#d39e00; font-weight:bold; }
            .danger  { color:#dc3545; font-weight:bold; }
            .footer  { margin-top:20px; font-size:11px; color:#999; text-align:center; }
        </style>
    </head>
    <body>
        <div class="contenedor">
            <h2>' . $titulo . '</h2>
            ' . $contenido . '
            <p class="footer">Generado automáticamente por FHB Restaurant POS &mdash; ' . date('d/m/Y H:i') . '</p>
        </div>
    </body>
    </html>';
}

/* =====================================================
   ENVÍO DE CORREOS DE NOTIFICACIONES PENDIENTES

   @param array  $destinatarios  Lista de correos
   @param string $asunto         Asunto del correo
   @param array  $notificaciones Resultado de BD (objetos)

   Después de enviar exitosamente actualiza correoEnviado=1
   y fechaUltimoCorreo en la BD para evitar reenvíos.
===================================================== */
function EnviarCorreoNotificacionesPendientes($destinatarios, $asunto, $notificaciones)
{
    if (empty($destinatarios) || empty($notificaciones)) {
        return false;
    }

    $CI =& get_instance();

    /* --------------------------------------------------
       Construir tabla HTML
    -------------------------------------------------- */
    $html = '<table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Título</th>
                <th>Mensaje</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($notificaciones as $n) {
        $clase  = ($n->nivel === 'danger') ? 'danger' : 'warning';
        $fecha  = date('d/m/Y H:i', strtotime($n->fechaCreacion));
        $html  .= '<tr>
            <td class="' . $clase . '">' . htmlspecialchars($n->tipo)    . '</td>
            <td>'                         . htmlspecialchars($n->titulo)  . '</td>
            <td>'                         . htmlspecialchars($n->mensaje) . '</td>
            <td>'                         . $fecha                        . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    $mensaje = PlantillaCorreoBase('Notificaciones del Sistema', $html);

    /* --------------------------------------------------
       Envío usando configuración de correo de Notificaciones
       (SMTP o SendGrid, configurable en AdminOnline > Configuración)
    -------------------------------------------------- */
    $envioExitoso = EnviarCorreoConConfig('NOTIF_EMAIL_', $destinatarios, $asunto, $mensaje);

    /* --------------------------------------------------
       ✅ FIX: Marcar correoEnviado=1 + fechaUltimoCorreo
       para evitar reenvíos infinitos en el próximo ciclo
    -------------------------------------------------- */
    if ($envioExitoso) {

        $ids = array_map(fn($n) => $n->idNotificacion, $notificaciones);

        $CI->db
            ->where_in('idNotificacion', $ids)
            ->update('notificaciones', [
                'correoEnviado'    => 1,
                'fechaUltimoCorreo' => date('Y-m-d H:i:s'),
            ]);
    }

    return $envioExitoso;
}

/* =====================================================
   OBTENER Y ENVIAR NOTIFICACIONES PENDIENTES DE CORREO
   Por sucursal — llamar desde cron / scheduler

   Filtra solo las que tienen:
   - enviarCorreo = 1
   - correoEnviado = 0
   - estado != 'cerrada'
===================================================== */
function DespacharCorreosNotificaciones($idSucursal, $destinatarios, $asunto = 'Notificaciones Pendientes')
{
    $CI =& get_instance();

    $pendientes = $CI->db
        ->where('idSucursal',    $idSucursal)
        ->where('enviarCorreo',  1)
        ->where('correoEnviado', 0)
        ->where('estado !=',     'cerrada')
        ->get('notificaciones')
        ->result();

    if (empty($pendientes)) {
        return false; // Nada que enviar
    }

    return EnviarCorreoNotificacionesPendientes($destinatarios, $asunto, $pendientes);
}
