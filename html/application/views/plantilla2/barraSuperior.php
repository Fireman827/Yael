<?php
$CI =& get_instance();
$CI->load->model('CoreModel');

$idUsuario  = $CI->session->idUsuario ?? null;

$notificaciones = [];
$totalNotificaciones = 0;

if ($idUsuario) {
    $notificaciones = $CI->CoreModel->listarNotificacionesUsuario($idUsuario);
    $totalNotificaciones = count($notificaciones);
}
?>


<nav class="main-header navbar navbar-expand <?=GblTraerConfiguracion("colorPlantilla");?>">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
		</li>
	</ul>

	<ul class="navbar-nav ml-auto">

		<li class="nav-item dropdown">
			<a class="nav-link" data-toggle="dropdown" href="#">
				<i class="far fa-bell"></i>
				<?php if($totalNotificaciones > 0): ?>
					<span class="badge badge-<?=GblTraerConfiguracion('colorComponentes');?> navbar-badge">
						<?=$totalNotificaciones;?>
					</span>
				<?php endif; ?>
			</a>

			<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

				<?php if($totalNotificaciones > 0): ?>
					<span class="dropdown-item dropdown-header">
						<?=$totalNotificaciones;?> Notificación(es)
					</span>

					<div class="dropdown-divider"></div>

					<?php foreach($notificacionesBarra as $n): 
						$ui = NotificacionUI($n->tipo, $n->estado);
							?>
							<a href="<?=base_url($n->url ?? '#');?>"
							class="dropdown-item marcar-leida"
							data-id="<?=$n->idNotificacion;?>"
							style="opacity: <?=$ui['opacidad'];?>">

							<i class="<?=$ui['icono'];?> text-<?=$ui['color'];?> mr-2"></i>

							<strong><?=$n->titulo;?></strong><br>
							<small><?=$n->mensaje;?></small>
							</a>
						<div class="dropdown-divider"></div>
					<?php endforeach; ?>


					<a href="<?=base_url('Notificaciones');?>"
					   class="dropdown-item dropdown-footer">
						Ver todas
					</a>
				<?php else: ?>
					<span class="dropdown-item dropdown-header">
						Sin notificaciones
					</span>
				<?php endif; ?>

			</div>
		</li>

	</ul>
</nav>
		<!-- /.navbar -->
