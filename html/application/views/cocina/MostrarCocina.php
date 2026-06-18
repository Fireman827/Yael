<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="margin-top:1%;">
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-4">
					<div class="card" style="min-height: 640px;">
						<!-- /.card-header -->
						<div class="card-body pre-scrollable" style="min-height: 740px;">

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class='' id="listaCuenta">
								<table id="tablaDetalleCliente" class="table table-condensed">
									<tbody id="listaCuentas">

									</tbody>
								</table>
								<table id="tablaDetalleCuenta" class="table table-condensed">
									<tbody id="listaCuentasDetalle">

									</tbody>
								</table>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->
						</div>
						<!-- <div class="card-footer dinamicos" id="totalProductos" >
							<div class="col-lg-12">
								<table class="table table-condensed">
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Total</td>
										<td id="totaltd" total="0.00"></td>
									</tr>
								</table>
							</div>
						</div> -->
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<div class="col-8">
					<div class="card pre-scrollable" style="min-height: 740px;">
						<!-- /.card-header -->
						<div class="card-body">
							<div class="row" id="listaCuentas">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<ul class="nav nav-tabs" id="listaCocinas">
										<?php if($impresoras):?>
											<li class="nav-item"><a class="nav-link pestana text-white active bg-primary" impresora="All" id="tabAll" href="#vistaAll" role="tab">TODO</a></li>
											<?php foreach($impresoras as $imp):?>
												<li class="nav-item"><a class="nav-link pestana text-white" impresora="<?=md5($imp->idImpresora)?>" id="tab<?=$imp->idImpresora?>" href="#vista<?=md5($imp->idImpresora)?>" role="tab"><?=$imp->nombreImpresora?></a></li>
											<?php endforeach;?>
										<?php endif;?>
									</ul>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="tab-content" id="contenedorOrdenes">
										<br>
										<div class="row tab-pane fade show active" role="tabpanel" id="vistaAll"></div>
										<?php if($impresoras):?>
											<?php foreach($impresoras as $imp):?>
												<div class="row tab-pane fade show" role="tabpanel" id="vista<?=md5($imp->idImpresora)?>"></div>
											<?php endforeach;?>
										<?php endif;?>
									</div>
								</div>
							</div>
							
							<!-- /.card-body -->
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
<!-- PARCHE PEDIDOS ONLINE -->
<script src="<?= base_url('vendors/core/js/cocina_online_patch.js') ?>"></script>

<!-- /.content-wrapper -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>


<!-- Modal -->
<div class='modal fade' id='xlModal' role='dialog' data-backdrop="static" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		</div>
  </div>
</div>
<!-- /.modal -->

<div class="modal fade" id="pagoServicioModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-lg btn-danger" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-lg btn-primary">Cobrar</button>
			</div>
		</div>
	</div>
</div>
