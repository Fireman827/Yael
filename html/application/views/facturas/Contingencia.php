<?php
defined('BASEPATH') or exit('No direct script access allowed');
$a = uniqid();
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?=$icono; ?>"></i> <?=$titulo; ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-lg-12">
			<label>Tipo de contingencia</label>
			<select class="form-control" id='tipoContingencia' name="">
				<?php
				foreach ($contingencia as $contin) {
					?>
					<option value="<?=$contin->codigo?>" <?php if ($factura->tipoContingencia == $contin->codigo): echo " selected "; ?>

					<?php endif; ?>><?=$contin->valores?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div class="col-lg-12">
			<label>Motivo</label>
			<input class='form-control' type="text" id="motivoContingencia" name="" value="<?=$factura->motivoContingencia?>">
		</div>
	</div>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-danger VentasContingencia" aliasDocumento='<?=$factura->tipoDocumentoFactura ?>' idVenta='<?=$factura->idFactura ?>'>Enviar Contingencia</button>
	<button type="button" class="btn btn-sm btn-default btnclose" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php }
?>
<!-- /.modal-content -->
<script type="text/javascript">
$(document).ready()
{
	$("#tipoContingencia").select2({
		theme: 'bootstrap4',
		placeholder : 'Seleccione una opcion',
		 dropdownParent: $('#dfModal')
	 });
}
</script>
