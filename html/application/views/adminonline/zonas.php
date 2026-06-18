<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1><i class="fas fa-map-marked-alt text-danger"></i> Zonas de Delivery</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=base_url('Inicio')?>">Inicio</a></li>
            <li class="breadcrumb-item active">Zonas de Delivery</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">

      <?php if(empty($googleMapsApiKey)): ?>
      <div class="alert alert-info">
        <i class="fas fa-map"></i>
        Mostrando el mapa con <b>OpenStreetMap (Leaflet)</b> — gratis, sin API key.
        Cuando configures tu <b>API Key de Google Maps</b> en
        <a href="<?=base_url('AdminOnline/configuracion')?>">Configuración</a>, este mapa cambiará
        automáticamente a Google Maps.
      </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-4">
          <div class="card card-outline card-danger">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-list"></i> Zonas guardadas</h3></div>
            <div class="card-body p-0">
              <ul class="list-group list-group-flush" id="listaZonas"></ul>
            </div>
          </div>
          <div class="card card-outline card-danger" id="cardEditor" style="display:none">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-draw-polygon"></i> Editar zona</h3></div>
            <div class="card-body">
              <input type="hidden" id="zonaId" value="0">
              <div class="form-group">
                <label class="small text-muted">NOMBRE DE LA ZONA</label>
                <input type="text" id="zonaNombre" class="form-control form-control-sm" placeholder="Ej: San Salvador Centro">
              </div>
              <div class="form-group">
                <label class="small text-muted">COLOR</label>
                <input type="color" id="zonaColor" class="form-control form-control-sm" value="#C0392B" style="height:38px">
              </div>
              <button class="btn btn-danger btn-sm" onclick="guardarZona()"><i class="fas fa-save"></i> Guardar zona</button>
              <button class="btn btn-secondary btn-sm" onclick="cancelarEdicion()">Cancelar</button>
            </div>
          </div>
          <div class="card card-outline card-secondary">
            <div class="card-body">
              <small class="text-muted">
                Usa la herramienta de polígono <i class="fas fa-draw-polygon"></i> en el mapa para dibujar una nueva zona de cobertura
                haciendo clic punto por punto (como la herramienta de lazo/pluma de Illustrator). Haz doble clic o clic en el
                primer punto para cerrar el área. Al terminar, ponle un nombre y guarda.
                Haz clic sobre una zona ya guardada para editar sus vértices arrastrándolos.
              </small>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card card-outline card-danger">
            <div class="card-body p-0">
              <div id="mapaZonas" style="width:100%;height:600px"></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>
<input type="hidden" id="csrf_token_id" value="<?=$this->security->get_csrf_hash()?>">

<?php if(!empty($googleMapsApiKey)): ?>
<script>
var url = window.location.origin + '/';
var mapa, drawingManager;
var zonas = [];        // {id,nombre,color,poligono(GMaps),overlay}
var poligonoActual = null; // overlay recién dibujado, sin guardar

function initMapaZonas(){
  mapa = new google.maps.Map(document.getElementById('mapaZonas'), {
    center: {lat: 13.6929, lng: -89.2182}, // San Salvador
    zoom: 12
  });

  drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: null,
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: ['polygon']
    },
    polygonOptions: {
      editable: true,
      draggable: true
    }
  });
  drawingManager.setMap(mapa);

  google.maps.event.addListener(drawingManager, 'polygoncomplete', function(poly){
    if(poligonoActual) poligonoActual.setMap(null);
    poligonoActual = poly;
    drawingManager.setDrawingMode(null);
    abrirEditor(0, '', '#C0392B', poly);
  });

  cargarZonas();
}

function pathToPuntos(path){
  var puntos=[];
  path.forEach(function(latlng){ puntos.push({lat:latlng.lat(), lng:latlng.lng()}); });
  return puntos;
}

function pintarZona(z){
  var path = z.poligonoZonaDelivery.map(function(p){ return {lat: parseFloat(p.lat), lng: parseFloat(p.lng)}; });
  var overlay = new google.maps.Polygon({
    paths: path,
    strokeColor: z.colorZonaDelivery,
    strokeOpacity: 0.9,
    strokeWeight: 2,
    fillColor: z.colorZonaDelivery,
    fillOpacity: 0.15,
    editable: false,
    draggable: false
  });
  overlay.setMap(mapa);
  overlay.idZonaDelivery = z.idZonaDelivery;
  overlay.addListener('click', function(){
    drawingManager.setDrawingMode(null);
    if(poligonoActual && poligonoActual!==overlay) poligonoActual.setMap(null);
    overlay.setEditable(true);
    overlay.setDraggable(true);
    poligonoActual = overlay;
    abrirEditor(z.idZonaDelivery, z.nombreZonaDelivery, z.colorZonaDelivery, overlay);
  });
  return overlay;
}

