var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = "/Productos"
var tablaAdmin;
Dropzone.autoDiscover = false;

$(document).ready(function () {

	FormatoDatos();
	tablaAdmin = RenderizarTabla(url, '/ProductoMostrar', token);

	var formulario = "FrmProductoGeneral";
	var reglas = {
		nombreProducto: {
			required: true,
		},
		precioVentaProducto: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);  

	var idBuscador = "buscarInsumo";
	var nombre = "nombre";
	var urlBuscador = "/ProductoAutocompletarInsumo";
	//AutoCompletar(idBuscador,nombre,urlBuscador,AutocompletarModificadorTipo);
	HacerAutoCompletar(idBuscador, nombre, urlBuscador, function (e, datos) {
		$('#' + idBuscador).typeahead('val', '');
		if(datos.presetaciones != ""){
			var tr = "<tr idUnico='general' idModificador='0'>";
				td = "<td class='nombreTipo' idInsumo='"+datos.id+"'>" + datos.nombre + "</td>";
				td += "<td>" + datos.presentaciones + "</td>";
				td += "<td><input type='text' class='form-control decimal cantidad' value='1'></td>";
				td += '<td>	<input type="hidden" class="incluirInsumo" value="1"><a class="btn btn-danger btn-block borrarTr" role="button"><i class="fa fa-trash"></i></a></td>';
			tr += td + "</tr>";
																							
			$("#tablaInsumoGeneral tbody").prepend(tr);
			FormatoDatos();
		}
		else{
			AlertaPersonalizada("Error","Este Insumo no cuenta con presentaciones disponibles");
		}
	});


});
/*/ Control de Tabs (Inicio) /*/
$(document).on('click','.nav-link.pestana' , function (e) {
	e.preventDefault()
	$(this).tab('show')
  })
  $('a.nav-link.pestana').on('shown.bs.tab', function (e) {
	var nuevaTab = $(e.target) // newly activated tab
	var antiguaTab = $(e.relatedTarget) // previous active tab
	cambiarColorTabs(nuevaTab,antiguaTab);
  })
/*/ Control de Tabs (Final) /*/

function cambiarColorTabs(nuevaTab,antiguaTab){
	(antiguaTab.hasClass("bg-default")) ? (nuevaTab.toggleClass('bg-default') 	,antiguaTab.toggleClass('bg-default') ) :
	(antiguaTab.hasClass("bg-success")) ? (nuevaTab.toggleClass('bg-success') 	,antiguaTab.toggleClass('bg-success') ) :
	(antiguaTab.hasClass("bg-primary")) ? (nuevaTab.toggleClass('bg-primary') 	,antiguaTab.toggleClass('bg-primary') ) :
	(antiguaTab.hasClass("bg-info")) 	? (nuevaTab.toggleClass('bg-info') 		,antiguaTab.toggleClass('bg-info') ) 	:
	(antiguaTab.hasClass("bg-warning")) ? (nuevaTab.toggleClass('bg-warning') 	,antiguaTab.toggleClass('bg-warning') ) :
	(antiguaTab.hasClass("bg-danger")) 	? (nuevaTab.toggleClass('bg-danger') 	,antiguaTab.toggleClass('bg-danger') ) 	: '';
}

//Agrega en la vista un registro nuevo en las tablas de Producto
$(document).on("click", ".agregarTr", function (event) {
	var ruta = $(this).attr("ruta");
	var tablaId = $(this).parents('table').attr("id");
	$.ajax({
		type: "POST",
		url: url + "/" + ruta,
		data: '',
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$("#"+tablaId+" tbody ").not(".listaDetalleModificadores").append(respuesta.tr);
				FormatoDatos();
			}
			if (respuesta.codigo == 500) {
				AlertaPersonalizada("error", "Hubo un problema al agregar el Modificador");
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', errorThrown, textStatus, XMLHttpRequest.responseText);
		}
	});
});

//Remueve una fila de una tabla autogenerada
$(document).on("click", ".borrarTr", function (event) {
	if($(this).hasClass('btnBorrarInsumo')){
		$("#tablaInsumosReceta tbody tr[idUnico='"+$(this).attr("idUnico")+"']").remove();
	}
	if($(this).hasClass('btnBorrarModificador')){
		$("div.tabs-recetas a[idUnicoAbuelo='"+$(this).attr("idUnicoSelect")+"']").each(function () { 
			var idUnicoHijo = $(this).attr("idUnico");
			$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnicoHijo+"']").remove();
		 });
		$("div.tabs-recetas a[idUnicoAbuelo='"+$(this).attr("idUnicoSelect")+"']").remove();
	}
	$(this).parents("tr").remove();
});

