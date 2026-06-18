<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('vfd_show_product')) {
    function vfd_show_product($cantidad, $precio, $producto) {
        $exe = 'C:\\vfd\\vfd_bridge.exe'; // ruta absoluta del exe en servidor Windows
        $cantidad = escapeshellarg($cantidad);
        $precio = escapeshellarg($precio);
        $producto = escapeshellarg($producto);
        $cmd = "$exe showProduct $cantidad $precio $producto";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_total')) {
    function vfd_show_total($total, $cambio) {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $total = escapeshellarg($total);
        $cambio = escapeshellarg($cambio);
        $cmd = "$exe showTotal $total $cambio";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_welcome')) {
    function vfd_show_welcome() {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $cmd = "$exe showWelcome";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_thanks')) {
    function vfd_show_thanks() {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $cmd = "$exe showThanks";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_close')) {
    function vfd_show_close() {
        $exe = 'C:\\vfd\\vfd_bridge.exe';
        $cmd = "$exe showClose";
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}
