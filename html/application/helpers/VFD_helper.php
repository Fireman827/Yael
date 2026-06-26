<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('vfd_show_product')) {
    function vfd_show_product($cantidad, $precio, $producto) {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $cantidad = escapeshellarg($cantidad);
        $precio   = escapeshellarg($precio);
        $producto = escapeshellarg($producto);
        $cmd = "$exe item $cantidad $precio $producto";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_total')) {
    function vfd_show_total($total, $cambio) {
        $exe   = 'C:\\vfd\\vfd_bridge.exe';
        $total  = escapeshellarg($total);
        $cambio = escapeshellarg($cambio);
        $cmd = "$exe total $total $cambio";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_welcome')) {
    function vfd_show_welcome() {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $cmd = "$exe welcome";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_thanks')) {
    function vfd_show_thanks() {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $cmd = "$exe goodbye";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_close')) {
    function vfd_show_close() {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $cmd = "$exe closed";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}
