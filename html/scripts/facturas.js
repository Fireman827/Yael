var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Facturas';
var tablaAdmin;
$(document).ready(function () {
	tablaAdmin = RenderizarTabla(url, '/FacturasMostrar', token);
});

/** Control general de MODALES - Inicio*/
$(document).on("click",".authdescpo-opener",function(){
  var kb = $('#authdescpo').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click",".motivacc-opener",function(){
  var kb = $('#motivacc').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click", "#facturaAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/FacturasAgregar', function () {
		FormatoDatos();
	});
});

$(document).on("click", ".FacturasVer", function () {
	$("#lgModal").modal("show");
	var id = $(this).attr('idFactura');
	$("#lgModal .modal-content").load(url + '/FacturasVer/'+id, function () {
		FormatoDatos();
	});
});

$(document).on("click", ".FacturasEditar", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idFactura');
	$("#smModal .modal-content").load(url + '/FacturasEditar/'+id, function () {
		FormatoDatos();
	});
});
function imprimirFacturaProducto(idFactura,vuelto="",efectivo="") {
	datos = {};
	if(efectivo != "" || vuelto !=""){
		datos = {
			vuelto : vuelto,
			efectivo : efectivo,
		}
	}
	$.ajax({
		type: "POST",
		url: url + "/ImprimirFacturaProducto/" + idFactura,
		data: datos,
		dataType: 'json',
		success: function (respuesta) {
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200) {
				setTimeout(function(){
					$.post("http://"+respuesta.servidor+"/imprimir/printFactura.php", {
					//$.post("http://localhost/imprimir/printProducto.php", {
						datos: respuesta.datos,
						tipo: respuesta.tipo,
						recurso: respuesta.recurso,
						ip: respuesta.ip,
					});
				},1500);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});

}

$(document).on("click", ".FacturasReimprimir", function () {
	var idFactura = $(this).attr("idFactura");
	var idFactura1 = $(this).attr("idFactura1");
    var accion = $(this).data("accion");
    var pdf = $(this).attr("pdf");
	if(pdf == "0"){
		if(accion == 'Servicio'){
			imprimirTicketSenorita(idFactura);
		} else {
			imprimirTicketProducto(idFactura);
		}
	} else {
		imprimirFacturaProducto(idFactura1);
		// imprimirFactura(idFactura1);
	}
});

$(document).on('shown.bs.modal', function (e) {

});

$(document).on('hidden.bs.modal', function (e) {
	var target = $(e.target);
	target.removeData('bs.modal').find(".modal-content").html('');
});

/** Control general de MODALES - Final*/

//click de Guardar en Agregar Factura
$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmFacturas";
	var reglas = {
		nombreFactura: {
			required: true,
		},
		recursoCompartidoFactura: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmFacturas').submit();
	//$("#smModal").modal("toggle");
});


