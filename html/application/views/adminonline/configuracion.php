<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1><i class="fas fa-cog text-danger"></i> Configuración de Pagos</h1></div></div></div>
  </section>
  <section class="content"><div class="container-fluid"><div class="row">

    <!-- Banco -->
    <div class="col-md-6">
      <div class="card card-outline card-danger">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-university"></i> Transferencia Bancaria</h3></div>
        <div class="card-body">
          <div class="form-group"><label class="small text-muted">BANCO</label><input type="text" class="form-control form-control-sm cfg-input" name="PAGO_BANCO_NOMBRE" value="<?=htmlspecialchars($config['PAGO_BANCO_NOMBRE'])?>"></div>
          <div class="form-group"><label class="small text-muted">NÚMERO DE CUENTA</label><input type="text" class="form-control form-control-sm cfg-input" name="PAGO_BANCO_CUENTA" value="<?=htmlspecialchars($config['PAGO_BANCO_CUENTA'])?>"></div>
          <div class="form-group"><label class="small text-muted">TITULAR</label><input type="text" class="form-control form-control-sm cfg-input" name="PAGO_BANCO_TITULAR" value="<?=htmlspecialchars($config['PAGO_BANCO_TITULAR'])?>"></div>
          <div class="form-group"><label class="small text-muted">TIPO</label>
            <select class="form-control form-control-sm cfg-input" name="PAGO_BANCO_TIPO">
              <option <?=$config['PAGO_BANCO_TIPO']==='Cuenta Corriente'?'selected':''?>>Cuenta Corriente</option>
              <option <?=$config['PAGO_BANCO_TIPO']==='Cuenta de Ahorro'?'selected':''?>>Cuenta de Ahorro</option>
            </select>
          </div>
          <button class="btn btn-danger btn-sm mb-3" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar</button>
          <hr>
          <label class="small text-muted">QR TRANSFERENCIA</label>
          <div class="d-flex align-items-center gap-3 mt-2">
            <?php if(!empty($config['PAGO_BANCO_QR'])): ?>
              <img src="<?=base_url($config['PAGO_BANCO_QR'])?>" id="prevBanco" style="width:100px;height:100px;object-fit:contain;border:1px solid #dee2e6;border-radius:8px">
            <?php else: ?>
              <div id="prevBanco" style="width:100px;height:100px;border:2px dashed #ccc;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:11px;text-align:center">Sin QR</div>
            <?php endif; ?>
            <div><input type="file" id="fileBanco" accept="image/*" class="form-control-file mb-2"><button class="btn btn-outline-danger btn-sm" onclick="subirQR('qr_banco','fileBanco','prevBanco')"><i class="fas fa-upload"></i> Subir QR</button></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chivo -->
    <div class="col-md-6">
      <div class="card card-outline card-danger">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-bolt"></i> Chivo Wallet</h3></div>
        <div class="card-body">
          <div class="form-group"><label class="small text-muted">NÚMERO O DIRECCIÓN CHIVO</label><input type="text" class="form-control form-control-sm cfg-input" name="PAGO_CHIVO_NUMERO" value="<?=htmlspecialchars($config['PAGO_CHIVO_NUMERO'])?>" placeholder="ej: 7000-0000"></div>
          <button class="btn btn-danger btn-sm mb-3" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar</button>
          <hr>
          <label class="small text-muted">QR CHIVO</label>
          <div class="d-flex align-items-center gap-3 mt-2">
            <?php if(!empty($config['PAGO_CHIVO_QR'])): ?>
              <img src="<?=base_url($config['PAGO_CHIVO_QR'])?>" id="prevChivo" style="width:100px;height:100px;object-fit:contain;border:1px solid #dee2e6;border-radius:8px">
            <?php else: ?>
              <div id="prevChivo" style="width:100px;height:100px;border:2px dashed #ccc;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:11px;text-align:center">Sin QR</div>
            <?php endif; ?>
            <div><input type="file" id="fileChivo" accept="image/*" class="form-control-file mb-2"><button class="btn btn-outline-danger btn-sm" onclick="subirQR('qr_chivo','fileChivo','prevChivo')"><i class="fas fa-upload"></i> Subir QR</button></div>
          </div>
        </div>
      </div>
    </div>

    <!-- WhatsApp -->
    <div class="col-md-6">
      <div class="card card-outline card-warning">
        <div class="card-header"><h3 class="card-title"><i class="fab fa-whatsapp"></i> WhatsApp — Fonnte</h3></div>
        <div class="card-body">
          <div class="form-group"><label class="small text-muted">TOKEN FONNTE</label><input type="text" class="form-control form-control-sm cfg-input" name="WA_API_KEY_REST" value="<?=htmlspecialchars($config['WA_API_KEY_REST'])?>"></div>
          <div class="form-group"><label class="small text-muted">NÚMERO RESTAURANTE</label><input type="text" class="form-control form-control-sm cfg-input" name="WA_NUMERO_REST" value="<?=htmlspecialchars($config['WA_NUMERO_REST'])?>" placeholder="503XXXXXXXX"></div>
          <div class="form-group"><label class="small text-muted">MODO PRUEBA OTP</label>
            <select class="form-control form-control-sm cfg-input" name="WA_MODO_PRUEBA">
              <option value="TRUE"  <?=$config['WA_MODO_PRUEBA']==='TRUE' ?'selected':''?>>TRUE — Mostrar código en pantalla (desarrollo)</option>
              <option value="FALSE" <?=$config['WA_MODO_PRUEBA']==='FALSE'?'selected':''?>>FALSE — Enviar WhatsApp real (producción)</option>
            </select>
          </div>
          <button class="btn btn-warning btn-sm" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar WhatsApp</button>
        </div>
      </div>
    </div>

    <!-- Correo: Pedidos Web -->
    <div class="col-md-6">
      <div class="card card-outline card-info">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-envelope"></i> Correo — Pedidos Web</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label class="small text-muted">PROVEEDOR DE CORREO</label>
            <select class="form-control form-control-sm cfg-input" name="EMAIL_PROVIDER" onchange="toggleProveedorCorreo('EMAIL')">
              <option value="SMTP"     <?=$config['EMAIL_PROVIDER']==='SMTP'    ?'selected':''?>>SMTP</option>
              <option value="SENDGRID" <?=$config['EMAIL_PROVIDER']==='SENDGRID'?'selected':''?>>SendGrid (API)</option>
            </select>
          </div>
          <div class="form-group"><label class="small text-muted">CORREO REMITENTE</label><input type="email" class="form-control form-control-sm cfg-input" name="EMAIL_FROM" value="<?=htmlspecialchars($config['EMAIL_FROM'])?>" placeholder="pedidos@tudominio.com"></div>
          <div class="form-group"><label class="small text-muted">NOMBRE REMITENTE</label><input type="text" class="form-control form-control-sm cfg-input" name="EMAIL_FROM_NAME" value="<?=htmlspecialchars($config['EMAIL_FROM_NAME'])?>" placeholder="Firehouse Burger"></div>

          <div class="sendgrid-fields-EMAIL">
            <div class="form-group"><label class="small text-muted">API KEY SENDGRID</label><input type="text" class="form-control form-control-sm cfg-input" name="SENDGRID_API_KEY" value="<?=htmlspecialchars($config['SENDGRID_API_KEY'])?>"></div>
          </div>

          <div class="smtp-fields-EMAIL">
            <div class="form-group"><label class="small text-muted">SERVIDOR SMTP</label><input type="text" class="form-control form-control-sm cfg-input" name="EMAIL_SMTP_HOST" value="<?=htmlspecialchars($config['EMAIL_SMTP_HOST'])?>" placeholder="smtp.gmail.com"></div>
            <div class="form-group"><label class="small text-muted">PUERTO</label><input type="text" class="form-control form-control-sm cfg-input" name="EMAIL_SMTP_PORT" value="<?=htmlspecialchars($config['EMAIL_SMTP_PORT'])?>" placeholder="587"></div>
            <div class="form-group"><label class="small text-muted">USUARIO SMTP</label><input type="text" class="form-control form-control-sm cfg-input" name="EMAIL_SMTP_USER" value="<?=htmlspecialchars($config['EMAIL_SMTP_USER'])?>"></div>
            <div class="form-group"><label class="small text-muted">CONTRASEÑA SMTP</label><input type="password" class="form-control form-control-sm cfg-input" name="EMAIL_SMTP_PASS" value="<?=htmlspecialchars($config['EMAIL_SMTP_PASS'])?>"></div>
            <div class="form-group"><label class="small text-muted">CIFRADO</label>
              <select class="form-control form-control-sm cfg-input" name="EMAIL_SMTP_CRYPTO">
                <option value="tls" <?=$config['EMAIL_SMTP_CRYPTO']==='tls'?'selected':''?>>TLS</option>
                <option value="ssl" <?=$config['EMAIL_SMTP_CRYPTO']==='ssl'?'selected':''?>>SSL</option>
              </select>
            </div>
          </div>

          <button class="btn btn-info btn-sm" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar Correo Pedidos</button>
        </div>
      </div>
    </div>

    <!-- Correo: Notificaciones del Sistema -->
    <div class="col-md-6">
      <div class="card card-outline card-secondary">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-bell"></i> Correo — Notificaciones del Sistema</h3></div>
        <div class="card-body">
          <div class="form-group">
            <label class="small text-muted">PROVEEDOR DE CORREO</label>
            <select class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_PROVIDER" onchange="toggleProveedorCorreo('NOTIF_EMAIL')">
              <option value="SMTP"     <?=$config['NOTIF_EMAIL_PROVIDER']==='SMTP'    ?'selected':''?>>SMTP</option>
              <option value="SENDGRID" <?=$config['NOTIF_EMAIL_PROVIDER']==='SENDGRID'?'selected':''?>>SendGrid (API)</option>
            </select>
          </div>
          <div class="form-group"><label class="small text-muted">CORREO REMITENTE</label><input type="email" class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_FROM" value="<?=htmlspecialchars($config['NOTIF_EMAIL_FROM'])?>" placeholder="notificaciones@tudominio.com"></div>
          <div class="form-group"><label class="small text-muted">NOMBRE REMITENTE</label><input type="text" class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_FROM_NAME" value="<?=htmlspecialchars($config['NOTIF_EMAIL_FROM_NAME'])?>" placeholder="Notificaciones FHB"></div>
          <div class="form-group"><label class="small text-muted">DESTINATARIOS (separados por coma)</label><input type="text" class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_TO" value="<?=htmlspecialchars($config['NOTIF_EMAIL_TO'])?>" placeholder="admin@tudominio.com, otro@correo.com"></div>

          <div class="sendgrid-fields-NOTIF_EMAIL">
            <div class="form-group"><label class="small text-muted">API KEY SENDGRID</label><input type="text" class="form-control form-control-sm cfg-input" name="NOTIF_SENDGRID_API_KEY" value="<?=htmlspecialchars($config['NOTIF_SENDGRID_API_KEY'])?>"></div>
          </div>

          <div class="smtp-fields-NOTIF_EMAIL">
            <div class="form-group"><label class="small text-muted">SERVIDOR SMTP</label><input type="text" class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_SMTP_HOST" value="<?=htmlspecialchars($config['NOTIF_EMAIL_SMTP_HOST'])?>" placeholder="smtp.gmail.com"></div>
            <div class="form-group"><label class="small text-muted">PUERTO</label><input type="text" class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_SMTP_PORT" value="<?=htmlspecialchars($config['NOTIF_EMAIL_SMTP_PORT'])?>" placeholder="587"></div>
            <div class="form-group"><label class="small text-muted">USUARIO SMTP</label><input type="text" class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_SMTP_USER" value="<?=htmlspecialchars($config['NOTIF_EMAIL_SMTP_USER'])?>"></div>
            <div class="form-group"><label class="small text-muted">CONTRASEÑA SMTP</label><input type="password" class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_SMTP_PASS" value="<?=htmlspecialchars($config['NOTIF_EMAIL_SMTP_PASS'])?>"></div>
            <div class="form-group"><label class="small text-muted">CIFRADO</label>
              <select class="form-control form-control-sm cfg-input" name="NOTIF_EMAIL_SMTP_CRYPTO">
                <option value="tls" <?=$config['NOTIF_EMAIL_SMTP_CRYPTO']==='tls'?'selected':''?>>TLS</option>
                <option value="ssl" <?=$config['NOTIF_EMAIL_SMTP_CRYPTO']==='ssl'?'selected':''?>>SSL</option>
              </select>
            </div>
          </div>

          <button class="btn btn-secondary btn-sm" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar Correo Notificaciones</button>
        </div>
      </div>
    </div>

    <!-- Orion Logistics -->
    <div class="col-md-6">
      <div class="card card-outline card-primary">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-truck"></i> Orion Logistics — Delivery</h3></div>
        <div class="card-body">
          <div class="form-group"><label class="small text-muted">URL DE LA API DE ORION</label><input type="text" class="form-control form-control-sm cfg-input" name="ORION_API_URL" value="<?=htmlspecialchars($config['ORION_API_URL'])?>" placeholder="http://localhost:3000"></div>
          <div class="form-group"><label class="small text-muted">API KEY (POS_API_KEY de Orion)</label><input type="text" class="form-control form-control-sm cfg-input" name="ORION_API_KEY" value="<?=htmlspecialchars($config['ORION_API_KEY'])?>"></div>
          <small class="text-muted">Si Orion se traslada a otro servidor, actualiza la URL aquí. Los pedidos a domicilio marcados como "Listo" se envían automáticamente a Orion para asignar conductor.</small>
          <button class="btn btn-primary btn-sm mt-3" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar Orion</button>
        </div>
      </div>
    </div>

    <!-- Google Maps -->
    <div class="col-md-6">
      <div class="card card-outline card-success">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Google Maps — Mapa y Zonas de Delivery</h3></div>
        <div class="card-body">
          <div class="form-group"><label class="small text-muted">API KEY DE GOOGLE MAPS</label><input type="text" class="form-control form-control-sm cfg-input" name="GOOGLE_MAPS_API_KEY" value="<?=htmlspecialchars($config['GOOGLE_MAPS_API_KEY'])?>" placeholder="AIza..."></div>
          <small class="text-muted">Habilita el mapa interactivo en el checkout y la administración de Zonas de Delivery. Obtenla en <a href="https://console.cloud.google.com/google/maps-apis" target="_blank">Google Cloud Console</a> habilitando "Maps JavaScript API" y "Places API".</small>
          <hr>
          <div class="form-row">
            <div class="form-group col-6"><label class="small text-muted">LATITUD DEL RESTAURANTE</label><input type="text" class="form-control form-control-sm cfg-input" name="RESTAURANTE_LAT" value="<?=htmlspecialchars($config['RESTAURANTE_LAT'])?>" placeholder="13.6929"></div>
            <div class="form-group col-6"><label class="small text-muted">LONGITUD DEL RESTAURANTE</label><input type="text" class="form-control form-control-sm cfg-input" name="RESTAURANTE_LNG" value="<?=htmlspecialchars($config['RESTAURANTE_LNG'])?>" placeholder="-89.2182"></div>
          </div>
          <small class="text-muted">Coordenadas del local, usadas para el mapa fijo del panel principal del Touch.</small>
          <button class="btn btn-success btn-sm mt-3" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar Google Maps</button>
        </div>
      </div>
    </div>

  </div>

  <div class="row"><div class="col-12">
    <div class="card card-outline card-warning">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-star"></i> Reseñas — QR en ticket de cobro</h3></div>
      <div class="card-body">
        <p class="text-muted small">Las URLs que ingreses aparecerán como código QR al final del ticket de cobro. El cliente las escanea y va directo a dejar su reseña. Deja en blanco las que no uses.</p>
        <div class="form-row">
          <div class="form-group col-md-4"><label class="small text-muted"><i class="fab fa-google"></i> URL de reseñas en Google</label><input type="text" class="form-control form-control-sm cfg-input" name="RESENIA_GOOGLE_URL" value="<?=htmlspecialchars($config['RESENIA_GOOGLE_URL'] ?? '')?>" placeholder="https://g.page/r/..."></div>
          <div class="form-group col-md-4"><label class="small text-muted"><i class="fab fa-facebook"></i> URL de reseñas en Facebook</label><input type="text" class="form-control form-control-sm cfg-input" name="RESENIA_FACEBOOK_URL" value="<?=htmlspecialchars($config['RESENIA_FACEBOOK_URL'] ?? '')?>" placeholder="https://www.facebook.com/..."></div>
          <div class="form-group col-md-4"><label class="small text-muted"><i class="fab fa-instagram"></i> URL de perfil en Instagram</label><input type="text" class="form-control form-control-sm cfg-input" name="RESENIA_INSTAGRAM_URL" value="<?=htmlspecialchars($config['RESENIA_INSTAGRAM_URL'] ?? '')?>" placeholder="https://www.instagram.com/..."></div>
        </div>
        <button class="btn btn-warning btn-sm" onclick="guardarConfig()"><i class="fas fa-save"></i> Guardar URLs de Reseñas</button>
      </div>
    </div>
  </div></div>

  </div></div></section>
