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
						<form id="FrmRol" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
										<div class="form-group">
											<label for="nombreRol">Nombre</label>
											<input type="text" class="form-control" id="nombreRol" name="nombreRol" placeholder="Nombre del rol">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
									<table class="table table-hover table-sm">
										<thead>
											<tr>
												<th>Producto</th>
												<th>Existencia</th>
												<th>Presentacion</th>
												<th>Cantidad</th>
												<th>Precio</th>
												<th>Subtotal</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Producto Prueba</td>
												<td>10</td>
												<td>
													<div class="form-group">
					                  <select class="form-control select2 dark-mode" style="width: 100%;">
					                    <option selected="selected">Alabama</option>
					                    <option>Alaska</option>
					                    <option>California</option>
					                    <option>Delaware</option>
					                    <option>Tennessee</option>
					                    <option>Texas</option>
					                    <option>Washington</option>
					                  </select>
					                </div>
												</td>
											</tr>
										</tbody>
									</div>
									</table>
								</div>
							</div>
							<!-- /.card-body -->

							<div class="card-footer">
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
