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
          <!-- <h4><i class="<=$icono?>"></i> <=$titulo?></h4> -->
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes');?>" href="<?=base_url();?>">Inicio</a></li>
            <li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes');?>" href="<?=base_url().$controlador;?>"><?=ucfirst($controlador);?></a></li>
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
            <form id="FrmContratosTipo" autocomplete="off">
              <div class="card-body">								
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="form-group">
                      <label for="nombreContratoTipo">Nombre: <span class="text-danger">*</span></label>
                      <input type="text" name="nombreContratoTipo" id="nombreContratoTipo" class="form-control upper text-uppercase" placeholder="Nombre del tipo de contrato" value="<?=$datosContratosTipo->nombreContratoTipo; ?>" >
                    </div>
                  </div>
                  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                    <div class="form-group">
                      <label for="horarioContrato">Clausulas disponibles <span class="text-danger">*</span></label>
                      <select class='select2' id='idContratoClausula' name='idContratoClausula' style="width: 100%;" >
                        <option value="" >Seleccione</option>
                        <?=$contratoClausulaOpciones; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
                    <button type="button" class="btn btn-block btn-<?= GblTraerConfiguracion('colorComponentes'); ?> agregarContratoClausula" style="margin-top: 31px;">Agregar</button>
                  </div>  
                </div>
                <div class="row mt-1">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="table-responsive" style="height: 245px;">
                      <table class="table table-sm table-condensed float rounded" id="tablaContratoTipoClausula">
                        <thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
                          <tr class="text-center">
                            <th colspan="12">Clausulas de Contrato Agregadas</th>
                          </tr>
                          <tr>
                            <th class="col-1">ID</th>
                            <th class="col-3">CLAUSULA</th>																											
                            <th class="col-7">DESCRIPCIÓN</th>																											
                            <th class="col-1">ACCIÓN</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?=$contratoTipoClausula; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>		
                <!-- <div class="row" hidden>
                  <div class="col">
                    <input type="hidden" name="idContratoTipo" id="idContratoTipo" value="<?=$datosContratosTipo->idContratoTipo; ?>">
                  </div>
                </div> -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <input type="hidden" name="idContratoTipo" id="idContratoTipo" value="<?=$datosContratosTipo->idContratoTipo?>">
                <button type="submit" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Guardar</button>
              </div>
            </form>
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