$(document).on("click", ".FacturasCambiarEstado", function (event) {
	event.preventDefault()
	var idFactura = $(this).attr("idFactura");
	var accion = $(this).data("accion").toLowerCase();

	var inpt = '<label for="authdescpo">Ingrese Codigo de Administrador</label>';
	inpt+= '<div class="input-group mb-3">';
	inpt+= '  <input type="password" class="form-control decimal siguiente" name="authdescpo" id="authdescpo" placeholder="">';
	inpt+= '  <div class="input-group-append">';
	inpt+= '    <a class="btn btn-default authdescpo-opener"><i class="fa fa-keyboard"></i></a>';
	inpt+= '  </div>';
	inpt+= '</div>';
	Swal.fire({
		title: "Alerta!",
		// text: "Ingrese Codigo de Administrador",
		// input: 'text',
		html: inpt,
		showCancelButton: true,
		// inputPlaceholder: "Codigo"
	}).then((result) => {
		if(result.isDismissed == false){
			if ($("#authdescpo").val() != "") {
			// if (result.value!= "") {
				var clave = $("#authdescpo").val();
				// var clave = result.value;
				$.ajax({
					type: "POST",
					url: url+"/ValidarPermiso",
					data: {clave:clave},
					dataType: 'json',
					success: function (respuesta){
						var respuesta = respuesta.bandera;
						if(respuesta == "1"){
							var inpt = '<label for="motivacc">Ingrese Motivo de esta Accion</label>';
							inpt+= '<div class="input-group mb-3">';
							inpt+= '  <input type="text" class="form-control decimal siguiente" name="motivacc" id="motivacc" placeholder="">';
							inpt+= '  <div class="input-group-append">';
							inpt+= '    <a class="btn btn-default motivacc-opener"><i class="fa fa-keyboard"></i></a>';
							inpt+= '  </div>';
							inpt+= '</div>';
							Swal.fire({
								title: "Alerta!",
								// text: "Ingrese Motivo de esta Accion",
								// input: 'text',
								html: inpt,
								showCancelButton: true,
								inputPlaceholder: "Motivo"
							}).then((resultt) => {
								if(resultt.isDismissed == false){
									// if (resultt.value!= "") {
									if ($("#motivacc").val() != "") {
										// var motivo = resultt.value;
										var motivo = $("#motivacc").val();
										var data = "idFactura=" + idFactura + "&accion=" + accion + "&comentario=" + motivo + "&csrf_test_name=" + token;
	                  $.ajax({
	                      type: "POST",
	                      url: url + "/FacturasCambiarEstado",
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
									} else {
										Swal.fire({
											icon: 'error',
											title: '¡Debes llenar el campo!',
											showConfirmButton: false,
											timer: 1000
										});
									}
								}
							});
							$(".swal2-input").addClass("bg-white");
							setTimeout(function(){
								// console.log('ok');
								$('#motivacc').keyboard({
									openOn : null,
									stayOpen : true,
									layout : 'qwerty',
									restrictInput : true,
									preventPaste : true,
									autoAccept : true
								});
							},200);
						} else {
							Swal.fire({
								icon: 'error',
								title: 'No Tienes Autorizacion',
								showConfirmButton: false,
								timer: 1000
							});
						}
						//$("#tablaDetalleCuenta tbody").html(respuesta.tbody);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown){
						AlertaPersonalizada('error', XMLHttpRequest.responseText);
					}
				});
			}
			else {
				Swal.fire({
					icon: 'error',
					title: '¡Debes llenar el campo!',
					showConfirmButton: false,
					timer: 1000
				});
			}
		}
	});
	setTimeout(function(){
		// console.log('ok');
		$('#authdescpo').keyboard({
			openOn : null,
			stayOpen : true,
			layout : 'num',
			restrictInput : true,
			preventPaste : true,
			autoAccept : true
		});
	},200);

    // Swal.fire({
    //     title: "Alerta!",
    //     text: "Escribe la razón de " + accion + " este registro en el siguiente campo:",
    //     input: 'text',
    //     showCancelButton: false,
    //     inputPlaceholder: "Escribe la razón aquí"
    // }).then((result) => {
    //     if (result.value!= "") {
		// 	var comentario = result.value;
    //         Swal.fire({
    //             title: 'Alerta!!',
    //             text: "¿Estas seguro que desea " + accion + " este registro?",
    //             icon: 'question',
    //             showCancelButton: true,
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonColor: '#d33',
    //             confirmButtonText: 'Si, ' + accion,
    //             cancelButtonText: 'Cancelar',
    //             type: "input",
    //             showCancelButton: true,
    //             animation: "slide-from-top",
    //             inputPlaceholder: "Escribe la razón aquí",
    //         }).then((resultado) => {
    //             if (resultado.value) {
		//
		// 			var data = "idFactura=" + idFactura + "&accion=" + accion + "&comentario=" + comentario + "&csrf_test_name=" + token;
    //                 $.ajax({
    //                     type: "POST",
    //                     url: url + "/FacturasCambiarEstado",
    //                     data: data,
    //                     dataType: 'json',
    //                     success: function (respuesta) {
    //                         Alerta(respuesta.codigo);
    //                         if (respuesta.codigo == 200) {
    //                             //setTimeout("reload();", 1500);
    //                             tablaAdmin.ajax.reload(null, false);
    //                         }
    //                     },
    //                     error: function (XMLHttpRequest) {
    //                         AlertaPersonalizada("error", XMLHttpRequest.responseText);
    //                     }
    //                 });
    //             }
    //         });
    //     } else {
    //         Swal.fire({
    //             icon: 'error',
    //             title: '¡Debes llenar el campo con la razón!',
    //             showConfirmButton: false,
    //             timer: 3000
    //         });
    //     }
    // });

});

/* $(document).on("click", ".FacturasEliminar", function (event) {
	event.preventDefault()
	var idFactura = $(this).attr("idFactura");
	var data = "idFactura=" + idFactura + "&csrf_test_name=" + token;
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
				url: url + "/FacturasEliminar",
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
}); */

var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var proceso = $("#proceso").val();
		var FrmFacturas = $("#FrmFacturas");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmFacturas[0]); }

		var ruta = (proceso == "Agregar") ? "FacturasAgregar" : (proceso == "Editar") ? "FacturasEditar/"+$("#idFactura").val() : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmFacturas.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				//Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					tablaAdmin.ajax.reload(null, false);
                    $("#smModal").modal("toggle");
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

