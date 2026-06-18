<?php
defined('BASEPATH') or exit('No direct script access allowed');
$a = uniqid();
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?=$icono; ?>"></i> <?=$titulo; ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">



	<div class="card-body">
		<div class="alert alert-primary text-center" role="alert">
			<strong>
				DOCUMENTO TRIBUTARIO ELECTRÓNICO <br>
				<?=$datosVenta->tipoDocumentoFactura?>
			</strong>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="nombreCategoria">Código generación</label>
					<p><?=$datosVenta->codigoGeneracion?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="">Sello de recepción</label>
					<p><?=$datosVenta->selloRecibido?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="">Número de control</label>
					<p><?=$datosVenta->numeroControl?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="">Modelo de facturacion</label>
					<p><?=$datosModelo->valores ?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="">Version Json</label>
					<p><?= $datosDocumento !== false ? $datosDocumento->version : ""; ?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="">Tipo de transmisión</label>
					<p><?=$datosTransmision->valores ?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="">Fecha emisión</label>
					<p><?=fecha_D_M_A($datosVenta->fechaFactura) ?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="">Hora emisión</label>
					<p><?=Hora($datosVenta->horaFactura) ?></p>
				</div>
			</div>
		</div>
	</div>

<?php if ($idReferencia != ""): ?>
	<div class="card-body">
		<div class="alert alert-primary text-center" role="alert">
			<strong>
				DOCUMENTOS RELACIONADOS
			</strong>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="nombreCategoria">Tipo de Documento</label>
					<p><?=$DocumentoRef?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="nombreCategoria">No. de  Documento</label>
					<p><?=$codigoGeneracionRef?></p>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="nombreCategoria">Fecha del Documento</label>
					<p><?=$fechaFacturaRef?></p>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="card-body">
	<div class="alert alert-primary text-center" role="alert">
		<strong>
			INFORMACION DEL CLIENTE
		</strong>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="form-group">
				<label for="nombreCategoria">Nombre</label>
				<p><?=$datosCliente->nombreCliente?></p>
			</div>
		</div>
		<?php if ($datosCliente->facturarConCliente == "DUI"): ?>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="nombreCategoria">DUI</label>
					<p><?=$datosCliente->duiCliente?></p>
				</div>
			</div>
		<?php else: ?>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="nombreCategoria">NIT</label>
					<p><?=$datosCliente->nitCliente?></p>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($datosVenta->tipoDocumentoFactura != 'FAC'): ?>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<div class="form-group">
					<label for="nombreCategoria">NRC</label>
					<p><?=$datosCliente->nrcCliente?></p>
				</div>
			</div>
		<?php endif; ?>
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="form-group">
				<label for="nombreCategoria">Telefono</label>
				<p><?=$datosCliente->telefonoCliente?></p>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="form-group">
				<label for="nombreCategoria">Correo</label>
				<p><?=$datosCliente->emailCliente?></p>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="form-group">
				<label for="nombreCategoria">Direccion</label>
				<p><?=mb_strtoupper($datosCliente->direccionCliente.", ".$municipioClienteNombre.", ".$departamentoClienteNombre)?></p>
			</div>
		</div>
	</div>
	<div class="row">
		<?php if ($datosVenta->tipoDocumentoFactura != "FAC"): ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreCategoria">Actividad</label>
					<p><?=mb_strtoupper($datosCliente->giroCliente.$giro)?></p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>


