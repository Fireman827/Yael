<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1><i class="fas fa-globe text-danger"></i> Órdenes en Línea</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=base_url('Inicio')?>">Inicio</a></li>
            <li class="breadcrumb-item active">Pedidos Online</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <?php
        $CI=&get_instance(); $CI->load->model('AdminOnline_model');
        $r=$CI->AdminOnline_model->ResumenHoy();
      ?>
      <div class="row mb-3">
        <div class="col-6 col-md-3"><div class="small-box bg-warning"><div class="inner"><h3><?=$r?$r->pendientes:0?></h3><p>Pendientes</p></div><div class="icon"><i class="fas fa-clock"></i></div></div></div>
        <div class="col-6 col-md-3"><div class="small-box bg-info"><div class="inner"><h3><?=$r?$r->enPreparacion:0?></h3><p>En preparación</p></div><div class="icon"><i class="fas fa-fire"></i></div></div></div>
        <div class="col-6 col-md-3"><div class="small-box bg-success"><div class="inner"><h3><?=$r?$r->entregados:0?></h3><p>Entregados hoy</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
        <div class="col-6 col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>$<?=$r?number_format($r->ventaHoy,2):'0.00'?></h3><p>Venta online hoy</p></div><div class="icon"><i class="fas fa-dollar-sign"></i></div></div></div>
      </div>
      <div class="card card-outline card-danger mb-3">
        <div class="card-body py-2">
          <form method="GET" action="<?=base_url('AdminOnline/ordenes')?>">
            <div class="row align-items-end">
              <div class="col-md-2"><label class="small">Estado</label>
                <select name="estado" class="form-control form-control-sm">
                  <option value="">Todos</option>
                  <?php foreach(['Recibido','EnPreparacion','Listo','EnCamino','Entregado','Cancelado'] as $e): ?>
                  <option value="<?=$e?>" <?=$filtroEstado===$e?'selected':''?>><?=$e?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2"><label class="small">Código</label>
                <input type="text" name="codigo" class="form-control form-control-sm" value="<?=htmlspecialchars($filtroCodigo)?>" placeholder="FHB-00001">
              </div>
              <div class="col-md-2"><label class="small">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="<?=$fechaDesde?>">
              </div>
              <div class="col-md-2"><label class="small">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?=$fechaHasta?>">
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-danger btn-sm w-100"><i class="fas fa-search"></i> Buscar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="card card-outline card-danger">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-list"></i> Órdenes</h3>
          <div class="card-tools"><span class="badge badge-danger"><?=count($ordenes)?> resultados</span></div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover table-striped mb-0">
              <thead class="bg-danger text-white">
                <tr><th>Código</th><th>Fecha</th><th>Cliente</th><th>Tipo</th><th>Pago</th><th>Total</th><th>Estado</th><th>Acción</th></tr>
              </thead>
              <tbody>
              <?php if($ordenes): foreach($ordenes as $o):
                $bc=array('Recibido'=>'warning','EnPreparacion'=>'info','Listo'=>'primary','EnCamino'=>'info','Entregado'=>'success','Cancelado'=>'danger');
                $ti=array('domicilio'=>'🛵','recoger'=>'🏃','local'=>'🪑');
              ?>
              <tr>
                <td><strong class="text-danger"><?=htmlspecialchars($o->codigoSeguimientoOnline)?></strong></td>
                <td><?=date('d/m/Y H:i',strtotime($o->fechaHoraOnline))?></td>
                <td><div><?=htmlspecialchars($o->nombreCliente)?></div><small class="text-muted"><?=htmlspecialchars($o->telefonoCliente)?></small></td>
                <td><?=isset($ti[$o->tipoPedidoOnline])?$ti[$o->tipoPedidoOnline]:'📦'?> <?=ucfirst($o->tipoPedidoOnline)?></td>
                <td><span class="badge badge-secondary"><?=strtoupper($o->metodoPagoOnline)?></span></td>
                <td><strong>$<?=number_format($o->totalPedido,2)?></strong></td>
                <td><span class="badge badge-<?=isset($bc[$o->estadoOnline])?$bc[$o->estadoOnline]:'secondary'?>"><?=$o->estadoOnline?></span></td>
                <td><button class="btn btn-xs btn-outline-danger" onclick="abrirOrden(<?=$o->idPedidoOnline?>,'<?=$o->estadoOnline?>','<?=htmlspecialchars($o->codigoSeguimientoOnline)?>')"><i class="fas fa-edit"></i></button></td>
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="8" class="text-center text-muted py-3">No hay órdenes con estos filtros.</td></tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<div class="modal fade" id="modalOrden" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Gestionar Orden <span id="moCode"></span></h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <label>Cambiar estado</label>
        <select class="form-control" id="selEstado">
          <option value="Recibido">📋 Recibido</option>
          <option value="EnPreparacion">👨‍🍳 En Preparación</option>
          <option value="Listo">✅ Listo</option>
          <option value="EnCamino">🛵 En Camino</option>
          <option value="Entregado">📍 Entregado</option>
          <option value="Cancelado">❌ Cancelado</option>
        </select>
        <p class="small text-muted mt-2">El cliente recibe WhatsApp al cambiar estado (en producción).</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button class="btn btn-danger" onclick="guardarEstado()"><i class="fas fa-save"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="csrf_token_id" value="<?=$this->security->get_csrf_hash()?>">
<script>
var url=window.location.origin+'/'; var idActual=null;
function abrirOrden(id,estado,codigo){ idActual=id; document.getElementById('moCode').textContent=codigo; document.getElementById('selEstado').value=estado; $('#modalOrden').modal('show'); }
function guardarEstado(){
  $.post(url+'AdminOnline/cambiarEstado',{idOnline:idActual,estado:$('#selEstado').val(),csrf_token_id:$('#csrf_token_id').val()},function(r){
    if(r.codigo==200){ $('#modalOrden').modal('hide'); location.reload(); } else { alert('Error: '+r.mensaje); }
  },'json');
}
<?php if(count(array_filter($ordenes?:[],function($o){return $o->estadoOnline==='Recibido';}))):?>
setTimeout(function(){location.reload();},30000);
<?php endif;?>
</script>
