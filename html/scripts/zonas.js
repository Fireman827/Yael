var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Zonas';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/ZonasMostrar', token);
});

/** Control general de MODALES - Inicio*/

//levanta modal para agregar Zona
$(document).on("click", "#zonaAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/ZonasAgregar', function () {
		FormatoDatos();
		var regla={
			nombreZona:{
				required : true
			},
			capacidadZona:{
				required : true
			},
			// tipoAumentoZona:{
			// 	required : true
			// }
		};
		var formulario = "FrmZonas";
		validarDatos(formulario, regla);
	});
});
//levanta modal para editar Zona
$(document).on("click", ".ZonasEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idZona');
	$("#smModal .modal-content").load(url + '/ZonasEditar/' + id, function () { 
		FormatoDatos();
		var regla={
			nombreZona:{
				required : true
			},
			capacidadZona:{
				required : true
			},
			// tipoAumentoZona:{
			// 	required : true
			// }
		};
		var formulario = "FrmZonas";
		validarDatos(formulario, regla);
	 });
});
//levanta modal para administrar las mesas por Zona
$(document).on("click", ".ZonasMesas", function () {
	$("#dfModal").modal("show");
	var id = $(this).attr('idZona');
	$("#dfModal .modal-content").load(url + '/ZonasMesas/' + id, function () {
		FormatoDatos();
		$('.btn-accion').attr('disabled', true);
	});
});
//muestra la vista de agregar Mesa por Zona
$(document).on("click", "#agregarZonaMesa", function () {
	$("#proceso").val("ZonasMesasAgregar");
	$("#zonaMesaBody").attr('hidden', true);
	$("#zonaMesaAgregarBody").attr('hidden', false);
	$("#zonaMesaFooter").attr('hidden', true);
	$("#zonaMesaAgregarFooter").attr('hidden', false);
	var regla={};
	var formulario = "FrmMesasAgregar";
	validarDatos(formulario, regla);
});
//muestra la vista de editar Mesa por Zona
$(document).on("click", "#editarZonaMesa", function () {
	$("#proceso").val("ZonasMesasEditar");
	var infoMesas = arrayCheckbox();

	$("#zonaMesaBody").attr('hidden', true);
	$("#zonaMesaFooter").attr('hidden', true);
	$("#zonaMesaEditarBody").attr('hidden', false);
	$("#zonaMesaEditarFooter").attr('hidden', false);

	var tbody = '';
	var regla = {};
	for (let i = 0; i < infoMesas.length; i++) {
		regla["" + (infoMesas[i]['idMesa'])] = { required: true };
		tbody += "<tr>";
		tbody += "<td  style='text-align: center;'>" + infoMesas[i]['nombreMesa'] + "</td>";
		tbody += "<td><input type='text' class='form-control numeric' id='" + infoMesas[i]['idMesa'] + "' name='" + infoMesas[i]['idMesa'] + "' value='" + infoMesas[i]['capacidadMesa'] + "'></td>";
		tbody += "<td><a class='btn btn-sm btn-block btn-danger borrarMesaEditar' ><i class='fa fa-trash'></i></a></td>";
		tbody += "</tr>";
	}

	$("#tablaEditarMesa tbody").empty().append(tbody);
	FormatoDatos();
	var formulario = "FrmMesasEditar";
	validarDatos(formulario, regla);
});
//muestra la vista de eliminar Mesa por Zona
$(document).on("click", "#eliminarZonaMesa", function () {
	$("#proceso").val("ZonasMesasEliminar");
	var infoMesas = arrayCheckbox();

	$("#zonaMesaBody").attr('hidden', true);
	$("#zonaMesaFooter").attr('hidden', true);
	$("#zonaMesaEditarBody").attr('hidden', false);
	$("#zonaMesaEliminarFooter").attr('hidden', false);

	var tbody = '';
	var regla = {};
	for (let i = 0; i < infoMesas.length; i++) {
		regla["" + (infoMesas[i]['idMesa'])] = { required: true };

		tbody += "<tr>";
		tbody += "<td  style='text-align: center;'>" + infoMesas[i]['nombreMesa'] + "</td>";
		tbody += "<td><input type='hidden' class='form-control numeric' id='" + infoMesas[i]['idMesa'] + "' name='" + infoMesas[i]['idMesa'] + "' value='" + infoMesas[i]['capacidadMesa'] + "'>" + infoMesas[i]['capacidadMesa'] + "</td>";
		tbody += "<td><a class='btn btn-sm btn-block btn-danger borrarMesaEditar' ><i class='fa fa-trash'></i></a></td>";
		tbody += "</tr>";
	}

	$("#tablaEditarMesa tbody").empty().append(tbody);
	FormatoDatos();

	var formulario = "FrmMesasEditar";
	validarDatos(formulario, regla);
});
//muestra la vista de trasladar Mesa por Zona
$(document).on("click", "#trasladarZonaMesa", function () {
	$("#proceso").val("ZonasMesasTrasladar");
	var infoMesas = arrayCheckbox();

	$("#zonaMesaBody").attr('hidden', true);
	$("#zonaMesaFooter").attr('hidden', true);
	$("#zonaMesaTrasladarBody").attr('hidden', false);
	$("#zonaMesaTrasladarFooter").attr('hidden', false);

	var tbody = '';
	var regla = {};

	for (let i = 0; i < infoMesas.length; i++) {

		regla["" + (infoMesas[i]['idMesa'])] = { required: true };

		tbody += "<tr>";
		tbody += "<td style='text-align: center;'>" + infoMesas[i]['nombreMesa'] + "</td>";
		tbody += "<td><input type='text' class='form-control numeric nMesa' id='" + infoMesas[i]['idMesa'] + "' name='" + infoMesas[i]['idMesa'] + "' value=''></td>";
		tbody += "<td style='text-align: center;'>" + infoMesas[i]['capacidadMesa'] + "</td>";
		tbody += "<td><a class='btn btn-sm btn-block btn-danger borrarMesaEditar' ><i class='fa fa-trash'></i></a></td>";
		tbody += "</tr>";
	}

	$("#tablaEditarMesa tbody").empty().append(tbody);
	FormatoDatos();

	var formulario = "FrmMesasTrasladar";
	validarDatos(formulario, regla);
});
//elimina el contenido de la modal al cerrarse
$(document).on('hidden.bs.modal', function (e) {
	var target = $(e.target);
	target.removeData('bs.modal').find(".modal-content").html('');
});

