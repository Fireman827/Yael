var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'PeriodosPlanilla';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/PeriodosPlanillaMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#periodoPlanilla", function () {

    $.ajax({
        type: "POST",
        url: url + "/PeriodosPlanillaVigente",
        data: {},
        dataType: 'json',
        success: function (respuesta) {
            //Alerta(respuesta.codigo);
            if (respuesta.codigo == 200) {
                $("#lgModal").modal("show");
                $("#lgModal .modal-content").load(url + '/PeriodosPlanillaEditar/'+respuesta.idPeriodoPlanilla, function () {
                    FormatoDatos();
                });
            } else {
                $("#lgModal").modal("show");
                $("#lgModal .modal-content").load(url + '/PeriodosPlanillaEditar/0', function () {
                    FormatoDatos();
                });
            }
        },
        error: function (XMLHttpRequest) {
            AlertaPersonalizada("error", XMLHttpRequest.responseText);
        }
    });

});

$(document).on("click", ".PeriodosPlanillaEditar", function () {
	$("#lgModal").modal("show");
	var id = $(this).attr('idPeriodoPlanilla');
	$("#lgModal .modal-content").load(url + '/PeriodosPlanillaEditar/'+id, function () {
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

//click de Guardar en Agregar PeriodoPlanilla
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmPeriodosPlanilla";
	var reglas = {
		fechaInicioPagoPlanilla: {
			required: true,
		},
		fechaFinPagoPlanilla: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmPeriodosPlanilla').submit();
});


$(document).on("click", ".PeriodosPlanillaCambiarEstado", function (event) {
	event.preventDefault()
	var idPeriodoPlanilla = $(this).attr("idPeriodoPlanilla");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idPeriodoPlanilla=" + idPeriodoPlanilla + "&csrf_test_name=" + token;
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
				url: url + "/PeriodosPlanillaCambiarEstado",
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

$(document).on("click", ".PeriodosPlanillaEliminar", function (event) {
	event.preventDefault()
	var idPeriodoPlanilla = $(this).attr("idPeriodoPlanilla");
	var data = "idPeriodoPlanilla=" + idPeriodoPlanilla + "&csrf_test_name=" + token;
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
				url: url + "/PeriodosPlanillaEliminar",
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
		var FrmPeriodosPlanilla = $("#FrmPeriodosPlanilla");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmPeriodosPlanilla[0]); }

		var ruta = (proceso == "Agregar") ? "PeriodosPlanillaAgregar" : (proceso == "Editar") ? "PeriodosPlanillaEditar/":"";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmPeriodosPlanilla.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					tablaAdmin.ajax.reload(null, false);
                    $("#lgModal").modal("toggle");
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