<div class="card-body">
	<hr>
	<input type="hidden" name="idpppp"id="idpppp" value="">
	<input type="hidden" name="datospp"id="datospp" value="">
	<div class="row mt-3">
		<div class="col-lg-12">
			<table class='table table-bordered col-lg-12'>
				<thead>
					<th class="col-lg-1">Cant</th>
					<th class="col-lg-2">Codigo</th>
					<th class="col-lg-3">Descripción</th>
					<th class="col-lg-1">Precio unitario</th>
					<th class="col-lg-1">Descuento</th>
					<th class="col-lg-2">V. N. S.</th>
					<th class="col-lg-2">V. E.</th>
					<th class="col-lg-1">Venta gravada</th>
				</thead>
				<tbody id="infodata">
					<?php
					if ($datosDetalle!=false) {
						$gravado = 0;
						$descuentos = 0;
						$totalIva = 0;
						$nDatosPedidoDetalle = 0;
						foreach ($datosDetalle as $fila) {
							$cantidad = $fila->cantidadPedidoDetalle;
			        if($datosVenta->tipoDocumentoFactura != "FAC"){
			          $precioUnitarioMostrar = $fila->precioPedidoDetalle / (1 + GblTraerConfiguracionFe("iva"));
			          // $precioUnitarioMostrar = $fila->precioIvaUnitarioPedidoDetalle / (1 + GblTraerConfiguracionFe("iva",$datosVenta->idSucursalFactura));
			          $descuentoMostrar = 0;//(($fila->precioUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
			          // $descuentoMostrar = (($fila->precioUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
			          $subTotalMostrar = ($precioUnitarioMostrar * $cantidad);
			          // $subTotalMostrar = ($fila->subTotalIvaPedidoDetalle / (1 + GblTraerConfiguracionFe("iva",$datosVenta->idSucursalFactura))) - ($descuentoMostrar * $cantidad);
			        } else {
			          $precioUnitarioMostrar = $fila->precioPedidoDetalle;
			          $descuentoMostrar = 0;//(($fila->precioIvaUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
			          $subTotalMostrar = ($precioUnitarioMostrar * $cantidad);
			          // $precioUnitarioMostrar = $fila->precioIvaUnitarioPedidoDetalle;
			          // $descuentoMostrar = (($fila->precioIvaUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
			          // $subTotalMostrar = ($fila->subTotalIvaPedidoDetalle) - ($descuentoMostrar * $cantidad);
			        }
							$gravado += $subTotalMostrar;
			        $descuentos += $descuentoMostrar;
							?>
							<?php if (($fila->nombreProducto ?? "") != ""): ?>
								<tr >
									<td><?=number_format($cantidad,2)?></td>
									<td><?=$fila->barcodeProducto?></td>
									<td><?=$fila->nombreProducto?></td>
									<td><?=number_format($precioUnitarioMostrar,4)?></td>
									<td><?=number_format($descuentoMostrar,4)?></td>
									<td></td>
									<td></td>
									<td><?=number_format($subTotalMostrar,4)  ?></td>
								</tr>
							<?php else: ?>
								<tr>
									<td colspan="8"><?=mb_strtoupper($fila->comentarioPedidoDetalle ?? "") ?></td>
								</tr>
							<?php endif; ?>
							<?php
						}
						$iva = round($gravado * GblTraerConfiguracionFe("iva",$datosVenta->idSucursalFactura),2);
			      $subtotal = round($gravado,2);
			      $nosujeto=0;
			      $exento=0;
			      $total = round($gravado,2);
			      if($datosVenta->tipoDocumentoFactura == "CCF"){
			        $total = round($gravado+$iva,2);
			      }
			      $ivaRet = 0;
			      if($datosCliente->retieneIvaCliente){
			        if($datosVenta->tipoDocumentoFactura == "CCF"){
			          $ivaRet = $gravado * GblTraerConfiguracionFe("ivaRet");
			        } else {
			          $ivaRet = ($gravado/(1+GblTraerConfiguracionFe("iva"))) * GblTraerConfiguracionFe("ivaRet");
			        }
			        $total = round($total-$ivaRet,2);
			      }
					}
					 ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4"></th>
						<th>Total operación</th>
						<th>No sujetas <?=number_format($nosujeto,2) ?></th>
						<th>Exentas <?=number_format($exento,2) ?></th>
						<th>Gravadas <?=number_format($gravado,2) ?></th>
					</tr>
					<tr>
						<th colspan="4"></th>
						<th colspan="3">Suma de operacion sin impuestos</th>
						<th><?=number_format($gravado,2) ?></th>
					</tr>
					<tr>
						<th colspan="4"></th>
						<th colspan="3">IVA Retenido</th>
						<th><?=number_format($ivaRet,2) ?></th>
					</tr>
					<?php if ($datosVenta->tipoDocumentoFactura != "FAC"): ?>
						<tr>
							<th colspan="4"></th>
							<th colspan="3">IVA 13%</th>
							<th><?=number_format($iva,2) ?></th>
						</tr>
						<tr>
							<th colspan="4"></th>
							<th colspan="3">Sub total</th>
							<th><?=number_format($total,2) ?></th>
						</tr>
						<tr>
							<th colspan="4"></th>
							<th colspan="3">Monto total de la operacion</th>
							<th><?=number_format($total,2) ?></th>
						</tr>
						<tr>
							<th colspan="4"></th>
							<th colspan="3">Total a pagar</th>
							<th><?=number_format($total,2) ?></th>
						</tr>
					<?php else: ?>
						<tr>
							<th colspan="4"></th>
							<th colspan="3">Monto total de la operacion</th>
							<th><?=number_format($total,2) ?></th>
						</tr>
						<tr>
							<th colspan="4"></th>
							<th colspan="3">Total a pagar</th>
							<th><?=number_format($total,2) ?></th>
						</tr>

					<?php endif; ?>
				</tfoot>
			</table>
		</div>
	</div>
</div>





</div>
</div>
<div class="modal-footer">
	<!-- <button type="button" class="btn btn-primary addpp" id="addpp" name="addpp" data-dismiss="modal"> <i class='fa fa-save'></i> Imprimir</button> -->
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>

</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->

<script type="text/javascript">
$(document).ready()
{
	$(".impresiones").numeric({negative:false});
}


</script>
