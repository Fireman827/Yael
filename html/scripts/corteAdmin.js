var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'CorteAdmin';
var tablaAdmin;
$(document).ready(function(){
	var extras = {
		idCaja:"#cajaAperturaActual"
	}
	tablaAdmin = RenderizarTabla(url,'/CorteMostrar',token,extras);
	$("#cajaAperturaActual").change();
	$("#buscadorInsumo").keyup(function(){
		_this = this;
		// Show only matching TR, hide rest of them
		$.each($("#tablaRevisionInsumos tr"), function()
		{
			if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
			$(this).hide();
			else
			$(this).show();
		});
	});
	$('#montoEfectivoCaja').keyboard({
		openOn : null,
		stayOpen : true,
		layout : 'num',
		restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
		preventPaste : true,  // prevent ctrl-v and right click
		autoAccept : true
	});
	$("#montoEfectivoCaja").trigger("change");
});

$(document).on("click",".apertura-opener",function(){
	var kb = $('#montoApertura').getkeyboard();
	// close the keyboard if the keyboard is visible and the button is clicked a second time
	if (kb.isOpen) {
		kb.close();
	} else {
		kb.reveal();
	}
});
$(document).on("click",".efectivo-opener",function(){
	var kb = $('#montoEfectivoCaja').getkeyboard();
	// close the keyboard if the keyboard is visible and the button is clicked a second time
	if (kb.isOpen) {
		kb.close();
	} else {
		kb.reveal();
	}
});

$(document).on("keyup change",'#montoEfectivoCaja', function(evt){
	var total = parseFloat($("#montoEfectivo").text());
	var total = parseFloat($("#montoEfectivo").text().replace(",",""));
	console.log(total);
	if(isNaN(total)){
		total = 0;
	}
	var efectivo = parseFloat($(this).val());
	if(isNaN(efectivo)){
		efectivo = 0;
	}
	var diferencia =  efectivo - total;
	// $("#montoEfectivoDiferencia").attr("total",diferencia);
	$("#montoEfectivoDiferencia").val(diferencia.toFixed(2));
});
var guardando = 0;
$(document).on("click",'#btnRealizarCorte', function(){
	if(!guardando){
		guardando =1;
		var idCorte = $("#idCorte").val();
		var revisionInsumo = $("#revisionInsumo").val();
		var idTurno = $("#idTurno").val();
		var montoEfectivoCaja = $("#montoEfectivoCaja").val();
		var montoEfectivoDiferencia = $("#montoEfectivoDiferencia").val();
		var montoEfectivo = parseFloat($("#montoEfectivo").text().replace(",",""));
		var entrada = $("#entrada").val();
		var salida = $("#salida").val();
		var totalDocumentos = $("#totalDocumentos").val();
		var totalEfectivo = $("#totalEfectivo").val();
		var totalTarjeta = $("#totalTarjeta").val();
		var totalBitcoin = $("#totalBitcoin").val();
		var totalTransferencia = $("#totalTransferencia").val();
		var totalPedidosYa = $("#totalPedidosYa").val();
		var montoApertura = $("#montoAperturax").val();
		var datosTotales = $("#datosTotales").val();
		var tipoCorte = $("#tipoCorte").val();
		if(!isNaN(montoEfectivoCaja)){
			$.ajax({
				type: "POST",
				url: url + "/RealizarCorte",
				data: {
					datosTotales:datosTotales,
					idCorte:idCorte,
					idTurno:idTurno,
					montoEfectivoCaja:montoEfectivoCaja,
					montoEfectivoDiferencia:montoEfectivoDiferencia,
					montoEfectivo:montoEfectivo,
					entrada:entrada,
					salida:salida,
					totalDocumentos:totalDocumentos,
					totalEfectivo:totalEfectivo,
					totalTarjeta:totalTarjeta,
					totalBitcoin:totalBitcoin,
					totalTransferencia:totalTransferencia,
					totalPedidosYa:totalPedidosYa,
					montoApertura:montoApertura,
					tipoCorte:tipoCorte,
				},
				dataType: 'json',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200)
					{
						if (tipoCorte == "Z")
						{
							if (revisionInsumo == "1")
							{
								Swal.fire({
									title: 'Revisar insumos?',
									showDenyButton: true,
									showCancelButton: false,
									confirmButtonText: 'Si, Cargar lista',
									denyButtonText: `No, Finalizar`,
								}).then((result) => {
									/* Read more about isConfirmed, isDenied below */
									if (result.isConfirmed) {
										imprimir_corte(respuesta.id,"",url+"/RevisionInventarioFinal/"+idCorte);
									} else if (result.isDenied) {
										imprimir_corte(respuesta.id,"",url+"/CorteAdmin");
									}
								})
							} else {
								// setTimeout("reload();",1000);
								imprimir_corte(respuesta.id,"",url+"/CorteAdmin");
							}
						} else {
							// setTimeout("reload();",1000);
							imprimir_corte(respuesta.id,"");
						}
					} else {
						guardando = 0;
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
					guardando = 0;
				}
			});
		}
		else{
			guardando = 0;
			AlertaPersonalizada('error', "Por favor ingrese el efectivo en caja");
		}
	}
});

