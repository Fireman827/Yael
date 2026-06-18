<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
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
						<form id="FrmInsumos" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="nombreInsumos">Nombre de Insumo: <i class="text-danger">*</i></label>
											<input type="text" class="form-control upper text-uppercase" id="nombreInsumos" name="nombreInsumos" placeholder="Nombre del Insumos">
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="categoriaInsumos">Categoria de Insumo: <i class="text-danger">*</i></label>
											<select class="form-control select2" name="categoriaInsumos" id="categoriaInsumos">
												<option value="">Seleccionar</option>
												<?php foreach ($insumoCategoria as $categoria) : ?>
													<option value="<?= $categoria->idInsumoCategoria ?>"><?php echo $categoria->nombreInsumoCategoria; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="marcaInsumos">Marca: <i class="text-danger">*</i></label>
											<input type="text" class="form-control upper text-uppercase" id="marcaInsumos" name="marcaInsumos" placeholder="Marca">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="proveedor1Insumos">Proveedor 1:  <i class="text-danger">*</i></label>
											<select class="form-control select2" name="proveedor1Insumos" id="proveedor1Insumos">
												<option value="">Seleccionar</option>
												<?php foreach ($proveedores as $proveedor) : ?>
													<option value="<?= $proveedor->idProveedor ?>"><?php echo $proveedor->nombreProveedor; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="proveedor2Insumos">Proveedor 2:</label>
											<select class="form-control select2" name="proveedor2Insumos" id="proveedor2Insumos">
												<option value="">Seleccionar</option>
												<?php foreach ($proveedores as $proveedor) : ?>
													<option value="<?= $proveedor->idProveedor ?>"><?php echo $proveedor->nombreProveedor; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="proveedor3Insumos">Proveedor 3:</label>
											<select class="form-control select2" name="proveedor3Insumos" id="proveedor3Insumos">
												<option value="">Seleccionar</option>
												<?php foreach ($proveedores as $proveedor) : ?>
													<option value="<?= $proveedor->idProveedor ?>"><?php echo $proveedor->nombreProveedor; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<label for="stockMinimoInsumo">Stock Minimo: <i class="text-danger">*</i></label>
											<input type="text" class="form-control numeric" name="stockMinimoInsumo" id="stockMinimoInsumo" placeholder="Stock Minimo">
										</div>
									</div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<br><br>
											<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
												<input type="checkbox" id="exentoIVAInsumos" name="exentoIVAInsumos">
												<label for="exentoIVAInsumos">Exento Iva</label>
											</div>
										</div>
									</div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<br><br>
											<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
												<input type="checkbox" id="perecederoInsumos" name="perecederoInsumos">
												<label for="perecederoInsumos">Perecedero</label>
											</div>
										</div>
									</div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" hidden>
										<div class="form-group">
											<br><br>
											<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
												<input type="checkbox" id="decimalInsumos" name="decimalInsumos">
												<label for="decimalInsumos">Venta decimal</label>
											</div>
										</div>
									</div>
									<?php if(GblTraerConfiguracion('stockApertura') == 'Si'):?>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
											<div class="form-group">
												<br><br>
												<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
													<input type="checkbox" id="revisarInsumos" name="revisarInsumos">
													<label for="revisarInsumos">Revisar en Corte</label>
												</div>
											</div>
										</div>
									<?php endif;?>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<br><br>
											<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
												<input type="checkbox" id="advaloremInsumos" name="advaloremInsumos">
												<label for="advaloremInsumos">Advalorem</label>
											</div>
										</div>
									</div>
									<div id="divMontoSugeridoInsumo"  class="col-xs-2 col-sm-2 col-md-2 col-lg-2" hidden>
										<div class="form-group">
											<label for="montoSugeridoInsumo">Monto Sugerido: <i class="text-danger">*</i></label>
											<input type="text" class="form-control decimal" name="montoSugeridoInsumos" id="montoSugeridoInsumos" placeholder="Monto Sugerido">
										</div>
										<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
											<input type="radio" id="advaloremTabacoInsumos" name="advaloremTipoInsumos" value="Tabaco">
											<label for="advaloremTabacoInsumos">Tabaco</label>
										</div>
										<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
											<input type="radio" id="advaloremAlcoholInsumos" name="advaloremTipoInsumos" value="Alcohol">
											<label for="advaloremAlcoholInsumos">Alcohol</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="form-group">
											<label for="descripcionInsumos">Descripcion de Insumo:</label>
											<textarea class="form-control upper text-uppercase" name="descripcionInsumos" id="descripcionInsumos" rows="2"></textarea>
										</div>
									</div>
								</div>
								<div class="row">

									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="table-responsive pre-scrollable" style="max-height:270px;">
											<table class="table table-sm table-condensed " id="tablaPresentacionesInsumos">
												<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
													<tr class="text-center">
														<th colspan="7">Presentaciones
															<a id="agregarTablaPresentacionesInsumos" class="float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
														</th>
													</tr>
													<tr>
														<th class="col-1" >Pre. Inventario</th>
														<th class="col-7" colspan="2">Presentacion</th>
														<!-- <th class="col-4">Descripcion</th> -->
														<th class="col-2">Unidad</th>
														<th class="col-2" colspan="2">Costo</th>
														<!-- <th class="col-1">Precio</th> -->
														<th class="col-1">Accion</th>
													</tr>
												</thead>
												<tbody id="tbodyPresentacionesInsumos">
												</tbody>
											</table>
										</div>
									</div>

								</div>
								
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" name="valoresTabla" id="valoresTabla" value="" >
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