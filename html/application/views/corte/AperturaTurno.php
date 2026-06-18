<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<span class="modal-title"> <i class="<?=$icono ?>"></i> <?=$titulo ?></span>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true"><i class="fa fa-times"></i></span>
	</button>
</div>
<div class="modal-body">
	<!-- <form id="FrmApertura" autocomplete="off"> -->
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreImpresora">Fecha: <span class="text-danger">*</span></label>
					<input type="text" name="fechaApertura" id="fechaApertura" class="form-control" placeholder="" value="<?= date('d-m-Y');?>" readonly>
				</div>
			</div>
		</div>
		<?php if ($admin == 1){ ?>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="Impresora">Usuario: <span class="text-danger">*</span></label>
						<select class="select2 form-control col-12" name="usuarioApertura" id="usuarioApertura">
							<option value="">Seleccione</option>
						<?php if($usuarios !== 0):?>
							<?php foreach($usuarios as $user):?>
								<option value="<?=$user->idUsuario?>"><?=$user->nombreUsuario?></option>
							<?php endforeach;?>
						<?php endif;?>
						</select>
					</div>
				</div>
			</div>
		<?php } else{ ?>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="Impresora">Usuario: <span class="text-danger">*</span></label>
						<select class="select2 form-control col-12" name="usuarioApertura" id="usuarioApertura" disabled="true">
							<option value="">Seleccione</option>
						<?php if($usuarios !== 0):?>
							<?php foreach($usuarios as $user):?>
								<option value="<?=$user->idUsuario?>" <?php if($usuarioid == $user->idUsuario){ echo "selected";} ?>><?=$user->nombreUsuario?></option>
							<?php endforeach;?>
						<?php endif;?>
						</select>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="nombreImpresora">Monto: <span class="text-danger">*</span></label>
					<input type="text" name="montoApertura" id="montoApertura" class="form-control decimal" placeholder="Monto de apertura">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="Impresora">Caja: <span class="text-danger">*</span></label>
					<select class="select2 form-control col-12" name="cajaApertura" id="cajaApertura">
					<?php if($caja){?>
						<?php foreach($caja as $cajas):?>
							<option value="<?=$cajas->idCaja?>"><?=$cajas->nombreCaja?></option>
						<?php endforeach;?>
					<?php }else {
						?>
						<option value="0">Sin cajas dispobles</option>
						<?php
					}?>
					</select>
				</div>
			</div>
		</div>
        <!-- <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idImpresora" id="idImpresora" value="<?=$idImpresora; ?>">
            </div>
        </div> -->
	<!-- </form> -->
</div>
<div class="modal-footer">
  <input type="hidden" id="idCorte" value="<?= $idCorte; ?>">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnAperturaTurno">Guardar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->
