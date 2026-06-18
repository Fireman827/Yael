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
						<form id="FrmSenorita" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="nombreTipo">Nombre: <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper" id="nombreSenorita" name="nombreSenorita" placeholder="Nombre de la Señorita">
										</div>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="apodoSenorita">Apodo: <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper" id="apodoSenorita" name="apodoSenorita" placeholder="Apodo de la Señorita">
										</div>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="categoriaSenorita">Categoria: <span class="text-danger">*</span></label>
											<select id="categoriaSenorita" name="categoriaSenorita" class="select2 col-12">
												<option value="">Sin asignar</option>
												<?php if ($categorias !== false) : ?>
													<?php foreach ($categorias as $categoria) : ?>
														<option value="<?= $categoria->idSenoritaCategoria ?>"><?= $categoria->nombreSenoritaCategoria; ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="alturaSenorita">Altura (mts):</label>
											<input type="text" class="form-control decimal" id="alturaSenorita" name="alturaSenorita" placeholder="0000.00">
										</div>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="pesoSenorita">Peso (lbs):</label>
											<input type="text" class="form-control decimal" id="pesoSenorita" name="pesoSenorita" placeholder="0000.00">
										</div>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="edadSenorita">Edad:</label>
											<input type="text" class="form-control numeric" id="edadSenorita" name="edadSenorita" placeholder="0000">
										</div>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="nacionalidadSenorita">Nacionalidad: <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper" id="nacionalidadSenorita" name="nacionalidadSenorita" placeholder="Nacionalidad de la Señorita">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="form-group">
										  <label for="extraSenorita">Información Extra:</label>
										  <textarea class="form-control upper" name="extraSenorita" id="extraSenorita" rows="2"></textarea>
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