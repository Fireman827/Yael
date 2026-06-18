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
	<form id="FrmPeriodosPlanilla" autocomplete="off">
        <div class="row">
			<div class="form-group col-lg-12" style="margin-bottom:-6px;">
				<div style="height:40px;" class="text-center alert alert-info"><h4 style="margin-top: -4px;">PERIODO A FINALIZAR</h4></div>
			</div>
		</div>
		<table class="table table-bordered table-striped" id="tableview">
            <thead>
                <tr>
                    <th class="col-lg-4">DESDE</th>
                    <th class="col-lg-4">HASTA</th>
                    <th class="col-lg-4">ESTADO</th>
                </tr>
            </thead>
			<tbody>
				<?php
					echo "<tr>";
                    echo "<td>".$datosPeriodosPlanilla->desdePeriodoPlanilla."<input type='hidden' name='desdePeriodoPlanilla' id='desdePeriodoPlanilla' value='".$datosPeriodosPlanilla->desdePeriodoPlanilla."'></td>";
                    echo "<td>".$datosPeriodosPlanilla->hastaPeriodoPlanilla."<input type='hidden' name='hastaPeriodoPlanilla' id='hastaPeriodoPlanilla' value='".$datosPeriodosPlanilla->hastaPeriodoPlanilla."'></td>";
                    echo "<td><h5 class='text-success'>VIGENTE</h5></td></tr>";
				?>
			</tbody>
		</table>
        <div class="row">
			<div class="form-group col-lg-12" style="margin-bottom:-6px;">
				<div style="height:40px;" class="text-center alert alert-info"><h4 style="margin-top: -4px;">PERIODO NUEVO</h4></div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="fechaInicioPagoPeriodoPlanilla">Fecha inicio de pago: <span class="text-danger">*</span></label>
					<input type="date" name="fechaInicioPagoPeriodoPlanilla" id="fechaInicioPagoPeriodoPlanilla" class="form-control"  value="<?=$datosPeriodosPlanilla->fechaInicioPagoPeriodoPlanilla; ?>" >
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="fechaFinPagoPeriodoPlanilla">Fecha final de pago: <span class="text-danger">*</span></label>
					<input type="date" name="fechaFinPagoPeriodoPlanilla" id="fechaFinPagoPeriodoPlanilla" class="form-control" value="<?=$datosPeriodosPlanilla->fechaFinPagoPeriodoPlanilla; ?>">
				</div>
			</div>
		</div>
        <div class="row" hidden>
            <div class="col">
                <input type="hidden" name="idPeriodoPlanilla" id="idPeriodoPlanilla" value="<?=$idPeriodoPlanilla; ?>">
            </div>
        </div>
	</form>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" id="btnGuardar">Guardar</button>
	<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?=$proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->