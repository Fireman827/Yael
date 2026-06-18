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
						<form id="FrmServicio" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="tiempoServicio">Tiempo (minutos): <span class="text-danger">*</span></label>
											<input type="text" class="form-control numeric" id="tiempoServicio" name="tiempoServicio" placeholder="tiempo de la Servicio" value="<?=$servicio->tiempoServicio?>">
										</div>
									</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="form-group">
											<label for="categoriaServicio">Categoria: <span class="text-danger">*</span></label>
											<select id="categoriaServicio" name="categoriaServicio" class="select2 col-12">
												<option value="">Sin asignar</option>
												<?php if ($categorias !== false) : ?>
													<?php foreach ($categorias as $categoria) : ?>
														<option value="<?= $categoria->idServicioCategoria ?>" <?php echo($categoria->idServicioCategoria == $servicio->idServicioCategoria) ? 'selected' : '';?> ><?= $categoria->nombreServicioCategoria; ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="form-group">
											<label for="descripcionServicio">Descripcion:</label>
											<textarea class="form-control upper" name="descripcionServicio" id="descripcionServicio" rows="2"><?php echo $servicio->descripcionServicio;?></textarea>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="table-responsive">
											<table class="table table-sm table-hover" id="tablaSenoritas">
												<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
													<tr>
														<th class="col-4">Señorita</th>
														<th class="col-4">Monto Total ($)</th>
														<th class="col-4">Porcentaje a Señorita (%)</th>
													</tr>
												</thead>
												<tbody>
													<?php if ($senoritas !== false) : ?>
														<?php foreach ($senoritas as $senorita) : ?>
															<tr>
																<td><label for="" class="input-text categoriaSenorita" idCategoria="<?= $senorita->idSenoritaCategoria?>"><?php echo $senorita->nombreSenoritaCategoria; ?></label></td>
																<td><input type="text" class="form-control montoTotal decimal" required="required" placeholder="$ 0000.00" value="<?=$senorita->montoServicioDetalle?>"></td>
																<td><input type="text" class="form-control porcentajeSenorita decimal" required="required" placeholder="000%" value="<?=$senorita->porcentajeSenoritaServicioDetalle?>"></td>
															</tr>
														<?php endforeach; ?>
													<?php endif; ?>
												</tbody>
											</table>
											<input type="hidden" name="datosTablaSenoritas" id="datosTablaSenoritas">
											<input type="hidden" name="idServicio" id="idServicio" value="<?=$servicio->idServicio?>">
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