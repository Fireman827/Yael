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
						<form id="FrmUsuario" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="nombreUsuario">Nombre</label>
											<input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" placeholder="Nombre del usuario" value="<?=$datosUsuario->nombreUsuario;?>">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="RolUsuario">Rol</label>
											<select class="form-control select2" name="rolUsuario" id="rolUsuario">
												<option value="">Sin asignar</option>
												<?php if ($roles !== false): ?>
													<?php foreach ($roles as $rol): ?>
														<option value="<?=$rol->idRol?>" <?php echo ($datosUsuario->rolUsuario == $rol->idRol) ? 'selected' : ''; ?>><?=$rol->nombreRol;?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="usuarioUsuario">Usuario</label>
											<input type="text" class="form-control" id="usuarioUsuario" name="usuarioUsuario" placeholder="Usuario" value="<?=$datosUsuario->usuarioUsuario;?>">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="claveUsuario">Clave</label>
												<input type="password" class="form-control" id="claveUsuario" name="claveUsuario" placeholder="Clave" value="<?=DesencriptarClave($datosUsuario->claveUsuario);?>" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="codigoUsuario">Codigo</label>
											<input type="text" class="form-control" id="codigoUsuario" name="codigoUsuario" placeholder="Codigo" value="<?=$datosUsuario->codigoUsuario;?>" >
										</div>
									</div>
								<?php if($superAdmin == 1):?>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="sucursal">Sucursal</label>
											<select name="sucursalUsuario" id="sucursalUsuario" class="form-control select2">
												<option value="">Sin asignar</option>
												<?php if ($sucursales !== false) : ?>
													<?php foreach ($sucursales as $sucursal) : ?>
														<option value="<?= $sucursal->idSucursal ?>" <?php echo ($sucursal->idSucursal == $datosUsuario->idSucursalUsuario ) ? 'selected':''; ?> ><?= $sucursal->nombreSucursal; ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>

										</div>
									</div>
									<?php else: ?>
										<input type="hidden" name="sucursalUsuario" id="sucursalUsuario" value="<?=$this->session->idSucursal?>">
									<?php endif; ?>

									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<div class="icheck-<?=GblTraerConfiguracion('colorComponentes');?> d-inline">
												<input type="checkbox" id="adminUsuario" name="adminUsuario" <?php echo ($datosUsuario->adminUsuario) ? 'checked' : ''; ?>>
												<label for="adminUsuario">Administrador
												</label>
											</div>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<div class="icheck-<?=GblTraerConfiguracion('colorComponentes');?> d-inline">
												<input type="checkbox" id="autorizadoUsuario" name="autorizadoUsuario" <?php echo ($datosUsuario->autorizadoUsuario) ? 'checked' : ''; ?>>
												<label for="autorizadoUsuario">Autorizar
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->

							<div class="card-footer">
								<input type="hidden" name="idUsuario" id="idUsuario" value="<?=$idUsuario?>">
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
