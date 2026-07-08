var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Membrecia';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/MembreciaMostrar',token);
});

$(document).on("click", "#MembreciaAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/MembreciaAgregar', function () {

	});
});
$(document).on("click", ".MembreciaEditar", function () {
  var id = $(this).attr('idMembrecia');
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/MembreciaEditar/' + id, function () {

	});
});


$(document).on("click", "#btnGenerar", function()
{
  $.ajax({
    type : "POST",
    url : url+'/MembreciaPin',
    data : "process=pin",
    dataType : 'JSON',
    success : function(datax)
    {
      $("#codigoMembrecia").val(datax.pin);
    }
  });
});

$(document).on("click", "#btnGuardar", function()
{
  send();
})
var guardando = 0;
function send()
{
  var proceso = $('#proceso').val();
  var idClienteMembrecia = $('#idClienteMembrecia').val();
  var pin = $('#codigoMembrecia').val();
  if(proceso == "Insertar")
  {
    var urlp = "MembreciaAgregar";
    var idMembrecia = 0;
  }
  else
  {
    var urlp = "MembreciaEditar";
    var idMembrecia = $("#idMembrecia").val();
  }
  var dataString = "idClienteMembrecia="+idClienteMembrecia+"&pin="+pin+"&idMembrecia="+idMembrecia;
  $.ajax({
    type: "POST",
    url: url+"/"+urlp,
    data: dataString,
    dataType: 'JSON',
    success: function(respuesta)
    {
      Alerta(respuesta.codigo);
      if (respuesta.codigo == 200) {
        setTimeout("reload();", 1500);
      } else {
        guardando = 0;
      }
    }
  });
}

function reload() {
	location.href = url + '/' + padre;
}
