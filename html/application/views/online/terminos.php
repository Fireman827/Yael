<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Términos y Privacidad - Firehouse Burger</title>
<link rel="stylesheet" href="<?= base_url('vendors/bootstrap/css/bootstrap.min.css') ?>">
<style>
:root{--red:#C0392B;--red-d:#922B21;--black:#111;--card:#1e1e1e;--border:#2a2a2a;--text:#e8e8e8;--muted:#888}
body{background:var(--black);font-family:'Segoe UI',sans-serif;color:var(--text);padding-bottom:2rem}
.top-bar{background:linear-gradient(135deg,#0d0d0d,#1a0808);border-bottom:2px solid var(--red);padding:8px 16px;display:flex;align-items:center;gap:10px;position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(192,57,43,.3)}
.top-bar img{height:34px;width:34px;object-fit:contain}
.btn-back{color:var(--muted);font-size:13px;text-decoration:none}.btn-back:hover{color:var(--red)}
.top-title{font-size:15px;font-weight:600}
.page{max-width:640px;margin:0 auto;padding:1rem}
.section{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;margin-bottom:1rem}
.sec-title{font-size:14px;font-weight:700;color:var(--red);margin-bottom:.5rem}
.section p, .section li{font-size:13.5px;line-height:1.7;color:var(--text);margin-bottom:.5rem}
.section ul{padding-left:1.2rem;margin-bottom:.5rem}
.section .muted{color:var(--muted);font-size:12px}
.intro{font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:1rem}
</style>
</head>
<body>

<div class="top-bar">
  <a href="javascript:history.back()" class="btn-back">← Volver</a>
  <img src="<?= base_url('vendors/core/img/logo.png') ?>" alt="" onerror="this.style.display='none'">
  <span class="top-title">Términos y Privacidad — Pedidos en línea</span>
</div>

<div class="page">

  <p class="intro">
    Esta página explica, en lenguaje sencillo, qué implica ordenar en línea con Firehouse Burger:
    cómo usamos tus datos, cómo funciona la entrega y el pago, y qué puedes esperar de nosotros.
    Última actualización: <?= date('d/m/Y') ?>.
  </p>

  <div class="section">
    <div class="sec-title">📋 Tu pedido</div>
    <ul>
      <li>Al confirmar tu pedido, este se envía directamente a nuestra cocina y se registra con un código de seguimiento (ej. <i>FHB-00000</i>) que podrás consultar en "Mis pedidos".</li>
      <li>Los precios y disponibilidad de los productos pueden cambiar sin previo aviso; el total que ves al confirmar es el que se cobrará.</li>
      <li>Solo recibimos pedidos dentro de nuestro horario de atención: <b><?= htmlspecialchars($horario['horarioTexto']) ?></b>. Fuera de este horario no se podrá confirmar el pedido.</li>
      <li>Una vez enviado, tu pedido pasa a preparación de inmediato — si necesitas cambiarlo o cancelarlo, contáctanos cuanto antes por WhatsApp.</li>
    </ul>
  </div>

  <div class="section">
    <div class="sec-title">🛵 Entrega y recolección</div>
    <ul>
      <li><b>Domicilio:</b> usamos la dirección registrada en tu cuenta. Si compartes tu ubicación con el botón "📍 Compartir mi ubicación", esa coordenada exacta se envía a nuestro repartidor por WhatsApp para ayudarle a llegar más rápido — es completamente opcional, y si no la compartes, usaremos tu dirección escrita.</li>
      <li><b>Recoger:</b> tu pedido estará listo para retirar en el horario indicado en tu seguimiento; te avisaremos por WhatsApp cuando esté listo.</li>
      <li>Los tiempos de entrega son estimados y pueden variar según tráfico, clima o demanda.</li>
    </ul>
  </div>

  <div class="section">
    <div class="sec-title">💳 Pago</div>
    <ul>
      <li>Puedes pagar en efectivo, con tarjeta (POS al momento de la entrega), por transferencia bancaria o con billetera Bitcoin Lightning (Chivo), según las opciones disponibles.</li>
      <li>Si pagas por transferencia o billetera digital, deberás enviarnos el comprobante por WhatsApp para confirmar tu pago.</li>
    </ul>
  </div>

  <div class="section">
    <div class="sec-title">🔒 Tus datos y privacidad</div>
    <ul>
      <li>Para procesar tu pedido usamos: tu nombre, teléfono, dirección, correo (si lo proporcionas) y, si lo autorizas, tu ubicación GPS.</li>
      <li>Estos datos se usan <b>únicamente</b> para gestionar tu pedido, contactarte sobre su estado (por WhatsApp y/o correo) y mejorar nuestro servicio. No vendemos ni compartimos tus datos con terceros ajenos a la operación del restaurante.</li>
      <li>La ubicación GPS solo se solicita al ordenar a domicilio, es opcional y se comparte exclusivamente con el repartidor asignado a tu pedido.</li>
      <li>Te notificamos el estado de tu pedido (recibido, en preparación, listo, en camino, entregado) por WhatsApp usando el número que registraste.</li>
      <li>Puedes solicitar la actualización o eliminación de tus datos de cuenta escribiéndonos por WhatsApp o desde la sección "Mis datos" de tu perfil.</li>
    </ul>
  </div>

  <div class="section">
    <div class="sec-title">🍪 Cookies y almacenamiento local</div>
    <ul>
      <li>Usamos una cookie de sesión (<code>ci_session</code>) para mantenerte conectado mientras navegas y haces tu pedido. Es necesaria para que el sitio funcione.</li>
      <li>Guardamos en el almacenamiento local de tu navegador datos como tu carrito de compras y tus preferencias, para que no se pierdan si recargas la página.</li>
      <li>Si compartes tu ubicación GPS (ver sección de entrega), esa coordenada solo se usa para validar tu zona de cobertura y, si confirmas el pedido a domicilio, se comparte con tu repartidor.</li>
      <li>Para ayudarte a detectar errores comunes (por ejemplo, si tu conexión parece venir de fuera de El Salvador), podemos consultar de forma anónima la ubicación aproximada de tu dirección IP. Esto es solo informativo: no bloqueamos ni rechazamos pedidos por este motivo.</li>
      <li>No usamos cookies de publicidad ni de rastreo de terceros.</li>
    </ul>
  </div>

  <div class="section">
    <div class="sec-title">✅ Al confirmar tu pedido aceptas que:</div>
    <ul>
      <li>La información que proporcionaste (nombre, dirección, teléfono) es correcta y es tuya.</li>
      <li>Autorizas a Firehouse Burger a contactarte por WhatsApp y/o correo para coordinar tu pedido.</li>
      <li>Entiendes que el pedido se prepara de inmediato al confirmarse, y que los horarios de entrega son estimados.</li>
    </ul>
    <p class="muted">Si tienes dudas sobre estos términos o sobre el manejo de tus datos, escríbenos por WhatsApp antes de confirmar tu pedido — con gusto te ayudamos. 🔥</p>
  </div>

</div>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>
