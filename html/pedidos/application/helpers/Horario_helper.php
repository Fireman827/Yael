<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------
// HorarioOnlineEstado — calcula si los pedidos en línea están
// abiertos según el día/hora actual y la configuración guardada en
// `configuraciones` (HORARIO_DOM_JUE / HORARIO_VIE_SAB, formato "HH:MM-HH:MM").
//
// Domingo a jueves -> HORARIO_DOM_JUE
// Viernes y sábado -> HORARIO_VIE_SAB
//
// Devuelve: array(
//   'abierto'      => bool,
//   'rango'        => 'HH:MM-HH:MM' del día actual,
//   'horarioTexto' => texto legible con ambos horarios para mostrar al cliente,
//   'mensaje'      => mensaje a mostrar/usar cuando está cerrado,
// )
// ------------------------------------------------------------------
if (!function_exists('HorarioOnlineEstado')) {
    function HorarioOnlineEstado($momento = null) {
        $domJue = TraerUnDato('configuraciones',
            "parametroConfiguracion = 'HORARIO_DOM_JUE' AND estadoConfiguracion = 'Activo'");
        $vieSab = TraerUnDato('configuraciones',
            "parametroConfiguracion = 'HORARIO_VIE_SAB' AND estadoConfiguracion = 'Activo'");

        $rangoDomJue = ($domJue && !empty($domJue->valorConfiguracion)) ? trim($domJue->valorConfiguracion) : '11:00-21:00';
        $rangoVieSab = ($vieSab && !empty($vieSab->valorConfiguracion)) ? trim($vieSab->valorConfiguracion) : '11:00-22:00';

        $ahora = $momento ?: time();
        $diaSemana = (int)date('w', $ahora); // 0=domingo ... 6=sábado
        $esViernesSabado = ($diaSemana === 5 || $diaSemana === 6);
        $rangoHoy = $esViernesSabado ? $rangoVieSab : $rangoDomJue;

        $partes = explode('-', $rangoHoy);
        $horaInicio = isset($partes[0]) ? trim($partes[0]) : '11:00';
        $horaFin    = isset($partes[1]) ? trim($partes[1]) : '21:00';

        $inicioTs = strtotime(date('Y-m-d', $ahora) . ' ' . $horaInicio);
        $finTs    = strtotime(date('Y-m-d', $ahora) . ' ' . $horaFin);

        $abierto = ($ahora >= $inicioTs && $ahora <= $finTs);

        $horarioTexto = "Domingo a jueves: {$rangoDomJue}  ·  Viernes y sábado: {$rangoVieSab}";

        return array(
            'abierto'      => $abierto,
            'rango'        => $horaInicio . ' - ' . $horaFin,
            'horarioTexto' => $horarioTexto,
            'mensaje'      => "🕒 Estamos cerrados en este momento. Nuestro horario de pedidos en línea es:\n{$horarioTexto}\nPuedes armar tu pedido y enviarlo cuando abramos.",
        );
    }
}
