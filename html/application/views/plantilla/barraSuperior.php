<?php
$CI =& get_instance();
$CI->load->model('CoreModel');

$idUsuario = $CI->session->idUsuario ?? null;

$notificaciones = [];
$totalNotificaciones = 0;

if ($idUsuario) {
    $notificaciones = $CI->CoreModel->listarNotificacionesUsuario($idUsuario);
    $totalNotificaciones = count($notificaciones);
}
?>

<nav class="main-header navbar navbar-expand <?= GblTraerConfiguracion("colorPlantilla"); ?>">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">

        <!-- 🔔 NOTIFICACIONES -->
        <li class="nav-item dropdown">

            <a class="nav-link" data-toggle="dropdown" href="javascript:void(0);">
                <i class="far fa-bell"></i>

                <?php if ($totalNotificaciones > 0): ?>
                    <span id="campana-notificaciones"
                          class="badge badge-<?= GblTraerConfiguracion('colorComponentes'); ?> navbar-badge">
                        <?= $totalNotificaciones ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                <span class="dropdown-item dropdown-header">
                    <?= $totalNotificaciones ?> Notificación(es)
                </span>

                <div class="dropdown-divider"></div>

                <!-- CONTENEDOR CORRECTO -->
                <div id="dropdown-lista" style="max-height:400px; overflow-y:auto;">
                    <?php if (!empty($notificaciones)): ?>
                        <?php foreach ($notificaciones as $n): ?>
                            <div class="dropdown-item d-flex justify-content-between align-items-start"
                                 id="notif-<?= $n->idNotificacion ?>">

                                <div class="mr-2">
									<?php if (!empty($n->url)): ?>
										<a href="<?= base_url($n->url); ?>" class="text-dark">
										<strong><?= $n->titulo ?></strong>
										</a><br>
								<?php else: ?>
									<strong><?= $n->titulo ?></strong><br>
								<?php endif; ?>

								<small><?= $n->mensaje ?></small>
								</div>


                                <button class="btn btn-sm btn-outline-primary marcar-leida"
                                        data-id="<?= $n->idNotificacion ?>">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>

                            <div class="dropdown-divider"></div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <span class="dropdown-item text-center text-muted">
                            Sin notificaciones
                        </span>

                    <?php endif; ?>

                </div>

                <div class="dropdown-divider"></div>

                <div class="dropdown-item text-right">
                    <button id="btn-marcar-todas"
                            class="btn btn-sm btn-outline-primary">
                        Marcar todas como leídas
                    </button>
                </div>

                <div class="dropdown-divider"></div>

                <a href="<?= base_url('Notificaciones'); ?>"
                   class="dropdown-item dropdown-footer">
                    Ver todas
                </a>

            </div>
        </li>

        <!-- Usuario -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-user-cog"></i> <?= $usuario ?>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('UsuarioEditar/' . md5($_SESSION['idUsuario'])) ?>" class="dropdown-item">
                    <i class="fas fa-id-card mr-2"></i> Perfil
                </a>
                <a href="<?= base_url('CerrarSesion') ?>" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </li>

    </ul>
</nav>
