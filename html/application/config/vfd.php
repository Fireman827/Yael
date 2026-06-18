<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'out_dir' => 'J:\\VFD\\vfd_bridge_project\\out\\', // carpeta que el bridge debe vigilar
    'use_tempfile' => true,
    'file_prefix' => 'vfd_',
    'file_ext' => '.json',
    'bridge_command' => '', // o la ruta al exe si prefieres ejecutar el bridge directamente
    'debug' => false
);