var guardandoTurno = 0;
$(document).on("click",'#btnRealizarCierreTurno', function(){
	if(!guardandoTurno){
		guardando =1;
		var idCorte = $("#idCorte").val();
		var idTurno = $("#idTurno").val();
		var montoEfectivoCaja = $("#montoEfectivoCaja").val();
		var montoEfectivoDiferencia = $("#montoEfectivoDiferencia").val();
		var montoEfectivo = parseFloat($("#montoEfectivo").text());
		var entrada = $("#entrada").val();
		var salida = $("#salida").val();
		var totalDocumentos = $("#totalDocumentos").val();
		var montoApertura = $("#montoAperturax").val();
		var datosTotales = $("#datosTotales").val();
		var tipoCorte = $("#tipoCorte").val();
		if(!isNaN(montoEfectivoCaja)){
			$.ajax({
				type: "POST",
				url: url + "/RealizarCierreTurno",
				data: {
					datosTotales:datosTotales,
					idCorte:idCorte,
					idTurno:idTurno,
					montoEfectivoCaja:montoEfectivoCaja,
					montoEfectivoDiferencia:montoEfectivoDiferencia,
					montoEfectivo:montoEfectivo,
					entrada:entrada,
					salida:salida,
					totalDocumentos:totalDocumentos,
					montoApertura:montoApertura,
					tipoCorte:tipoCorte
				},
				dataType: 'json',
				success: function (respuesta) {
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200)
					{
						setTimeout("reload();",1000);
					} else {
						guardandoTurno = 0;
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
					guardandoTurno = 0;
				}
			});
		}
		else{
			guardandoTurno = 0;
			AlertaPersonalizada('error', "Por favor ingrese el efectivo en caja");
		}
	}
});
// var imprimiendo = 0;
$(document).on("click",'#printCorte', function(){
	// if(!imprimiendo){
	// 	imprimiendo =1;
	var impresora = $("#impresora").val();
	var idCorteCaja = $("#idCorteHistorial").val();
	imprimir_corte(idCorteCaja,impresora);
	// }
});

