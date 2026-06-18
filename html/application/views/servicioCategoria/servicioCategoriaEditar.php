<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<form id="FrmServicioCategoria" autocomplete="off">
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
          <label for="categoriaServicio">Nombre Categoria: <span class="text-danger">*</span> </label>
          <input type="text" class="form-control upper" id="nombreServicioCategoria" name="nombreServicioCategoria" value="<?=$servicioCategoria->nombreServicioCategoria ?>">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="form-group">
          <label for="descripcionServicioCategoria">Descripcion:<span class="text-danger">*</span></label>
          <textarea class="form-control upper" name="descripcionServicioCategoria" id="descripcionServicioCategoria" rows="3"><?=$servicioCategoria->descripcionServicioCategoria ?></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <input type="hidden" value="<?=$servicioCategoria->idServicioCategoria ?>" id="idServicioCategoria" name="idServicioCategoria">
    <button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Guardar</button>
    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
  </div>
  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
  <?php if (isset($proceso)) { ?>
    <input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
  <?php } ?>
</form>
<!-- /.modal-content -->