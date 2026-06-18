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
					<label for="nombreZona">Nombre de Zona: <span class="text-danger">*</span></label>
					<input type="text" name="nombreZona" id="nombreZona" class="form-control upper" placeholder="Nombre de Zona">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="capacidadZona">Capacidad: <span class="text-danger">*</span></label>
					<input type="text" name="capacidadZona" id="capacidadZona" class="form-control numeric" placeholder="Capacidad">
				</div>
			</div>
		</div>
		<div class="row" hidden>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="tipoAumentoZona">Tipo de Aumento: <span class="text-danger">*</span></label>
					<select class="form-control select2" name="tipoAumentoZona" id="tipoAumentoZona">
						<option value="Ninguno" selected>Ninguno</option>
						<option value="Monto">Monto</option>
						<option value="Porcentaje">Porcentaje</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row" hidden>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="aumentoZona">Valor del Aumento:</label>
					<input type="text" class="form-control decimal" name="aumentoZona" id="aumentoZona" placeholder="Valor de Aumento">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
						<input type="checkbox" id="visibleZona" name="visibleZona">
						<label for="visibleZona">Visible</label>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" formulario="FrmZonas" id="btnGuardar">Guardar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->