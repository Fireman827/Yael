<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* =====================================================
   LEER CONFIGURACIÓN DE CORREO POR PREFIJO
   Prefijos usados: 'EMAIL_' (pedidos web), 'NOTIF_EMAIL_' (notificaciones)

   Claves leídas de la tabla `configuraciones`:
     {prefijo}PROVIDER          -> SMTP | SENDGRID
     {prefijo}FROM               -> correo remitente
     {prefijo}FROM_NAME           -> nombre remitente
     {prefijo}SENDGRID_API_KEY
     {prefijo}SMTP_HOST
     {prefijo}SMTP_PORT
     {prefijo}SMTP_USER
     {prefijo}SMTP_PASS
     {prefijo}SMTP_CRYPTO
===================================================== */
function ObtenerConfigCorreo($prefijo)
{
    $claves = [
        'PROVIDER', 'FROM', 'FROM_NAME', 'SENDGRID_API_KEY',
        'SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASS', 'SMTP_CRYPTO',
    ];

    $cfg = [];
    foreach ($claves as $clave) {
        $row = TraerUnDato('configuraciones',
            "parametroConfiguracion='{$prefijo}{$clave}' AND estadoConfiguracion='Activo'");
        $cfg[$clave] = ($row && $row->valorConfiguracion !== '') ? $row->valorConfiguracion : '';
    }

    return $cfg;
}

/* =====================================================
   ENVIAR CORREO USANDO LA CONFIGURACIÓN GUARDADA
   (SMTP o SendGrid, según {prefijo}PROVIDER)

   @param string $prefijo        'EMAIL_' | 'NOTIF_EMAIL_' | etc.
   @param array  $destinatarios  Lista de correos (strings)
   @param string $asunto
   @param string $htmlBody
   @return bool
===================================================== */
function EnviarCorreoConConfig($prefijo, $destinatarios, $asunto, $htmlBody)
{
    $destinatarios = array_filter(array_map('trim', (array) $destinatarios));
    if (empty($destinatarios)) {
        log_message('error', "[Correo {$prefijo}] Sin destinatarios.");
        return false;
    }

    $cfg = ObtenerConfigCorreo($prefijo);
    $proveedor = strtoupper($cfg['PROVIDER'] ?: 'SMTP');

    if ($proveedor === 'SENDGRID') {
        return _EnviarCorreoSendGrid($cfg, $destinatarios, $asunto, $htmlBody, $prefijo);
    }

    return _EnviarCorreoSmtp($cfg, $destinatarios, $asunto, $htmlBody, $prefijo);
}

/* =====================================================
   ENVÍO VÍA SMTP (librería email de CodeIgniter)
===================================================== */
function _EnviarCorreoSmtp($cfg, $destinatarios, $asunto, $htmlBody, $prefijo)
{
    if (empty($cfg['SMTP_HOST']) || empty($cfg['SMTP_USER'])) {
        log_message('error', "[Correo {$prefijo}] Configuración SMTP incompleta.");
        return false;
    }

    $CI =& get_instance();
    $CI->load->library('email');

    $CI->email->initialize([
        'protocol'    => 'smtp',
        'smtp_host'   => $cfg['SMTP_HOST'],
        'smtp_port'   => (int) ($cfg['SMTP_PORT'] ?: 587),
        'smtp_user'   => $cfg['SMTP_USER'],
        'smtp_pass'   => $cfg['SMTP_PASS'],
        'smtp_crypto' => $cfg['SMTP_CRYPTO'] ?: 'tls',
        'mailtype'    => 'html',
        'charset'     => 'utf-8',
        'newline'     => "\r\n",
        'wordwrap'    => true,
    ]);

    $from = $cfg['FROM'] ?: $cfg['SMTP_USER'];
    $name = $cfg['FROM_NAME'] ?: $from;

    $exito = true;
    foreach ($destinatarios as $correo) {
        $CI->email->clear(true);
        $CI->email->from($from, $name);
        $CI->email->to($correo);
        $CI->email->subject($asunto);
        $CI->email->message($htmlBody);

        if (!$CI->email->send()) {
            log_message('error', "[Correo {$prefijo} SMTP] Fallo al enviar a {$correo}: " . $CI->email->print_debugger());
            $exito = false;
        }
    }

    return $exito;
}

/* =====================================================
   ENVÍO VÍA API DE SENDGRID
===================================================== */
function _EnviarCorreoSendGrid($cfg, $destinatarios, $asunto, $htmlBody, $prefijo)
{
    if (empty($cfg['SENDGRID_API_KEY'])) {
        log_message('error', "[Correo {$prefijo} SendGrid] API Key no configurada.");
        return false;
    }

    $from = $cfg['FROM'] ?: 'no-reply@firehouseburger.com';
    $name = $cfg['FROM_NAME'] ?: $from;

    $personalizations = [];
    foreach ($destinatarios as $correo) {
        $personalizations[] = ['to' => [['email' => $correo]]];
    }

    $payload = [
        'personalizations' => $personalizations,
        'from'    => ['email' => $from, 'name' => $name],
        'subject' => $asunto,
        'content' => [['type' => 'text/html', 'value' => $htmlBody]],
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://api.sendgrid.com/v3/mail/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $cfg['SENDGRID_API_KEY'],
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    log_message('debug', "[Correo {$prefijo} SendGrid] HTTP {$code} | resp: {$resp}");

    return ($code >= 200 && $code < 300);
}
