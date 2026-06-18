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
              <?php if ($admin != 0) { ?>
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
                <div class="col-md-2" >
                    <select name="cajaAperturaActual" id="cajaAperturaActual" class="form-control col-3 select2" style="width:100%" hidden>
                      <?php
                      echo $cajasReal;
                      ?>
                    </select>
                </div>
              <!-- <?php if (count($botones) > 0) :
                $contador = 0;
                foreach ($botones as $boton) :
                  if (GblPermisos($this, $boton["url"], $boton["controlador"])) :
                    if ($boton["modal"] == true) { ?>
                      <a <?php if ($contador > 0) : ?> style="margin-right:1%;" <?php endif; ?> data-toggle='modal' data-target="viewModal" data-refresh="true" id="<?= $boton["id"] ?>" class="btn btn-<?= $boton["tipo"] ?> btn-sm  float-<?= $boton["posicion"] ?> m-t-n-xs"><i class="<?= $boton["icono"] ?>"></i> <?= $boton["txt"] ?></a>
                    <?php
                    } else {
                    ?>
                      <a <?php if ($contador > 0) : ?> style="margin-right:1%;" <?php endif; ?> href="<?= base_url() . $boton["url"]; ?> " class="btn btn-<?= $boton["tipo"] ?> btn-sm float-<?= $boton["posicion"] ?>"><i class="<?= $boton["icono"] ?>"></i> <?= $boton["txt"] ?></a>
              <?php }
                  endif;
                  $contador++;
                endforeach;
              endif; ?> -->
            </div>
            <!-- /.card-header -->
            <?php if ($existe == 1){
              foreach ($datosApertura as $fila)
              {
                if ($fila->idTurnoVigente != 0)
                {
                  $montoTurnoCorteCaja = $fila->montoTurnoCorteCaja;
                  $corteTurno = $fila->corteTurno;
                }
                else
                {
                  $montoTurnoCorteCaja = "";
                  $corteTurno = "";
                }
                ?>
                <div class="card-header">
                  <table class="table table-bordered corteAdminTabla">
                    <thead>
                      <tr>
                        <th colspan="4" style="text-align: center"><label class="badge badge-success" style="font-size: 15px; ">Apertura Vigente</label></th>
                      </tr>
                      <tr>
                        <th>Nombre: <?=$fila->nombreUsuario;?></th>
                        <th>Fecha Apertura: <?=$fila->fechaCorte;?></th>
                        <th>Hora Apertura: <?=$fila->horaCorte;?></th>
                      </tr>
                      <tr>
                        <th>Monto Apertura Turno: <?=$montoTurnoCorteCaja;?></th>
                        <th>Turno: <?=$corteTurno;?></th>
                        <th>Monto Apertura: <?= number_format($fila->montoApertura, 2,'.',',');?></th>
                      </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="4" style="text-align: center">
                            <?php
                            if ($fila->idTurnoVigenteExiste != 0)
                            {
                              ?>
                              <a href="<?=base_url()."RealizarCorte/".md5($fila->idCorteCaja)."/".md5($fila->idTurnoVigente)."/".md5($fila->idCaja)?>" style="margin-bottom: 10px;" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnCorte">Realizar Corte</a><br>
                              <a href="<?=base_url()."RealizarCierreTurno/".md5($fila->idCorteCaja)."/".md5($fila->idTurnoVigente)."/".md5($fila->idCaja)?>" class="btn btn-sm btn-danger" id="btnTurno">Cerrar Turno</a>
                              <?php
                            }
                            else
                            {
                              ?>
                              <a data-toggle='modal' data-target='viewModal' data-refresh='true' id='aperturaTurno' idCorte='<?= md5($fila->idCorteCaja);?>' class='btn btn-danger btn-sm  float-center m-t-n-xs'>Aperturar Turno</a>
                              <?php
                            }
                            ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                </div>
                <?php
              }
            }
          else
          { ?>
            <div class="card-header">
              <table class="table table-bordered corteAdminTabla">
                <?php
                if ($nCajasApertura > 0)
                {
                  ?>
                <thead>
                  <tr>
                    <th colspan="4" style="text-align: center"><label class="badge badge-success" style="font-size: 15px; ">Sin apertura de caja</label></th>
                  </tr>
                  <tr>
                    <th colspan="4" style="text-align: center">
                      <a data-toggle='modal' data-target='viewModal' data-refresh='true' id='aperturaCaja' class='btn btn-primary btn-sm  float-center m-t-n-xs'><i class=''></i> Realizar Apertura</a>
                    </th>
                  </tr>
                  </thead>
                  <?php
                  }
                  if($nCajasTurnos > 0)
                  {
                    ?>
                    <thead>
                      <tr>
                        <th colspan="4" style="text-align: center"><label class="badge badge-warning" style="font-size: 15px; ">Hay cajas disponibles para aperturar Turno</label></th>
                      </tr>
                      <tr>
                        <th colspan="4" style="text-align: center">
                          <a data-toggle='modal' data-target='viewModal' data-refresh='true' id='aperturaTurnoUsuario' class='btn btn-danger btn-sm  float-center m-t-n-xs'>Aperturar Turno</a>
                        </th>
                      </tr>
                      </thead>
                    <?php
                  }
                ?>
                </table>
            </div>
          <?php
         } ?>
            <!-- <div class="card-body">
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
            </div> -->
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
