var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'InstitucionFinanciera';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/InstitucionFinancieraMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#institucionFinancieraAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/InstitucionFinancieraAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".InstitucionFinancieraEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idInstitucionFinanciera');
	$("#smModal .modal-content").load(url + '/InstitucionFinancieraEditar/'+id, function () {
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

//click de Guardar en Agregar InstitucionFinanciera
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmInstitucionFinanciera";
	var reglas = {
		nombreInstitucionFinanciera: {
			required: true,
		},
		unidadInstitucionFinanciera: {
			required: true,
		},
        descripcionInstitucionFinanciera:{
            required:true
        }
	};
	validarDatos(formulario, reglas);
	$('#FrmInstitucionFinanciera').submit();
});


$(document).on("click", ".InstitucionFinancieraCambiarEstado", function (event) {
	event.preventDefault()
	var idInstitucionFinanciera = $(this).attr("idInstitucionFinanciera");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idInstitucionFinanciera=" + idInstitucionFinanciera + "&csrf_test_name=" + token;
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
				url: url + "/InstitucionFinancieraCambiarEstado",
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

$(document).on("click", ".InstitucionFinancieraEliminar", function (event) {
	event.preventDefault()
	var idInstitucionFinanciera = $(this).attr("idInstitucionFinanciera");
	var data = "idInstitucionFinanciera=" + idInstitucionFinanciera + "&csrf_test_name=" + token;
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
				url: url + "/InstitucionFinancieraEliminar",
				data: data,
				dataType: 'json',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200) {
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
		var FrmInstitucionFinanciera = $("#FrmInstitucionFinanciera");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmInstitucionFinanciera[0]); }

		var ruta = (proceso == "Agregar") ? "InstitucionFinancieraAgregar" : (proceso == "Editar") ? "InstitucionFinancieraEditar/"+$("#idInstitucionFinanciera").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmInstitucionFinanciera.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
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