</div>
<input type="hidden" id="csrf_token_id" value="<?=$this->security->get_csrf_hash()?>">
<script>
var url=window.location.origin+'/';
function toggleProveedorCorreo(prefijo){
  var proveedor=$('select[name="'+prefijo+'_PROVIDER"]').val();
  if(proveedor==='SENDGRID'){
    $('.sendgrid-fields-'+prefijo).show();
    $('.smtp-fields-'+prefijo).hide();
  } else {
    $('.sendgrid-fields-'+prefijo).hide();
    $('.smtp-fields-'+prefijo).show();
  }
}
$(function(){
  toggleProveedorCorreo('EMAIL');
  toggleProveedorCorreo('NOTIF_EMAIL');
});
function guardarConfig(){
  var d={csrf_token_id:$('#csrf_token_id').val()};
  $('.cfg-input').each(function(){ d[$(this).attr('name')]=$(this).val(); });
  $.post(url+'AdminOnline/guardarConfig',d,function(r){
    if(r.codigo==200){ toastr?toastr.success(r.mensaje):alert(r.mensaje); }else{ alert('Error: '+r.mensaje); }
  },'json');
}
function subirQR(tipo,inputId,prevId){
  var f=document.getElementById(inputId);
  if(!f.files||!f.files[0]){alert('Selecciona una imagen.');return;}
  var fd=new FormData(); fd.append('tipo',tipo); fd.append('archivo_qr',f.files[0]); fd.append('csrf_token_id',$('#csrf_token_id').val());
  $.ajax({type:'POST',url:url+'AdminOnline/subirQR',data:fd,processData:false,contentType:false,dataType:'json',
    success:function(r){
      if(r.codigo==200){
        var p=document.getElementById(prevId);
        if(p.tagName==='IMG'){p.src=r.ruta;}
        else{var i=document.createElement('img');i.src=r.ruta;i.id=prevId;i.style.cssText='width:100px;height:100px;object-fit:contain;border:1px solid #dee2e6;border-radius:8px';p.parentNode.replaceChild(i,p);}
        toastr?toastr.success(r.mensaje):alert(r.mensaje);
      }else{alert('Error: '+r.mensaje);}
    }
  });
}
</script>
