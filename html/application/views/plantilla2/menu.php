<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

	<!-- Main Sidebar Container -->
	<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
		<!-- Brand Logo -->
		<a href="" class="brand-link text-<?=GblTraerConfiguracion('colorComponentes');?>">
			<img src="<?=base_url();?>/vendors/core/img/dms11.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
			<span class="brand-text font-weight-bold">DigitalsPos</span>
		</a>

		<!-- Sidebar -->
		<div class="sidebar">
			<!-- Sidebar user panel (optional) -->
			<!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
				<div class="image">
					<img src="<?=base_url();?>/vendors/core/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
				</div>
				<div class="info">
					<a href="#" class="d-block">Alexander Pierce</a>
				</div>
			</div> -->

			<!-- Sidebar Menu -->
			<nav class="mt-2">
				<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
					<!-- Add icons to the links using the .nav-icon class
							 with font-awesome or any other icon font library -->
					 <li class="nav-item">
 						<a href="<?=base_url($iniciourl);?>" class="nav-link">
 							<i class="nav-icon fas fa-tachometer-alt"></i>
 							<p>
 								Inicio
 								<!-- <span class="right badge badge-danger">New</span> -->
 							</p>
 						</a>
 					</li>
					<?php if($menus !== false){ ?>

						<?php foreach ($menus as $menu): ?>
							<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="nav-icon <?=$menu->iconoMenu?>"></i>
									<p>
										<?=$menu->nombreMenu?>
										<i class="right fas fa-angle-left"></i>
									</p>
								</a>
								<ul class="nav nav-treeview">
									<?php if ($menu->modulos !== false): ?>
										<?php foreach ($menu->modulos as $modulo): ?>
											<li class="nav-item">
												<a href="<?=base_url($modulo->controladorModulo.'/'.$modulo->funcionModulo);?>" class="nav-link">
													<i class="far fa-circle nav-icon"></i>
													<p><?=$modulo->nombreModulo?></p>
												</a>
											</li>
										<?php endforeach; ?>
									<?php endif; ?>
	              </ul>
	          	</li>
						<?php endforeach; ?>
					<?php } ?>
					<li class="nav-item">
					 <a href="<?=base_url();?>/CerrarSesion" class="nav-link">
						 <i class="nav-icon fa fa-sign-out-alt"></i>
						 <p>
							 Cerrar Sesión
							 <!-- <span class="right badge badge-danger">New</span> -->
						 </p>
					 </a>
				 </li>
				</ul>
			</nav>
			<!-- /.sidebar-menu -->
		</div>
		<!-- /.sidebar -->
	</aside>
