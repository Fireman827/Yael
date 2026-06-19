<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Whatsapp_helper.php — FHB POS
 * Ruta: application/helpers/Whatsapp_helper.php
 *
 * Envía mensajes WhatsApp vía Fonnte.com
 *
 * CONFIGURACIÓN (ya en tu BD):
 *   WA_API_KEY_REST → token del dispositivo Fonnte
 *   WA_NUMERO_REST  → número del restaurante ej: 50376212908
 *   WA_MODO_PRUEBA  → TRUE = solo muestra en pantalla | FALSE = envía WhatsApp real
 *
 * Para migrar a otro proveedor en el futuro:
 *   Solo reemplaza WaSendCurl() — el resto del helper no cambia.
 */

// ------------------------------------------------------------------
// WaEnviarCliente — confirmación de pedido al cliente
// ------------------------------------------------------------------
function WaEnviarCliente($cliente, $pedido, $apiKeyCliente = '') {
    $telefono = WaLimpiarTelefono($cliente->telefonoCliente);
    if (empty($telefono)) return false;

    $cfgToken = TraerUnDato('configuraciones',
        "parametroConfiguracion = 'WA_API_KEY_REST' AND estadoConfiguracion = 'Activo'");
    if (!$cfgToken || empty($cfgToken->valorConfiguracion)) return false;

    $tipo = '';
    switch ($pedido->tipoPedidoOnline) {
        case 'domicilio': $tipo = 'a domicilio 🛵'; break;
        case 'recoger':   $tipo = 'para recoger 🏃'; break;
        case 'local':     $tipo = 'en local 🪑';    break;
    }

    $mensaje  = "✅ *¡Pedido recibido!*\n";
    $mensaje .= "Código: *{$pedido->codigoSeguimientoOnline}*\n";
    $mensaje .= "Tipo: {$tipo}\n";
    $mensaje .= "Total: *\${$pedido->totalPedido}*\n";
    $mensaje .= "Estado: ⏳ En preparación\n\n";
    $mensaje .= "Gracias por tu pedido, te avisamos cuando esté listo. 🍔🔥";

    return WaSendCurl($telefono, $mensaje, $cfgToken->valorConfiguracion);
}

// ------------------------------------------------------------------
// WaEnviarRestaurante — notificación de nueva orden
// ------------------------------------------------------------------
function WaEnviarRestaurante($cliente, $pedido, $items = array()) {
    $cfgApiKey = TraerUnDato('configuraciones',
        "parametroConfiguracion = 'WA_API_KEY_REST' AND estadoConfiguracion = 'Activo'");
    $cfgNumero = TraerUnDato('configuraciones',
        "parametroConfiguracion = 'WA_NUMERO_REST' AND estadoConfiguracion = 'Activo'");

    if (!$cfgApiKey || !$cfgNumero) return false;

    $apiKey   = $cfgApiKey->valorConfiguracion;
    $telefono = WaLimpiarTelefono($cfgNumero->valorConfiguracion);

    $tipo = '';
    switch ($pedido->tipoPedidoOnline) {
        case 'domicilio': $tipo = '🛵 DOMICILIO'; break;
        case 'recoger':   $tipo = '🏃 PARA RECOGER'; break;
        case 'local':     $tipo = '🪑 EN LOCAL'; break;
    }

    $listado = '';
    foreach ($items as $item) {
        $listado .= "• {$item->cantidadPedidoDetalle}x {$item->nombreProducto}\n";
    }

    $mensaje  = "🔔 *NUEVA ORDEN ONLINE*\n";
    $mensaje .= "Código: *{$pedido->codigoSeguimientoOnline}*\n";
    $mensaje .= "Cliente: *{$cliente->nombreCliente}*\n";
    $mensaje .= "Tel: {$cliente->telefonoCliente}\n";
    $mensaje .= "Tipo: {$tipo}\n";
    if ($pedido->tipoPedidoOnline === 'domicilio' && !empty($cliente->direccionCliente)) {
        $mensaje .= "Dirección: {$cliente->direccionCliente}\n";
    }
    if (!empty($pedido->latitudOnline) && !empty($pedido->longitudOnline)) {
        $mensaje .= "📍 Ubicación: https://www.google.com/maps?q={$pedido->latitudOnline},{$pedido->longitudOnline}\n";
    }
    $mensaje .= "Pago: " . strtoupper($pedido->metodoPagoOnline) . "\n";
    if (!empty($listado)) {
        $mensaje .= "\nProductos:\n{$listado}";
    }
    $mensaje .= "Total: *\${$pedido->totalPedido}*";
    if (!empty($pedido->notasOnline)) {
        $mensaje .= "\n📝 Nota: {$pedido->notasOnline}";
    }

    return WaSendCurl($telefono, $mensaje, $apiKey);
}

// ------------------------------------------------------------------
// WaEnviarEstado — cambio de estado al cliente
// ------------------------------------------------------------------
function WaEnviarEstado($cliente, $pedido, $nuevoEstado, $apiKeyCliente = '') {
    $telefono = WaLimpiarTelefono($cliente->telefonoCliente);
    if (empty($telefono)) return false;

    $cfgToken = TraerUnDato('configuraciones',
        "parametroConfiguracion = 'WA_API_KEY_REST' AND estadoConfiguracion = 'Activo'");
    if (!$cfgToken || empty($cfgToken->valorConfiguracion)) return false;

    $iconos = array(
        'EnPreparacion' => '👨‍🍳 Tu pedido está en preparación...',
        'Listo'         => '✅ ¡Tu pedido está listo!',
        'EnCamino'      => '🛵 ¡Tu pedido va en camino!',
        'Entregado'     => '🎉 ¡Pedido entregado! Buen provecho.',
        'Cancelado'     => '❌ Tu pedido fue cancelado. Contáctanos para más info.',
    );

    $texto   = isset($iconos[$nuevoEstado]) ? $iconos[$nuevoEstado] : "Estado: {$nuevoEstado}";
    $mensaje  = "📦 *Pedido {$pedido->codigoSeguimientoOnline}*\n";
    $mensaje .= $texto;

    return WaSendCurl($telefono, $mensaje, $cfgToken->valorConfiguracion);
}

// ------------------------------------------------------------------
// WaSendCurl — envío real via Fonnte
// Para migrar a otro proveedor: reemplaza solo esta función.
// ------------------------------------------------------------------
function WaSendCurl($telefono, $mensaje, $token) {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL            => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query(array(
            'target'      => $telefono,
            'message'     => $mensaje,
            'countryCode' => '503',
        )),
        CURLOPT_HTTPHEADER     => array('Authorization: ' . $token),
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'FHB-POS/1.0',
    ));

    $respuesta = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        log_message('error', 'Fonnte HTTP ' . $httpCode . ' | ' . $respuesta);
        return false;
    }

    $json = json_decode($respuesta, true);
    if (!$json || empty($json['status'])) {
        $razon = isset($json['reason']) ? $json['reason'] : 'Respuesta desconocida';
        log_message('error', 'Fonnte status false | ' . $razon);
        return false;
    }

    return true;
}

// ------------------------------------------------------------------
// WaLimpiarTelefono — normaliza el número
// ------------------------------------------------------------------
function WaLimpiarTelefono($telefono) {
    $limpio = preg_replace('/[^0-9]/', '', $telefono);
    if (strlen($limpio) === 8) {
        $limpio = '503' . $limpio; // El Salvador sin código de país
    }
    return $limpio;
}

/* End of file Whatsapp_helper.php */
