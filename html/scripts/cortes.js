var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Corte';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/CorteMostrar',token);

	// $.validator.setDefaults({
	// 	submitHandler: function(){
	// 		ClienteAgregarEditar();
	// 	}
	// });
	var formulario = "FrmCorte";
	var tipoFormulario = $('#avanzado').val();
	if(tipoFormulario=='true'){
		var reglas = {
			nombreCliente:{
				required: true,
			},
			direccionCliente:{
				required: true,
			},
			telefonoCliente:{
				required: true
			},
			emailCliente:{
				email: true,
				required:true
			},
			departamentoCliente:{
				required:true
			},
			municipioCliente:{
				required: true
			},
			duiCliente:{
				required:true
			},
			nitCliente:{
				required:true
			},
			nrcCliente:{
				required:true
			},
			referenciaCliente:{
				required:true
			},
			idCategoriaCliente:{
				required:true
			}
		};
		validarDatos(formulario,reglas);
	} else {
		var reglas = {
			nombreCliente:{
				required: true,
			},
			direccionCliente:{
				required: true,
			},
			telefonoCliente:{
				required: true
			},
			idCategoriaCliente:{
				required:true
			}
		};
		validarDatos(formulario,reglas);
	}
});


$(document).on("keyup",'#efectivo', function(evt){
	var total = parseFloat($("#totaltd").attr("total"));
	if(isNaN(total)){
		total = 0;
	}
	var efectivo = parseFloat($(this).val());
	if(isNaN(efectivo)){
		efectivo = 0;
	}
	var diferencia =  efectivo - total;
	$("#diferenciatd").attr("total",diferencia);
	$("#diferenciatd").text("$"+diferencia.toFixed(2));
});
var guardando = 0;
$(document).on("click",'#finalizar_corte', function(){
	if(!guardando){
		guardando =1;
		var idCorteCaja = $("#idCorteCaja").val();
		var efectivo = parseFloat($("#efectivo").val());
		var total = parseFloat($("#totaltd").attr("total"));
		var diferencia = parseFloat($("#diferenciatd").attr("total"));
		if(!isNaN(efectivo)){
			$.ajax({
				type: "POST",
				url: url + "/CorteAgregar",
				data: {
					'efectivo':efectivo,
					'diferencia':diferencia,
					'total':total
				},
				dataType: 'json',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						// setTimeout("reload();", 1500);
						imprimir_corte(efectivo,diferencia,total,idCorteCaja);
					} else {
						guardando = 0;
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
					guardando = 0;
				}
			});
		} else{
			guardando = 0;
			AlertaPersonalizada('error', "Por favor ingrese el efectivo en caja");
		}
	}
});
var imprimiendo = 0;
$(document).on("click",'#imprimir_corte', function(){
	if(!imprimiendo){
		imprimiendo =1;
		var idCorteCaja = $("#idCorteCaja").val();
		var efectivo = parseFloat($("#efectivo").val());
		var total = parseFloat($("#totaltd").attr("total"));
		var diferencia = parseFloat($("#diferenciatd").attr("total"));
		if(!isNaN(efectivo)){
			imprimir_corte(efectivo,diferencia,total,idCorteCaja);
		} else{
			imprimiendo = 0;
			AlertaPersonalizada('error', "Por favor ingrese el efectivo en caja");
		}
	}
});

var imprimiendos = 0;
$(document).on("click",'#imprimir_cortes', function(){
	if(!imprimiendos){
		imprimiendos =1;
		var idCorteCaja = $("#idCorteCaja").val();
			imprimir_cortes(idCorteCaja);
			var imprimiendos = 0;

	}
});
var imprimiendos2 = 0;
$(document).on("click",'#imprimir_cortes2', function(){
	if(!imprimiendos2){
		imprimiendos2 =1;
		var idCorteCaja = $("#idCorteCaja").val();
			imprimir_cortes2(idCorteCaja);
			var imprimiendos2 = 0;

	}
});
$(document).on("click",'#realizar_apertura', function(){
	realizar_apertura()
});
// $('#montoApertura').keyboard({
//     openOn : null,
//     stayOpen : true,
// 		layout : 'num',
// 		restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
// 		preventPaste : true,  // prevent ctrl-v and right click
// 		autoAccept : true
// 	});
// $('.apertura-opener').click(function(){
//   var kb = $('#montoApertura').getkeyboard();
//   // close the keyboard if the keyboard is visible and the button is clicked a second time
//   if (kb.isOpen) {
//     kb.close();
//   } else {
//     kb.reveal();
//   }
// });
function realizar_apertura() {
	monto = $("#monto").val();
	$.ajax({
		type: "POST",
		url: url + "/RealizarAperturar",
		data: {
			'monto':monto,
		},
		dataType: 'json',
		success: function (respuesta) {
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200){
				setTimeout("location.href = '"+url+"/Touch' ;", 1500);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
			guardando = 0;
		}
	});
}
function imprimir_corte(efectivo,diferencia,total,idCorteCaja){
	$.ajax({
		type: "POST",
		url: url+"/ImprimirCorte",
		data: {
			idCorteCaja: idCorteCaja,
			efectivo: efectivo,
			diferencia: diferencia,
			total: total
		},
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$.post("http://localhost/imprimir/printCorte.php", {
					datos: respuesta.datos,
				});
			setTimeout("reload();",1000);
			} else {
				Alerta(respuesta.codigo);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}
function imprimir_cortes(idCorteCaja){
	$.ajax({
		type: "POST",
		url: url+"/ImprimirCortes",
		data: {
			idCorteCaja: idCorteCaja,
		},
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$.post("http://localhost/imprimir/printCortes.php", {
					datos: respuesta.datos,
				});
			setTimeout("reload();",1000);
			} else {
				Alerta(respuesta.codigo);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}
function imprimir_cortes2(idCorteCaja){
	$.ajax({
		type: "POST",
		url: url+"/ImprimirCortes2",
		data: {
			idCorteCaja: idCorteCaja,
		},
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$.post("http://localhost/imprimir/printCortes.php", {
					datos: respuesta.datos,
				});
			setTimeout("reload();",1000);
			} else {
				Alerta(respuesta.codigo);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}
function reload() {
	location.href = url+"/"+padre;
}
