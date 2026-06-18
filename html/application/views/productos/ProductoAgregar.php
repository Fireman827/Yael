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
							<input type="hidden" id="bgColor" value="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<div class="card-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<ul class="nav nav-tabs">
										<li class="nav-item"><a class="nav-link pestana text-white active bg-<?= GblTraerConfiguracion('colorComponentes'); ?>" href="#vistaGeneral" role="tab">Informacion General</a></li>
										<?php	if(GblPermisos($this,'ProductoAgregarModificador',$controlador)){ ?>
											<li class="nav-item"><a class="nav-link disabled pestana  tab-modificadores" href="#vistaModificadores" role="tab">Modificadores</a></li>
										<?php } ?>
										<?php	if(GblPermisos($this,'ProductoAgregarInsumoGeneral',$controlador)){ ?>
											<li class="nav-item"><a class="nav-link disabled pestana  tab-receta" href="#vistaReceta" role="tab">Receta</a></li>
										<?php } ?>
									</ul>
									<input type="hidden" id="idProducto" name="idProducto" value="">
								</div>
							</div>
							<br>
							<div class="tab-content" id="myTabContent">
								<div class="row tab-pane fade show active" role="tabpanel" id="vistaGeneral">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<form id="FrmProductoGeneral" autocomplete="off">
											<div class="row">
												<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
													<div class="row">
														<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
															<div class="form-group">
																<label for="nombreProducto">Barcode: <span class="text-danger"></span></label>
																<input type="text" class="form-control upper" id="barcodeProducto" name="barcodeProducto" placeholder="Barcode del Producto" value="">
															</div>
														</div>
														<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
															<div class="form-group">
																<label for="nombreProducto">Nombre de Producto: <span class="text-danger">*</span></label>
																<input type="text" class="form-control upper" id="nombreProducto" name="nombreProducto" placeholder="Nombre del Producto">
															</div>
														</div>
														<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
															<div class="form-group">
																<label for="Precio">Precio de Venta: <span class="text-danger">*</span></label>
																<input type="text" class="form-control decimal" id="precioVentaProducto" name="precioVentaProducto" placeholder="$ 00.00">
															</div>
														</div>
														<?php if(GblTraerConfiguracion('precioEspecial') == 'Si'):?>
														<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
															<div class="form-group">
																<label for="PrecioEspecial">Precio Especial: <span class="text-danger">*</span></label>
																<input type="text" required class="form-control decimal" id="precioEspecialProducto" name="precioEspecialProducto" placeholder="$ 00.00">
															</div>
														</div>
														<?php endif;?>
														<?php if(GblTraerConfiguracion('precioEmpleado') == 'Si'):?>
														<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
															<div class="form-group">
																<label for="PrecioEmpleado">Precio Empleado: <span class="text-danger">*</span></label>
																<input type="text" required class="form-control decimal" id="precioEmpleadoProducto" name="precioEmpleadoProducto" placeholder="$ 00.00">
															</div>
														</div>
														<?php endif;?>

														<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
															<div class="form-group">
																<label for="Impresora">Impresora: <span class="text-danger">*</span></label>
																<select class="select2 form-control col-12" required name="impresoraProducto" id="impresoraProducto">
																<?php if($impresoras !== 0):?>
																	<?php foreach($impresoras as $imp):?>
																		<option value="<?=$imp->idImpresora?>"><?=$imp->nombreImpresora?></option>
																	<?php endforeach;?>
																<?php endif;?>
																</select>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<div class="form-group">
																<label for="descripcionProducto">Descripcion</label>
																<textarea class="form-control upper" name="descripcionProducto" id="descripcionProducto" rows="2"></textarea>
															</div>
														</div>
													</div>
												</div>
												<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
													<label for="imagenProducto">Imagen:</label>
													<input type="file" class="dropify" data-allowed-file-extensions="jpg png jpeg" data-height="170"  name="imagenProducto" id="imagenProducto">
												</div>
											</div>

											<div class="row">
												<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
													<div class="table-responsive pre-scrollable" style="max-height:225px;">
														<table class="table table-sm table-condensed " id="tablaCategoriaProducto">
															<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																<tr class="text-center">
																	<th colspan="3">Categoria Producto
																		<a ruta="ProductoListarCategoria" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
																	</th>
																</tr>
																<tr>
																	<th class="col-10">Categoria</th>
																	<th class="col-2">Accion</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
													</div>
												</div>
												<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
													<div class="table-responsive pre-scrollable" style="max-height:225px;">
														<table class="table table-sm table-hover" id="tablaComoModificador">
															<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																<tr class="text-center">
																	<th colspan="3">Hacer Modificador
																		<a ruta="ProductoListarModificadorTipo" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
																	</th>
																</tr>
																<tr>
																	<th class="col-10">Tipo de Modificador</th>
																	<!-- <th class="col-3">Precio</th> -->
																	<th class="col-2">Accion</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
													</div>
												</div>

											</div>
											<br>
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<input type="hidden" name="categoriasProducto" id="categoriasProducto" class="form-control" value="">
													<input type="hidden" name="comoModificadoresProducto" id="comoModificadoresProducto" class="form-control" value="">
													<!-- <a data-toggle="modal" data-target="#modalModificador" data-refresh="true" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?>  float-left"><i class="fa fa-outdent"></i> Agregar Como Modificador</a> -->
													<button type="button" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> float-right salirProducto" hidden><i class="fa fa-times"></i> Salir</button>
													<button type="submit" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> float-right guardarProducto"><i class="fa fa-save"></i> Guardar</button>
												</div>
											</div>
										</form>
									</div>
								</div>
								<div class="row tab-pane fade" role="tabpanel" id="vistaModificadores">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<form id="FrmProductoModificadores" autocomplete="off">
											<!-- <div class="row" hidden>
												<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 float-rigth">
													<div class="form-group">
														<label for="listaModificadores">Modificadores</label>
														<select class="select2 form-control" name="listaModificadores" id="listaModificadores">
															<?php foreach ($modificadores as $mod) : ?>
																<option value="<?= $mod->idModificadorTipo ?>"><?= $mod->nombreModificadorTipo ?></option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>
											</div> -->
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="table-responsive pre-scrollable" style="max-height:750px;">
														<table class="table table-sm table-condensed " id="tablaModificadores">
															<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																<tr class="text-center">
																	<th colspan="3">Modificadores
																		<a ruta="ProductoListarModificador" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
																	</th>
																</tr>
																<tr>
																	<th class="col-8">Categoria</th>
																	<th class="col-3">Cantidad</th>
																	<th class="col-1">Accion</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<button type="button" id="insertarModificadores" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?>">Guardar Modificadores</button>
												</div>
											</div>
										</form>
									</div>
								</div>
								<div class="row tab-pane fade" role="tabpanel" id="vistaReceta">
									<div class="row">
										<div class="col-4">
											<div class="nav flex-column nav-tabs tabs-recetas" id="v-pills-tab" role="tablist" aria-orientation="vertical">
												<a class="nav-link active " id="v-pills-general-tab" data-toggle="pill" href="#v-pills-general" role="tab" aria-controls="v-pills-home" aria-selected="true" idUnico="general">Insumos Generales</a>
											</div>
										</div>
										<div class="col-8">
											<div class="tab-content" id="v-pills-tabContent">
												<div class="tab-pane fade show active" id="v-pills-general" role="tabpanel">
													<div class="row">
														<div class="col-12">
															<div class="form-group">
																<label for="buscarInsumo">Buscar Insumo</label>
																<input type="text" id="buscarInsumo" name="buscarInsumo" class="form-control" placeholder="Buscar Insumo">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<div class="table-responsive pre-scrollable" style="max-height:500px;">
																<table class="table table-sm table-condensed" id="tablaInsumoGeneral">
																	<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																		<tr class="text-center">
																			<th colspan="4">Insumos Generales
																				<!-- <a id="agregarInsumoGeneral" ruta="ProductoListarInsumoGeneral" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
																			</th>
																		</tr>
																		<tr>
																			<th class="col-4">Insumo</th>
																			<th class="col-4">Presentacion</th>
																			<th class="col-3">Cantidad</th>
																			<th class="col-1">Acciones</th>
																		</tr>
																	</thead>
																	<tbody>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<button type="button" id="insertarRecetaGeneral" class="btn btn-primary ">Guardar Receta</button>
														</div>
													</div>

												</div>
												<div class="tab-pane fade" id="v-pills-home" role="tabpanel">
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<div class="table-responsive pre-scrollable" style="max-height: 500px;">
																<table id="tablaModificadoresInsumosReceta" class="table table-sm table-condensed">
																	<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																		<tr class="text-center">
																			<th colspan="4">Insumos Generales
																				<!-- <a id="agregarInsumoGeneral" ruta="ProductoListarInsumoGeneral" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
																			</th>
																		</tr>
																		<tr>
																			<th class="col-5">Insumo</th>
																			<th class="col-5">Presentacion</th>
																			<th class="col-2">Cantidad</th>
																		</tr>
																	</thead>
																	<tbody>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<button type="button" id="insertarRecetaModificador" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?>">Guardar Receta</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
						<!-- /.card-body -->

						<div class="card-footer">

						</div>
					</div>
					<!-- /.card -->
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->
			<!-- Button trigger modal -->
			<!-- Modal -->
			<div class="modal fade" id="modalModificador" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
							<h5 class="modal-title">Agregar Como Modificador</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="form-group">
										<label for="">Buscar Tipo Modificador</label>
										<input type="text" class="form-control" name="buscarTipoModificador" id="buscarTipoModificador" placeholder="Buscar Tipo Modificador">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<!-- <table class="table table-sm table-hover" id="tablaComoModificador">
										<thead>
											<tr>
												<th class="col-8">Tipo de Modificador</th>
												<th class="col-3">Precio</th>
												<th class="col-1">Accion</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table> -->
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
							<!-- <button type="button" class="btn btn-primary">Save</button> -->
						</div>
						<!-- /.content-wrapper -->
					</div>
				</div>
			</div>
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
