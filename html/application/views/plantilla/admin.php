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
          <h4 class="text-<?= GblTraerConfiguracion('colorComponentes'); ?>"><i class="<?= $icono ?>"></i> <?= $titulo ?></h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes'); ?>" href="<?= base_url(); ?>">Inicio</a></li>
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
          <div class="card">
            <div class="card-header">
              <div class="row">
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
              <div class="row">
               <div class="col-12 d-flex justify-content-end flex-wrap" style="gap:6px;">
                <?php if (count($botones) > 0) :
                    foreach ($botones as $boton) :
                      if (GblPermisos($this, $boton["url"], $boton["controlador"])) :
                        if ($boton["modal"] == true) { ?>
                          <a data-toggle='modal' data-target="viewModal" data-refresh="true" id="<?= $boton["id"] ?>" class="btn btn-<?= $boton["tipo"] ?> btn-sm"><i class="<?= $boton["icono"] ?>"></i> <?= $boton["txt"] ?></a>
                        <?php
                        } else {
                        ?>
                          <a href="<?= base_url() . $boton["url"]; ?>" class="btn btn-<?= $boton["tipo"] ?> btn-sm"><i class="<?= $boton["icono"] ?>"></i> <?= $boton["txt"] ?></a>
                  <?php }
                      endif;
                    endforeach;
                  endif; ?>
               </div>
              </div>
              <div class="row">
              <?php if (isset($buscador) && $buscador == true) : ?>
                <div class="col-4">
                  <label for="buscadorTablaTexto">Buscador</label>
                  <input type="text" class="form-control " placeholder="Buscar" id="buscadorTablaTexto">
                  <input type="hidden" id="buscadorTabla" value="1">
                </div>
              <?php endif; ?>
                <input type="hidden" placeholder="Buscar" id="buscadorTabla" value="0">
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
<?php if (isset($proceso)) { ?>
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

