var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'ProductosCategoria';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ProductosCategoriaMostrar',token);
	var formulario = "FrmProductosCategoria";
	var reglas = {
			nombreCategoria:{
				required : true,
			},
		};
	validarDatos(formulario,reglas);
});

$(document).on("click",".ProductosCategoriaCambiarEstado", function(event){
	event.preventDefault()
	var idCategoria = $(this).attr("idProductoCategoria");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idCategoria=" + idCategoria+"&csrf_test_name="+token;
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
				url: url+"/ProductosCategoriaCambiarEstado",
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

$(document).on("click",".ProductosCategoriaEliminar", function(event){
	event.preventDefault()
	var idCategoria = $(this).attr("idProductoCategoria");
	var dataString = "idCategoria=" + idCategoria+"&csrf_test_name="+token;
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
				url: url+"/ProductosCategoriaEliminar",
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
		var FrmProductosCategoria = $("#FrmProductosCategoria");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmProductosCategoria[0]);
		}
		var ruta = (proceso == "Agregar") ? "ProductosCategoriaAgregar" : (proceso == "Editar") ? "ProductosCategoriaEditar" :'';
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmProductosCategoria.serialize(),
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
