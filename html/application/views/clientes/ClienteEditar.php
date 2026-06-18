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
					<!-- <h4><i class="<?=$icono?>"></i> <?=$titulo?></h4> -->
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes');?>" href="<?=base_url();?>">Inicio</a></li>
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes');?>" href="<?=base_url().$controlador;?>"><?=ucfirst($controlador);?></a></li>
						<li class="breadcrumb-item font-weight-bold active"><?=$titulo;?></li>
					</ol>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section><!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
						<div class="card-header">
							<h3 class="card-title"><?=$titulo?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmCliente" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="nombreCliente">Nombre</label>
											<input type="text" class="form-control upper text-uppercase" id="nombreCliente" name="nombreCliente" placeholder="Nombre del Cliente" value="<?=$datosCliente->nombreCliente;?>">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="direccionCliente">Dirección</label>
											<input type="text" class="form-control upper text-uppercase" id="direccionCliente" name="direccionCliente" placeholder="Dirección del Cliente" value="<?=$datosCliente->direccionCliente;?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="telefonoCliente">Teléfono</label>
											<input type="text" class="form-control tel" id="telefonoCliente" name="telefonoCliente" placeholder="Teléfono" value="<?=$datosCliente->telefonoCliente;?>">
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="referenciaCliente">Punto de Referencia</label>
											<input type="text" class="form-control upper text-uppercase" id="referenciaCliente" name="referenciaCliente" placeholder="Punto de Referencia" value="<?=$datosCliente->referenciaCliente;?>">
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="idCategoriaCliente">Categoría del Cliente</label>
											<select type="text" class="form-control" id="idCategoriaCliente" name="idCategoriaCliente">
												<?=$categoriasClienteOption;?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->

							<div class="card-footer">
								<input type="hidden" name="idCliente" id="idCliente" value="<?=$idCliente;?>">
								<input type="hidden" name="avanzado" id="avanzado" value="false">
								<button type="submit" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Guardar</button>
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
<?php if(isset($proceso)){ ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
