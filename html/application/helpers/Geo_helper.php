<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------
// PuntoEnPoligono — algoritmo ray-casting estándar.
// $lat/$lng: punto a evaluar.
// $poligono: array de puntos array('lat'=>..,'lng'=>..)
// ------------------------------------------------------------------
if (!function_exists('PuntoEnPoligono')) {
    function PuntoEnPoligono($lat, $lng, $poligono) {
        $dentro = false;
        $n = count($poligono);
        if ($n < 3) return false;
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $latI = (float)$poligono[$i]['lat'];
            $lngI = (float)$poligono[$i]['lng'];
            $latJ = (float)$poligono[$j]['lat'];
            $lngJ = (float)$poligono[$j]['lng'];

            $interseca = (($lngI > $lng) != ($lngJ > $lng))
                && ($lat < ($latJ - $latI) * ($lng - $lngI) / ($lngJ - $lngI) + $latI);
            if ($interseca) $dentro = !$dentro;
        }
        return $dentro;
    }
}

// ------------------------------------------------------------------
// EncontrarZonaDelivery — busca entre las zonas activas cuál contiene
// el punto (lat,lng). Devuelve el objeto de la zona o null si no
// está dentro de ninguna.
// ------------------------------------------------------------------
if (!function_exists('EncontrarZonaDelivery')) {
    function EncontrarZonaDelivery($lat, $lng) {
        if (!is_numeric($lat) || !is_numeric($lng)) return null;

        $zonas = TraerDatos('zonadelivery', "estadoZonaDelivery = 'Activo'");
        if (!$zonas) return null;

        foreach ($zonas as $zona) {
            $poligono = json_decode($zona->poligonoZonaDelivery, true);
            if (!is_array($poligono) || count($poligono) < 3) continue;
            if (PuntoEnPoligono((float)$lat, (float)$lng, $poligono)) {
                return $zona;
            }
        }
        return null;
    }
}
