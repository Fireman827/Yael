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
	<form id="FrmImpresoras" autocomplete="off">
		<div class="row">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<label for="nombreImpresora">Nombre: <span class="text-danger">*</span></label>
					<input type="text" name="nombreImpresora" id="nombreImpresora" class="form-control upper text-uppercase" placeholder="Nombre de la impresora" value="<?=$datosImpresoras->nombreImpresora; ?>" >
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<label for="recursoCompartidoImpresora">Rec Comp: <span class="text-danger">*</span></label>
					<input type="text" name="recursoCompartidoImpresora" id="recursoCompartidoImpresora" class="form-control" placeholder="Nombre como recurso compartido" value="<?=$datosImpresoras->recursoCompartidoImpresora; ?>">
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<label for="IpImpresora">IP: <span class="text-danger">*</span></label>
					<input type="text" name="IpImpresora" id="IpImpresora" class="form-control ip" placeholder="IP de la impresora" value="<?=$datosImpresoras->IpImpresora?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="tipoImpresora">Tipo Impresora: <span class="text-danger">*</span></label>
					<select class="form-control" name="tipoImpresora" id="tipoImpresora">
						<option value="LIN" <?php echo ($datosImpresoras->tipoImpresora == "LIN") ? "selected" : "";?> >Linux</option>
						<option value="WIN" <?php echo ($datosImpresoras->tipoImpresora == "WIN") ? "selected" : "";?> >Windows</option>
						<option value="IP" <?php echo ($datosImpresoras->tipoImpresora == "IP") ? "selected" : "";?> >Direccion IP</option>
					</select>
				</div>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="servidorImpresora">Servidor de Impresion: <span class="text-danger">*</span></label>
					<input type="text" name="servidorImpresora" id="servidorImpresora" class="form-control" placeholder="Servidor de impresion" value="<?=$datosImpresoras->servidorImpresora?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
						<input type="checkbox" id="cobroImpresora" name="cobroImpresora" <?php echo ($datosImpresoras->cobroImpresora == 1) ? 'checked' : ''; ?> >
						<label for="cobroImpresora">Para Cobro</label>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
						<input type="checkbox" id="cocinaImpresora" name="cocinaImpresora" <?php echo ($datosImpresoras->cocinaImpresora == 1) ? 'checked' : ''; ?> >
						<label for="cocinaImpresora">Para Cocina</label>
					</div>
				</div>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<div class="form-group">
					<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
						<input type="checkbox" id="cuentaImpresora" name="cuentaImpresora" <?php echo ($datosImpresoras->cuentaImpresora == 1) ? 'checked' : ''; ?> >
						<label for="cuentaImpresora">Para Cuentas</label>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
		</div>
        <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idImpresora" id="idImpresora" value="<?=$idImpresora; ?>">
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