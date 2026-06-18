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
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nombreCliente">Nombre</label>
											<input type="text" class="form-control upper text-uppercase" id="nombreCliente" name="nombreCliente" placeholder="Nombre del Cliente" value="<?=$datosCliente->nombreCliente;?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nombreCliente">Dirección</label>
											<input type="text" class="form-control upper text-uppercase" id="direccionCliente" name="direccionCliente" placeholder="Dirección del Cliente" value="<?=$datosCliente->direccionCliente;?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="telefonoCliente">Teléfono</label>
											<input type="text" class="form-control tel" id="telefonoCliente" name="telefonoCliente" placeholder="Teléfono" value="<?=$datosCliente->telefonoCliente;?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="emailCliente">Correo</label>
											<input type="text" class="form-control" id="emailCliente" name="emailCliente" placeholder="correo@servidor.com" value="<?=$datosCliente->emailCliente;?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="departamentoCliente">Departamento</label>
											<select class="select2 form-control" id="departamentoCliente" name="departamentoCliente">
												<?=$departamentos; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="municipioCliente">Municipio</label>
											<select class="select2 form-control" id="municipioCliente" name="municipioCliente">
												<?=$municipios; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="facturarConCliente">Facturar Con</label>
											<select class="select2 form-control" id="facturarConCliente" name="facturarConCliente">
												<option value="DUI" <?= ($datosCliente->facturarConCliente == "DUI") ? " selected " : "";  ?>>DUI</option>
												<option value="NIT" <?= ($datosCliente->facturarConCliente == "NIT") ? " selected " : "";  ?>>NIT</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="ClienteCliente">DUI</label>
											<input type="text" class="form-control dui" id="duiCliente" name="duiCliente" placeholder="000000-0" value="<?=$datosCliente->duiCliente;?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="documentoFacturacionCliente">Documento de Facturación</label>
											<select class="select2 form-control" id="documentoFacturacionCliente" name="documentoFacturacionCliente">
												<option value="FAC" <?= ($datosCliente->documentoFacturacionCliente == "FAC") ? " selected " : "";?>>FACTURA</option>
												<option value="CCF" <?= ($datosCliente->documentoFacturacionCliente == "CCF") ? " selected " : "";?>>CREDITO FISCAL</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="claveCliente">NIT</label>
											<input type="text" class="form-control nit" id="nitCliente" name="nitCliente" placeholder="0000-000000-000-0" value="<?=$datosCliente->nitCliente;?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="claveCliente">NRC</label>
											<input type="text" class="form-control" id="nrcCliente" name="nrcCliente" placeholder="000000-0" value="<?=$datosCliente->nrcCliente;?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="giroCliente">Giro del Cliente</label>
											<select type="text" class="form-control select2" id="giroCliente" name="giroCliente">
												<?=$categoriasClienteOption;?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="retieneIvaCliente">Retiene IVA</label>
											<select class="select2 form-control" id="retieneIvaCliente" name="retieneIvaCliente">
												<option value="0" <?= ($datosCliente->retieneIvaCliente) ? " selected " : "";?>>NO</option>
												<option value="1" <?= ($datosCliente->retieneIvaCliente) ? " selected " : "";?>>SI</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="retieneRentaCliente">Retiene Renta</label>
											<select class="select2 form-control" id="retieneRentaCliente" name="retieneRentaCliente">
												<option value="0" <?= ($datosCliente->retieneRentaCliente) ? " selected " : "";?>>NO</option>
												<option value="1" <?= ($datosCliente->retieneRentaCliente) ? " selected " : "";?>>SI</option>
											</select>
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="claveCliente">Referencia</label>
											<input type="text" class="form-control upper text-uppercase" id="referenciaCliente" name="referenciaCliente" placeholder="Referencia" value="<?=$datosCliente->referenciaCliente;?>">
										</div>
									</div>

								</div>

							</div>
							<!-- /.card-body -->

							<div class="card-footer">
								<input type="hidden" name="idCliente" id="idCliente" value="<?=$idCliente;?>">
								<input type="hidden" name="avanzadoCliente" id="avanzadoCliente" value="1">
								<input type="hidden" id="parametro" value="<?=$param?>">
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
