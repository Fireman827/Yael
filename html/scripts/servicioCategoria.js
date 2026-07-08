var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'ServicioCategoria';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/ServicioCategoriaMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#ServicioCategoriaAgregar", function () {
	$("#dfModal").modal("show");
	$("#dfModal .modal-content").load(url + '/ServicioCategoriaAgregar', function () {
	});
});

$(document).on("click", ".ServicioCategoriaEditar", function () {
	$("#dfModal").modal("show");
	var id = $(this).attr('idCategoria');
	$("#dfModal .modal-content").load(url + '/ServicioCategoriaEditar/'+id, function () {
  });
});

$(document).on('shown.bs.modal', function (e) {
  FormatoDatos();
});

$(document).on('hidden.bs.modal', function (e) {
	var target = $(e.target);
	target.removeData('bs.modal').find(".modal-content").html('');
});

/** Control general de MODALES - Final*/

//click de Guardar en Agregar ServicioCategoria
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmServicioCategoria";
	var reglas = {
		nombreServicioCategoria: {
			required: true,
		},
    	descripcionServicioCategoria: {
			required: true,
		},
	};
	validarDatos(formulario, reglas);
	$('#FrmServicioCategoria').submit();
});


$(document).on("click", ".ServicioCategoriaCambiarEstado", function (event) {
	event.preventDefault()
	var idServicioCategoria = $(this).attr("idCategoria");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idCategoria=" + idServicioCategoria + "&csrf_test_name=" + token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea " + accion + " este registro?!",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, ' + accion,
		cancelButtonText: 'Cancelar',
	}).then((result) => {
		if (result.value) {
			$.ajax({
				type: "POST",
				url: url + "/ServicioCategoriaCambiarEstado",
				data: data,
				dataType: 'json',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200) {
						tablaAdmin.ajax.reload(null, false);
					}
				},
				error: function (XMLHttpRequest) {
					AlertaPersonalizada("error", XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click", ".ServicioCategoriaEliminar", function (event) {
	event.preventDefault()
	var idServicioCategoria = $(this).attr("idCategoria");
	var data = "idCategoria=" + idServicioCategoria + "&csrf_test_name=" + token;
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
	}).then((result) => {
		if (result.value) {
			$.ajax({
				type: "POST",
				url: url + "/ServicioCategoriaEliminar",
				data: data,
				dataType: 'json',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200) {
						//setTimeout("reload();", 1500);
						tablaAdmin.ajax.reload(null, false);
					}
				},
				error: function (XMLHttpRequest) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var proceso = $("#proceso").val();
		var FrmServicioCategoria = $("#FrmServicioCategoria");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmServicioCategoria[0]); }

		var ruta = (proceso == "Agregar") ? "ServicioCategoriaAgregar" : (proceso == "Editar") ? "ServicioCategoriaEditar/" : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmServicioCategoria.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					tablaAdmin.ajax.reload(null, false);
					$("#dfModal").modal("toggle");
					guardando = 0;
				} else {
					guardando = 0;
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				guardando = 0;
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
}
function reload() {
	location.href = url + '/' + padre;
}
