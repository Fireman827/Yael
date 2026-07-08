var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Ventas';
var tablaAdmin;
$(document).ready(function(){
	var formulario = "FrmventaDevolucion";
	var reglas = {
		idFactura:{
			required : true,
		},
	};
	validarDatos(formulario,reglas);
	$(".decimald").numeric({
    negative: false,
    decimalPlaces: 8
  });
});
// $(document).on("click",".AjusteinventarioCambiarEstado", function(event){
//
// });

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		var proceso = $("#proceso").val();
		var idFactura = $("#idFactura").val();
		var FrmventaDevolucion = $("#FrmventaDevolucion");
		var array_json = new Array();
		var i = 0;

		$("#infodata>tr").each(function(index) {
			var obj = new Object();
			if ($(this).find(".subtotalDevolucion").val() > 0  || $(this).find(".cantidadaDevolver").val() >0) {

			obj.idProducto = $(this).find('.idProducto').val();
			obj.cantidadaDevolver = $(this).find(".cantidadaDevolver").val();
			obj.idProductoPresentacion = $(this).find(".idProductoPresentacion").val();
			obj.idFacturaDetalle = $(this).find(".idFacturaDetalle").val();
			obj.ProductoPresentacion = $(this).find(".presentacionProducto").text();
			obj.unidadProductoPresentacion = $(this).find(".unidadProductoPresentacion").val();
			obj.costoUnitarioFacturaDetalle = $(this).find(".costoUnitarioFacturaDetalle").val();
			obj.precioIvaUnitarioFacturaDetalle = $(this).find(".precioIvaUnitarioFacturaDetalle").val();
			obj.precioUnitarioFacturaDetalle = $(this).find(".precioUnitarioFacturaDetalle").val();
			obj.descuento = $(this).find(".descuentoFacturaDetalle").val();
			obj.subtotal = $(this).find(".subtotalDevolucion").val();
			text = JSON.stringify(obj);
			array_json.push(text);
			i = i + 1;
			}else {

			}

		});
		json_arr = '[' + array_json + ']';

		$("#details").val(json_arr);
		var aliasDocumento = $('#idDocumento option:selected').attr('tipo');
		$('#aliasDocumento').val(aliasDocumento);
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmventaDevolucion[0]);
		}
		var ruta = (proceso == "Devolucion") ? "VentasDevolucion" : '';
		if (i>0) {

		$.ajax({
			type: 'POST',
			url: url+'/'+ruta+'/'+idFactura,
			// url: url+'/Ventas/'+ruta+'/'+idFactura,
			cache: false,
			data: Frm ? Frm : FrmventaDevolucion.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta){
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200){
					var idVenta = respuesta.idFactura;
					var aliasDocumento = respuesta.tipoImpresion;
						if(aliasDocumento == "NDC" || aliasDocumento == "NDD"){
								var ruta = "crearDTENota";
							}
						$.ajax({
							type: "POST",
							url: url+"/"+ruta+"/"+respuesta.idGuardado,
							dataType: 'json',
							success: function (data1){
								Alerta(data1.codigo);
									Swal.fire({
										title: 'Alerta!!',
										text: "Imprimir comprobante?!",
										icon: 'question',
										showCancelButton: true,
										confirmButtonColor: '#3085d6',
										cancelButtonColor: '#d33',
										confirmButtonText: 'Si, imprimir',
										cancelButtonText: 'Cancelar',
									}).then((result) => {
										if (result.isConfirmed){
											window.open(url+"/VentasPdf/"+respuesta.idGuardadom,'','');
											// imprimirDocumento(data.idGuardado,aliasDocumento);
											// setTimeout("reload();", 1500);
										} else {
											setTimeout("reload();", 1500);
										}
									});
							},
							error: function(XMLHttpRequest){
								AlertaPersonalizada('error', XMLHttpRequest.responseText);
							}
						});
				} else{
					guardando = 0;
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				guardando = 0;
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}else {
		guardando = 0;
		AlertaPersonalizada('error', 'No hay modicaciones ');
	}

	}
}

