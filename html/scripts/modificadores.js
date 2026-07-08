var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Modificadores';
var tablaAdmin;
$(document).ready(function () {
	FormatoDatos();
	tablaAdmin = RenderizarTabla(url, '/ModificadoresMostrar', token);
	var formulario = "FrmModificadores";
	var reglas = {
		nombreTipo: {
			required: true,
		},
		tamanio: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	OpcionesDiv();

	HacerAutoCompletar('buscarProducto','nombreProducto','/ModificadoresAutocomplete',function (e, data) {
		$('#buscarProducto').typeahead('val', '');
		var idModificador = data.idModificador;
		var idProducto = data.idProducto;
		var nombre = data.nombreProducto;
		var precio = data.precioVentaProducto;

		$("#nombreProducto").val(nombre);
		//$("#nombreModificador").val(nombre);
		$("#idProducto").val(idProducto);
		$("#precioModificador").val(precio);
		// if(idModificador == null){
		// }
		// else{
		// 	AlertaPersonalizada("Error","Ya existe un modificador Relacionado a este Producto");
		// }
		
		});
});

$(document).on("change","#forma",function(event){
	OpcionesDiv();
});

$(document).on("change","#categoriaProducto,#tipoCategoria",function(event){
	var categoriaP = $("#categoriaProducto").val();
	var categoriaM = $("#tipoCategoria").val();
	var data = {
		"categoriaP" : categoriaP,
		"categoriaM" : categoriaM,
	}
	$.ajax({
		type: "POST",
		url: url + "/ModificadoresTraerProductoCategoria",
		data: data,
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$("#tablaProducto tbody").html(respuesta.tbody);
				FormatoDatos();
			}
			else{
				$("#tablaProducto tbody").html("");
			}
		},
		error: function (XMLHttpRequest) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
});

$(document).on("click", ".ModificadoresCambiarEstado", function (event) {
	event.preventDefault()
	var idModificador = $(this).attr("idModificador");
	var accion = $(this).data("accion").toLowerCase();
	var data = "idModificador=" + idModificador + "&csrf_test_name=" + token;
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
				url: url + "/ModificadoresCambiarEstado",
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

$(document).on("click", ".ModificadoresEliminar", function (event) {
	event.preventDefault()
	var idModificador = $(this).attr("idModificador");
	var data = "idModificador=" + idModificador + "&csrf_test_name=" + token;
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
				url: url + "/ModificadoresEliminar",
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
$(document).on("change","#relacionarModificador",function () { 
	if($(this).val() == "No"){
		$("#divBuscadorEditar").attr("hidden",true);
		$("#divProductoEditar").attr("hidden",true);
	}else{
		$("#divBuscadorEditar").attr("hidden",false);
		$("#divProductoEditar").attr("hidden",false);
	}
});

function OpcionesDiv() {
	var tipo = $("#forma").val();
	if (tipo == "categoria") {
		$("#categoriaProductoDiv").prop("hidden", false);
		$("#productoDiv").prop("hidden", true);
		
		$("#categoria").prop("hidden", false);
		$("#producto").prop("hidden", true);
		$("#otro").prop("hidden", true);
	}
	if (tipo == "producto") {
		$("#productoDiv").prop("hidden", false);
		$("#categoriaProductoDiv").prop("hidden", true);
		
		$("#producto").prop("hidden", false);
		$("#categoria").prop("hidden", true);
		$("#otro").prop("hidden", true);
	}
	if (tipo == "otro") {
		$("#productoDiv").prop("hidden", true);
		$("#categoriaProductoDiv").prop("hidden", true);
		
		$("#otro").prop("hidden", false);
		$("#producto").prop("hidden", true);
		$("#categoria").prop("hidden", true);
	}
	//$(".tipo").addClass("select2");
}

var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var proceso = $("#proceso").val();
		var data = '';

		var forma = $("#forma").val();
		if(forma == "otro"){
			data = {
				"forma" : forma,
				"tipo" : $("#tipoOtro").val(),
				"nombre" : $("#nombre").val(),
			}
		}
		if( forma == "producto"){
			data = {
				"forma" : forma,
				"tipo" :$("#tipoProducto").val(),
				"nombre" :$("#nombreModificador").val(),
				"idProducto" :$("#idProducto").val(),
			} 
		}
		if( forma == "categoria"){
			var array = [];
			$("#tablaProducto tbody tr").each(function(){
				var tipo = $("#tipoCategoria").val();
				var usar = $(this).find(".usar");
				if( $(usar).is(':checked') ) {
					var idProd = $(this).find(".idProd").val();
					var nombreMod = $(this).find(".nombreMod").val();
					array.push([tipo,idProd,nombreMod]);
				}
			});
			data = {
				"forma" : forma,
				"datos" : JSON.stringify(array),
			}
		}
		if(proceso == "Editar"){
			data = {
				"relacionar" :$("#relacionarModificador").val(),
				"tipo" :$("#tipoModificador").val(),
				"nombre" :$("#nombreModificador").val(),
				"idProducto" :$("#idProducto").val(),
				"idModificador" :$("#idModificador").val(),
			} 
		}
		
		var ruta = (proceso == "Agregar") ? "ModificadoresAgregar" : (proceso == "Editar") ? "ModificadoresEditar" : '';
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			data: data,
			dataType: 'json',
			success: function (respuesta) {
				//funcion de notificaciones, 4 parametros, tipo, titulo, subtitulo, mensaje
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					setTimeout("reload();", 1500);
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
