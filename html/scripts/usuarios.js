var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Usuarios';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/UsuarioMostrar',token);

	var formularioId = "FrmUsuario";
	var reglas = {
			nombreUsuario:{
			required: true,
		},
		claveUsuario:{
			required: true,
			minlength: 6
		},
		codigoUsuario:{
			required: true,
			minlength: 1
		},
		usuarioUsuario:{
			required: true
		}
	};
	validarDatos(formularioId,reglas);
	$("#adminUsuario").click(function(event){
		if($(this).is(":checked")){
			$(".permisoUsuario").prop('checked',true);
			$(this).val('1');
		} else{
			$(this).val('0');
			$(".permisoUsuario").prop('checked',false);
		}
	});
	$(".permisoUsuario").click(function(event){
		var principal = $(this).hasClass("principal");
		var controlador = $(this).attr("controlador");
		if($(this).is(":checked")){
			if(!principal){
				$(this).parents(".menuPermiso").find(".principal[controlador='"+controlador+"']").prop('checked',true);
			}
		} else {
			if(principal){
				if($(this).parents(".menuPermiso").find(".permisoUsuario[controlador='"+controlador+"']:checked").length > 0){
					$(this).parents(".menuPermiso").find(".principal[controlador='"+controlador+"']").prop('checked',true);
				}
			}
		}
	});
});


$(document).on("click", "#btnGuardar", function(event){
  event.preventDefault();
	PermisosAgregarEditar();
});



$(document).on("click",".UsuarioCambiarEstado", function(event){
	event.preventDefault()
	var idUsuario = $(this).attr("idUsuario");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idUsuario=" + idUsuario+"&csrf_test_name="+token;
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
				url: url+"/UsuarioCambiarEstado",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						setTimeout("reload();", 1500);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".UsuarioEliminar", function(event){
	event.preventDefault()
	var idUsuario = $(this).attr("idUsuario");
	var dataString = "idUsuario=" + idUsuario+"&csrf_test_name="+token;
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
				url: url+"/UsuarioEliminar",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						setTimeout("reload();", 1500);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					MostrarAlerta('error',errorThrown, textStatus, XMLHttpRequest.responseText);
				}
			});
		}
	});
});

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		var FrmUsuario = $("#FrmUsuario");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmUsuario[0]);
		}
		if($("#proceso").val() == "Editar"){
			ruta = "UsuarioEditar";
		}
		if($("#proceso").val() == "Agregar"){
			ruta = "UsuarioAgregar";
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

var guardando = 0;
function PermisosAgregarEditar(){
  if (!guardando) {
    guardando = 1;
		var permisosArray = new Array();
    $('.permisoUsuario:checked').each(function(){
			var permisoObj = new Object();
      permisoObj.idModulo = $(this).data('idmodulo');
      permisoText = JSON.stringify(permisoObj);
      permisosArray.push(permisoText);
    });
		var listaPermisos = '[' + permisosArray + ']';

		var idUsuario = $("#idUsuario").val();
		var adminUsuario = $("#adminUsuario").val();

    $.ajax({
      type: "POST",
      url: url+"/UsuarioPermisos",
      data:{
				idUsuario: idUsuario,
				adminUsuario: adminUsuario,
				listaPermisos: listaPermisos,
			},
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

