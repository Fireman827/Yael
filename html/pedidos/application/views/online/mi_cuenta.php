<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mis pedidos - Firehouse Burger</title>
<link rel="stylesheet" href="<?= '/' ?>">
<style>
  :root{--fhb-red:#C0392B;--fhb-red-dark:#922B21;--fhb-black:#111;--fhb-card:#1e1e1e;--fhb-border:#2a2a2a;--fhb-text:#e8e8e8;--fhb-muted:#888}
  body{background:var(--fhb-black);font-family:'Segoe UI',sans-serif;color:var(--fhb-text);padding-bottom:2rem}
  .top-bar{background:linear-gradient(135deg,#0d0d0d,#1a0808);border-bottom:2px solid var(--fhb-red);padding:8px 16px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 16px rgba(192,57,43,.3)}
  .brand-wrap{display:flex;align-items:center;gap:8px}
  .brand-wrap img{height:36px;width:36px;object-fit:contain}
  .brand-name{font-size:16px;font-weight:700;color:var(--fhb-red)}
  .user-info{font-size:13px;color:var(--fhb-muted)}
  .user-info a{color:var(--fhb-red);text-decoration:none}
  .page{max-width:560px;margin:0 auto;padding:1rem}
  .page-title{font-size:18px;font-weight:700;margin-bottom:1rem;color:var(--fhb-text)}
  .pedido-card{background:var(--fhb-card);border:1px solid var(--fhb-border);border-radius:12px;padding:1rem;margin-bottom:.75rem;transition:border-color .2s}
  .pedido-card:hover{border-color:var(--fhb-red)}
  .pedido-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
  .pedido-codigo{font-size:14px;font-weight:700;color:var(--fhb-red);letter-spacing:.5px}
  .pedido-meta{display:flex;justify-content:space-between;align-items:center;margin-top:4px}
  .pedido-fecha{font-size:12px;color:var(--fhb-muted)}
  .pedido-total{font-size:15px;font-weight:700;color:var(--fhb-text)}
  .pedido-tipo{font-size:12px;color:var(--fhb-muted);margin-top:2px}
  /* Badges de estado */
  .badge-estado{font-size:11px;padding:3px 10px;border-radius:10px;font-weight:600}
  .badge-Recibido{background:rgba(243,156,18,.15);color:#f39c12;border:1px solid rgba(243,156,18,.3)}
  .badge-EnPreparacion{background:rgba(52,152,219,.15);color:#3498db;border:1px solid rgba(52,152,219,.3)}
  .badge-Listo{background:rgba(39,174,96,.15);color:#27ae60;border:1px solid rgba(39,174,96,.3)}
  .badge-EnCamino{background:rgba(155,89,182,.15);color:#9b59b6;border:1px solid rgba(155,89,182,.3)}
  .badge-Entregado{background:rgba(39,174,96,.15);color:#27ae60;border:1px solid rgba(39,174,96,.3)}
  .badge-Cancelado{background:rgba(192,57,43,.15);color:var(--fhb-red);border:1px solid rgba(192,57,43,.3)}
  .btn-tracking{font-size:12px;color:#27ae60;text-decoration:none;display:inline-block;margin-top:4px}
  .btn-tracking:hover{text-decoration:underline}
  .empty-state{text-align:center;padding:4rem 1rem;color:var(--fhb-muted)}
  .btn-primary-fhb{background:linear-gradient(135deg,var(--fhb-red),var(--fhb-red-dark));color:#fff;border:none;width:100%;padding:12px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;display:block;text-align:center;margin-top:1rem}
  .btn-primary-fhb:hover{filter:brightness(1.1);color:#fff}
</style>
</head>
<body>
<div class="top-bar">
  <div class="brand-wrap">
    <img src="/vendors/core/img/logo.png" alt="" onerror="this.style.display='none'">
    <span class="brand-name">Firehouse Burger</span>
  </div>
  <div class="user-info">
    <?= htmlspecialchars(explode(' ', $sesion['nombre'])[0]) ?> ·
    <a href="<?= site_url('Online/logout') ?>">Salir</a>
  </div>
</div>

<div class="page">
  <div class="page-title">🛵 Mis pedidos</div>

  <?php if ($pedidos): ?>
    <?php
    $estadoLabels = ['Recibido'=>'Recibido','EnPreparacion'=>'En preparación','Listo'=>'Listo','EnCamino'=>'En camino','Entregado'=>'Entregado','Cancelado'=>'Cancelado'];
    $tipoLabels   = ['domicilio'=>'🛵 Domicilio','recoger'=>'🏃 Recoger','local'=>'🪑 Local'];
    foreach ($pedidos as $p):
    ?>
    <div class="pedido-card">
      <div class="pedido-header">
        <span class="pedido-codigo"><?= htmlspecialchars($p->codigoSeguimientoOnline) ?></span>
        <span class="badge-estado badge-<?= $p->estadoOnline ?>">
          <?= isset($estadoLabels[$p->estadoOnline]) ? $estadoLabels[$p->estadoOnline] : $p->estadoOnline ?>
        </span>
      </div>
      <div class="pedido-tipo"><?= isset($tipoLabels[$p->tipoPedidoOnline]) ? $tipoLabels[$p->tipoPedidoOnline] : '' ?></div>
      <div class="pedido-meta">
        <span class="pedido-fecha"><?= date('d/m/Y H:i', strtotime($p->fechaHoraOnline)) ?></span>
        <span class="pedido-total">$<?= number_format($p->totalPedido, 2) ?></span>
      </div>
      <?php if (!empty($p->trackingUrlOnline)): ?>
        <a href="<?= htmlspecialchars($p->trackingUrlOnline) ?>" target="_blank" class="btn-tracking">
          🗺️ Rastrear este pedido
        </a>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="empty-state">
      <div style="font-size:3rem">🍔</div>
      <p style="margin-top:.75rem">Aún no tienes pedidos.<br>¡Haz tu primera orden!</p>
    </div>
  <?php endif; ?>

  <a href="<?= site_url('Online/menu') ?>" class="btn-primary-fhb">🔥 Hacer un pedido</a>
</div>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>


