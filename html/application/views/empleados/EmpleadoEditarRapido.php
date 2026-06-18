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
							<h3 class="card-title"><i class="<?=$icono?>"></i> <?=$titulo?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmEmpleados" autocomplete="off">
							<div class="card-body">
                            	<div class="row">
									<div class="form-group col-lg-12" style="margin-bottom:-6px;">
										<div style="height:40px;" class="text-center alert alert-info"><h4 style="margin-top: -4px;">Datos Generales</h4></div>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-6">
										<label>Nombres</label>
										<input type="text" placeholder="Nombres" class="form-control" id="nombreEmpleado" name="nombreEmpleado" value="<?=$datosEmpleados->nombreEmpleado; ?>">
									</div>
									<div class="form-group col-6">
										<label>Apellidos</label>
										<input type="text" placeholder="Apellidos" class="form-control" id="apellidoEmpleado" name="apellidoEmpleado" value="<?=$datosEmpleados->apellidoEmpleado; ?>">
									</div>
                                    
								</div>
								<div class="row">
                                    <div class="form-group col-6">
                                        <label>Domicilio</label>
                                        <input type="text" placeholder="Domicilio" class="form-control" id="direccionEmpleado" name="direccionEmpleado" value="<?=$datosEmpleados->direccionEmpleado; ?>">
                                    </div>
                                    <div class="form-group col-3">
                                            <label>Sexo</label>
                                            <select class="form-control select2" name="sexoEmpleado" id="sexoEmpleado">
                                                <option value="">Seleccionar</option>
                                                <option value="FEMENINO" <?php if($datosEmpleados->sexoEmpleado=="FEMENINO") echo "selected"; ?> >FEMENINO</option>
                                                <option value="MASCULINO" <?php if($datosEmpleados->sexoEmpleado=="MASCULINO") echo "selected"; ?> >MASCULINO</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-3">
                                            <label>Estado Civil</label>
                                            <select class="form-control select2" name="estadoCivilEmpleado" id="estadoCivilEmpleado">
                                                <option value="">Seleccionar</option>
                                                <option value="SOLTERO/A" <?php if($datosEmpleados->estadoCivilEmpleado=="SOLTERO/A") echo "selected"; ?> >SOLTERO/A</option>
                                                <option value="COMPROMETIDO/A" <?php if($datosEmpleados->estadoCivilEmpleado=="COMPROMETIDO/A") echo "selected"; ?> >COMPROMETIDO/A</option>
                                                <option value="CASADO/A" <?php if($datosEmpleados->estadoCivilEmpleado=="CASADO/A") echo "selected"; ?> >CASADO/A</option>
                                                <option value="DIVORCIADO/A" <?php if($datosEmpleados->estadoCivilEmpleado=="DIVORCIADO/A") echo "selected"; ?> >DIVORCIADO/A</option>
                                                <option value="VIUDO/A" <?php if($datosEmpleados->estadoCivilEmpleado=="VIUDO/A") echo "selected"; ?> >VIUDO/A</option>
                                        </select>
                                    </div>
								</div>
								<div class="row">
									<div class="form-group col-3">
										<label>NIT</label>
										<input type="text" placeholder="NIT" class="form-control nit" id="nitEmpleado" name="nitEmpleado" value="<?=$datosEmpleados->nitEmpleado; ?>">
									</div>
									<div class="form-group col-3">
										<label>DUI</label>
										<input type="text" placeholder="DUI" class="form-control dui" id="duiEmpleado" name="duiEmpleado" value="<?=$datosEmpleados->duiEmpleado; ?>">
									</div>
                                    <div class="form-group col-3">
                                        <label>Telefono 1</label>
                                        <input type="text" placeholder="Telefono 1" class="form-control tel" id="telefono1Empleado" name="telefono1Empleado" value="<?=$datosEmpleados->telefono1Empleado; ?>">
                                    </div>
                                    <div class="form-group col-3">
                                        <label>Email</label>
                                        <input type="text" placeholder="Email" class="form-control" id="emailEmpleado" name="emailEmpleado" value="<?=$datosEmpleados->emailEmpleado; ?>">
                                    </div>
								</div>
								<div class="row">
									<div class="form-group has-info col-lg-3">
										<label>Cargo </label>
                                        <select class="form-control select2" id='idCargoEmpleado' name='idCargoEmpleado' >
                                            <option value=''>Seleccione</option>
										    <?=$cargosOpciones; ?>
                                        </select>
									</div>
									<div class="form-group has-info col-lg-3">
										<label>Salario base</label>
										<input type="text" placeholder="Salario base" class="form-control decimal" id="salarioBaseEmpleado" name="salarioBaseEmpleado" value="<?=$datosEmpleados->salarioBaseEmpleado; ?>">
									</div>
								</div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" name="idEmpleado" id="idEmpleado" value="<?=$idEmpleado; ?>">
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
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>