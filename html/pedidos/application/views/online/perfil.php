<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi perfil - Firehouse Burger</title>
<link rel="stylesheet" href="<?= base_url('vendors/bootstrap/css/bootstrap.min.css') ?>">
<style>
:root{--red:#C0392B;--red-d:#922B21;--black:#111;--dark:#1a1a1a;--card:#1e1e1e;--border:#2a2a2a;--text:#e8e8e8;--muted:#888;--green:#27ae60}
body{background:var(--black);font-family:'Segoe UI',sans-serif;color:var(--text);padding-bottom:2rem}
.top-bar{background:linear-gradient(135deg,#0d0d0d,#1a0808);border-bottom:2px solid var(--red);padding:8px 16px;display:flex;align-items:center;gap:10px;position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(192,57,43,.3)}
.top-bar img{height:34px;width:34px;object-fit:contain}
.btn-back{color:var(--muted);font-size:13px;text-decoration:none}.btn-back:hover{color:var(--red)}
.top-title{font-size:15px;font-weight:600}
.page{max-width:520px;margin:0 auto;padding:1rem}
.section{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;margin-bottom:1rem}
.sec-title{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:1rem}
.avatar-wrap{text-align:center;margin-bottom:1.5rem}
.avatar-big{width:72px;height:72px;border-radius:50%;background:var(--red);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:700;margin:0 auto 8px}
.form-label{font-size:11px;color:var(--muted);font-weight:700;letter-spacing:.4px;text-transform:uppercase;margin-bottom:4px}
.form-control{background:#252525;border:1px solid var(--border);color:var(--text);border-radius:8px;font-size:14px;padding:.55rem .8rem;width:100%}
.form-control:focus{border-color:var(--red);outline:none;box-shadow:0 0 0 3px rgba(192,57,43,.2)}
.form-control:disabled{opacity:.5;cursor:not-allowed}
.btn-save{background:linear-gradient(135deg,var(--red),var(--red-d));color:#fff;border:none;width:100%;padding:12px;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:all .2s}
.btn-save:hover{filter:brightness(1.1)}
.btn-save:disabled{opacity:.6;cursor:not-allowed}
.btn-outline-red{background:transparent;border:1.5px solid var(--red);color:var(--red);border-radius:8px;padding:9px 16px;font-size:14px;font-weight:600;cursor:pointer;width:100%;transition:all .2s}
.btn-outline-red:hover{background:var(--red);color:#fff}
.alert-ok{background:rgba(39,174,96,.1);border:1px solid rgba(39,174,96,.3);color:#6dffb0;border-radius:8px;padding:10px;font-size:13px;margin-bottom:1rem;display:none}
.alert-err{background:rgba(192,57,43,.1);border:1px solid rgba(192,57,43,.3);color:#ff8a7a;border-radius:8px;padding:10px;font-size:13px;margin-bottom:1rem;display:none}
hr{border-color:var(--border)}
</style>
</head>
<body>

<div class="top-bar">
  <a href="<?= site_url('menu') ?>" class="btn-back">← Menú</a>
  <img src="<?= base_url('vendors/core/img/logo.png') ?>" alt="" onerror="this.style.display='none'">
  <span class="top-title">Mi Perfil</span>
</div>

<div class="page">

  <div class="avatar-wrap">
    <div class="avatar-big"><?= strtoupper(substr($sesion['nombre'], 0, 2)) ?></div>
    <div style="font-size:14px;font-weight:600"><?= htmlspecialchars($sesion['nombre']) ?></div>
    <div style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($sesion['email']) ?></div>
  </div>

  <div id="alertOk"  class="alert-ok"></div>
  <div id="alertErr" class="alert-err"></div>

  <!-- Datos personales -->
  <div class="section">
    <div class="sec-title">👤 Datos personales</div>
    <div class="mb-3">
      <label class="form-label">Nombre completo</label>
      <input type="text" id="pNombre" class="form-control"
             value="<?= htmlspecialchars($cliente->nombreCliente ?? $sesion['nombre']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Correo electrónico</label>
      <input type="email" id="pEmail" class="form-control"
             value="<?= htmlspecialchars($sesion['email']) ?>" disabled>
      <small style="color:var(--muted);font-size:11px">El correo no se puede cambiar</small>
    </div>
    <div class="mb-3">
      <label class="form-label">Teléfono (WhatsApp)</label>
      <input type="tel" id="pTelefono" class="form-control"
             value="<?= htmlspecialchars($cliente->telefonoCliente ?? $sesion['telefono']) ?>">
    </div>
    <div class="mb-4">
      <label class="form-label">Dirección de entrega</label>
      <input type="text" id="pDireccion" class="form-control"
             value="<?= htmlspecialchars($cliente->direccionCliente ?? $sesion['direccion']) ?>">
    </div>
    <button class="btn-save" id="btnGuardar" onclick="guardarPerfil()">
      <i>💾</i> Guardar cambios
    </button>
  </div>

  <!-- Cambiar contraseña -->
  <div class="section">
    <div class="sec-title">🔐 Cambiar contraseña</div>
    <div class="mb-3">
      <label class="form-label">Contraseña actual</label>
      <input type="password" id="pPassActual" class="form-control" placeholder="Tu contraseña actual">
    </div>
    <div class="mb-3">
      <label class="form-label">Nueva contraseña</label>
      <input type="password" id="pPassNueva" class="form-control" placeholder="Mínimo 6 caracteres">
    </div>
    <div class="mb-4">
      <label class="form-label">Confirmar nueva contraseña</label>
      <input type="password" id="pPassConfirm" class="form-control" placeholder="Repite la nueva contraseña">
    </div>
    <button class="btn-outline-red" onclick="cambiarPassword()">
      🔑 Cambiar contraseña
    </button>
  </div>

  <!-- Mis pedidos -->
  <div class="section">
    <div class="sec-title">📦 Mis pedidos recientes</div>
    <?php if ($pedidos): foreach (array_slice($pedidos, 0, 5) as $p):
      $badgeMap = array('Recibido'=>'#f39c12','EnPreparacion'=>'#3498db','Listo'=>'#27ae60','EnCamino'=>'#9b59b6','Entregado'=>'#27ae60','Cancelado'=>'#e74c3c');
      $bc = isset($badgeMap[$p->estadoOnline]) ? $badgeMap[$p->estadoOnline] : '#888';
    ?>
    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border)">
      <div>
        <div style="font-size:13px;font-weight:600;color:var(--red)"><?= $p->codigoSeguimientoOnline ?></div>
        <div style="font-size:11px;color:var(--muted)"><?= date('d/m/Y H:i', strtotime($p->fechaHoraOnline)) ?></div>
      </div>
      <div style="text-align:right">
        <div style="font-size:13px;font-weight:700">$<?= number_format($p->totalPedido, 2) ?></div>
        <span style="font-size:11px;background:<?= $bc ?>20;color:<?= $bc ?>;border:1px solid <?= $bc ?>40;border-radius:10px;padding:1px 8px"><?= $p->estadoOnline ?></span>
      </div>
    </div>
    <?php endforeach; else: ?>
    <p style="color:var(--muted);font-size:13px;text-align:center;padding:1rem 0">Aún no tienes pedidos.</p>
    <?php endif; ?>
    <?php if ($pedidos && count($pedidos) > 5): ?>
    <div class="text-center mt-2">
      <a href="<?= site_url('cuenta') ?>" style="font-size:13px;color:var(--red)">Ver todos mis pedidos →</a>
    </div>
    <?php endif; ?>
  </div>

</div>

<input type="hidden" id="csrf_token_id" value="<?= $this->security->get_csrf_hash() ?>">

<script src="<?= base_url('vendors/plugins/jquery/jquery.min.js') ?>"></script>
<script>
var siteUrl = '<?= site_url() ?>';

function mostrarMsg(tipo, msg) {
  var id = tipo === 'ok' ? 'alertOk' : 'alertErr';
  var el = document.getElementById(id);
  el.textContent = msg;
  el.style.display = 'block';
  setTimeout(function(){ el.style.display = 'none'; }, 4000);
}

function guardarPerfil() {
  var btn = document.getElementById('btnGuardar');
  btn.disabled = true; btn.textContent = '⏳ Guardando...';

  $.ajax({
    type: 'POST', url: siteUrl + 'pedidos/guardarPerfil',
    data: {
      nombre:        $('#pNombre').val(),
      telefono:      $('#pTelefono').val(),
      direccion:     $('#pDireccion').val(),
      csrf_token_id: $('#csrf_token_id').val(),
    },
    dataType: 'json',
    success: function(r) {
      if (r.codigo == 200) {
        mostrarMsg('ok', '✅ ' + r.mensaje);
      } else {
        mostrarMsg('err', '❌ ' + r.mensaje);
      }
      btn.disabled = false; btn.textContent = '💾 Guardar cambios';
    },
    error: function() {
      mostrarMsg('err', 'Error de conexión.');
      btn.disabled = false; btn.textContent = '💾 Guardar cambios';
    }
  });
}

function cambiarPassword() {
  var actual   = $('#pPassActual').val();
  var nueva    = $('#pPassNueva').val();
  var confirmar= $('#pPassConfirm').val();

  if (!actual || !nueva) { mostrarMsg('err', 'Completa todos los campos.'); return; }
  if (nueva.length < 6)  { mostrarMsg('err', 'La contraseña debe tener al menos 6 caracteres.'); return; }
  if (nueva !== confirmar){ mostrarMsg('err', 'Las contraseñas no coinciden.'); return; }

  $.ajax({
    type: 'POST', url: siteUrl + 'pedidos/cambiarPassword',
    data: {
      password_actual:  actual,
      password_nueva:   nueva,
      csrf_token_id:    $('#csrf_token_id').val(),
    },
    dataType: 'json',
    success: function(r) {
      if (r.codigo == 200) {
        mostrarMsg('ok', '✅ ' + r.mensaje);
        $('#pPassActual,#pPassNueva,#pPassConfirm').val('');
      } else {
        mostrarMsg('err', '❌ ' + r.mensaje);
      }
    }
  });
}
</script>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>

