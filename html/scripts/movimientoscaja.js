var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'MovimientosCaja';
var tablaAdmin;
$(document).ready(function(){
  if($("#proceso").val() == "Consulta"){
    tablaAdmin = RenderizarTablaStock(url,token);
  }
  else{
    tablaAdmin = RenderizarTabla(url,'/MovimientosCajaMostrar',token);
  }
});

$(document).on("click", "#cajaMovimientoIngreso", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/MovimientosCajaIngreso', function () {
		FormatoDatos();
	});
});
$(document).on("click", "#cajaMovimientoSalida", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/MovimientosCajaSalida', function () {
		FormatoDatos();
	});
});
$(document).on("click", ".MovimientosCajaEditar", function () {
  console.log("Entra");
	$("#smModal").modal("show");
	var id = $(this).attr('idCajaMovimiento');
	$("#smModal .modal-content").load(url + '/MovimientosCajaEditar/'+id, function () {
		FormatoDatos();
	});
});

$(document).on("click", ".MovimientosCajaBorrar", function (event) {
	event.preventDefault()
	var idCajaMovimiento = $(this).attr("idCajaMovimiento");
	var data = "idCajaMovimiento=" + idCajaMovimiento + "&csrf_test_name=" + token;
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
				url: url + "/MovimientosCajaBorrar",
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

$(document).on("click", ".MovimientosCajaReimprimir", function (event) {
	var idFactura = $(this).attr("idCajaMovimiento");
	imprimirTicketMovimientoCaja(idFactura);
});

$(document).on("click", "#btnGuardar", function (event) {
	var formulario = "FrmMovimientoIngreso";
	var reglas = {
		montoIngreso: {
			required: true,
		},
		conceptoIngreso: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmMovimientoIngreso').submit();
	//$("#smModal").modal("toggle");
});

var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var proceso = $("#proceso").val();
		var FrmImpresoras = $("#FrmMovimientoIngreso");
		var Frm = false;
		if (window.FormData) { Frm = new FormData(FrmImpresoras[0]); }

		var ruta = (proceso == "Ingreso") ? "MovimientosCajaIngreso" : (proceso == "Salida") ? "MovimientosCajaSalida" : (proceso == "Editar") ? "MovimientosCajaEditar" : "";
		$.ajax({
			type: 'POST',
			url: url + '/' + ruta,
			cache: false,
			data: Frm ? Frm : FrmImpresoras.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				// Codigo
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200) {
					tablaAdmin.ajax.reload(null, false);
                    $("#smModal").modal("toggle");
                    guardando = 0;
					 imprimirTicketMovimientoCaja(respuesta.idCajaMovimiento);
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

function imprimirTicketMovimientoCaja(idFactura) {
	$.ajax({
		type: "POST",
		url: url + "/ImprimirTicketMovimientoCaja/" + idFactura,
		data: '',
		dataType: 'json',
		success: function (respuesta) {
			Alerta(respuesta.codigo);
			if (respuesta.codigo == 200) {
				//console.log(respuesta.servicio);
				//$.post(""+url+"/imprimir/printMovimientoCaja.php", {
				$.post("http://"+respuesta.servidor+"/imprimir/printMovimientoCaja.php", {
					datos: respuesta.servicio,
					tipo: respuesta.tipo,
					recurso: respuesta.recurso,
					ip: respuesta.ip,
				});
				$(".home").click();
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			AlertaPersonalizada('error', XMLHttpRequest.responseText);
		}
	});

}