<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pedidos en línea - Firehouse Burger</title>
<link rel="icon" type="image/png" href="<?= base_url('vendors/core/img/logo.png') ?>">
<link rel="stylesheet" href="<?= '/' ?>">
<style>
:root{--red:#C0392B;--red-d:#922B21;--black:#111;--card:#1e1e1e;--border:#2a2a2a;--text:#e8e8e8;--muted:#888}
body{background:var(--black);min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:'Segoe UI',sans-serif;padding:1rem}
.card-login{max-width:420px;width:100%;background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.6);overflow:hidden}
.logo-wrap{background:linear-gradient(135deg,#0d0d0d,#1a0808);padding:1.5rem;text-align:center;border-bottom:2px solid var(--red)}
.logo-wrap img{height:72px;width:72px;object-fit:contain;filter:drop-shadow(0 0 12px rgba(192,57,43,.6))}
.brand{font-size:18px;font-weight:700;color:var(--red);letter-spacing:1px;margin-top:6px}
.card-body-pad{padding:1.75rem}
.form-label{font-size:11px;color:var(--muted);font-weight:700;letter-spacing:.5px;text-transform:uppercase;margin-bottom:4px;display:block}
.form-control{background:#252525;border:1px solid var(--border);color:var(--text);border-radius:8px;font-size:14px;padding:.6rem .85rem;width:100%}
.form-control:focus{border-color:var(--red);outline:none;box-shadow:0 0 0 3px rgba(192,57,43,.2)}
.form-control::placeholder{color:#555}
.btn-brand{background:linear-gradient(135deg,var(--red),var(--red-d));color:#fff;border:none;width:100%;padding:12px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:all .2s;box-shadow:0 4px 16px rgba(192,57,43,.35)}
.btn-brand:hover{filter:brightness(1.1)}
.btn-crear{background:transparent;border:1.5px solid var(--border);color:var(--muted);width:100%;padding:11px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;display:block;text-align:center;margin-top:.5rem}
.btn-crear:hover{border-color:var(--red);color:var(--red);text-decoration:none}
.alert-err{background:rgba(192,57,43,.12);border:1px solid rgba(192,57,43,.3);color:#ff8a7a;border-radius:8px;padding:10px;font-size:13px;margin-bottom:1rem}
.alert-ok{background:rgba(39,174,96,.1);border:1px solid rgba(39,174,96,.3);color:#6dffb0;border-radius:8px;padding:10px;font-size:13px;margin-bottom:1rem}
hr{border-color:var(--border);margin:1rem 0}
.link-red{color:var(--red);text-decoration:none;font-size:13px}
.link-red:hover{text-decoration:underline}
</style>
</head>
<body>
<div class="card-login">
  <div class="logo-wrap">
    <img src="/vendors/core/img/logo.png" alt="FHB" onerror="this.style.display='none'">
    <div class="brand">Firehouse Burger</div>
  </div>
  <div class="card-body-pad">
    <?php if($this->session->flashdata('error')): ?>
      <div class="alert-err"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>
    <?php if($this->session->flashdata('info')): ?>
      <div class="alert-ok"><?= $this->session->flashdata('info') ?></div>
    <?php endif; ?>
    <?php if(!empty($error)): ?>
      <div class="alert-err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= site_url('login') ?>">
      <?= form_hidden('csrf_token_id', $this->security->get_csrf_hash()) ?>
      <input type="hidden" name="accion" value="login">
      <div class="mb-3">
        <label class="form-label">Correo electrónico</label>
        <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required autocomplete="email" autofocus>
      </div>
      <div class="mb-4">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" placeholder="••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn-brand">Iniciar sesión →</button>
    </form>

    <div class="text-center mt-2">
      <a href="<?= site_url('recuperar') ?>" class="link-red">¿Olvidaste tu contraseña?</a>
    </div>
    <hr>
    <a href="<?= site_url('registro') ?>" class="btn-crear">
      ¿No tienes cuenta? Crear cuenta nueva →
    </a>
  </div>
</div>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>


