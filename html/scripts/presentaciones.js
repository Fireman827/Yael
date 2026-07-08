var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Presentaciones';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/PresentacionesMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#presentacionAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/PresentacionesAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".PresentacionesEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idPresentacion');
    //console.log(id);
	$("#smModal .modal-content").load(url + '/PresentacionesEditar/'+id, function () {
		FormatoDatos();
	});
});

$(document).on('shown.bs.modal', function (e) {

});

$(document).on('hidden.bs.modal', function (e) {
	var target = $(e.target);
	target.removeData('bs.modal').find(".modal-content").html('');
});

/** Control general de MODALES - Final*/

//click de Guardar en Agregar Presentaciones
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmPresentaciones";
	var reglas = {
		nombrePresentaciones: {
			required: true,
		},
		unidadPresentaciones: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmPresentaciones').submit();
	//$("#smModal").modal("toggle");
});


$(document).on("click", ".PresentacionesCambiarEstado", function (event) {
	event.preventDefault()
	var idPresentaciones = $(this).attr("idPresentacion");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idPresentacion=" + idPresentaciones + "&csrf_test_name=" + token;
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
				url: url + "/PresentacionesCambiarEstado",
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
					AlertaPersonalizada("error", XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click", ".PresentacionesEliminar", function (event) {
	event.preventDefault()
	var idPresentaciones = $(this).attr("idPresentacion");
	var data = "idPresentacion=" + idPresentaciones + "&csrf_test_name=" + token;
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
				url: url + "/PresentacionesEliminar",
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
		var FrmPresentaciones = $("#FrmPresentaciones");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmPresentaciones[0]); }

		var ruta = (proceso == "Agregar") ? "PresentacionesAgregar" : (proceso == "Editar") ? "PresentacionesEditar/"+$("#idPresentaciones").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmPresentaciones.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					tablaAdmin.ajax.reload(null, false);
                    $("#smModal").modal("toggle");
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