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
				<label for="nombreZona">Cliente:</label>
        <select name="idClienteParqueo" id="idClienteParqueo" class="form-control select2">
          <option value="">Sin asignar</option>
          <?php if ($clientes !== false) : ?>
            <?php foreach ($clientes as $cliente) : ?>
              <option value="<?= $cliente->idCliente ?>" <?php if($datos->idClienteParqueo == $cliente->idCliente){ echo "selected";} ?>><?= $cliente->nombreCliente; ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
			</div>
		</div>
    <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="codigoMembrecia">Palca</label>
        <input type="text" class="form-control" id="placaParqueo" name="placaParqueo" value="<?= $datos->placaParqueo; ?>">
      </div>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="codigoMembrecia">Hora</label>
        <input type="time" class="form-control" id="horaParqueo" name="horaParqueo" value="<?= $datos->horaEntradaParqueo; ?>">
      </div>
    </div>
	</div>
</div>
<div class="modal-footer">
  <input type="hidden" name="proceso" id="proceso" value="Editar">
  <input type="hidden" name="idParqueo" id="idParqueo" value="<?= md5($datos->idParqueo);?>">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Guardar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->
