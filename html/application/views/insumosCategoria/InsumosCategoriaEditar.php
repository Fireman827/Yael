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
	<form id="FrmInsumosCategoria" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreInsumosCategoria">Nombre: <span class="text-danger">*</span></label>
					<input type="text" name="nombreInsumosCategoria" id="nombreInsumosCategoria" class="form-control upper text-uppercase" placeholder="Nombre de la categoria" value="<?= $datosInsumosCategoria->nombreInsumoCategoria; ?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="descripcionInsumosCategoria">Descripcion: <span class="text-danger">*</span></label>
					<input type="text" name="descripcionInsumosCategoria" id="descripcionInsumosCategoria" class="form-control upper text-uppercase" placeholder="Descripcion de la Categoria" value="<?= $datosInsumosCategoria->descripcionInsumoCategoria; ?>">
				</div>
			</div>
		</div>
		<div class="row" hidden>
			<div class="col">
				<input type="hidden" name="idInsumoCategoria" id="idInsumoCategoria" value="<?= $idInsumoCategoria; ?>">
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
	<input type="hidden" value="<?= $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->