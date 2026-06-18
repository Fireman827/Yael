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
	<!-- <form id="FrmApertura" autocomplete="off"> -->
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="Impresora">Impresora: <span class="text-danger">*</span></label>
						<select class="select2 form-control col-12" name="impresora" id="impresora">
							<!-- <option value="">Seleccione</option> -->
						<?php if($impresoras !== 0):?>
							<?php foreach($impresoras as $impresora):?>
								<option value="<?=md5($impresora->idImpresora)?>"><?=$impresora->nombreImpresora?></option>
							<?php endforeach;?>
						<?php endif;?>
						</select>
					</div>
				</div>
			</div>
</div>
<div class="modal-footer">
	<input type="hidden" id="idCorteHistorial" value="<?=$idCorteHistorial?>">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="printCorte">Imprimir</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->
