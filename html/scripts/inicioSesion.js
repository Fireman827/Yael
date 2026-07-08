var url = window.location.origin;
$(document).ready(function() {

  $("#usuarioUsuario").keyup(function(event) {
    if ($(this).val() != "") {
      if (event.keyCode == 13) {
        $("#claveUsuario").focus();
      }
    }
  });
  $("#claveUsuario").keyup(function(event) {
    if ($(this).val() != "") {
      if (event.keyCode == 13) {
        IniciarSesion();
      }
    }
  });
  $("#claveUsuario2").keyup(function(event) {
    if ($(this).val() != "") {
      if (event.keyCode == 13) {
        IniciarSesionClave();
      }
    }
  });
});

$(function() {
  //binding event click for button in modal form
  $(document).on("click", "#btnIniciarSesion", function(event) {
    IniciarSesion();
  });
  $(document).on("click", "#btnIniciarSesion2", function(event) {
    IniciarSesionClave();
  });
});

$('#claveUsuario').keyboard({
openOn : null,
stayOpen : true,
layout : 'qwerty'
})
$('.password-opener').click(function(){
var kb = $('#claveUsuario').getkeyboard();
// close the keyboard if the keyboard is visible and the button is clicked a second time
if ( kb.isOpen ) {
kb.close();
} else {
kb.reveal();
}
});
$('#usuarioUsuario').keyboard({
openOn : null,
stayOpen : true,
layout : 'qwerty'
})
$('.usuario-opener').click(function(){
var kb = $('#usuarioUsuario').getkeyboard();
// close the keyboard if the keyboard is visible and the button is clicked a second time
if ( kb.isOpen ) {
kb.close();
} else {
kb.reveal();
}
});
$('#claveUsuario2').keyboard({
    openOn : null,
    stayOpen : true,
		layout : 'num',
		restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
		preventPaste : true,  // prevent ctrl-v and right click
		autoAccept : true
	})
$('.password2-opener').click(function(){
  var kb = $('#claveUsuario2').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});

function IniciarSesion() {
  var usuarioUsuario = $("#usuarioUsuario").val();
  var claveUsuario = $("#claveUsuario").val();
  let token = $("#csrf_token_id").val()
  $.ajax({
    type: 'POST',
    url: url + "/inicio/IniciarSesion",
    data: "usuarioUsuario=" + usuarioUsuario + "&claveUsuario=" + claveUsuario + "&csrf_test_name=" + token,
    dataType: 'JSON',
    success: function(respuesta) {
      if(respuesta.tipo == "exito"){
        tipo = "success";
        mostrarBoton = false;
        if (respuesta.numeroDias <= -5 ) {
          Swal.fire({
            title: respuesta.titulo,
            text: respuesta.mensaje,
            type: tipo,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Continuar",
          }).then((result) =>{
            if (result.value){
              location.href = url + "/PagoAgregar";
            }
          });
        }
        if (respuesta.numeroDias <=0 && respuesta.numeroDias >= -4 ) {
          Swal.fire({
            title: respuesta.titulo,
            text: respuesta.mensaje,
            type: tipo,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Realizar Pago",
            showCancelButton: true,
            cancelButtonColor: "#7DFF63",
            cancelButtonText: "Continuar al sistema",
          }).then((result) =>{
            if (result.value){
              location.href = url + "/PagoAgregar";
            }else {
              location.href = url + "/"+respuesta.rol;
            }
          });
        }
        if (respuesta.numeroDias >0 ) {
          Swal.fire({
            title: respuesta.mensaje,
            type: tipo,
            icon: tipo,
            showCancelButton: false,
            showConfirmButton: mostrarBoton,
          });
          location.href = url + "/"+respuesta.rol;
        }
      } else{
        Swal.fire({
          title: respuesta.mensaje,
          icon: 'error',
          showCancelButton: false,
          showConfirmButton: true,
        });
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      MostrarAlerta('error', errorThrown, textStatus, XMLHttpRequest.responseText);
    }
  });
}
function IniciarSesionClave() {
  var claveUsuario = $("#claveUsuario2").val();
  let token = $("#csrf_token_id").val()
  $.ajax({
    type: 'POST',
    url: url + "/inicio/IniciarSesionClave",
    data:  "&claveUsuario=" + claveUsuario + "&csrf_test_name=" + token,
    dataType: 'JSON',
    success: function(respuesta) {
      if(respuesta.tipo == "exito"){
        tipo = "success";
        mostrarBoton = false;
        if (respuesta.numeroDias <= -5 ) {
          Swal.fire({
            title: respuesta.titulo,
            text: respuesta.mensaje,
            type: tipo,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Continuar",
          }).then((result) =>{
            if (result.value){
              location.href = url + "/PagoAgregar";
            }
          });
        }
        if (respuesta.numeroDias <=0 && respuesta.numeroDias >= -4 ) {
          Swal.fire({
            title: respuesta.titulo,
            text: respuesta.mensaje,
            type: tipo,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Realizar Pago",
            showCancelButton: true,
            cancelButtonColor: "#7DFF63",
            cancelButtonText: "Continuar al sistema",
          }).then((result) =>{
            if (result.value){
              location.href = url + "/PagoAgregar";
            }else {
              location.href = url + "/"+respuesta.rol;
            }
          });
        }
        if (respuesta.numeroDias >0 ) {
          Swal.fire({
            title: respuesta.mensaje,
            type: tipo,
            icon: tipo,
            showCancelButton: false,
            showConfirmButton: mostrarBoton,
          });
          location.href = url + "/"+respuesta.rol;
        }
      } else{
        Swal.fire({
          title: respuesta.mensaje,
          icon: 'error',
          showCancelButton: false,
          showConfirmButton: true,
        });
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      MostrarAlerta('error', errorThrown, textStatus, XMLHttpRequest.responseText);
    }
  });
}

function reload() {
  location.href = url;
}