function cargarZonas(){
  $.get(url+'AdminOnline/listarZonas', function(r){
    if(r.codigo!==200) return;
    // limpiar overlays previos
    zonas.forEach(function(z){ if(z.overlay) z.overlay.setMap(null); });
    zonas=[];
    var $lista=$('#listaZonas').empty();
    if(!r.zonas.length){
      $lista.append('<li class="list-group-item text-muted small">No hay zonas registradas todavía.</li>');
    }
    r.zonas.forEach(function(z){
      var overlay = pintarZona(z);
      zonas.push({id:z.idZonaDelivery, nombre:z.nombreZonaDelivery, color:z.colorZonaDelivery, overlay:overlay});
      $lista.append(
        '<li class="list-group-item d-flex justify-content-between align-items-center">'+
          '<span><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:'+z.colorZonaDelivery+';margin-right:6px"></span>'+
          $('<div>').text(z.nombreZonaDelivery).html()+
          '</span>'+
          '<button class="btn btn-outline-danger btn-xs" onclick="eliminarZona('+z.idZonaDelivery+')"><i class="fas fa-trash"></i></button>'+
        '</li>'
      );
    });
  }, 'json');
}

function abrirEditor(id, nombre, color, overlay){
  $('#zonaId').val(id);
  $('#zonaNombre').val(nombre);
  $('#zonaColor').val(color);
  $('#cardEditor').show();
  poligonoActual = overlay;
}

function cancelarEdicion(){
  if(poligonoActual){
    var id = $('#zonaId').val();
    if(id == '0'){ poligonoActual.setMap(null); }
    else { poligonoActual.setEditable(false); poligonoActual.setDraggable(false); }
    poligonoActual = null;
  }
  $('#cardEditor').hide();
}

function guardarZona(){
  if(!poligonoActual){ alert('Dibuja un polígono primero.'); return; }
  var nombre = $('#zonaNombre').val().trim();
  if(!nombre){ alert('Ingresa un nombre para la zona.'); return; }
  var puntos = pathToPuntos(poligonoActual.getPath());
  if(puntos.length<3){ alert('El polígono debe tener al menos 3 puntos.'); return; }

  $.post(url+'AdminOnline/guardarZona', {
    csrf_token_id: $('#csrf_token_id').val(),
    idZonaDelivery: $('#zonaId').val(),
    nombre: nombre,
    color: $('#zonaColor').val(),
    poligono: JSON.stringify(puntos)
  }, function(r){
    if(r.codigo===200){
      toastr ? toastr.success(r.mensaje) : alert(r.mensaje);
      if(poligonoActual) poligonoActual.setMap(null);
      poligonoActual = null;
      $('#cardEditor').hide();
      cargarZonas();
    } else {
      alert('Error: '+r.mensaje);
    }
  }, 'json');
}

