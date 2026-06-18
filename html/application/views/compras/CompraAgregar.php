<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="content-wrapper">

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4 class="text-<?=GblTraerConfiguracion('colorComponentes');?>">
          <i class="<?=$icono?>"></i> <?=$titulo?>
        </h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?=base_url();?>">Inicio</a></li>
          <li class="breadcrumb-item active"><?=$titulo?></li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
<div class="container-fluid">

<form id="FrmCompra" enctype="multipart/form-data" autocomplete="off">

<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">

<!-- ================= HEADER ================= -->
<div class="card-header">
  <h3 class="card-title">
    <i class="fa fa-shopping-cart"></i> Registro de Compra
  </h3>
  <div class="card-tools">
    <button type="button" id="btnNuevoProveedor" class="btn btn-<?=GblTraerConfiguracion('colorComponentes')?> btn-sm">
        <i class="fa fa-plus"></i> Nuevo Proveedor
    </button>
  </div>
</div>

<!-- ================= DATOS GENERALES ================= -->
<div class="card-body">

<div class="row">

  <div class="col-md-4">
    <label>Proveedor</label>
    <select name="idProveedor" id="idProveedor" class="form-control select2" required>
      <option value="">Seleccione proveedor</option>
      <?php foreach($proveedores as $p): ?>
        <option value="<?=$p->idProveedor?>">
          <?=$p->nombreProveedor?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label>Tipo Documento</label>
    <select name="tipoDocumento" id="tipoDocumento" class="form-control" required>
      <option value="">Seleccione</option>
      <option value="TICKET">Ticket</option>
      <option value="FACTURA">Factura</option>
      <option value="CREDITO_FISCAL">Crédito Fiscal</option>
    </select>
  </div>

  <div class="col-md-3">
    <label>N° Documento</label>
    <input type="text" name="numeroDocumento" class="form-control" required>
  </div>

  <div class="col-md-2">
    <label>Fecha</label>
    <input type="date" name="fechaCompra" class="form-control"
           value="<?=date('Y-m-d');?>" required>
  </div>

</div>

<!-- ================= FISCALES ================= -->
<div class="row mt-2" id="boxFiscal">
  <div class="col-md-3">
    <label>NIT</label>
    <input type="text" name="nitProveedor" class="form-control">
  </div>
  <div class="col-md-3">
    <label>NRC</label>
    <input type="text" name="nrcProveedor" class="form-control">
  </div>
</div>

<!-- ================= ARCHIVO ================= -->
<div class="row mt-2" id="boxArchivo">
  <div class="col-md-4">
    <label>Documento (PDF / Imagen)</label>
    <input type="file"
           name="archivoFactura"
           id="archivoFactura"
           class="dropify"
           data-allowed-file-extensions="pdf jpg jpeg png">
  </div>
</div>

<hr>

<!-- ================= BUSCADOR ================= -->
<div class="row mb-2">
  <div class="col-md-6">
    <label>Buscar Insumo</label>
    <input type="text"
           id="producto_buscar"
           class="form-control producto_buscar"
           placeholder="Ingrese nombre de insumo">
  </div>
</div>

<!-- ================= TABLA INSUMOS ================= -->
<div class="table-responsive">
<table class="table table-bordered table-sm" id="inventable">
  <thead class="bg-<?=GblTraerConfiguracion('colorComponentes');?> text-center">
    <tr>
      <th class="d-none">ID</th>
      <th>Insumo</th>
      <th>Presentación</th>
      <th>Costo</th>
      <th>Vencimiento</th>
      <th>Cantidad</th>
      <th></th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
</div>

<!-- ================= TOTALES ================= -->
<div class="row mt-3">
  <div class="col-md-6 text-right"><strong>Total Cantidad:</strong></div>
  <div class="col-md-2"><span id="totcant">0</span></div>

  <div class="col-md-2 text-right"><strong>Total Compra:</strong></div>
  <div class="col-md-2"><span id="total_dinero">0.00</span></div>
</div>

<!-- ================= HIDDEN ================= -->
<input type="hidden" name="subtotal" id="subtotal">
<input type="hidden" name="total" id="total_dineroh">
<input type="hidden" name="datos" id="datos">

</div>

<!-- ================= FOOTER ================= -->
<div class="card-footer text-right">
  <button type="button"
          id="btnGuardarCompra"
          class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?>">
    <i class="fa fa-save"></i> Guardar Compra
  </button>
</div>

</div>
</form>

</div>
</section>
</div>

<input type="hidden" id="csrf_token"
       value="<?=$this->security->get_csrf_hash();?>">

