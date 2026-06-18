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
          <form id="FrmMovimiento" autocomplete="off">
            <div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
              <div class="card-header">
                <h3 class="card-title"><?=$titulo?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <?php $fecha_actual = date("Y-m-d");?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                      <div class="form-group has-info">
                        <label>Tipo</label>
                        <select class="form-control select2" id="tipo" name='tipo'>
                          <option value="">Seleccione</option>
                          <option value='Vencimiento'>Vencimiento</option>
                          <option value='Descarte'>Descarte</option>
                          <option value='Dañado'>Producto Dañado</option>
                          <option value='Consumo'>Consumo Interno</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                      <div class="form-group has-info">
                        <label>Concepto</label>
                        <input type='text' class='form-control' value='' id='concepto' name='concepto'>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                      <div class='form-group has-info'>
                        <label>Fecha</label>
                        <input type='date' class='form-control' value='<?php echo $fecha_actual; ?>' id='fecha1' name='fecha1'>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="row" id='buscador'>
                    <div class="col-lg-6">
                      <div class='form-group has-info'><label>Buscar Producto</label>
                        <input type="text" id="producto_buscar" name="producto_buscar" class="producto_buscar form-control" placeholder="Ingrese nombre de producto"  data-provide="typeahead">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                        <div class="ibox-content">
                          <section>
                            <table class="table table-sm table-condensed" id="inventable">
                              <thead class="bg-<?=GblTraerConfiguracion('colorComponentes');?>">
                                <tr class="text-center">
                                  <th colspan="8">Lista Productos
                                  </th>
                                </tr>
                                <tr>
                                  <th class="col-lg-1">Id</th>
                                  <th class="col-lg-5">Nombre</th>
                                  <th class="col-lg-2">Presentación</th>
                                  <th class="col-lg-1">Prec. C</th>
                                  <th class="col-lg-1">Prec. V</th>
                                  <th class="col-lg-1">Existencia</th>
                                  <th class="col-lg-1">Cantidad</th>
                                  <th class="col-lg-1">Acci&oacute;n</th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                              <tfoot>
                                <tr>
                                  <td></td>
                                  <td>Total Dinero <strong>$</strong></td>
                                  <td id='total_dinero'>$0.00 </td>
                                  <td colspan=2>Total Producto</td>
                                  <td id='totcant'>0</td>
                                  <td></td>
                                  <td></td>
                                </tr>
                              </tfoot>

                            </table>
                            <input type="hidden" name="datos" id="datos" value="false-0">
                            <input type="hidden" name="total_dineroh" id="total_dineroh">
                          </section>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" id="btnGuardar" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Guardar</button>
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
