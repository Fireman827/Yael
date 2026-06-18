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
    <form id="FrmZonas" autocomplete="off">
        <?php if ($proceso == 'ZonasMesasTrasladar') : ?>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="zonaDestino">Zona de Destino:</label>
                        <select class="form-control select2" name="zonaDestino" id="zonaDestino">
                            <?php foreach ($zonas as $zona) : ?>
                                <option value="<?= $zona->idZona ?>"><?= $zona->nombreZona . " (" . $zona->ocupadoZona . " de " . $zona->capacidadZona . ")" ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <div class="table-responsive sticky-table sticky-headers" style="height: 270px;">
                    <table id="tablaEditarMesa" class="table table-sm table-hover">
                        <thead>
                            <tr class='sticky-header'>
                                <?php if ($proceso == 'ZonasMesasTrasladar') : ?>
                                    <th class="col-3" style='text-align: center;'>Mesa</th>
                                    <th class="col-3" style='text-align: center;'>Nuevo Nombre</th>
                                    <th class="col-3" style='text-align: center;'>Capacidad</th>
                                    <th class="col-3" style='text-align: center;'>Accion</th>
                                <?php endif; ?>
                                <?php if ($proceso != 'ZonasMesasTrasladar') : ?>
                                    <th class="col-3" style='text-align: center;'>Mesa</th>
                                    <th class="col-5" style='text-align: center;'>Capacidad</th>
                                    <th class="col-4" style='text-align: center;'>Accion</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <input type="hidden" name="idZona" id="idZona" value="<?= $idZona ?>">
    <?php if ($proceso == 'ZonasMesasEditar') : ?>
        <button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnEditar">Editar</button>
    <?php endif; ?>
    <?php if ($proceso == 'ZonasMesasEliminar') : ?>
        <button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnBorrar">Eliminar</button>
    <?php endif; ?>
    <?php if ($proceso == 'ZonasMesasTrasladar') : ?>
        <button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnTrasladar">Trasladar</button>
    <?php endif; ?>
    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
    <input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->