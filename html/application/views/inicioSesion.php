<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?=base_url()."/".GblTraerConfiguracion("logoEmpresa");?>" rel="icon">

	<title>DigitalsPos | Iniciar Sesión</title>

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="<?=base_url();?>/vendors/fonts/google.css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/fontawesome-free/css/all.min.css">
	<!-- icheck bootstrap -->
	<link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/jquery-ui.min.css">
	<link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/adminlte.min.css">
	<link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
	<link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/keyboard.css">
	<!-- <link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/demokey.css"> -->
	<style media="screen">

	</style>
</head>
<body class="hold-transition <?=GblTraerConfiguracion("modoPlantilla");?> login-page">
	<div class="login-box">
		<div class="login-logo">
			<img src="<?=base_url()."/".GblTraerConfiguracion("logoEmpresa");?>" alt="" style="width:60%;">
		</div>
		<!-- /.login-logo -->
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg">Ingrese sus credenciales</p>

				<form id="FrmLogin" autocomplete="off">

					<!-- <ul class="tab-group">
						<li class="tab active"><a href="#UsuarioCredenciales">Sign Up</a></li>
						<li class="tab"><a href="#UsuarioCodigo">Log In</a></li>
					</ul> -->
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
					  <li class="nav-item">
					    <a class="nav-link active" id="pills-UsuarioCredenciales-tab" data-toggle="pill"
							 		href="#pills-UsuarioCredenciales" role="tab" aria-controls="pills-UsuarioCredenciales"
									aria-selected="true">Usuario/Clave
								</a>
					  </li>
					  <li class="nav-item">
					    <a class="nav-link" id="pills-UsuarioCodigo-tab" data-toggle="pill"
									href="#pills-UsuarioCodigo" role="tab" aria-controls="pills-UsuarioCodigo"
									aria-selected="false">Codigo
								</a>
					  </li>
					</ul>
					<!-- <nav class="nav nav-pills nav-justified">
					  <a class="nav-item nav-link active" href="#UsuarioCredenciales">Usuario</a>
					  <a class="nav-item nav-link" href="#UsuarioCodigo">Clave</a>
					</nav> -->
					<hr>
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane fade show active" id="pills-UsuarioCredenciales" role="tabpanel" aria-labelledby="pills-UsuarioCredenciales-tab">
							<div class="input-group mb-3">
 							 <input type="text" class="form-control" id="usuarioUsuario" name="usuarioUsuario" placeholder="Usuario">
 							 <div class="input-group-append">
 								 <div class="input-group-text">
 									 <span class="fa fa-keyboard usuario-opener"></span>
 								 </div>
 							 </div>
 						 </div>
 						 <div class="input-group mb-3">
 							 <input type="password" class="form-control" id="claveUsuario" name="claveUsuario" placeholder="Clave" autocomplete="off">
 							 <div class="input-group-append">
 								 <div class="input-group-text">
 									 <span class="fa fa-keyboard password-opener"></span>
 								 </div>
 							 </div>
 						 </div>
 						 <div class="row">
 							 <div class="col-12">
 								 <button type="button" id="btnIniciarSesion" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> btn-block">Iniciar Sesión</button>
 							 </div>
 						 </div>
						</div>
						<div class="tab-pane fade" id="pills-UsuarioCodigo" role="tabpanel" aria-labelledby="pills-UsuarioCodigo-tab">
							<div class="input-group mb-3">
								<input type="password" class="form-control" id="claveUsuario2" name="claveUsuario2" placeholder="Clave" autocomplete="off">
								<div class="input-group-append">
									<div class="input-group-text">
										<span class="fa fa-keyboard password2-opener"></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<button type="button" id="btnIniciarSesion2" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> btn-block">Iniciar Sesión</button>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-content">
					 <!-- <div id="UsuarioCredenciales">

					 </div>
					 <div id="UsuarioCodigo">

					</div> -->
				 </div><!-- tab-content -->


				</form>
				<!-- <p class="mb-1">
				<a href="forgot-password.html">No recuerdas la clave?</a>
			</p> -->
		</div>
		<!-- /.login-card-body -->
	</div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?=base_url();?>/vendors/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=base_url();?>/vendors/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url();?>/vendors/core/js/adminlte.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/numeric/jquery.numeric.js"></script>
<script src="<?=base_url();?>/vendors/plugins/mask/jquery.mask.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/select2/js/select2.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?=base_url();?>/vendors/core/js/main.js"></script>
<script src="<?=base_url();?>/vendors/core/js/jquery-ui-custom.min.js"></script>
<script src="<?=base_url();?>/vendors/core/js/jquery.keyboard.js"></script>
<!-- <script src="<?=base_url();?>/vendors/core/js/medokey.js"></script> -->
<script src="<?=base_url();?>/scripts/inicioSesion.js?p=<?=uniqid()  ?>"></script>
</body>
</html>
