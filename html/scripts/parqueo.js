var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Parqueo';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ParqueoMostrar',token);
});

$(document).on("click", "#ParqueoAgregar", function () {
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/ParqueoAgregar', function () {
      FormatoDatos();
	});
});
$(document).on("click", ".ParqueoEditar", function () {
  var id = $(this).attr('idParqueo');
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/ParqueoEditar/' + id, function () {
    FormatoDatos();
	});
});
$(document).on("click", ".ParqueoCobro", function () {
  var id = $(this).attr('idParqueo');
	$("#smModal").modal("show");
	$("#smModal .modal-content").load(url + '/ParqueoCobro/' + id, function () {
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
  var idClienteParqueo = $('#idClienteParqueo').val();
  var placaParqueo = $('#placaParqueo').val();
  var horaParqueo = $('#horaParqueo').val();
  if(proceso == "Insertar")
  {
    var urlp = "ParqueoAgregar";
    var idParqueo = 0;
  }
  else
  {
    var urlp = "ParqueoEditar";
    var idParqueo = $("#idParqueo").val();
  }
  var dataString = "idClienteParqueo="+idClienteParqueo+"&placaParqueo="+placaParqueo+"&idParqueo="+idParqueo+"&horaParqueo="+horaParqueo;
  $.ajax({
    type: "POST",
    url: url+"/"+urlp,
    data: dataString,
    dataType: 'JSON',
    success: function(respuesta)
    {
      Alerta(respuesta.codigo);
      if (respuesta.codigo == 200) {
        // setTimeout("reload();", 1500);
				imprimir(respuesta.idParqueo);
      } else {
        guardando = 0;
      }
    }
  });
}


$(document).on("click", "#btnCobrar", function()
{
  cobro();
})
function cobro()
{
  var proceso = $('#proceso').val();

  var urlp = "ParqueoCobro";
  var idParqueo = $("#idParqueo").val();
	var totalParqueo = $("#totalParqueo").val();
	var fechaSalidaParqueo = $("#fechaSalidaParqueo").val();
	var horaSalidaParqueo = $("#horaSalidaParqueo").val();
  var dataString = "idParqueo="+idParqueo+"&totalParqueo="+totalParqueo+"&fechaSalidaParqueo="+fechaSalidaParqueo+"&horaSalidaParqueo="+horaSalidaParqueo;
  $.ajax({
    type: "POST",
    url: url+"/"+urlp,
    data: dataString,
    dataType: 'JSON',
    success: function(respuesta)
    {
      Alerta(respuesta.codigo);
      // if (respuesta.codigo == 200) {
      //   setTimeout("reload();", 1500);
      // } else {
         guardando = 0;
      // }
    }
  });
}
function imprimir(idParqueo = "")
{
  var proceso = $('#proceso').val();

  var urlp = "ParqueoImprimir";
	if(idParqueo != "")	{

	}
	else {
		var idParqueo = $("#idParqueo").val();
	}
  var dataString = "idParqueo="+idParqueo;
  $.ajax({
    type: "POST",
    url: url+"/"+urlp,
    data: dataString,
    dataType: 'JSON',
    success: function(respuesta)
    {
      // Alerta(respuesta.codigo);
      // if (respuesta.codigo == 200) {
      //   setTimeout("reload();", 1500);
      // } else {
      //   guardando = 0;
      // }
		  $.post("http://localhost/imprimir/printparqueo.php", {
				cliente: respuesta.cliente,
				existe: respuesta.existe,
				datos: respuesta.datos,
				idParqueo: respuesta.idParqueo,
			});
			setTimeout("reload();", 1500);
    }
  });
}

function reload() {
	location.href = url + '/' + padre;
}
