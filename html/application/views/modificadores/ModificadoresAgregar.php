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
						<form id="FrmModificadores" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="forma" class="control-label">Cargar Modificador por:</label>
											<select name="forma" id="forma" class="col-12 select2">
												<option value="">Seleccione</option>
												<option value="categoria">Categoria de Producto</option>
												<option value="producto">Producto Especifico</option>
												<option value="otro">Otros</option>
											</select>
										</div>
									</div>
									
								</div>
								<div class="row">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="productoDiv" hidden>
										<div class="form-group">
											<label for="buscarProducto" class="control-label">Buscar producto:</label>
											<input type="text" id="buscarProducto" name="buscarProducto" class="form-control" placeholder="Buscar Producto">
										</div>
									</div>
								</div>
								<hr>
								<div id="categoria" hidden>
									<div class="row">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
											<label for="tipo">Categoria de Modificador: <span class="text-danger">*</span></label>
											<select name="tipo" id="tipoCategoria" class="select2 col-12">
												<option value="">Seleccione</option>
												<?php if ($modificadorTipo !== false) : ?>
													<?php foreach ($modificadorTipo as $tipo) :
														//if ($tipo->variosModificadorTipo == 1) { ?>
															<option value="<?= $tipo->idModificadorTipo ?>"><?= $tipo->nombreModificadorTipo; ?></option>
													<?php //}
													endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="categoriaProductoDiv" hidden>
											<div class="form-group">
												<label for="categoriaProducto" class="control-label">Categoria de producto:</label>
												<select name="categoriaProducto" id="categoriaProducto" class="col-12 select2">
													<option value="">Seleccione</option>
													<?php if ($productoCategoria !== false) : ?>
														<?php foreach ($productoCategoria as $producto) : ?>
															<option value="<?= $producto->idProductoCategoria ?>"><?= $producto->nombreProductoCategoria; ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</div>
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-12">
											<div class="table-responsive">
												<table class="table table-condensed table-striped table-sm" id="tablaProducto">
													<thead class="bg-<?=GblTraerConfiguracion('colorComponentes');?>">
														<tr class="text-center">
															<th colspan="4">PRODUCTOS
															</th>
														</tr>
														<tr>
															<th class="col-1 text-center">USAR</th>
															<th class="col-4">PRODUCTO</th>
															<th class="col-4">NOMBRE</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="row" id="producto" hidden>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="row">
											<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
												<label for="nombreProducto">Producto Relacionado: <span class="text-danger">*</span></label>
												<input type="text" id="nombreProducto" name="nombreProducto" class="form-control" placeholder="Nombre de Producto" readonly>
												<input type="hidden" id="idProducto" name="idProducto">
											</div>
											<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
												<label for="tipo">Categoria de Modificador: <span class="text-danger">*</span></label>
												<select name="tipo" id="tipoProducto" class="select2 col-12">
													<option value="">Seleccione</option>
													<?php if ($modificadorTipo !== false) : ?>
														<?php foreach ($modificadorTipo as $tipo) :
															//if ($tipo->variosModificadorTipo == 1) { ?>
																<option value="<?= $tipo->idModificadorTipo ?>"><?= $tipo->nombreModificadorTipo; ?></option>
														<?php //}
														endforeach; ?>
													<?php endif; ?>
												</select>
											</div>
											<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
												<label for="nombreModificador">Nombre Modificador: <span class="text-danger">*</span></label>
												<input type="text" id="nombreModificador" name="nombreModificador" class="form-control upper" placeholder="Nombre de Modificador">
											</div>
										</div>
									</div>
								</div>
								<div class="row" id="otro" hidden>
									<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
										<label for="nombre">Categoria de Modificador: <span class="text-danger">*</span></label>
										<select name="tipo" id="tipoOtro" class="select2 col-12">
											<option value="">Seleccione</option>
											<?php if ($modificadorTipo !== false) : ?>
												<?php foreach ($modificadorTipo as $tipo) :
													//if ($tipo->variosModificadorTipo == 0) { ?>
														<option value="<?= $tipo->idModificadorTipo ?>"><?= $tipo->nombreModificadorTipo; ?></option>
												<?php //}
												endforeach; ?>
											<?php endif; ?>
										</select>
									</div>
									<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
										<div class="form-group">
											<label for="nombre">Nombre Modificador: <span class="text-danger">*</span></label>
											<input type="text" id="nombre" name="nombre" class="form-control upper" placeholder="Nombre Modificador">
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->
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