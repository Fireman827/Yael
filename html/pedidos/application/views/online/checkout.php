<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirmar pedido - Firehouse Burger</title>
<link rel="icon" type="image/png" href="<?= base_url('vendors/core/img/logo.png') ?>">
<link rel="stylesheet" href="<?= '/' ?>">
<style>
:root{--red:#C0392B;--red-d:#922B21;--black:#111;--card:#1e1e1e;--border:#2a2a2a;--text:#e8e8e8;--muted:#888}
body{background:var(--black);font-family:'Segoe UI',sans-serif;color:var(--text);padding-bottom:2rem}
.top-bar{background:linear-gradient(135deg,#0d0d0d,#1a0808);border-bottom:2px solid var(--red);padding:8px 16px;display:flex;align-items:center;gap:10px;position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(192,57,43,.3)}
.top-bar img{height:34px;width:34px;object-fit:contain}
.btn-back{color:var(--muted);font-size:13px;text-decoration:none}.btn-back:hover{color:var(--red)}
.top-title{font-size:15px;font-weight:600}
.page{max-width:560px;margin:0 auto;padding:1rem}
.section{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem;margin-bottom:1rem}
.sec-title{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:.75rem}
.order-line{display:flex;justify-content:space-between;font-size:13px;padding:5px 0;border-bottom:1px solid var(--border)}
.order-line:last-child{border-bottom:none}
.order-total{display:flex;justify-content:space-between;font-size:16px;font-weight:700;border-top:1.5px solid var(--border);padding-top:8px;margin-top:4px}
.order-total span:last-child{color:var(--red)}
.info-row{display:flex;justify-content:space-between;font-size:13px;padding:3px 0}
.info-row span:first-child{color:var(--muted)}
.opt-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.opt-grid.cols2{grid-template-columns:repeat(2,1fr)}
.opt-label{display:flex;flex-direction:column;align-items:center;gap:4px;background:#252525;border:2px solid var(--border);border-radius:10px;padding:10px 6px;cursor:pointer;transition:all .2s;font-size:12px;font-weight:600;color:var(--muted);text-align:center}
.opt-label input{display:none}
.opt-label .ico{font-size:1.4rem}
.opt-label:hover{border-color:var(--red);color:var(--text)}
.opt-label.sel{border-color:var(--red);background:rgba(192,57,43,.12);color:var(--red)}
.form-control{background:#252525;border:1px solid var(--border);color:var(--text);border-radius:8px;font-size:13px;width:100%;padding:.5rem .75rem}
.form-control:focus{border-color:var(--red);outline:none;box-shadow:0 0 0 3px rgba(192,57,43,.2)}
textarea.form-control{resize:none}
.pago-info{margin-top:10px;padding:12px;background:#252525;border-radius:8px;border-left:3px solid var(--red);display:none}
.pago-info.visible{display:block}
.pago-row{display:flex;justify-content:space-between;font-size:13px;padding:3px 0}
.pago-row span:first-child{color:var(--muted)}
.pago-row span:last-child{color:var(--text);font-weight:600}
.pago-qr{text-align:center;margin-top:10px}
.pago-qr img{max-width:160px;border-radius:8px;border:1px solid var(--border)}
.btn-confirm{background:linear-gradient(135deg,var(--red),var(--red-d));color:#fff;border:none;width:100%;padding:14px;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;transition:all .2s;box-shadow:0 4px 16px rgba(192,57,43,.35)}
.btn-confirm:hover{filter:brightness(1.1)}
.btn-confirm:disabled{opacity:.6;cursor:not-allowed}
.horario-banner{background:rgba(192,57,43,.15);border:1px solid var(--red);border-radius:10px;padding:12px;margin-bottom:1rem;font-size:13px;line-height:1.6;color:var(--text)}
.horario-banner b{color:var(--red)}
.btn-geo{display:flex;align-items:center;justify-content:center;gap:6px;background:#252525;border:1px dashed var(--border);color:var(--muted);border-radius:8px;padding:10px;width:100%;font-size:13px;font-weight:600;cursor:pointer;margin-top:10px;transition:all .2s}
.btn-geo:hover{border-color:var(--red);color:var(--text)}
.btn-geo.ok{border-style:solid;border-color:#2e7d32;color:#66bb6a;background:rgba(46,125,50,.12)}
.btn-geo.err{border-style:solid;border-color:var(--red);color:var(--red);background:rgba(192,57,43,.1)}
.terminos-box{display:flex;align-items:flex-start;gap:8px;font-size:12.5px;color:var(--muted);line-height:1.5;margin-bottom:.75rem;padding:0 2px}
.terminos-box input{margin-top:3px;flex-shrink:0}
.terminos-box a{color:var(--red);text-decoration:underline}
/* Overlay éxito */
.success-overlay{position:fixed;inset:0;background:rgba(0,0,0,.95);z-index:9999;display:none;align-items:center;justify-content:center;flex-direction:column;text-align:center;padding:2rem}
.success-overlay.show{display:flex;animation:fadeIn .4s ease}
@keyframes fadeIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.success-fire{font-size:5rem;animation:bounce .6s ease .1s both}
@keyframes bounce{0%{transform:scale(0)}60%{transform:scale(1.2)}100%{transform:scale(1)}}
.success-titulo{font-size:1.1rem;font-weight:700;color:var(--text);margin:.75rem 0 .25rem}
.success-codigo{font-size:2.8rem;font-weight:700;color:var(--red);letter-spacing:4px;margin:.25rem 0 .75rem;text-shadow:0 0 20px rgba(192,57,43,.5)}
.success-msg{font-size:.95rem;color:var(--muted);max-width:300px;line-height:1.7;margin:0 auto 1.5rem}
.btn-ver-orden{background:var(--red);color:#fff;border:none;padding:13px 36px;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 16px rgba(192,57,43,.4)}
.btn-ver-orden:hover{background:var(--red-d)}
.success-footer{font-size:.8rem;color:#444;margin-top:1.25rem}
.progress-bar-wrap{width:200px;height:3px;background:#2a2a2a;border-radius:3px;margin-top:1rem;overflow:hidden}
.progress-bar-fill{height:100%;background:var(--red);border-radius:3px;animation:progreso 4s linear forwards}
@keyframes progreso{from{width:0}to{width:100%}}
</style>
</head>
<body>

<div class="top-bar">
  <a href="<?= site_url('menu') ?>" class="btn-back">← Menú</a>
  <img src="/vendors/core/img/logo.png" alt="" onerror="this.style.display='none'">
  <span class="top-title">Confirmar pedido</span>
</div>

<?php
function _cfgPago($param) {
    $row = TraerUnDato('configuraciones',
        "parametroConfiguracion = '{$param}' AND estadoConfiguracion = 'Activo'");
    return ($row && !empty($row->valorConfiguracion)) ? $row->valorConfiguracion : '';
}
$bancoNombre  = _cfgPago('PAGO_BANCO_NOMBRE');
$bancoCuenta  = _cfgPago('PAGO_BANCO_CUENTA');
$bancoTitular = _cfgPago('PAGO_BANCO_TITULAR');
$bancoTipo    = _cfgPago('PAGO_BANCO_TIPO');
$bancoQR      = _cfgPago('PAGO_BANCO_QR');
$chivoNum     = _cfgPago('PAGO_CHIVO_NUMERO');
$chivoQR      = _cfgPago('PAGO_CHIVO_QR');
?>

<?php $horario = HorarioOnlineEstado(); ?>

<div class="page">
  <?php if (!$horario['abierto']): ?>
  <div class="horario-banner">
    🕒 <b>Estamos cerrados en este momento.</b><br>
    Horario de pedidos en línea: <?= htmlspecialchars($horario['horarioTexto']) ?><br>
    Puedes armar tu pedido y enviarlo — lo recibiremos apenas abramos.
  </div>
  <?php endif; ?>
  <div class="section">
    <div class="sec-title">🧾 Resumen del pedido</div>
    <?php foreach ($carrito as $item): ?>
    <div class="order-line">
      <span><?= htmlspecialchars($item['nombre']) ?> <span style="color:var(--muted)">×<?= $item['cantidad'] ?></span></span>
      <span>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></span>
    </div>
    <?php endforeach; ?>
    <div class="order-total"><span>Total</span><span>$<?= $total ?></span></div>
  </div>

  <div class="section">
    <div class="sec-title">👤 Datos de entrega</div>
    <div class="info-row"><span>Nombre</span><span><?= htmlspecialchars($sesion['nombre']) ?></span></div>
    <div class="info-row"><span>Teléfono</span><span><?= htmlspecialchars($sesion['telefono']) ?></span></div>
    <div class="info-row"><span>Dirección</span><span><?= htmlspecialchars($sesion['direccion']) ?></span></div>
    <div style="text-align:right;margin-top:4px">
      <a href="<?= site_url('perfil') ?>" style="font-size:12px;color:var(--red)">✏️ Editar mis datos</a>
    </div>
  </div>

  <div class="section">
    <div class="sec-title">📦 Tipo de pedido</div>
    <div class="opt-grid">
      <label class="opt-label sel" onclick="selOpt(this)">
        <input type="radio" name="tipoPedido" value="domicilio" checked>
        <span class="ico">🛵</span>Domicilio
      </label>
      <label class="opt-label" onclick="selOpt(this)">
        <input type="radio" name="tipoPedido" value="recoger">
        <span class="ico">🏃</span>Recoger
      </label>
      <!-- "En local" deshabilitado temporalmente: requiere local físico para reservar mesas / comer ahí. Reactivar cuando se tenga local.
      <label class="opt-label" onclick="selOpt(this)">
        <input type="radio" name="tipoPedido" value="local">
        <span class="ico">🪑</span>En local
      </label>
      -->
    </div>
    <div id="geoWrap">
      <?php if (!empty($googleMapsApiKey)): ?>
      <input type="text" id="mapaBuscar" class="form-control mb-2" placeholder="🔍 Buscar mi dirección...">
      <div id="mapaUbicacion" style="width:100%;height:220px;border-radius:8px;overflow:hidden;margin-bottom:8px"></div>
      <div style="font-size:12px;color:var(--muted);margin-bottom:8px">
        Arrastra el pin 📍 para ajustar tu ubicación exacta de entrega.
      </div>
      <div id="zonaWarning" style="display:none;background:rgba(231,76,60,.12);border:1px solid #e74c3c;color:#ff6b5e;border-radius:8px;padding:10px;font-size:12.5px;margin-bottom:8px;line-height:1.5">
        ⚠️ Lo sentimos, tu ubicación está fuera de nuestras zonas de cobertura de delivery. Ajusta el pin a una dirección dentro de nuestra zona, o elige "Recoger en restaurante".
      </div>
      <?php else: ?>
      <button type="button" class="btn-geo" id="btnGeo" onclick="compartirUbicacion()">
        📍 Compartir mi ubicación (ayuda al repartidor a llegar más rápido)
      </button>
      <?php endif; ?>
      <input type="hidden" id="geoLat" value="">
      <input type="hidden" id="geoLng" value="">
    </div>
    <div id="ipCountryWarning" style="display:none;background:rgba(243,156,18,.1);border:1px dashed #f39c12;color:#f3b15c;border-radius:8px;padding:10px;font-size:12px;margin-top:8px;line-height:1.5">
      ⚠️ Detectamos tu conexión fuera de El Salvador<span id="ipCountryName"></span>. Si vas a recibir tu pedido aquí
      (por ejemplo, usas VPN o estás de viaje), no hay problema — solo asegúrate de que tu ubicación/dirección de
      entrega sea la correcta. Esto es solo informativo, no afecta tu pedido.
    </div>
  </div>

  <div class="section">
    <div class="sec-title">💳 Método de pago</div>
    <div class="opt-grid cols2">
      <label class="opt-label sel" onclick="selPago(this,'efectivo')">
        <input type="radio" name="metodoPago" value="efectivo" checked>
        <span class="ico">💵</span>Efectivo
      </label>
      <label class="opt-label" onclick="selPago(this,'pos')">
        <input type="radio" name="metodoPago" value="pos">
        <span class="ico">💳</span>Tarjeta POS
      </label>
      <?php if ($bancoNombre || $bancoCuenta): ?>
      <label class="opt-label" onclick="selPago(this,'transferencia')">
        <input type="radio" name="metodoPago" value="transferencia">
        <span class="ico">🏦</span>Transferencia
      </label>
      <?php endif; ?>
      <?php if ($chivoNum || $chivoQR): ?>
      <label class="opt-label" onclick="selPago(this,'qr')">
        <input type="radio" name="metodoPago" value="qr">
        <span class="ico">⚡</span>Chivo / QR
      </label>
      <?php endif; ?>
    </div>
    <div class="pago-info visible" id="info-efectivo">
      <div style="font-size:13px;color:var(--muted)">💵 Paga en efectivo al recibir. El repartidor también puede llevar terminal POS.</div>
    </div>
    <div class="pago-info" id="info-pos">
      <div style="font-size:13px;color:var(--muted)">💳 La terminal POS llegará con el repartidor. Acepta Visa y Mastercard.</div>
    </div>
    <div class="pago-info" id="info-transferencia">
      <?php if ($bancoNombre):  ?><div class="pago-row"><span>Banco</span><span><?= htmlspecialchars($bancoNombre) ?></span></div><?php endif; ?>
      <?php if ($bancoTipo):    ?><div class="pago-row"><span>Tipo</span><span><?= htmlspecialchars($bancoTipo) ?></span></div><?php endif; ?>
      <?php if ($bancoCuenta):  ?><div class="pago-row"><span>Cuenta</span><span><?= htmlspecialchars($bancoCuenta) ?></span></div><?php endif; ?>
      <?php if ($bancoTitular): ?><div class="pago-row"><span>Titular</span><span><?= htmlspecialchars($bancoTitular) ?></span></div><?php endif; ?>
      <?php if ($bancoQR): ?>
      <div class="pago-qr"><p style="font-size:12px;color:var(--muted);margin-bottom:6px">Escanea para transferir:</p>
        <img src="<?= base_url($bancoQR) ?>" alt="QR Transferencia"></div>
      <?php endif; ?>
      <p style="font-size:12px;color:var(--muted);margin-top:8px;margin-bottom:0">📸 Envía el comprobante por WhatsApp al confirmar.</p>
    </div>
    <div class="pago-info" id="info-qr">
      <?php if ($chivoNum): ?><div class="pago-row"><span>Chivo / Lightning</span><span><?= htmlspecialchars($chivoNum) ?></span></div><?php endif; ?>
      <?php if ($chivoQR): ?>
      <div class="pago-qr"><p style="font-size:12px;color:var(--muted);margin-bottom:6px">Escanea con Chivo Wallet:</p>
        <img src="<?= base_url($chivoQR) ?>" alt="QR Chivo"></div>
      <?php endif; ?>
      <p style="font-size:12px;color:var(--muted);margin-top:8px;margin-bottom:0">⚡ Paga con Chivo Wallet u otra billetera Bitcoin Lightning.</p>
    </div>
  </div>

  <div class="section">
    <div class="sec-title">📝 Notas (opcional)</div>
    <textarea id="notasTxt" class="form-control" rows="2" maxlength="500"
              placeholder="Sin cebolla, extra salsa, alergia a..."></textarea>
  </div>

  <label class="terminos-box">
    <input type="checkbox" id="chkTerminos">
    <span>He leído y acepto los <a href="<?= site_url('terminos') ?>" target="_blank">Términos y Condiciones y la Política de Privacidad</a> de los pedidos en línea (uso de mis datos, tiempos de entrega y proceso de pago).</span>
  </label>

  <button class="btn-confirm" id="btnConfirmar" onclick="confirmarPedido()" <?= $horario['abierto'] ? '' : 'disabled' ?>>
    <?= $horario['abierto'] ? 'Confirmar pedido — $' . $total : '🕒 Cerrado por ahora' ?>
  </button>
  <div class="text-center mt-3">
    <a href="<?= site_url('menu') ?>" style="font-size:13px;color:var(--muted)">← Volver al menú</a>
  </div>
</div>

<!-- Overlay de éxito -->
<div class="success-overlay" id="successOverlay">
  <div class="success-fire">🔥</div>
  <div class="success-titulo">¡Tu pedido fue recibido!</div>
  <div class="success-codigo" id="successCodigo">FHB-00000</div>
  <div class="success-msg">
    Estamos preparando tu orden con mucho fuego 🍔<br>
    Te notificaremos por WhatsApp cuando esté lista.
  </div>
  <button class="btn-ver-orden" onclick="irAConfirmacion()">
    Ver detalle de mi orden →
  </button>
  <div class="progress-bar-wrap">
    <div class="progress-bar-fill" id="progressFill"></div>
  </div>
  <div class="success-footer">Firehouse Burger · Pedidos en línea</div>
</div>

<input type="hidden" id="csrf_token_id" value="<?= $this->security->get_csrf_hash() ?>">
<input type="hidden" id="carritoBackup" value="<?= htmlspecialchars(json_encode(array_values($carrito))) ?>">

<script src="/vendors/plugins/jquery/jquery.min.js"></script>
<script>
var siteUrl = '<?= site_url() ?>';
var zonaCubierta = null; // null = aún no se valida, true/false = resultado de /pedidos/validarZona

function selOpt(lbl) {
  document.querySelectorAll('[name=tipoPedido]').forEach(function(r){
    r.closest('.opt-label').classList.remove('sel');
  });
  lbl.classList.add('sel');
  var esDomicilio = lbl.querySelector('input').value === 'domicilio';
  document.getElementById('geoWrap').style.display = esDomicilio ? 'block' : 'none';
  if (!esDomicilio) {
    document.getElementById('zonaWarning').style.display = 'none';
    document.getElementById('btnConfirmar').disabled = false;
  } else if (zonaCubierta === false) {
    document.getElementById('zonaWarning').style.display = 'block';
    document.getElementById('btnConfirmar').disabled = true;
  }
}

function compartirUbicacion() {
  var btn = document.getElementById('btnGeo');
  if (!navigator.geolocation) {
    btn.classList.add('err');
    btn.textContent = '⚠️ Tu navegador no soporta geolocalización. Usaremos tu dirección registrada.';
    return;
  }
  btn.disabled = true;
  btn.textContent = '⏳ Obteniendo tu ubicación...';
  navigator.geolocation.getCurrentPosition(function(pos) {
    document.getElementById('geoLat').value = pos.coords.latitude;
    document.getElementById('geoLng').value = pos.coords.longitude;
    btn.disabled = false;
    btn.classList.remove('err');
    btn.classList.add('ok');
    btn.textContent = '✅ Ubicación compartida — el repartidor la recibirá por WhatsApp';
  }, function(err) {
    document.getElementById('geoLat').value = '';
    document.getElementById('geoLng').value = '';
    btn.disabled = false;
    btn.classList.add('err');
    btn.textContent = '⚠️ No pudimos obtener tu ubicación. Usaremos tu dirección registrada.';
  }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
}

function selPago(lbl, metodo) {
  document.querySelectorAll('[name=metodoPago]').forEach(function(r){
    r.closest('.opt-label').classList.remove('sel');
  });
  lbl.classList.add('sel');
  document.querySelectorAll('.pago-info').forEach(function(el){ el.classList.remove('visible'); });
  var infoEl = document.getElementById('info-' + metodo);
  if (infoEl) infoEl.classList.add('visible');
}

function confirmarPedido() {
  var tipo = document.querySelector('[name=tipoPedido]:checked');
  var pago = document.querySelector('[name=metodoPago]:checked');
  if (!tipo || !pago) { alert('Selecciona tipo y método de pago.'); return; }

  if (!document.getElementById('chkTerminos').checked) {
    alert('Debes aceptar los Términos y Condiciones y la Política de Privacidad para continuar.');
    return;
  }

  if (tipo.value === 'domicilio' && zonaCubierta === false) {
    alert('Lo sentimos, tu ubicación está fuera de nuestras zonas de cobertura de delivery. Ajusta el pin a una dirección dentro de nuestra zona, o elige "Recoger en restaurante".');
    return;
  }

  var btn = document.getElementById('btnConfirmar');
  btn.disabled = true;
  btn.textContent = '⏳ Procesando tu orden...';

  $.ajax({
    type: 'POST',
    url: siteUrl + 'pedidos/confirmar',
    data: {
      tipoPedido:    tipo.value,
      metodoPago:    pago.value,
      notas:         $('#notasTxt').val(),
      latitud:       $('#geoLat').val(),
      longitud:      $('#geoLng').val(),
      carrito:       $('#carritoBackup').val(),
      csrf_token_id: $('#csrf_token_id').val(),
    },
    dataType: 'json',
    success: function(r) {
      // El servidor devuelve {"codigo":"FHB-XXXXX","idOnline":N}
      // Si "codigo" empieza con "FHB-" es éxito, no un código HTTP
      if (r.idOnline && r.codigo && String(r.codigo).indexOf('FHB-') === 0) {
        mostrarExito(r.codigo);
      } else if (r.codigo == 200) {
        // Formato alternativo con http código separado
        mostrarExito(r.codigo_seguimiento || r.codigo_orden || '');
      } else {
        btn.disabled = false;
        btn.textContent = 'Confirmar pedido — $<?= $total ?>';
        alert('Error: ' + (r.mensaje || 'Intenta de nuevo.'));
      }
    },
    error: function(xhr) {
      // Intentar igual parsear — a veces jQuery marca error por content-type
      try {
        var r = JSON.parse(xhr.responseText);
        if (r.idOnline && String(r.codigo).indexOf('FHB-') === 0) {
          mostrarExito(r.codigo);
          return;
        }
      } catch(e) {}
      btn.disabled = false;
      btn.textContent = 'Confirmar pedido — $<?= $total ?>';
      alert('Verifica tu pedido en "Mis pedidos" antes de volver a intentarlo.');
    }
  });
}

function mostrarExito(codigo) {
  document.getElementById('successCodigo').textContent = codigo || '¡Listo!';
  document.getElementById('successOverlay').classList.add('show');
  // Auto-redirigir en 4 segundos
  setTimeout(function(){ irAConfirmacion(); }, 4000);
}

function irAConfirmacion() {
  window.location.href = siteUrl + 'pedidos/confirmacion';
}

<?php if (!empty($googleMapsApiKey)): ?>
// ===========================================================
// Mapa interactivo de ubicación (Google Maps)
// ===========================================================
var mapaUbicacion, marcadorUbicacion;

function initMapaUbicacion() {
  var centroDefault = { lat: 13.6929, lng: -89.2182 }; // San Salvador

  mapaUbicacion = new google.maps.Map(document.getElementById('mapaUbicacion'), {
    center: centroDefault,
    zoom: 13,
    streetViewControl: false,
    mapTypeControl: false,
    fullscreenControl: false
  });

  marcadorUbicacion = new google.maps.Marker({
    position: centroDefault,
    map: mapaUbicacion,
    draggable: true
  });

  google.maps.event.addListener(marcadorUbicacion, 'dragend', function() {
    actualizarUbicacion(marcadorUbicacion.getPosition());
  });

  mapaUbicacion.addListener('click', function(e) {
    marcadorUbicacion.setPosition(e.latLng);
    actualizarUbicacion(e.latLng);
  });

  var autocomplete = new google.maps.places.Autocomplete(document.getElementById('mapaBuscar'), {
    componentRestrictions: { country: 'sv' }
  });
  autocomplete.bindTo('bounds', mapaUbicacion);
  autocomplete.addListener('place_changed', function() {
    var place = autocomplete.getPlace();
    if (!place.geometry || !place.geometry.location) return;
    mapaUbicacion.setCenter(place.geometry.location);
    mapaUbicacion.setZoom(16);
    marcadorUbicacion.setPosition(place.geometry.location);
    actualizarUbicacion(place.geometry.location);
  });

  // Intentar geolocalizar al usuario al cargar
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
      var ubicacion = { lat: pos.coords.latitude, lng: pos.coords.longitude };
      mapaUbicacion.setCenter(ubicacion);
      mapaUbicacion.setZoom(16);
      marcadorUbicacion.setPosition(ubicacion);
      actualizarUbicacion(ubicacion);
    }, function() { /* sin permiso, se queda en el centro por defecto */ }, { enableHighAccuracy: true, timeout: 8000 });
  }
}

function actualizarUbicacion(latLng) {
  var lat = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
  var lng = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;
  document.getElementById('geoLat').value = lat;
  document.getElementById('geoLng').value = lng;
  validarZonaCobertura(lat, lng);
}

function validarZonaCobertura(lat, lng) {
  var warn = document.getElementById('zonaWarning');
  var btn = document.getElementById('btnConfirmar');
  $.post(siteUrl + 'pedidos/validarZona', { latitud: lat, longitud: lng, csrf_token_id: $('#csrf_token_id').val() }, function(r) {
    if (r.codigo === 200 && r.cubierto === false) {
      zonaCubierta = false;
      warn.style.display = 'block';
      btn.disabled = true;
    } else if (r.codigo === 200) {
      zonaCubierta = true;
      warn.style.display = 'none';
      btn.disabled = false;
    } else {
      zonaCubierta = null;
      warn.style.display = 'none';
    }
  }, 'json');
}
<?php endif; ?>

// Aviso informativo (no bloqueante) si la IP parece estar fuera de El Salvador
(function(){
  try {
    if (sessionStorage.getItem('fhb_ip_check_done')) return;
  } catch (e) {}
  fetch('https://ipapi.co/json/').then(function(r){ return r.json(); }).then(function(d){
    try { sessionStorage.setItem('fhb_ip_check_done', '1'); } catch (e) {}
    if (d && d.country_code && d.country_code !== 'SV') {
      var nameEl = document.getElementById('ipCountryName');
      if (nameEl && d.country_name) nameEl.textContent = ' (' + d.country_name + ')';
      var el = document.getElementById('ipCountryWarning');
      if (el) el.style.display = 'block';
    }
  }).catch(function(){});
})();
</script>
<?php if (!empty($googleMapsApiKey)): ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($googleMapsApiKey) ?>&libraries=places&callback=initMapaUbicacion" async defer></script>
<?php endif; ?>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>

