var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'TramosRenta';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/TramosRentaMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#tramoRentaAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/TramosRentaAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".TramosRentaEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idTramoRenta');
    console.log(id);
	$("#smModal .modal-content").load(url + '/TramosRentaEditar/'+id, function () {
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

//click de Guardar en Agregar Tramo de la Renta
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmTramosRenta";
	var reglas = {
		desdeTramoRenta: {
			required: true,
		},
		hastaTramoRenta: {
			required: true,
		},
        porcentajeTramoRenta: {
			required: true,
		},
        excesoTramoRenta: {
			required: true,
		},
        cuotaTramoRenta: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmTramosRenta').submit();
});


$(document).on("click", ".TramosRentaCambiarEstado", function (event) {
	event.preventDefault()
	var idTramoRenta = $(this).attr("idTramoRenta");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idTramoRenta=" + idTramoRenta + "&csrf_test_name=" + token;
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
				url: url + "/TramosRentaCambiarEstado",
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

$(document).on("click", ".TramosRentaEliminar", function (event) {
	event.preventDefault()
	var idTramoRenta = $(this).attr("idTramoRenta");
	var data = "idTramoRenta=" + idTramoRenta + "&csrf_test_name=" + token;
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
				url: url + "/TramosRentaEliminar",
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
		var FrmTramosRenta = $("#FrmTramosRenta");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmTramosRenta[0]); }

		var ruta = (proceso == "Agregar") ? "TramosRentaAgregar" : (proceso == "Editar") ? "TramosRentaEditar/"+$("#idTramoRenta").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmTramosRenta.serialize(),
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
