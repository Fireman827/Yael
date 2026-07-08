var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'ActivoFijo';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ActivoFijoMostrar',token);

	var formularioId = "FrmActivoFijo";
	var reglas = {
		nombreActivoFijo:{
			required: true,
		},
		vidaActivoFijo:{
			required: true,
		},
		depreciaciónActivoFijo:{
			required: true,
		},
		marcaActivoFijo:{
			required: true,
		},
	};
	validarDatos(formularioId,reglas);

  $(".numeric2").numeric({
    negative: false,
    // decimal: false
  });
});



var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		var FrmUsuario = $("#FrmActivoFijo");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmUsuario[0]);
		}
		if($("#proceso").val() == "Editar"){
			ruta = "ActivoFijoEditar";
		}
		if($("#proceso").val() == "Agregar"){
			ruta = "ActivoFijoAgregar";
		}
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmUsuario.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta){
				//funcion de notificaciones, 4 parametros, tipo, titulo, subtitulo, mensaje
				//tipos Exito, Error, Advertencia, Informacion
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
	location.href = url + '/' + padre;
}