/** Control general de MODALES - Final*/

//crea un array con los valores ingresados en agregar Mesa y asignarlos a un input
$(document).on("click", "#btnGuardarAgregar", function (event) {
	var formulario = $(this).attr('formulario');
	var k = 0;
	var array_json1 = new Array();
  	$("#tbodyAgregarZonaMesa tr").each(function() {
		var cantidad=$(this).find(".cantidadMesa").val();
		var capacidad=$(this).find(".capacidadMesa").val();
		if (cantidad!="") {
			var obj1 = new Object();
			obj1.cantidad = cantidad;
			obj1.capacidad = capacidad;
			text1 = JSON.stringify(obj1);
			array_json1.push(text1);
			k=k+1;
		}
	});
	json_arr1 = '[' + array_json1 + ']';
	$("#valores").val(json_arr1);
	$("#" + formulario).submit();
});
//click de Guardar en Agregar, Editar, Borrar y Trasladar Zona
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = $(this).attr('formulario');
	$("#" + formulario).submit();
});
//seleccionar las mesas a alterar (Editar, Borrar o Trasladar de Zona) usando un checkbox
$(document).on("click", ".checkboxMesas", function () {
	var marcado = $(this).is(":checked");
	var padre = $(this).parents('tr');
	padre.toggleClass("sticky-header");
	(marcado == true) ? (padre.remove(), $("#tablaMesas tbody").prepend(padre)) :
		(padre.insertAfter("#tr" + ($(this).attr("id") - 1)));
	var tr = $('tr.sticky-header');
	if (tr.length >= 1) {
		$('.btn-accion').attr('disabled', false);
	}
	if (tr.length <= 0) {
		$('.btn-accion').attr('disabled', true);
	}
});
//borrar una mesa de la lista de mesas a editar
$(document).on('click', '.borrarMesaEditar', function () {
	$(this).parents('tr').remove();
});
//verifica si ya existe una mesa con el numero ecrito en la zona donde se trasladará
$(document).on('keyup', '.nMesa', function () {
	var nombreMesa = $(this);
	var idZona = $("#zonaDestino").val();

	$.ajax({
		type: 'GET',
		url: url + '/ZonasMesasNuevoNombre',
		cache: false,
		data: "idZona=" + idZona + "&nombreZonaMesa=" + nombreMesa.val(),
		contentType: false,
		processData: false,
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 400) {
				nombreMesa.addClass('bg-danger');
				AlertaPersonalizada('error', 'Ya existe una mesa con ese número');
				$('#btnTrasladar').attr('disabled', true);
			}
			else {
				nombreMesa.removeClass('bg-danger');
			}
			var nMesa = $('.nMesa');
			nMesa.each(function () {
				if ($(this).val() == '') {
					$('#btnTrasladar').attr('disabled', true);
					return false;
				}
				else {
					$('#btnTrasladar').attr('disabled', false)
				}
			});
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			guardando = 0;
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
});
//cambia de estado una Zona de Activo a Inacivo o viseversa
$(document).on("click", ".ZonasCambiarEstado", function (event) {
	event.preventDefault()
	var idZona = $(this).attr("idZona");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idZona=" + idZona + "&csrf_test_name=" + token;
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
				url: url + "/ZonasCambiarEstado",
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
//elimina una Zona
$(document).on("click", ".ZonasEliminar", function (event) {
	event.preventDefault()
	var idZona = $(this).attr("idZona");
	var data = "idZona=" + idZona + "&csrf_test_name=" + token;
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
				url: url + "/ZonasEliminar",
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
//agrega una fila a la tabla para crear mesas por Zona
$(document).on("click","#agregarTablaZonaMesa",function(){
	var tbody = "";
	tbody += "<tr>";
	tbody += "<td><input type='text' class='form-control cantidadMesa numeric'></td>";
	tbody += "<td><input type='text' class='form-control capacidadMesa numeric'></td>";
	tbody += "<td><a class='btn btn-sm btn-block btn-danger borrarMesaEditar' ><i class='fa fa-trash'></i></a></td>"
	tbody += "</tr>";

	$("#tablaAgregarZonaMesa").append(tbody)
	FormatoDatos();
});
//retorna un array con los datos de las filas donde hay un checkbox marcado 
function arrayCheckbox() {
	var tr = $('tbody tr.sticky-header');
	var infoMesas = [];
	tr.each(function () {
		var td = $(this).children('td');
		var idMesa = td[0].children[0].children[0].id;
		var nombreMesa = td[1].textContent;
		var capacidadMesa = td[2].children[0].textContent;
		var mesa = {
			'idMesa': idMesa,
			'nombreMesa': nombreMesa,
			'capacidadMesa': capacidadMesa
		}
		infoMesas.push(mesa);
	});
	return infoMesas;
}

var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var proceso = $("#proceso").val();
		var FrmZonas = '';
		var ruta = (proceso == "Agregar") ? ("ZonasAgregar") :
			(proceso == "Editar") ? ("ZonasEditar/" + $("#idZona").val()) :
				(proceso == "ZonasMesasAgregar") ? ('ZonasMesasAgregar/') :
					(proceso == "ZonasMesasEditar") ? ("ZonasMesasEditar/") :
						(proceso == "ZonasMesasEliminar") ? ("ZonasMesasEliminar/") :
							(proceso == "ZonasMesasTrasladar") ? ("ZonasMesasTrasladar/") : "";
		if (proceso == "Agregar" || proceso == "Editar") {
			FrmZonas = $("#FrmZonas");
		} else {
			FrmZonas = (proceso == 'ZonasMesasAgregar') ? $("#FrmMesasAgregar") :
				(proceso == 'ZonasMesasEditar') ? $("#FrmMesasEditar") :
					(proceso == 'ZonasMesasEliminar') ? $("#FrmMesasEditar") :
						(proceso == 'ZonasMesasTrasladar') ? $("#FrmMesasTrasladar") : "";
		}
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmZonas[0]); }
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmZonas.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				//funcion de notificaciones, 4 parametros, tipo, titulo, subtitulo, mensaje
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					if (proceso != "Agregar" && proceso != "Editar") {
						$("#dfModal").modal('toggle');
					}
					if (proceso == "Agregar" || proceso == "Editar") {
						$("#smModal").modal('toggle');
					}
					tablaAdmin.ajax.reload(null, false);
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
