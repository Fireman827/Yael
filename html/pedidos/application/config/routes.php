<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Online/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Rutas públicas del storefront
$route['']                    = 'Online/login';
$route['login']               = 'Online/login';
$route['registro']            = 'Online/registro';
$route['verificar']           = 'Online/verificar';
$route['reenviar_otp']        = 'Online/reenviar_otp';
$route['recuperar']           = 'Online/recuperar';
$route['nueva_password']      = 'Online/nueva_password';
$route['logout']              = 'Online/logout';
$route['terminos']            = 'Online/terminos';

// Páginas autenticadas
$route['menu']                = 'Online/menu';
$route['menu/(:any)']         = 'Online/menu/$1';
$route['checkout']            = 'Online/checkout';
$route['confirmar']           = 'Online/confirmar';
$route['confirmacion']        = 'Online/confirmacion';
$route['confirmacion/(:any)'] = 'Online/confirmacion/$1';
$route['cuenta']              = 'Online/mi_cuenta';
$route['validarZona']         = 'Online/validarZona';

// Perfil del cliente
$route['perfil']              = 'Online/perfil';
$route['guardarPerfil']       = 'Online/guardarPerfil';
$route['guardarFacturacion']  = 'Online/guardarFacturacion';
$route['cambiarPassword']     = 'Online/cambiarPassword';

// AJAX — carrito y estado
$route['agregar_carrito']     = 'Online/agregar_carrito';
$route['carrito']             = 'Online/carrito';
$route['estado_pedido']       = 'Online/estado_pedido';
