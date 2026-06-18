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
					<!-- <h1>404 Error Page</h1> -->
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">404 Error Page</li> -->
					</ol>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="error-page">
				<div >
				<h2><i class="fas fa-exclamation-triangle text-warning"></i> No se ha encontrado la pagina.</h2>
				<p>
					La pagina solicitada no existe.
					<hr>
					<button type="button" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?>" onclick="history.back();"><i class="fa fa-arrow-circle-left"></i> Regresar </button>
				</p>

			</div>
			<!-- /.error-content -->
		</div>
		<!-- /.error-page -->
	</section>
	<!-- /.content -->
</div>
