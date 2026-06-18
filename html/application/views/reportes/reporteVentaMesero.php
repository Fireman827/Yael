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
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url(); ?>">Inicio</a></li>
						<!-- <li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url() . $controlador; ?>"><?= ucfirst($controlador); ?></a></li> -->
						<li class="breadcrumb-item font-weight-bold active"><?= $titulo; ?></li>
					</ol>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section><!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-<?= GblTraerConfiguracion('colorComponentes'); ?>">
						<div class="card-header">
							<h3 class="card-title"><?= $titulo ?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmReporte" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
										<div class="form-group">
											<label for="fechaInicial">Fecha de Inicio: <span class="text-danger">*</span></label>
											<?php
											$fecha_actual = date("Y-m-d");
											?>
											<input type="date" required class="form-control" id="fechaInicio" name="fechaInicio" value="<?=date("Y-m-d",strtotime($fecha_actual."- 1 month"))?>">
										</div>
									</div>
									<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
										<div class="form-group">
											<label for="fechaFinal">Fecha Final: <span class="text-danger">*</span></label>
											<input type="date" required class="form-control" id="fechaFinal" name="fechaFinal" value="<?=date("Y-m-d")?>">
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<label for="tipoReporte">Mesero</label>
											<select class="form-control select2" name="idUsuario" id="idUsuario">
											<?php

												foreach ($meseros as $key)
												{
												$idUsuario = $key->idUsuario;
												$nombreUsuario = $key->nombreUsuario;
												echo "<option value='".$idUsuario."'>".$nombreUsuario."</option>";
												}
											?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer text-right">
    <div class="d-flex align-items-center" style="gap:10px;">

        <!-- PDF Admin -->
        <button type="button" id="btnSubmit" 
                class="btn btn-danger btn-sm">
            <i class="fa fa-file-pdf"></i> PDF Admin
        </button>

        <!-- Excel Admin -->
        <button type="button" id="btnExcelAdmin" 
                class="btn btn-primary btn-sm">
            <i class="fa fa-file-excel"></i> Excel Admin
        </button>

        <!-- Excel Contador -->
        <button type="button" id="btnExcelContador" 
                class="btn btn-success btn-sm">
            <i class="fa fa-file-excel"></i> Excel Contador
        </button>

    </div>
</div>

						</form>
						<!-- </form> -->
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
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
