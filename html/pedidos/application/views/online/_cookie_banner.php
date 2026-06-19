<!-- ============================================================
     Aviso de cookies / almacenamiento local
     Incluir antes de </body> en cada vista de pedidos en línea.
     ============================================================ -->
<div id="fhbCookieBanner" style="
  position:fixed; left:0; right:0; bottom:0; z-index:99999;
  background:#1a1a1a; border-top:2px solid #C0392B;
  color:#e8e8e8; font-family:'Segoe UI',sans-serif; font-size:13px;
  padding:14px 16px; box-shadow:0 -4px 20px rgba(0,0,0,.5);
  display:none; align-items:center; gap:14px; flex-wrap:wrap;
">
  <div style="flex:1; min-width:220px; line-height:1.5">
    🍪 Usamos cookies y almacenamiento local para mantener tu sesión, tu carrito y
    recordar tus preferencias. Con tu permiso, también podemos usar tu ubicación
    para validar zonas de entrega. Más información en nuestra
    <a href="<?= site_url('terminos') ?>" target="_blank" style="color:#ff8a7a">Política de Privacidad</a>.
  </div>
  <div style="display:flex; gap:8px; flex-shrink:0">
    <button onclick="fhbCookieConsent('rechazado')" style="
      background:none; border:1px solid #444; color:#aaa;
      padding:8px 14px; border-radius:8px; font-size:13px; cursor:pointer;
    ">Solo lo esencial</button>
    <button onclick="fhbCookieConsent('aceptado')" style="
      background:linear-gradient(135deg,#C0392B,#922B21); border:none; color:#fff;
      padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;
    ">Aceptar</button>
  </div>
</div>
<script>
(function(){
  try {
    if (!localStorage.getItem('fhb_cookie_consent')) {
      var b = document.getElementById('fhbCookieBanner');
      if (b) b.style.display = 'flex';
    }
  } catch (e) {}
})();
function fhbCookieConsent(valor) {
  try { localStorage.setItem('fhb_cookie_consent', valor); } catch (e) {}
  var b = document.getElementById('fhbCookieBanner');
  if (b) b.style.display = 'none';
}
</script>


