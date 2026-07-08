var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'ContratosTipo';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/ContratosTipoMostrar', token);

	var formulario = "FrmContratosTipo";
	var reglas = {
		nombreContratoTipo: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
});

/** Control general de MODALES - Inicio*/
/*
$(document).on("click", "#contratoTipoAgregar", function () {
	$("#xlModal").modal("show");
	$("#xlModal .modal-content").load(url + '/ContratosTipoAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".ContratosTipoEditar", function () {
	$("#xlModal").modal("show");
	var id = $(this).attr('idContratoTipo');
	$("#xlModal .modal-content").load(url + '/ContratosTipoEditar/'+id, function () {
		FormatoDatos();
	});
});

$(document).on('shown.bs.modal', function (e) {

});

$(document).on('hidden.bs.modal', function (e) {
	var target = $(e.target);
	target.removeData('bs.modal').find(".modal-content").html('');
});
*/
/** Control general de MODALES - Final*/

//Click para agregar Clausula del select a la tabla
$(document).on("click", ".agregarContratoClausula", function () {
	var existeClausula = false;
	var idContratoClausula = $('#idContratoClausula').val();

	if (idContratoClausula !== '') {
		$.ajax({
			type: "POST",
			url: url + "/ContratosTipoClausula",
			data: { 'idContratoClausula': idContratoClausula },
			dataType: 'json',
			success: function (respuesta) {
				if (respuesta.codigo == 200) {
					$('#tablaContratoTipoClausula tbody tr').each(function () {
						var id = $(this).find('.ContratosTipoClausulaBorrar').attr('idContratoClausula');
						if (id == idContratoClausula) {
							existeClausula = true;
						}
					});
					if (existeClausula) {
						AlertaPersonalizada('error', '¡La clausula ya esta agregada!');
					} else {
						$("#tablaContratoTipoClausula tbody").append(respuesta.contratoClausula);
					}
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
});

//Click para eliminar Clausula agregada a la tabla
$(document).on("click", ".ContratosTipoClausulaBorrar", function (event) {
	event.preventDefault();
	var idContratoTipoClausula = $(this).attr('idContratoTipoClausula');
	var dataString = "idContratoTipoClausula=" + idContratoTipoClausula + "&csrf_test_name=" + $("#csrf_token_id").val();
	$(this).parents('tr').remove();
	if (idContratoTipoClausula != '') {
		$.ajax({
			type: "POST",
			url: url + "/ContratosTipoClausulaEliminar",
			data: dataString,
			dataType: 'json',
			success: function (respuesta) {
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					//setTimeout("refresh();", 1500);
					//AlertaPersonalizada('success', '¡La clausula fue eliminada con éxito!');
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	} else {
		//$(this).parents('tr').remove();
	}
});

//click de Guardar en Agregar ContratoTipo
/*
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmContratosTipo";
	var reglas = {
		nombreContratoTipo: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmContratosTipo').submit();
});
*/

//Funcion para extraer las clausulas agregadas al tipo de contrato
function extraerDatos() {
	var arrayContratoClausula = [];
	$('#tablaContratoTipoClausula tbody tr').each(function () {
		var id = $(this).find('.ContratosTipoClausulaBorrar').attr('idContratoClausula');

		var contratoClausula = [id];
		arrayContratoClausula.push(contratoClausula);
	});
	return arrayContratoClausula;
}

//Click para cambiar el estado del tipo de contrato
$(document).on("click", ".ContratosTipoCambiarEstado", function (event) {
	event.preventDefault()
	var idContratoTipo = $(this).attr("idContratoTipo");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idContratoTipo=" + idContratoTipo + "&csrf_test_name=" + token;
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
				url: url + "/ContratosTipoCambiarEstado",
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

$(document).on("click", ".ContratosTipoEliminar", function (event) {
	event.preventDefault()
	var idContratoTipo = $(this).attr("idContratoTipo");
	var data = "idContratoTipo=" + idContratoTipo + "&csrf_test_name=" + token;
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
				url: url + "/ContratosTipoEliminar",
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
		var contratoTipoClausula = extraerDatos();
		var contratoTipoClausulaJSON = JSON.stringify(contratoTipoClausula);
		var id = $('#idContratoTipo').val();
		var nombre = $('#nombreContratoTipo').val();

		var dataString = {
			'idContratoTipo': id,
			'nombreContratoTipo': nombre,
			'datosContratoTipoClausula': contratoTipoClausulaJSON
		}
		var FrmContratosTipo = $("#FrmContratosTipo");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmContratosTipo[0]); }

		var ruta = (proceso == "Agregar") ? "ContratosTipoAgregar" : (proceso == "Editar") ? "ContratosTipoEditar/"+$("#idContratoTipo").val() : "";
		if(contratoTipoClausula.length!==0){
			$.ajax({
				type: 'POST',
				url: url + '/' + ruta,
				//cache: false,
				data: dataString,
				//data: Frm ? Frm : FrmContratosTipo.serialize(),
				//contentType: false,
				//processData: false,
				dataType: 'json',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200) {
						setTimeout('reload();',1500);
						//tablaAdmin.ajax.reload(null, false);
						//$("#xlModal").modal("toggle");
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
		} else {
			AlertaPersonalizada('error', '¡Debes agregar al menos una clausula!');
		}
	}
}
function reload() {
	location.href = url + '/' + padre;
}