var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Impresoras';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/ImpresorasMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#impresoraAgregar", function () {
	$("#dfModal").modal("show");
	$("#dfModal .modal-content").load(url + '/ImpresorasAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".ImpresorasEditar", function () {
	$("#dfModal").modal("show");
	var id = $(this).attr('idImpresora');
	$("#dfModal .modal-content").load(url + '/ImpresorasEditar/'+id, function () {
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

//click de Guardar en Agregar Impresora
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmImpresoras";
	var reglas = {
		nombreImpresora: {
			required: true,
		},
		recursoCompartidoImpresora: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmImpresoras').submit();
	//$("#smModal").modal("toggle");
});


$(document).on("click", ".ImpresorasCambiarEstado", function (event) {
	event.preventDefault()
	var idImpresora = $(this).attr("idImpresora");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idImpresora=" + idImpresora + "&csrf_test_name=" + token;
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
				url: url + "/ImpresorasCambiarEstado",
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

$(document).on("click", ".ImpresorasEliminar", function (event) {
	event.preventDefault()
	var idImpresora = $(this).attr("idImpresora");
	var data = "idImpresora=" + idImpresora + "&csrf_test_name=" + token;
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
				url: url + "/ImpresorasEliminar",
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

$(document).on("click", ".ImpresorasTest", function (event) {
	event.preventDefault()
	var idImpresora = $(this).attr("idImpresora");
	var data = "idImpresora=" + idImpresora + "&csrf_test_name=" + token;
	$.ajax({
		type: "POST",
		url: url + "/ImpresorasTest",
		data: data,
		dataType: 'json',
		success: function (respuesta) {
			Alerta(respuesta.codigo);
			if(respuesta.codigo == 200){
				$.post("http://"+respuesta.servidor+"/imprimir/printTest.php", {
					datos: respuesta.datos,
				});
			}
		},
		error: function (XMLHttpRequest) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
});

var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var proceso = $("#proceso").val();
		var FrmImpresoras = $("#FrmImpresoras");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmImpresoras[0]); }

		var ruta = (proceso == "Agregar") ? "ImpresorasAgregar" : (proceso == "Editar") ? "ImpresorasEditar/"+$("#idImpresora").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmImpresoras.serialize(),
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
