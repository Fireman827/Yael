var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'ModificadoresTipo';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ModificadoresTipoMostrar',token);
});

$(document).on("click", "#modificadorTipoAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/ModificadoresTipoAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".ModificadoresTipoEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idTipo');
	$("#smModal .modal-content").load(url + '/ModificadoresTipoEditar/'+id, function () {
		FormatoDatos();
	});
});

//click de Guardar en Agregar Impresora
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmModificadoresTipo";
	var reglas = {
			nombreTipo:{
				required : true,
			},
			varios:{
				required : true,
			}
		};
	validarDatos(formulario,reglas);
	$('#FrmModificadoresTipo').submit();
	//$("#smModal").modal("toggle");
});

$(document).on("click",".ModificadoresTipoCambiarEstado", function(event){
	event.preventDefault()
	var idTipo = $(this).attr("idTipo");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idTipo=" + idTipo+"&csrf_test_name="+token;
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
				url: url+"/ModificadoresTipoCambiarEstado",
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

$(document).on("click",".ModificadoresTipoEliminar", function(event){
	event.preventDefault()
	var idTipo = $(this).attr("idTipo");
	var dataString = "idTipo=" + idTipo+"&csrf_test_name="+token;
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
				url: url+"/ModificadoresTipoEliminar",
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
		var FrmModificadoresTipo = $("#FrmModificadoresTipo");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmModificadoresTipo[0]);
		}
		var ruta = (proceso == "Agregar") ? "ModificadoresTipoAgregar" : (proceso == "Editar") ? "ModificadoresTipoEditar" :'';
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmModificadoresTipo.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta){
				//funcion de notificaciones, 4 parametros, tipo, titulo, subtitulo, mensaje
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200){
					$("#smModal").modal("toggle");
					tablaAdmin.ajax.reload(null,false);
					guardando = 0;
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
