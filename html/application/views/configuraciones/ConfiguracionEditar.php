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
	<form id="FrmConfiguraciones" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="parametroConfiguracion">Parametro: <span class="text-danger">*</span></label>
					<input type="text" name="parametroConfiguracion" id="parametroConfiguracion" class="form-control" placeholder="Parametro" value="<?=$datosConfiguraciones->parametroConfiguracion; ?>" >
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="valorConfiguracion">Valor: <span class="text-danger">*</span></label>
					<input type="text" name="valorConfiguracion" id="valorConfiguracion" class="form-control" placeholder="Valor" value="<?=$datosConfiguraciones->valorConfiguracion; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="comentarioConfiguracion">Descripción: <span class="text-danger">*</span></label>
					<input type="text" name="comentarioConfiguracion" id="comentarioConfiguracion" class="form-control" placeholder="Comentario" value="<?=$datosConfiguraciones->comentarioConfiguracion; ?>">
				</div>
			</div>
		</div>
        <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idConfiguracion" id="idConfiguracion" value="<?=$idConfiguracion; ?>">
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