//remueve un Tipo de Modificador en la Modal Agregar/Editar Producto
$(document).on("click", ".borrarComoModificador", function (event) {
	$(this).parents("tr").remove();
});
//Valida Select de Modificadores de Producto, si un Modificador es Unico no le permite agregar mas de 1
$(document).on("change", ".productoModificador", function (event) {
	var elemento = $(this);
	var varios = $("option:selected", $(this)).attr("varios");
	var valor = $(this).parent().next("div").children(".cantidadModificador");
	// if (varios == 0) {
	// 	valor.val("1").attr("readonly", true);
	// }
	// else {
	 	valor.attr("readonly", false);
	// }

	var idModificador = $(this).val();
	var idUnico = $("option:selected", $(this)).attr("idUnico");
	var idUnicoSelect = $("option:selected", $(this)).attr("idUnicoSelect");
	var nombre = $("option:selected", $(this)).attr("nombre");
	var varios = $("option:selected", $(this)).attr("varios");

	var row = elemento.parents("div.row");
	var nrow = row.next("div.row");
	var contenedor = nrow.find("div.detalleModificadoresTipo");

	$("div.tabs-recetas a[idUnicoAbuelo='"+idUnicoSelect+"']").each(function () { 
		var idUnicoHijo = $(this).attr("idUnico");
		$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnicoHijo+"']").remove();
	 });
	$("div.tabs-recetas a[idUnicoAbuelo='"+idUnicoSelect+"']").remove();
	$.ajax({
		type: "POST",
		url: url + "/ProductoListarModificadorDetalle",
		data: 'idModificadorTipo=' + idModificador +"&idUnico=" + idUnico +"&nombre=" + nombre +"&varios=" + varios +"&idUnicoSelect=" + idUnicoSelect,
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				contenedor.empty().append(respuesta.tabla);

				// $("div.tabs-recetas a[idUnicoPadre='"+idUnico+"']").each(function () { 
				// 	var idUnicoHijo = $(this).attr("idUnico");
				// 	$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnicoHijo+"']").remove();
				//  });
				//  $("div.tabs-recetas a[idUnicoPadre='"+idUnico+"']").empty();
				FormatoDatos();
			}
			if (respuesta.codigo == 500) {
				contenedor.empty();
				AlertaPersonalizada("advertencia", "Este Modificador no cuenta con opciones para Receta");
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', errorThrown, textStatus, XMLHttpRequest.responseText);
		}
	});

});
//llama todos los modificadores de una categoria especifica
$(document).on("click",".modificadorDetalle",function (e) { 
	pasarParaReceta($(this));
});

//Agrega a la Receta los insumos generales que se agreguen
$(document).on("change",".productoInsumoGeneral",function(){			
	var idInsumo = $(this).val();
	var nombreInsumo = $("option:selected", $(this)).attr("nombre");
	var idUnico = $("option:selected", $(this)).attr("idUnico");
	var tr = "";
	tr += '<tr idUnico="'+idUnico+'">';
	
	var td1 = '	<td>';
	td1 += '	<label idUnico="'+idUnico+'" idInsumo="'+ idInsumo +'" nombreInsumo="'+ nombreInsumo +'" >'+ nombreInsumo +'</label>';
	td1 += '	</td>';
	
	var td3 = '	<td>';
	td3 += '	<input type="text" class="form-control decimal cantidadInsumo" placeholder="0000" value="1">';
	td3 += '	</td>';
	
	$.ajax({
		type: "POST",
		url: url + "/ProductoListarInsumosPresentacion",
		data: 'idInsumo=' + idInsumo ,
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				var select = respuesta.select;
				var td2 = "<td>"+ select +"</td>"

				tr += td1 + td2 +td3;
				tr += '</tr>';
				$("#tablaInsumoGeneral tbody").append(tr);
				FormatoDatos();
			}
			if (respuesta.codigo == 500) {
				$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnico+"']").remove();
				AlertaPersonalizada("advertencia", "Este Insumo no cuenta con Presentaciones para Receta");
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', errorThrown, textStatus, XMLHttpRequest.responseText);
		}
	});

});

//Inserción de los Modificadores
$(document).on("click","#insertarModificadores",function (e) { 
	insertarModificadores();
});

$(document).on("click",".tabInsumoMod",function (e) { 
	var idUnico = $(this).attr("idUnico");
	if($(this).hasClass("active")){
		$("#tablaInsumoGeneral tbody tr").removeClass("bg-success");
		$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnico+"']").addClass("bg-success");
	}
	else{
		$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnico+"']").removeClass("bg-success");
	}
});
//Inserción de la Receta General
$(document).on("click","#insertarRecetaGeneral",function (e) { 
	insertarRecetaGeneral();
});

