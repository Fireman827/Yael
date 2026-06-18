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
						<form id="FrmEmpleadosDescuento" autocomplete="off">
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
											<label for="empleadoEmpleadoDescuento">Nombre Compleado <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper text-uppercase" id="empleadoEmpleadoDescuento" name="empleadoEmpleadoDescuento" placeholder="Nombre completo" readonly>
										</div>
									</div>
									<input type="hidden" id="idEmpleadoEmpleadoDescuento" name="idEmpleadoEmpleadoDescuento" value="">                               								
								</div>
                                <div class="row">
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="tipoEmpleadoDescuento">Tipo <span class="text-danger">*</span></label>
											<select class="form-control select2" id="tipoEmpleadoDescuento" name="tipoEmpleadoDescuento" >
                                                <option value="" >Seleccione</option>
                                                <option value="DESCUENTO" >DESCUENTO</option>
                                                <option value="ANTICIPO" >ANTICIPO</option>
                                            </select>
										</div>
									</div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="montoEmpleadoDescuento">Monto <span class="text-danger">*</span></label>
											<input type="text" class="form-control decimal" id="montoEmpleadoDescuento" name="montoEmpleadoDescuento" placeholder="0.00" >
										</div>
									</div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="idPeriodoEmpleadoDescuento">Aplicado a <span class="text-danger">*</span></label>
											<select class="form-control" id="idPeriodoEmpleadoDescuento" name="idPeriodoEmpleadoDescuento" >
                                                <option value="" >Seleccione</option>
                                                <option value="<?=$idPeriodoPlanilla; ?>" >PERIODO ACTUAL</option>
                                                <option value="<?=$idPeriodoPlanilla+1; ?>" >PERIODO SIGUIENTE</option>
                                            </select>
										</div>
									</div>                                    								
								</div>
                                <div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											<label for="descripcionEmpleadoDescuento">Descripción <span class="text-danger">*</span></label>
											<textarea class="form-control" id="descripcionEmpleadoDescuento" name="descripcionEmpleadoDescuento"></textarea>
										</div>
									</div>                                   								
								</div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" name="idEmpleadoDescuento" id="idEmpleadoDescuento" value="0">
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