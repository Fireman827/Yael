var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'EmpleadosDescuento';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/EmpleadosDescuentoMostrar',token);
	
	var formulario = "FrmEmpleadosDescuento";
	var reglas = {
		empleadoEmpleadoDescuento:{
			required: true
		},
		tipoEmpleadoDescuento:{
			required:true
		},
		montoEmpleadoDescuento:{
			required:true
		},
        idPeriodoEmpleadoDescuento:{
			required:true
		},
		descripcionEmpleadoDescuento:{
			required:true
		}
	};
	validarDatos(formulario,reglas);

	HacerAutoCompletar('buscarEmpleado','nombreEmpleado','/EmpleadosDescuentoAutocompleteEmpleado',function (e, data) {
		$('#empleadoEmpleadoDescuento').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
		$('#idEmpleadoEmpleadoDescuento').val(data.idEmpleado);
	});
	//TYPEAHEAD
	// $("#buscarEmpleado").typeahead({
	// 	hint: false,
	// 	highlight: true,
	// 	minLength: 1,
	// }, {
	// 	name: "nombreEmpleado",
	// 	displayKey: "nombreEmpleado",
	// 	limit: 100,
	// 	templates: {
	// 		notFound: function (q) {
	// 			return '<div><p class="text-danger">No match found <strong>' + q.query + "</strong></p></div>";
	// 		},
	// 		suggestion: function (data) {
	// 			return ("<div class='text-light'>[" + data.idEmpleado + "]  (" + data.nombreEmpleado +" "+ data.apellidoEmpleado+ ")</div>");
	// 		},
	// 	},
	// 	async: true,
	// 	source: function (query, processSync, processAsync) {
	// 		//processSync(['Uno', 'Dos']);
	// 		return $.ajax({
	// 			url: url + '/EmpleadosDescuentoAutocompleteEmpleado',
	// 			type: "GET",
	// 			data: { 
	// 				'search': query,
	// 				'csrf_test_name':token
	// 			 },
	// 			dataType: "json",
	// 			success: function (json) {
	// 				return processAsync(json);
	// 			}
	// 		});
	// 	},
	// }).on("typeahead:selected", onAutocompleted);
});

function onAutocompleted(e, data) {
	$('#empleadoEmpleadoDescuento').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
	$('#idEmpleadoEmpleadoDescuento').val(data.idEmpleado);
}

$(document).on("click",".EmpleadosDescuentoCambiarEstado", function(event){
	event.preventDefault()
	var idEmpleadoDescuento = $(this).attr("idEmpleadoDescuento");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idEmpleadoDescuento=" + idEmpleadoDescuento+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea "+ accion+" este registro?!",
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
				url: url+"/EmpleadosDescuentoCambiarEstado",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						setTimeout("reload();", 1500);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".EmpleadosDescuentoEliminar", function(event){
	event.preventDefault()
	var idEmpleadoDescuento = $(this).attr("idEmpleadoDescuento");
	var dataString = "idEmpleadoDescuento=" + idEmpleadoDescuento + "&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro de eliminar este regitro?!",
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
				url: url+"/EmpleadosDescuentoEliminar",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						setTimeout("reload();", 1500);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		
		var FrmEmpleadosDescuento = $("#FrmEmpleadosDescuento");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmEmpleadosDescuento[0]);
		}
		if ($("#proceso").val() == "Editar"){
			ruta = "EmpleadosDescuentoEditar";			
		}
		if ($("#proceso").val() == "Agregar"){
			ruta = "EmpleadosDescuentoAgregar";
		}
			
		if(true){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				cache: false,
				data: Frm ? Frm : FrmEmpleadosDescuento.serialize(),
				dataType: 'json',
				contentType: false,
				processData: false,
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						setTimeout("reload();", 1500);
						guardando = 0;
					} else {
						guardando = 0;
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					guardando = 0;
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		} 
	}
}

function reload() {
	location.href = url+'/'+padre;
}

function refresh() {
	//Actualizamos la página
	location.reload();
};
