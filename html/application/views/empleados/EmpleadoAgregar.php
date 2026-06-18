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
					<form id="FrmEmpleados" autocomplete="off">
					<!-- <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
						<div class="card-header">
							<h3 class="card-title"><i class="<?=$icono?>"></i> <?=$titulo?></h3>
						</div> -->
						<!-- /.card-header -->
						<!-- form start -->
							<!-- <div class="card-body"> -->
							<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
								<div class="card-header">
									<h3 class="card-title"> <i class="<?=$icono?>"></i> <?=$titulo?> - Datos Generales</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="form-group col-lg-4">
											<label>Nombres</label>
											<input type="text" placeholder="Nombres" class="form-control" id="nombreEmpleado" name="nombreEmpleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Apellidos</label>
											<input type="text" placeholder="Apellidos" class="form-control" id="apellidoEmpleado" name="apellidoEmpleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Domicilio</label>
											<input type="text" placeholder="Domicilio" class="form-control" id="direccionEmpleado" name="direccionEmpleado">
										</div>
		
									</div>
									<div class="row">
										<div class="form-group col-lg-4">
											<label>Residencia</label>
											<input type="text" placeholder="Residencia" class="form-control" id="residenciaEmpleado" name="residenciaEmpleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Nacionalidad</label>
											<input type="text" placeholder="Nacionalidad" class="form-control" id="nacionalidadEmpleado" name="nacionalidadEmpleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Fecha de Nacimento</label>
											<input type="date" class="form-control" id="fechaNacimientoEmpleado" name="fechaNacimientoEmpleado">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-lg-4">
												<label>Sexo</label>
												<select class="form-control select2" name="sexoEmpleado" id="sexoEmpleado">
													<option value="">Seleccionar</option>
													<option value="FEMENINO">FEMENINO</option>
													<option value="MASCULINO">MASCULINO</option>
											</select>
										</div>
										<div class="form-group col-lg-4">
												<label>Estado Civil</label>
												<select class="form-control select2" name="estadoCivilEmpleado" id="estadoCivilEmpleado">
													<option value="">Seleccionar</option>
													<option value="SOLTERO/A">SOLTERO/A</option>
													<option value="COMPROMETIDO/A">COMPROMETIDO/A</option>
													<option value="CASADO/A">CASADO/A</option>
													<option value="DIVORCIADO/A">DIVORCIADO/A</option>
													<option value="VIUDO/A">VIUDO/A</option>
											</select>
										</div>
										<div class="form-group col-lg-4">
											<label>Profesión u Oficio</label>
											<input type="text" placeholder="Profesión u Oficio" class="form-control" id="profesionOficioEmpleado" name="profesionOficioEmpleado">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-lg-4">
											<label>NIT</label>
											<input type="text" placeholder="NIT" class="form-control nit" id="nitEmpleado" name="nitEmpleado">
										</div>
										<div class="form-group col-lg-4">
											<label>DUI</label>
											<input type="text" placeholder="DUI" class="form-control dui" id="duiEmpleado" name="duiEmpleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Expedición DUI</label>
											<input type="text" placeholder="Expedido en" class="form-control" id="expedicionDuiEmpleado" name="expedicionDuiEmpleado">
										</div>
									</div>
									<div class="row">
										<div class="form-group col-lg-4">
											<label>Telefono 1</label>
											<input type="text" placeholder="Telefono 1" class="form-control tel" id="telefono1Empleado" name="telefono1Empleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Telefono 2</label>
											<input type="text" placeholder="Telefono 2" class="form-control tel" id="telefono2Empleado" name="telefono2Empleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Email</label>
											<input type="text" placeholder="Email" class="form-control" id="emailEmpleado" name="emailEmpleado">
										</div>
										<div class="form-group col-lg-4">
											<label>Tipo de Sangre</label>
											<!-- <input type="text" placeholder="Sangre" class="form-control" id="sangreEmpleado" name="sangreEmpleado"> -->
											<select class="form-control select2" id="sangreEmpleado" name="sangreEmpleado">
												<option value="">Seleccione</option>
												<option value="A+">A+</option>
												<option value="O+">O+</option>
												<option value="B+">B+</option>
												<option value="AB+">AB+</option>
												<option value="A-">A-</option>
												<option value="O-">O-</option>
												<option value="B-">B-</option>
												<option value="AB-">AB-</option>
											</select>
										</div>
									</div>
								
								</div>
							</div>
							<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
								<div class="card-header">
									<h3 class="card-title"> <i class="<?=$icono?>"></i> <?=$titulo?> - Datos Laborales</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<!-- <div class="form-group has-info col-lg-3">
											<label>Id de Reloj</label>
											<input type="text" placeholder="id empleado reloj" class="form-control decimal" id="id_empleado_reloj" name="id_empleado_reloj">
										</div> -->
										<div class="form-group has-info col-lg-3">
											<label>Cargo </label>
											<select class="form-control select2" id='idCargoEmpleado' name='idCargoEmpleado' >
												<option value=''>Seleccione</option>
												<?=$cargosOpciones; ?>
											</select>
										</div>
										<div class="form-group has-info col-lg-3">
											<label>Salario base</label>
											<input type="text" placeholder="Salario base" class="form-control decimal" id="salarioBaseEmpleado" name="salarioBaseEmpleado">
										</div>
										<div class="form-group has-info col-lg-3">
											<label>Departamento </label>
											<div>
												<select name='departamentoEmpleado' id='departamentoEmpleado' class="form-control select2" style='width:100%;'>
													<option value=''>Seleccione</option>
													<option value='VENTAS'>VENTAS</option>
												</select>
											</div>
										</div>
										<div class="form-group has-info col-lg-3">
											<label>Modalidad</label>							
												<select class="form-control select2" id="modalidadEmpleado" name="modalidadEmpleado"  style='width:100%;'>
													<option value="">Seleccione</option>
													<option value="PERMANENTE">PERMANENTE</option>
													<option value="TEMPORAL">TEMPORAL</option>
												</select>									
										</div>
									</div>
									<div class="row">
										<div class="form-group has-info col-lg-3">
											<label>Documento de Contratacion</label>
											<input type="text" placeholder="Documento" class="form-control" id="documentoEmpleado" name="documentoEmpleado">
										</div>
										<div class="form-group has-info col-lg-3">
											<label>Fecha de Ingreso</label>
											<input type="date" class="form-control" id="fechaContratacionEmpleado" name="fechaContratacionEmpleado">
										</div>
										<div class="form-group has-info col-lg-3">
											<label>Fecha de cese</label>
											<input type="date" class="form-control" id="fechaCeseEmpleado" name="fechaCeseEmpleado">
										</div>
										<div class="col-lg-3">
											<div class="form-group">
												<label >Forma de pago </label>
												<select id="formaPagoEmpleado" name="formaPagoEmpleado" class="form-control select2" style='width:100%;'>
													<option value="">Seleccione</option>
													<option value="EFECTIVO">EFECTIVO</option>
													<option value="CHEQUE">CHEQUE</option>
													<option value="DEPOSITO">DEPÓSITO</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group has-info col-lg-3"><br>
											<div class="icheck-<?=GblTraerConfiguracion('colorComponentes')?> d-inline">
												<input type="checkbox" id="afp" name="afp" >
												<label for="afp">Cotiza AFP</label>
											</div>										
											<input type="hidden" id="afpEmpleado" name="afpEmpleado" value="NO">
										</div>
										<div class="form-group has-info col-lg-3"><br>
											<div class="icheck-<?=GblTraerConfiguracion('colorComponentes')?> d-inline">
												<input type="checkbox" id="isss" name="isss" >
												<label for="isss">Cotiza ISSS</label>
											</div>
											<input type="hidden" id="isssEmpleado" name="isssEmpleado" value="NO">
										</div>
										<div class="form-group has-info col-lg-3"><br>																	
											<div class="icheck-<?=GblTraerConfiguracion('colorComponentes')?> d-inline">
												<input type="checkbox" id="renta" name="renta" >
												<label for="renta">Cotiza Renta</label>
											</div>
											<input type="hidden" id="rentaEmpleado" name="rentaEmpleado" value="NO">
										</div>
										<div class="form-group has-info col-lg-3">
											<label>Afiliado a</label>
											<select class="form-control select2" id="afiliadoAfpEmpleado" name="afiliadoAfpEmpleado"  style='width:100%;'>
												<option value="">Seleccione</option>
												<option value="Crecer">Crecer</option>
												<option value="Confia">Confia</option>
											</select>
										</div>
									</div>
									<div class="row">
										<div class="form-group has-info col-lg-3"><br>
											<div class="icheck-<?=GblTraerConfiguracion('colorComponentes')?> d-inline">
												<input type="checkbox" id="hacerUsuario" name="hacerUsuario" >
												<label for="hacerUsuario">Volver Usuario</label>
											</div>										
											<input type="hidden" id="hacerUsuarioEmpleado" name="hacerUsuarioEmpleado" value="NO">
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 divDatosUsuario">
											<div class="form-group">
												<label for="usuarioUsuario">Usuario</label>
												<input type="text" class="form-control" id="usuarioUsuario" name="usuarioUsuario" placeholder="Usuario">
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 divDatosUsuario">
											<div class="form-group">
												<label for="claveUsuario">Clave</label>
												<input type="password" class="form-control" id="claveUsuario" name="claveUsuario" placeholder="Clave">
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 divDatosUsuario">
											<div class="form-group">
												<label for="RolUsuario">Rol</label>
												<select class="form-control select2" name="rolUsuario" id="rolUsuario">
													<option value="">Sin asignar</option>
													<?php if ($roles !== false) : ?>
														<?php foreach ($roles as $rol) : ?>
															<option value="<?= $rol->idRol ?>"><?= $rol->nombreRol; ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</div>
										</div>
										<!-- <div class="col-lg-3">
											<div class="form-group">
												<label >Banco </label>
												<select id="institucionEmpleado" name="institucionEmpleado" class="form-control select2">
													<option value="">Seleccione</option>									
												</select>
											</div>
										</div>
										<div class="form-group has-info col-lg-3 depo">
											<label>Cuenta</label>
											<input type="text" placeholder="Cuenta" class="form-control" id="cuentaEmpleado" name="cuentaEmpleado">
										</div> -->
									</div>

								</div>
							</div>
							<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
								<div class="card-header">
									<h3 class="card-title"> <i class="<?=$icono?>"></i> <?=$titulo?> - PERSONAS QUE DEPENDEN ECONÓMICAMENTE DE EL(A) TRABAJADOR(A)</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="form-group col-lg-4">
											<label>Nombre </label>
											<input type="text" placeholder="Nombre" class="form-control" id="nombre_f" name="nombre_f">
										</div>
										<div class="form-group col-lg-4">
											<label>Apellido</label>
											<input type="text" placeholder="Apellido" class="form-control" id="apellido_f" name="apellido_f">
										</div>
										<div class="form-group col-lg-2">
											<label>Parentesco</label>
											<input type="text" placeholder="Parentesco" class="form-control" id="parentesco_f" name="parentesco_f">
										</div>
										<div class="col-lg-2">
											<button type="button" id='AgregarEmpleadoFamilia' name='AgregarEmpleadoFamilia' class='btn btn-primary btn-block' style="margin-top: 31px;">Agregar</button>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<table class="table table-bordered table-hover table-sm " id="tablaEmpleadoFamilia">
												<thead class="bg-<?=GblTraerConfiguracion('colorComponentes');?>">
													<tr>
														<th class="col-lg-4">Nombre</th>
														<th class="col-lg-4">Apellido</th>
														<th class="col-lg-2">Parentesco</th>
														<th class="col-lg-2">Accion</th>
													</tr>
												</thead>
												<tbody id="lfamilia">
		
												</tbody>
											</table>
										</div>
									</div>
									<input type="hidden" name="familiaresEmpleado" id="familiaresEmpleado" value="">				
								<!-- </div> -->
								<!-- /.card-body -->
								<div class="card-footer">
									<input type="hidden" name="idEmpleado" id="idEmpleado" value="0">
									<button type="submit" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Guardar</button>
								</div>
								</div>
							</div>

							<!-- </div> -->
							<!-- /.card -->
						</form>
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
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>