<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['correo_notif_from'] = 'grupobenoah@gmail.com';
$config['correo_notif_name'] = 'FHB Restaurant notificaciones';
/*
|--------------------------------------------------------------------------
| Destinatarios de notificaciones
|--------------------------------------------------------------------------
| - correo_notif_cc      → copia (futuro)
| - correo_notif_bcc     → copia oculta (futuro)
*/
$config['correo_notif_to']   = 'grupobenoah@gmail.com';
// FUTURO (opcional)
//$config['correo_notif_cc']  = [
//    'admin@empresa.com',
//    'soporte@empresa.com'
//];

//$config['correo_notif_bcc'] = [
//    'auditoria@empresa.com'
//];
$config['smtp_host']   = 'smtp.gmail.com';
$config['smtp_port']   = 587;
$config['smtp_user']   = 'grupobenoah@gmail.com';
$config['smtp_pass']   = 'rwbd ukvo bpza atef';
$config['smtp_crypto'] = 'tls';

