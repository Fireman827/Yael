<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear cuenta - Firehouse Burger</title>
<link rel="stylesheet" href="<?= base_url('vendors/bootstrap/css/bootstrap.min.css') ?>">
<style>
:root{--red:#C0392B;--red-d:#922B21;--black:#111;--card:#1e1e1e;--border:#2a2a2a;--text:#e8e8e8;--muted:#888}
body{background:var(--black);min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:'Segoe UI',sans-serif;padding:1rem}
.card-reg{max-width:520px;width:100%;background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.6);overflow:hidden}
.logo-wrap{background:linear-gradient(135deg,#0d0d0d,#1a0808);padding:1.25rem;text-align:center;border-bottom:2px solid var(--red);display:flex;align-items:center;gap:10px;justify-content:center}
.logo-wrap img{height:40px;width:40px;object-fit:contain}
.brand{font-size:16px;font-weight:700;color:var(--red)}
.card-body-pad{padding:1.5rem}
.form-label{font-size:11px;color:var(--muted);font-weight:700;letter-spacing:.5px;text-transform:uppercase;margin-bottom:4px;display:block}
.form-control{background:#252525;border:1px solid var(--border);color:var(--text);border-radius:8px;font-size:14px;padding:.55rem .8rem;width:100%}
.form-control:focus{border-color:var(--red);outline:none;box-shadow:0 0 0 3px rgba(192,57,43,.2)}
.form-control::placeholder{color:#555}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
.btn-brand{background:linear-gradient(135deg,var(--red),var(--red-d));color:#fff;border:none;width:100%;padding:12px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:all .2s}
.btn-brand:hover{filter:brightness(1.1)}
.alert-err{background:rgba(192,57,43,.12);border:1px solid rgba(192,57,43,.3);color:#ff8a7a;border-radius:8px;padding:10px;font-size:13px;margin-bottom:1rem}
.sec-title{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin:1rem 0 .75rem;padding-top:.75rem;border-top:1px solid var(--border)}
.hint{font-size:11px;color:var(--muted);margin-top:3px}
.optional-tag{font-size:10px;color:#555;font-weight:400;text-transform:none;letter-spacing:0;margin-left:4px}
hr{border-color:var(--border);margin:.75rem 0}
.link-red{color:var(--red);text-decoration:none;font-size:13px}
</style>
</head>
<body>
<div class="card-reg">
  <div class="logo-wrap">
    <img src="<?= base_url('vendors/core/img/logo.png') ?>" alt="FHB" onerror="this.style.display='none'">
    <span class="brand">Firehouse Burger — Crear cuenta</span>
  </div>
  <div class="card-body-pad">
    <?php if(!empty($error)): ?>
      <div class="alert-err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= site_url('registro') ?>">
      <?= form_hidden('csrf_token_id', $this->security->get_csrf_hash()) ?>

      <!-- DATOS PERSONALES -->
      <div class="row2 mb-3">
        <div>
          <label class="form-label">Nombre(s) *</label>
          <input type="text" name="nombreCliente" class="form-control"
                 placeholder="Juan" required autocomplete="given-name"
                 value="<?= htmlspecialchars($_POST['nombreCliente'] ?? '') ?>">
        </div>
        <div>
          <label class="form-label">Apellido(s) *</label>
          <input type="text" name="apellidoCliente" class="form-control"
                 placeholder="García" required autocomplete="family-name"
                 value="<?= htmlspecialchars($_POST['apellidoCliente'] ?? '') ?>">
        </div>
      </div>

      <div class="row2 mb-3">
        <div>
          <label class="form-label">Teléfono *</label>
          <input type="tel" name="telefonoCliente" class="form-control"
                 placeholder="7000-0000" required autocomplete="tel"
                 value="<?= htmlspecialchars($_POST['telefonoCliente'] ?? '') ?>">
        </div>
        <div>
          <label class="form-label">Correo electrónico *</label>
          <input type="email" name="emailCliente" class="form-control"
                 placeholder="tu@correo.com" required autocomplete="email"
                 value="<?= htmlspecialchars($_POST['emailCliente'] ?? '') ?>">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Dirección *</label>
        <input type="text" name="direccionCliente" class="form-control"
               placeholder="Col. Escalón, Calle Los Claveles #12" required
               autocomplete="street-address"
               value="<?= htmlspecialchars($_POST['direccionCliente'] ?? '') ?>">
      </div>

      <div class="row2 mb-3">
        <div>
          <label class="form-label">Departamento *</label>
          <select name="departamentoCliente" class="form-control" required>
            <option value="">Seleccione</option>
            <?php if(!empty($departamentos)): foreach($departamentos as $d): ?>
            <option value="<?= $d->codigo ?>"
              <?= (($_POST['departamentoCliente'] ?? '') == $d->codigo) ? 'selected' : '' ?>>
              <?= htmlspecialchars($d->valores) ?>
            </option>
            <?php endforeach; endif; ?>
          </select>
        </div>
        <div>
          <label class="form-label">Municipio *</label>
          <select name="municipioCliente" id="selectMunicipio" class="form-control" required>
            <option value="">Seleccione depto. primero</option>
          </select>
        </div>
      </div>

      <!-- CONTRASEÑA -->
      <div class="row2 mb-3">
        <div>
          <label class="form-label">Contraseña *</label>
          <input type="password" name="password" class="form-control"
                 placeholder="Mínimo 6 caracteres" required minlength="6">
        </div>
        <div>
          <label class="form-label">Confirmar contraseña *</label>
          <input type="password" name="password2" class="form-control"
                 placeholder="Repite tu contraseña" required>
        </div>
      </div>

      <!-- FACTURACIÓN — OPCIONAL -->
      <div class="sec-title">
        Datos de facturación
        <span class="optional-tag">(opcional — solo si necesitas factura electrónica o crédito fiscal)</span>
      </div>

      <div class="row2 mb-3">
        <div>
          <label class="form-label">DUI <span class="optional-tag">opcional</span></label>
          <input type="text" name="duiCliente" class="form-control"
                 placeholder="00000000-0" maxlength="10"
                 value="<?= htmlspecialchars($_POST['duiCliente'] ?? '') ?>">
        </div>
        <div>
          <label class="form-label">NIT empresa <span class="optional-tag">opcional</span></label>
          <input type="text" name="nitCliente" class="form-control"
                 placeholder="0000-000000-000-0" maxlength="17"
                 value="<?= htmlspecialchars($_POST['nitCliente'] ?? '') ?>">
        </div>
      </div>

      <div class="row2 mb-4">
        <div>
          <label class="form-label">NRC <span class="optional-tag">opcional</span></label>
          <input type="text" name="nrcCliente" class="form-control"
                 placeholder="000000-0"
                 value="<?= htmlspecialchars($_POST['nrcCliente'] ?? '') ?>">
        </div>
        <div>
          <label class="form-label">Giro / Actividad <span class="optional-tag">opcional</span></label>
          <input type="text" name="giroCliente" class="form-control"
                 placeholder="Comercio"
                 value="<?= htmlspecialchars($_POST['giroCliente'] ?? '') ?>">
        </div>
      </div>

      <p style="font-size:12px;color:var(--muted);text-align:center;line-height:1.5">
        Al crear tu cuenta aceptas nuestros
        <a href="<?= site_url('terminos') ?>" target="_blank" class="link-red">Términos y Política de Privacidad</a>
        para pedidos en línea.
      </p>

      <button type="submit" class="btn-brand">Crear cuenta y ordenar →</button>
    </form>

    <hr>
    <div class="text-center">
      <span style="font-size:13px;color:var(--muted)">¿Ya tienes cuenta? </span>
      <a href="<?= site_url('login') ?>" class="link-red">Iniciar sesión</a>
    </div>
  </div>
</div>

<script>
// Cargar municipios al seleccionar departamento
var municipiosPorDepto = <?= json_encode($municipiosPorDepto ?? []) ?>;
document.querySelector('[name=departamentoCliente]').addEventListener('change', function(){
  var sel = document.getElementById('selectMunicipio');
  sel.innerHTML = '<option value="">Seleccione municipio</option>';
  var munis = municipiosPorDepto[this.value] || [];
  munis.forEach(function(m){ sel.innerHTML += '<option value="'+m.codigo+'">'+m.valores+'</option>'; });
});
</script>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>

