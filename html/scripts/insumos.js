var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Insumos';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/InsumosMostrar', token);
	var formulario = "FrmInsumos";
	var reglas = {
		nombreInsumos: {
			required: true,
		},
		categoriaInsumos: {
			required: true,
		},
		marcaInsumos: {
			required: true,
		},
		proveedor1Insumos: {
			required: true,
		},
		stockMininoInsumos: {
			required: true,
		},
	};
	validarDatos(formulario, reglas);
});

/** Control general de MODALES - Inicio*/

$(document).on('shown.bs.modal', function (e) {
});

$(document).on('hidden.bs.modal', function (e) {
	var target = $(e.target);
	target.removeData('bs.modal').find(".modal-content").html('');
});

/** Control general de MODALES - Final*/

$(document).on("click", "#agregarTablaPresentacionesInsumos", function () {
	$.ajax({
		type: 'POST',
		url: url + '/InsumosPresentaciones',
		cache: false,
		data: {},
		contentType: false,
		processData: false,
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$("#tablaPresentacionesInsumos").append(respuesta.tbody);
				FormatoDatos();
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});

});
$(document).on("click", "#advaloremInsumos", function (event) {
	if($("#advaloremInsumos").is(":checked")){
		$("#divMontoSugeridoInsumo").attr("hidden",false)
	} else {
		$("#divMontoSugeridoInsumo").attr("hidden",true);
	}
});

$(document).on("click", ".InsumosCambiarEstado", function (event) {
	event.preventDefault()
	var idInsumo = $(this).attr("idInsumo");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idInsumo=" + idInsumo + "&csrf_test_name=" + token;
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
				url: url + "/InsumosCambiarEstado",
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

$(document).on("click", ".InsumosEliminar", function (event) {
	event.preventDefault()
	var idInsumo = $(this).attr("idInsumo");
	var data = "idInsumo=" + idInsumo + "&csrf_test_name=" + token;
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
				url: url + "/InsumosEliminar",
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

//borrar una fila de la lista de presentaciones
$(document).on('click', '.borrarTablaPresentacionInsumos', function () {
	var tr = $(this).parents('tr');
	var idPresentacion = $(this).attr("idPresentacion");
	var idInsumo = $("#idInsumo").val();
	$.ajax({
		type: 'POST',
		url: url + '/InsumosVerificarBorrado',
		data: {
			idPresentacion : idPresentacion,
			idInsumo : idInsumo
		},
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				tr.remove();
			}
			else{
				AlertaPersonalizada("Error","No puede borrar esta presentacion!!, existen productos que actualmente dependen de esta")
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});

});
// $(document).on("change", ".presentacion,.descripcion,.unidad,.costo,.precio", function () {
// 	validarTablaInsumosPresentacion();
// });

function validarTablaInsumosPresentacion() {
	var tabla = $("#tablaPresentacionesInsumos tr");
	var n = 0;
	var i = 1;
	tabla.each(function () {
		var preInventario = $(this).find(".preInventario");
		var presentacion = $(this).find(".presentacion");
		var descripcion = $(this).find(".descripcion");
		var unidad = $(this).find(".unidad");
		var costo = $(this).find(".costo");
		var precio = $(this).find(".precio");
		(preInventario.is(":checked")) ? i = 0 : ""; 
		(presentacion.val() == '') ? (presentacion.addClass('is-invalid'), n++) : presentacion.removeClass('is-invalid');
		(descripcion.val() == '') ? (descripcion.addClass('is-invalid'), n++) : descripcion.removeClass('is-invalid');
		(unidad.val() == '') ? (unidad.addClass('is-invalid'), n++) : unidad.removeClass('is-invalid');
		(costo.val() == '') ? (costo.addClass('is-invalid'), n++) : costo.removeClass('is-invalid');
		(precio.val() == '') ? (precio.addClass('is-invalid'), n++) : precio.removeClass('is-invalid');
	});
	if(i == 1){
		AlertaPersonalizada("Error","Debe seleccionar una presentacion para inventario");
	}
	return (n == 0 && i == 0) ? true : false;
}

function arrayTablaInsumosPresentacion() {
	var k = 0;
	var array_json1 = new Array();
	var tabla = $("#tablaPresentacionesInsumos tbody tr");
	tabla.each(function () {
		var inventario;
		var preInventario = $(this).find(".preInventario");
		if(preInventario.is(":checked")){
			inventario = 1;
		}else{inventario = 0;}
		var presentacion = $(this).find(".presentacion").val();
		var descripcion = $(this).find(".descripcion").val();
		var unidad = $(this).find(".unidad").val();
		var costo = $(this).find(".costo").val();
		var precio = $(this).find(".precio").val();

		var obj1 = new Object();
		obj1.inventario = inventario;
		obj1.presentacion = presentacion;
		obj1.descripcion = descripcion;
		obj1.unidad = unidad;
		obj1.costo = costo;
		obj1.precio = precio;

		var text1 = JSON.stringify(obj1);
		array_json1.push(text1);
		k = k + 1;
	});

	var json_arr1 = '[' + array_json1 + ']';

	return json_arr1;
}
var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var proceso = $("#proceso").val();
		var ruta = (proceso == "Agregar") ? "InsumosAgregar" : (proceso == "Editar") ? "InsumosEditar/" + $("#idInsumo").val() : "";
		if (validarTablaInsumosPresentacion() == true) {
			$("#valoresTabla").val(arrayTablaInsumosPresentacion());
			
			var FrmInsumos = $("#FrmInsumos");
			var Frm = false;
			if (window.FormData) { Frm = new FormData(FrmInsumos[0]); }

			$.ajax({
				type: 'POST',
				url: url + '/' + ruta,
				cache: false,
				data: Frm ? Frm : FrmInsumos.serialize(),
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function (respuesta) {
					//Codigo
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200) {
						reload();
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
		else {

			guardando = 0;
		}
	}
}
function reload() {
	setTimeout("location.href = url + '/' + padre;" ,1000);
}
