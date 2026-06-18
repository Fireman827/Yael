<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?= $icono ?>"></i> <?= $titulo ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">
	<form id="FrmZonas" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreZona" id="nombreZonaMesaLabel">Nombre de Mesa: <?=($mesas+1)?></label>
					<input type="hidden" name="nombreZonaMesa" id="nombreZonaMesa" class="form-control numeric"  value="<?=($mesas+1)?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="capacidadZona">Capacidad: <span class="text-danger">*</span></label>
					<input type="text" name="capacidadZonaMesa" id="capacidadZonaMesa" class="form-control numeric" placeholder="Capacidad">
					<input type="hidden" name="idZona" id="idZona" value="<?= $idZona ?>">
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardarDuplicar">Guardar y Duplicar</button>
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardarSalir">Guardar y Salir</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->
