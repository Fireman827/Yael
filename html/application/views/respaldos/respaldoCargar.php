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
	<form id="FrmRespaldos" autocomplete="off">
		<div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="fechaRespaldo">Fecha: <?=date("d-m-Y")?></label>
					<input type="hidden" name="fechaRespaldo" id="fechaRespaldo" value="<?=date("Y-m-d")?>">
				</div>
			</div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="horaRespaldo">Hora: <?=date("h:i:s A")?></label>
					<input type="hidden" name="horaRespaldo" id="horaRespaldo" value="<?=date("H:i:s")?>">
				</div>
			</div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <label for="archivoRespaldo">Archivo:</label>
                <input type="file" class="dropify" data-allowed-file-extensions="zip" data-height="170"  name="archivoRespaldo" id="archivoRespaldo">
            </div>
		</div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Cargar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->