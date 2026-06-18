<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php if($verDashboardCompleto): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php endif; ?>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Panel de Administración</h1>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->
<section class="content">
	<div class="container-fluid">
		<!-- Small boxes (Stat box) -->
		<div class="row">
			<a href="<?=base_url("/Productos") ?>">
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-info">
						<div class="inner">
							<h3> Productos</h3>

							<p>Admin</p>
						</div>
						<div class="icon">
							<i class="fa fa-shopping-cart"></i>
						</div>
						<a href="<?=base_url("/Productos") ?>" class="small-box-footer">VER <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
			</a>
			<!-- ./col -->
			<a href="<?=base_url("/Insumos") ?>">
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-success">
						<div class="inner">
							<h3> Insumos<sup style="font-size: 20px"></sup></h3>
							<p>Admin</p>
						</div>
						<div class="icon">
	 						<i class="fa fa-drumstick-bite"></i>
						</div>
						<a href="<?=base_url("/Insumos") ?>" class="small-box-footer">VER <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
			</a>
			<!-- ./col -->
			<a href="<?=base_url("/MovimientosInventario") ?>">
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-warning">
						<div class="inner">
							<h3>Inventario</h3>

							<p>Admin</p>
						</div>
						<div class="icon">
							<i class="fa fa-user-clock"></i>
						</div>
						<a href="<?=base_url("/MovimientosInventario") ?>" class="small-box-footer">Ver <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
			</a>

			<!-- ./col -->
			<a href="<?=base_url("/Touch") ?>">
				<div class="col-lg-3 col-6">
					<!-- small box -->
					<div class="small-box bg-danger">
						<div class="inner">
							<h3>Touch</h3>

							<p>POS</p>
						</div>
						<div class="icon">
							<i class="fa fa-desktop"></i>
						</div>
						<a href="<?=base_url("/Touch") ?>" class="small-box-footer">Ingresar <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
			</a>

			<!-- ./col -->
		</div>
		<!-- /.row -->

		<?php if($verDashboardCompleto): ?>
		<!-- Resumen del día -->
		<div class="row mt-2">
			<div class="col-md-4">
				<div class="card card-outline card-primary mb-2">
					<div class="card-header py-1"><h6 class="card-title mb-0"><i class="fa fa-map-marker-alt"></i> Restaurante</h6></div>
					<div class="card-body p-0">
						<div id="mapaRestaurante" style="height:200px;border-radius:0 0 .25rem .25rem;"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card card-outline card-success mb-2">
					<div class="card-header py-1"><h6 class="card-title mb-0"><i class="fa fa-chart-pie"></i> Ventas vs Gastos (Hoy)</h6></div>
					<?php $totalDesgloseGastos = $resumenDashboard['compras'] + $resumenDashboard['gastosFijos'] + $resumenDashboard['planilla']; ?>
					<div class="card-body text-center p-2">
						<canvas id="chartVentasGastos" style="max-height:140px;<?=$totalDesgloseGastos==0?'display:none;':''?>"></canvas>
						<?php if($totalDesgloseGastos==0): ?>
							<p class="text-muted mb-0 d-flex align-items-center justify-content-center" style="height:140px;">Sin gastos registrados hoy.</p>
						<?php endif; ?>
						<div class="row mt-1 text-center" style="font-size:11px;">
							<div class="col-4 d-flex align-items-center justify-content-center"><span style="display:inline-block;width:10px;height:10px;background:#dc3545;border-radius:2px;margin-right:4px;"></span>Compras</div>
							<div class="col-4 d-flex align-items-center justify-content-center"><span style="display:inline-block;width:10px;height:10px;background:#fd7e14;border-radius:2px;margin-right:4px;"></span>Gastos Fijos</div>
							<div class="col-4 d-flex align-items-center justify-content-center"><span style="display:inline-block;width:10px;height:10px;background:#6f42c1;border-radius:2px;margin-right:4px;"></span>Planilla</div>
						</div>
						<div class="row mt-2 small">
							<div class="col-4"><strong class="text-success">$<?=number_format($resumenDashboard['ventas'],2)?></strong><br>Ventas</div>
							<div class="col-4"><strong class="text-danger">$<?=number_format($resumenDashboard['gastosTotal'],2)?></strong><br>Gastos</div>
							<div class="col-4"><strong class="<?=$resumenDashboard['utilidad']>=0?'text-primary':'text-danger'?>">$<?=number_format($resumenDashboard['utilidad'],2)?></strong><br>Utilidad</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card card-outline card-warning mb-2">
					<div class="card-header py-1"><h6 class="card-title mb-0"><i class="fa fa-trophy"></i> Producto más vendido (Hoy)</h6></div>
					<div class="card-body text-center p-2">
						<?php if(!empty($resumenDashboard['topProducto'])): ?>
							<?php if(!empty($resumenDashboard['topProducto']['imagen'])): ?>
								<img src="<?=base_url($resumenDashboard['topProducto']['imagen'])?>" style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
							<?php endif; ?>
							<h5 class="mt-2 mb-1"><?=htmlspecialchars($resumenDashboard['topProducto']['nombre'])?></h5>
							<span class="badge badge-warning"><?=$resumenDashboard['topProducto']['cantidad']?> vendidos</span>
						<?php else: ?>
							<p class="text-muted mb-0">Aún no hay ventas registradas hoy.</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<!-- Mapa de pedidos a domicilio del día -->
		<div class="row">
			<div class="col-12">
				<div class="card card-outline card-info mb-2">
					<div class="card-header py-1"><h6 class="card-title mb-0"><i class="fa fa-truck"></i> Pedidos a domicilio - Hoy</h6></div>
					<div class="card-body p-0">
						<div id="mapaDeliveryHoy" style="height:320px;border-radius:0 0 .25rem .25rem;"></div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<!-- /.row (main row) -->
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>

