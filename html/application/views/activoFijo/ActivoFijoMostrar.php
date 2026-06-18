<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content-wrapper">

    <!-- Encabezado -->
    <section class="content-header">
        <div class="container-fluid">
            <h4 class="text-<?= GblTraerConfiguracion('colorComponentes'); ?>">
                <i class="fa fa-building"></i> Administrar Activos Fijos
            </h4>
        </div>
    </section>

    <!-- Contenido -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="card shadow-sm">

                    <div class="card-header bg-<?= GblTraerConfiguracion('colorComponentes'); ?> text-white">
                        <h4 class="card-title mb-0">
                            <i class="fa fa-building"></i> Activos Fijos
                        </h4>
                    </div>

                    <div class="card-body">

                        <!-- Botón agregar activo -->
                        <?php if (GblPermisos($this, "ActivoFijoAgregar", "ActivoFijo")) { ?>
                            <a href="<?= base_url("ActivoFijoAgregar"); ?>"
                               class="btn btn-success mb-3">
                                <i class="fa fa-plus"></i> Agregar Activo
                            </a>
                        <?php } ?>

                        <!-- Tabla de activos fijos -->
                        <div class="table-responsive">
                            <table id="tablaAdmin"
                                   class="table table-bordered table-hover table-striped"
                                   width="100%">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Marca</th>
                                        <th>Precio</th>
                                        <th>Vida Útil (años)</th>
                                        <th>Estado</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

</div>

<!-- Script del módulo -->
<script src="<?= base_url('scripts/activofijo.js'); ?>"></script>
