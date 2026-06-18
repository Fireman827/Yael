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
					<!-- <h4><i class="<=$icono?>"></i> <=$titulo?></h4> -->
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
						<form id="FrmCaja" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="nombreCaja">Nombre</label>
											<input type="text" class="form-control upper text-uppercase" id="nombreCaja" name="nombreCaja" placeholder="Nombre de la Caja" value="<?=$datosCaja->nombreCaja; ?>" >
										</div>
									</div>
									<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="Impresora">Impresora: <span class="text-danger">*</span></label>
											<select class="select2 form-control col-12" required name="impresoraCaja" id="impresoraCaja">
											<?php if($impresoras !== 0):?>
												<?php foreach($impresoras as $imp):?>
													<option value="<?=$imp->idImpresora?>" <?php echo ($imp->idImpresora == $datosCaja->impresoraCaja ) ? "selected" : ""; ?>><?=$imp->nombreImpresora?></option>
												<?php endforeach;?>
											<?php endif;?>
											</select>
										</div>
									</div>								
								</div>
								<div class="row mt-1">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<!-- <div class="table-responsive" style="height: 245px;"> -->
										<div>
											<table class="table table-sm table-condensed float" id="tablaCajaDocumento">
												<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
													<tr class="text-center">
														<th colspan="9">Documentos de Caja
															<a class="float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> agregarCajaDocumento" role="button" id_btn='agregar_nuevo'><i class="fa fa-plus"></i></a>
														</th>
													</tr>
													<tr>
														<th class="col-2">Documento</th>
														<th class="col-1">Inicio</th>
														<th class="col-1">Final</th>
														<th class="col-1">Actual</th>
														<th class="col-1">Fecha Autorización</th>														
														<th class="col-1">Fecha Resolución</th>														
														<th class="col-2">Número de Resolución</th>														
														<th class="col-2">Serie</th>														
														<th class="col-1">Accion</th>
													</tr>
												</thead>
												<tbody>
													<?=$cajaDocumento; ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->

							<div class="card-footer">
								<input type="hidden" name="idCaja" id="idCaja" value="<?=$datosCaja->idCaja?>">
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