<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?= $icono ?>"></i> <?= $titulo ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group">
        <?php if($existe == 1){?>
				  <label for="nombreZona">Cliente: <?php echo $cliente->nombreCliente; ?></label>
        <?php } else {?>
          <label for="nombreZona">Cliente: <?php echo $cliente; ?></label>
        <?php } ?>
        <label for=""></label>
			</div>
		</div>
    <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="codigoMembrecia">Palca: <?php echo $datos->placaParqueo; ?></label>
      </div>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="codigoMembrecia">Hora Entrada: <?php echo $datos->horaEntradaParqueo; ?></label>
      </div>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
      <div class="form-group">
        <label for="codigoMembrecia">Hora Salida: <?php echo date("H:i:s"); ?></label>
      </div>
    </div>


    <?php
      $d1 = $datos->fechaEntradaParqueo."T".$datos->horaEntradaParqueo;
      $d2 = date("Y-m-d")."T".date("H:i:s");
      $date1 = new DateTime($d1);
      $date2 = new DateTime($d2);

      $diff = $date2->diff($date1);

      $hours = $diff->h;
      $minutos = $diff->i;
      $hours = $hours + ($diff->days*24);
      $tarifa = $tarifa->valorConfiguracion;

      $total_cobro = ceil($hours.".".$minutos);

     ?>
     <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
       <div class="form-group">
         <label for="codigoMembrecia">Tiempo transcurrido: <?php echo $hours." Horas y ".$minutos." minutos"; ?></label>
       </div>
     </div>
     <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
       <div class="form-group">
         <label for="totalParqueo">Total a cobrar ($)</label>
         <input type="text" class="form-control" id="totalParqueo" name="totalParqueo" value="<?php echo $total_cobro; ?>">
       </div>
     </div>
	</div>
</div>
<div class="modal-footer">
  <input type="hidden" name="proceso" id="proceso" value="Editar">
  <input type="hidden" name="idParqueo" id="idParqueo" value="<?= md5($datos->idParqueo);?>">
  <input type="hidden" name="fechaSalidaParqueo" id="fechaSalidaParqueo" value="<?= date("Y-m-d");?>">
  <input type="hidden" name="horaSalidaParqueo" id="horaSalidaParqueo" value="<?= date("H:i:s");?>">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnCobrar">Cobrar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->
