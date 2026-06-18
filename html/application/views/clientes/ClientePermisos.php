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
						<li class="breadcrumb-item active font-weight-bold"><?=$titulo;?></li>
					</ol>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section><!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-<?=GblTraerConfiguracion('colorComponentes')?>">
						<div class="card-header">
							<h3 class="card-title"><?=$titulo?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<div class="card-body">
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="nombreUsuario">Nombre</label>
										<input type="text" readonly class="form-control" id="nombreUsuario" name="nombreUsuario" placeholder="Nombre del usuario" value="<?=$datosUsuario->nombreUsuario;?>">
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group">
										<label for="usuarioUsuario">Usuario</label>
										<input type="text" readonly class="form-control" id="usuarioUsuario" name="usuarioUsuario" placeholder="Usuario" value="<?=$datosUsuario->usuarioUsuario;?>">
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
									<div class="form-group" style="margin-top:12%;">
										<div class="icheck-<?=GblTraerConfiguracion('colorComponentes')?> d-inline">
											<input type="checkbox" id="adminUsuario" name="adminUsuario" <?php echo ($datosUsuario->adminUsuario) ? "checked value='1'" : "value='0'"; ?>>
											<label for="adminUsuario">Administrador
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<?php if (count($menus)>0): ?>
									<?php
									$contador = 0;
									foreach ($menus as $menu):
										if($contador == 3): ?>
									</div>
									<div class="row">
									<?php endif; ?>
									<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
										<div class="card card-<?=GblTraerConfiguracion('colorComponentes')?>">
											<div class="card-header">
												<h3 class="card-title font-weight-bold"><?=$menu->nombreMenu;?></h3>
											</div>
											<div class="card-body menuPermiso">
												<?php if (!empty($menu->modulos)): ?>
													<?php foreach ($menu->modulos as $modulo): ?>
														<div class="icheck-<?=GblTraerConfiguracion('colorComponentes')?> d-inline">
															<input type="checkbox" class="permisoUsuario <?=($modulo->funcionModulo =="") ? "principal" : ""; ?>" id="modulo<?=$modulo->idMenuModulo?>" data-idmodulo="<?=$modulo->idMenuModulo?>" <?php echo ($modulo->existeEnPermiso || $datosUsuario->adminUsuario) ? 'checked' : '';?>>
															<label for="modulo<?=$modulo->idMenuModulo?>"><?=$modulo->nombreModulo;?>
															</label>
														</div><hr>
													<?php endforeach; ?>
												<?php endif; ?>
											</div>
										</div>
									</div>
									<?php
									$contador++;
								endforeach;
							endif; ?>
						</div>

					</div>
					<!-- /.card-body -->

					<div class="card-footer">
						<input type="hidden" name="idUsuario" id="idUsuario" value="<?=$idUsuario?>">
						<button type="button" id="btnGuardar" class="btn btn-<?=GblTraerConfiguracion('colorComponentes')?> float-right"><i class="fa fa-save"></i> Guardar</button>
					</div>
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
