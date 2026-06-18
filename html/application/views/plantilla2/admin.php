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
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a class="font-weight-bold text-<?=GblTraerConfiguracion('colorComponentes');?>" href="<?=base_url();?>">Inicio</a></li>
            <li class="breadcrumb-item font-weight-bold active"><?=$titulo;?></li>
          </ol>
        </div>
      </div>
   
  </section><!-- Main content -->
  <section class="content pt-2">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <?php if (count($botones)>0):
                 $contador=0;
                 foreach ($botones as $boton):
                   if (GblPermisos($this,$boton["url"],$boton["controlador"])): ?>
                    <a <?php if ($contador>0) : ?> style="margin-right:1%;" <?php endif; ?> href="<?=base_url().$boton["url"];?>" class="btn btn-<?=$boton["tipo"]?> btn-sm float-<?=$boton["posicion"]?>"><i class="<?=$boton["icono"]?>"></i> <?=$boton["txt"]?></a>
                    <?php
                    endif;
                  $contador++;
                  endforeach;
                endif;
              ?>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="tablaAdmin" class="table table-hover table-sm">
                <thead>
                  <tr>
                    <?php foreach ($encabezados as $encabezado => $tamano): ?>
                      <th class="col-lg-<?=$tamano?> col-md-<?=$tamano?> col-sm-<?=$tamano?>"><?=$encabezado?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <?php foreach ($encabezados as $encabezado => $tamano): ?>
                      <th class="col-lg-<?=$tamano?> col-md-<?=$tamano?> col-sm-<?=$tamano?>"><?=$encabezado?></th>
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


<div class='modal  fade' id='viewModal' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-md'>
        <div class='modal-content modal-md'>
		</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