$(document).on("click",".ProductoCambiarEstado", function(event){
	event.preventDefault()
	var idSenorita = $(this).attr("idProducto");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idProducto=" + idSenorita+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea "+ accion+" este registro?!",
		type: 'warning',
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, '+accion,
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/ProductoCambiarEstado",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						//setTimeout("reload();", 1500);
						tablaAdmin.ajax.reload(null,false);
					}
				},
				error: function(XMLHttpRequest){
					AlertaPersonalizada("error",XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".ProductoEliminar", function(event){
	event.preventDefault()
	var idProducto = $(this).attr("idProducto");
	var dataString = "idProducto=" + idProducto+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro de eliminar este regitro?!",
		type: 'error',
		icon: 'question',
		target:'#page-top',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Eliminar',
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/ProductoEliminar",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						//setTimeout("reload();", 1500);
						tablaAdmin.ajax.reload(null,false);
					}
				},
				error: function(XMLHttpRequest){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".checkIncluir",function(){
	if($(this).is(":checked")){
		$(this).parent().parent().find(".incluirInsumo").val("1");
	}
	else{
		$(this).parent().parent().find(".incluirInsumo").val("0");
	}
});

//Pasa los modificadores que vienen de un producto y tienen receta a la pestaña de receta al marcarlos
// y los quita al desmarcarlos en los Modificadores del producto
function pasarParaReceta(elemento){
		var elementoTab = '';
			var nombre = elemento.attr("nombre");
			var nombrePadre = elemento.attr("nombrePadre");
			var idModificador = elemento.attr("id");
			var idMofificadorTipo = elemento.attr("tipo");
			var varios = elemento.attr("varios");
			var idUnico = elemento.attr("idUnico");//id unico del modificador
			var idUnicoPadre = elemento.attr("idUnicoPadre");//id unico de la categoria de modificador
			var idUnicoAbuelo = elemento.attr("idUnicoAbuelo");//id unico del select con los modificadores
			
		if (elemento.is(':checked')) {
			$.ajax({
				type: "POST",
				url: url + "/ProductoVerificarInsumosModificador",
				data: 'idModificador=' + idModificador + '&idUnico=' +idUnico,
				dataType: 'json',
				success: function (respuesta) {
					if (respuesta.codigo == 200) {
						if(varios == 1){
							elementoTab += '<a class="nav-link tabInsumoMod" idUnicoAbuelo="'+idUnicoAbuelo+'" idUnicoPadre="'+idUnicoPadre+'" idUnico="'+idUnico+'" idMod="'+ idModificador +'" idTipo="'+ idMofificadorTipo +'" id="tab'+idModificador+'" data-toggle="pill" href="#v-pills-general" role="tab" aria-controls="v-pills-general" >'+nombrePadre+' ('+nombre+') </a>';
							$("div.tabs-recetas a[idUnico='"+idUnico+"']").remove();
							$("div.tabs-recetas").append(elementoTab);
							// $("#tablaModificadoresInsumosReceta tbody").append(respuesta.insumos);
							$("#tablaInsumoGeneral tbody").append(respuesta.insumos);
						}
						FormatoDatos();
					}
					if (respuesta.codigo == 500) {
						$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnico+"']").remove();
						
						AlertaPersonalizada("advertencia", "Este Insumo no cuenta con Presentaciones para Receta");
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', errorThrown, textStatus, XMLHttpRequest.responseText);
				}
			});
		}
		else{
			$("div.tabs-recetas a[idUnico='"+idUnico+"']").replaceWith(elementoTab);
			$("#tablaInsumoGeneral tbody tr[idUnico='"+idUnico+"']").remove();

		}
}

//Funcion para hacer un array de Modificadores
function extraerDatosModificadores() {
	var arrayModificador = [];
	$('#tablaModificadores tbody tr').each(function () {
		var select = $(this).find('select.productoModificador');
		if(select.val() != ""){
			var arrayModificadorDetalle = [];
			var rowDetalle = $(this).find(".grupoDetalleModificador");
			rowDetalle.each(function(){
				var check = $(this).find('.modificadorDetalle');
				if(check.is(':checked')){
					var modificadord = {
						id : 				check.attr("id"),
						idUnico : 			check.attr("idUnico"),		
						idUnicoPadre : 		check.attr("idUnicoPadre"),
						idUnicoAbuelo : 	check.attr("idUnicoAbuelo"),
						tipoModificador : 	check.attr("tipo"),
						varios : 			check.attr("varios"),
						nombre : 			check.attr("nombre"),
						nombrePadre : 		check.attr("nombrePadre"),
						aumento : 	$(this).find('.aumentoModificadorDetalle').val(),
					}
					arrayModificadorDetalle.push(modificadord);
				}
			});
			var modificador = {
				cantidad : $(this).find('.cantidadModificador').val(),
				idModTipo : select.val(),
				idUnico : $("option:selected", select).attr("idUnico"),
				idUnicoSelect : $("option:selected", select).attr("idUnicoSelect"),
				varios : $("option:selected", select).attr("varios"),
				multiSeleccion : $(this).find('.multiSeleccionModificador').is(':checked') ? 1 : 0,
				nombre : $("option:selected", select).attr("nombre"),
				detalle : arrayModificadorDetalle,
			}
			arrayModificador.push(modificador);
		}
	});
	return arrayModificador;
}

//Funcion para hacer un array de Categorias de Producto
function extraerDatosCategoria() {
	var arrayCategoria = [];
	$('#tablaCategoriaProducto tbody tr').each(function () {
		var id = $(this).children('td').children('.categoriaProducto').val();
		var categoria = [id];
		arrayCategoria.push(categoria);
	});
	return arrayCategoria;
}
//Funcion para hacer un array de los Insumos Generales
function extraerDatosInsumoGeneral() {
	var arrayInsumoGen = [];
	var proceso = $("#proceso").val();
	$('#tablaInsumoGeneral tbody tr').each(function () {
		var incluir = $(this).find('.incluirInsumo');

		var idInsumo = $(this).find('.nombreTipo').attr("idInsumo");
		var idPresentacion = $(this).find('.presentacion').val();
		var cantidad = $(this).find('input.cantidad').val();
		var idMod = $(this).attr('idModificador');
		var insumoGen = {
			idMod:idMod,
			idInsumo:idInsumo,
			idPresentacion: idPresentacion,
			cantidad:cantidad
		}
		if(proceso == "Agregar" )	{
			arrayInsumoGen.push(insumoGen);
		}	
		if(proceso == "Editar"  &&  incluir.val() == "1"){
			arrayInsumoGen.push(insumoGen);
		}
		//var insumoGen = [idInsumo,idUnico, idInsumoPresentacion,cantidad];
	});
	return arrayInsumoGen;
}
//Funcion para hacer un array de los Insumos por Modificador
function extraerDatosInsumoModificador() {
	var arrayInsumoGen = [];
	$('#tablaModificadoresInsumosReceta tbody tr').each(function () {
		var idInsumo = $(this).find('.nombreTipo').attr("idInsumo");
		var idPresentacion = $(this).find('.presentacion').val();
		var cantidad = $(this).find('input.cantidad').val();
		var insumoGen = {
			idInsumo:idInsumo,
			idPresentacion: idPresentacion,
			cantidad:cantidad
		}
		//var insumoGen = [idInsumo,idUnico, idInsumoPresentacion,cantidad];
		arrayInsumoGen.push(insumoGen);
	});
	return arrayInsumoGen;
}
//Funcion para hacer un array de Tipos de Modificador que será el producto
function extraerDatosComoModificadores() {
	var arrayComoModificador = [];
	$('#tablaComoModificador tbody tr').each(function () {
		var precio = 0;
		var id = $(this).find('.modificadorTipo').val();
		var nombre = $("#nombreProducto").val();
		// var nombre = $(this).children('td.nombreTipo').text();
		// var id = $(this).children('td').children('.precioComoModificador').attr("idTipo");
		// var precio = $(this).children('td').children('.precioComoModificador').val();

		var comoModificador = [id, nombre, precio];
		arrayComoModificador.push(comoModificador);
	});
	return arrayComoModificador;
}
//FUncion insertar / editar la receta del producto
function insertarRecetaGeneral(){
	var proceso = $("#proceso").val();
	var idProducto = $("#idProducto").val();
	var tablaInsumoGeneral = extraerDatosInsumoGeneral();
	var datos = {
		"idProducto" : idProducto,
		"tablaInsumoGeneral" : JSON.stringify(tablaInsumoGeneral),
	}
	var ruta = (proceso == "Agregar") ? "ProductoAgregarInsumoGeneral" : (proceso == "Editar") ? "ProductoEditarInsumoGeneral/" + idProducto : '';
	if(tablaInsumoGeneral.length != 0){
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			data: datos,
			dataType: 'json',
			success: function (respuesta) {
				Alerta(respuesta.codigo);
				// if (respuesta.codigo == 200) {
				// 	$(".tab-modificadores").removeClass("disabled");
				// } 
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
	else{
		AlertaPersonalizada("Error","Asigne insumos al producto");
	}
}
//Funcion para insertar / editar los modificadores de un producto y su detalle
function insertarModificadores(){
	var proceso = $("#proceso").val();
	var idProducto = $("#idProducto").val();
	var tablaModificador = extraerDatosModificadores();
	//var tablaModificadorDetalle = extraerDatosModificadoresDetalle();
	var datos = {
		"idProducto" : idProducto,
		"tablaModificador" : JSON.stringify(tablaModificador),
	}
	var ruta = (proceso == "Agregar") ? "ProductoAgregarModificador" : (proceso == "Editar") ? "ProductoEditarModificador/"+idProducto : '';
	$.ajax({
		type: 'POST',
		url: url + '/' + ruta,
		data: datos,
		dataType: 'json',
		success: function (respuesta) {
			//Codigo
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200) {
				$("#idProducto ").val(respuesta.idProducto);
				$(".tab-modificadores").removeClass("disabled");
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
//Funcion para insertar / editar los datos generales del sistema 
var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var tablaModificadores = extraerDatosModificadores();
		var tablaCategoria = extraerDatosCategoria();
		var tablaComoModificador = extraerDatosComoModificadores();
		$("#categoriasProducto").val(JSON.stringify(tablaCategoria));
		$("#comoModificadoresProducto").val(JSON.stringify(tablaComoModificador));
		
		var FrmProductos = $("#FrmProductoGeneral");
		if (window.FormData){
			Frm = new FormData(FrmProductos[0]);
		}
		Frm ? Frm : FrmProductos.serialize()
		var idProducto = $("#idProducto").val();
		var proceso = $("#proceso").val();
		// var nombreProducto = $("#nombreProducto").val();
		// var descripcionProducto = $("#descripcionProducto").val();
		// var precioVentaProducto = $("#precioVentaProducto").val();
		// var precioEspecialProducto = $("#precioEspecialProducto").val();
		// var precioEmpleadoProducto = $("#precioEmpleadoProducto").val();
		// var impresoraProducto = $("#impresoraProducto").val();
		var tablaModificadores = extraerDatosModificadores();
		var tablaCategoria = extraerDatosCategoria();
		var tablaComoModificador = extraerDatosComoModificadores();
	
		// var formData = new FormData();
        // var files = $('#imagenProducto')[0].files[0];
        // formData.append('file',files);
        // formData.append("nombreProducto", $("#nombreProducto").val());
        // formData.append('descripcionProducto',$("#descripcionProducto").val());
        // formData.append('precioProducto',$("#precioVentaProducto").val());
        // formData.append('precioEspecial',$("#precioEspecialProducto").val());
        // formData.append('precioEmpleado',$("#precioEmpleadoProducto").val());
        // formData.append('impresora',$("#impresoraProducto").val());
        // formData.append('categoriasProducto',JSON.stringify(tablaCategoria));
        // formData.append('comoModificadoresProducto',JSON.stringify(tablaComoModificador));
		//var datos = "nombre="+nombreProducto+"&categoria="+categoriaProducto+"&precio="+precioVentaProducto+"&modificadores="+JSON.stringify(tablaModificadores)+"&comoModificadores="+JSON.stringify(tablaComoModificador);
		// var datos = {
		// 	"nombreProducto": nombreProducto,
		// 	"descripcionProducto": descripcionProducto,
		// 	"precioProducto": precioVentaProducto,
		// 	"precioEspecial": precioEspecialProducto,
		// 	"precioEmpleado": precioEmpleadoProducto,
		// 	"impresora": impresoraProducto,
		// 	"categoriasProducto": JSON.stringify(tablaCategoria),
		// 	"comoModificadoresProducto": JSON.stringify(tablaComoModificador)
		// };
		// if(proceso == "Editar"){
		// 	formData.append("idProducto", $("#idProducto").val());
		// 	//datos.idProducto = idProducto
		// }

		var ruta = (proceso == "Agregar") ? "ProductoAgregar" : (proceso == "Editar") ? "ProductoEditar/"+idProducto : '';
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmProductos.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					$("#idProducto ").val(respuesta.idProducto);
					$(".tab-modificadores,.tab-receta").removeClass("disabled");
					$(".guardarProducto").attr("hidden",true);
					$(".salirProducto").attr("hidden",false);
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
$(document).on("click",".salirProducto",function(){
	reload();
});
$(document).on("click",".salirModificadores",function(){
	reload();
});

function reload() {
	location.href = url + padre;
}
