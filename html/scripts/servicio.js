var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Servicios';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ServicioMostrar',token);
	var formulario = "FrmServicio";
	var reglas = {
			tiempoServicio:{
				required : true,
			},
			categoriaServicio:{
				required : true,
			}
		};
	validarDatos(formulario,reglas);
});

$(document).on("click",".ServicioCambiarEstado", function(event){
	event.preventDefault()
	var idServicio = $(this).attr("idServicio");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idServicio=" + idServicio+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea "+ accion+" este registro?!",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, '+accion,
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/ServicioCambiarEstado",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						//setTimeout("reload();", 1500);
						tablaAdmin.ajax.reload(null,false);
					}
				},
				error: function(XMLHttpRequest){
					AlertaPersonalizada("error",XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".ServicioEliminar", function(event){
	event.preventDefault()
	var idServicio = $(this).attr("idServicio");
	var dataString = "idServicio=" + idServicio+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro de eliminar este regitro?!",
		icon: 'question',
		target:'#page-top',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Eliminar',
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/ServicioEliminar",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						//setTimeout("reload();", 1500);
						tablaAdmin.ajax.reload(null,false);
					}
				},
				error: function(XMLHttpRequest){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

function extraerDatosSenorita() {
	var arraySenoritas = [];
	$('#tablaSenoritas tbody tr').each(function () {
		var label = $(this).find('.categoriaSenorita');
		var idCategoria = label.attr('idCategoria');
		var monto = $(this).find('.montoTotal').val();		
		var porcentaje = $(this).find('.porcentajeSenorita').val();
		
		var senoritas = [idCategoria,monto,porcentaje];
		arraySenoritas.push(senoritas);
	});
	return JSON.stringify(arraySenoritas);
}

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
        $("#datosTablaSenoritas").val(extraerDatosSenorita());
		var proceso = $("#proceso").val();
		var FrmServicio = $("#FrmServicio");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmServicio[0]);
		}
		var ruta = (proceso == "Agregar") ? "ServicioAgregar" : (proceso == "Editar") ? "ServicioEditar" :'';
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmServicio.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta){
				//funcion de notificaciones, 4 parametros, tipo, titulo, subtitulo, mensaje
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200){
					setTimeout("reload();", 1500);
				} else{
					guardando = 0;
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				guardando = 0;
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
}

function reload() {
	location.href = url+'/'+padre;
}
