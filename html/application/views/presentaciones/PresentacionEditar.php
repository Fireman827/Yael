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
	<form id="FrmPresentaciones" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombrePresentacion">Nombre: <span class="text-danger">*</span></label>
					<input type="text" name="nombrePresentacion" id="nombrePresentacion" class="form-control upper text-uppercase" placeholder="Nombre de la presentación" value="<?=$datosPresentaciones->nombrePresentacion; ?>" >
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="unidadPresentacion">Unidad: <span class="text-danger">*</span></label>
					<input type="text" name="unidadPresentacion" id="unidadPresentacion" class="form-control upper text-uppercase" placeholder="Unidad de la presentación" value="<?=$datosPresentaciones->unidadPresentacion; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="descripcionPresentacion">Descripción:</label>
					<input type="text" name="descripcionPresentacion" id="descripcionPresentacion" class="form-control upper text-uppercase" placeholder="Descripción de la presentación" value="<?=$datosPresentaciones->descripcionPresentacion; ?>">
				</div>
			</div>
		</div>
        <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idPresentacion" id="idPresentacion" value="<?=$idPresentacion; ?>">
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