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
          <h4 class="text-<?=GblTraerConfiguracion('colorComponentes');?>"><i class="<?=$icono?>"></i> <?=$titulo?></h4>
        </div>
        <div class="col-sm-6">
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section><!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
              <div class="card-header">
                <div class="row">
                    <div class="col-3">
                        <div id="divHora">
                            
                        </div>
                    </div>
                    <?php if (count($botones)>0):
                        $contador=0;
                        foreach ($botones as $boton):?>
                          <div class="col-3">
                            <a <?php if ($contador>0) : ?> style="margin-right:1%;" <?php endif; ?> href="<?=base_url().$boton["url"];?>" id="<?= $boton["id"] ?>" class="btn btn-<?=$boton["tipo"]?> btn-block btn-lg float-<?=$boton["posicion"]?>"><i class="<?=$boton["icono"]?>"></i> <?=$boton["txt"]?></a>
                          </div>
                        <?php
                        $contador++;
                        endforeach;
                        endif;
                    ?>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <table id="tablaAdmin" class="table table-hover table-sm">
                <thead>
                  <tr>
                    <?php foreach ($encabezados as $encabezado => $tamano) : ?>
                      <th class="col-lg-<?= $tamano ?> col-md-<?= $tamano ?> col-sm-<?= $tamano ?>"><?= $encabezado ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <?php foreach ($encabezados as $encabezado => $tamano) : ?>
                      <th class="col-lg-<?= $tamano ?> col-md-<?= $tamano ?> col-sm-<?= $tamano ?>"><?= $encabezado ?></th>
                    <?php endforeach; ?>
                  </tr>
                </tfoot>
              </table>
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
<!-- /.content-wrapper -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if(isset($proceso)){ ?>
<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>