<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>¡Pedido confirmado! - Firehouse Burger</title>
<link rel="stylesheet" href="<?= base_url('vendors/bootstrap/css/bootstrap.min.css') ?>">
<style>
:root{--red:#C0392B;--red-d:#922B21;--black:#111;--card:#1e1e1e;--border:#2a2a2a;--text:#e8e8e8;--muted:#888;--green:#27ae60}
body{background:var(--black);font-family:'Segoe UI',sans-serif;color:var(--text);padding-bottom:2rem}
.top-bar{background:linear-gradient(135deg,#0d0d0d,#1a0808);border-bottom:2px solid var(--red);padding:8px 16px;display:flex;align-items:center;gap:8px;box-shadow:0 2px 16px rgba(192,57,43,.3)}
.top-bar img{height:36px;width:36px;object-fit:contain}
.top-title{font-size:16px;font-weight:600}
.page{max-width:520px;margin:0 auto;padding:1.5rem 1rem}
.hero{text-align:center;padding:1.5rem 0 1rem}
.hero-icon{width:72px;height:72px;border-radius:50%;background:rgba(39,174,96,.15);border:2px solid var(--green);display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1rem}
.codigo-badge{display:inline-block;background:rgba(192,57,43,.15);border:1px solid var(--red);color:var(--red);border-radius:8px;padding:4px 16px;font-size:1.6rem;font-weight:700;letter-spacing:2px;margin-bottom:.5rem}
.status-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(192,57,43,.1);border:1px solid var(--red);border-radius:50px;padding:6px 16px;font-size:13px;font-weight:600;color:var(--red);margin-bottom:6px}
.pulse{width:10px;height:10px;background:var(--red);border-radius:50%;animation:pulse 1.5s ease-in-out infinite;flex-shrink:0}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(.6)}}
.section{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1rem;margin-bottom:1rem}
.sec-title{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:.75rem}
.order-line{display:flex;justify-content:space-between;font-size:13px;padding:5px 0;border-bottom:1px solid var(--border)}
.order-line:last-child{border-bottom:none}
.order-total{display:flex;justify-content:space-between;font-size:16px;font-weight:700;border-top:1.5px solid var(--border);padding-top:8px;margin-top:4px}
.order-total span:last-child{color:var(--red)}
.status-row{display:flex;align-items:center;gap:12px;padding:7px 0}
.status-dot{width:14px;height:14px;border-radius:50%;flex-shrink:0;transition:all .4s}
.status-dot.activo{background:var(--green);box-shadow:0 0 8px rgba(39,174,96,.5)}
.status-dot.pendiente{background:var(--border)}
.status-dot.actual{background:var(--red);box-shadow:0 0 10px rgba(192,57,43,.5);animation:pulse 1.5s infinite}
.status-label{font-size:14px;transition:color .4s}
.status-label.activo{color:var(--text)}
.status-label.pendiente{color:var(--muted)}
.status-badge-actual{background:rgba(192,57,43,.15);color:var(--red);font-size:11px;border-radius:10px;padding:2px 8px;margin-left:auto;font-weight:600}
.info-row{display:flex;justify-content:space-between;font-size:13px;padding:4px 0}
.info-row span:first-child{color:var(--muted)}
.btn-primary-fhb{background:linear-gradient(135deg,var(--red),var(--red-d));color:#fff;border:none;width:100%;padding:12px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;text-decoration:none;display:block;text-align:center}
.btn-primary-fhb:hover{filter:brightness(1.1);color:#fff}
.btn-secondary-fhb{background:transparent;color:var(--muted);border:1px solid var(--border);width:100%;padding:11px;border-radius:10px;font-size:14px;cursor:pointer;text-decoration:none;display:block;text-align:center;margin-top:.5rem}
.btn-secondary-fhb:hover{border-color:var(--red);color:var(--red)}
.last-update{font-size:11px;color:var(--muted);text-align:center;margin-top:4px}
</style>
</head>
<body>
<div class="top-bar">
  <img src="<?= base_url('vendors/core/img/logo.png') ?>" alt="" onerror="this.style.display='none'">
  <span class="top-title">Firehouse Burger</span>
</div>

<div class="page">
  <div class="hero">
    <div class="hero-icon">✅</div>
    <h5 style="font-weight:700;margin-bottom:4px">¡Pedido confirmado!</h5>
    <div class="codigo-badge"><?= htmlspecialchars($pedido['codigo']) ?></div><br>
    <div class="status-pill" id="statusPill">
      <div class="pulse" id="statusPulse"></div>
      <span id="statusLabel"><?php
        $labels = array(
          'Recibido'      => '📋 Recibido — en espera',
          'EnPreparacion' => '👨‍🍳 En preparación',
          'Listo'         => '✅ Listo para entrega',
          'EnCamino'      => '🛵 En camino',
          'Entregado'     => '🎉 Entregado',
          'Cancelado'     => '❌ Cancelado',
        );
        $estadoActual = $pedidoOnline ? $pedidoOnline->estadoOnline : 'Recibido';
        echo isset($labels[$estadoActual]) ? $labels[$estadoActual] : $estadoActual;
      ?></span>
    </div>
    <div class="last-update" id="lastUpdate">Actualizando cada 15 seg...</div>
  </div>

  <?php if ($detalles): ?>
  <div class="section">
    <div class="sec-title">🧾 Productos</div>
    <?php foreach ($detalles as $d): ?>
    <div class="order-line">
      <span><?= htmlspecialchars($d->nombreProducto) ?> <span style="color:var(--muted)">×<?= $d->cantidadPedidoDetalle ?></span></span>
      <span>$<?= number_format($d->precioPedidoDetalle * $d->cantidadPedidoDetalle, 2) ?></span>
    </div>
    <?php endforeach; ?>
    <div class="order-total"><span>Total</span><span>$<?= number_format($pedido['total'], 2) ?></span></div>
  </div>
  <?php endif; ?>

  <!-- Timeline de estados -->
  <div class="section">
    <div class="sec-title">📍 Estado del pedido</div>
    <?php
    $estados = array(
      'Recibido'      => '📋 Pedido recibido',
      'EnPreparacion' => '👨‍🍳 En preparación',
      'Listo'         => '✅ Listo para entrega',
      'EnCamino'      => '🛵 En camino',
      'Entregado'     => '📍 Entregado',
    );
    $orden     = array_keys($estados);
    $posActual = ($p = array_search($estadoActual, $orden)) !== false ? $p : 0;
    ?>
    <div id="statusTimeline">
    <?php foreach ($estados as $key => $lbl):
      $pos  = array_search($key, $orden);
      $curr = ($key === $estadoActual);
      $done = ($pos < $posActual);
    ?>
      <div class="status-row" id="row-<?= $key ?>">
        <div class="status-dot <?= $curr ? 'actual' : ($done ? 'activo' : 'pendiente') ?>"></div>
        <div class="status-label <?= ($done || $curr) ? 'activo' : 'pendiente' ?>"><?= $lbl ?></div>
        <?php if ($curr): ?><span class="status-badge-actual">Actual</span><?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
  </div>

  <?php if ($pedidoOnline): ?>
  <div class="section">
    <div class="sec-title">ℹ️ Información</div>
    <?php
    $tLbls = array('domicilio'=>'🛵 Domicilio','recoger'=>'🏃 Para recoger','local'=>'🪑 En local');
    $pLbls = array('efectivo'=>'💵 Efectivo','pos'=>'💳 Tarjeta POS','qr'=>'📱 Chivo/QR','transferencia'=>'🏦 Transferencia');
    ?>
    <div class="info-row"><span>Tipo</span><span><?= isset($tLbls[$pedidoOnline->tipoPedidoOnline]) ? $tLbls[$pedidoOnline->tipoPedidoOnline] : $pedidoOnline->tipoPedidoOnline ?></span></div>
    <div class="info-row"><span>Pago</span><span><?= isset($pLbls[$pedidoOnline->metodoPagoOnline]) ? $pLbls[$pedidoOnline->metodoPagoOnline] : $pedidoOnline->metodoPagoOnline ?></span></div>
    <?php if (!empty($pedidoOnline->notasOnline)): ?>
    <div class="info-row"><span>Nota</span><span><?= htmlspecialchars($pedidoOnline->notasOnline) ?></span></div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <a href="<?= site_url('pedidos/menu') ?>" class="btn-primary-fhb">🍔 Hacer otro pedido</a>
  <a href="<?= site_url('pedidos/cuenta') ?>" class="btn-secondary-fhb">Ver mis pedidos</a>
</div>

<input type="hidden" id="csrf_token_id" value="<?= $this->security->get_csrf_hash() ?>">
<input type="hidden" id="idOnline" value="<?= $pedido['idOnline'] ?>">
<input type="hidden" id="idPedidoPOS" value="<?= $pedido['idPedido'] ?>">

<!-- jQuery desde la ruta correcta del POS -->
<script src="<?= base_url('vendors/plugins/jquery/jquery.min.js') ?>"></script>
<script>
// Verificar que jQuery cargó correctamente
if (typeof $ === 'undefined') {
  // Intentar ruta alternativa
  document.write('<script src="<?= base_url('vendors/jquery/jquery.min.js') ?>"><\/script>');
}
</script>
<script>
var siteUrl    = '<?= site_url() ?>';
var estadoActual = '<?= $estadoActual ?>';
var idOnline   = document.getElementById('idOnline').value;
var idPedidoPOS= document.getElementById('idPedidoPOS').value;
var FINALES    = ['Entregado', 'Cancelado'];
var ORDEN      = ['Recibido','EnPreparacion','Listo','EnCamino','Entregado'];
var LABELS     = {
  Recibido:      '📋 Recibido — en espera',
  EnPreparacion: '👨‍🍳 En preparación',
  Listo:         '✅ Listo para entrega',
  EnCamino:      '🛵 En camino',
  Entregado:     '🎉 Entregado',
  Cancelado:     '❌ Cancelado',
};

function actualizarUI(estado) {
  document.getElementById('statusLabel').textContent = LABELS[estado] || estado;
  var posActual = ORDEN.indexOf(estado);
  ORDEN.forEach(function(key, pos) {
    var dot   = document.querySelector('#row-' + key + ' .status-dot');
    var lbl   = document.querySelector('#row-' + key + ' .status-label');
    var badge = document.querySelector('#row-' + key + ' .status-badge-actual');
    if (!dot) return;
    if (key === estado) {
      dot.className = 'status-dot actual';
      lbl.className = 'status-label activo';
      if (!badge) {
        var sp = document.createElement('span');
        sp.className = 'status-badge-actual';
        sp.textContent = 'Actual';
        document.getElementById('row-' + key).appendChild(sp);
      }
    } else if (pos < posActual) {
      dot.className = 'status-dot activo';
      lbl.className = 'status-label activo';
      if (badge) badge.remove();
    } else {
      dot.className = 'status-dot pendiente';
      lbl.className = 'status-label pendiente';
      if (badge) badge.remove();
    }
  });
}

function pollEstado() {
  if (typeof $ === 'undefined') return;
  $.ajax({
    type: 'POST',
    url: siteUrl + 'Online/estado_pedido',
    data: {
      idOnline:      idOnline,
      idPedidoPOS:   idPedidoPOS,
      csrf_token_id: $('#csrf_token_id').val()
    },
    dataType: 'json',
    success: function(r) {
      if (!r) return;
      var nuevoEstado = r.estado || estadoActual;
      if (nuevoEstado !== estadoActual) {
        estadoActual = nuevoEstado;
        actualizarUI(nuevoEstado);
        if (FINALES.indexOf(nuevoEstado) !== -1) {
          clearInterval(pollingInterval);
          document.getElementById('lastUpdate').textContent = '';
        }
      }
      document.getElementById('lastUpdate').textContent =
        'Última actualización: ' + new Date().toLocaleTimeString('es-SV');
    },
    error: function() {}
  });
}

var pollingInterval = null;
if (FINALES.indexOf(estadoActual) === -1) {
  pollingInterval = setInterval(pollEstado, 15000);
}
</script>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>