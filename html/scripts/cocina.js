var url = window.location.origin;
var token = $("#csrf_token_id").val();
var cantidadCocina = 0;
$(document).ready(function () {
	FormatoDatos();
	$(document).on('click','.nav-link.pestana' , function (e) {
		e.preventDefault()
		$(this).tab('show')
    })
    $('a.nav-link.pestana').on('shown.bs.tab', function (e) {
        var nuevaTab = $(e.target) // newly activated tab
        var antiguaTab = $(e.relatedTarget) // previous active tab
        cambiarColorTabs(nuevaTab,antiguaTab);
    })

	/*/ Control de Tabs (Final) /*/
	
	$(".dinamicos").hide();
	
	setInterval("actualizarCocina();",1000);
});

/*****************************************************************************/
/*********************************ORDENES*************************************/
/*****************************************************************************/

$(document).on("click", ".pestana", function () {
	//actualizarCocina();
	var idImpresora = $(this).attr("impresora");
	// setInterval("ordenesCocina('"+idImpresora+"');",5000);
	// setInterval("actualizarTimer();",1000);
	ordenesCocina(idImpresora);
	setInterval("actualizarTimer();",1000);
	//$("#listaCuentas").empty();
	//$("#listaCuentasDetalle").empty();
});

$(document).on("click", ".pedido", function () {
	var idPedido = $(this).find(".idPedido").val();
	var idImpresora = $(this).find(".idPedido").attr("impresora");
	detalleOrden(idPedido,idImpresora);
	setInterval("actualizarTimerDetalle();",1000);

});

$(document).on("click","#listaCuentasDetalle tr.prim",function(){
	//$("#botoneraHome").show();
	$("#listaCuentasDetalle tr").removeClass("bg-success");
	$(this).removeClass("bg-success");
	if(!$(this).hasClass("hide-table-padding")){
		
		var actual = $(this).attr("href");
		var idPedido = $(this).attr("idPedido");
		
		$(".collapse").not(actual).hide(200);
		$(actual).toggle(200);
		//$(this).addClass("bg-success");
	}
});

