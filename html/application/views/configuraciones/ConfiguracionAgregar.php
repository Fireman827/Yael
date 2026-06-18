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
						<form id="FrmConfiguraciones" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nombreEmisor">Nombre del Contribuyente</label>
											<input type="text" class="form-control upper text-uppercase" id="nombreEmisor" name="nombreEmisor" placeholder="Nombre" value="<?=$emisor["nombre"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nombreComercialEmisor">Nombre Comercial o Razon Social</label>
											<input type="text" class="form-control upper text-uppercase" id="nombreComercialEmisor" name="nombreComercialEmisor" placeholder="Nombre Comercial" value="<?=$emisor["nombre_comercial"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nitEmisor">NIT</label>
											<input type="text" class="form-control nit" id="nitEmisor" name="nitEmisor" placeholder="0000-000000-000-0" value="<?=$emisor["nit"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="nrcEmisor">NRC</label>
											<input type="text" class="form-control" id="nrcEmisor" name="nrcEmisor" placeholder="000000-0" value="<?=$emisor["nrc"]?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="giroEmisor">Giro</label>
											<select class="select2 form-control" id="giroEmisor" name="giroEmisor">
												<?=$giros; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="direccionEmisor">Dirección</label>
											<input type="text" class="form-control upper text-uppercase" id="direccionEmisor" name="direccionEmisor" placeholder="Dirección" value="<?=$emisor["complemento"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="departamentoEmisor">Departamento</label>
											<select class="select2 form-control" id="departamentoEmisor" name="departamentoEmisor">
												<?=$departamentos; ?>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="municipioEmisor">Municipio</label>
											<select class="select2 form-control" id="municipioEmisor" name="municipioEmisor">
												<?=$municipios; ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="emailEmisor">Correo</label>
											<input type="text" class="form-control" id="correoEmisor" name="correoEmisor" placeholder="correo@servidor.com" value="<?=$emisor["correo"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="telefonoEmisor">Teléfono</label>
											<input type="text" class="form-control tel" id="telefonoEmisor" name="telefonoEmisor" placeholder="Teléfono" value="<?=$emisor["telefono"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="cobroPropina">Propina</label>
											<select class="select2 form-control" id="cobroPropina" name="cobroPropina">
												<option value="No" <?= $emisor["cobroPropina"] == "No" ? " selected " : ""; ?>>NO</option>
												<option value="Si" <?= $emisor["cobroPropina"] == "Si" ? " selected " : ""; ?>>SI</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 propina" <?= $emisor["cobroPropina"] == "No" ? "hidden" : ""; ?>>
										<div class="form-group">
											<label for="valorPropina">Valor propina(%)</label>
											<input type="text" class="form-control numeric" id="valorPropina" name="valorPropina" placeholder="" value="<?=$emisor["valorPropina"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="facturacionElectronica">Facturacion Electronica</label>
											<select class="select2 form-control" id="facturacionElectronica" name="facturacionElectronica">
												<option value="No" <?= $emisor["facturacionElectronica"] == "No" ? " selected " : ""; ?>>NO</option>
												<option value="Si" <?= $emisor["facturacionElectronica"] == "Si" ? " selected " : ""; ?>>SI</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 feOptions" <?= $emisor["facturacionElectronica"] == "No" ? "hidden" : ""; ?>>
										<div class="form-group">
											<label for="entornoFE">Entorno Facturacion Electronica</label>
											<select class="select2 form-control" id="entornoFE" name="entornoFE">
												<option value="prueba" <?= $emisor["entornoFE"] == "prueba" ? " selected " : ""; ?>>Prueba</option>
												<option value="produccion" <?= $emisor["entornoFE"] == "produccion" ? " selected " : ""; ?>>Produccion</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row feOptions prueba" <?= $emisor["facturacionElectronica"] == "No"  || $emisor["entornoFE"] == "produccion" ? "hidden" : ""; ?>>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="usuario">Usuario plataforma MH</label>
											<input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario plataforma de hacienda" value="<?=$emisor["usuario"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="claveApi">Clave API</label>
											<input type="text" class="form-control" id="claveApi" name="claveApi" placeholder="Clave del API de transmision" value="<?=$emisor["claveApi"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="clave">Clave Privada</label>
											<input type="text" class="form-control" id="clave" name="clave" placeholder="Clave privada del certificado de firma Electronica" value="<?=$emisor["clave"]?>">
										</div>
									</div>
								</div>
								<div class="row feOptions produccion" <?= $emisor["facturacionElectronica"] == "No" || $emisor["entornoFE"] == "prueba" ? "hidden" : ""; ?>>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="usuario">Usuario plataforma MH</label>
											<input type="text" class="form-control" id="usuarioP" name="usuarioP" placeholder="Usuario plataforma de hacienda" value="<?=$emisor["usuarioP"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="claveApi">Clave API</label>
											<input type="text" class="form-control" id="claveApiP" name="claveApiP" placeholder="Clave del API de transmision" value="<?=$emisor["claveApiP"]?>">
										</div>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="clave">Clave Privada</label>
											<input type="text" class="form-control" id="claveP" name="claveP" placeholder="Clave privada del certificado de firma Electronica" value="<?=$emisor["claveP"]?>">
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" value="<?php echo $idSucursal; ?>" id="idSucursal" name="idSucursal">
								<button type="button" id="guardarDatos" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Guardar</button>
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
