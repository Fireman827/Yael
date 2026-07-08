<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="margin-top:1%;">
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-4">
					<div class="card" style="min-height: <?=GblTraerConfiguracion("height-panel")?>;">
						<!-- /.card-header -->
						<div class="card-body pre-scrollable" style="min-height: <?=GblTraerConfiguracion("height-panel-interno"); ?>">
							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div id="botoneraPrimaria">
								<!-- Ordenes -->
								<?php if($this->session->idUsuario != $idUsuarioCorte){?>
									<?php if(GblTraerConfiguracion("Cuentas") == "Si"){?>
									<div class="col-lg-12">
										<button type="button" id="btnOrden" class="btn btn-success btn-block btn-lg"><i class='fa fa-utensils'></i> Ordenes</button>
									</div>
									<hr>
										<?php }?>
								<?php }else{?>
									<div class="col-lg-12">
										<button type="button" id="btnOrden" class="btn btn-success btn-block btn-lg"><i class='fa fa-utensils'></i> Ordenes</button>
									</div>
									<hr>
								<?php };?>
								 <!-- Fin Ordenes -->

								 <!-- Cover -->
								<?php if($this->session->idUsuario == $idUsuarioCorte){?>
									<?php if(GblTraerConfiguracion("Cover") == "Si"){?>
									<div class="col-lg-12">
										<button type="button" id="btnCover" class="btn btn-info btn-block btn-lg"><i class='fa fa-hand-holding-usd'></i> Cover</button>
									</div>
									<hr>
										<?php }?>
								<?php }?>
								<!-- Fin Cover  -->

								<!-- Servicios -->
								<?php if($this->session->idUsuario == $idUsuarioCorte){?>
									<?php if(GblTraerConfiguracion("ServicioSenorita") == "Si"){?>
									<div class="col-lg-12">
										<button type="button" id="btnServicio" class="btn btn-default btn-block btn-lg"><i class='fa fa-user-clock'></i> Servicios</button>
									</div>
									<hr>
										<?php }?>
								<?php }?>
								<!-- Fin Servicio -->

								<!-- <div class="col-lg-12" hidden>
									<button type="button" id="btnMesa" class="btn btn-warning btn-block btn-lg"><i class='fa fa-th-large'></i> Mesas</button>
								</div>
								<hr> -->
								<!-- Cuentas -->
								<?php if($this->session->idUsuario != $idUsuarioCorte){?>
									<?php if(GblTraerConfiguracion("Cuentas") == "Si"){?>
									<div class="col-lg-12">
										<button type="button" id="btnCuenta" class="btn btn-primary btn-block btn-lg"><i class='fa fa-receipt'></i> Cuentas</button>
									</div>
									<hr>
										<?php }?>
								<?php  }
								else{?>
									<div class="col-lg-12">
										<button type="button" id="btnCuenta" class="btn btn-primary btn-block btn-lg"><i class='fa fa-receipt'></i> Cuentas</button>
									</div>
									<hr>
								<?php };?>
								<!-- Fin Cuentas -->

								<!-- Movimientos -->
								<?php if($this->session->idUsuario == $idUsuarioCorte){?>
									<?php if(GblTraerConfiguracion("Movimientos") == "Si"){?>
									<div class="col-lg-12">
										<button type="button" id="btnMovimiento" class="btn btn-danger btn-block btn-lg"><i class='fa fa-exchange-alt'></i> Movimientos</button>
									</div>
									<hr>
										<?php }?>
								<?php }?>
								<!-- Fin Movimientos -->
								<?php if($this->session->idUsuario == $idUsuarioCorte){?>
									<div class="col-lg-12">
										<a class="btn btn-primary btn-block btn-lg" href="<?=base_url()?>CorteAdmin"><i class='fa fa-money-check-alt'></i> Corte</a>
									</div>
								<?php }?>

							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class='dinamicos' id="detalleCuenta">
								<label for="tablaDetalleCliente">Datos Generales</label>
								<table id="tablaDetalleCliente" class="table table-condensed table-sm">
									<tbody></tbody>
								</table>
								<hr>
								<label for="tablaDetalleCliente">Detalle Cuenta</label>
								<table id="tablaDetalleCuenta" class="table table-condensed table-sm">
									<thead>
										<tr>
											<th class="col-1">Cant.</th>
											<th class="col-7">Producto</th>
											<th class="col-1">Precio</th>
											<th class="col-1">Reg.</th>
											<th class="col-1">Inspec.</th>
											<th class="col-1">Selec.</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
								<table id="tablaUnirCuenta" class="table table-condensed table-sm">
									<tbody>
									</tbody>
								</table>
							</div>
							<!-- <div class='dinamicos' id="detalleCuenta">
							</div> -->
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class='dinamicos' id="listaOrden">
								<table class="table table-condensed table-sm">
									<thead>
										<tr>
											<th class="col-1">Cant.</th>
											<th class="col-7">Producto</th>
											<th class="col-1">Precio</th>
											<th class="col-1">Inspec.</th>
											<th class="col-1">Reg.</th>
										</tr>
									</thead>
									<tbody id="listaProductos">

									</tbody>
								</table>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class='dinamicos' id="listaServicio">
								<table class="table table-condensed">
									<tbody id="listaServicios">

									</tbody>
								</table>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class='dinamicos' id="listaCover">
								<table class="table table-condensed">
									<tbody id="listaCovers">

									</tbody>
								</table>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->
						</div>
						<div class="card-footer dinamicos" id="totalProductos" >
							<div class="col-lg-12">
								<input type="hidden" id="idPedidoGuardado" value="">
								<table class="table table-condensed">
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Total</td>
										<td id="totaltd" total="0.00"></td>
									</tr>
								</table>
							</div>
							<div class="row">

								<?php if(GblTraerConfiguracion('PrecioEspecial') == 'Si' || GblTraerConfiguracion('PrecioEmpleado') == 'Si'):?>
										<div class="col-lg-4">
											<button type="button" id="btnRegresarMenu" class="btn btn-primary btn-block "><i class='fa fa-arrow-left'></i> Regresar</button>
										</div>
										<div class="col-lg-4">
											<button type="button" id="btnAgrupar" class="btn btn-primary btn-block "><i class="fas fa-dot-circle" aria-hidden="true"></i> Agrupar</button>
										</div>
										<div class="col-lg-4">
											<button class="btn btn-success btn-block  btnFinalizarOrden"><i class='fa fa-receipt'></i> Finalizar</button>
										</div>
									<?php endif;?>
									<?php if(GblTraerConfiguracion('PrecioEspecial') == 'No' && GblTraerConfiguracion('PrecioEmpleado') == 'No'):?>
										<div class="col-lg-6">
											<button type="button" id="btnAgrupar" class="btn btn-primary btn-block btn-lg"><i class="fas fa-dot-circle" aria-hidden="true"></i> Agrupar</button>
										</div>
										<div class="col-lg-6">
											<button class="btn btn-success btn-block btn-lg btnFinalizarOrden"><i class='fa fa-receipt'></i> Finalizar</button>
										</div>
									<?php endif;?>
							</div>
						</div>
						<div class="card-footer dinamicos" id="totalServicios" >
							<div class="col-lg-12">
								<table class="table table-condensed">
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Total</td>
										<td id="totaltds"></td>
									</tr>
								</table>
							</div>
							<div class="col-lg-12">
								<button class="btn btn-success btn-block btn-lg btnFinalizarServicio"><i class='fa fa-receipt'></i> Finalizar</button>
							</div>
						</div>
						<div class="card-footer dinamicos" id="totalCover" >
							<div class="col-lg-12">
								<table class="table table-condensed">
									<tr style="font-size:1.2rem; font-weight:bold;">
										<td>Total</td>
										<td id="totaltcv"></td>
									</tr>
								</table>
							</div>
							<div class="col-lg-12">
								<button class="btn btn-success btn-block btn-lg"><i class='fa fa-receipt'></i> Finalizar</button>
							</div>
						</div>
						<?php if($this->session->idUsuario == $idUsuarioCorte){?>
						<div class="card-footer dinamicos" id="accionesCuentaDetalle" >
							<div class="row">
								<div class="col-12">
									<div class="row">

										<div class="col-4 ">
											<button class="btn btn-primary btn-block btn-lg accionCuentaDetalle" tipo="dar" ><i class='fa fa-plus'></i> Regalia</button>
										</div>
										<div class="col-4 ">
											<button class="btn btn-warning btn-block btn-lg accionCuentaDetalle" tipo="quitar" ><i class='fa fa-minus'></i> Regalia</button>
										</div>
										<div class="col-4">
											<button class="btn btn-danger btn-block btn-lg accionCuentaDetalle" tipo="borrar" ><i class='fa fa-trash'></i> Borrar</button>
										</div>

									</div>
								</div>
								<div class="col-9"></div>
							</div>
						</div>
						<?php }?>
						<div class="card-footer dinamicos" id="accionUnirCuenta" >
							<div class="col-lg-12">
								<button class="btn btn-success btn-block btn-lg btnUnirCuenta"><i class='fa fa-compress-arrows-alt'></i> Unir Cuentas</button>
								<input type="hidden" id="idCuentaPrincipalUnir" value="">
							</div>
						</div>

						<!-- /.card-body -->
					</div>

					<!-- /.card -->
				</div>
				<div class="col-8">
					<div class="card pre-scrollable" style="min-height: <?=GblTraerConfiguracion("height-panel")?>;">

						<!-- /.card-header -->
						<div class="card-body">
							<div class="dinamicos mb-1" id="botoneraHome">
								<div class="row rounded p-1">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="row">
											<input type="hidden" id="tipoPrecioActual" value="regular">
											<input type="hidden" id="AgregarCuentaLlevarLocal" value="0">
											<input type="hidden" id="CobrarCuentaFinal" value="0">
											<input type="hidden" id="idCorte" value="<?=$idCorte?>">
											<input type="hidden" id="idUsuarioCorte" value="<?=$idUsuarioCorte?>">
											<input type="hidden" id="idUsuarioSesion" value="<?=$this->session->idUsuario?>">

											<?php if(GblTraerConfiguracion('PrecioEspecial') == 'Si' || GblTraerConfiguracion('PrecioEmpleado') == 'Si'):?>
												<input type="hidden" id="diferentesPrecios" value="1">
											<?php endif;?>
											<?php if(GblTraerConfiguracion('PrecioEspecial') == 'No' || GblTraerConfiguracion('PrecioEmpleado') == 'No'):?>
												<input type="hidden" id="diferentesPrecios" value="0">
											<?php endif;?>
											<?php if(GblTraerConfiguracion('Cuentas') == 'Si'):?>
												<input type="hidden" id="cuentas" value="1">
											<?php endif;?>
											<?php if(GblTraerConfiguracion('Cuentas') == 'No'):?>
												<input type="hidden" id="cuentas" value="0">
											<?php endif;?>
											<?php if(GblTraerConfiguracion('ServicioSenorita') == 'Si'):?>
												<input type="hidden" id="servicioSenorita" value="1">
											<?php endif;?>
											<?php if(GblTraerConfiguracion('ServicioSenorita') == 'No'):?>
												<input type="hidden" id="servicioSenorita" value="0">
											<?php endif;?>
											<?php if(GblTraerConfiguracion('ImpresionComanda') == 'Si'):?>
												<input type="hidden" id="impresionComanda" value="1">
											<?php endif;?>
											<?php if(GblTraerConfiguracion('ImpresionComanda') == 'No'):?>
												<input type="hidden" id="impresionComanda" value="0">
											<?php endif;?>
										</div>
										<div class="row">
											<div class="col-lg-11 pasosOrden">
												<input type="hidden" class="banderaAccionPasos" value="0">
												<a class="btn btn-default pasos tipoOrden"><i class="fa fa-utensils" aria-hidden="true"></i>TIPO</a>
												<?php if(GblTraerConfiguracion('Cuentas') == 'Si'):?>
													<a class="btn btn-default pasos mesaOrden"><i class="fa fa-chair" aria-hidden="true"></i>MESA</a>
													<a class="btn btn-default pasos cuentaOrden"><i class="fa fa-user" aria-hidden="true"></i>CUENTA</a>
													<a class="btn btn-default pasos elementosOrden"><i class="fa fa-drumstick-bite" aria-hidden="true"></i>ORDEN</a>
												<?php endif;?>
											</div>
											<div class="col-lg-1">
												<a class="btn btn-lg btn-default home float-right"><i class="fa fa-home" aria-hidden="true"></i></a>
											</div>
										</div>
										<!-- <a class="btn btn-lg btn-success"><i class="fa fa-utensils" aria-hidden="true"></i> Ordenes</a>
										<a class="btn btn-lg btn-info"><i class="fa fa-hand-holding-usd" aria-hidden="true"></i> Cover</a>
										<a class="btn btn-lg btn-default"><i class="fa fa-user-clock" aria-hidden="true"></i> Servicios</a>
										<a class="btn btn-lg btn-primary"><i class="fa fa-receipt" aria-hidden="true"></i> Cuentas</a>
										<a class="btn btn-lg btn-danger"><i class="fa fa-exchange-alt" aria-hidden="true"></i> Movimientos</a> -->
									</div>
								</div>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="botoneraOrdenes">

								<div class="row">
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
									<?php if(GblTraerConfiguracion('Llevar') == 'Si'):?>
									<div class="col-lg-6">
										<a id="btnLlevar">
											<div class="small-box bg-primary">
												<div class="inner">
													<h3>Llevar</h3>
													<p><i class="fa fa-plus"></i></p>
												</div>
												<div class="icon">
													<i class="fa fa-shopping-bag"></i>
												</div>
											</div>
										</a>
									</div>
									<?php endif;?>
									<?php if(GblTraerConfiguracion('Domicilio') == 'Si'):?>
									<div class="col-lg-6" >
										<a id="btnDomicilio">
											<div class="small-box bg-info">
												<div class="inner">
													<h3>Domicilio</h3>
													<p><i class="fa fa-plus"></i></p>
												</div>
												<div class="icon">
													<i class="fa fa-shipping-fast"></i>
												</div>
											</div>
										</a>
									</div>
									<?php endif;?>
									<?php if(GblTraerConfiguracion('Recoger') == 'Si'):?>
									<div class="col-lg-6" >
										<a id="btnRecoger">
											<div class="small-box bg-warning">
												<div class="inner">
													<h3>Recoger</h3>
													<p><i class="fa fa-plus"></i></p>
												</div>
												<div class="icon">
													<i class="fa fa-car"></i>
												</div>
											</div>
										</a>
									</div>
									<?php endif;?>
								</div>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="botoneraPrecios">
								<div class="row">
										<div class="col-lg-4">
											<a id="btnRegular" class="tipoPrecio" tipo="regular">
												<div class="small-box bg-warning">
													<div class="inner">
														<h3>Regular</h3>
														<p><i class="fas fa-money-bill"></i></p>
													</div>
													<div class="icon">
													<i class="fas fa-money-bill"></i>
													</div>
												</div>
											</a>
										</div>
									<?php if(GblTraerConfiguracion('PrecioEspecial') == 'Si'):?>
										<div class="col-lg-4">
											<a id="btnEspecial" class="tipoPrecio" tipo="especial">
												<div class="small-box bg-warning">
													<div class="inner">
														<h3>Especial</h3>
														<p><i class="fas fa-money-bill"></i></p>
													</div>
													<div class="icon">
													<i class="fas fa-money-bill"></i>
													</div>
												</div>
											</a>
										</div>
									<?php endif;?>
									<?php if(GblTraerConfiguracion('PrecioEmpleado') == 'Si'):?>
										<div class="col-lg-4">
											<a id="btnEmpleado" class="tipoPrecio" tipo="empleado">
												<div class="small-box bg-warning">
													<div class="inner">
														<h3>Empleados</h3>
														<p><i class="fas fa-money-bill"></i></p>
													</div>
													<div class="icon">
													<i class="fas fa-money-bill"></i>
													</div>
												</div>
											</a>
										</div>
									<?php endif;?>
								</div>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="botoneraMovimientos">
								<div class="row">
									<div class="col-lg-6">
										<a id="btnMovEntrada">
											<div class="small-box bg-success">
												<div class="inner">
													<h3>Entrada</h3>
													<p><i class="fa fa-plus"></i></p>
												</div>
												<div class="icon">
													<i class="ion ion-usd"></i>
												</div>
											</div>
										</a>
									</div>
									<div class="col-lg-6">
										<a id="btnMovSalida">
											<div class="small-box bg-warning">
												<div class="inner">
													<h3>Salida</h3>
													<p><i class="fa fa-minus"></i></p>
												</div>
												<div class="icon">
													<i class="fa fa-usd"></i>
												</div>
											</div>
										</a>
									</div>
								</div>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="nuevaOrden">
								<div class="row">
									<div class="scrollmenu">
										<a class="btn btn-default mb-2 categoria" idc="T">TODO</a>
										<?php if (!empty($categorias) && is_array($categorias)): ?>
										<?php foreach ($categorias as $cat): ?>
											<a class="btn btn-default mb-2 categoria" idc="<?= $cat->idProductoCategoria; ?>"><?= $cat->nombreProductoCategoria; ?></a>
										<?php endforeach; ?>
										<?php endif; ?>


									</div>
								</div>
								<div class="row">
									<input type="text" class="form-control barcode" placeholder="CODIGO" name="" value="">
								</div>

									<div id="listaProductosMostrar">
									<div class="row">
									<!-- Productos cargados dinámicamente por JS -->
									</div>
									</div>						

								<div class="dinamicos" id="modificadores">

									<div id="generalModificadores">
										<!-- <label for="">Producto:</label>
										<label for="nombreProducto" idProducto="" id="nombreProducto"></label><br> -->
										<!-- <label for="">Precio:</label>
										<label for="precioProducto" precioOriginal="" precio="" id="precioProducto"></label> -->
									</div>
									<div id="modificadoresTipo">

									</div>
									<div id="listaModificadores">
									</div>
									<div id="listaModificadoresAgregar">
										<div class="row" hidden>
											<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<button type="button" name="agregarProducto" id="agregarProducto" class="btn btn-primary btn-lg btn-block">Agregar</button>
											</div>
										</div>
									</div>
								</div>

								<div class="dinamicos" id="divDatosZonaMesa">
									<div class="row">
										<div id="divZonaCuenta" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<label for="">Zona</label>
													<input type="hidden" id="nombreZonaCuenta">
													<input type="hidden" id="aumentoZonaCuenta">
													<input type="hidden" id="tipoAumentoZonaCuenta">
													<input type="hidden" id="idZonaCuenta">
													<input type="hidden" name="precioRegularZonaCuenta" id="precioRegularZonaCuenta">
													<input type="hidden" name="precioEspecialZonaCuenta" id="precioEspecialZonaCuenta">
													<input type="hidden" name="precioEmpleadoZonaCuenta" id="precioEmpleadoZonaCuenta">
													<input type="hidden" name="mesaHacerCuenta" id="mesaHacerCuenta">

													<?php $div = ''; 
													if ($zonas) {
														$div.='<div class="row">';
														$pd=0;
														foreach ($zonas as $zon){

															$div.='<div class="col-lg-2 ">
															<div class="card-container mt-2"  >
															<div class="card-custom zonas" style="height:110px;" idz="'.$zon->idZona.'">
															<input type="hidden" class="nombre" value="'.$zon->nombreZona.'">
															<input type="hidden" class="aumento" value="'.$zon->aumentoZona.'">
															<input type="hidden" class="tipoAumento" value="'.$zon->tipoAumentoZona.'">
															<input type="hidden" class="precioRegular" value="'.$zon->precioRegularZona.'">
															<input type="hidden" class="precioEspecial" value="'.$zon->precioEspecialZona.'">
															<input type="hidden" class="precioEmpleado" value="'.$zon->precioEmpleadoZona.'">
															<div class="info-card">
															<p class="nombre" style="font-size: small;">'.$zon->nombreZona.'</p>
															</div>
															</div>
															</div>
															</div>';
															if ($pd == 5){
															$div.='</div><div class="row">';
															$pd = 0;
																}
															$pd++;
														}
														$div.='</div>';

													}; echo $div ?>
														<br>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="divMesasCuenta"></div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<br>
											<button type="button" idPedido='' name="ordenarProductoZonaMesa"  id="ordenarProductoZonaMesa" class="btn btn-primary btn-lg btn-block">SIGUIENTE</button>
											<!-- <button type="button" name="ordenarProductoZonaMesa"  id="ordenarProductoZonaMesa" class="btn btn-primary btn-lg btn-block">SIGUIENTE</button> -->
										</div>
									</div>
								</div>

								<div class="dinamicos" id="divDatosCliente">
									<div class="row">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" hidden>
											<label for="">Tipo de Orden</label>
											<select class="select2 form-control" name="tipoHacerCuenta" id="tipoHacerCuenta" >
												<option></option>
												<option value="local">Local</option>
												<?php if(GblTraerConfiguracion('Llevar') == 'Si'):?>
												<option value="llevar">Llevar</option>
												<?php endif;?>
												<?php if(GblTraerConfiguracion('Domicilio') == 'Si'):?>
												<option value="domicilio">Domicilio</option>
												<?php endif;?>
												<?php if(GblTraerConfiguracion('Recoger') == 'Si'):?>
												<option value="recoger">Recoger</option>
												<?php endif;?>
											</select>
										</div>

									</div>

									<div class="row" >
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
											<label for="clienteProducto">Cliente</label>
											<div class="input-group mb-3">
												<input type="text" class="clearafter form-control upper tecladoPantalla" autocomplete="off" name="clienteProducto" id="clienteProducto" placeholder="Cliente">
												<!-- <div class="input-group-append">
													<a class="btn btn-default tecladoOpener" data-target="clienteProducto"><i class="fa fa-keyboard"></i></a>
												</div> -->
											</div>
											<input type="hidden" id="idClienteProducto" name="idClienteProducto">
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label for="personaHacerCuenta">Personas</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantallaNum" name="personaHacerCuenta" id="personaHacerCuenta" placeholder="Personas">
													<div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="personaHacerCuenta"><i class="fa fa-keyboard"></i></a>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" hidden>
											<select class="select2 form-control" name="zonaHacerCuenta" id="zonaHacerCuenta">
												<option></option>												
												<?php if (!empty($zonas) && is_array($zonas)): ?>
												<?php foreach ($zonas as $zon): ?>
													<option value="<?= $zon->idZona ?>" nombre="<?= $zon->nombreZona ?>" aumento="<?= $zon->aumentoZona ?>" tipoAumento="<?= $zon->tipoAumentoZona ?>"><?= $zon->nombreZona ?></option>
												<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>

									</div>
									<div class="row">
										<div class="col-12">
											<div id="divDireccionCliente">
												<div class="row">
													<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
														<label for="direccionProducto">Direccion</label>
														<div class="input-group mb-3">
															<input type="text" class="clearafter form-control upper tecladoPantalla" name="direccionCliente" id="direccionCliente" placeholder="Direccion">
															<div class="input-group-append">
																<a class="btn btn-default tecladoOpener" data-target="direccionCliente"><i class="fa fa-keyboard"></i></a>
															</div>
														</div>
													</div>
													<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="divVerificarZonaDelivery" style="display:none">
														<button type="button" id="btnVerificarZonaDelivery" class="btn btn-outline-danger btn-block mb-2"><i class="fas fa-map-marked-alt"></i> Verificar zona de delivery</button>
													</div>
												</div>
											</div>

										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<button type="button" name="ordenarProducto"  id="ordenarProducto" class="btn btn-primary btn-lg btn-block">ORDENAR</button>
												</div>
											</div>
										</div>
										<!-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<button type="button" name="pagarProducto"  id="pagarProducto" class="btn btn-primary btn-lg btn-block">COBRAR</button>
												</div>
											</div>
										</div> -->
									</div>

								</div>

								<div class="dinamicos" id="divPagoProducto">
									<div class="row">
										<div class="col-12">
											<br>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" hidden>
											<label for="cajaProducto">Caja</label>
											<div class="input-group mb-3">
												<input type="hidden" name="idCaja" id="idCaja" value="<?=$idCaja?>">
												<select class="select2 col-11 siguiente" name="cajaProducto" id="cajaProducto">
												<?php if (!empty($cajas) && is_array($cajas)): ?>
												<?php foreach ($cajas as $doc): ?>
														<option value="<?= $doc->idCaja ?>" recurso="<?php echo ($doc->recursoCompartidoImpresora) ; ?>" ip="<?php echo ($doc->ipImpresora) ; ?>"><?= $doc->nombreCaja ?></option>
													<?php endforeach; ?>
													<?php endif; ?>
												</select>
												<!-- <div class="input-group-append">
													<div class="input-group-text">
														<span class="fa fa-keyboard select-opener" idselect="cajaProducto"></span>
													</div>
												</div> -->
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<label for="tipoPagoProducto">Tipo de Pago</label>
											<div class="input-group mb-3">
												<select class="select2 col-11" name="tipoPagoProducto" id="tipoPagoProducto">
													<option></option>
												</select>
												<!-- <div class="input-group-append">
													<div class="input-group-text">
														<span class="fa fa-keyboard select-opener" idselect="tipoPagoProducto"></span>
													</div>
												</div> -->
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 docGEN docFAC docCCF">
											<div class="form-group">
												<label for="nombreClientePagoProducto">Nombre</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla siguiente" name="nombreClientePagoProducto" id="nombreClientePagoProducto" placeholder="Cliente">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="nombreClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>
											</div>
										</div>
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 docGEN docFAC docCCF">
											<div class="form-group">
												<label for="direccionClientePagoProducto">Direccion</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla siguiente" name="direccionClientePagoProducto" id="direccionClientePagoProducto" placeholder="Direccion">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="direccionClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>
											</div>
										</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<div class="form-group">
												<button class="btn btn-danger btn-sm agregarCliente" style="margin-top:35px;"><i class='fa fa-user-plus'></i> </button>
												<button class="btn btn-warning btn-sm editarCliente" hidden style="margin-top:35px;"><i class='fa fa-user-edit'></i> </button>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docCCF">
											<div class="form-group">
												<label for="departamentoClientePagoProducto">Departamento</label>
												<div class="input-group mb-3">
													<select class="clearafter form-control upper siguiente" name="departamentoClientePagoProducto" id="departamentoClientePagoProducto">
														<option value="">Seleccione</option>
														<?php if(!empty($departamentos)): foreach($departamentos as $depto): ?>
														<option value="<?=$depto->codigo?>"><?=$depto->valores?></option>
														<?php endforeach; endif; ?>
													</select>
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="nitClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docCCF">
											<div class="form-group">
												<label for="municipioClientePagoProducto">Municipio</label>
												<div class="input-group mb-3">
													<select class="clearafter form-control upper siguiente" name="municipioClientePagoProducto" id="municipioClientePagoProducto">
														<option value="">Seleccione</option>
													</select>
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="nrcClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>

											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docFAC">
											<div class="form-group">
												<label for="telefonoClientePagoProducto">Telefono</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla siguiente" name="telefonoClientePagoProducto" id="telefonoClientePagoProducto" placeholder="">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="nrcClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>

											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docFAC docCCF">
											<div class="form-group">
												<label for="correoClientePagoProducto">Correo</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla" name="correoClientePagoProducto" id="correoClientePagoProducto" placeholder="">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="correlativoClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>
											</div>
										</div>
										<!-- <div class="col-lg-6">
												<div class="form-group">
													<label for="envioProducto">Envio</label>
													<div class="input-group mb-3">
														<input readonly type="text" class="form-control decimal" name="envioProducto" id="envioProducto" placeholder="$00.00">
														<div class="input-group-append">
															<a class="btn btn-default envio-opener"><i class="fa fa-keyboard"></i></a>
														</div>
													</div>
												</div>
										</div> -->
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docCCF">
											<div class="form-group">
												<label for="nitClientePagoProducto">NIT</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla siguiente" name="nitClientePagoProducto" id="nitClientePagoProducto" placeholder="NIT">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="nitClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docCCF">
											<div class="form-group">
												<label for="nrcClientePagoProducto">NRC</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla siguiente" name="nrcClientePagoProducto" id="nrcClientePagoProducto" placeholder="NRC">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="nrcClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>

											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docCCF">
											<div class="form-group">
												<label for="giroClientePagoProducto">GIRO</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla siguiente" name="giroClientePagoProducto" id="giroClientePagoProducto" placeholder="">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="nrcClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>

											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 docFAC docCCF">
											<div class="form-group">
												<label for="correlativoClientePagoProducto">Correlativo</label>
												<div class="input-group mb-3">
													<input type="text" class="clearafter form-control upper tecladoPantalla" readonly name="correlativoClientePagoProducto" id="correlativoClientePagoProducto" placeholder="Correlativo">
													<!-- <div class="input-group-append">
														<a class="btn btn-default tecladoOpener" data-target="correlativoClientePagoProducto"><i class="fa fa-keyboard"></i></a>
													</div> -->
												</div>
											</div>
										</div>
										<div class="col-lg-6" hidden>
												<div class="form-group">
													<label for="envioProducto">Envio</label>
													<div class="input-group mb-3">
														<input type="text" class="form-control decimal" name="envioProducto" id="envioProducto" placeholder="$00.00">
														<div class="input-group-append">
															<a class="btn btn-default envio-opener"><i class="fa fa-keyboard"></i></a>
														</div>
													</div>
												</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div class="icheck-success d-inline">
												<input type="checkbox" class="siguiente" id="porConsumoPagoProducto" name="porConsumoPagoProducto">
												<label for="porConsumoPagoProducto">Por Consumo</label>
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div class="icheck-success d-inline">
												<input type="checkbox" id="quitarPropina" name="quitarPropina">
												<label for="quitarPropina">Quitar Propina</label>
											</div>
										</div>
										<!-- <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div class="icheck-success d-inline">
												<input type="checkbox" id="pedidosYaPagoProducto" name="pedidosYaPagoProducto">
												<label for="pedidosYaPagoProducto">Pedidos Ya</label>
											</div>
										</div> -->
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<br>
											<div class="row">
												<div class="col-2">
													<div class="form-group">
														<label for="descuentoPagoProducto">Descuento(%)</label>
														<input type="hidden" id="autorizadoUsuario" value="<?php echo ($this->session->autorizadoUsuario == 1) ? "0" : "0";?>">
														<div class="input-group mb-3">
															<input type="text" class="form-control decimal" permiso="<?php echo ($this->session->autorizadoUsuario == 1) ? "0" : "0";?>" name="descuentoPagoProducto" id="descuentoPagoProducto" placeholder="00.00%">
															<div class="input-group-append" id="descpor">
															</div>
														</div>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="descuentoDolarPagoProducto">Descuento($)</label>
														<div class="input-group mb-3">
															<input type="text" class="form-control decimal " permiso="<?php echo ($this->session->autorizadoUsuario == 1) ? "0" : "0";?>" name="descuentoDolarPagoProducto" id="descuentoDolarPagoProducto" placeholder="00.00$">
															<div class="input-group-append" id="descdin">
															</div>
														</div>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="efectivoPagoProducto">Efectivo</label>
														<div class="input-group mb-3">
															<input type="text" class="form-control decimal siguiente " name="efectivoPagoProducto" id="efectivoPagoProducto" placeholder="$00.00">
															<div class="input-group-append">
																<a class="btn btn-default efectivo-opener"><i class="fa fa-keyboard"></i></a>
															</div>
														</div>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="tarjetaPagoProducto">Tarjeta</label>
														<div class="input-group mb-3">
															<input type="text" class="form-control decimal siguiente" name="tarjetaPagoProducto" id="tarjetaPagoProducto" placeholder="$00.00">
															<div class="input-group-append">
																<a class="btn btn-default tarjeta-opener"><i class="fa fa-keyboard"></i></a>
															</div>
														</div>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="bitcoinPagoProducto">Bitcoin</label>
														<div class="input-group mb-3">
															<input type="text" class="form-control decimal siguiente" name="bitcoinPagoProducto" id="bitcoinPagoProducto" placeholder="$00.00">
															<div class="input-group-append">
																<a class="btn btn-default btc-opener"><i class="fa fa-keyboard"></i></a>
															</div>
														</div>
													</div>
												</div>
												<div class="col-2" hidden>
													<div class="form-group">
														<label for="pedidosYaPagoProducto">Pedidos Ya</label>
														<div class="input-group mb-3">
															<input type="text" class="form-control decimal siguiente" name="pedidosYaPagoProducto" id="pedidosYaPagoProducto" placeholder="$00.00">
															<div class="input-group-append">
																<a class="btn btn-default pedidos-opener"><i class="fa fa-keyboard"></i></a>
															</div>
														</div>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="transferenciaProducto">Transferencia</label>
														<div class="input-group mb-3">
															<input type="text" class="form-control decimal siguiente" name="transferenciaProducto" id="transferenciaProducto" placeholder="$00.00">
															<div class="input-group-append">
																<a class="btn btn-default transferencia-opener"><i class="fa fa-keyboard"></i></a>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div id="totalesNormal" class="row" >
												<div class="col-3">
													<div class="form-group">
														<label for="totalPagoProducto">SubTotal</label>
														<h4 id="totalPagoProducto">$00.00</h4>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="propinaPagoProducto">Propina (<?=$propina?>%)</label>
														<h4 id="propinaPagoProducto">$00.00</h4>
														<input type="hidden" id="propina" value="<?=$propina?>">
														<input type="hidden" id="cobroPropina" value="<?=$cobroPropina?>">
													</div>
												</div>
												<div class="col-2" hidden>
													<div class="form-group">
														<label for="cargoZonaPagoProducto">Cargo Por Zona</label>
														<h4 id="cargoZonaPagoProducto">$00.00</h4>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label for="totalPropPagoProducto">Total</label>
														<h4 id="totalPropPagoProducto">$00.00</h4>
													</div>
												</div>
												<div class="col-2" hidden>
													<div class="form-group">
														<label for="totalEnvioPagoProducto">Total + Envio</label>
														<h4 id="totalEnvioPagoProducto">$00.00</h4>
													</div>
												</div>
												<div class="col-2">
													<div class="form-group">
														<label>Cambio: </label>
														<h4 id="vueltoPagoProducto">$00.00</h4>
													</div>
												</div>
											</div>
											<div class="row">
												<?php if(GblTraerConfiguracion('Cuentas') == 'No'):?>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<button type="button" disabled name="pagarProducto"  id="pagarProducto" class="btn btn-primary btn-lg btn-block">FACTURAR</button>
												</div>
												<?php endif;?>
												<?php if(GblTraerConfiguracion('Cuentas') == 'Si'):?>
												<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
													<button type="button" name="abrirCuenta"  id="abrirCuenta" class="btn btn-primary btn-lg btn-block">ABRIR CUENTA</button>
												</div>
												<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
													<button type="button" disabled name="pagarProducto"  id="pagarProducto" class="btn btn-primary btn-lg btn-block">FACTURAR</button>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<button type="button" name="agregarACuenta"  id="agregarACuenta" class="btn btn-primary btn-lg btn-block">AGREGAR A CUENTA</button>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<button type="button" disabled name="finalizarCuenta"  id="finalizarCuenta" class="btn btn-primary btn-lg btn-block">FACTURAR</button>
												</div>
												<?php endif;?>
											</div>
										</div>
										<!-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="softkeys" data-target="#efectivoPagoProducto"></div>
												</div>
											</div>
										</div> -->
									</div>

								</div>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->


							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="nuevoServicio">
								<div class="row">
									<div class="scrollmenu">
										<!-- <a class="btn btn-default mb-2 servicioCategoria" idc="T">TODO</a> -->										
											<?php if (!empty($servicioCategoria) && is_array($servicioCategoria)): ?>
												<?php foreach ($servicioCategoria as $servCat): ?>
												<a class="btn btn-default mb-2 servicioCategoria" idc="<?= $servCat->idServicioCategoria; ?>"><?= $servCat->nombreServicioCategoria; ?></a>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								</div>

								<div id="listaServiciosMostrar">
									<div class="row">

									</div>
								</div>
								<div id="listaSenoritasMostrar">

								</div>
								<div id="listaNombresSenoritasMostrar">

								</div>
								<div class="" id="divPagoServicio">

									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<label for="cajaServicio">Caja</label>
											<select class="select2 col-11" name="cajaServicio" id="cajaServicio">
												<option></option>
												<?php if (!empty($cajas) && is_array($cajas)): ?>
													<?php foreach ($cajas as $doc): ?>
													<option value="<?= $doc->idCaja ?>"><?= $doc->nombreCaja ?></option>
												<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<label for="tipoPagoServicio">Tipo de Pago</label>
											<div class="input-group mb-3">
												<select class="select2 col-11" name="tipoPagoServicio" id="tipoPagoServicio">
													<option></option>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<div class="col-12">
													<div class="form-group">
														<label for="efectivoPagoServicio">Efectivo</label>
														<input type="text" class="form-control decimal" name="efectivoPagoServicio" id="efectivoPagoServicio" placeholder="$00.00">
													</div>
												</div>
												<div class="col-12">
													<div class="form-group">
														<label for="totalPagoServicio">Total</label>
														<h3 id="totalPagoServicio">$00.00</h3>
													</div>
												</div>
												<div class="col-12">
													<div class="form-group">
														<label>Vuelto: </label>
														<h3 id="vueltoPagoServicio">$00.00</h3>
													</div>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<button type="button" disabled name="pagarServicio"  id="pagarServicio" class="btn btn-primary btn-lg btn-block">COBRAR</button>
												</div>
										</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="softkeyss" data-target="#efectivoPagoServicio"></div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">

									</div>
								</div>
							</div>

							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<!-- <div class="dinamicos" id="servicioPendiente">

							</div> -->

							<!--------------------------------------------------->
							<!--------------------------------------------------->

							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="nuevoCover">

    <div id="listaCoverMostrar">
        <div class="row">

            <?php
            $pd = 0;
            if (!empty($bebidas) && is_array($bebidas)):
                foreach ($bebidas as $beb):
            ?>

                    <div class="col-lg-3">
                        <div class="card-container mt-2">
                            <div class="card-custom cover" idp="<?= $beb->idProducto; ?>">
                                <input type="hidden" class="precio" value="3">
                                <div class="info-card">
                                    <p><?= $beb->nombreProducto; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($pd == 3): ?>
                        </div>
                        <div class="row">
                        <?php $pd = 0; ?>
                    <?php else: ?>
                        <?php $pd++; ?>
                    <?php endif; ?>

            <?php
                endforeach;
				endif;
            ?>

        </div>
    </div>

