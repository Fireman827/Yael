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
       <div class="col-12">
        <div class="form-group">
            <label for="">Presentacion</label>
            <select name="presentacionAjuste" id="presentacionAjuste" class="select2 form-control col-12">
                <?php if($presentaciones):?>
                    <option value=""></option>
                    <?php foreach($presentaciones as $pre):?>
                        <option value="<?=$pre->idPresentacion?>" unidad="<?=$pre->unidadInsumoPresentacion?>" <?=($pre->unidadInventarioInsumoPresentacion == "1") ? "selected": ""; ?> ><?=$pre->nombrePresentacion?> (<?=$pre->unidadPresentacion?>)</option>
                    <?php endforeach;?>
                <?php endif;?>
            </select>
        </div>
       </div>
    </div>
    <div class="row">
       <div class="col-12">
        <div class="form-group">
            <label for="">Cantidad</label>
            <input type="text" id="cantidadAjustar" class="form-control decimal" placeholder="Cantidad">
        </div>
       </div>
    </div>
    <div class="row">
       <div class="col-12">
        <div class="form-group">
            <label for="">Costo</label>
            <input type="text" id="costoAjustar" class="form-control decimal" placeholder="Costo" value="<?=$datos->costoPromedioInsumo?>">
        </div>
       </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="hidden" name="idInsumo" id="idInsumo"  value="<?=$idInsumo?>">
            <input type="hidden" name="stock" id="stock"  value="<?=$idInsumo?>">
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" id="btnAjustar" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>">Ajustar</button>
    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
    <input type="hidden" value="<?= $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->