<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?=$icono ?>"></i> <?=$titulo ?></span>
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
					<input type="text" name="desdeTramoRenta" id="desdeTramoRenta" class="form-control decimal" placeholder="Desde" value="<?=$datosTramosRenta->desdeTramoRenta; ?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="hastaTramoRenta">Hasta: <span class="text-danger">*</span></label>
					<input type="text" name="hastaTramoRenta" id="hastaTramoRenta" class="form-control decimal" placeholder="Hasta" value="<?=$datosTramosRenta->hastaTramoRenta; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="porcentajeTramoRenta">Porcentaje: <span class="text-danger">*</span></label>
					<input type="text" name="porcentajeTramoRenta" id="porcentajeTramoRenta" class="form-control porcentaje" placeholder="Ingrese porcentaje" value="<?=$datosTramosRenta->porcentajeTramoRenta; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="excesoTramoRenta">Aplicable desde: <span class="text-danger">*</span></label>
					<input type="text" name="excesoTramoRenta" id="excesoTramoRenta" class="form-control decimal" placeholder="Aplicable desde" value="<?=$datosTramosRenta->excesoTramoRenta; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="cuotaTramoRenta">Cuota fija: <span class="text-danger">*</span></label>
					<input type="text" name="cuotaTramoRenta" id="cuotaTramoRenta" class="form-control decimal" placeholder="Cuota fija" value="<?=$datosTramosRenta->cuotaTramoRenta; ?>">
				</div>
			</div>
		</div>
        <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idTramoRenta" id="idTramoRenta" value="<?=$idTramoRenta; ?>">
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