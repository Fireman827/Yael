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
				<div class="col-sm-6" hidden>
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
							<div class="card-body">
                <div class="row" style="text-align: center">
                  <div class="col-md-12" style="text-align: center;">
                    <label class="badge badge-danger" style="font-size: 15px; text-align: center">Advertencia!! Si el campo de Existencia Real se envia en cero el inventario se ajustara a esa cantidad.</label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<label for="insumoCategoriaRevision">Categoria</label>
											<select class="select2 form-control" id="insumoCategoriaRevision" name="insumoCategoriaRevision">
                        <option value='All'>General</option>
                        <?php
                            if ($categorias)
                            {
                              foreach ($categorias as $key)
                              {
                                echo "<option value='".$key->idInsumoCategoria."'>".$key->nombreInsumoCategoria."</option>";
                              }
                            }
                        ?>
											</select>
										</div>
									</div>
                  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                    <div class="form-group">
                      <label for="buscadorInsumo">Buscar insumo</label>
                      <input type="text" class="form-control pull-right" id="buscadorInsumo" placeholder="Escriba para buscar en la tabla...">
                    </div>
                  </div>
                </div>
								<div class="row">
                  <table class="table table-hover table-sm dataTable dtr-inline" role="grid" aria-describedby="tablaTurno_info">
                    <thead>
                      <tr>
                        <th class="col-lg-1">ID</th>
                        <th class="col-lg-4">Nombre</th>
                        <!-- <th class="col-lg-4">Descripción</th> -->
                        <th class="col-lg-4">Presentacion</th>
                        <th class="col-lg-1">Existencia</th>
                        <th class="col-lg-2">Exist. Real</th>
                      </tr>
                    </thead>
                    <tbody id="tablaRevisionInsumos">
                      <?php
                          if ($insumo)
                          {
                            $n = 1;
                            foreach ($insumo as $fila)
                            {
                              $idInsumo = $fila->idInsumo;
                              $nombreInsumo = $fila->nombreInsumo;
                              $descripcionInsumo = $fila->descripcionInsumo;
                              $cantidadInsumoStock = $fila->cantidadInsumoStock;
                              $descripcionInsumoPresentacion = $fila->descripcionInsumoPresentacion;
                              $unidadInsumoPresentacion = $fila->unidadInsumoPresentacion;
                              $idInsumoPresentacion = $fila->idInsumoPresentacion;
                              $nombrePresentacion = $fila->nombrePresentacion;
                              $idCategoriaInsumo = $fila->idCategoriaInsumo;
                              $costoInsumoPresentacion = $fila->costoInsumoPresentacion;
                              $precioInsumoPresentacion = $fila->precioInsumoPresentacion;
                              $existenciaUnidad = number_format($cantidadInsumoStock/$unidadInsumoPresentacion, 2, '.', ',');
                              echo "<tr class='fila".$idCategoriaInsumo."'>";
                              echo "<td><input type='hidden' id='idInsumo' value='".$idInsumo."'><input type='hidden' id='idInsumoPresentacion' value='".$idInsumoPresentacion."'><input type='hidden' id='unidadInsumoPresentacion' value='".$unidadInsumoPresentacion."'>".$n."</td>";
                              echo "<td><input type='hidden' id='precioInsumoPresentacion' value='".$precioInsumoPresentacion."'><input type='hidden' id='nombreInsumo' value='".$nombreInsumo."'>".$nombreInsumo."</td>";

                              echo "<td><input type='hidden' id='descripcionInsumo' value='".$descripcionInsumo."'><input type='hidden' id='descripcionInsumoPresentacion' value='".$nombrePresentacion."'>".$nombrePresentacion."</td>";
                              echo "<td><input type='hidden' id='cantidadInsumoStock' value='".$existenciaUnidad."'><input type='hidden' id='existenciaMinima' value='".$cantidadInsumoStock."'>".$existenciaUnidad."</td>";
                              echo "<td><input type='hidden' id='costoInsumoPresentacion' value='".$costoInsumoPresentacion."'><input type='text' class='form-control decimal' id='existenciaReal' value=''></td>";
                              echo "</tr>";
                              $n +=1;
                            }
                          }
                      ?>
                    </tbody>
                  </table>
								</div>

							</div>
							<!-- /.card-body -->

							<div class="card-footer">
                <input type="hidden" name="idCorte" id="idCorte" value="<?php echo $idCorte; ?>">
								<input type="hidden" name="avanzado" id="avanzado" value="false">
								<input type="hidden" name="proceso" id="proceso" value="Ver">
								<button class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right" id="btnRevisarInusmo"><i class="fa fa-save"></i> Guardar</button>
							</div>
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
