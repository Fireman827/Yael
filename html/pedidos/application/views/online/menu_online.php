<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menú - Firehouse Burger</title>
<link rel="icon" type="image/png" href="<?= base_url('vendors/core/img/logo.png') ?>">
<link rel="stylesheet" href="<?= '/' ?>">
<style>
:root{--red:#C0392B;--red-d:#922B21;--black:#111;--dark:#1a1a1a;--card:#1e1e1e;--border:#2a2a2a;--text:#e8e8e8;--muted:#888}
*{box-sizing:border-box}
body{background:var(--black);color:var(--text);font-family:'Segoe UI',sans-serif;padding-bottom:90px;margin:0}
.top-bar{background:linear-gradient(135deg,#0d0d0d,#1a0808);border-bottom:2px solid var(--red);padding:10px 16px;position:sticky;top:0;z-index:200;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 16px rgba(192,57,43,.35)}
.brand-wrap{display:flex;align-items:center;gap:10px}
.brand-logo{height:38px;width:38px;object-fit:contain;filter:drop-shadow(0 0 6px rgba(192,57,43,.6))}
.brand-name{font-size:16px;font-weight:700;color:var(--red);letter-spacing:1px}
.user-chip{display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.06);border-radius:20px;padding:4px 10px 4px 4px}
.avatar{width:26px;height:26px;border-radius:50%;background:var(--red);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700}
.btn-link-sm{font-size:11px;color:var(--muted);text-decoration:none;margin-left:2px}
.btn-link-sm:hover{color:var(--red)}
.horario-banner{background:rgba(192,57,43,.15);border-bottom:1px solid var(--red);padding:10px 16px;font-size:12.5px;line-height:1.6;color:var(--text);text-align:center}
.horario-banner b{color:var(--red)}
.cat-bar{display:flex;gap:8px;overflow-x:auto;padding:10px 16px;background:var(--dark);border-bottom:1px solid var(--border);scrollbar-width:none;position:sticky;top:62px;z-index:100}
.cat-bar::-webkit-scrollbar{display:none}
.cat-pill{padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;border:1.5px solid var(--border);background:var(--card);color:var(--muted);text-decoration:none;white-space:nowrap;transition:all .2s;flex-shrink:0}
.cat-pill:hover{border-color:var(--red);color:var(--text)}
.cat-pill.active{background:var(--red);border-color:var(--red);color:#fff}
.products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(155px,1fr));gap:12px;padding:14px 16px;max-width:960px;margin:0 auto}
.prod-card{background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;cursor:pointer;transition:all .2s;display:flex;flex-direction:column}
.prod-card:hover{border-color:var(--red);box-shadow:0 6px 24px rgba(192,57,43,.25);transform:translateY(-2px)}
.prod-img-wrap{position:relative;overflow:hidden;aspect-ratio:4/3}
.prod-img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s ease}
.prod-card:hover .prod-img{transform:scale(1.08)}
.prod-img-ph{width:100%;aspect-ratio:4/3;background:linear-gradient(135deg,#1a1a1a,#252525);display:flex;align-items:center;justify-content:center;font-size:2.5rem}
.prod-body{padding:10px;flex:1;display:flex;flex-direction:column}
.prod-name{font-size:13px;font-weight:600;color:var(--text);line-height:1.3;margin-bottom:2px}
.prod-desc-short{font-size:11px;color:var(--muted);line-height:1.4;flex:1}
.prod-price{font-size:16px;font-weight:700;color:var(--red);margin-top:6px}
.qty-ctrl{display:flex;align-items:center;gap:6px;margin-top:8px}
.qty-btn{width:28px;height:28px;border-radius:50%;border:1.5px solid var(--border);background:var(--dark);color:var(--text);font-size:15px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;line-height:1;flex-shrink:0}
.qty-btn:hover{border-color:var(--red);background:var(--red);color:#fff}
.qty-btn.del{border-color:#555;font-size:12px}
.qty-btn.del:hover{border-color:#e74c3c;background:#e74c3c}
.qty-num{font-size:14px;font-weight:600;min-width:20px;text-align:center}
/* Cart bar */
.cart-bar{position:fixed;bottom:0;left:0;right:0;background:linear-gradient(135deg,var(--red),var(--red-d));padding:12px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 -4px 20px rgba(192,57,43,.5);transform:translateY(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);z-index:300}
.cart-bar.visible{transform:translateY(0)}
.cart-count{background:rgba(255,255,255,.25);color:#fff;border-radius:20px;padding:2px 10px;font-size:13px;font-weight:700}
.cart-total-txt{flex:1;font-size:15px;font-weight:600;color:#fff}
.btn-cart-detail{background:rgba(255,255,255,.15);color:#fff;border:1.5px solid rgba(255,255,255,.4);padding:7px 12px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.btn-cart-detail:hover{background:rgba(255,255,255,.25)}
.btn-ordenar{background:#fff;color:var(--red);border:none;padding:9px 22px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s}
.btn-ordenar:hover{background:var(--black);color:#fff}
/* Drawer del carrito */
.cart-drawer{position:fixed;bottom:0;left:0;right:0;background:var(--card);border-top:2px solid var(--red);border-radius:20px 20px 0 0;z-index:400;max-height:70vh;overflow-y:auto;transform:translateY(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);padding:1rem}
.cart-drawer.open{transform:translateY(0)}
.drawer-handle{width:40px;height:4px;background:var(--border);border-radius:2px;margin:0 auto 1rem}
.drawer-title{font-size:15px;font-weight:700;margin-bottom:.75rem}
.cart-item{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)}
.cart-item:last-child{border-bottom:none}
.cart-item-name{flex:1;font-size:13px;font-weight:600}
.cart-item-price{font-size:13px;color:var(--red);font-weight:700;white-space:nowrap}
.cart-item-controls{display:flex;align-items:center;gap:6px}
.btn-del-item{background:rgba(231,76,60,.15);border:1px solid rgba(231,76,60,.3);color:#e74c3c;border-radius:6px;font-size:11px;padding:3px 8px;cursor:pointer;transition:all .2s}
.btn-del-item:hover{background:#e74c3c;color:#fff}
.drawer-total{display:flex;justify-content:space-between;font-size:16px;font-weight:700;border-top:1.5px solid var(--border);padding-top:10px;margin-top:4px}
.drawer-total span:last-child{color:var(--red)}
.btn-checkout-drawer{background:linear-gradient(135deg,var(--red),var(--red-d));color:#fff;border:none;width:100%;padding:13px;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;margin-top:.75rem}
.drawer-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:350;display:none}
.drawer-overlay.open{display:block}
/* Modal producto */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:500;display:none;align-items:flex-end;justify-content:center}
.modal-overlay.open{display:flex}
.modal-sheet{background:var(--card);border-radius:20px 20px 0 0;border-top:2px solid var(--red);width:100%;max-width:580px;max-height:92vh;overflow-y:auto;animation:slideUp .25s ease}
@keyframes slideUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.modal-img{width:100%;max-height:280px;object-fit:cover;display:block;transition:transform .4s ease;cursor:zoom-in}
.modal-img:hover{transform:scale(1.08)}
.modal-img-ph{height:180px;background:linear-gradient(135deg,#1a1a1a,#252525);display:flex;align-items:center;justify-content:center;font-size:4rem}
.modal-close{position:absolute;top:12px;right:14px;background:rgba(0,0,0,.6);border:none;width:32px;height:32px;border-radius:50%;color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10}
.modal-body-pad{padding:1.25rem}
.modal-nombre{font-size:1.1rem;font-weight:700;margin-bottom:4px}
.modal-precio{font-size:1.6rem;font-weight:700;color:var(--red);margin-bottom:8px}
.modal-desc{font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:1rem}
.modal-qty-row{display:flex;align-items:center;gap:10px;margin-bottom:1rem}
.modal-qty-label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}
.btn-add{background:linear-gradient(135deg,var(--red),var(--red-d));color:#fff;border:none;width:100%;padding:13px;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;margin:0 1.25rem;width:calc(100% - 2.5rem)}
.empty-state{text-align:center;padding:4rem 1rem;color:var(--muted);grid-column:1/-1}
</style>
</head>
<body>

<div class="top-bar">
  <div class="brand-wrap">
    <img src="/vendors/core/img/logo.png" class="brand-logo" alt="FHB" onerror="this.style.display='none'">
    <span class="brand-name">Firehouse Burger</span>
  </div>
  <div class="user-chip">
    <div class="avatar"><?= strtoupper(substr($sesion['nombre'], 0, 2)) ?></div>
    <span style="font-size:13px"><?= htmlspecialchars(explode(' ', $sesion['nombre'])[0]) ?></span>
    <a href="<?= site_url('perfil') ?>" class="btn-link-sm">perfil</a>
    <a href="<?= site_url('logout') ?>" class="btn-link-sm">salir</a>
  </div>
</div>

<?php $horario = HorarioOnlineEstado(); ?>
<?php if (!$horario['abierto']): ?>
<div class="horario-banner">
  🕒 <b>Estamos cerrados en este momento.</b> Horario de pedidos en línea: <?= htmlspecialchars($horario['horarioTexto']) ?> — puedes armar tu pedido y enviarlo cuando abramos.
</div>
<?php endif; ?>

<div class="cat-bar">
  <?php if ($categorias): foreach ($categorias as $cat): ?>
  <a href="<?= site_url('menu?cat=' . $cat->idProductoCategoria) ?>"
     class="cat-pill <?= ($catActiva == $cat->idProductoCategoria) ? 'active' : '' ?>">
    <?= htmlspecialchars($cat->nombreProductoCategoria) ?>
  </a>
  <?php endforeach; endif; ?>
</div>

<div class="products-grid">
<?php if ($productos): foreach ($productos as $p):
  $key       = 'p_' . $p->idProducto;
  $enCarrito = isset($carrito[$key]) ? $carrito[$key]['cantidad'] : 0;
  $imgUrl    = !empty($p->imagenProducto) ? '/' . ltrim($p->imagenProducto, '/') : '';
  $desc      = $p->descripcionProducto ?? '';
  $descShort = $desc ? mb_substr($desc, 0, 60) . (mb_strlen($desc) > 60 ? '...' : '') : '';
?>
  <div class="prod-card"
       data-id="<?= $p->idProducto ?>"
       data-nombre="<?= htmlspecialchars($p->nombreProducto) ?>"
       data-precio="<?= $p->precioVentaProducto ?>"
       data-desc="<?= htmlspecialchars($desc) ?>"
       data-img="<?= $imgUrl ?>"
       onclick="abrirModal(this)">
    <div class="prod-img-wrap">
      <?php if ($imgUrl): ?>
        <img class="prod-img" src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($p->nombreProducto) ?>" loading="lazy"
             onerror="this.parentElement.innerHTML='<div class=prod-img-ph>🍔</div>'">
      <?php else: ?>
        <div class="prod-img-ph">🍔</div>
      <?php endif; ?>
    </div>
    <div class="prod-body">
      <div class="prod-name"><?= htmlspecialchars($p->nombreProducto) ?></div>
      <?php if ($descShort): ?><div class="prod-desc-short"><?= htmlspecialchars($descShort) ?></div><?php endif; ?>
      <div class="prod-price">$<?= number_format($p->precioVentaProducto, 2) ?></div>
      <div class="qty-ctrl" onclick="event.stopPropagation()">
        <button class="qty-btn del" onclick="eliminarItem(<?= $p->idProducto ?>)" title="Eliminar del carrito">🗑</button>
        <button class="qty-btn" onclick="cambiarCantidad(<?= $p->idProducto ?>, 'remove')">−</button>
        <span class="qty-num" id="qty-<?= $p->idProducto ?>"><?= $enCarrito ?: 0 ?></span>
        <button class="qty-btn" onclick="cambiarCantidad(<?= $p->idProducto ?>, 'add')">+</button>
      </div>
    </div>
  </div>
<?php endforeach; else: ?>
  <div class="empty-state"><div style="font-size:3rem">🔥</div><p>No hay productos en esta categoría.</p></div>
<?php endif; ?>
</div>

<!-- Cart bar -->
<div class="cart-bar" id="cartBar">
  <span class="cart-count" id="cartCount">0</span>
  <div class="cart-total-txt">· <strong id="cartTotal">$0.00</strong></div>
  <button class="btn-cart-detail" onclick="abrirDrawer()">Ver carrito</button>
  <button class="btn-ordenar" onclick="irCheckout()">Ordenar →</button>
</div>

<!-- Drawer del carrito -->
<div class="drawer-overlay" id="drawerOverlay" onclick="cerrarDrawer()"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-title">🛒 Tu carrito</div>
  <div id="drawerItems"></div>
  <div class="drawer-total" id="drawerTotal" style="display:none">
    <span>Total</span><span id="drawerTotalVal">$0.00</span>
  </div>
  <button class="btn-checkout-drawer" onclick="irCheckout()">Confirmar pedido →</button>
</div>

<!-- Modal producto -->
<div class="modal-overlay" id="modalOverlay" onclick="cerrarModalClick(event)">
  <div class="modal-sheet">
    <div style="position:relative">
      <img id="modalImg" src="" alt="" class="modal-img" style="display:none">
      <div id="modalImgPh" class="modal-img-ph" style="display:none">🍔</div>
      <button class="modal-close" onclick="cerrarModal()">×</button>
    </div>
    <div class="modal-body-pad">
      <div class="modal-nombre" id="modalNombre"></div>
      <div class="modal-precio" id="modalPrecio"></div>
      <div class="modal-desc"   id="modalDesc"></div>
      <div class="modal-qty-row">
        <span class="modal-qty-label">Cantidad:</span>
        <button class="qty-btn" onclick="modalDelta(-1)">−</button>
        <span class="qty-num" id="modalQty">1</span>
        <button class="qty-btn" onclick="modalDelta(1)">+</button>
      </div>
    </div>
    <button class="btn-add" id="btnAdd" onclick="agregarDesdeModal()">🛒 Agregar al carrito</button>
    <div style="height:1.25rem"></div>
  </div>
</div>

<input type="hidden" id="csrf_token_id" value="<?= $this->security->get_csrf_hash() ?>">

<script src="/vendors/plugins/jquery/jquery.min.js"></script>
<script>
var siteUrl    = '<?= site_url() ?>';
var carrito    = <?= json_encode(array_values($carrito)) ?>;
var modalProdId= null;
var modalCant  = 1;

function cambiarCantidad(idProducto, accion) {
  $.post(siteUrl+'agregar_carrito',
    {idProducto:idProducto, accion:accion, csrf_token_id:$('#csrf_token_id').val()},
    function(r){ if(r.codigo==200){ carrito=r.carrito; actualizarQty(idProducto,r); actualizarCartBar(r.totalItems,r.totalPrecio); } },'json');
}

function eliminarItem(idProducto) {
  $.post(siteUrl+'agregar_carrito',
    {idProducto:idProducto, accion:'delete', csrf_token_id:$('#csrf_token_id').val()},
    function(r){ if(r.codigo==200){ carrito=r.carrito; $('#qty-'+idProducto).text(0); actualizarCartBar(r.totalItems,r.totalPrecio); if(drawerAbierto) renderDrawer(); } },'json');
}

function actualizarQty(idProducto, r) {
  var qty=0;
  r.carrito.forEach(function(i){ if(i.idProducto==idProducto) qty=i.cantidad; });
  $('#qty-'+idProducto).text(qty);
  if(drawerAbierto) renderDrawer();
}

function actualizarCartBar(items, total) {
  if(items>0){
    $('#cartBar').addClass('visible');
    $('#cartCount').text(items+(items==1?' item':' items'));
    $('#cartTotal').text('$'+total);
  } else {
    $('#cartBar').removeClass('visible');
    cerrarDrawer();
  }
}

function irCheckout(){ window.location.href = siteUrl+'checkout'; }

/* ── Drawer ── */
var drawerAbierto = false;
function abrirDrawer() {
  renderDrawer();
  $('#cartDrawer').addClass('open');
  $('#drawerOverlay').addClass('open');
  drawerAbierto = true;
  document.body.style.overflow = 'hidden';
}
function cerrarDrawer() {
  $('#cartDrawer').removeClass('open');
  $('#drawerOverlay').removeClass('open');
  drawerAbierto = false;
  document.body.style.overflow = '';
}
function renderDrawer() {
  var html = '';
  var total = 0;
  carrito.forEach(function(item) {
    var sub = item.precio * item.cantidad;
    total += sub;
    html += '<div class="cart-item">' +
      '<div class="cart-item-name">' + item.nombre + '<br>' +
        '<small style="color:var(--muted)">×' + item.cantidad + ' · $' + item.precio.toFixed(2) + ' c/u</small>' +
      '</div>' +
      '<div class="cart-item-controls">' +
        '<button class="qty-btn" style="width:24px;height:24px;font-size:13px" onclick="cambiarCantidad('+item.idProducto+',\'remove\')">−</button>' +
        '<span style="font-size:13px;min-width:16px;text-align:center">' + item.cantidad + '</span>' +
        '<button class="qty-btn" style="width:24px;height:24px;font-size:13px" onclick="cambiarCantidad('+item.idProducto+',\'add\')">+</button>' +
      '</div>' +
      '<div class="cart-item-price">$' + sub.toFixed(2) + '</div>' +
      '<button class="btn-del-item" onclick="eliminarItem('+item.idProducto+')">🗑 Quitar</button>' +
    '</div>';
  });
  if (!carrito.length) html = '<p style="color:var(--muted);text-align:center;padding:1rem">Tu carrito está vacío.</p>';
  $('#drawerItems').html(html);
  if (carrito.length) {
    $('#drawerTotal').show();
    $('#drawerTotalVal').text('$' + total.toFixed(2));
  } else {
    $('#drawerTotal').hide();
  }
}

/* ── Modal ── */
function abrirModal(card) {
  modalProdId = parseInt(card.dataset.id);
  modalCant = 1;
  $('#modalNombre').text(card.dataset.nombre);
  $('#modalPrecio').text('$' + parseFloat(card.dataset.precio).toFixed(2));
  $('#modalDesc').text(card.dataset.desc || 'Sin descripción.');
  $('#modalQty').text(1);
  $('#btnAdd').text('🛒 Agregar al carrito').prop('disabled', false);
  var img = card.dataset.img;
  if (img) { $('#modalImg').attr('src', img).show(); $('#modalImgPh').hide(); }
  else      { $('#modalImg').hide(); $('#modalImgPh').show(); }
  $('#modalOverlay').addClass('open');
  document.body.style.overflow = 'hidden';
}
function cerrarModal() { $('#modalOverlay').removeClass('open'); document.body.style.overflow=''; }
function cerrarModalClick(e) { if(e.target===document.getElementById('modalOverlay')) cerrarModal(); }
function modalDelta(d) { modalCant = Math.max(1, modalCant+d); $('#modalQty').text(modalCant); }
function agregarDesdeModal() {
  if (!modalProdId) return;
  var btn = document.getElementById('btnAdd');
  btn.disabled = true; btn.textContent = 'Agregando...';
  var reqs = [];
  for(var i=0;i<modalCant;i++) reqs.push($.post(siteUrl+'agregar_carrito',{idProducto:modalProdId,accion:'add',csrf_token_id:$('#csrf_token_id').val()},null,'json'));
  $.when.apply($, reqs).always(function(){
    $.post(siteUrl+'carrito',{csrf_token_id:$('#csrf_token_id').val()},function(r){
      if(r.codigo==200){ carrito=r.carrito; $('#qty-'+modalProdId).text(0); r.carrito.forEach(function(i){if(i.idProducto==modalProdId)$('#qty-'+modalProdId).text(i.cantidad);}); actualizarCartBar(r.totalItems,r.totalPrecio); }
      btn.textContent='✅ ¡Agregado!'; setTimeout(function(){cerrarModal();},700);
    },'json');
  });
}

/* Init */
(function(){
  var items=0,total=0;
  carrito.forEach(function(i){items+=i.cantidad;total+=i.precio*i.cantidad;});
  if(items>0) actualizarCartBar(items,total.toFixed(2));
})();
</script>
<?php $this->load->view('online/_cookie_banner'); ?>
</body>
</html>

