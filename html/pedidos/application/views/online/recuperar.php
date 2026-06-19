<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar contraseña - Firehouse Burger</title>
<link rel="stylesheet" href="<?= base_url('vendors/plugins/bootstrap/css/bootstrap.min.css') ?>">
<style>
  body { background:#111; min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Segoe UI',sans-serif; padding:1rem; }
  .card-login { max-width:400px; width:100%; background:#1a1a1a; border:1px solid #333; border-radius:16px; padding:2rem; }
  .logo-wrap { text-align:center; margin-bottom:1.5rem; }
  .logo-wrap img { width:80px; height:80px; object-fit:contain; border-radius:50%; border:3px solid #c0392b; }
  .brand { font-size:18px; font-weight:700; color:#e74c3c; margin-top:8px; }
  .form-label { font-size:12px; color:#aaa; margin-bottom:4px; }
  .form-control { background:#252525 !important; border:1px solid #444; color:#fff !important; border-radius:8px; font-size:14px; padding:9px 12px; width:100%; }
  .form-control:focus { border-color:#e74c3c; outline:none; }
  .form-control::placeholder { color:#555; }
  .btn-brand { background:#e74c3c; color:#fff; border:none; width:100%; padding:11px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; margin-top:.5rem; }
  .btn-brand:hover { background:#c0392b; }
  .alert-error { background:#2d1515; border:1px solid #5a1a1a; color:#e74c3c; border-radius:8px; padding:10px 12px; font-size:13px; margin-bottom:1rem; }
  .alert-info { background:#1a2a1a; border:1px solid #2a5a2a; color:#5cb85c; border-radius:8px; padding:10px 12px; font-size:13px; margin-bottom:1rem; }
  .back-link { display:block; text-align:center; margin-top:1rem; font-size:13px; color:#666; text-decoration:none; }
  .back-link:hover { color:#e74c3c; }
  .otp-row { display:flex; gap:10px; justify-content:center; margin:1rem 0; }
  .otp-row input { width:52px; height:58px; text-align:center; font-size:22px; font-weight:600; border:1.5px solid #444; border-radius:8px; background:#252525; color:#fff; }
  .otp-row input:focus { border-color:#e74c3c; outline:none; }
</style>
</head>
<body>
<div class="card-login">
  <div class="logo-wrap">
    <img src="<?= base_url('vendors/core/img/logo2.png') ?>" alt="Firehouse Burger">
    <div class="brand">FIREHOUSE BURGER</div>
  </div>

  <?php if (!empty($error)): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if (!empty($info)): ?><div class="alert-info"><?= htmlspecialchars($info) ?></div><?php endif; ?>

  <?php if ($paso == 1): ?>
    <!-- Paso 1: pedir email -->
    <p style="color:#aaa;font-size:13px;margin-bottom:1rem">Ingresa tu correo y te enviamos un código de verificación por WhatsApp.</p>
    <form method="POST" action="<?= site_url('recuperar') ?>">
      <?= form_hidden('csrf_token_id', $this->security->get_csrf_hash()) ?>
      <div class="mb-3">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" class="form-control" placeholder="juan@correo.com" required autocomplete="email">
      </div>
      <button type="submit" class="btn-brand">Enviar código →</button>
    </form>

  <?php else: ?>
    <!-- Paso 2: ingresar OTP y nueva contraseña -->
    <?php if (!empty($otp_debug)): ?>
      <div class="alert-info" style="text-align:center">
        <strong style="letter-spacing:6px;font-size:1.4rem">⚠️ MODO PRUEBA — OTP: <?= htmlspecialchars($otp_debug) ?></strong><br>
        <small>Cambiar WA_MODO_PRUEBA a FALSE en producción</small>
      </div>
    <?php endif; ?>
    <p style="color:#aaa;font-size:13px;margin-bottom:1rem">Ingresa el código que recibiste por WhatsApp y tu nueva contraseña.</p>
    <form method="POST" action="<?= site_url('nueva_password') ?>">
      <?= form_hidden('csrf_token_id', $this->security->get_csrf_hash()) ?>
      <label class="form-label" style="display:block;text-align:center">Código de verificación</label>
      <div class="otp-row">
        <input type="tel" maxlength="1" class="otp-digit" inputmode="numeric">
        <input type="tel" maxlength="1" class="otp-digit" inputmode="numeric">
        <input type="tel" maxlength="1" class="otp-digit" inputmode="numeric">
        <input type="tel" maxlength="1" class="otp-digit" inputmode="numeric">
      </div>
      <input type="hidden" name="otp" id="hiddenOtp">
      <div class="mb-3">
        <label class="form-label">Nueva contraseña</label>
        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
      </div>
      <div class="mb-3">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="password2" class="form-control" placeholder="Repite tu contraseña" required minlength="6">
      </div>
      <button type="submit" class="btn-brand">Cambiar contraseña →</button>
    </form>
  <?php endif; ?>

  <a href="<?= site_url('pedidos') ?>" class="back-link">← Volver al inicio</a>
</div>
<script>
document.querySelectorAll('.otp-digit').forEach(function(inp, idx, arr){
  inp.addEventListener('input', function(){
    this.value = this.value.replace(/[^0-9]/g,'');
    if(this.value && idx < arr.length-1) arr[idx+1].focus();
    document.getElementById('hiddenOtp').value = Array.from(arr).map(i=>i.value).join('');
  });
  inp.addEventListener('keydown', function(e){
    if(e.key==='Backspace' && !this.value && idx>0) arr[idx-1].focus();
  });
});
</script>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>
