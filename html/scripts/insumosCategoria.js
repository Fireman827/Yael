var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'InsumosCategoria';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/InsumosCategoriaMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#insumosCategoriaAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/InsumosCategoriaAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".InsumosCategoriaEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idInsumoCategoria');
    //console.log(id);
	$("#smModal .modal-content").load(url + '/InsumosCategoriaEditar/'+id, function () {
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

//click de Guardar en Agregar InsumosCategoria
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmInsumosCategoria";
	var reglas = {
		nombreInsumosCategoria: {
			required: true,
		},
		descripcionInsumosCategoria: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmInsumosCategoria').submit();
});


$(document).on("click", ".InsumosCategoriaCambiarEstado", function (event) {
	event.preventDefault()
	var idInsumosCategoria = $(this).attr("idInsumoCategoria");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idInsumoCategoria=" + idInsumosCategoria + "&csrf_test_name=" + token;
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
				url: url + "/InsumosCategoriaCambiarEstado",
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

$(document).on("click", ".InsumosCategoriaEliminar", function (event) {
	event.preventDefault()
	var idInsumosCategoria = $(this).attr("idInsumoCategoria");
	var data = "idInsumoCategoria=" + idInsumosCategoria + "&csrf_test_name=" + token;
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
				url: url + "/InsumosCategoriaEliminar",
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
		var FrmInsumosCategoria = $("#FrmInsumosCategoria");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmInsumosCategoria[0]); }

		var ruta = (proceso == "Agregar") ? "InsumosCategoriaAgregar" : (proceso == "Editar") ? "InsumosCategoriaEditar/"+$("#idInsumoCategoria").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmInsumosCategoria.serialize(),
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