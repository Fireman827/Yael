var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Clientes';
var tablaAdmin;
$(document).ready(function(){
	$('.select2').select2();
	$("#ccvTarjeta").mask("000");
	$("#mmaaTarjeta").mask("00/00");
	$("#numeroTarjeta").mask("0000 0000 0000 0000");
	$("#FrmPagoAgregarTransferencia").validate({
    rules: {},
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    },
    submitHandler :function () {
      PagoAgregarTransferencia();
    }
  });

  $("#FrmPagoAgregarTarjeta").validate({
    rules: {},
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    },
    submitHandler :function () {
      PagoAgregarTarjeta();
    }
  });
});

$(document).on("change",'#idCuentaBancaria', function(sel){
	var idCuentaBancaria = $(this).val();
	var nombreBanco = $('select[name="idCuentaBancaria"] option:selected').text();
	var numeroCuenta = $('select[name="idCuentaBancaria"] option:selected').attr("numeroCuenta");
	var nombreDestinatario = $('select[name="idCuentaBancaria"] option:selected').attr("nombreDestinatario");
	var correoElectronicoDestinatario = $('select[name="idCuentaBancaria"] option:selected').attr("correoElectronicoDestinatario");
	if(idCuentaBancaria!=''){
		$('#informacionCuentaBancaria').show()
		$('#nombreBanco').text(nombreBanco)
		$('#numeroCuenta').text(numeroCuenta)
		$('#nombreDestinatario').text(nombreDestinatario)
		$('#correoElectronicoDestinatario').text(correoElectronicoDestinatario)
	}else {
		$('#informacionCuentaBancaria').hide()
	}
});

var guardando = 0;
function PagoAgregarTransferencia(){
	if(!guardando){
		guardando = 1;

		var arrayJsonDetallePago = new Array();
	  var i = 0;
	  $(".filaDetallePago").each(function(index, el) {
	    var obj = new Object();
	    obj.cantidadDetallePago = $(this).find('.cantidadDetallePago').val();
	    obj.precioDetallePago = $(this).find('.precioDetallePago').val();
	    obj.subtotalDetallePago = $(this).find('.subtotalDetallePago').val();
	    obj.descripcionDetallePago = $(this).find('.descripcionDetallePago').text();
	    text = JSON.stringify(obj);
	    arrayJsonDetallePago.push(text);
	    i = i + 1;
	  });
	  jsonArrDetallePago = '[' + arrayJsonDetallePago + ']';
	  $("#detallePagoTransferencia").val(jsonArrDetallePago);
		var FrmPagoAgregarTransferencia = $("#FrmPagoAgregarTransferencia");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmPagoAgregarTransferencia[0]);
		}
		ruta = "PagoAgregarTransferencia";
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmUsuario.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: function(){
	      $(".cargando").show();
	    },
			success: function (respuesta){
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200){
					Swal.fire({
            title: 'Información',
            text: '¿Desea ver el comprobante de pago?',
            type: 'exito',
						allowOutsideClick: false,
            confirmButtonColor: "#7DFF63",
            confirmButtonText: "Confirmar",
            showCancelButton: true,
            cancelButtonColor: "#DD6B55",
            cancelButtonText: "Cancelar",
          }).then((result) =>{
            if (result.value){
							window.open(url + "/PagoRecibo/" + respuesta.idPago, "", "");
              location.href = url + "/inicio";
            }else {
              location.href = url + "/inicio";
            }
          });
				} else {
					guardando = 0;
				}
			},
	    complete:function(data){
	      $(".cargando").hide();
	    },
			error: function(XMLHttpRequest, textStatus, errorThrown){
				guardando = 0;
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
}



function PagoAgregarTarjeta(){
	if(!guardando){
		guardando = 1;

		var arrayJsonDetallePago = new Array();
	  var i = 0;
	  $(".filaDetallePago").each(function(index, el) {
	    var obj = new Object();
	    obj.cantidadDetallePago = $(this).find('.cantidadDetallePago').val();
	    obj.precioDetallePago = $(this).find('.precioDetallePago').val();
	    obj.subtotalDetallePago = $(this).find('.subtotalDetallePago').val();
	    obj.descripcionDetallePago = $(this).find('.descripcionDetallePago').text();
	    text = JSON.stringify(obj);
	    arrayJsonDetallePago.push(text);
	    i = i + 1;
	  });
	  jsonArrDetallePago = '[' + arrayJsonDetallePago + ']';
	  $("#detallePagoTarjeta").val(jsonArrDetallePago);
		var FrmPagoAgregarTarjeta = $("#FrmPagoAgregarTarjeta");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmPagoAgregarTarjeta[0]);
		}
		ruta = "PagoAgregarTarjeta";
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmUsuario.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: function(){
	      $(".cargando").show();
	    },
			success: function (respuesta){
				AlertaPersonalizada(respuesta.tipo,respuesta.mensaje);
				if (respuesta.tipo == 'exito'){
					Swal.fire({
            title: 'Información',
            text: '¿Desea ver el comprobante de pago?',
            type: 'exito',
						allowOutsideClick: false,
            confirmButtonColor: "#7DFF63",
            confirmButtonText: "Confirmar",
            showCancelButton: true,
            cancelButtonColor: "#DD6B55",
            cancelButtonText: "Cancelar",
          }).then((result) =>{
            if (result.value){
							window.open(url + "/PagoRecibo/" + respuesta.idPago, "", "");
              location.href = url + "/inicio";
            }else {
              location.href = url + "/inicio";
            }
          });
				} else {
					guardando = 0;
				}
			},
	    complete:function(data){
	      $(".cargando").hide();
	    },
			error: function(XMLHttpRequest, textStatus, errorThrown){
				guardando = 0;
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
}

