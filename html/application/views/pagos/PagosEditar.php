<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content-wrapper">

<section class="content-header">
<div class="container-fluid">
<div class="row mb-2">
    <div class="col-sm-6"></div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
                <a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes'); ?>"
                   href="<?= base_url(); ?>">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                <a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes'); ?>"
                   href="<?= base_url() . $controlador; ?>"><?= ucfirst($controlador); ?></a>
            </li>
            <li class="breadcrumb-item font-weight-bold active"><?= $titulo; ?></li>
        </ol>
    </div>
</div>
</div>
</section>

<section class="content">
<div class="container-fluid">
<div class="row">
<div class="col-lg-6 col-md-12">

<div class="card card-<?= GblTraerConfiguracion('colorComponentes'); ?>">

    <div class="card-header">
        <h3 class="card-title"><?= $titulo; ?></h3>
    </div>

    <form id="FrmPagos" autocomplete="off">

    <div class="card-body">

        <div class="form-group">
            <label>Nombre del Pago <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control text-uppercase"
                   name="nombrePago"
                   value="<?= htmlspecialchars($datosPago->nombrePago); ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Monto <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control decimal"
                   name="montoPago"
                   value="<?= $datosPago->montoPago; ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Fecha de Pago <span class="text-danger">*</span></label>
            <input type="date"
                   class="form-control"
                   name="fechaPago"
                   value="<?= $datosPago->fechaPago; ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Estado</label>
            <select name="estadoPago" class="form-control">
                <option value="Activo"   <?= $datosPago->estadoPago === 'Activo'   ? 'selected' : ''; ?>>Activo</option>
                <option value="Inactivo" <?= $datosPago->estadoPago === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>

    </div>

    <div class="card-footer">
        <!-- FIX: variable correcta $datosPago (sin S) -->
        <input type="hidden" name="idPago" value="<?= $datosPago->idPago; ?>">
        <input type="hidden" id="proceso" value="<?= $proceso; ?>">

        <a href="<?= base_url($controlador); ?>"
           class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Volver
        </a>

        <button type="submit"
                class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> float-right">
            <i class="fa fa-save"></i> Guardar Cambios
        </button>
    </div>

    </form>

</div>
</div>
</div>
</div>
</section>
</div>

<input type="hidden"
       id="csrf_token_id"
       name="<?= $this->security->get_csrf_token_name(); ?>"
       value="<?= $this->security->get_csrf_hash(); ?>">