<?php if($verDashboardCompleto): ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
	var restLat = <?=json_encode((float)$restauranteLat)?>;
	var restLng = <?=json_encode((float)$restauranteLng)?>;
	var pedidosDelivery = <?=json_encode($pedidosDeliveryHoy)?>;
	var resumenDashboard = <?=json_encode($resumenDashboard)?>;

	var mapaRestaurante = L.map('mapaRestaurante').setView([restLat, restLng], 16);
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap' }).addTo(mapaRestaurante);
	L.marker([restLat, restLng]).addTo(mapaRestaurante).bindPopup('<strong>Restaurante</strong>');

	var mapaDeliveryHoy = L.map('mapaDeliveryHoy').setView([restLat, restLng], 12);
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap' }).addTo(mapaDeliveryHoy);
	L.marker([restLat, restLng]).addTo(mapaDeliveryHoy).bindPopup('<strong>Restaurante</strong>');

	var coloresEstadoOnline = { Recibido:'#dc3545', EnPreparacion:'#fd7e14', Listo:'#0d6efd', EnCamino:'#6f42c1', Entregado:'#198754' };
	var iconConductor = L.divIcon({ html:'<i class="fa fa-motorcycle" style="color:#198754;font-size:20px;"></i>', className:'', iconSize:[20,20] });

	pedidosDelivery.forEach(function(p){
		var color = coloresEstadoOnline[p.estado] || '#6c757d';
		var popupBase = '<strong>'+p.codigo+'</strong><br>'+(p.cliente||'')+'<br>'+(p.direccion||'')+'<br>Estado: '+p.estado+'<br>Total: $'+Number(p.total).toFixed(2);
		var marker = L.circleMarker([p.lat, p.lng], { radius:9, color:color, fillColor:color, fillOpacity:0.85, weight:2 })
			.addTo(mapaDeliveryHoy)
			.bindPopup(popupBase + '<br><em>Consultando conductor...</em>');

		marker.on('click', function(){
			$.post(url + '/Inicio/estadoConductor', { codigo: p.codigo }, function(r){
				var info = popupBase;
				if(r.codigo === 200 && r.data){
					if(r.data.estado) info += '<br>Orion: '+r.data.estado;
					if(r.data.conductorNombre) info += '<br>Conductor: '+r.data.conductorNombre;
					if(r.data.conductorTelefono) info += '<br>Tel: '+r.data.conductorTelefono;
					if(r.data.conductorLat && r.data.conductorLng){
						L.marker([r.data.conductorLat, r.data.conductorLng], { icon: iconConductor })
							.addTo(mapaDeliveryHoy)
							.bindPopup('Conductor: '+(r.data.conductorNombre||''));
					}
					if(!r.data.estado && !r.data.conductorNombre) info += '<br><em>Sin conductor asignado aún.</em>';
				} else {
					info += '<br><em>'+(r.mensaje||'Sin información de Orion.')+'</em>';
				}
				marker.setPopupContent(info);
			}, 'json');
		});
	});

	var totalDesgloseGastos = resumenDashboard.compras + resumenDashboard.gastosFijos + resumenDashboard.planilla;
	if(totalDesgloseGastos > 0){
		var ctxVentasGastos = document.getElementById('chartVentasGastos').getContext('2d');
		new Chart(ctxVentasGastos, {
			type: 'doughnut',
			data: {
				labels: ['Compras','Gastos Fijos','Planilla'],
				datasets: [{
					data: [resumenDashboard.compras, resumenDashboard.gastosFijos, resumenDashboard.planilla],
					backgroundColor: ['#dc3545','#fd7e14','#6f42c1']
				}]
			},
			options: {
				plugins: { legend: { display: false } }
			}
		});
	}

	setTimeout(function(){
		mapaRestaurante.invalidateSize();
		mapaDeliveryHoy.invalidateSize();
	}, 200);
});
</script>
<?php endif; ?>