$('.dropify').dropify({
	messages: {
			'default': 'Arrastra y suelta',
			'replace': 'Arrastra y suelta o has click para reemplazar',
			'remove':  'Remover',
			'error':   'Ooops, Un problema a ocurrido.'
	},
	error: {
		'fileSize': 'El tamaño del archivo es muy grande ({{ value }} max).',
		'minWidth': 'El tamaño del archivo es muy pequeño ({{ value }}}px min).',
		'maxWidth': 'El ancho de la imagen es muy grande ({{ value }}}px max).',
		'minHeight': 'La altura de la imagen es muy pequeña ({{ value }}}px min).',
		'maxHeight': 'La altura de la imagen es muy grande ({{ value }}px max).',
		'imageFormat': 'El formato de la imagen no es permitido, solo ({{ value }} ).'
	}
});
jQuery.extend(jQuery.validator.messages, {
  required: "Este campo es obligatorio.",
  remote: "Por favor, rellena este campo.",
  email: "Por favor, escribe una dirección de correo válida",
  url: "Por favor, escribe una URL válida.",
  date: "Por favor, escribe una fecha válida.",
  dateISO: "Por favor, escribe una fecha (ISO) válida.",
  number: "Por favor, escribe un número entero válido.",
  digits: "Por favor, escribe sólo dígitos.",
  creditcard: "Por favor, escribe un número de tarjeta válido.",
  equalTo: "Por favor, escribe el mismo valor de nuevo.",
  accept: "Por favor, escribe un valor con una extensión aceptada.",
  maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
  minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
  rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
  range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
  max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
  min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
});

function Alerta(codigo) {
  // 200 : Exito
  // 400 : ya existe un registro con el parametro que no se puede repetir
  // 402 : Error al subir el archivo o imagen.
  // 403 : no se tiene los permisos necesarios para realizar la accion
  // 424 : existen otros registros dependientes del registro a eliminar
  // 500 - 505 : error en las transacciones con las tablas 500 = tabla principal, 501 = 1er tabla secundaria, 502 = 2da tabla secundaria, etc...

  if (codigo == 200) {
    icon = 'fa fa-check';
    tipoNotificacion = "success";
    mensaje = "¡Transacción realizada con exito!";
  } else if (codigo == 400) {
    icon = 'fa fa-exclamation';
    tipoNotificacion = "warning";
    mensaje = "¡Ya existe un registro con estos datos!";
  } else if (codigo == 402) {
    icon = 'fa fa-exclamation';
    tipoNotificacion = "warning";
    mensaje = "¡Transacción no pudo ser realizada!";
  } else if (codigo == 404) {
    icon = 'fa fa-exclamation';
    tipoNotificacion = "warning";
    mensaje = "¡No se encontraron registros!";
  } else if (codigo == 403) {
    icon = 'fa fa-exclamation';
    tipoNotificacion = "warning";
    mensaje = "¡No tiene permisos para realizar esta acción!";
  } else if (codigo == 424) {
    icon = "fa fa-times"
    tipoNotificacion = "danger";
    mensaje = "¡Transanccion no pudo ser realizada! Existen registros que dependen de este";
  } else if (codigo <= 505 && codigo >= 500) {
    icon = "fa fa-times"
    tipoNotificacion = "danger";
    mensaje = "¡Transacción no pudo ser realizada!";
  }
  $(document).Toasts('create', {
    class: 'bg-' + tipoNotificacion,
    title: 'Notificación',
    subtitle: codigo,
    body: mensaje,
    icon: icon,
    autohide: true,
    close : true,
    delay: 18000,
  });
}
function AlertaPersonalizada(tipo, mensaje = "") {
  //tipos success, info, warning, danger, maroon
  if (tipo == "exito" || tipo == "Exito") {
    tipoNotificacion = "success";
  }
  else if (tipo == "error" || tipo == "Error") {
    tipoNotificacion = "danger";
  }
  else if (tipo == "advertencia" || tipo == "Advertencia") {
    tipoNotificacion = "warning";
  }
  else if (tipo == "informacion" || tipo == "Informacion") {
    tipoNotificacion = "info";
  }
  $(document).Toasts('create', {
    class: 'bg-' + tipoNotificacion,
    title: 'Notificación',
    autohide: true,
    close : true,
    delay: 1500,
    body: mensaje,
  });
}

function reload() {
	location.href = url+"/"+padre;
}