$(document).on("click",".finalizarPlato",function(){
	var idPedidoDetalle = $(this).attr("idPedidoDetalle");
	var idPedido = $(this).attr("idPedido");
	var idImpresora = $(this).attr("idImpresora");
	var it = $(this).attr("it");
	

	$.ajax({
		type: "POST",
		url: url+"/FinalizarPlato",
		data: {
			idPedidoDetalle : idPedidoDetalle,
		},
		dataType: 'json',
		success: function (respuesta){
			Alerta(respuesta.codigo);
			if(respuesta.codigo == 200){
				$("#listaCuentasDetalle tr.idt"+it).remove();
				//detalleOrden(idPedido,idImpresora);
				var contador = 0;
				$("#listaCuentasDetalle tr.prim").each(function () { 
					contador ++;
				 });
				if(contador == 0){
					$(".pedidoDiv[regalia='"+idPedido+"']").remove();
						$("#listaCuentas").empty();
						$("#listaCuentasDetalle").empty();
				}
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
	
});

$(document).on("click",".finalizarPlatoGeneral",function(){

	$("#tablaDetalleCuenta tr.prim").each(function(){
		$(this).find("a.finalizarPlato").click();
	});

});

/*****************************************************************************/
/*********************************FUNCIONES***********************************/
/*****************************************************************************/

function cambiarColorTabs(nuevaTab,antiguaTab){
	(antiguaTab.hasClass("bg-default")) ? (nuevaTab.toggleClass('bg-default') 	,antiguaTab.toggleClass('bg-default') ) :
	(antiguaTab.hasClass("bg-success")) ? (nuevaTab.toggleClass('bg-success') 	,antiguaTab.toggleClass('bg-success') ) :
	(antiguaTab.hasClass("bg-primary")) ? (nuevaTab.toggleClass('bg-primary') 	,antiguaTab.toggleClass('bg-primary') ) :
	(antiguaTab.hasClass("bg-info")) 	? (nuevaTab.toggleClass('bg-info') 		,antiguaTab.toggleClass('bg-info') ) 	:
	(antiguaTab.hasClass("bg-warning")) ? (nuevaTab.toggleClass('bg-warning') 	,antiguaTab.toggleClass('bg-warning') ) :
	(antiguaTab.hasClass("bg-danger")) 	? (nuevaTab.toggleClass('bg-danger') 	,antiguaTab.toggleClass('bg-danger') ) 	: '';
}

function actualizarTimer(){
	$("#contenedorOrdenes .tab-pane .pedido").each(function(){
	  var horai = $(this).find(".timer").val();
	  var max1 = 5;
	  var max2 = 10;
	  var max3 = 15;
	  var max4 = 20;
	  //var horaii = horai.split(" ")[0];
	  //alert(horaii);
	  var deadline = new Date(horai).getTime();
	  var now = new Date().getTime();
	  var t = now - deadline;
	  var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
	  var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((t % (1000 * 60)) / 1000);
	  var dif = "";
  
	  if(hours	>	0)  { dif+=hours; }
	  if(minutes <	10) { minutes = "0"+minutes; }
	  if(dif	==	"") { dif+=minutes; }
	  else {dif+=":"+minutes; }
	  if(seconds<	10) { seconds = "0"+seconds; }
	  
	  dif+=":"+seconds;
	 
	  if(minutes >= max1 && hours <= 0)   {
		$(this).find(".timers").parents(".pedido").removeClass("bg-secondary bg-info bg-warning bg-danger").addClass("bg-info");
	  }
	  if(minutes >= max2 && hours <= 0)   {
		$(this).find(".timers").parents(".pedido").removeClass("bg-secondary bg-info bg-warning bg-danger").addClass("bg-success");
	  }
	  if(minutes >= max3 && hours <= 0)   {
		$(this).find(".timers").parents(".pedido").removeClass("bg-secondary bg-info bg-warning bg-danger").addClass("bg-warning");
	  }
	  if(minutes >= max4 || hours > 0)   {
		$(this).find(".timers").parents(".pedido").removeClass("bg-secondary bg-info bg-warning bg-danger").addClass("bg-danger");
	  }
	  $(this).find(".timers").html(""+dif);
	});
}
function actualizarTimerDetalle(){
	$("#listaCuentasDetalle tr.prim").each(function(){
	  var horai = $(this).find(".timer").val();

	  var deadline = new Date(horai).getTime();
	  var now = new Date().getTime();
	  var t = now - deadline;
	  var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
	  var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((t % (1000 * 60)) / 1000);
	  var dif = "";
  
	  if(hours	>	0)  { dif+=hours; }
	  if(minutes <	10) { minutes = "0"+minutes; }
	  if(dif	==	"") { dif+=minutes; }
	  else {dif+=":"+minutes; }
	  if(seconds<	10) { seconds = "0"+seconds; }
	  
	  dif+=":"+seconds;
	 
	  $(this).find(".timers").html(""+dif);
	});
}

function listarCuentasLocal(idPedido = 0,idZona = 0){
	var searching = "<tr><td colspan='3'><img style='width:30px;' src='"+url+"/vendors/core/img/loading.gif'> <span class='blink_me' style='font-size:20pt; color:#FFF;'>Cargando datos ...</span></td></tr>";
	$("#tablaUnirCuenta tbody").html(searching);
	//$("#espacioCuentas").show();
	$.ajax({
		type: "POST",
		url: url+"/ActualizarCuentasLocal",
		data : {
			idPedido : idPedido,
			idZona : idZona
		},
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$("#tablaUnirCuenta tbody").html(respuesta.datos);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}

function detalleOrden(idPedido,idImpresora){
	var datos = {
		idPedido : idPedido,
		idImpresora : idImpresora,
	}
	$.ajax({
		type: "POST",
		url: url+"/DetalleOrden",
		data: datos,
		dataType: 'json',
		success: function (respuesta){
			$("#tablaDetalleCuenta tbody").html(respuesta.tbody);
			$("#tablaDetalleCliente tbody").html(respuesta.tbodyPedido);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown){
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}

function ordenesCocina(idImpresora){
	$.ajax({
		type: "POST",
		url: url+"/OrdenesCocina",
		data : {
			idImpresora : idImpresora
		},
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$("#vista"+idImpresora).html(respuesta.div);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}

function actualizarCocina() {
	var idImpresora = $("#listaCocinas").find("li a.pestana.active").attr("impresora");
	var idPestana = $("#listaCocinas").find("li a.pestana.active").attr("id");
	$.ajax({
		type: "POST",
		url: url+"/VerificarCocina",
		data : {
			idImpresora : idImpresora
		},
		dataType: 'json',
		success: function (respuesta) {
			//console.log("actual:"+cantidadCocina+" | nueva:"+respuesta.cantidad);
			if (respuesta.cantidad != cantidadCocina) {
				cantidadCocina = respuesta.cantidad;
				$("#"+idPestana).click();
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
	
}

function reload() {
	location.href = url + "/Cocina";
}

/*****************************************************************************/
/*** PARCHE PEDIDOS ONLINE v2 — detecta [WEB en nombre del cliente **********/
/*****************************************************************************/
(function () {
    'use strict';

    var audioAlerta    = null;
    var alertaSonando  = false;
    var cantidadPrevia = 0;

    // CSS: borde rojo para tarjetas web, etiqueta WEB
    var estilos = document.createElement('style');
    estilos.textContent =
        '#badge-online-fhb{' +
            'position:fixed;top:12px;right:16px;z-index:9999;' +
            'background:#C0392B;color:#fff;border-radius:20px;' +
            'padding:5px 14px;font-size:13px;font-weight:700;' +
            'box-shadow:0 2px 12px rgba(192,57,43,.5);cursor:pointer;' +
            'animation:fhb-bounce .6s ease infinite alternate;' +
        '}' +
        '@keyframes fhb-bounce{from{transform:translateY(0)}to{transform:translateY(-4px)}}' +
        '.fhb-card-online{border:3px solid #C0392B !important;box-shadow:0 0 14px rgba(192,57,43,.4) !important;}' +
        '.fhb-web-tag{display:inline-block;background:#C0392B;color:#fff;font-size:10px;font-weight:700;border-radius:4px;padding:1px 6px;margin-left:4px;vertical-align:middle;}';
    document.head.appendChild(estilos);

    function initAudio() {
        try { audioAlerta = new Audio(url + '/vendors/core/audio/online-alert.mp3'); } catch(e){}
    }

    function mostrarBadge(cantidad) {
        var badge = document.getElementById('badge-online-fhb');
        if (!badge) {
            badge = document.createElement('span');
            badge.id = 'badge-online-fhb';
            badge.title = 'Click para recargar';
            badge.addEventListener('click', function(){ location.reload(); });
            document.body.appendChild(badge);
        }
        badge.textContent = '🌐 ' + cantidad + ' ONLINE';
    }

    function ocultarBadge() {
        var b = document.getElementById('badge-online-fhb');
        if (b) b.remove();
    }

    // Resaltar tarjetas que contengan [WEB en el texto
    function resaltarTarjetasWeb() {
        document.querySelectorAll('.pedido, .pedidoDiv').forEach(function(el) {
            if (el.textContent.indexOf('[WEB') !== -1 && !el.classList.contains('fhb-card-online')) {
                el.classList.add('fhb-card-online');
                if (!el.querySelector('.fhb-web-tag')) {
                    var tag = document.createElement('span');
                    tag.className = 'fhb-web-tag';
                    tag.textContent = 'WEB';
                    var titulo = el.querySelector('h4,h5,strong,.nombre') || el.firstElementChild;
                    if (titulo) titulo.appendChild(tag);
                }
            }
        });
    }

    function reproducirAlerta() {
        if (!audioAlerta || alertaSonando) return;
        alertaSonando = true;
        audioAlerta.currentTime = 0;
        audioAlerta.play().catch(function(){ alertaSonando = false; });
        audioAlerta.onended = function() {
            alertaSonando = false;
            if (cantidadPrevia > 0) setTimeout(function(){ if(cantidadPrevia>0) reproducirAlerta(); }, 8000);
        };
    }

    function verificarOnline() {
        if (typeof $ === 'undefined') return;
        $.ajax({
            type: 'POST',
            url: url + '/Online/VerificarOnlinePendientes',
            dataType: 'json',
            timeout: 5000,
            success: function(r) {
                if (r && r.codigo === 200) {
                    var cantidad = parseInt(r.cantidad) || 0;
                    if (cantidad > 0) {
                        mostrarBadge(cantidad);
                        resaltarTarjetasWeb();
                        if (cantidad > cantidadPrevia) reproducirAlerta();
                        cantidadPrevia = cantidad;
                    } else {
                        cantidadPrevia = 0;
                        ocultarBadge();
                    }
                }
            },
            error: function() {}
        });
        resaltarTarjetasWeb();
    }

    function init() {
        initAudio();
        verificarOnline();
        setInterval(verificarOnline, 5000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
/*** FIN DEL PARCHE **********************************************************/