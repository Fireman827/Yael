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
							<h3 class="card-title"><?=$titulo?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmContratos" autocomplete="off">
							<div class="card-body">								
                                <div class="row">
                					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                						<div class="text-center alert alert-<?=GblTraerConfiguracion('colorComponentes');?>" style="padding: 5px;"><h6>Datos del Empleado</h6></div>
                					</div>
                				</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="buscarEmpleado">Buscar Empleado <span class="text-danger">*</span></label>
											<input type="text" data-provide="typeahead"  class="form-control" id="buscarEmpleado" name="buscarEmpleado" placeholder="Nombre del empleado">
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="empleadoContrato">Nombre Compleado <span class="text-danger">*</span></label>
											<input type="text" class="form-control upper text-uppercase" id="empleadoContrato" name="empleadoContrato" placeholder="Nombre completo" value="<?=$empleadoContrato?>" readonly>
										</div>
									</div>
									<input type="hidden" id="idEmpleadoContrato" name="idEmpleadoContrato" value="<?=$datosContratos->idEmpleadoContrato;?>"> 
								</div> 
                                <div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="duiContrato">DUI <span class="text-danger">*</span></label>
											<input type="text" class="form-control dui" id="duiContrato" name="duiContrato" placeholder="DUI" value="<?=$datosContratos->duiContrato;?>" readonly >
										</div>
									</div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="nitContrato">NIT <span class="text-danger">*</span></label>
											<input type="text" class="form-control nit" id="nitContrato" name="nitContrato" placeholder="NIT" value="<?=$datosContratos->nitContrato;?>" readonly >
										</div>
									</div>                                    								
								</div>
								<div class="row">
                					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                						<div class="text-center alert alert-<?=GblTraerConfiguracion('colorComponentes');?>" style="padding: 5px;"><h6>Vigencia de Contrato</h6></div>
                					</div>
                				</div>             
                                <div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="desdeContrato">Desde <span class="text-danger">*</span></label>
											<input type="date" class="form-control" id="desdeContrato" name="desdeContrato" value="<?=$datosContratos->desdeContrato;?>" >
										</div>
									</div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="hastaContrato">Hasta <span class="text-danger">*</span></label>
											<input type="date" class="form-control" id="hastaContrato" name="hastaContrato" value="<?=$datosContratos->hastaContrato;?>">
										</div>
									</div>                                    								
								</div>
								<div class="row">
                					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                						<div class="text-center alert alert-<?=GblTraerConfiguracion('colorComponentes');?>" style="padding: 5px;"><h6>Horario</h6></div>
                					</div>
                				</div>                                
                                <div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											<label for="horarioContrato">Horario <span class="text-danger">*</span></label>
											<textarea type="text" class="form-control upper text-uppercase" rows="2" id="horarioContrato" name="horarioContrato" placeholder="Horario"><?=$datosContratos->horarioContrato;?></textarea>
										</div>
									</div> 							                                								
								</div>
								<div class="row">
                					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                						<div class="text-center alert alert-<?=GblTraerConfiguracion('colorComponentes');?>" style="padding: 5px;"><h6>Tipos de contrato disponibles</h6></div>
                					</div>
                				</div>								
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="horarioContrato">Tipos de contrato <span class="text-danger">*</span></label>
											<select class='select2' id='idContratoTipoContrato' name='idContratoTipoContrato' style="width: 100%;" >
												<option value="" >Seleccione</option>
												<?=$contratoTipoOpciones; ?>
											</select>
										</div>
									</div> 
								</div>
							</div>
							<!-- /.card-body -->

							<div class="card-footer">
								<input type="hidden" name="idContrato" id="idContrato" value="<?=$idContrato?>">
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