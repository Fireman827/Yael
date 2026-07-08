<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1><i class="fas fa-utensils text-danger"></i> Productos en Menú Web</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=base_url('Inicio')?>">Inicio</a></li>
            <li class="breadcrumb-item active">Productos en Menú Web</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">

      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Marca los productos que quieres mostrar en el menú de pedidos en línea (<a href="<?=base_url('pedidos')?>" target="_blank">/pedidos</a>).
        Las pestañas son las mismas categorías del POS; los productos sin marcar no aparecerán para los clientes, pero seguirán
        disponibles normalmente en el POS.
      </div>

      <?php if(empty($categorias)): ?>
      <div class="alert alert-warning">No hay categorías con productos activos en el POS.</div>
      <?php else: ?>

      <div class="card card-outline card-danger">
        <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-tabs" id="tabsCategoriasProductos" role="tablist">
            <?php foreach($categorias as $i=>$cat): ?>
            <li class="nav-item">
              <a class="nav-link <?=($i==0?'active':'')?>" id="tab-cat-<?=$cat->idProductoCategoria?>" data-toggle="tab" href="#panel-cat-<?=$cat->idProductoCategoria?>" role="tab"><?=htmlspecialchars($cat->nombreProductoCategoria)?></a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <?php foreach($categorias as $i=>$cat): ?>
            <div class="tab-pane fade <?=($i==0?'show active':'')?>" id="panel-cat-<?=$cat->idProductoCategoria?>" role="tabpanel">
              <div class="row">
                <?php foreach($cat->productos as $prod): ?>
                <div class="col-md-3 col-sm-4 col-6 mb-3">
                  <div class="card h-100 mb-0">
                    <?php if(!empty($prod->imagenProducto)): ?>
                    <img src="<?=base_url($prod->imagenProducto)?>" class="card-img-top" style="height:120px;object-fit:cover" alt="">
                    <?php endif; ?>
                    <div class="card-body p-2">
                      <p class="mb-1 small font-weight-bold"><?=htmlspecialchars($prod->nombreProducto)?></p>
                      <p class="mb-2 text-muted small">$<?=number_format($prod->precioVentaProducto,2)?></p>
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input chkVisibleOnline" id="chk-prod-<?=$prod->idProducto?>" data-id="<?=$prod->idProducto?>" <?=($prod->visibleOnlineProducto=='Si'?'checked':'')?>>
                        <label class="custom-control-label" for="chk-prod-<?=$prod->idProducto?>">Mostrar en web</label>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <?php endif; ?>

    </div>
  </section>
</div>
<input type="hidden" id="csrf_token_id" value="<?=$this->security->get_csrf_hash()?>">

<script>
$(document).on('change', '.chkVisibleOnline', function(){
  var $chk = $(this);
  var idProducto = $chk.data('id');
  var visible = $chk.is(':checked') ? '1' : '0';
  $.post(window.location.origin + '/AdminOnline/toggleProductoOnline', {
    csrf_token_id: $('#csrf_token_id').val(),
    idProducto: idProducto,
    visible: visible
  }, function(r){
    if(r.codigo === 200){
      if(typeof toastr !== 'undefined') toastr.success(r.mensaje);
    } else {
      $chk.prop('checked', !$chk.is(':checked'));
      alert('Error: '+r.mensaje);
    }
  }, 'json').fail(function(){
    $chk.prop('checked', !$chk.is(':checked'));
    alert('Error de conexión, intenta de nuevo.');
  });
});
</script>
