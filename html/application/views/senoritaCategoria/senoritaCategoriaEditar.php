<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<form id="FrmSenoritaCategoria" autocomplete="off">
  <div class="modal-header">
    <span class="modal-title"> <i class="<?= $icono ?>"></i> <?= $titulo ?></span>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true"><i class="fa fa-times"></i></span>
    </button>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
        <div class="form-group">
          <label for="categoriaSenorita">Nombre Categoria</label>
          <input type="text" class="form-control upper" id="categoriaSenorita" name="categoriaSenorita" value="<?php echo $senoritaCategoria->nombreSenoritaCategoria; ?>">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="form-group">
          <label for="nombreZona">Tipo Comision: <span class="text-danger">*</span></label>
          <select name="tipoComision" id="tipoComision" class="form-control select2">
            <option value="Monto" <?php echo ($senoritaCategoria->tipoComisionSenoritaCategoria) == 'Monto' ? 'selected' : '' ?> >Monto Fijo</option>
            <option value="Procentaje" <?php echo ($senoritaCategoria->tipoComisionSenoritaCategoria) == 'Porcentaje' ? 'selected' : '' ?> >Porcentaje</option>
          </select>
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="categoriaSenorita">Cantidad</label>
          <input type="text" class="form-control decimal" id="cantidadComision" name="cantidadComision" value="<?php echo $senoritaCategoria->comisionSenoritaCategoria; ?>">
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
      <input type="hidden" value="<?php echo md5($senoritaCategoria->idSenoritaCategoria); ?>" id="idSenoritaCategoria" name="idSenoritaCategoria">
    <button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Guardar</button>
    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
  </div>
  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
  <?php if (isset($proceso)) { ?>
    <input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
  <?php } ?>
</form>
<!-- /.modal-content -->