</div>

							<!--------------------------------------------------->
							<!--------------------------------------------------->


							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="espacioCuentas">
								<!-- <label>Cuentas Actuales</label> -->
								<div class="row" id="listaCuentas">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<ul class="nav nav-tabs">
											<li class="nav-item"><a class="nav-link pestana text-white active bg-primary" id="tabCuentaActiva" href="#vistaCuentasActivas" role="tab">Cuentas Activas</a></li>
											<li class="nav-item"><a class="nav-link pestana text-white" id="tabCuentaCancelada" href="#vistaCuentasCanceladas" role="tab">Cuentas Finalizadas</a></li>
										</ul>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="tab-content" id="myTabContent">
											<div class="row tab-pane fade show active" role="tabpanel" id="vistaCuentasActivas">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<br>
													<ul class="nav nav-tabs">
														<li class="nav-item"><a class="nav-link pestanaDet text-white active bg-primary" tipo="local" id="tabCuentaActivaLocal" href="#vistaCuentasActivasEspecifica" role="tab">Local</a></li>
														<?php if(GblTraerConfiguracion('Llevar') == 'Si'):?>
															<li class="nav-item"><a class="nav-link pestanaDet text-white" tipo="llevar" id="tabCuentaActivaLlevar" href="#vistaCuentasActivasEspecifica" role="tab">Llevar</a></li>
														<?php endif;?>
														<?php if(GblTraerConfiguracion('Domicilio') == 'Si'):?>
															<li class="nav-item"><a class="nav-link pestanaDet text-white" tipo="domicilio" id="tabCuentaActivaDomicilio" href="#vistaCuentasActivasEspecifica" role="tab">Domicilio</a></li>
														<?php endif;?>
														<?php if(GblTraerConfiguracion('Recoger') == 'Si'):?>
															<li class="nav-item"><a class="nav-link pestanaDet text-white" tipo="recoger" id="tabCuentaActivaRecoger" href="#vistaCuentasActivasEspecifica" role="tab">Recoger</a></li>
														<?php endif;?>
													</ul>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="row tab-pane fade show active" role="tabpanel" id="vistaCuentasActivasEspecifica">
														<table class="table table-condensed" id="cuentasLista">

														</table>
													</div>
												</div>
											</div>
											<div class="row tab-pane fade" role="tabpanel" id="vistaCuentasCanceladas">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<br>
													<ul class="nav nav-tabs">
														<li class="nav-item"><a class="nav-link pestanaDetCancel text-white active bg-primary" tipo="local" id="tabCuentaCanceladaLocal" href="#vistaCuentasActivasEspecifica" role="tab">Local</a></li>
														<?php if(GblTraerConfiguracion('Llevar') == 'Si'):?>
															<li class="nav-item"><a class="nav-link pestanaDetCancel text-white" tipo="llevar" id="tabCuentaCanceladaLlevar" href="#vistaCuentasActivasEspecifica" role="tab">Llevar</a></li>
														<?php endif;?>
														<?php if(GblTraerConfiguracion('Domicilio') == 'Si'):?>
															<li class="nav-item"><a class="nav-link pestanaDetCancel text-white" tipo="domicilio" id="tabCuentaCanceladaDomicilio" href="#vistaCuentasActivasEspecifica" role="tab">Domicilio</a></li>
														<?php endif;?>
														<?php if(GblTraerConfiguracion('Recoger') == 'Si'):?>
															<li class="nav-item"><a class="nav-link pestanaDetCancel text-white" tipo="recoger" id="tabCuentaCanceladaRecoger" href="#vistaCuentasActivasEspecifica" role="tab">Recoger</a></li>
														<?php endif;?>
													</ul>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<table class="table table-condensed" id="cuentasListaCancelada">

													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--------------------------------------------------->
							<!--------------------------------------------------->



							<!--------------------------------------------------->
							<!--------------------------------------------------->
							<div class="dinamicos" id="espacioMovimientos">
								<h1 for="">Movimientos de Caja</h1><br>
								<h2 id="labelTipoMov"></h2>

								<form id="FrmMovimientoCaja" autocomplete="off">
									<div class="row">
										<div class="col-12">
											<label for="cajaMovimiento">Caja</label>
											<div class="form-group mb-3">
												<select class="select2 col-12" name="cajaMovimiento" id="cajaMovimiento">
													<?php if (!empty($cajas)&&is_array($cajas)): 
													foreach ($cajas as $doc): ?> 
														<option value="<?= $doc->idCaja ?>" <?php echo ($doc->idCaja == '1') ? 'selected' : '';   ?> ><?= $doc->nombreCaja ?></option>
													<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-6">
											<label for="tipo">Recibe</label>
											<div class="input-group mb-3">
												<input type="text" class="clearafter form-control upper tecladoPantalla" name="movimientoRecibe" id="movimientoRecibe" placeholder="Recibe">
												<div class="input-group-append">
													<a class="btn btn-default tecladoOpener" data-target="movimientoRecibe"><i class="fa fa-keyboard"></i></a>
												</div>
											</div>
										</div>
										<div class="col-6">
											<label for="tipo">Entrega</label>
											<div class="input-group mb-3">
												<input type="text" class="clearafter form-control upper tecladoPantalla" name="movimientoEntrega" id="movimientoEntrega" placeholder="Entrega">
												<div class="input-group-append">
													<a class="btn btn-default tecladoOpener" data-target="movimientoEntrega"><i class="fa fa-keyboard"></i></a>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<label for="tipo">Concepto</label>
											<div class="input-group mb-3">
												<input type="text" class="clearafter form-control upper tecladoPantalla" name="movimientoConcepto" id="movimientoConcepto" placeholder="Concepto">
												<div class="input-group-append">
													<a class="btn btn-default tecladoOpener" data-target="movimientoConcepto"><i class="fa fa-keyboard"></i></a>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-6">
											<label for="tipo">Monto</label>
											<div class="input-group mb-3">
												<input type="text" class="form-control decimal tecladoPantallaNum" name="movimientoMonto" id="movimientoMonto" placeholder="$0.00">
												<div class="input-group-append">
													<a class="btn btn-default tecladoOpener" data-target="movimientoMonto"><i class="fa fa-keyboard"></i></a>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-12">
											<button type="button" class="btn btn-success btn-block btn-lg btnFinalizarMovimiento"><i class='fa fa-receipt'></i> Finalizar</button>
											<input type="hidden" name="tipoMovimiento" id="tipoMovimiento">
										</div>
									</div>
									</form>
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
<script>
  // Catálogo FE_CAT_013_Municipio agrupado por código de departamento (FE_CAT_012),
  // usado para llenar en cascada el select de Municipio del cobro.
  var municipiosPorDepto = <?=json_encode(!empty($municipiosPorDepto) ? $municipiosPorDepto : new stdClass())?>;
