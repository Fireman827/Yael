var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Cargos';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/CargosMostrar', token);
	//CKEDITOR.replace('editor1');
});

/** Control general de MODALES - Inicio*/

$(document).on("click", "#cargoAgregar", function () {
	$("#xlModal").modal("show");
	$("#xlModal .modal-content").load(url + '/CargosAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".CargosEditar", function () {
	$("#xlModal").modal("show");
	var id = $(this).attr('idCargo');
	$("#xlModal .modal-content").load(url + '/CargosEditar/'+id, function () {
		FormatoDatos();
	});
});

$(document).on('shown.bs.modal', function (e) {
	CKEDITOR.replace('editor1');
});

$(document).on('hidden.bs.modal', function (e) {
	var target = $(e.target);
	target.removeData('bs.modal').find(".modal-content").html('');
});

/** Control general de MODALES - Final*/

//click de Guardar en Agregar Cargo
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmCargos";
	var reglas = {
		nombreCargo: {
			required: true,
		},
		descripcionCargo: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmCargos').submit();
});


$(document).on("click", ".CargosCambiarEstado", function (event) {
	event.preventDefault()
	var idCargo = $(this).attr("idCargo");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idCargo=" + idCargo + "&csrf_test_name=" + token;
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
				url: url + "/CargosCambiarEstado",
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

$(document).on("click", ".CargosEliminar", function (event) {
	event.preventDefault()
	var idCargo = $(this).attr("idCargo");
	var data = "idCargo=" + idCargo + "&csrf_test_name=" + token;
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
				url: url + "/CargosEliminar",
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
		var id = $('#idCargo').val();
		var nombre = $('#nombreCargo').val();
		var descripcion = $('#descripcionCargo').val();
		var funciones = CKEDITOR.instances['editor1'].getData();
		var dataString = {
			'idCargo':id,
			'nombreCargo':nombre,
			'descripcionCargo':descripcion,
			'funcionesCargo': funciones
		}
		console.log(dataString);
		
		var FrmCargos = $("#FrmCargos");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmCargos[0]); }

		var ruta = (proceso == "Agregar") ? "CargosAgregar" : "CargosEditar/" + $("#idCargo").val();
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			//cache: false,
			data: Frm ? Frm : FrmCargos.serialize(),
			data:dataString,
			//contentType: false,
			//processData: false,
			dataType: 'json',
			success: function (respuesta) {
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					tablaAdmin.ajax.reload(null, false);
                    $("#xlModal").modal("toggle");
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
