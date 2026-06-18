<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content-wrapper">

<!-- ================= HEADER ================= -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes');?>"
                           href="<?=base_url();?>">Inicio</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes');?>"
                           href="<?=base_url().$controlador;?>"><?=ucfirst($controlador);?></a>
                    </li>
                    <li class="breadcrumb-item font-weight-bold active"><?=$titulo;?></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- ================= CONTENIDO ================= -->
<section class="content">
<div class="container-fluid">

<div class="row">

<!-- ========== FORMULARIO PAGO ========== -->
<div class="col-lg-6 col-md-12">
    <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">

        <div class="card-header">
            <h3 class="card-title">Registro de Pago</h3>
        </div>

        <form id="FrmPagos" autocomplete="off">

        <div class="card-body">

            <div class="form-group">
                <label>Nombre del Pago <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control text-uppercase"
                       name="nombrePago"
                       required>
            </div>

            <div class="form-group">
                <label>Monto <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control decimal"
                       name="montoPago"
                       required>
            </div>

            <div class="form-group">
                <label>Fecha de Pago <span class="text-danger">*</span></label>
                <input type="date"
                       class="form-control"
                       name="fechaPago"
                       value="<?=date('Y-m-d');?>"
                       required>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="estadoPago" class="form-control">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            </div>

        </div>

        <div class="card-footer">
            <input type="hidden" id="proceso" value="<?=$proceso;?>">
            <button type="submit"
                    class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right">
                <i class="fa fa-save"></i> Guardar
            </button>
        </div>

        </form>
    </div>
</div>

<!-- ========== CALENDARIO DE PAGOS ========== -->
<div class="col-lg-6 col-md-12">
    <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">

        <div class="card-header">
            <h3 class="card-title">Calendario de Pagos</h3>
        </div>

        <div class="card-body">
            <div id="calendarioPagos" style="min-height: 350px;"></div>
        </div>

    </div>
</div>

</div>
</div>
</section>
</div>

<!-- ================= CSRF ================= -->
<input type="hidden"
       id="csrf_token_id"
       name="<?= $this->security->get_csrf_token_name(); ?>"
       value="<?= $this->security->get_csrf_hash(); ?>">
