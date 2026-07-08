var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'ClientesCategoria';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/ClientesCategoriaMostrar', token);
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#clienteCategoriaAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/ClientesCategoriaAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".ClientesCategoriaEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idClienteCategoria');
	$("#smModal .modal-content").load(url + '/ClientesCategoriaEditar/'+id, function () {
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

//click de Guardar en Agregar Categoría de Cliente
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmClientesCategoria";
	var reglas = {
		nombreClienteCategoria: {
			required: true,
		},
		descripcionClienteCategoria: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmClientesCategoria').submit();
	//$("#smModal").modal("toggle");
});

//guardar mesa y salir de la modal
/*$(document).on("click","#btnGuardarSalir",function () {
	var formulario = "FrmClientesCategoria";
	var reglas = {
		nombreClienteCategoriaMesa: {
			required: true,
		},
		capacidadClienteCategoriaMesa: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmClientesCategoria').submit();
	$("#dfModal").modal("toggle");
});*/

$(document).on("click", ".ClientesCategoriaCambiarEstado", function (event) {
	event.preventDefault()
	var idClienteCategoria = $(this).attr("idClienteCategoria");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idClienteCategoria=" + idClienteCategoria + "&csrf_test_name=" + token;
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
				url: url + "/ClientesCategoriaCambiarEstado",
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

$(document).on("click", ".ClientesCategoriaEliminar", function (event) {
	event.preventDefault()
	var idClienteCategoria = $(this).attr("idClienteCategoria");
	var data = "idClienteCategoria=" + idClienteCategoria + "&csrf_test_name=" + token;
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
				url: url + "/ClientesCategoriaEliminar",
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
		var FrmClientesCategoria = $("#FrmClientesCategoria");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmClientesCategoria[0]); }

		var ruta = (proceso == "Agregar") ? "ClientesCategoriaAgregar" : (proceso == "Editar") ? "ClientesCategoriaEditar/"+$("#idClienteCategoria").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmClientesCategoria.serialize(),
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
