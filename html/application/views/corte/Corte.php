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
						<div class="card-body pre-scrollable" style="min-height: 500px;">
							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div id="botoneraPrimaria">
								<div class="col-lg-12">
									<div class="alert alert-<?=GblTraerConfiguracion('colorComponentes');?> tex-center">
										<h4><?=$titulo?></h4>
									</div>
									<!-- <button type="button" id="btnOrden" class="btn btn-success btn-block btn-lg"><i class='fa fa-utensils'></i> Ordenes</button> -->
								</div>
								<hr>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class='dinamicos' id="listaOrden">
								<?php if ($corte["idCorteCaja"] !=0) {?>
								<table class="table table-condensed">
									<tbody id="listaProductos">
											<tr>
												<td>Detalle</td>
												<td>Monto $</td>
											</tr>
											<tr>
												<td>Monto Apertura</td>
												<td><?="$".number_format($corte["montoApertura"],2,".",",");?></td>
											</tr>
											<tr>
												<td>Ventas Regulares</td>
												<td><?="$".number_format($corte["regular"],2,".",",");?></td>
											</tr>
											<tr>
												<td>Propina</td>
												<td><?="$".number_format($corte["propina"],2,".",",");?></td>
											</tr>
											<?php if(GblTraerConfiguracion('PrecioEspecial') == 'Si'):?>
											<tr>
												<td>Ventas Especiales</td>
												<td><?="$".number_format($corte["senorita"],2,".",",");?></td>
											</tr>
											<?php endif;?>
											<?php if(GblTraerConfiguracion('PrecioEmpleado') == 'Si'):?>
											<tr>
												<td>Ventas Empleado</td>
												<td><?="$".number_format($corte["empleado"],2,".",",");?></td>
											</tr>
											<?php endif;?>
											<!-- <tr>
												<td>Subtotal</td>
												<td><?="$".number_format($corte["subtotal"],2,".",",");?></td>
											</tr> -->
											<!-- <tr>
												<td>Total Venta</td>
												<td><?="$".number_format($corte["totalventa"],2,".",",");?></td>
											</tr> -->
											<?php if(GblTraerConfiguracion('ServicioSenorita') == 'Si'):?>
											<tr>
												<td>Servicios Señoritas</td>
												<td><?="$".number_format($corte["servicios"],2,".",",");?></td>
											</tr>
											<?php endif;?>
									</tbody>
								</table>
								<?php
								}
								?>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->


							<!--------------------------------------------------->
							<!--------------------------------------------------->
						</div>
						<div class="card-footer dinamicos" id="totalProductos">
							<?php if ($corte["idCorteCaja"] !=0) {?>
							<div class="col-lg-12">
								<table class="table table-condensed">
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Total</td>
										<td id="totaltd" total="<?=number_format($corte["total"],2,".","");?>"><?="$".number_format($corte["total"],2,".",",");?></td>
									</tr>
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Efectivo en Caja</td>
										<td> <input type="text" class="form-control decimal" id="efectivo" placeholder="0.00" value=""></td>
									</tr>
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Diferencia</td>
										<td id="diferenciatd" total="<?="-".number_format($corte["total"],2,".","");?>"><?="$-".number_format($corte["total"],2,".",",");?></td>
									</tr>
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td colspan="2"> <button type="button" class="btn btn-<?=GblTraerConfiguracion("colorComponentes");?> btn-block" id="imprimir_corte"><i class='fa fa-print'></i> Imprimir Corte</button> </td>
									</tr>
									<tr style="font-size:1.2rem; font-weight:bold;">
										<input type="hidden" id="idCorteCaja" value="<?=$corte["idCorteCaja"]?>">
										<td colspan="2"> <button type="button" class="btn btn-<?=GblTraerConfiguracion("colorComponentes");?> btn-block" id="finalizar_corte"><i class='fa fa-check'></i> Finalizar Corte</button> </td>
									</tr>
								</table>
							</div>
							<div class="col-lg-12">
								<!-- <button class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> btn-block btn-lg btnFinalizarOrden"><i class='fa fa-receipt'></i> </button> -->
							</div>
						<?php }else {
							?>
							<div class="col-lg-12">
								<table class="table table-condensed">
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Monto de Apertura</td>
										<td> <input type="text" class="form-control decimal" id="monto" placeholder="0.00" value=""></td>
									</tr>
									<tr style="font-size:1.2rem; font-weight:bold;">
										<input type="hidden" id="idCorteCaja" value="<?=$corte["idCorteCaja"]?>">
										<td colspan="2"> <button type="button" class="btn btn-<?=GblTraerConfiguracion("colorComponentes");?> btn-block" id="realizar_apertura"><i class='fa fa-check'></i> Realizar Apertura</button> </td>
									</tr>
								</table>
							</div>
							<?php
						} ?>
						</div>
						<!-- /.card-body -->
					</div>

					<!-- /.card -->
				</div>
				<div class="col-8">
					<div class="card pre-scrollable" style="min-height: 640px;">
						<!-- /.card-header -->
						<div class="card-body">
							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="botoneraOrdenes">
							<?php if ($corte["idCorteCaja"] !=0) {?>
								<button type="button" class="btn btn-<?=GblTraerConfiguracion("colorComponentes");?> btn-block" id="imprimir_cortes2"><i class='fa fa-print'></i> Imprimir Corte Ventas</button>
								<button type="button" class="btn btn-<?=GblTraerConfiguracion("colorComponentes");?> btn-block" id="imprimir_cortes"><i class='fa fa-print'></i> Imprimir Corte Servicios</button> <br>

							<?php }?>
								<!-- <div class="row">
									<div class="col-lg-6">
										<a id="btnLocal">
											<div class="small-box bg-success">
												<div class="inner">
													<h3>Local</h3>
													<p><i class="fa fa-plus"></i></p>
												</div>
												<div class="icon">
													<i class="ion ion-home"></i>
												</div>
											</div>
										</a>
									</div>
									<div class="col-lg-6">
										<a id="btnEspecial">
											<div class="small-box bg-warning">
												<div class="inner">
													<h3>Señorita</h3>
													<p><i class="fa fa-female"></i></p>
												</div>
												<div class="icon">
													<i class="fa fa-female"></i>
												</div>
											</div>
										</a>
									</div>
								</div> -->
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

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
<!-- /.content-wrapper -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