</script>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>

<!-- Modal: verificar zona de delivery (solo lectura, las zonas se administran desde AdminOnline) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<div class="modal fade" id="modalZonaDelivery" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fas fa-map-marked-alt"></i> Verificar zona de delivery</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-row mb-2">
					<div class="col-8">
						<input type="text" class="form-control" id="coordsZonaDelivery" placeholder="Pega aquí la ubicación que envió el cliente (ej: 13.6929, -89.2182)">
					</div>
					<div class="col-4">
						<button type="button" class="btn btn-secondary btn-block" id="btnCentrarCoordsZona">Buscar en mapa</button>
					</div>
				</div>
				<div id="mapaZonaDeliveryTouch" style="width:100%;height:400px"></div>
				<div id="resultadoZonaDelivery" class="mt-3"></div>
				<small class="text-muted">Haz clic en el mapa donde se encuentra el cliente, o pega las coordenadas que te compartió por WhatsApp.</small>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<script>
	var mapaZonaDeliveryTouch, capasZonaDeliveryTouch, marcadorZonaDeliveryTouch, zonaDeliveryMapaListo = false;

	function inicializarMapaZonaDeliveryTouch(){
		if(zonaDeliveryMapaListo) return;
		zonaDeliveryMapaListo = true;

		mapaZonaDeliveryTouch = L.map('mapaZonaDeliveryTouch').setView([13.6929, -89.2182], 12);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; OpenStreetMap'
		}).addTo(mapaZonaDeliveryTouch);

		capasZonaDeliveryTouch = new L.FeatureGroup();
		mapaZonaDeliveryTouch.addLayer(capasZonaDeliveryTouch);

		$.get(url + '/Touch/ZonasDeliveryMapa', function(r){
			if(r.codigo !== 200) return;
			r.zonas.forEach(function(z){
				var path = z.poligonoZonaDelivery.map(function(p){ return [parseFloat(p.lat), parseFloat(p.lng)]; });
				var capa = L.polygon(path, {
					color: z.colorZonaDelivery,
					weight: 2,
					fillColor: z.colorZonaDelivery,
					fillOpacity: 0.15
				});
				capa.bindTooltip(z.nombreZonaDelivery);
				capasZonaDeliveryTouch.addLayer(capa);
			});
		}, 'json');

		mapaZonaDeliveryTouch.on('click', function(e){
			verificarPuntoZonaDeliveryTouch(e.latlng.lat, e.latlng.lng);
		});
	}

	function verificarPuntoZonaDeliveryTouch(lat, lng){
		if(marcadorZonaDeliveryTouch) mapaZonaDeliveryTouch.removeLayer(marcadorZonaDeliveryTouch);
		marcadorZonaDeliveryTouch = L.marker([lat, lng]).addTo(mapaZonaDeliveryTouch);

		$.post(url + '/Touch/VerificarZonaDelivery', {lat: lat, lng: lng}, function(r){
			var $res = $('#resultadoZonaDelivery');
			if(r.codigo === 200 && r.enZona){
				$res.html('<div class="alert alert-success mb-0"><i class="fas fa-check-circle"></i> En zona de delivery: <b>'+r.zona.nombreZonaDelivery+'</b></div>');
			} else {
				$res.html('<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle"></i> Esta ubicación está fuera de las zonas de delivery configuradas.</div>');
			}
		}, 'json');
	}

	window.addEventListener('load', function(){
		$('#btnVerificarZonaDelivery').click(function(){
			$('#resultadoZonaDelivery').empty();
			$('#modalZonaDelivery').modal('show');
		});

		$('#modalZonaDelivery').on('shown.bs.modal', function(){
			inicializarMapaZonaDeliveryTouch();
			setTimeout(function(){ if(mapaZonaDeliveryTouch) mapaZonaDeliveryTouch.invalidateSize(); }, 200);
		});

		$('#btnCentrarCoordsZona').click(function(){
			var partes = $('#coordsZonaDelivery').val().split(',');
			if(partes.length !== 2){ alert('Formato esperado: latitud, longitud'); return; }
			var lat = parseFloat(partes[0].trim());
			var lng = parseFloat(partes[1].trim());
			if(isNaN(lat) || isNaN(lng)){ alert('Coordenadas inválidas.'); return; }
			mapaZonaDeliveryTouch.setView([lat, lng], 16);
			verificarPuntoZonaDeliveryTouch(lat, lng);
		});
	});
</script>

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
</div>
?>