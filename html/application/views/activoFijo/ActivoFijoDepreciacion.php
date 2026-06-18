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
					<!-- <h4><i class="<?= $icono ?>"></i> <?= $titulo ?></h4> -->
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url(); ?>">Inicio</a></li>
						<li class="breadcrumb-item"><a class="font-weight-bold text-<?= GblTraerConfiguracion('colorComponentes') ?>" href="<?= base_url() . $controlador; ?>"><?= ucfirst($controlador); ?></a></li>
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
					<div class="card card-<?= GblTraerConfiguracion('colorComponentes'); ?>">
						<div class="card-header">
							<h3 class="card-title"><?= $titulo ?></h3>
						</div>
						<!-- /.card-header -->
						<!-- form start -->
						<?php
                $vidaActivoFijo = round($datosActivoFijo->vidaActivoFijo, 2);
                $precioActivoFijo = $datosActivoFijo->precioActivoFijo;
                $depreciacionActivoFijo = $datosActivoFijo->depreciacionActivoFijo;
                $cargoDepreciacion = $precioActivoFijo/$vidaActivoFijo;

            ?>
            <h3 style="text-align: center;">Metodo Lineal</h3>
            <table class="table table-dark">
              <thead>
                <tr>
                  <th class="col-md-3">AÑO</th>
                  <th class="col-md-3">DEPRECIACION ANUAL</th>
                  <th class="col-md-3">DEPRECIACION ACUMULADA</th>
                  <th class="col-md-3">VALOR EN LIBROS</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $acumulado = $cargoDepreciacion;
                $valorLibro = $precioActivoFijo;
                for ($i=0; $i < $vidaActivoFijo+1 ; $i++)
                {
                  if($i == 0)
                  {
                    echo "<tr>";
                    echo "  <td>".$i."</td>";
                    echo "  <td></td>";
                    echo "  <td></td>";
                    echo "  <td>".number_format($valorLibro, 2,'.', ',')."</td>";
                    echo "</tr>";
                  }
                  else
                  {
                    echo "<tr>";
                    echo "  <td>".$i."</td>";
                    echo "  <td>".number_format($cargoDepreciacion, 2, ".", ",")."</td>";
                    echo "  <td>".number_format($acumulado, 2, ".", ",")."</td>";
                    echo "  <td>".number_format($valorLibro, 2,'.', ',')."</td>";
                    echo "</tr>";
                    $acumulado += $cargoDepreciacion;
                  }
                  $valorLibro -= $cargoDepreciacion;
                }
                ?>
              </tbody>
            </table>
            <div class="card-footer">
              <input type="hidden" name="idActivoFijo" id="idActivoFijo" value="<?=$idActivoFijo?>">
              <a href="<?=base_url()."ActivoFijoImprimir/".$idActivoFijo?>" class="btn btn-<?= GblTraerConfiguracion('colorComponentes'); ?> float-right" target="_blank">Imprimir</a>
            </div>
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
