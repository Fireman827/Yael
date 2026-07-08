var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Senorita';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/SenoritaMostrar',token);
	var formulario = "FrmSenorita";
	var reglas = {
			nombreSenorita:{
				required : true,
			},
			apodoSenorita:{
				required : true,
			},
			categoriaSenorita:{
				required : true,
			},
			nacionalidadSenorita:{
				required : true,
			}
		};
	validarDatos(formulario,reglas);
});

$(document).on("click",".SenoritaCambiarEstado", function(event){
	event.preventDefault()
	var idSenorita = $(this).attr("idSenorita");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idSenorita=" + idSenorita+"&csrf_test_name="+token;
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
				url: url+"/SenoritaCambiarEstado",
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

$(document).on("click",".SenoritaEliminar", function(event){
	event.preventDefault()
	var idSenorita = $(this).attr("idSenorita");
	var dataString = "idSenorita=" + idSenorita+"&csrf_test_name="+token;
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
				url: url+"/SenoritaEliminar",
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

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		var proceso = $("#proceso").val();
		var FrmSenorita = $("#FrmSenorita");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmSenorita[0]);
		}
		var ruta = (proceso == "Agregar") ? "SenoritaAgregar" : (proceso == "Editar") ? "SenoritaEditar" :'';
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmSenorita.serialize(),
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
