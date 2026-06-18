<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Variables inyectadas por el controlador vía GblPlantilla
$notificaciones  = isset($notificaciones)  ? $notificaciones  : [];
$pagosCalendario = isset($pagosCalendario) ? $pagosCalendario : [];

$CI           =& get_instance();
$idSucursal   = $CI->session->idSucursal;
$idUsuario    = $CI->session->idUsuario;
$esAdmin      = $CI->session->adminUsuario    ? 1 : 0;
$esSuperAdmin = $CI->session->superAdminUsuario ? 1 : 0;
?>

<div class="content-wrapper">
<section class="content">
<div class="container-fluid">

<!-- =============================================
     🔔 NOTIFICACIONES
============================================== -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="far fa-bell"></i> Notificaciones
        </h3>
        <?php if (!empty($notificaciones)): ?>
            <button id="btn-marcar-todas" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-check-double"></i> Marcar todas como leídas
            </button>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <ul class="list-group list-group-flush" id="lista-notificaciones">

        <?php if (!empty($notificaciones)): ?>

            <?php foreach ($notificaciones as $n): ?>

                <?php
                    /* Solo mostrar pendientes o según rol:
                       - Admin/SuperAdmin: ve todas las de la sucursal (idUsuario = -1 o las propias)
                       - Usuario normal  : solo las dirigidas a él o globales (idUsuario = -1)
                    */
                    $esGlobal  = ($n->idUsuario == -1);
                    $esPropia  = ($n->idUsuario == $idUsuario);
                    $puedeVer  = ($esAdmin || $esSuperAdmin) ? true : ($esGlobal || $esPropia);
                    if (!$puedeVer) continue;

                    $claseFila = $n->estado === 'pendiente' ? 'font-weight-bold' : 'text-muted';

                    $mapaIconos = [
                        'STOCK_BAJO'       => 'fas fa-box',
                        'PAGO'             => 'fas fa-file-invoice-dollar',
                        'PAGO_VENCIDO'     => 'fas fa-exclamation-circle',
                        'PAGO_ADVERTENCIA' => 'fas fa-clock',
                    ];
                    $icono = isset($mapaIconos[$n->tipo]) ? $mapaIconos[$n->tipo] : 'fas fa-info-circle';

                    $mapaColores = [
                        'danger'  => 'text-danger',
                        'warning' => 'text-warning',
                    ];
                    $colorNivel = isset($mapaColores[$n->nivel]) ? $mapaColores[$n->nivel] : 'text-info';
                ?>

                <li class="list-group-item <?= $claseFila ?>"
                    id="notif-<?= $n->idNotificacion ?>">

                    <div class="d-flex justify-content-between align-items-start">

                        <!-- Ícono + Contenido -->
                        <div class="d-flex align-items-start">
                            <i class="<?= $icono ?> <?= $colorNivel ?> mr-2 mt-1" style="font-size:1.1rem;"></i>
                            <div>
                                <strong><?= htmlspecialchars($n->titulo) ?></strong><br>
                                <small><?= htmlspecialchars($n->mensaje) ?></small><br>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($n->fechaCreacion)) ?>
                                    <?php if ($esAdmin || $esSuperAdmin): ?>
                                        &mdash; Sucursal #<?= $n->idSucursal ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="d-flex align-items-center ml-2" style="gap:.4rem; white-space:nowrap;">

                            <?php if (!empty($n->url)): ?>
                                <a href="<?= base_url($n->url) ?>"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-arrow-right"></i> Ir
                                </a>
                            <?php endif; ?>

                            <?php if ($n->estado === 'pendiente'): ?>
                                <button class="btn btn-sm btn-outline-success marcar-leida"
                                        data-id="<?= $n->idNotificacion ?>"
                                        title="Marcar como leída">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php else: ?>
                                <span class="badge badge-secondary">Leída</span>
                            <?php endif; ?>

                        </div>
                    </div>
                </li>

            <?php endforeach; ?>

        <?php else: ?>
            <li class="list-group-item text-center text-muted" id="sin-notificaciones">
                <i class="far fa-bell-slash mr-1"></i> No hay notificaciones
            </li>
        <?php endif; ?>

        </ul>
    </div>
</div>

<!-- =============================================
     📅 CALENDARIO DE PAGOS
============================================== -->
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="far fa-calendar-alt"></i> Calendario de Pagos
        </h3>
    </div>
    <div class="card-body">
        <div id="calendarioPagos"></div>
    </div>
</div>

</div>
</section>
</div>

<!-- Pasar datos del calendario a JS -->
<script>
    var pagosCalendario = <?php echo json_encode(isset($pagosCalendario) ? $pagosCalendario : array()); ?>;
</script>
