<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?=$icono; ?>"></i> <?=$titulo; ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">
	<form id="FrmTramosRenta" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="desdeTramoRenta">Desde: <span class="text-danger">*</span></label>
					<input type="date" name="desdeTramoRenta" id="desdeTramoRenta" class="form-control" placeholder="Desde">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="hastaTramoRenta">Hasta: <span class="text-danger">*</span></label>
					<input type="date" name="hastaTramoRenta" id="hastaTramoRenta" class="form-control" placeholder="Hasta">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="porcentajeTramoRenta">Porcentaje: <span class="text-danger">*</span></label>
					<input type="text" name="porcentajeTramoRenta" id="porcentajeTramoRenta" class="form-control" placeholder="Ingrese porcentaje">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="excesoTramoRenta">Aplicable desde: <span class="text-danger">*</span></label>
					<input type="text" name="excesoTramoRenta" id="excesoTramoRenta" class="form-control" placeholder="Aplicable desde">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="cuotaTramoRenta">Cuota fija: <span class="text-danger">*</span></label>
					<input type="text" name="cuotaTramoRenta" id="cuotaTramoRenta" class="form-control" placeholder="Cuota fija">
				</div>
			</div>
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Guardar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->