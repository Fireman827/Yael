<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="modal-header bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
    <h5 class="modal-title">
        <i class="<?= $icono ?>"></i> <?= $titulo ?>
    </h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">
    <form id="FrmCompraEditar" autocomplete="off" enctype="multipart/form-data">

        <input type="hidden" id="idCompra" name="idCompra" value="<?= $compra->idCompra ?>">

        <?php
        /**
         * Reutilizamos EXACTAMENTE la misma vista de agregar
         * Solo que ya viene con variables cargadas desde el controlador
         */
        $this->load->view(
            'compras/CompraAgregar',
            [
                'compra'        => $compra,
                'detalles'      => $detalles,
                'proveedores'   => $proveedores,
                'presentaciones'=> $presentaciones,
                'controlador'   => 'Compras',
                'titulo'        => 'Editar Compra',
                'icono'         => 'fa fa-edit'
            ]
        );
        ?>

    </form>
</div>

<div class="modal-footer">
    <button id="btnActualizarCompra"
            class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> btn-sm">
        <i class="fa fa-save"></i> Actualizar
    </button>

    <button class="btn btn-secondary btn-sm" data-dismiss="modal">
        Cerrar
    </button>
</div>

