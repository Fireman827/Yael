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
	<div id="zonaMesaBody">
		<form id="FrmMesas" autocomplete="off">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="sticky-table sticky-headers sticky-cell" style="height: 270px;">
						<table id="tablaMesas" class="table table-striped table-sm table-condensed">
							<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
								<tr class="sticky-row text-center">
									<th class="col-1">Accion</th>
									<th>Mesa</th>
									<th>Capacidad</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($mesas) {
									foreach ($mesas as $mesa) : ?>
										<tr id="tr<?= $mesa->idZonaMesa ?>">
											<td>
												<div class="icheck-<?= GblTraerConfiguracion('colorComponentes'); ?> ">
													<input type="checkbox" class="checkboxMesas" id="<?= $mesa->idZonaMesa ?>" name="<?= $mesa->idZonaMesa ?>">
													<label for="<?= $mesa->idZonaMesa ?>"></label>
												</div>
											</td>
											<td><?= $mesa->nombreZonaMesa ?></td>
											<td><label><?= $mesa->capacidadZonaMesa ?></label> personas</td>
										</tr>
									<?php endforeach;
								} else {	?>
									<tr>
										<td colspan="3">No hay mesas asignadas</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div id="zonaMesaAgregarBody" hidden>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="table-responsive pre-scrollable" style="height:270px;">
					<table class="table table-sm table-condensed " id="tablaAgregarZonaMesa">
						<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
							<tr class="text-center">
								<th colspan="3">Mesas por Zona
									<a id="agregarTablaZonaMesa" class="float-right btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> " role="button"><i class="fa fa-plus"></i></a>
								</th>
							</tr>
							<tr>
								<th class="col-4">Cantidad de Mesas</th>
								<th class="col-4">Capacidad de Mesas</th>
								<th class="col-2">Accion</th>
							</tr>
						</thead>
						<tbody id="tbodyAgregarZonaMesa">
							
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<form id="FrmMesasAgregar" autocomplete="off">
			<input type="hidden" name="valores" id="valores">
			<input type="hidden" name="idZona" id="idZona" value="<?= $idZona ?>">
		</form>
	</div>
	<div id="zonaMesaEditarBody" hidden>
		<form id="FrmMesasEditar" autocomplete="off">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="table-responsive sticky-table sticky-headers" style="height: 270px;">
						<table id="tablaEditarMesa" class="table table-sm table-hover">
							<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
								<tr class='text-center'>
									<th class="col-3" style='text-align: center;'>Mesa</th>
									<th class="col-5" style='text-align: center;'>Capacidad</th>
									<th class="col-4" style='text-align: center;'>Accion</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div id="zonaMesaTrasladarBody" hidden>
		<form id="FrmMesasTrasladar" autocomplete="off">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="zonaDestino">Zona de Destino:</label>
						<select class="form-control select2" name="zonaDestino" id="zonaDestino">
							<?php foreach ($zonas as $zona) : ?>
								<option value="<?= $zona->idZona ?>"><?= $zona->nombreZona . " ( Capacidad : " . $zona->capacidadZona . ")" ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
			<div class="table-responsive sticky-table sticky-headers" style="height: 270px;">
				<table id="tablaEditarMesa" class="table table-sm table-hover">
					<thead class="bg-<?= GblTraerConfiguracion('colorComponentes'); ?>">
						<tr class='text-center'>
							<th class="col-2" style='text-align: center;'>Mesa</th>
							<th class="col-4" style='text-align: center;'>Nuevo Nombre</th>
							<th class="col-3" style='text-align: center;'>Capacidad</th>
							<th class="col-3" style='text-align: center;'>Accion</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</form>
	</div>
</div>
<div class="modal-footer">
	<div id="zonaMesaFooter">
		<?php if (count($botones) > 0) :
			foreach ($botones as $boton) :
				if (GblPermisos($this, $boton["url"], $boton["controlador"])) : ?>
					<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?> <?= $boton["clases"] ?>" idZona="<?= $idZona ?>" id="<?= $boton['id'] ?>"><i class="<?= $boton["icono"] ?>"></i> <?= $boton["txt"]; ?></button>
		<?php endif;
			endforeach;
		endif; ?>
	</div>
	<div id="zonaMesaAgregarFooter" hidden>
		<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" formulario="FrmMesasAgregar" id="btnGuardarAgregar">Guardar</button>
		<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
	</div>
	<div id="zonaMesaEditarFooter" hidden>
		<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" formulario="FrmMesasEditar" id="btnGuardar">Editar</button>
		<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
	</div>
	<div id="zonaMesaEliminarFooter" hidden>
		<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" formulario="FrmMesasEditar" id="btnGuardar">Eliminar</button>
		<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
	</div>
	<div id="zonaMesaTrasladarFooter" hidden>
		<button type="button" class="btn btn-sm btn-<?= GblTraerConfiguracion('colorComponentes'); ?>" formulario="FrmMesasTrasladar" id="btnGuardar">Trasladar</button>
		<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
	</div>

	<input type="hidden" name="idZona" id="idZona" value="<?= $idZona ?>">
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if (isset($proceso)) { ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
<!-- /.modal-content -->