function eliminarZona(id){
  if(!confirm('¿Eliminar esta zona de delivery?')) return;
  $.post(url+'AdminOnline/eliminarZona', {csrf_token_id: $('#csrf_token_id').val(), idZonaDelivery: id}, function(r){
    if(r.codigo===200){
      toastr ? toastr.success(r.mensaje) : alert(r.mensaje);
      cargarZonas();
    } else {
      alert('Error: '+r.mensaje);
    }
  }, 'json');
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=urlencode($googleMapsApiKey)?>&libraries=drawing,places&callback=initMapaZonas" async defer></script>

<?php else: ?>

<!-- ============================================================
     Editor de zonas con Leaflet + OpenStreetMap (sin API key)
     Usa los mismos endpoints (listarZonas/guardarZona/eliminarZona)
     y el mismo formato de polígono {lat,lng} que la versión Google.
     ============================================================ -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
var url = window.location.origin + '/';
var mapa, drawnItems;
var zonas = [];            // {id,nombre,color,layer}
var poligonoActual = null; // capa recién dibujada o en edición, sin guardar

function initMapaZonasLeaflet(){
  mapa = L.map('mapaZonas').setView([13.6929, -89.2182], 12); // San Salvador

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(mapa);

  drawnItems = new L.FeatureGroup();
  mapa.addLayer(drawnItems);

  var drawControl = new L.Control.Draw({
    position: 'topright',
    draw: {
      polygon: {
        allowIntersection: false,
        showArea: true,
        shapeOptions: { color: '#C0392B' }
      },
      polyline: false,
      rectangle: false,
      circle: false,
      circlemarker: false,
      marker: false
    },
    edit: {
      featureGroup: drawnItems,
      remove: false
    }
  });
  mapa.addControl(drawControl);

  // Nuevo polígono dibujado punto por punto
  mapa.on(L.Draw.Event.CREATED, function(e){
    if(poligonoActual){
      if(poligonoActual.editing) poligonoActual.editing.disable();
      drawnItems.removeLayer(poligonoActual);
    }
    var layer = e.layer;
    drawnItems.addLayer(layer);
    activarEdicionZona(0, '', '#C0392B', layer);
  });

  cargarZonas();
}

function pathToPuntos(layer){
  var latlngs = layer.getLatLngs()[0];
  return latlngs.map(function(ll){ return {lat: ll.lat, lng: ll.lng}; });
}

function pintarZona(z){
  var path = z.poligonoZonaDelivery.map(function(p){ return [parseFloat(p.lat), parseFloat(p.lng)]; });
  var layer = L.polygon(path, {
    color: z.colorZonaDelivery,
    weight: 2,
    fillColor: z.colorZonaDelivery,
    fillOpacity: 0.15
  });
  drawnItems.addLayer(layer);
  layer.idZonaDelivery = z.idZonaDelivery;
  layer.on('click', function(){
    activarEdicionZona(z.idZonaDelivery, z.nombreZonaDelivery, z.colorZonaDelivery, layer);
  });
  return layer;
}

function activarEdicionZona(id, nombre, color, layer){
  if(poligonoActual && poligonoActual !== layer && poligonoActual.editing){
    poligonoActual.editing.disable();
  }
  if(layer.editing) layer.editing.enable();
  abrirEditor(id, nombre, color, layer);
}

function cargarZonas(){
  $.get(url+'AdminOnline/listarZonas', function(r){
    if(r.codigo!==200) return;
    // limpiar capas previas
    zonas.forEach(function(z){ if(z.layer) drawnItems.removeLayer(z.layer); });
    zonas=[];
    var $lista=$('#listaZonas').empty();
    if(!r.zonas.length){
      $lista.append('<li class="list-group-item text-muted small">No hay zonas registradas todavía.</li>');
    }
    r.zonas.forEach(function(z){
      var layer = pintarZona(z);
      zonas.push({id:z.idZonaDelivery, nombre:z.nombreZonaDelivery, color:z.colorZonaDelivery, layer:layer});
      $lista.append(
        '<li class="list-group-item d-flex justify-content-between align-items-center">'+
          '<span><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:'+z.colorZonaDelivery+';margin-right:6px"></span>'+
          $('<div>').text(z.nombreZonaDelivery).html()+
          '</span>'+
          '<button class="btn btn-outline-danger btn-xs" onclick="eliminarZona('+z.idZonaDelivery+')"><i class="fas fa-trash"></i></button>'+
        '</li>'
      );
    });
  }, 'json');
}

function abrirEditor(id, nombre, color, layer){
  $('#zonaId').val(id);
  $('#zonaNombre').val(nombre);
  $('#zonaColor').val(color);
  $('#cardEditor').show();
  poligonoActual = layer;
}

function cancelarEdicion(){
  if(poligonoActual){
    if(poligonoActual.editing) poligonoActual.editing.disable();
    var id = $('#zonaId').val();
    if(id == '0'){ drawnItems.removeLayer(poligonoActual); }
    poligonoActual = null;
  }
  $('#cardEditor').hide();
}

function guardarZona(){
  if(!poligonoActual){ alert('Dibuja un polígono primero.'); return; }
  var nombre = $('#zonaNombre').val().trim();
  if(!nombre){ alert('Ingresa un nombre para la zona.'); return; }
  var puntos = pathToPuntos(poligonoActual);
  if(puntos.length<3){ alert('El polígono debe tener al menos 3 puntos.'); return; }

  $.post(url+'AdminOnline/guardarZona', {
    csrf_token_id: $('#csrf_token_id').val(),
    idZonaDelivery: $('#zonaId').val(),
    nombre: nombre,
    color: $('#zonaColor').val(),
    poligono: JSON.stringify(puntos)
  }, function(r){
    if(r.codigo===200){
      toastr ? toastr.success(r.mensaje) : alert(r.mensaje);
      if(poligonoActual && poligonoActual.editing) poligonoActual.editing.disable();
      poligonoActual = null;
      $('#cardEditor').hide();
      cargarZonas();
    } else {
      alert('Error: '+r.mensaje);
    }
  }, 'json');
}

function eliminarZona(id){
  if(!confirm('¿Eliminar esta zona de delivery?')) return;
  $.post(url+'AdminOnline/eliminarZona', {csrf_token_id: $('#csrf_token_id').val(), idZonaDelivery: id}, function(r){
    if(r.codigo===200){
      toastr ? toastr.success(r.mensaje) : alert(r.mensaje);
      if(poligonoActual && poligonoActual.idZonaDelivery == id){
        if(poligonoActual.editing) poligonoActual.editing.disable();
        poligonoActual = null;
        $('#cardEditor').hide();
      }
      cargarZonas();
    } else {
      alert('Error: '+r.mensaje);
    }
  }, 'json');
}

document.addEventListener('DOMContentLoaded', initMapaZonasLeaflet);
</script>
<?php endif; ?>
