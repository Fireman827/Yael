<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="content-wrapper">

<section class="content-header">
    <div class="container-fluid">
        <h4><i class="fa fa-shopping-cart"></i> <?= $titulo ?></h4>
    </div>
</section>

<section class="content">
<div class="container-fluid">

<div class="card card-<?= GblTraerConfiguracion('colorComponentes'); ?>">

<div class="card-body">

<h5>
    Compra #<?= $compra->idCompra ?>
    <small class="text-muted">(<?= $compra->fechaCompra ?>)</small>
</h5>

<p>
    <strong>Proveedor:</strong>
    <?php
        $pr = TraerUnDato('proveedor',['idProveedor'=>$compra->idProveedor]);
        echo $pr ? $pr->nombreProveedor : '';
    ?>
</p>

<p>
    <strong>Documento:</strong>
    <?= $compra->tipoDocumentoCompra ?> - <?= $compra->numeroDocumento ?>
</p>

<hr>

<div class="table-responsive">
<table class="table table-sm table-bordered">
<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
<tr>
    <th>Insumo</th>
    <th>Cantidad</th>
    <th>Costo</th>
    <th>Subtotal</th>
</tr>
</thead>
<tbody>

<?php foreach($detalles as $d): 
    $ins = TraerUnDato('insumo',['idInsumo'=>$d->idInsumoDetalle]);
?>
<tr>
    <td><?= $ins ? $ins->nombreInsumo : 'Insumo #' . $d->idInsumoDetalle ?></td>
    <td><?= $d->cantidadInsumoDetalle ?></td>
    <td>$<?= number_format($d->costoInsumoDetalle,2) ?></td>
    <td>$<?= number_format($d->subtotalInsumoDetalle,2) ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

<hr>

<div class="row">
    <div class="col-md-6"></div>
    <div class="col-md-6 text-right">
        <p><strong>Total:</strong> $<?= number_format($compra->totalCompra,2) ?></p>
    </div>
</div>

<?php if(!empty($compra->archivoFactura)): ?>
<hr>
<a href="<?= base_url($compra->archivoFactura); ?>"
   target="_blank"
   class="btn btn-info btn-sm">
    <i class="fa fa-file"></i> Ver archivo adjunto
</a>
<?php endif; ?>

</div>

</div>
</div>
</section>
</div>

