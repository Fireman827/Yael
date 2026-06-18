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
	<form id="FrmClientesCategoria" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreClienteCategoria">Nombre: <span class="text-danger">*</span></label>
					<input type="text" name="nombreClienteCategoria" id="nombreClienteCategoria" class="form-control upper text-uppercase" placeholder="Nombre de la Categoría" value="<?=$datosClientesCategoria->nombreClienteCategoria; ?>" >
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="descripcionClienteCategoria">Descripción: <span class="text-danger">*</span></label>
					<input type="text" name="descripcionClienteCategoria" id="descripcionClienteCategoria" class="form-control upper text-uppercase" placeholder="Descripción de la Categoría" value="<?=$datosClientesCategoria->descripcionClienteCategoria; ?>">
				</div>
			</div>
		</div>
        <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idClienteCategoria" id="idClienteCategoria" value="<?=$idClienteCategoria; ?>">
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