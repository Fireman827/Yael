<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verificar teléfono - Firehouse Burger</title>
<link rel="stylesheet" href="<?= '/' ?>">
<style>
  :root { --fhb-red:#C0392B; --fhb-red-dark:#922B21; --fhb-black:#111; --fhb-card:#1e1e1e; --fhb-border:#2a2a2a; --fhb-text:#e8e8e8; --fhb-muted:#888; --fhb-gold:#f39c12; }
  body { background:var(--fhb-black); min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Segoe UI',sans-serif; padding:1rem; }
  .card-login { max-width:380px; width:100%; background:var(--fhb-card); border:1px solid var(--fhb-border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.6); padding:2rem; }
  .logo-wrap { text-align:center; margin-bottom:1.2rem; }
  .logo-wrap img { height:64px; width:64px; object-fit:contain; filter:drop-shadow(0 0 12px rgba(192,57,43,.5)); }
  .brand { font-size:20px; font-weight:700; color:var(--fhb-red); margin-top:4px; }
  .step-dots { display:flex; gap:6px; justify-content:center; margin-bottom:1.5rem; }
  .dot { width:8px; height:8px; border-radius:50%; background:var(--fhb-border); }
  .dot.done { background:rgba(192,57,43,.3); border:2px solid var(--fhb-red); }
  .dot.active { background:var(--fhb-red); width:22px; border-radius:4px; }
  .otp-row { display:flex; gap:10px; justify-content:center; margin:1.2rem 0; }
  .otp-digit {
    width:54px; height:62px; text-align:center;
    font-size:24px; font-weight:700;
    background:#252525; border:1.5px solid var(--fhb-border);
    color:var(--fhb-text); border-radius:10px;
  }
  .otp-digit:focus { border-color:var(--fhb-red); box-shadow:0 0 0 3px rgba(192,57,43,.2); outline:none; background:#2a2a2a; }
  .btn-brand { background:linear-gradient(135deg,var(--fhb-red) 0%,var(--fhb-red-dark) 100%); color:#fff; border:none; width:100%; padding:11px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; transition:all .2s; }
  .btn-brand:hover { filter:brightness(1.1); transform:translateY(-1px); }
  .alert-danger { background:rgba(192,57,43,.15); border:1px solid rgba(192,57,43,.3); color:#ff8a7a; border-radius:8px; font-size:13px; }
  .debug-box { background:rgba(243,156,18,.1); border:1px dashed var(--fhb-gold); border-radius:10px; padding:12px; margin-bottom:1rem; text-align:center; }
  .debug-code { font-size:2.2rem; font-weight:700; color:var(--fhb-gold); letter-spacing:8px; display:block; }
  hr { border-color:var(--fhb-border); }
  a { color:var(--fhb-red); text-decoration:none; }
  a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="card-login">
  <div class="logo-wrap">
    <img src="<?= '/' ?>" alt="Firehouse Burger"
         onerror="this.style.display='none'">
    <div class="brand">Verificación</div>
  </div>

  <div class="step-dots">
    <div class="dot done"></div>
    <div class="dot active"></div>
    <div class="dot"></div>
    <div class="dot"></div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger py-2 mb-3"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($otp_debug)): ?>
    <div class="debug-box">
      <small style="color:var(--fhb-gold);font-weight:700">⚠️ MODO PRUEBA — OTP:</small>
      <span class="debug-code"><?= $otp_debug ?></span>
      <small style="color:var(--fhb-muted)">Cambiar WA_MODO_PRUEBA a FALSE en producción</small>
    </div>
  <?php endif; ?>

  <p class="text-center mb-1" style="font-size:13px;color:var(--fhb-muted)">
    Código enviado a <strong style="color:var(--fhb-text)"><?= htmlspecialchars($telefono) ?></strong>
  </p>

  <form method="POST" action="<?= site_url('Online/verificar') ?>" id="frmOtp">
    <?= form_hidden('csrf_token_id', $this->security->get_csrf_hash()) ?>
    <input type="hidden" name="otp" id="hiddenOtp">

    <div class="otp-row">
      <input type="tel" maxlength="1" id="o0" name="d0" class="otp-digit" inputmode="numeric">
      <input type="tel" maxlength="1" id="o1" name="d1" class="otp-digit" inputmode="numeric">
      <input type="tel" maxlength="1" id="o2" name="d2" class="otp-digit" inputmode="numeric">
      <input type="tel" maxlength="1" id="o3" name="d3" class="otp-digit" inputmode="numeric">
    </div>

    <button type="submit" class="btn-brand">Verificar código</button>
  </form>

  <hr class="my-3">

  <div class="text-center">
    <a href="<?= site_url('Online/reenviar_otp') ?>" style="font-size:13px">
      ¿No lo recibiste? Reenviar código
    </a><br>
    <a href="<?= site_url('Online/login') ?>" style="font-size:12px;color:var(--fhb-muted)">
      ← Volver y editar datos
    </a>
  </div>
</div>

<script>
var digits = document.querySelectorAll('.otp-digit');

digits.forEach(function(inp, idx) {
  inp.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').slice(-1);
    if (this.value && idx < digits.length - 1) digits[idx + 1].focus();
    updateHidden();
  });
  inp.addEventListener('keydown', function(e) {
    if (e.key === 'Backspace' && !this.value && idx > 0) digits[idx - 1].focus();
  });
  // Pegar código completo
  inp.addEventListener('paste', function(e) {
    e.preventDefault();
    var paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
    paste.split('').slice(0, 4).forEach(function(ch, i) {
      if (digits[i]) { digits[i].value = ch; }
    });
    updateHidden();
    if (digits[3]) digits[3].focus();
  });
});

function updateHidden() {
  document.getElementById('hiddenOtp').value =
    Array.from(digits).map(function(i){ return i.value; }).join('');
}

document.getElementById('frmOtp').addEventListener('submit', function() { updateHidden(); });
digits[0].focus();
</script>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>


