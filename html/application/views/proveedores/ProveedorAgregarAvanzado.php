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
						<form id="FrmProveedor" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="nombreProveedor">Nombre <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper text-uppercase" id="nombreProveedor" name="nombreProveedor" placeholder="Nombre del Proveedor">
										</div>
									</div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
										<div class="form-group">
											<label for="razonSocialProveedor">Razón Social <span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="razonSocialProveedor" name="razonSocialProveedor" placeholder="Razón Social">
										</div>
									</div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
										<div class="form-group">
											<label for="departamentoProveedor">Departamento <span class="text-danger">*</span></label>
											<select class="select2 form-control" id="departamentoProveedor" name="departamentoProveedor">
                                                <?=$departamentos; ?>
                                            </select>
										</div>
									</div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
										<div class="form-group">
											<label for="municipioProveedor">Municipio <span class="text-danger">*</span></label>
											<select class="select2 form-control" id="municipioProveedor" name="municipioProveedor">
                                                <option value="">Seleccione un municipio</option>
                                            </select>
										</div>
									</div>
								</div>
								<div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="direccionProveedor">Dirección <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper text-uppercase" id="direccionProveedor" name="direccionProveedor" placeholder="Dirección">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nrcProveedor">NRC <span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="nrcProveedor" name="nrcProveedor" placeholder="NRC">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nitProveedor">NIT <span class="text-danger">*</span></label>
											<input type="text" class="form-control nit" id="nitProveedor" name="nitProveedor" placeholder="0000-000000-000-0">
										</div>
									</div>
								</div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="giroProveedor">Giro <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper text-uppercase" id="giroProveedor" name="giroProveedor" placeholder="Giro">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="categoriaProveedor">Categoria Contribuyente <span class="text-danger">*</span></label>
											<select type="text" class="form-control select2" id="categoriaProveedor" name="categoriaProveedor">
                                                <option value="">Seleccione una categoría</option>
                                                <option value="micro">Micro</option>
                                                <option value="pequenia">Pequeña</option>
                                                <option value="mediana">Mediana</option>
                                                <option value="grande">Gran Contribuyente</option>
                                            </select>
										</div>
									</div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="telefonoProveedor">Teléfono <span class="text-danger">*</span></label>
											<input type="text" class="form-control tel" id="telefonoProveedor" name="telefonoProveedor" placeholder="0000-0000">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="correoProveedor">Correo <span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="correoProveedor" name="correoProveedor" placeholder="alias@dominio.com" >
										</div>
									</div>
                                </div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6col-xs-12">
										<div class="form-group">
											<label for="bancoProveedor">Banco Proveedor <span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="bancoProveedor" name="bancoProveedor" placeholder="Banco Agricola">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6col-xs-12">
										<div class="form-group">
											<label for="cuentaProveedor">Numero Cuenta Banco Proveedor <span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="cuentaProveedor" name="cuentaProveedor" placeholder="Numero Cuenta Banco">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6col-xs-12">
										<div class="form-group">
											<label for="bancoProveedor2">Banco Proveedor 2<span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="bancoProveedor2" name="bancoProveedor2" placeholder="Banco Agricola">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6col-xs-12">
										<div class="form-group">
											<label for="cuentaProveedor2">Numero Cuenta Banco Proveedor 2<span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="cuentaProveedor2" name="cuentaProveedor2" placeholder="Numero Cuenta Banco">
										</div>
									</div>
								</div>
								<div class="row mt-1">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="table-responsive" style="height: 245px;">
											<table class="table table-sm table-condensed float rounded" id="tablaContactos">
												<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
													<tr class="text-center">
														<th colspan="5">Contactos
															<a id="agregarContacto" class="float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
														</th>
													</tr>
													<tr>
														<th class="col-4">Nombre</th>
														<th class="col-2">Cargo</th>
														<th class="col-2">Teléfono</th>
														<th class="col-3">Correo</th>
														<th class="col-1">Accion</th>
													</tr>
												</thead>
												<tbody>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" name="idProveedor" id="idProveedor" value="0">
                                <input type="hidden" name="avanzado" id="avanzado" value="true">
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
