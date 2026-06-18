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
								<?php foreach($datosModificador as $dm):?>
								<div class="row"></div>
								<div class="row" >									
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" >
										<div class="form-group">
											<label for="buscarProducto" class="control-label">Relacionar a Producto:</label>
											<select name="relacionarModificador" id="relacionarModificador" class="select2 col-12">
												<option value="Si" <?php echo ($dm->idProducto != 0) ? "selected" : "";?> >Si</option>
												<option value="No" <?php echo ($dm->idProducto == 0) ? "selected" : "";?> >No</option>
											</select>
										</div>
									</div>
									<div id="divBuscadorEditar" class="col-xs-6 col-sm-6 col-md-6 col-lg-6" <?php echo ($dm->idProducto != 0) ? "" : "hidden";?> >
										<div class="form-group">
											<label for="buscarProducto" class="control-label">Buscar Producto:</label>
											<input type="text" id="buscarProducto" name="buscarProducto" placeholder="Buscar Producto" class="form-control">
										</div>
									</div>
								</div>
								<hr>
									<div class="row">
										<div id="divProductoEditar" class="col-xs-3 col-sm-3 col-md-3 col-lg-3" <?php echo ($dm->idProducto != 0) ? "" : "hidden";?>>
											<div class="form-group">
												<label for="nombre">Producto Relacionado:</label>
												<input type="text" id="nombreProducto" name="nombreProducto" value="<?=$dm->nombreProducto?>" class="form-control" readonly>
												<input type="hidden" id="idProducto" name="idProducto" value='<?=$dm->idProducto ?>'>
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<label for="nombre">Categoria de Modificador: <span class="text-danger">*</span></label>
											<select name="tipoModificador" id="tipoModificador" class="select2 col-12">
												<option value="">Seleccione</option>
												<?php if ($modificadorTipo !== false) : ?>
													<?php foreach ($modificadorTipo as $tipo) : ?>
														<?php //if($dm->idProducto == 0 && $tipo->variosModificadorTipo == 0):?>
															<option value="<?= $tipo->idModificadorTipo ?>" <?php echo ( $tipo->idModificadorTipo == $dm->idModificadorTipo) ?"selected": "";?> ><?= $tipo->nombreModificadorTipo; ?></option>
														<?php //endif;?>
														<?php // if($dm->idProducto != 0 && $tipo->variosModificadorTipo != 0):?>
															<!-- <option value="<?= $tipo->idModificadorTipo ?>" <?php echo ( $tipo->idModificadorTipo == $dm->idModificadorTipo) ?"selected": "";?> ><?= $tipo->nombreModificadorTipo; ?></option> -->
														<?php //endif;?>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label for="nombre">Nombre Modificador: <span class="text-danger">*</span></label>
												<input type="text" id="nombreModificador" name="nombreModificador" class="form-control upper" value="<?= $dm->nombreModificador ?>">
											</div>
										</div>
									</div>
								<?php endforeach;?>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" id="idModificador" name="idModificador" value='<?=$idModificador ?>'>
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