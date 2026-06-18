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
  <?php if($idCorteCaja != 0){ ?>
	<form id="FrmMovimientoIngreso" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="entregaMovimiento">Entrega: <span class="text-danger">*</span></label>
					<input type="text" name="entregaMovimiento" id="entregaMovimiento" class="form-control upper text-uppercase" placeholder="Entrega">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="recibeMovimiento">Recibe: <span class="text-danger">*</span></label>
					<input type="text" name="recibeMovimiento" id="recibeMovimiento" class="form-control upper text-uppercase" placeholder="Recibe">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreImpresora">Monto: <span class="text-danger">*</span></label>
					<input type="text" name="montoIngreso" id="montoIngreso" class="form-control upper text-uppercase decimal" placeholder="Monto del ingreso">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="recursoCompartidoImpresora">Concepto: <span class="text-danger">*</span></label>
					<textarea name="conceptoIngreso" id="conceptoIngreso" class="form-control upper text-uppercase"></textarea>
				</div>
			</div>
		</div>
    <input type="hidden" value="<?=$idCorteCaja; ?>" id="idCorteCaja" name="idCorteCaja">
  	<input type="hidden" value="<?=$idCaja; ?>" id="idCaja" name="idCaja">
  	<input type="hidden" value="<?=$idTurnoVigente; ?>" id="idTurnoVigente" name="idTurnoVigente">
	</form>
<?php } else { ?>
  <div></div><br><br><div class='alert alert-warning text-center'>No se ha encontrado una apertura vigente.</div>
<?php } ?>
</div>
<?php if($idCorteCaja != 0){ ?>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Guardar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<?php } ?>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->
