<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Copia este archivo a database.php y completa los valores.
// local: fhbpos | produccion: fhbresturant

$active_group = 'default';
$active_record = TRUE;
$query_builder = TRUE;

$db['default'] = array(
    'dsn'          => '',
    'hostname'     => 'localhost',
    'username'     => 'USUARIO_AQUI',
    'password'     => 'PASSWORD_AQUI',
    'database'     => 'NOMBRE_DB_AQUI',
    'dbdriver'     => 'mysqli',
    'dbprefix'     => '',
    'pconnect'     => FALSE,
    'db_debug'     => (ENVIRONMENT !== 'production'),
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8',
    'dbcollat'     => 'utf8_general_ci',
    'swap_pre'     => '',
    'encrypt'      => FALSE,
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => TRUE,
);
