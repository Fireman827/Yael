var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Roles';
var tablaAdmin;
$(document).ready(function() {
  tablaAdmin = RenderizarTabla(url,'/RolMostrar',token);

	$(".permisoRol").click(function(event){
		var principal = $(this).hasClass("principal");
    var controlador = $(this).attr("controlador");
		if($(this).is(":checked")){
			if(!principal){
				$(this).parents(".menuPermiso").find(".principal[controlador='"+controlador+"']").prop('checked',true);
			}
		}	else{
			if(principal){
				if($(this).parents(".menuPermiso").find(".permisoRol[controlador='"+controlador+"']:checked").length > 0){
					$(this).parents(".menuPermiso").find(".principal[controlador='"+controlador+"']").prop('checked',true);
				}
			}
		}
	});
});


$(document).on("click", "#btnGuardar", function(event){
  event.preventDefault();
  if ($('.permisoRol:checked').length > 0){
		if ($("#nombreRol").val() != ""){
			RolAgregarEditar();
		} else{
			AlertaPersonalizada("advertencia",  "Ingrese un nombre para el rol");
		}
  } else {
    AlertaPersonalizada("advertencia",  "Seleccione al menos un permiso para este rol");
  }
});

$(document).on("click", ".RolEstado", function(event){
  event.preventDefault()
  var idRol = $(this).attr("idRol");
  var accion = $(this).data("accion").toLowerCase();
  var dataString = "idRol=" + idRol + "&csrf_test_name=" + token;
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro que desea " + accion + " este registro?!",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, ' + accion,
    cancelButtonText: 'Cancelar',
  }).then((result) =>{
    if (result.value){
      $.ajax({
        type: "POST",
        url: url + "/RolCambiarEstado",
        data: dataString,
        dataType: 'json',
        success: function(respuesta){
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

$(document).on("click", ".RolEliminar", function(event){
  event.preventDefault()
  var idRol = $(this).attr("idRol");
  var dataString = "idRol=" + idRol + "&csrf_test_name=" + token;
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro de eliminar este regitro?!",
    icon: 'question',
    target: '#page-top',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si, Eliminar',
    cancelButtonText: 'Cancelar',
  }).then((result) =>{
    if (result.value){
      $.ajax({
        type: "POST",
        url: url + "/RolEliminar",
        data: dataString,
        dataType: 'json',
        success: function(respuesta){
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

var guardando = 0;
function RolAgregarEditar(){
  if (!guardando) {
    guardando = 1;
		var permisosArray = new Array();
    $('.permisoRol:checked').each(function(){
			var permisoObj = new Object();
      permisoObj.idModulo = $(this).data('idmodulo');
      permisoText = JSON.stringify(permisoObj);
      permisosArray.push(permisoText);
    });
		var listaPermisos = '[' + permisosArray + ']';

		if($("#proceso").val() == "Editar"){
			ruta = "RolEditar";
		}
		if($("#proceso").val() == "Agregar"){
			ruta = "RolAgregar";
		}
		var idRol = $("#idRol").val();
		var nombreRol = $("#nombreRol").val();
		var rutaRol = $("#rutaRol").val();


    $.ajax({
      type: "POST",
      url: url+"/"+ruta,
      data:{
				idRol: idRol,
				nombreRol: nombreRol,
				rutaRol: rutaRol,
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
