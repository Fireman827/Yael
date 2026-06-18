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
	<?php
	$error=urldecode($factura->error);
	$array = json_decode($error, true);
	if(!empty($array)){
	?>
	<div class="alert alert-primary" role="alert">
		<?=$array['descripcionMsg'] ?>
	</div>
	<?php if(!empty($array["observaciones"])){ foreach ($array['observaciones'] as $fila): ?>
		<div class="alert alert-danger" role="alert">
			<?=$fila ?>
		</div>
	<?php endforeach; } } ?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-danger VentasRetransmitir" aliasDocumento='<?=$factura->tipoDocumentoFactura ?>' idVenta='<?=$factura->idFactura ?>'>Retransmitir</button>
	<button type="button" class="btn btn-sm btn-default btnclose" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php }
?>
<!-- /.modal-content -->
<script type="text/javascript">
