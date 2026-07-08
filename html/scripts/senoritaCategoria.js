var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'SenoritaCategoria';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/SenoritaCategoriaMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#senoritaCategoriaAgregar", function () {
	$("#dfModal").modal("show");
	$("#dfModal .modal-content").load(url + '/SenoritaCategoriaAgregar', function () {
	});
});

$(document).on("click", ".SenoritaCategoriaEditar", function () {
	$("#dfModal").modal("show");
	var id = $(this).attr('idCategoria');
	$("#dfModal .modal-content").load(url + '/SenoritaCategoriaEditar/'+id, function () {
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

//click de Guardar en Agregar senoritaCategoria
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmSenoritaCategoria";
	var reglas = {
		categoriaSenorita: {
			required: true,
		},
    tipoComision: {
			required: true,
		},
    cantidadComision: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmSenoritaCategoria').submit();
});


$(document).on("click", ".SenoritaCategoriaCambiarEstado", function (event) {
	event.preventDefault()
	var idSenoritaCategoria = $(this).attr("idCategoria");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idCategoria=" + idSenoritaCategoria + "&csrf_test_name=" + token;
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
				url: url + "/SenoritaCategoriaCambiarEstado",
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

$(document).on("click", ".SenoritaCategoriaEliminar", function (event) {
	event.preventDefault()
	var idSenoritaCategoria = $(this).attr("idCategoria");
	var data = "idCategoria=" + idSenoritaCategoria + "&csrf_test_name=" + token;
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
				url: url + "/SenoritaCategoriaEliminar",
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
		var FrmSenoritaCategoria = $("#FrmSenoritaCategoria");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmSenoritaCategoria[0]); }

		var ruta = (proceso == "Agregar") ? "SenoritaCategoriaAgregar" : (proceso == "Editar") ? "SenoritaCategoriaEditar/"+$("#idSenoritaCategoria").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmSenoritaCategoria.serialize(),
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