$(document).on("click", "#aperturaCaja", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/AperturaCaja', function () {
		FormatoDatos();
	});
});
$(document).on("click", ".ReimprimirCorte", function () {
	var idCorteHistorial = $(this).attr("idCorteHistorial");
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/CorteReimprimir/'+idCorteHistorial, function () {
		// FormatoDatos();
	});
});
$(document).on("click", "#aperturaTurno", function () {

	var idCorte = $(this).attr("idCorte");
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/AperturaTurno/'+idCorte, function () {
		FormatoDatos();
	});
});
$(document).on("click", "#aperturaTurnoUsuario", function ()
{
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/AperturaTurnoUsuario', function () {
		FormatoDatos();
	});
});

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
function imprimir_corte(idCorteHistorial,impresora,urlr=""){
	$.ajax({
		type: "POST",
		url: url+"/ImprimirCorteFiscal",
		data: {
			idCorte: idCorteHistorial,
			impresora: impresora,
		},
		dataType: 'json',
		success: function (respuesta) {
			if (respuesta.codigo == 200) {
				$.post("http://"+respuesta.servidor+"/imprimir/printCorteFiscal.php", {
					datos: respuesta.datos,
					tipo: respuesta.tipo,
					servidor: respuesta.servidor,
				});
				if(impresora ==""){
					if(url!=""){
						setTimeout(function(){
							location.href = urlr;
						},1500);
					} else {
						setTimeout("reload();",1500);
					}
				}
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

$(document).on("change", "#sucursal", function (event)
{
	traerCaja();
});
$(document).on("change", "#cajaAperturaActual", function (event)
{
	traerApertura();
});

function traerCaja()
{
	var sucursal = $("#sucursal").val();
	$("#cajaAperturaActual *").remove();
	$("#select2-cajaAperturaActual-container").text("");
	var sucursal = $("#sucursal").val();
	$.ajax({
		type: "POST",
		url: url+"/TraerCajasSucursal",
		data: {
			sucursal : sucursal,
		},
		// dataType: 'json',
		success: function (respuesta)
		{
			$("#select2-cajaAperturaActual-container").text("Seleccione");
			$("#cajaAperturaActual").html(respuesta);
			// $("#cajaAperturaActual").val("");
			traerApertura();
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}

function traerApertura()
{
	var lista = "";
	var sucursal = $("#sucursal").val();
	var caja = $("#cajaAperturaActual").val();
	var datos = {
		sucursal : sucursal,
		caja : caja,
	};
	//console.log(datos);
	$.ajax({
		type: "POST",
		url: url+"/CorteTraerApertura",
		data: {
			sucursal : sucursal,
			caja : caja,
		},
		dataType: 'json',
		success: function (respuesta)
		{
			$(".corteAdminTabla").html("");
			//console.log(respuesta);
			if (respuesta.existe == "1")
			{
				lista += "<thead>";
				lista += "	<tr>";
				lista += "		<th colspan='4' style='text-align: center'><label class='badge badge-success' style='font-size: 15px; '>Apertura Vigente</label></th>";
				lista += "	</tr>";
				lista += "	<tr>";
				lista += "		<th>Nombre: "+respuesta.nombreUsuario+"</th>";
				lista += "		<th>Fecha Apertura: "+respuesta.fechaCorte+"</th>";
				lista += "		<th>Hora Apertura: "+respuesta.horaCorte+"</th>";
				lista += "	</tr>";
				lista += "	<tr>";
				lista += "		<th>Monto Apertura Turno: "+respuesta.montoTurnoCorteCaja+"</th>";
				lista += "		<th>Turno: "+respuesta.corteTurno+"</th>";
				lista += "		<th>Monto Apertura: "+respuesta.montoApertura+"</th>";
				lista += "	</tr>";
				lista += "	</thead>";
				lista += "	<tbody>";
				lista += "		<tr>";
				lista += "			<td colspan='4' style='text-align: center'>";
				if ((respuesta.idTurnoVigenteExiste != "0" && respuesta.usuario == "1") || (respuesta.idTurnoVigenteExiste != "0" && respuesta.admin == "1"))
				{
					lista += "				<a href='"+url+"/RealizarCorte/"+respuesta.idCorteCaja+"/"+respuesta.idTurnoVigente+"/"+respuesta.idCaja+"' style='margin-bottom: 10px;' class='btn btn-sm btn-"+respuesta.color+"' id='btnCorte'>Realizar Corte</a><br>";
					lista += "				<a href='"+url+"/RealizarCierreTurno/"+respuesta.idCorteCaja+"/"+respuesta.idTurnoVigente+"/"+respuesta.idCaja+"' class='btn btn-sm btn-danger' id='btnTurno'>Cerrar Turno</a>";
				}
				if (respuesta.idTurnoVigenteExiste == "0")
				{
					lista += "				<a data-toggle='modal' data-target='viewModal' data-refresh='true' id='aperturaTurno' idCorte='"+respuesta.idCorteCaja+"' class='btn btn-danger btn-sm  float-center m-t-n-xs'>Aperturar Turno</a>";
				}
				lista += "			</td>";
				lista += "		</tr>";
				lista += "	</tbody>";
				lista += "</table>";
			}
			else
			{
				lista +="<thead>";
				lista +="	<tr>";
				lista +="		<th colspan='4' style='text-align: center'><label class='badge badge-success' style='font-size: 15px; '>Sin apertura de caja</label></th>";
				lista +="	</tr>";
				lista +="	<tr>";
				lista +="		<th colspan='4' style='text-align: center'>";
				lista +="				<a data-toggle='modal' data-target='viewModal' data-refresh='true' id='aperturaCaja' class='btn btn-primary btn-sm  float-center m-t-n-xs'><i class=''></i> Realizar Apertura</a>";
				lista +="		</th>";
				lista +="	</tr>";
				lista +="</thead>";
			}
			$(".corteAdminTabla").html(lista);
			tablaAdmin.ajax.reload();
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}

$(document).on("click", "#btnApertura", function()
{
	AperturaCaja();
});
$(document).on("click", "#btnAperturaTurno", function()
{
	AperturaTurno();
});
$(document).on("click", "#btnAperturaTurnoUsuario", function()
{
	AperturaTurnoUsuario();
});
var sending = false;
function AperturaCaja(){
	if(!sending){
		sending = true;
		var montoApertura = $("#montoApertura").val();
		var usuarioApertura = $("#usuarioApertura").val();
		var cajaApertura = $("#cajaApertura").val();
		//console.log(cajaApertura);
		if (montoApertura != "")
		{
			if(usuarioApertura != "")
			{
				if (cajaApertura != 0)
				{
					$.ajax({
						type: "POST",
						url: url+"/AperturaCaja",
						data: {
							montoApertura: montoApertura,
							usuarioApertura: usuarioApertura,
							cajaApertura: cajaApertura,
						},
						dataType: 'json',
						success: function (respuesta) {
							Alerta(respuesta.codigo);
							if (respuesta.codigo == 200)
							{
								var idCorteCaja = respuesta.idCorteCaja;
								var stockApertura = respuesta.stockApertura;
								// setTimeout("reload();",1000);
								if (stockApertura == "Si")
								{
									Swal.fire({
										title: 'Cargar lista de insumos?',
										showDenyButton: true,
										showCancelButton: false,
										confirmButtonText: 'Si, Cargar',
										denyButtonText: `No, continuar apertura`,
									}).then((result) => {
										/* Read more about isConfirmed, isDenied below */
										if (result.isConfirmed) {
											location.href = url+"/RevisionInventario/"+idCorteCaja;
											// Swal.fire('Saved!', '', 'success')
										} else if (result.isDenied) {
											setTimeout("reload();",1000);
										}
									})
								}
								else
								{
									setTimeout("reload();",1000);
								}

							}
							else
							{
								Alerta(respuesta.codigo);
							}
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							AlertaPersonalizada('error', XMLHttpRequest.responseText);
						}
					});
				}
				else
				{
					AlertaPersonalizada('error', "No hay cajas disponibles");
					sending = false;
				}
			}
			else
			{
				AlertaPersonalizada('error', "Seleccione un usuario");
				sending = false;
			}
		}
		else
		{
			AlertaPersonalizada('error',"Ingrese el monto de la apertura");
			sending = false;
		}
	}
}
var sendingtu = false;
function AperturaTurno()
{
	if(!sendingtu){
		sendingtu = true;
		var montoApertura = $("#montoApertura").val();
		var usuarioApertura = $("#usuarioApertura").val();
		var cajaApertura = $("#cajaApertura").val();
		var idCorte = $("#idCorte").val();
		console.log(cajaApertura);
		if (montoApertura != "")
		{
			if(usuarioApertura != "")
			{
				if (cajaApertura != 0)
				{
					$.ajax({
						type: "POST",
						url: url+"/AperturaTurno",
						data: {
							montoApertura: montoApertura,
							usuarioApertura: usuarioApertura,
							cajaApertura: cajaApertura,
							idCorte: idCorte,
						},
						dataType: 'json',
						success: function (respuesta) {
							Alerta(respuesta.codigo);
							if (respuesta.codigo == 200)
							{
								setTimeout("reload();",1000);
							}
							else
							{
								Alerta(respuesta.codigo);
							}
						},
						error: function (XMLHttpRequest, textStatus, errorThrown) {
							AlertaPersonalizada('error', XMLHttpRequest.responseText);
						}
					});
				}
				else
				{
					AlertaPersonalizada('error', "No hay cajas disponibles");
					sendingtu = false;
				}
			}
			else
			{
				AlertaPersonalizada('error', "Seleccione un usuario");
				sendingtu = false;
			}
		}
		else
		{
			AlertaPersonalizada('error',"Ingrese el monto de la apertura");
			sendingtu = false;
		}
	}
}
function AperturaTurnoUsuario()
{
	var montoApertura = $("#montoApertura").val();
	var usuarioApertura = $("#usuarioApertura").val();
	var cajaApertura = $("#cajaApertura").val();
	// var idCorte = $("#idCorte").val();
	console.log(cajaApertura);
	if (montoApertura != "")
	{
		if(usuarioApertura != "")
		{
			if (cajaApertura != 0)
			{
				$.ajax({
					type: "POST",
					url: url+"/AperturaTurnoUsuario",
					data: {
						montoApertura: montoApertura,
						usuarioApertura: usuarioApertura,
						cajaApertura: cajaApertura,
						// idCorte: idCorte,
					},
					dataType: 'json',
					success: function (respuesta) {
						Alerta(respuesta.codigo);
						if (respuesta.codigo == 200)
						{
							setTimeout("reload();",1000);
						}
						else
						{
							Alerta(respuesta.codigo);
						}
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						AlertaPersonalizada('error', XMLHttpRequest.responseText);
					}
				});
			}
			else
			{
				AlertaPersonalizada('error', "No hay cajas disponibles");
			}
		}
		else
		{
			AlertaPersonalizada('error', "Seleccione un usuario");
		}
	}
	else
	{
		AlertaPersonalizada('error',"Ingrese el monto de la apertura");
	}
}

$(document).on("click", "#btnRevisarInusmo", function()
{
	RevisionInventario();
})
$(document).on("click", "#btnRevisarInusmoFinal", function()
{
	RevisionInventarioFinal();
})
function RevisionInventario()
{
	var idCorte = $("#idCorte").val();
	var arrayData = new Array();
	var i = 0;
	var j = 0;
	$("#tablaRevisionInsumos tr").each(function(index)
	{
		var idInsumo = $(this).find("#idInsumo").val();
		var nombreInsumo = $(this).find("#nombreInsumo").val();
		var descripcionInsumo = $(this).find("#descripcionInsumo").val();
		var descripcionInsumoPresentacion = $(this).find("#descripcionInsumoPresentacion").val();
		var cantidadInsumoStock = $(this).find("#cantidadInsumoStock").val();
		var unidadInsumoPresentacion = $(this).find("#unidadInsumoPresentacion").val();
		var idInsumoPresentacion = $(this).find("#idInsumoPresentacion").val();
		var existenciaMinima = $(this).find("#existenciaMinima").val();
		var existenciaReal = $(this).find("#existenciaReal").val();
		var costoInsumoPresentacion = $(this).find("#costoInsumoPresentacion").val();
		var precioInsumoPresentacion = $(this).find("#precioInsumoPresentacion").val();
		if (existenciaReal != "")
		{
			j +=1;
		}

		var obj = new Object();
		obj.idInsumo = idInsumo;
		obj.idInsumoPresentacion = idInsumoPresentacion;
		obj.nombreInsumo = nombreInsumo;
		obj.descripcionInsumo = descripcionInsumo;
		obj.descripcionInsumoPresentacion = descripcionInsumoPresentacion;
		obj.cantidadInsumoStock = cantidadInsumoStock;
		obj.unidadInsumoPresentacion = unidadInsumoPresentacion;
		obj.existenciaMinima = existenciaMinima;
		obj.existenciaReal = existenciaReal;
		obj.costoInsumoPresentacion = costoInsumoPresentacion;
		obj.precioInsumoPresentacion = precioInsumoPresentacion;
		//convert object to json string
		text = JSON.stringify(obj);
		arrayData.push(text);
		i = i + 1;
	});
	var json_arr = '[' + arrayData + ']';

	$.ajax({
		type: "POST",
		url: url+"/RevisionInventario",
		data:
		{
			datos : json_arr,
			idCorte : idCorte,
			cuantos : i,
			ajustar : j,
		},
		dataType: 'json',
		success: function (respuesta)
		{
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200)
			{
				setTimeout("reload();",1000);
			}
			else
			{
				Alerta(respuesta.codigo);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}

function RevisionInventarioFinal()
{
	var idCorte = $("#idCorte").val();
	var arrayData = new Array();
	var i = 0;
	var j = 0;
	$("#tablaRevisionInsumos tr").each(function(index)
	{
		var idInsumo = $(this).find("#idInsumo").val();
		var nombreInsumo = $(this).find("#nombreInsumo").val();
		var descripcionInsumo = $(this).find("#descripcionInsumo").val();
		var descripcionInsumoPresentacion = $(this).find("#descripcionInsumoPresentacion").val();
		var cantidadInsumoStock = $(this).find("#cantidadInsumoStock").val();
		var unidadInsumoPresentacion = $(this).find("#unidadInsumoPresentacion").val();
		var idInsumoPresentacion = $(this).find("#idInsumoPresentacion").val();
		var existenciaMinima = $(this).find("#existenciaMinima").val();
		var diferencia = $(this).find("#diferencia").val();
		var idRevisionInsumo = $(this).find("#idRevisionInsumo").val();
		var insumoActual = $(this).find("#insumoActual").val();
		var existenciaReal = $(this).find("#existenciaReal").val();

		if (existenciaReal != "")
		{
			j +=1;
		}

		var obj = new Object();
		obj.idInsumo = idInsumo;
		obj.idInsumoPresentacion = idInsumoPresentacion;
		obj.nombreInsumo = nombreInsumo;
		obj.descripcionInsumo = descripcionInsumo;
		obj.descripcionInsumoPresentacion = descripcionInsumoPresentacion;
		obj.cantidadInsumoStock = cantidadInsumoStock;
		obj.unidadInsumoPresentacion = unidadInsumoPresentacion;
		obj.existenciaMinima = existenciaMinima;
		obj.diferencia = diferencia;
		obj.idRevisionInsumo = idRevisionInsumo;
		obj.insumoActual = insumoActual;
		obj.existenciaReal = existenciaReal;
		//convert object to json string
		text = JSON.stringify(obj);
		arrayData.push(text);
		i = i + 1;
	});
	var json_arr = '[' + arrayData + ']';

	$.ajax({
		type: "POST",
		url: url+"/RevisionInventarioFinal",
		data:
		{
			datos : json_arr,
			idCorte : idCorte,
			cuantos : i,
			ajustar : j,
		},
		dataType: 'json',
		success: function (respuesta)
		{
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200)
			{
				setTimeout("reload();",1000);
			}
			else
			{
				Alerta(respuesta.codigo);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});
}
$(document).on("change", "#insumoCategoriaRevision", function()
{
	var valor = $(this).val();
	console.log(valor);
	if(valor == "All")
	{
		$("#tablaRevisionInsumos tr").each(function(index)
		{
			$(this).css('display',"");
		});
	}
	else
	{
		$("#tablaRevisionInsumos tr").each(function(index)
		{
			console.log($(this).hasClass("fila"+valor));
			if($(this).hasClass("fila"+valor))
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
	}
})
