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
							<h3 class="card-title"><i class="<?=$icono?>"></i> <?=$titulo?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmEmpleadosDescuentoCuota" autocomplete="off">
							<div class="card-body">
								<div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div style="padding:5px;" class="text-center alert alert-<?=GblTraerConfiguracion('colorComponentes');?>"><h6>PERIODO VIGENTE: <?=$periodoVigente; ?></h6></div>
									</div>
                				</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="buscarEmpleado">Buscar Empleado <span class="text-danger">*</span></label>
											<input type="text" class="form-control" id="buscarEmpleado" name="buscarEmpleado" placeholder="Buscar empleado por nombre">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="empleadoEmpleadoDescuentoCuota">Nombre Compleado <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper text-uppercase" id="empleadoEmpleadoDescuentoCuota" name="empleadoEmpleadoDescuentoCuota" placeholder="Nombre completo" readonly value="<?=$empleadoEmpleadoDescuentoCuota; ?>">
										</div>
									</div>
									<input type="hidden" id="idEmpleadoEmpleadoDescuentoCuota" name="idEmpleadoEmpleadoDescuentoCuota" value="<?=$datosEmpleadosDescuentoCuota->idEmpleadoEmpleadoDescuentoCuota; ?>">                               								
								</div>
                                <div class="row">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="idInstitucionEmpleadoDescuentoCuota">Institución Financiera <span class="text-danger">*</span></label>
											<select class="form-control select2" id="idInstitucionEmpleadoDescuentoCuota" name="idInstitucionEmpleadoDescuentoCuota" >
                                                <option value="" >Seleccione</option>
                                                <?=$InstitucionFinancieraOpciones; ?>
                                            </select>
										</div>
									</div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="montoEmpleadoDescuentoCuota">Monto <span class="text-danger">*</span></label>
											<input type="text" class="form-control decimal" id="montoEmpleadoDescuentoCuota" name="montoEmpleadoDescuentoCuota" placeholder="0.00" value="<?=$datosEmpleadosDescuentoCuota->montoEmpleadoDescuentoCuota; ?>" >
										</div>
									</div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="numeroCuotasEmpleadoDescuentoCuota">Número de Cuotas <span class="text-danger">*</span></label>
											<input type="text" class="form-control numeric" id="numeroCuotasEmpleadoDescuentoCuota" name="numeroCuotasEmpleadoDescuentoCuota" placeholder="0" value="<?=$datosEmpleadosDescuentoCuota->numeroCuotasEmpleadoDescuentoCuota; ?>" >
										</div>
									</div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
										<div class="form-group">
											<label for="idPeriodoEmpleadoDescuentoCuota">Aplicado a <span class="text-danger">*</span></label>
											<select class="form-control select2" id="idPeriodoEmpleadoDescuentoCuota" name="idPeriodoEmpleadoDescuentoCuota" >
                                                <option value="" >Seleccione</option>
                                                <option value="<?=$idPeriodoPlanilla; ?>" <?php if($datosEmpleadosDescuentoCuota->idPeriodoEmpleadoDescuentoCuota==$idPeriodoPlanilla) echo "selected"; ?> >PERIODO ACTUAL</option>
                                                <option value="<?=$idPeriodoPlanilla+1; ?>" <?php if($datosEmpleadosDescuentoCuota->idPeriodoEmpleadoDescuentoCuota==($idPeriodoPlanilla+1)) echo "selected"; ?> >PERIODO SIGUIENTE</option>
                                            </select>
										</div>
									</div>                                     								
								</div>
                                <div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											<label for="descripcionEmpleadoDescuentoCuota">Descripción <span class="text-danger">*</span></label>
											<textarea class="form-control" id="descripcionEmpleadoDescuentoCuota" name="descripcionEmpleadoDescuentoCuota"><?=$datosEmpleadosDescuentoCuota->descripcionEmpleadoDescuentoCuota; ?></textarea>
										</div>
									</div>                                   								
								</div>
							</div>
							<!-- /.card-body -->

							<div class="card-footer">
								<input type="hidden" name="idEmpleadoDescuentoCuota" id="idEmpleadoDescuentoCuota" value="<?=$idEmpleadoDescuentoCuota?>">
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