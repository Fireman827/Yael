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
							<h3 class="card-title"><?=$titulo?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<!-- <form id="FrmCliente" autocomplete="off"> -->
							<div class="card-body">
								<div class="row">
                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
											<label for="depreciacionActivoFijo">Tipo Corte</label>
											<select name="tipoCorte" id="tipoCorte" class="form-control select2">
												<option value="C">Corte Caja</option>
												<option value="X">Corte X</option>
												<option value="Z">Corte Z</option>
											</select>
										</div>
									</div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="form-group">
											<label for="vidaActivoFijo">Fecha</label>
											<input type="text" class="form-control" id="fechaCorte" name="fechaCorte" value="<?= date("Y-m-d"); ?>" readonly>
										</div>
									</div>
								</div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="container-fluid">
                      <div class="row">
                				<div class="col-md-12">
                          <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
                						<div class="card-header">
                							<h3 class="card-title">Total documentos</h3>
                						</div>
                            <div class="card-body">
              								<div class="row">
                                <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> -->
                                  <table id="tablaTurno" class="table table-hover table-sm dataTable dtr-inline" role="grid" aria-describedby="tablaTurno_info">
                                    <thead>
                                      <tr>
                                        <th class="col-lg-3">Tipo</th>
                                        <th class="col-lg-3">N° Inicio</th>
                                        <th class="col-lg-3">N° Final</th>
                                        <th class="col-lg-3">T. Doc.</th>
                                        <th class="col-lg-3">Total</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                        $totalDocumentos = 0;
                                        $totalTarjeta = 0;
                                        $totalBitcoin = 0;
                                        $totalPedidosYa = 0;
                                        $totalEfectivo = 0;
                                        $totalDescuento = 0;
										$totalTransferencia = 0;
										$totalEnvio = 0;
										$totalPropina = 0;
										$vuelto = 0;

                                        if ($datosTotales)
                                        {
                                          foreach ($datosTotales as $fila)
                                          {
                                            if($this->session->admin == "1"){

                                              echo "<tr>";
                                              echo "	<td>".$fila["nombreDocumento"]."</td>";
                                              echo "	<td>".$fila["minimo"]."</td>";
                                              echo "	<td>".$fila["maximo"]."</td>";
                                              echo "	<td>".$fila["nDatos"]."</td>";
                                              echo "	<td>".$fila["total"]."</td>";
                                              echo "</tr>";
                                            }
                                            $totalDocumentos += $fila["total"];
                                            $tarjeta = $fila["tarjeta"];
                                            $bitcoin = $fila["bitcoin"];
                                            $pedidosYa = $fila["pedidosYa"];
											$envio = $fila["envio"];
											$transferencia = $fila["transferencia"];
											$propina = $fila["propina"];

                                            $efectivo = $fila["efectivo"] - $fila['vuelto'];
                                            $totalTarjeta += $tarjeta;
                                            $totalBitcoin += $bitcoin;
                                            $totalPedidosYa += $pedidosYa;
                                            $totalEfectivo += $efectivo;
											$totalEnvio += $envio;
											$totalTransferencia += $transferencia;
											$totalPropina += $propina;
                                            $totalDescuento += $fila["totalDescuento"];
                                          }
                                        }
                                      ?>

                  										<tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?>>
                  											<td colspan="4">TOTAL</td>
                  											<td><label id="id_total"><?= number_format($totalDocumentos, 2, ".", ",")?></label></td>

                  										</tr>
                                    </tbody>
                                  </table>
                                <!-- </div> -->
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="container-fluid">
                      <div class="row">
                				<div class="col-md-12">
                          <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
                						<div class="card-header">
                							<h3 class="card-title">Total Movimientos de Caja</h3>
                						</div>
                            <div class="card-body">
              								<div class="row">
                                <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> -->
                                  <table id="tablaTurno" class="table table-hover table-sm dataTable dtr-inline" role="grid" aria-describedby="tablaTurno_info">
                                    <thead>
                                      <tr>
                                        <th class="col-lg-9">Tipo Movimiento</th>
                                        <th class="col-lg-3">Total</th>
                                      </tr>
                                    </thead>
                                    <tbody <?php echo ($this->session->admin == "0") ? "hidden" : "";?>>
                                      <tr>
                  											<td>Entrada</td>
                  											<td><?= $entrada; ?></td>
                  										</tr>
                  										<tr>
                  											<td>Salida</td>
                  											<td><?= $salida; ?></td>
                  										</tr>
                                      <tr>
                  											<td>TOTAL</td>
                  											<td><label id="id_total_mov"><?= number_format($entrada - $salida, 2, ".", ",")?></label></td>
                  										</tr>
                                    </tbody>
                                  </table>
                                <!-- </div> -->
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

								<div class="row">
									<div class="col-md-6">
										<div class="container-fluid">
											<div class="row">
												<div class="col-md-12">
													<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
                            <div class="card-header">
                              <h3 class="card-title">Tipos De Pago</h3>
                            </div>
														<div class="card-body">
															<div class="row">
																<!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> -->
																	<table id="tablatipospago" class="table table-hover table-sm dataTable dtr-inline" role="grid" aria-describedby="">
																		<thead>
																			<tr>
																				<th class="col-lg-6">Tipo</th>
																				<th class="col-lg-6">Monto</th>
																			</tr>
																		</thead>
																		<tbody>
																			<tr>
																				<td>Efectivo Real</td>
																				<td><?=number_format($totalEfectivo,2)?></td>
																			</tr>
																			<tr>
																				<td>Tarjeta</td>
																				<td><?=number_format($totalTarjeta,2)?></td>
																			</tr>
																			<tr>
																				<td>Bitcoin</td>
																				<td><?=number_format($totalBitcoin,2)?></td>
																			</tr>
																			<tr>
																				<td>Pedidos Ya</td>
																				<td><?=number_format($totalPedidosYa,2)?></td>
																			</tr>
																			<tr>
																				<td>Transferencia</td>
																				<td><?=number_format($totalTransferencia,2)?></td>
																			</tr>
																		</tbody>
																	</table>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="container-fluid">
											<div class="row">
												<div class="col-md-12">
													<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
                            <div class="card-header">
                              <h3 class="card-title">Envios a domicilio</h3>
                            </div>
														<div class="card-body">
															<div class="row">
																<!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> -->
																	<table id="tablatipospago" class="table table-hover table-sm dataTable dtr-inline" role="grid" aria-describedby="">
																		<thead>
																			<tr>
																				<th class="col-lg-6">Tipo</th>
																				<th class="col-lg-6">Monto</th>
																			</tr>
																		</thead>
																		<tbody>
																			<tr>
																				<td>Monto Extra de Envio</td>
																				<td><?=number_format($totalEnvio,2)?></td>
																			</tr>
																		</tbody>
																	</table>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
                            <div class="card-header">
                              <h3 class="card-title">Total documentos</h3>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> -->
                                  <table id="tablaTurno" class="table table-hover table-sm dataTable dtr-inline" role="grid" aria-describedby="tablaTurno_info">
                                    <thead>
                                      <tr>
                                        <th class="col-lg-10">Tipo</th>
                                        <!-- <th class="col-lg-3">N° Inicio</th>
                                        <th class="col-lg-3">N° Inicio</th>
                                        <th class="col-lg-3">T. Doc.</th> -->
                                        <th class="col-lg-2">Total</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?> >
                                        <td>MONTO APERTURA</td>
                                        <td><label id="montoApertura"><?= number_format($montoApertura, 2, ".", ","); ?></label></td>
                                      </tr>
																			<tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?> >
																				<td>PROPINAS</td>
																				<td><label id="montoApertura"><?= number_format($totalPropina, 2, ".", ","); ?></label></td>
																			</tr>
                                      <tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?>>
                                        <td>TOTAL DOCUMENTOS</td>
                                        <td><label id="montoDocumentos"><?= number_format($totalDocumentos, 2, ".", ",") ?></label></td>
                                      </tr>
                                      <tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?>>
                                        <td>TOTAL DESCUENTOS</td>
                                        <td><label id="montoDescuentos"><?= number_format($totalDescuento, 2, ".", ",") ?></label></td>
                                      </tr>
                                      <tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?>>
                                        <td>TOTAL CAJA</td>
                                        <td><label id="montoDocumentos"><?= number_format($entrada - $salida, 2, ".", ",") ?></label></td>
                                      </tr>
                                      <tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?>>
                                        <td>TOTAL GENERAL EN EFECTIVO</td>
                                        <td><label id="montoEfectivo"><?= number_format($montoApertura + $totalDocumentos + $entrada - $salida, 2, ".", ",") ?></label></td>
                                      </tr>
                                      <tr>
                                        <td>EFECTIVO EN CAJA</td>
                                        <td>
																					<div class="input-group mb-3">
																						<input type="text" name="montoEfectivoCaja" id="montoEfectivoCaja" class="form-control decimal" placeholder="Efectivo en caja">
																						<!-- <input type="text" name="montoApertura" id="montoApertura" class="form-control decimal" placeholder="Monto de apertura"> -->
																						<!-- <input type="password" class="form-control" id="claveUsuario2" name="claveUsuario2" placeholder="Clave" autocomplete="off"> -->
																						<div class="input-group-append">
																							<div class="input-group-text">
																								<span class="fa fa-keyboard efectivo-opener"></span>
																							</div>
																						</div>
																					</div>
																				</td>
                                      </tr>
                                      <tr <?php echo ($this->session->admin == "0") ? "hidden" : "";?>>
                                        <td>DIFERENCIA</td>
                                        <td><input type="text" name="montoEfectivoDiferencia" id="montoEfectivoDiferencia" class="form-control decimal" placeholder="Diferencia" readonly></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                <!-- </div> -->
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<input type="hidden" name="datosTotales" id="datosTotales" value='<?php print_r(json_encode($datosTotales, true)); ?>'>
								<input type="hidden" name="entrada" id="entrada" value="<?= $entrada; ?>">
								<input type="hidden" name="salida" id="salida" value="<?= $salida; ?>">
								<input type="hidden" name="totalDocumentos" id="totalDocumentos" value="<?= $totalDocumentos; ?>">
                				<input type="hidden" name="totalEfectivo" id="totalEfectivo"  value="<?=$totalEfectivo?>">
								<input type="hidden" name="totalTarjeta" id="totalTarjeta"  value="<?=$totalTarjeta?>">
								<input type="hidden" name="totalTransferencia" id="totalTransferencia"  value="<?=$totalTransferencia?>">
								<input type="hidden" name="totalBitcoin" id="totalBitcoin"  value="<?=$totalBitcoin?>">
								<input type="hidden" name="totalPedidosYa" id="totalPedidosYa"  value="<?=$totalPedidosYa?>">
								<input type="hidden" name="montoAperturax" id="montoAperturax" value="<?= $montoApertura; ?>">
								<input type="hidden" name="idCorte" id="idCorte" value="<?= $idCorte; ?>">
								<input type="hidden" name="idTurno" id="idTurno" value="<?= $idTurno; ?>">
								<input type="hidden" name="revisionInsumo" id="revisionInsumo" value="<?= $revisionInsumo; ?>">
								<input type="hidden" name="proceso" id="proceso" value="Ver">
								<button class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right" id="btnRealizarCorte"><i class="fa fa-save"></i> Guardar</button>
							</div>
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
<?php if(isset($proceso)){ ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
