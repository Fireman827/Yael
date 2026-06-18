<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<style media="screen">
.cant,.precioSinIva,.precioConIva,.precio_sugerido,.desc,.subtotal
{
	text-align: right!important;
}

.sectioncot
{
	background-color: lightblue;
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<!-- <h4><i class="<?= $icono ?>"></i> <?= $titulo ?></h4> -->
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url(); ?>">Inicio</a></li>
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url() . $controlador; ?>"><?= ucfirst($controlador); ?></a></li>
						<li class="breadcrumb-item font-weight-bold active"><?= $titulo; ?></li>
					</ol>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section><!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-<?= GblTraerConfiguracion('colorComponentes'); ?>">
						<div class="card-header">
							<h3 class="card-title"><?= $titulo ?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmventaDevolucion" autocomplete="off">
							<input type="hidden" id="details" name="details" value="">
							<input type="hidden" id="idFactura" name="idFactura" value="<?=$datosFactura->idFactura ?>">
							<div class="card-body">
								<div class="row">

									<div class="col-xs-12 col-sm-3 col-md-3 col-lg-4">
										<div class="form-group">
											<label for="">Cliente</label>
											<input type="text" readonly  class="form-control" id="cliente" name="cliente" value="<?=$datosCliente->nombreCliente ?>">
											<input type="hidden"   class="form-control" id="idCliente" name="idCliente" value="<?=$datosCliente->idCliente ?>">
											<input type="hidden"   class="form-control" id="retieneIvaCliente" name="retieneIvaCliente" value="<?=$datosCliente->retieneIvaCliente ?>">
											<input type="hidden"   class="form-control" id="retencionFacturagen" name="retencionFacturagen" value="<?=$datosFactura->retencionFactura ?>">
										</div>
									</div>
									<div class="col-xs-12 col-sm-2col-md-2 col-lg-2">
										<div class="form-group">
											<label for="">Factura</label>
											<input type="text" readonly  class="form-control" id="documento" name="documento" value="<?=$datosFactura->tipoDocumentoFactura." ".$datosFactura->numeroDocumentoFactura ?>">
										</div>
									</div>
									<div class="col-xs-12 col-sm-2col-md-2 col-lg-2">
										<div class="form-group">
											<label for="">Caja</label>
											<select class="select2 form-control col-12" name="idCaja" id="idCaja">
												<?=$cajas ?>
											</select>
										</div>
									</div>
									<div class="col-xs-12 col-sm-2col-md-2 col-lg-2">
										<div class="form-group">
											<label for="">Documento</label>
											<select class="select2 form-control col-12" name="idDocumento" id="idDocumento">
												<?=$documentos ?>
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<label for=""># Devolucion </label>
											<input type="text" readonly  class="form-control" id="numeroDocumentoDev" name="numeroDocumentoDev" value="<?=$nDocumento ?>">
											<input type="hidden"   class="form-control" id="aliasDocumento" name="aliasDocumento" value="">
										</div>
									</div>


								</div>
								<!-- <button type="button" class="btn btn-info" id="modalAddProduct" name="modalAddProduct"> <i class='fa fa-plus'></i> Producto</button> -->
							</div>
						<div class="card-body">
							<div class="row">
								<div class="col-lg-12">
									<table class='table table-bordered col-lg-12'>
										<thead>
											<th class="col-lg-2">Producto</th>
											<th class="col-lg-1">Presentación</th>
											<th class="col-lg-1">Costo</th>
											<th class="col-lg-1">P. Venta</th>
											<th class="col-lg-1">% DESC</th>
											<th class="col-lg-1">Cantidad</th>
											<th class="col-lg-1">Unidades a Devolver</th>
											<th class="col-lg-1">Unidades Devultas</th>
											<th class="col-lg-1">Subtotal</th>
										</thead>
										<tbody id="infodata">
											<?=$detalles ?>
										</tbody>
										<tfoot>
											<tr>
												<th class="col-lg-1" colspan="5">SUMAS</th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"> <input type="hidden" readOnly class=" form-control totalSumasDevolucion" id="totalSumasDevolucion" name="totalSumasDevolucion" value=""></th>
												<th class="col-lg-1"> <span class="totalSumasDevolucion"></span> </th>
											</tr>
											<tr>
												<th class="col-lg-1" colspan="5">IVA</th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"> <input type="hidden" readOnly class=" form-control totalIvaDevolucion" id="totalIvaDevolucion" name="totalIvaDevolucion" value=""></th>
												<th class="col-lg-1"> <span class="totalIvaDevolucion"></span> </th>
											</tr>
											<tr>
												<th class="col-lg-1" colspan="5">RETENCION</th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"> <input type="hidden" readOnly class=" form-control totalRetencionDevolucion" id="totalRetencionDevolucion" name="totalRetencionDevolucion" value="">
													<input type="hidden" readOnly class=" form-control totalDescuentoDevolucion" id="totalDescuentoDevolucion" name="totalDescuentoDevolucion" value="">
												</th>
												<th class="col-lg-1"> <span class="totalRetencionDevolucion"></span> </th>
											</tr>
											<tr>
												<th class="col-lg-1" colspan="5">TOTAL DEVOLUCION</th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"></th>
												<th class="col-lg-1"> <input type="hidden" readOnly class=" form-control totalDevolucion" id="totalDevolucion" name="totalDevolucion" value=""></th>
												<th class="col-lg-1"> <span class="totalDevolucion"></span> </th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> float-right"><i class="fa fa-save"></i> Guardar</button>
						</div>
					</form>
					</div>
					<!-- /.card -->
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /.container-fluid -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>

<!-- Modal normal (default) -->
<div class="modal fade" id="dfModal" data-backdrop="static" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-lg">
    </div>
  </div>
</div>
