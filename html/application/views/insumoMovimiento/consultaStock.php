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
						<!-- <li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes')?>" href="<?=base_url().$controlador;?>"><?=ucfirst($controlador);?></a></li> -->
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
          <form id="FrmMovimiento" autocomplete="off">
            <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
              <div class="card-header">
                <h3 class="card-title"><?=$titulo?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <?php $fecha_actual = date("Y-m-d");?>
                <div class="card-body">
                  <div class="row" id='buscador'>
                    <div class="col-lg-4">
                      <div class='form-group has-info'><label>Buscar Producto</label>
                        <input type="text" id="productoBuscar" name="productoBuscar" class="productoBuscar form-control" placeholder="Ingrese nombre de producto" >
                      </div>
                    </div>
                    <div class="col-4">
                      <?php if (isset($categorias)) { ?>
                        <label for="categoria">Categoria</label>
                        <select name="categoria" id="categoria" class="form-control col-12 select2">
                          <?php if ($categorias !== false) : ?>
                            <option value="All">Todas Las Categoria</option>
                            <?php foreach ($categorias as $categoria) : ?>
                              <option value="<?= $categoria->idCategoria ?>"><?= $categoria->nombreCategoria; ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      <?php }else{ ?>
                        <input type="hidden" name="categoria" id="categoria" class="form-control" value="All">
                      <?php } ?>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                        <?php if ($admin != 0 && GblTraerConfiguracion('Sucursales') == 'Si') { ?>
                          <select name="sucursal" id="sucursal" class="form-control col-3 select2">
                            <?php if ($sucursales !== false) : ?>
                              <?php foreach ($sucursales as $sucursal) : ?>
                                <option value="<?= $sucursal->idSucursal ?>" <?php echo ($sucursal->idSucursal ==$idSucursal)? 'selected':''; ?>><?= $sucursal->nombreSucursal; ?></option>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </select>
                        <?php }else{ ?>
                          <input type="hidden" name="sucursal" id="sucursal" class="form-control" value="<?=$idSucursal?>">
                        <?php } ?>
                        </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                        <div class="ibox-content">
                          <section>
                            <table class="table table-sm table-condensed" id="tablaAdmin">
                              <thead class="bg-<?=GblTraerConfiguracion('colorComponentes');?>">
                                <tr class="text-center">
                                  <th colspan="8">Existencia
                                  </th>
                                </tr>
                                <tr>
                                  <th class="col-lg-1">Id</th>
                                  <th class="col-lg-2">Nombre</th>
                                  <th class="col-lg-1">Categoria</th>
                                  <th class="col-lg-3">Descripcion</th>
                                  <th class="col-lg-1">Unidad Inv.</th>
                                  <th class="col-lg-2">Stock Min.</th>
                                  <th class="col-lg-1">Existencia</th>
                                  <th class="col-lg-1">Acci&oacute;n</th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                              <!-- <tfoot>
                                <tr>
                                  <td></td>
                                  <td>Total Dinero <strong>$</strong></td>
                                  <td id='total_dinero'>$0.00 </td>
                                  <td colspan=2>Total Producto</td>
                                  <td id='totcant'>0</td>
                                  <td></td>
                                  <td></td>
                                </tr>
                              </tfoot> -->

                            </table>
                          </section>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <!-- <button type="submit" id="btnGuardar" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Guardar</button> -->
                </div>
            </div>
            <!-- /.card -->
          </form> 
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

<!-- Modal pequeña -->
<div class="modal fade" id="smModal" data-backdrop="static" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal normal (default) -->
<div class="modal fade" id="dfModal" data-backdrop="static" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal grande -->
<div class="modal fade" id="lgModal" data-backdrop="static" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal extra grande -->
<div class="modal fade" id="xlModal" data-backdrop="static" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>