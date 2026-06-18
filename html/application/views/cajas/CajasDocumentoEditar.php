<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<span class="modal-title">  <i class="<?=$icono; ?>"></i> <?=$titulo; ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">
	<form id="FrmCajaDocumento" autocomplete="off">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreCajaDocumento">Seleccione el tipo de documento: <span class="text-danger">*</span></label>
					<?=$tiposDocumentos; ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="inicioCajaDocumento">Inicio: <span class="text-danger">*</span></label>
					<input type="text" name="inicioCajaDocumento" id="inicioCajaDocumento" class="form-control" placeholder="" value="<?=$datosCajaDocumento->inicioCajaDocumento; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="finalCajaDocumento">Final: <span class="text-danger">*</span></label>
					<input type="text" name="finalCajaDocumento" id="finalCajaDocumento" class="form-control" placeholder="" value="<?=$datosCajaDocumento->finalCajaDocumento; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="actualCajaDocumento">Inicio: <span class="text-danger">*</span></label>
					<input type="text" name="actualCajaDocumento" id="actualCajaDocumento" class="form-control" placeholder="" value="<?=$datosCajaDocumento->actualCajaDocumento; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="fechaAutorizacionCajaDocumento">Fecha de Autorización: <span class="text-danger">*</span></label>
					<input type="text" name="fechaAutorizacionCajaDocumento" id="fechaAutorizacionCajaDocumento" class="form-control" placeholder="" value="<?=$datosCajaDocumento->fechaAutorizacionCajaDocumento; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="fechaResolucionCajaDocumento">Inicio: <span class="text-danger">*</span></label>
					<input type="text" name="fechaResolucionCajaDocumento" id="fechaResolucionCajaDocumento" class="form-control" placeholder="" value="<?=$datosCajaDocumento->fechaResolucionCajaDocumento; ?>">
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="numeroResolucionCajaDocumento">Inicio: <span class="text-danger">*</span></label>
					<input type="text" name="numeroResolucionCajaDocumento" id="numeroResolucionCajaDocumento" class="form-control" placeholder="" value="<?=$datosCajaDocumento->numeroResolucionCajaDocumento; ?>">
				</div>
			</div>
		</div>
        <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idCajaDocumento" id="idCajaDocumento" value="<?=$idCajaDocumento; ?>">
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