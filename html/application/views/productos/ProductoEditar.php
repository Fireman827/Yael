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
					<!-- <h4><i class="<?= $icono ?>"></i> <?= $titulo ?></h4> -->
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url(); ?>">Inicio</a></li>
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url() . $controlador; ?>"><?= ucfirst($controlador); ?></a></li>
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
							<input type="hidden" id="bgColor" value="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<div class="card-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<ul class="nav nav-tabs">
										<li class="nav-item"><a class="nav-link pestana  active bg-<?= GblTraerConfiguracion('colorComponentes'); ?>" href="#vistaGeneral" role="tab">Informacion General</a></li>
										<?php	if(GblPermisos($this,'ProductoEditarModificador',$controlador)){ ?>
											<li class="nav-item"><a class="nav-link pestana  tab-modificadores" href="#vistaModificadores" role="tab">Modificadores</a></li>
										<?php } ?>
										<?php	if(GblPermisos($this,'ProductoEditarInsumoGeneral',$controlador)){ ?>
											<li class="nav-item"><a class="nav-link pestana  tab-receta" href="#vistaReceta" role="tab">Receta <?php //echo ($diferencia != 0) ? '<span class="badge badge-warning">'.$diferencia.'</span>': $diferencia;?></a></li>
										<?php } ?>
									</ul>
								</div>
							</div>
							<br>
							<div class="tab-content" id="myTabContent">
								<div class="row tab-pane fade show active" role="tabpanel" id="vistaGeneral">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<form id="FrmProductoGeneral" autocomplete="off" >
											<?php if($producto):?>
												<div class="row">
													<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
														<div class="row">
															<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
																<div class="form-group">
																	<label for="nombreProducto">Barcode: <span class="text-danger"></span></label>
																	<input type="text" class="form-control upper" id="barcodeProducto" name="barcodeProducto" placeholder="Barcode del Producto" value="<?=$producto->barcodeProducto?>">
																</div>
															</div>
															<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
																<div class="form-group">
																	<label for="nombreProducto">Nombre de Producto: <span class="text-danger">*</span></label>
																	<input type="text" class="form-control upper" id="nombreProducto" name="nombreProducto" placeholder="Nombre del Producto" value="<?=$producto->nombreProducto?>">
																</div>
															</div>
															<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
																<div class="form-group">
																	<label for="Precio">Precio de Venta:<span class="text-danger">*</span></label>
																	<input type="text" class="form-control decimal" id="precioVentaProducto" name="precioVentaProducto" placeholder="$ 00.00" value="<?=$producto->precioVentaProducto?>">
																</div>
															</div>
															<?php if(GblTraerConfiguracion('precioEspecial') == 'Si'):?>
															<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
																<div class="form-group">
																	<label for="PrecioEspecial">Precio Especial:<span class="text-danger">*</span></label>
																	<input type="text" class="form-control decimal" id="precioEspecialProducto" name="precioEspecialProducto" placeholder="$ 00.00" value="<?=$producto->precioEspecialProducto?>">
																</div>
															</div>
															<?php endif;?>
															<?php if(GblTraerConfiguracion('precioEmpleado') == 'Si'):?>
															<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
																<div class="form-group">
																	<label for="PrecioEmpleado">Precio Empleado:<span class="text-danger">*</span></label>
																	<input type="text" class="form-control decimal" id="precioEmpleadoProducto" name="precioEmpleadoProducto" placeholder="$ 00.00" value="<?=$producto->precioEmpleadoProducto?>">
																</div>
															</div>
															<?php endif;?>
															<?php if(GblTraerConfiguracion('impresionRed') == 'Si'):?>
																<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
																	<div class="form-group">
																		<label for="Impresora">Impresora:<span class="text-danger">*</span></label>
																		<select class="select2 form-control col-12" name="impresoraProducto" id="impresoraProducto" value="<?=$producto->nombreProducto?>">
																		<?php if($impresoras !== 0):?>
																			<?php foreach($impresoras as $imp):?>
																				<option value="<?=$imp->idImpresora?>" <?php echo ($imp->idImpresora == $producto->impresoraProducto) ? 'selected' : '';?> ><?=$imp->nombreImpresora?></option>
																			<?php endforeach;?>
																		<?php endif;?>
																		</select>
																	</div>
																</div>
															<?php endif;?>
														</div>
														<div class="row">
															<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
																<div class="form-group">
																	<label for="descripcionProducto">Descripcion</label>
																	<textarea class="form-control upper" name="descripcionProducto" id="descripcionProducto" rows="2"><?=$producto->descripcionProducto?></textarea>
																</div>
															</div>
														</div>
													</div>
													<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
														<label for="imagenProducto">Imagen:</label>
														<input type="file" class="dropify" data-allowed-file-extensions="jpg png jpeg" data-height="170" data-default-file="<?=base_url().$producto->imagenProducto?>" name="imagenProducto" id="imagenProducto">
													</div>
												</div>
												<div class="row">
													<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
														<div class="table-responsive pre-scrollable" style="max-height:225px;">
															<table class="table table-sm table-condensed " id="tablaCategoriaProducto">
																<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																	<tr class="text-center">
																		<th colspan="3">Categoria Producto
																			<a ruta="ProductoListarCategoria" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
																		</th>
																	</tr>
																	<tr>
																		<th class="col-10">Categoria</th>
																		<th class="col-2">Accion</th>
																	</tr>
																</thead>
																<tbody>
																	<?php if($categoriasEspecifica):?>
																		<?php foreach($categoriasEspecifica as $ce):?>
																		<tr>
																			<td>
																				<select class="categoriaProducto select2 col-12 form-control">
																					<?php foreach($categorias as $c): ?>
																						<option value="<?=$c->idProductoCategoria?>"  <?php echo ($c->idProductoCategoria == $ce->idProductoCategoria) ? 'selected':'';?>><?=$c->nombreProductoCategoria?></option>
																					<?php endforeach;?>
																				</select>
																			</td>
																			<td>
																				<a class="btn btn-block btn-danger borrarTr" role="button"><i class="fa fa-trash"></i></a>
																			</td>
																		</tr>
																		<?php endforeach;?>
																	<?php endif;?>
																</tbody>
															</table>
														</div>
													</div>
													<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
														<div class="table-responsive pre-scrollable" style="max-height:225px;">
															<table class="table table-sm table-hover" id="tablaComoModificador">
																<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																	<tr class="text-center">
																		<th colspan="3">Hacer Modificador
																			<a ruta="ProductoListarModificadorTipo" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
																		</th>
																	</tr>
																	<tr>
																		<th class="col-10">Tipo de Modificador</th>
																		<!-- <th class="col-3">Precio</th> -->
																		<th class="col-2">Accion</th>
																	</tr>
																</thead>
																<tbody>
																	<?php if($modificadores):?>
																		<?php foreach($modificadores as $ce):?>
																		<tr>
																			<td>
																				<select class="modificadorTipo select2 col-12 form-control">
																					<?php foreach($modificadoresTipo as $c): ?>
																						<option value="<?=$c->idModificadorTipo?>"  <?php echo ($c->idModificadorTipo == $ce->idModificadorTipo) ? 'selected':'';?>><?=$c->nombreModificadorTipo?></option>
																					<?php endforeach;?>
																				</select>
																			</td>
																			<td>
																				<a class="btn btn-block btn-danger borrarTr" role="button"><i class="fa fa-trash"></i></a>
																			</td>
																		</tr>
																		<?php endforeach;?>
																	<?php endif;?>
																</tbody>
															</table>
														</div>
													</div>

												</div>
												<br>
												<div class="row">
													<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
														<input type="hidden" id="idProducto" name="idProducto" value="<?php echo $idProducto ?>">
														<input type="hidden" name="categoriasProducto" id="categoriasProducto" class="form-control" value="">
														<input type="hidden" name="comoModificadoresProducto" id="comoModificadoresProducto" class="form-control" value="">
														<!-- <a data-toggle="modal" data-target="#modalModificador" data-refresh="true" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?>  float-left"><i class="fa fa-outdent"></i> Agregar Como Modificador</a> -->
														<button type="submit" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> float-right"><i class="fa fa-save"></i> Guardar</button>
													</div>
												</div>
											<?php endif;?>
										</form>
									</div>
								</div>
								<div class="row tab-pane fade" role="tabpanel" id="vistaModificadores">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<form id="FrmProductoModificadores" autocomplete="off">
											<?php if($producto):?>
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="table-responsive pre-scrollable" style="max-height:750px;">
														<table class="table table-sm table-condensed " id="tablaModificadores">
															<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																<tr class="text-center">
																	<th colspan="3">Modificadores
																		<a ruta="ProductoListarModificador" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
																	</th>
																</tr>
																<tr>
																	<th class="col-8">Categoria</th>
																	<th class="col-3">Cantidad</th>
																	<th class="col-1">Accion</th>
																</tr>
															</thead>
															<tbody>
															<?php if($modificadoresProd):?>
																	<?php foreach($modificadoresProd as $ce):?>
																	<tr>
																		<td colspan="3">
																			<div class="row mt-2">
																				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12">
																					<select class="select2 form-control productoModificador">
																						<?php
																						$padre = "";
																						foreach($modificadoresTipo as $c): ?>
																							<?php ($c->idModificadorTipo == $ce->idModificadorTipo) ? $padre = $c->nombreModificadorTipo : '';?>
																							<option value="<?=$c->idModificadorTipo?>"  <?php echo ($c->idModificadorTipo == $ce->idModificadorTipo) ? 'selected':'';?>  idUnico="<?=$ce->idUnicoProductoModificador?>" idUnicoSelect="<?=$ce->idUnicoSelectProductoModificador?>" varios="<?=$ce->variosProductoModificador?>" nombre="<?=$c->nombreModificadorTipo?>"><?=$c->nombreModificadorTipo?></option>
																						<?php endforeach;?>
																					</select>
																				</div>
																				<div class="col-xl-2 col-lg-2 col-md-2 col-sm-10 col-xs-10">
																					<input type="text" class="form-control numeric cantidadModificador" value='<?=$ce->cantidadProductoModificador?>' title="Cantidad de selecciones / maximo si es seleccion multiple">
																				</div>
																				<div class="col-xl-3 col-lg-3 col-md-3 col-sm-10 col-xs-10">
																					<div class="icheck-primary d-inline">
																						<input type="checkbox" class="multiSeleccionModificador" id="multiSeleccion<?=$ce->idProductoModificador?>" <?php echo ($ce->multiSeleccionProductoModificador == 1) ? 'checked' : ''; ?>>
																						<label for="multiSeleccion<?=$ce->idProductoModificador?>">Seleccion multiple</label>
																					</div>
																				</div>
																				<div class="col-1">
																					<a class="btn btn-block btn-danger borrarTr btnBorrarModificador" idunicoselect="<?=$ce->idUnicoSelectProductoModificador?>" role="button"><i class="fa fa-trash"></i></a>
																				</div>
																			</div>
																			<div class="row">
																				<div class="col-12 detalleModificadoresTipo pre-scrollable" style="max-height:500px;">
																					<div class="row listaDetalleModificadores">
																						<div class="col-12 mt-2">
																							<?php if($ce->modificadores):?>
																								<div class="row">
																								<?php
																									// echo json_encode($ce->modificadores);
																									$n = 0;
																									foreach($ce->modificadores as $mod):
																										$aumento = '0.00';?>

																									<div class="col-xl-4 col-lg-4 col-sm-4 col-xs-12  py-1 ">
																										<div class="row grupoDetalleModificador">
																											<div class="col-6">
																												<div class="icheck-primary d-inline">
																													<?php if($modificadoresProdDet ){?>
																														<?php foreach($modificadoresProdDet as $mpd): $existe = false;?>
																															<?php if($mpd->idModificador == $mod->idModificador){?>
																																<input type="checkbox" checked idUnicoAbuelo="<?=$mpd->idUnicoAbueloProductoModificadorDetalle?>" idUnicoPadre="<?=$mpd->idUnicoPadreProductoModificadorDetalle?>" idUnico="<?=$mpd->idUnicoProductoModificadorDetalle?>" varios="<?=$ce->variosProductoModificador?>" tipo="<?=$mpd->idModificadorTipoProductoModificadorDetalle?>" nombre="<?=$mod->nombreModificador?>" nombrePadre="<?=$padre?>" class="modificadorDetalle" id="<?=$mod->idModificador?>">
																																<label for="<?=$mod->idModificador?>"><?=$mod->nombreModificador?></label>
																																<?php
																																	$aumento = $mpd->aumentoProductoModificadorDetalle;
																																	$existe = true;
																																	break;}?>
																														<?php endforeach;?>
																														<?php if(!$existe){?>
																																<input type="checkbox" idUnicoAbuelo="<?=$ce->idUnicoSelectProductoModificador?>" idUnicoPadre="<?=$ce->idUnicoProductoModificador?>" idUnico="<?php echo uniqid();?>" varios="<?=$ce->variosProductoModificador?>" tipo="<?=$mpd->idModificadorTipoProductoModificadorDetalle?>" nombre="<?=$mod->nombreModificador?>" nombrePadre="<?=$padre?>" class="modificadorDetalle" id="<?=$mod->idModificador?>">
																																<label for="<?=$mod->idModificador?>"><?=$mod->nombreModificador?></label>
																														<?php }?>
																													<?php }else{?>
																															<input type="checkbox" idUnicoAbuelo="<?=$ce->idUnicoSelectProductoModificador?>" idUnicoPadre="<?=$ce->idUnicoProductoModificador?>" idUnico="<?php echo uniqid();?>" varios="<?=$ce->variosProductoModificador?>" tipo="<?=$mod->idModificadorTipo?>" nombre="<?=$mod->nombreModificador?>" nombrePadre="<?=$padre?>" class="modificadorDetalle" id="<?=$mod->idModificador?>">
																															<label for="<?=$mod->idModificador?>"><?=$mod->nombreModificador?></label>
																													<?php }?>
																												</div>
																											</div>
																											<div class="col-6">
																												<input type="text" class="form-control decimal aumentoModificadorDetalle" placeholder="Aumento" value="<?=$aumento?>">
																											</div>
																										</div>
																									</div>
																										<?php
																										$n++;
																										if($n == 3){
																											echo '</div>';
																											echo '<div class="row">';
																											$n = 0;
																										}
																										endforeach;?>
																										<?php endif;?>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>

																	</tr>
																	<?php endforeach;?>
																<?php endif;?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<br>
													<button type="button" id="insertarModificadores" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> float-right">Guardar Modificadores</button>
												</div>
											</div>

											<?php endif;?>
										</form>
									</div>
								</div>
								<div class="row tab-pane fade" role="tabpanel" id="vistaReceta">
								<?php if($producto):?>
									<div class="row">

									<div class="col-12">

									</div>
										<div class="col-4">
											<div class="nav flex-column nav-tabs tabs-recetas" id="v-pills-tab" role="tablist" aria-orientation="vertical">
												<a class="nav-link tabInsumoMod" id="v-pills-general-tab" data-toggle="pill" href="#v-pills-general" role="tab" aria-controls="v-pills-home" aria-selected="true" idUnico="general">Insumos Generales</a>
												<?php  $nuevo = 0;$viejo = 0; $modificado = 0; if($modificadoresProdDet):?>
													<?php  foreach($modificadoresProdDet as $ce):?>
														<?php
															$junto = '';
															// $actual = (is_array($ce->modificadoresInsumoActuales)) ? count($ce->modificadoresInsumoActuales) : 0;
															// $real = (is_array($ce->modificadoresInsumoReales)) ? count($ce->modificadoresInsumoReales) : 0;
															// if( $actual - $real == 0){ $junto .= ''; }
															// if( $actual - $real < 0){ $junto .= '<span class="badge badge-warning">Nuevo</span>'; }
															// if( $actual - $real > 0){ $junto .= '<span class="badge badge-danger">Viejo</span>'; }
														?>
														<?php if($ce->modificadoresInsumo != ""):
															if($ce->etiquetaNuevo != ""){$nuevo ++;}
															if($ce->etiquetaViejo != ""){$viejo ++;}
															if($ce->etiquetaModificado != ""){$modificado ++;}
															?>
															<a class="nav-link tabInsumoMod " idunicoabuelo="<?=$ce->idUnicoAbueloProductoModificadorDetalle?>" idunicopadre="<?=$ce->idUnicoPadreProductoModificadorDetalle?>" idunico="<?=$ce->idUnicoProductoModificadorDetalle?>" idmod="<?=$ce->idModificador?>" idtipo="<?=$ce->idModificadorTipoProductoModificadorDetalle?>" id="tab<?=$ce->idModificador?>" data-toggle="pill" href="#v-pills-general" role="tab" aria-controls="v-pills-general" aria-selected="true"><?=$ce->nombrePadreProductoModificadorDetalle?> (<?=$ce->nombreProductoModificadorDetalle?>) <?=$ce->etiquetaNuevo?> <?=$ce->etiquetaViejo?>  <?=$ce->etiquetaModificado?></a>
														<?php endif;?>
													<?php  endforeach;?>
												<?php endif;?>
											</div>
										</div>
										<div class="col-8">
											<div class="tab-content" id="v-pills-tabContent">
												<div class="tab-pane  show active" id="v-pills-general" role="tabpanel">
													<div class="row">
														<div class="col-12">
															<?php if($nuevo > 0):?>
																<div class="alert alert-<?=GblTraerConfiguracion("colorComponentes")?> alert-dismissible fade show">
																	<strong>Informacion: </strong> Todos los insumos con la etiqueta <span class="badge badge-warning">Nuevo</span> son insumos que fueron agregados al modificador despues de haber creado este producto.
																	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																</div>
															<?php endif;?>
															<?php if($viejo > 0):?>
																<div class="alert alert-<?=GblTraerConfiguracion("colorComponentes")?> alert-dismissible fade show">
																	<strong>Informacion: </strong> Todos los insumos con la etiqueta <span class="badge badge-danger">Viejo</span> son insumos que fueron eliminados del modificador despues de haber creado este producto.
																	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																</div>
															<?php endif;?>
															<?php if($modificado > 0):?>
																<div class="alert alert-<?=GblTraerConfiguracion("colorComponentes")?> alert-dismissible fade show">
																	<strong>Informacion: </strong> Todos los insumos con la etiqueta <span class="badge badge-info">Modificado</span> son insumos cuya presentacion o cantidad fueron modificados en el modificador despues de haber creado este producto.
																	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																</div>
															<?php endif;?>
														</div>
													</div>
													<div class="row">
														<div class="col-12">
															<div class="form-group">
																<label for="buscarInsumo">Buscar Insumo</label>
																<input type="text" id="buscarInsumo" name="buscarInsumo" class="form-control" placeholder="Buscar Insumo">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<div class="table-responsive pre-scrollable" style="max-height:500px;">
																<table class="table table-sm table-condensed" id="tablaInsumoGeneral">
																	<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																		<tr class="text-center">
																			<th colspan="4">Insumos del Producto (Receta)
																				<!-- <a id="agregarInsumoGeneral" ruta="ProductoListarInsumoGeneral" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
																			</th>
																		</tr>
																		<tr>
																			<th class="col-4">Insumo</th>
																			<th class="col-4">Presentacion</th>
																			<th class="col-3">Cantidad</th>
																			<th class="col-1">Acciones</th>
																		</tr>
																	</thead>
																	<tbody>
																	<?php if($insumos):?>
																		<?php foreach($insumos as $ce):?>
																		<tr idModificador= "0" idUnico="general">
																			<td class="nombreTipo" idInsumo="<?=$ce->idInsumo?>"><?=$ce->nombreInsumo?></td>
																			<td>
																				<select class="presentacion select2 col-12 form-control">
																					<?php foreach($ce->presentaciones as $c): ?>
																						<option value="<?=$c->idPresentacion?>"  <?php echo ($c->idPresentacion == $ce->idPresentacionProductoInsumo) ? 'selected':'';?>><?=$c->nombrePresentacion?></option>
																					<?php endforeach;?>
																				</select>
																			</td>
																			<td>
																				<input type="text" class="form-control decimal cantidad" value="<?=$ce->cantidadProductoInsumo?>">
																			</td>
																			<td>
																				<input type="hidden" class="incluirInsumo" value="1">
																				<a class="btn btn-block btn-danger borrarTr" role="button"><i class="fa fa-trash"></i></a>
																			</td>
																		</tr>
																		<?php endforeach;?>
																	<?php endif;?>
																	<?php if($modificadoresProdDet):?>
																		<?php  foreach($modificadoresProdDet as $ce):?>
																			<?php if($ce->modificadoresInsumo != ""):?>
																				<?php

																					// $actual = count($ce->modificadoresInsumoActuales);
																					// $real = count($ce->modificadoresInsumoReales);
																					// $diferencia1 = $actual - $real;
																					// if( $diferencia1 == 0){ $principal = $ce->modificadoresInsumoActuales; $secundario = $ce->modificadoresInsumoReales; }
																					// if( $diferencia1 > 0){ $principal = $ce->modificadoresInsumoActuales; $secundario = $ce->modificadoresInsumoReales; }
																					// if( $diferencia1 < 0){ $principal = $ce->modificadoresInsumoReales;  $secundario = $ce->modificadoresInsumoActuales; }

																					?>
																				<?php $nCheck = 0;
																					foreach($ce->modificadoresInsumo as $cee){?>
																					<?php
																						$etiqueta = ($cee["tipo"] == "nuevo") ? '<span class="badge badge-warning">Nuevo</span>' : (
																									($cee["tipo"] == "viejo") ? '<span class="badge badge-danger">Viejo</span>' : (
																									($cee["tipo"] == "modificado") ? '<span class="badge badge-info">Modificado</span><br>Presentacion :'.$cee["preAnterior"].'<br>Cantidad :'.$cee["canAnterior"] : ""));
																						$junto = '';

																					?>
																					<tr class="" idUnico="<?=$ce->idUnicoProductoModificadorDetalle?>" idModificador= "<?=$ce->idModificador?>">
																						<td class="nombreTipo" idInsumo="<?=$cee['idInsumo']?>"><?=$cee['nombreInsumo']?> <?=$etiqueta?></td>
																						<td>
																							<select class="presentacion select2 col-12 form-control">
																								<?php foreach($cee['presentaciones'] as $c): ?>
																									<option value="<?=$c->idPresentacion?>"  <?php echo ($c->idPresentacion == $cee['idPresentacionProductoInsumo']) ? 'selected':'';?>><?=$c->nombrePresentacion?></option>
																								<?php endforeach;?>
																							</select>
																						</td>
																						<td>
																							<input type="text" class="form-control numeric cantidad" value="<?=$cee['cantidadProductoInsumo']?>">
																						</td>
																						<td>
																							<?php if($cee["tipo"] == "viejo"):?>
																								<input type="hidden" class="incluirInsumo" value="1">
																								<a class="btn btn-block btn-danger borrarTr" role="button"><i class="fa fa-trash"></i></a>
																							<?php endif;?>
																							<?php if($cee["tipo"] == "nuevo"){?>
																								<div class="icheck-<?=GblTraerConfiguracion("colorComponentes")?> mt-1 ">
																									<input type="checkbox" class="checkIncluir" id="check<?=$nCheck?>" ><label for="check<?=$nCheck?>"> Incluir</label>
																								</div>
																								<input type="hidden" class="incluirInsumo" value="0">
																							<?php }else{;?>
																								<input type="hidden" class="incluirInsumo" value="1">
																							<?php };?>
																						</td>
																					</tr>
																				<?php $nCheck ++; };?>
																			<?php endif;?>
																		<?php  endforeach;?>
																	<?php endif;?>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<button type="button" id="insertarRecetaGeneral" class="btn btn-primary ">Guardar Receta</button>
														</div>
													</div>

												</div>
												<div class="tab-pane " id="v-pills-home" role="tabpanel">
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<div class="table-responsive pre-scrollable" style="max-height: 500px;">
																<table id="tablaModificadoresInsumosReceta" class="table table-sm table-condensed">
																	<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
																		<tr class="text-center">
																			<th colspan="4">Insumos Por Modificador
																				<!-- <a id="agregarInsumoGeneral" ruta="ProductoListarInsumoGeneral" class="agregarTr float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus" aria-hidden="true"></i></a> -->
																			</th>
																		</tr>
																		<tr>
																			<th class="col-5">Insumo</th>
																			<th class="col-5">Presentacion</th>
																			<th class="col-2">Cantidad</th>
																		</tr>
																	</thead>
																	<tbody>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
															<button type="button" id="insertarRecetaModificador" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?>">Guardar Receta</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

								<?php endif;?>


								</div>
							</div>
						</div>
						<!-- /.card-body -->

						<div class="card-footer">

						</div>
					</div>
					<!-- /.card -->
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->
			<!-- Button trigger modal -->
			<!-- Modal -->
			<div class="modal fade" id="modalModificador" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
							<h5 class="modal-title">Agregar Como Modificador</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="form-group">
										<label for="">Buscar Tipo Modificador</label>
										<input type="text" class="form-control" name="buscarTipoModificador" id="buscarTipoModificador" placeholder="Buscar Tipo Modificador">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<!-- <table class="table table-sm table-hover" id="tablaComoModificador">
										<thead>
											<tr>
												<th class="col-8">Tipo de Modificador</th>
												<th class="col-3">Precio</th>
												<th class="col-1">Accion</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table> -->
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
							<!-- <button type="button" class="btn btn-primary">Save</button> -->
						</div>
						<!-- /.content-wrapper -->
					</div>
				</div>
			</div>
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
