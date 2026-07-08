var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Cajas';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/CajasMostrar',token);
	
	var formulario = "FrmCaja";
	var reglas = {
		nombreCaja:{
			required: true
		},
		idDocumento:{
			required:true
		},
		inicio:{
			required:true
		},
		final:{
			required:true
		},
		actual:{
			required:true
		},
		fechaAutorizacion:{
			required:true
		},
		fechaResolucion:{
			required:true
		},
		numeroResolucion:{
			required:true
		},
		serie:{
			required:true
		}
	};
	validarDatos(formulario,reglas);
});

$(document).on("click", ".agregarCajaDocumento", function() {
	var string_tr = "";
	$.ajax({
		type: "POST",
		url: url + "/Cajas/CajaDocumento",
		data: {
			'id':0
		},
		dataType: 'json',
		success: function (respuesta) {					
			if(respuesta.tiposDocumentos!=""){
				string_tr += "<tr>";
				string_tr += "<td><select class='select2 idDocumento' name='idDocumento' >"+respuesta.tiposDocumentos+"</select></td>";
				string_tr += "<td><input type='text' class='form-control inicio numeric' name='inicio' placeholder='Inicio' ></td>";
				string_tr += "<td><input type='text' class='form-control final numeric' name='final' placeholder='Final' ></td>";
				string_tr += "<td><input type='text' class='form-control actual numeric' name='actual' placeholder='Actual' ></td>";
				string_tr += "<td><input type='date' class='form-control fechaAutorizacion' name='fechaAutorizacion' placeholder='Fecha de autorización' ></td>";
				string_tr += "<td><input type='date' class='form-control fechaResolucion' name='fechaResolucion' placeholder='Fecha de resolución' ></td>";
				string_tr += "<td><input type='text' class='form-control numeroResolucion ' name='numeroResolucion' placeholder='Número de resolución' ></td>";
				string_tr += "<td><input type='text' class='form-control serie ' name='serie' placeholder='Serie' ></td>";
				string_tr += "<td><button class='btn btn-block btn-danger CajaDocumentoBorrar' idCajaDocumento='' type='button'><i class='fa fa-trash'></i></button></td>";
				string_tr += "</tr>";

				$("#tablaCajaDocumento tbody").append(string_tr);
				FormatoDatos();
			}					
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
});

$(document).on("click",".CajaDocumentoBorrar",function(event){
	event.preventDefault();
	var idCajaDocumento = $(this).attr('idCajaDocumento');
	var dataString = "idCajaDocumento=" + idCajaDocumento + "&csrf_test_name=" + $("#csrf_token_id").val();
	if (idCajaDocumento != ''){
		$.ajax({
			type: "POST",
			url: url + "/CajasDocumentoEliminar",
			data: dataString,
			dataType: 'json',
			success: function (respuesta) {
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					setTimeout("refresh();", 1500);
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	} else {
		$(this).parents('tr').remove();
	}
});

function extraerDatos(){
	var arrayCajaDocumento = [];
	$('#tablaCajaDocumento tbody tr').each(function(){
		var id = $(this).children('td').children('.CajaDocumentoBorrar').attr('idCajaDocumento');
		var documento = $(this).children('td').children('.idDocumento').val();	
		var inicio = $(this).children('td').children('.inicio').val();	
		var final = $(this).children('td').children('.final').val();	
		var actual = $(this).children('td').children('.actual').val();	
		var fechaAutorizacion = $(this).children('td').children('.fechaAutorizacion').val();	
		var fechaResolucion = $(this).children('td').children('.fechaResolucion').val();	
		var numeroResolucion = $(this).children('td').children('.numeroResolucion').val();	
		var serie = $(this).children('td').children('.serie').val();	

		var cajaDocumento = [id,documento,inicio,final,actual,fechaAutorizacion,fechaResolucion,numeroResolucion,serie];
		arrayCajaDocumento.push(cajaDocumento);
	});
	return arrayCajaDocumento;
}

$(document).on("click",".CajasCambiarEstado", function(event){
	event.preventDefault()
	var idCaja = $(this).attr("idCaja");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idCaja=" + idCaja+"&csrf_test_name="+token;
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
				url: url+"/CajasCambiarEstado",
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

$(document).on("click",".CajaDocumentoCambiarEstado", function(event){
	event.preventDefault()
	var idCajaDocumento = $(this).attr("idCajaDocumento");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idCajaDocumento=" + idCajaDocumento+"&csrf_test_name="+token;
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
				url: url+"/CajasDocumentoCambiarEstado",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						setTimeout("refresh();", 1500);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".CajasEliminar", function(event){
	event.preventDefault()
	var idCaja = $(this).attr("idCaja");
	var dataString = "idCaja=" + idCaja + "&csrf_test_name="+token;
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
				url: url+"/CajasEliminar",
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
		var cajaDocumentos = extraerDatos();
		var cajaDocumentosJSON = JSON.stringify(cajaDocumentos);
		var id = $('#idCaja').val();
		var nombre = $('#nombreCaja').val();
		var impresora = $('#impresoraCaja').val();

		var dataString = {
			'idCaja': id,
			'nombreCaja': nombre,
			'impresoraCaja': impresora,
			'datosCajaDocumento': cajaDocumentosJSON
		};
		
		var FrmCajaes = $("#FrmCaja");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmCajaes[0]);
		}
		if ($("#proceso").val() == "Editar"){
			ruta = "CajasEditar";			
		}
		if ($("#proceso").val() == "Agregar"){
			ruta = "CajasAgregar";
		}
		//Frm ? Frm : FrmCajaes.serialize()		
		if(cajaDocumentos.length!=0){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				data: dataString,				
				dataType: 'json',
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
		} else {
			guardando = 0;
			AlertaPersonalizada('error','Debes ingresar almenos un contacto');
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

$(document).on("click", ".CajaDocumentoEditar", function () {
	var idCajaDocumento = $(this).attr('idCajaDocumento');
	$(this).parents('tr').remove();
	if(idCajaDocumento!=''){
		$.ajax({
			type: 'POST',
			url: url+'/CajasDocumentoEditar',
			data: { idCajaDocumento: idCajaDocumento},				
			dataType: 'json',
			success: function (respuesta){
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200){					
					$("#tablaCajaDocumento tbody").append(respuesta.cajaDocumento);
					FormatoDatos();					
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){				
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
});

/** Control general de MODALES - Final*/
