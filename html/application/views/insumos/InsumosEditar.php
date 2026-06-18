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
											<input type="text" class="form-control upper text-uppercase" id="nombreInsumos" name="nombreInsumos" placeholder="Nombre del Insumos" value="<?= $datosInsumos->nombreInsumo; ?>">
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="categoriaInsumos">Categoria de Insumo: <i class="text-danger">*</i></label>
											<select class="form-control select2" name="categoriaInsumos" id="categoriaInsumos">
												<option value="">Seleccionar</option>
												<?php foreach ($insumoCategoria as $categoria) : ?>
													<option value="<?= $categoria->idInsumoCategoria ?>" <?php echo ($categoria->idInsumoCategoria == $datosInsumos->idCategoriaInsumo) ? 'selected' : ''; ?>><?php echo $categoria->nombreInsumoCategoria; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="marcaInsumos">Marca: <i class="text-danger">*</i></label>
											<input type="text" class="form-control upper text-uppercase" id="marcaInsumos" name="marcaInsumos" placeholder="Marca" value="<?= $datosInsumos->marcaInsumo; ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="proveedor1Insumos">Proveedor 1: <i class="text-danger">*</i></label>
											<select class="form-control select2" name="proveedor1Insumos" id="proveedor1Insumos">
												<option value="">Seleccionar</option>
												<?php foreach ($proveedores as $proveedor) : ?>
													<option value="<?= $proveedor->idProveedor ?>" <?php echo ($proveedor->idProveedor == $datosInsumos->proveedor1Insumo) ? 'selected' : ''; ?>><?php echo $proveedor->nombreProveedor; ?></option>
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
													<option value="<?= $proveedor->idProveedor ?>" <?php echo ($proveedor->idProveedor == $datosInsumos->proveedor2Insumo) ? 'selected' : ''; ?>><?php echo $proveedor->nombreProveedor; ?></option>
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
													<option value="<?= $proveedor->idProveedor ?>" <?php echo ($proveedor->idProveedor == $datosInsumos->proveedor3Insumo) ? 'selected' : ''; ?>><?php echo $proveedor->nombreProveedor; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<label for="stockMinimoInsumo">Stock Minimo: <i class="text-danger">*</i></label>
											<input type="text" class="form-control numeric" name="stockMinimoInsumo" id="stockMinimoInsumo" placeholder="Stock Minimo" value="<?= $datosInsumos->stockMinimoInsumo; ?>">
										</div>
									</div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<br><br>
											<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
												<input type="checkbox" id="exentoIVAInsumos" name="exentoIVAInsumos" <?php echo ($datosInsumos->exentoIVAInsumo == 1) ? 'checked' : ''; ?>>
												<label for="exentoIVAInsumos">Exento Iva</label>
											</div>
										</div>
									</div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<br><br>
											<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
												<input type="checkbox" id="perecederoInsumos" name="perecederoInsumos" <?php echo ($datosInsumos->perecederoInsumo == 1) ? 'checked' : ''; ?>>
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
												<input type="checkbox" id="revisarInsumos" name="revisarInsumos"  <?php echo ($datosInsumos->revisarInsumo == 1) ? 'checked' : ''; ?>>
												<label for="revisarInsumos">Revisar en Corte</label>
											</div>
										</div>
									</div>
									<?php endif;?>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<div class="form-group">
											<br><br>
											<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
												<input type="checkbox" id="advaloremInsumos" name="advaloremInsumos"  <?php echo ($datosInsumos->advaloremInsumo == 1) ? 'checked' : ''; ?>>
												<label for="advaloremInsumos">Advalorem</label>
											</div>
										</div>
									</div>
									<div id="divMontoSugeridoInsumo" class="col-xs-2 col-sm-2 col-md-2 col-lg-2" <?php echo ($datosInsumos->advaloremInsumo == 1) ? '' : 'hidden'; ?>>
										<div class="form-group">
											<label for="montoSugeridoInsumos">Monto Sugerido: <i class="text-danger">*</i></label>
											<input type="text" class="form-control decimal" name="montoSugeridoInsumos" id="montoSugeridoInsumos" placeholder="Monto Sugerido" value="<?= $datosInsumos->montoSugeridoInsumo; ?>">
										</div>
										<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
											<input type="radio" id="advaloremTabacoInsumos" name="advaloremTipoInsumos" <?php echo ($datosInsumos->advaloremTipoInsumo == "Tabaco") ? 'checked' : ''; ?> value="Tabaco">
											<label for="advaloremTabacoInsumos">Tabaco</label>
										</div>
										<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
											<input type="radio" id="advaloremAlcoholInsumos" name="advaloremTipoInsumos" <?php echo ($datosInsumos->advaloremTipoInsumo == "Alcohol") ? 'checked' : ''; ?> value="Alcohol">
											<label for="advaloremAlcoholInsumos">Alcohol</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="form-group">
											<label for="descripcionInsumos">Descripcion de Insumo:</label>
											<textarea class="form-control" name="descripcionInsumos" id="descripcionInsumos" rows="2"><?php echo $datosInsumos->descripcionInsumo; ?></textarea>
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
														<th class="col-6" colspan="2">Presentacion</th>
														<!-- <th class="col-4">Descripcion</th> -->
														<th class="col-2">Unidad</th>
														<th class="col-2" colspan="2">Costo</th>
														<!-- <th class="col-1">Precio</th> -->
														<th class="col-1">Accion</th>
													</tr>
												</thead>
												<tbody id="tbodyPresentacionesInsumos">
													<?php if ($presentacionesInsumo) : ?>
														<?php foreach ($presentacionesInsumo as $row) : ?>
															<tr>
																<td>
																	<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> d-inline">
																		<input type="radio" id="paraInventario<?=$row->idPresentacion?>" class="preInventario" name="paraInventario"  <?php echo ($row->unidadInventarioInsumoPresentacion == 1) ? 'checked' : ''; ?>>
																		<label for="paraInventario<?=$row->idPresentacion?>"></label>
																	</div>
																</td>
																<td>
																	<select class="form-control select2 presentacion">
																		<option value="">Seleccionar</option>
																		<?php foreach ($presentaciones as $row1) : ?>
																			<option value="<?= $row1->idPresentacion ?>" <?php echo ($row1->idPresentacion == $row->idPresentacion) ? "Selected" : ''; ?>><?= $row1->nombrePresentacion ?> (<?= $row1->unidadPresentacion ?>)</option>
																		<?php endforeach; ?>
																	</select>
																</td>
																<td><input type="hidden" class="form-control upper text-uppercase descripcion" value="<?= $row->descripcionInsumoPresentacion ?>"></td>
																<td><input type="text" class="form-control numeric unidad" value="<?= $row->unidadInsumoPresentacion ?>"></td>
																<td><input type="text" class="form-control decimal4 costo" value="<?= $row->costoInsumoPresentacion  ?>"></td>
																<td><input type="hidden" class="form-control decimal4 precio" value="<?= $row->precioInsumoPresentacion ?>"></td>
																<td><button type="button" class="btn btn-sm btn-danger btn-block borrarTablaPresentacionInsumos" idPresentacion="<?=$row->idPresentacion?>"><i class="fa fa-trash"></i></button></td>
															</tr>
														<?php endforeach; ?>
													<?php endif; ?>
												</tbody>
											</table>
										</div>
									</div>

								</div>
								<div class="row" hidden>
									<div class="col">
										<input type="hidden" name="idInsumo" id="idInsumo" value="<?= $idInsumo; ?>">
									</div>
								</div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" name="valoresTabla" id="valoresTabla" value="">
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