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
        Los productos sin marcar no aparecerán para los clientes, pero seguirán disponibles normalmente en el POS.
        Los productos nuevos se crean ocultos por defecto.
      </div>

      <div class="card card-outline card-danger">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-list"></i> Listado de productos</h3>
          <div class="card-tools">
            <div class="input-group input-group-sm" style="width:250px">
              <input type="text" id="buscadorProducto" class="form-control" placeholder="Buscar producto...">
              <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover table-striped" id="tablaProductosMenu">
            <thead class="thead-dark">
              <tr>
                <th style="width:60px">ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th style="width:90px">Precio</th>
                <th style="width:110px">Estado POS</th>
                <th style="width:120px">Visible Web</th>
                <th style="width:110px">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($categorias)): ?>
              <tr><td colspan="7" class="text-center text-muted py-3">No hay categorías con productos activos.</td></tr>
              <?php else: ?>
              <?php foreach($categorias as $cat): ?>
                <?php foreach($cat->productos as $prod): ?>
                <tr>
                  <td><?= $prod->idProducto ?></td>
                  <td>
                    <?php if(!empty($prod->imagenProducto)): ?>
                    <img src="<?=base_url($prod->imagenProducto)?>" style="width:36px;height:36px;object-fit:cover;border-radius:4px;margin-right:6px;vertical-align:middle" alt="">
                    <?php endif; ?>
                    <?= htmlspecialchars($prod->nombreProducto) ?>
                  </td>
                  <td><?= htmlspecialchars($cat->nombreProductoCategoria) ?></td>
                  <td>$<?= number_format($prod->precioVentaProducto,2) ?></td>
                  <td>
                    <?php if($prod->estadoProducto === 'Activo'): ?>
                      <span class="badge badge-primary font-bold">Activo</span>
                    <?php else: ?>
                      <span class="badge badge-secondary font-bold"><?= htmlspecialchars($prod->estadoProducto) ?></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($prod->visibleOnlineProducto === 'Si'): ?>
                      <span class="badge badge-success" id="badge-<?= $prod->idProducto ?>"><i class="fas fa-globe"></i> Visible</span>
                    <?php else: ?>
                      <span class="badge badge-secondary" id="badge-<?= $prod->idProducto ?>"><i class="fas fa-eye-slash"></i> Oculto</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="input-group-prepend">
                      <button data-toggle="dropdown" class="btn btn-<?= GblTraerConfiguracion('colorComponentes') ?> btn-block btn-sm dropdown-toggle font-weight-bold" aria-expanded="false">
                        <i class="mdi mdi-menu" aria-haspopup="false"></i> Menu
                      </button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <?php if($prod->visibleOnlineProducto === 'Si'): ?>
                          <a class="dropdown-item accion-visibilidad" href="#"
                             data-id="<?= $prod->idProducto ?>" data-visible="0">
                            <i class="fas fa-eye-slash text-secondary"></i> Ocultar en web
                          </a>
                        <?php else: ?>
                          <a class="dropdown-item accion-visibilidad" href="#"
                             data-id="<?= $prod->idProducto ?>" data-visible="1">
                            <i class="fas fa-globe text-success"></i> Mostrar en web
                          </a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</div>
<input type="hidden" id="csrf_token_id" value="<?=$this->security->get_csrf_hash()?>">

<script>
// Buscador en tiempo real
$('#buscadorProducto').on('keyup', function(){
  var val = $(this).val().toLowerCase();
  $('#tablaProductosMenu tbody tr').each(function(){
    $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
  });
});

// Acción de visibilidad
$(document).on('click', '.accion-visibilidad', function(e){
  e.preventDefault();
  var $a    = $(this);
  var id    = $a.data('id');
  var vis   = $a.data('visible');
  var $badge = $('#badge-' + id);

  $a.html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

  $.post(window.location.origin + '/AdminOnline/toggleProductoOnline', {
    csrf_token_id : $('#csrf_token_id').val(),
    idProducto    : id,
    visible       : vis
  }, function(r){
    if(r.codigo === 200){
      if(vis == 1){
        $badge.removeClass('badge-secondary').addClass('badge-success').html('<i class="fas fa-globe"></i> Visible');
        $a.attr('data-visible','0').html('<i class="fas fa-eye-slash text-secondary"></i> Ocultar en web');
      } else {
        $badge.removeClass('badge-success').addClass('badge-secondary').html('<i class="fas fa-eye-slash"></i> Oculto');
        $a.attr('data-visible','1').html('<i class="fas fa-globe text-success"></i> Mostrar en web');
      }
    } else {
      alert('Error al guardar: ' + (r.mensaje || 'intente de nuevo'));
      $a.html(vis == 1 ? '<i class="fas fa-globe text-success"></i> Mostrar en web' : '<i class="fas fa-eye-slash text-secondary"></i> Ocultar en web');
    }
  }, 'json').fail(function(){
    alert('Error de conexión.');
    $a.html(vis == 1 ? '<i class="fas fa-globe text-success"></i> Mostrar en web' : '<i class="fas fa-eye-slash text-secondary"></i> Ocultar en web');
  });
});
</script>
