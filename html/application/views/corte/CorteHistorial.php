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
					<div class="card card-<?=GblTraerConfiguracion('colorComponentes');?>">
						<div class="card-header">
							<h3 class="card-title"><?=$titulo?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<form id="FrmCliente" autocomplete="off">
							<div class="card-body">
								<div class="row">
									<table id="tablaTurno" class="table table-hover table-sm dataTable dtr-inline" role="grid" aria-describedby="tablaTurno_info">
                    <thead>
                      <tr>
                        <th class="col-lg-1">Fecha</th>
                        <th class="col-lg-1">N° Turno</th>
                        <th class="col-lg-3">Usuario</th>
                        <th class="col-lg-2">Monto Turno</th>
                        <th class="col-lg-2">Monto Final</th>
                        <th class="col-lg-1">Hora</th>
                        <th class="col-lg-1">Estado</th>
                        <th class="col-lg-1">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        if($turnos)
                        {
                          $lista = "";
                          foreach ($turnos as $row)
                          {
                            $corteTurno = $row->corteTurno;
                            $montoTurnoCorteCaja = $row->montoTurnoCorteCaja;
                            $montoTurnoCierreCorteCaja = $row->montoTurnoCierreCorteCaja;
                            $nombreUsuario = $row->nombreUsuario;
                            $fechaCorteTurno = substr($row->fechaCorteTurno, 0, -9);
                            $horaCorteTurno = $row->horaCorteTurno;
                            $estadoCorteTurno = $row->estadoCorteTurno;
                            $idTurno = $row->idTurno;
                            $idCorte= $row->idCorte;
                            $menuOpciones = "
                            <div class='input-group-prepend'>
                            <button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
                            <div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";

                            $funcion ="CorteHistorial";
                            $menuOpciones .= "<a class='dropdown-item' href='". base_url().$funcion."/".md5($idTurno)."/".md5($idCorte)."'><i class='fas fa-eye' ></i> Ver detalles</a>";


                            $menuOpciones .= "
                            </div>
                            </div>";
                            $lista .= "<tr>";
                            $lista .= "<td>".$fechaCorteTurno."</td>";
                            $lista .= "<td>".$corteTurno."</td>";
                            $lista .= "<td>".$nombreUsuario."</td>";
                            $lista .= "<td>".$montoTurnoCorteCaja."</td>";
                            $lista .= "<td>".$montoTurnoCierreCorteCaja."</td>";
                            $lista .= "<td>".$horaCorteTurno."</td>";
                            $lista .= "<td>".$estadoCorteTurno."</td>";
                            $lista .= "<td>".$menuOpciones."</td>";
                            $lista .= "</tr>";
                          }
                          echo $lista;
                        }
                      ?>
                    </tbody>
                  </table>
								</div>

							</div>
							<!-- /.card-body -->

							<div class="card-footer">
								<input type="hidden" name="avanzado" id="avanzado" value="false">
								<input type="hidden" name="proceso" id="proceso" value="Ver">
								<!-- <button type="submit" class="btn btn-<?=GblTraerConfiguracion('colorComponentes');?> float-right"><i class="fa fa-save"></i> Guardar</button> -->
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
