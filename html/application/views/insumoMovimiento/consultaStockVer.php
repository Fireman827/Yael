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
    <?php foreach ($datos as $datos) : 
        $stock = $datos->cantidadInsumoStock    ;
    ?>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Nombre Insumo</label>
                    <p><?= $datos->nombreInsumo ?></p>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Categoria Insumo</label>
                    <p><?= $datos->nombreInsumoCategoria ?></p>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Descripcion Insumo</label>
                    <p><?= $datos->descripcionInsumo ?></p>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Marca</label>
                    <p><?= $datos->marcaInsumo ?></p>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Stock Minimo</label>
                    <p><?= $datos->stockMinimoInsumo ?></p>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Stock Actual</label>
                    <p><?= number_format($datos->cantidadInsumoStock / $datos->unidadInsumoPresentacion,2) ?></p>
                </div>
            </div>
        </div>
        
    <?php  endforeach; ?>
    <div class="row">
       <div class="col-5">
        <table class="table table-sm table-condensed table-striped">
                <thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
                    <tr class="text-center">
                        <th colspan="4">Existencia Por Presentacion</th>
                    </tr>
                    <tr>
                        <th>Presentacion</th>
                        <th>Unidad</th>
                        <th>P. Compra (U)</th>
                        <th>Existencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $signo = ($stock >= 0) ? "" : "-"; 
                        foreach($datosDetalle as $dd):
                            $unidad = $dd->unidadInsumoPresentacion;
                            if($unidad == "1"){
                                $existencia = (abs($stock) / $unidad);
                            }
                            else{
                                $existencia = floor(abs($stock) / $unidad);
                            }
                        ?>
                        <tr>
                            <td><?=$dd->nombrePresentacion." (".$dd->unidadPresentacion.")"?></td>
                            <td>1X<?=$unidad?></td>
                            <td>$<?=$dd->costoInsumoPresentacion?></td>
                            <td><?=$signo.number_format($existencia,2)?></td>
                        </tr>
                    <?php 
                            $stock = abs($stock) - (($existencia) * $unidad);
                        endforeach;?>
                </tbody>
            </table>
       </div>
       <div class="col-7">
        <table class="table table-sm table-condensed table-striped">
                <thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
                    <tr class="text-center">
                        <th colspan="4">Insumo por Producto</th>
                    </tr>    
                    <tr>
                        <th>Producto</th>
                        <th>Presentacion</th>
                        <th>Cantidad</th>
                        <th>Utilizado</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    if($datosDetalleProd){
                        foreach($datosDetalleProd as $ddp):
                            $unidad = $ddp->unidadPresentacion;
                        ?>
                        <tr>
                            <td><?=$ddp->nombreProducto?></td>
                            <td><?=$ddp->nombrePresentacion." (".$ddp->unidadPresentacion.")"?></td>
                            <td><?=$ddp->cantidadProductoInsumo?></td>
                            <td></td>
                        </tr>
                    <?php 
                        endforeach;
                    }else{?>
                        <tr>
                            <td colspan="4" class="text-center">No hay Productos con este Insumo Asignado</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
       </div>
    </div>
    <div class="row">
        <div class="col-11">
            <div class="form-group">
                <!-- <h5 class="float-right">Total: $<?= $datos->totalInsumoMovimiento ?></h5> -->
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