function imprimirTicketSenorita(idFactura) {
	$.ajax({
		type: "POST",
		url: url + "/ImprimirTicketServicio/" + idFactura,
		data: '',
		dataType: 'json',
		success: function (respuesta) {
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200) {
				$.post(""+url+"/imprimir/printSenorita.php", {
					datos: respuesta.datos,
				});
				//setTimeout("reload();",1000);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});

}

function imprimirTicketProducto(idFactura) {
	$.ajax({
		type: "POST",
		url: url + "/ImprimirTicketProducto/" + idFactura,
		data: '',
		dataType: 'json',
		success: function (respuesta) {
			console.log(respuesta.datos);
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200) {
				$.post("http://"+respuesta.servidor+"/imprimir/printProducto.php", {
				//$.post("http://localhost/imprimir/printProducto.php", {
					datos: respuesta.datos,
					tipo:respuesta.tipo,
					recurso:respuesta.recurso,
					ip:respuesta.ip,
				});
				//setTimeout("reload();",1000);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});

}
function imprimirFactura(idFactura) {
	window.open(url + "/FacturasDoc/" + idFactura,"","");
}

function reload() {
	location.href = url + '/' + padre;
}

///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
$(document).on("click",".VentasRetransmitirModal", function(event){
	var idVenta = $(this).attr("idVenta");
	$("#dfModal").modal("show");
	$("#dfModal .modal-content").load(url + '/VentasError/'+idVenta);
})
$(document).on("click",".VentasContingenciaModal", function(event){
	var idVenta = $(this).attr("idVenta");
	$("#dfModal").modal("show");
	$("#dfModal .modal-content").load(url + '/VentasContingencia/'+idVenta);
})
$(document).on("click",".VentasReenviarModal", function(event){
	var idVenta = $(this).attr("idVenta");
	$("#dfModal").modal("show");
	$("#dfModal .modal-content").load(url + '/VentasReenviar/'+idVenta);
})
$(document).on("click",".VentasReimprimir", function(event){
	var idFactura = $(this).attr("idFactura");
	var aliasDocumento = $(this).attr("aliasDocumento");
	var ruta = "Imprimir/ImprimirVenta";
	var file = "print.php";
	if(aliasDocumento == "TIK"){
		file = "printTicket.php";
		ruta = "Imprimir/ImprimirTiket";
	}
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea reimprimir comprobante ?!",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Imprimir',
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url + "/"+ruta+"/" + idFactura,
				dataType: 'JSON',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200) {
						$.post("http://"+respuesta.datos.servidor+"/imprimir/"+file, {
							datos: respuesta.datos.ticket,
						});
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});
$(document).on("click",".VentasAnular", function(event){
	var idFactura = $(this).attr("idFactura");
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea anular este documento ?!",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Anular',
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url + "/anularDTE/" + idFactura,
				dataType: 'JSON',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200) {
						tablaAdmin.ajax.reload(null,false);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});
$(document).on("click",".VentasReenviar", function(event){
	var idFactura = $(this).attr("idVenta");
	var email = $("#emailCliente").val();
	if(email != ""){
		Swal.fire({
			title: 'Alerta!!',
			text: "Estas seguro que desea reenviar el correo ?!",
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Si, Enviar',
			cancelButtonText: 'Cancelar',
		}).then((result) =>{
			if (result.value){
				$.ajax({
					type: "POST",
					url: url + "/EnviarCorreo/" + idFactura+"/0",
					// url: url + "/EnviarCorreo/" + idFactura,
					data: {
						email: email
					},
					dataType: 'JSON',
					success: function (respuesta) {
						Alerta(respuesta.codigo);
						// if (respuesta.codigo == 200) {
						// 	$.post("http://"+respuesta.datos.servidor+"/imprimir/print.php", {
						// 		datos: respuesta.datos.ticket,
						// 	});
							// setTimeout(function(){
							// 	location.reload();
							// },1500);
						// }
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						AlertaPersonalizada('error', XMLHttpRequest.responseText);
					}
				});
			}
		});
	} else {
		AlertaPersonalizada('error','Ingrese una direccion de correo electronico');
	}
});

$(document).on("click",".VentasRetransmitir", function(event){
	event.preventDefault()
	var idVenta = $(this).attr("idVenta");
	var aliasDocumento = $(this).attr("aliasDocumento");
	var ruta = "crearDTE";
	if(aliasDocumento == "NDC" || aliasDocumento == "NDD"){
		var ruta = "crearDTENota";
	}
	var dataString = "idVenta=" + idVenta+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro de retransmitir este registro?!",
		icon: 'question',
		target:'#page-top',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Retransmitir',
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/"+ruta+"/"+idVenta,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						tablaAdmin.ajax.reload(null,false);
						$(".btnclose").click();
					}else {
						if (respuesta.idVenta != "") {
							$("#dfModal .modal-content").load(url + '/VentasError/'+respuesta.idVenta);
						}
					}
				},
				error: function(XMLHttpRequest){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".VentasContingencia", function(event){
	event.preventDefault()
	var idVenta = $(this).attr("idVenta");
	var tipoContingencia = $("#tipoContingencia").val();
	var motivoContingencia = $("#motivoContingencia").val();
	if(motivoContingencia != "" && tipoContingencia !=""){
		var dataString = "idVenta=" + idVenta+"&csrf_test_name="+token;
		Swal.fire({
			title: 'Alerta!!',
			text: "Estas seguro de crear este registro de contingencia?!",
			icon: 'question',
			target:'#page-top',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Si, Crear',
			cancelButtonText: 'Cancelar',
		}).then((result) =>{
			if (result.value){
				$.ajax({
					type: "POST",
					url: url+"/contingenciaDTE/"+idVenta,
					data: {
						idVenta : idVenta,
						tipoContingencia : tipoContingencia,
						motivoContingencia : motivoContingencia,
					},
					dataType: 'json',
					success: function (respuesta){
						console.log(respuesta);
						Alerta(respuesta.codigo);
						if (respuesta.codigo == 200){
							tablaAdmin.ajax.reload(null,false);
							$(".btnclose").click();
						}
						// }else {
						// 	if (respuesta.idVenta != "") {
						// 		$("#dfModal .modal-content").load(url + '/VentasError/'+respuesta.idVenta);
						// 	}
						// }
					},
					error: function(XMLHttpRequest){
						AlertaPersonalizada('error', XMLHttpRequest.responseText);
					}
				});
			}
		});
	} else {
		AlertaPersonalizada('error','Complete todos los datos para continuar');
	}
});
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
