<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?=$icono; ?>"></i> <?=$titulo; ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">
	<!-- <form id="FrmImpresoras" autocomplete="off"> -->
		<div class="row">
			<div class="col-5">
                <div class="form-group">
                    <label>Cliente</label>
                    <p><?= $factura->nombreFactura ?></p>
                </div>
            </div>
			<div class="col-2">
                <div class="form-group">
                    <label>Documento</label>
                    <p><?= $factura->tipoDocumentoFactura." ".$factura->numeroDocumentoFactura ?></p>
                </div>
            </div>
			<div class="col-4">
                <div class="form-group">
                    <label>Fecha</label>
                    <p><?= Fecha_D_M_A($factura->fechaFactura)." a las ".Hora($factura->horaFactura)  ?></p>
                </div>
            </div>
			<div class="col-1">
                <div class="form-group">
                    <label>Total</label>
                    <p><?= "$".number_format($factura->totalFactura + $factura->propinaFactura,2) ?></p>
                </div>
            </div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="table-responsive">
					<table class="table table-sm table-condensed table-bordered table-hover">
						<thead>
							<tr>
								<th class="col-1">Cant.</th>
								<th class="col-8">Producto</th>
								<th class="col-1">Precio</th>
								<th class="col-1">Subtotal</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($facturaDetalle as $d):?>
								<tr>
									<td><?=$d->cantidadPedidoDetalle?></td>
									<td><?=$d->nombreProducto?></td>
									<td>$<?=number_format($d->precioPedidoDetalle,2)?></td>
									<td>$<?=number_format(($d->cantidadPedidoDetalle * $d->precioPedidoDetalle),2)?></td>
								</tr>
							<?php endforeach;?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="3">SUBTOTAL</th>
								<th>$<?=number_format($factura->totalFactura + $factura->descuentoDolarFactura,2)?></th>
							</tr>
							<tr>
								<th colspan="3">(+)PROPINA</th>
								<th>$<?=number_format($factura->propinaFactura,2)?></th>
							</tr>
							<tr>
								<th colspan="3">(-)DESCUENTO</th>
								<th>$<?=number_format($factura->descuentoDolarFactura,2)?></th>
							</tr>
							<tr>
								<th colspan="3">TOTAL</th>
								<th>$<?=number_format($factura->totalFactura + $factura->propinaFactura,2)?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	<!-- </form> -->
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->