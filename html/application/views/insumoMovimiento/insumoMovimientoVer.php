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
    <?php foreach ($datos as $datos) : ?>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Categoria</label>
                    <p><?= $datos->categoriaInsumoMovimiento ?></p>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Tipo</label>
                    <p><?= $datos->tipoMovimientoInsumo ?></p>
                </div>
            </div>
            <?php
            $fecha =  explode(" ", $datos->fechaHoraInsumoMovimiento);
            list($fecha, $hora) = $fecha;
            $fecha = date_format(date_create($fecha), "d-m-Y");
            $hora = date_format(date_create($hora), "h:i:s A");
            ?>
            <div class="col-2">
                <div class="form-group">
                    <label>Fecha Movimiento</label>
                    <p><?= $fecha ?></p>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Hora Movimiento</label>
                    <p><?= $hora ?></p>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Descripcion</label>
                    <p><?= $datos->descripcionInsumoMovimiento ?></p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if ($datos->nombreProveedor != '') : ?>
                <div class="col-2">
                    <div class="form-group">
                        <label>Proveedor: </label>
                        <p><?= $datos->nombreProveedor ?></p>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Tipo Documento: </label>
                        <p><?= $datos->tipoDocumentoInsumoMovimiento ?></p>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label>Numero Documento: </label>
                        <p><?= $datos->numeroDocumentoInsumoMovimiento ?></p>
                    </div>
                </div>
            <?php endif; ?>
            </div>
    <?php endforeach; ?>
    <div class="row">
        <table class="table table-sm table-condensed table-striped">
            <thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
                <tr>
                    <th>Producto</th>
                    <th>Presentacion</th>
                    <th>Cantidad</th>
                    <th>Precio Compra (Unidad)</th>
                    <th>Precio Venta (Unidad)</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($datosDetalle as $dd):?>
                    <tr>
                        <td><?=$dd->nombreInsumo?></td>
                        <td><?=$dd->nombrePresentacion." (".$dd->unidadPresentacion.")"?></td>
                        <td><?=$dd->cantidad?></td>
                        <td>$<?=$dd->costoInsumoMovimientoDetalle?></td>
                        <td>$<?=$dd->precioInsumoMovimientoDetalle?></td>
                        <td>$<?=number_format(($dd->cantidad * $dd->costoInsumoMovimientoDetalle),2)?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-11">
            <div class="form-group">
                <h5 class="float-right">Total: $<?= $datos->totalInsumoMovimiento ?></h5>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
    <input type="hidden" value="<?= $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->