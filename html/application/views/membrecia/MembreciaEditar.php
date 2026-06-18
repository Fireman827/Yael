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
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group">
				<label for="nombreZona">Cliente: <span class="text-danger">*</span></label>
        <select name="idClienteMembrecia" id="idClienteMembrecia" class="form-control select2"  disabled="disabled">
          <option value="">Sin asignar</option>
          <?php if ($clientes !== false) : ?>
            <?php foreach ($clientes as $cliente) : ?>
              <option value="<?= $cliente->idCliente ?>" <?php if($datos->idClienteMembrecia == $cliente->idCliente){ echo "selected"; } ?>><?= $cliente->nombreCliente; ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
			</div>
		</div>
    <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="codigoMembrecia">Codigo</label>
        <input type="text" class="form-control" id="codigoMembrecia" name="codigoMembrecia" value="<?= $datos->codigoMembrecia; ?>" readonly>
      </div>
    </div>
	</div>
</div>
<div class="modal-footer">
  <input type="hidden" name="proceso" id="proceso" value="Editar">
  <input type="hidden" name="idMembrecia" id="idMembrecia" value="<?= md5($datos->idMembrecia);?>">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Editar</button>
	<button type="button" class="btn btn-sm btn-danger" id="btnGenerar">Nuevo Codigo</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->
