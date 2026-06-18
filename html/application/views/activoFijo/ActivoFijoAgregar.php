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
						<form id="FrmActivoFijo" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="nombreUsuario">Nombre</label>
											<input type="text" class="form-control" id="nombreActivoFijo" name="nombreActivoFijo" placeholder="Nombre del Activo Fijo">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="marcaActivoFijo">Marca</label>
											<input type="text" class="form-control" id="marcaActivoFijo" name="marcaActivoFijo" placeholder="Marca del activo">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="modeloActivoFijo">Modelo</label>
											<input type="text" class="form-control" id="modeloActivoFijo" name="modeloActivoFijo" placeholder="Modelo del activo">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="precioActivoFijo">Precio</label>
											<input type="text" class="form-control" id="precioActivoFijo" name="precioActivoFijo" placeholder="Precio">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="vidaActivoFijo">Vida Util</label>
											<input type="text" class="form-control numeric2" id="vidaActivoFijo" name="vidaActivoFijo" placeholder="Vida util en años">
										</div>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="depreciacionActivoFijo">Tipo depreciación</label>
											<select name="depreciacionActivoFijo" id="depreciacionActivoFijo" class="form-control select2">
												<option value="">Seleccione</option>
												<option value="1">Linea Recta</option>
												<option value="2">Suma de cifras anuales</option>
												<option value="3">Disminución de saldo</option>
											</select>
										</div>
									</div>
								</div>
                <div class="row">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="categoriaActivoFijo">Categoria</label>
											<select name="categoriaActivoFijo" id="categoriaActivoFijo" class="form-control select2">
												<option value="">Sin asignar</option>

												<?php if ($categorias !== false) : ?>
													<?php foreach ($categorias as $categoria) : ?>
														<option value="<?= $categoria->idCategoria ?>"><?= $categoria->nombreCategoria; ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>

										</div>
									</div>
                  <?php if($superAdmin == 1):?>
  									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
  										<div class="form-group">
  											<label for="sucursalActivoFijo">Sucursal</label>
  											<select name="sucursalActivoFijo" id="sucursalActivoFijo" class="form-control select2">
  												<option value="">Sin asignar</option>
  												<?php if ($sucursales !== false) : ?>
  													<?php foreach ($sucursales as $sucursal) : ?>
  														<option value="<?= $sucursal->idSucursal ?>"><?= $sucursal->nombreSucursal; ?></option>
  													<?php endforeach; ?>
  												<?php endif; ?>
  											</select>

  										</div>
  									</div>
  									<?php endif; ?>
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
