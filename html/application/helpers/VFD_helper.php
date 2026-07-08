<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Exe compilado de C:\VFD\vfd_bridge_project (comandos: welcome, goodbye, closed, item, total)
define('VFD_EXE', 'C:\\VFD\\vfd_bridge_project\\bin\\Release\\vfd_bridge_project.exe');

if (!function_exists('vfd_exec')) {
    function vfd_exec($args) {
        $exe = VFD_EXE;
        if (!file_exists($exe)) return false;
        $cmd = '"' . $exe . '" ' . $args . ' 2>&1';
        exec($cmd, $output, $ret);
        return $ret === 0;
    }
}

if (!function_exists('vfd_show_welcome')) {
    function vfd_show_welcome() {
        return vfd_exec('welcome');
    }
}

if (!function_exists('vfd_show_thanks')) {
    function vfd_show_thanks() {
        return vfd_exec('goodbye');
    }
}

if (!function_exists('vfd_show_close')) {
    function vfd_show_close() {
        return vfd_exec('closed');
    }
}

if (!function_exists('vfd_show_product')) {
    function vfd_show_product($cantidad, $precio, $producto) {
        $a1 = escapeshellarg($cantidad);
        $a2 = escapeshellarg($precio);
        $a3 = escapeshellarg($producto);
        return vfd_exec("item $a1 $a2 $a3");
    }
}

if (!function_exists('vfd_show_total')) {
    function vfd_show_total($total, $cambio) {
        $a1 = escapeshellarg($total);
        $a2 = escapeshellarg($cambio);
        return vfd_exec("total $a1 $a2");
    }
}

// Muestra lineas en el VFD al cobrar/registrar pedido.
// Variante 2 lineas: ["Cant: X  $Y", "NombreProducto"]
// Variante 3 lineas: ["Gracias...", "Pedido #: X", "Total: $ X"]
if (!function_exists('vfd_write_lines')) {
    function vfd_write_lines($lines) {
        if (empty($lines)) return false;
        if (count($lines) == 2) {
            // Extraer cantidad y precio de linea 0: "Cant: 2   $5.50"
            preg_match('/(\d+)/', $lines[0], $mCant);
            preg_match('/\$?([\d\.]+)\s*$/', $lines[0], $mPrecio);
            $cant   = isset($mCant[1])   ? $mCant[1]   : '1';
            $precio = isset($mPrecio[1]) ? $mPrecio[1] : '';
            $nombre = isset($lines[1])   ? $lines[1]   : '';
            return vfd_show_product($cant, '$'.$precio, $nombre);
        }
        // 3+ lineas: pedido# y total
        $pedido = preg_replace('/[^0-9]/', '', isset($lines[1]) ? $lines[1] : '');
        $monto  = preg_replace('/[^0-9\.]/', '', isset($lines[2]) ? $lines[2] : '');
        return vfd_show_total('Ord#'.$pedido, $monto);
    }
}