function reload() {
	location.href = url+'/'+padre;
}
$(document).on('keyup', '.cantidadaDevolver', function(event) {
	var cantidadaDevolver = parseInt($(this).val());
	if(isNaN(cantidadaDevolver)){	cantidadaDevolver=0;}
	var suma=0, devolucionanterior=0, cantidadvendidas=0, precioventa=0, subtotal=0;
	precio_venta = parseFloat($(this).closest('tr').find('.precioIvaUnitarioFacturaDetalle').val());
	cantidadvendidas = parseFloat($(this).closest('tr').find('.cantidadFacturaDetalle').val());
	devolucionanterior = parseFloat($(this).closest('tr').find('.cantidadDevolucion').val());
	if(isNaN(devolucionanterior)){devolucionanterior=0;}
	suma=devolucionanterior+cantidadaDevolver;
	if(suma>cantidadvendidas){
		cantidadaDevolver = cantidadvendidas-(suma-cantidadaDevolver);
		$(this).val(cantidadaDevolver);
	}
	totalesInsumo()
});

	function totalesInsumo(){

		var tipoImpresion = $("#idDocumento").attr('tipo');
		var descuentoPor = "Porcentaje";
		var totalGravado = bigDecimal.add(0,0);
		var totalDescuento = bigDecimal.add(0,0);
		var totalItem = 0;

		$("#infodata tr").each(function(index) {
			if (index >= 0) {
					var fila = $(this);
					var precio = $(this).find(".precioUnitarioFacturaDetalle").val();
					var precioIva = $(this).find(".precioIvaUnitarioFacturaDetalle").val();
					var precioVenta = $(this).find(".precioIvaUnitarioFacturaDetalle").val();
					var insumoCantidad = $(this).find('.cantidadaDevolver').val();
					var descuentoItem = $(this).find('.descuentoFacturaDetalle').val();
					var descuentoAplicar= bigDecimal.add(0,0);
					var descuentoAplicarSubt= bigDecimal.add(0,0);
					var subtot = bigDecimal.add(0,0);
					if (precioVenta != "") {
						precioCalculo = bigDecimal.round(precio,8);
						precioSubt = bigDecimal.round(precio,8);
						if(tipoImpresion == "FAC" || tipoImpresion == "TIK"){
							precioCalculo = bigDecimal.round(precioIva,8);
						}
						if (descuentoItem != "" && descuentoItem >0){
							descuentoAplicar = bigDecimal.round(descuentoItem,8);
							descuentoAplicarSubt = bigDecimal.round(descuentoItem,8);
							if (descuentoPor == "Porcentaje"){
								pordesc = bigDecimal.divide(descuentoItem,100);
								descuentoAplicar = bigDecimal.multiply(precioCalculo,pordesc);
								descuentoAplicarSubt = bigDecimal.multiply(precioSubt,pordesc);
							}
						}
						precioDesc = bigDecimal.subtract(precioCalculo,descuentoAplicar);
						subtot = bigDecimal.multiply(precioDesc,insumoCantidad);
						subtot = bigDecimal.round(subtot,8, bigDecimal.RoundingModes.HALF_UP);

						fila.find(".subtotalDevolucion").val(subtot);

						totalGravado = bigDecimal.add(totalGravado,subtot);
						descuentoFila = bigDecimal.multiply(descuentoAplicar,insumoCantidad);
						totalDescuento = bigDecimal.add(totalDescuento,descuentoFila);
					}
					totalItem += 1;

			}
		});

		totalGravado = bigDecimal.round(totalGravado,8, bigDecimal.RoundingModes.HALF_UP);
		if(tipoImpresion == "FAC" || tipoImpresion == "TIK"){
			totalGravado = bigDecimal.round(totalGravado,2,bigDecimal.RoundingModes.HALF_UP);
			totalGravado =  bigDecimal.divide(totalGravado, 1.13);
		}
		var totalIva = bigDecimal.multiply(totalGravado, 0.13);
		totalIva = bigDecimal.round(totalIva,2, bigDecimal.RoundingModes.HALF_UP);

		var totalRetencion = 0;
		if ($("#retieneIvaCliente").val()==1 && ($("#retencionFacturagen").val())>0) {
			totalRetencion =  bigDecimal.multiply(totalGravado, 0.01);
			totalRetencion = bigDecimal.round(totalRetencion,2, bigDecimal.RoundingModes.HALF_UP);
		}
		var totalFinal =  bigDecimal.add(totalGravado,totalIva);
		totalFinal = bigDecimal.round(totalFinal,2, bigDecimal.RoundingModes.HALF_UP);
		totalGravado = bigDecimal.round(totalGravado,2, bigDecimal.RoundingModes.HALF_UP);

		$(".totalRetencionDevolucion").text(totalRetencion);
		$(".totalSumasDevolucion").text(totalGravado);
		$(".totalIvaDevolucion").text(totalIva);
		$(".totalRetencionDevolucion").val(totalRetencion);
		$(".totalSumasDevolucion").val(totalGravado);
		$(".totalIvaDevolucion").val(totalIva);
		totalFinal =  bigDecimal.subtract(totalFinal,totalRetencion);
		totalFinal = bigDecimal.round(totalFinal,2, bigDecimal.RoundingModes.HALF_UP);
		totalDescuento = bigDecimal.round(totalDescuento,2, bigDecimal.RoundingModes.HALF_UP);
		$(".totalDescuentoDevolucion").val(totalDescuento);
		$(".totalDevolucion").val(totalFinal);
		$(".totalDevolucion").text(totalFinal);
	}



	$(document).on("change",'#idCaja', function(){
		var idCaja = $(this).val();
		if(idCaja!=0){
			$.ajax({
				type: "POST",
				url: url + "/"+padre+"/cajaDocumentoDevolucion",
				data: {
					'idCaja':idCaja
				},
				dataType: 'json',
				success: function (respuesta) {

					if(respuesta.documentos!=""){
						$('#idDocumento').html(respuesta.documentos);
						$("#idDocumento").trigger("change");


					}

				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
	$(document).on("change",'#idDocumento', function(){
		var idDocumento = $(this).val();
		// console.log(aliasDocumento);
		if(idDocumento!=0){
			$.ajax({
				type: "POST",
				url: url + "/"+padre+"/traerUltimoDocumento",
				data: {
					'idDocumento':idDocumento
				},
				dataType: 'json',
				success: function (respuesta) {
					if(respuesta.nDocumento!=""){
						$('#numeroDocumentoDev').val(respuesta.nDocumento);
						// var aliasDocumento = $(this).find('option:selected').attr('tipo');
						// $('#aliasDocumento').val(aliasDocumento);
					}

				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
