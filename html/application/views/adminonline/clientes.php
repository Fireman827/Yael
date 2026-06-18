<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2"><div class="col-sm-6"><h1><i class="fas fa-users text-danger"></i> Clientes Web</h1></div></div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card card-outline card-danger mb-3">
        <div class="card-body py-2">
          <form method="GET" action="<?=base_url('AdminOnline/clientes')?>" class="form-inline">
            <input type="text" name="buscar" class="form-control form-control-sm mr-2" style="width:300px" placeholder="Nombre, correo o teléfono..." value="<?=htmlspecialchars($buscar)?>">
            <button class="btn btn-danger btn-sm"><i class="fas fa-search"></i> Buscar</button>
          </form>
        </div>
      </div>
      <div class="card card-outline card-danger">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-user-circle"></i> Clientes registrados</h3>
          <div class="card-tools"><span class="badge badge-danger"><?=count($clientes)?></span></div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover table-striped mb-0">
              <thead class="bg-danger text-white">
                <tr><th>Cliente</th><th>Teléfono</th><th>Email</th><th>Pedidos</th><th>Último</th><th>Estado</th><th>Acciones</th></tr>
              </thead>
              <tbody>
              <?php if($clientes): foreach($clientes as $c): ?>
              <tr>
                <td><strong><?=htmlspecialchars($c->nombreCliente)?></strong><br><small class="text-muted"><?=htmlspecialchars($c->direccionCliente)?></small></td>
                <td><?=htmlspecialchars($c->telefonoCliente)?></td>
                <td><?=htmlspecialchars($c->emailCliente?:$c->emailAcceso)?></td>
                <td><span class="badge badge-info"><?=$c->totalPedidos?></span></td>
                <td><?=$c->ultimoPedido?date('d/m/Y',strtotime($c->ultimoPedido)):'—'?></td>
                <td><span class="badge badge-<?=$c->estadoAcceso==='Activo'?'success':'danger'?>"><?=$c->estadoAcceso?></span></td>
                <td>
                  <button class="btn btn-xs btn-outline-danger mr-1" onclick='abrirEditar(<?=json_encode($c)?> )'><i class="fas fa-edit"></i></button>
                  <button class="btn btn-xs btn-outline-warning" onclick="abrirOTP(<?=$c->idClienteAcceso?>,'<?=htmlspecialchars($c->nombreCliente)?>')"><i class="fas fa-key"></i></button>
                </td>
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="7" class="text-center text-muted py-3">No hay clientes.</td></tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-user-edit"></i> Editar Cliente</h5><button class="close text-white" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <input type="hidden" id="eId">
        <div class="form-group"><label class="small text-muted">NOMBRE</label><input type="text" id="eNombre" class="form-control form-control-sm"></div>
        <div class="form-group"><label class="small text-muted">TELÉFONO</label><input type="tel" id="eTel" class="form-control form-control-sm"></div>
        <div class="form-group"><label class="small text-muted">EMAIL</label><input type="email" id="eEmail" class="form-control form-control-sm"></div>
        <div class="form-group"><label class="small text-muted">DIRECCIÓN</label><input type="text" id="eDir" class="form-control form-control-sm"></div>
        <div class="form-group"><label class="small text-muted">ESTADO</label>
          <select id="eEstado" class="form-control form-control-sm"><option>Activo</option><option>Inactivo</option></select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger btn-sm" onclick="guardarCliente()"><i class="fas fa-save"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal OTP -->
<div class="modal fade" id="modalOTP" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-warning"><h5 class="modal-title"><i class="fas fa-key"></i> OTP — <span id="otpNombre"></span></h5><button class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <p class="small text-muted">Enviar OTP a número alternativo para soporte al cliente.</p>
        <div class="form-group"><label class="small">TELÉFONO DESTINO</label><input type="tel" id="otpTel" class="form-control form-control-sm" placeholder="50370000000"></div>
        <div id="otpResult"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
        <button class="btn btn-warning btn-sm" onclick="enviarOTP()"><i class="fab fa-whatsapp"></i> Enviar</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="csrf_token_id" value="<?=$this->security->get_csrf_hash()?>">
<script>
var url=window.location.origin+'/'; var otpId=null;
function abrirEditar(c){
  $('#eId').val(c.idClienteAcceso); $('#eNombre').val(c.nombreCliente||'');
  $('#eTel').val(c.telefonoCliente||''); $('#eEmail').val(c.emailCliente||c.emailAcceso||'');
  $('#eDir').val(c.direccionCliente||''); $('#eEstado').val(c.estadoAcceso||'Activo');
  $('#modalEditar').modal('show');
}
function guardarCliente(){
  $.post(url+'AdminOnline/editarCliente',{idClienteAcceso:$('#eId').val(),nombre:$('#eNombre').val(),telefono:$('#eTel').val(),email:$('#eEmail').val(),direccion:$('#eDir').val(),estado:$('#eEstado').val(),csrf_token_id:$('#csrf_token_id').val()},function(r){
    if(r.codigo==200){$('#modalEditar').modal('hide');location.reload();}else{alert('Error: '+r.mensaje);}
  },'json');
}
function abrirOTP(id,nombre){ otpId=id; $('#otpNombre').text(nombre); $('#otpTel').val(''); $('#otpResult').html(''); $('#modalOTP').modal('show'); }
function enviarOTP(){
  $.post(url+'AdminOnline/enviarOtpAdmin',{idClienteAcceso:otpId,telefonoNuevo:$('#otpTel').val(),csrf_token_id:$('#csrf_token_id').val()},function(r){
    var c=r.codigo==200?'success':'danger'; var m=r.mensaje+(r.otp_debug?' <strong>(PRUEBA: '+r.otp_debug+')</strong>':'');
    $('#otpResult').html('<div class="alert alert-'+c+' py-2 small mb-0">'+m+'</div>');
  },'json');
}
</script>
