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
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes')?>" href="<?=base_url();?>">Inicio</a></li>
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes')?>" href="<?=base_url().$controlador;?>"><?=ucfirst($controlador);?></a></li>
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
						<form action="<?=base_url()."Respaldos/RespaldoDescargar"?>" autocomplete="off">
							<div class="card-body">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="alert alert-danger text-center">
										<label>Conserve el respaldo en un lugar seguro, preferentemente en un dispositivo externo</label>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<button type="submit" class="btn btn-block btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-download"></i> Descargar Respaldo</button>
							</div>
						</form>
					</div>
					<!-- /.card -->
				</div>
				<div class="col-12">
					<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
						<div class="card-header">
							<h3 class="card-title"><?=$titulo1?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmRestoreBackup" autocomplete="off">
							<div class="card-body">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="alert alert-danger text-center">
										<label>Solo se permiten archivos con extension .zip</label><br>
										<label>Recuerde que al restaurar un respaldo perdera cualquier información ingresada en fechas posteriores a la fecha de creación del respaldo</label>
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="form-group">
										<input type="file" class="form-control dropify" accept=".zip" data-height="130"  name="backupFile" id="backupFile"  aria-label="file example">
									</div>
								</div>
							</div>
							<div class="card-footer">
								<button type="button" id="btnGuardar" class="btn btn-block btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Restaurar</button>
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
