var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'MovimientosInventario';
var tablaAdmin;
$(document).ready(function(){
  if($("#proceso").val() == "Consulta"){
    tablaAdmin = RenderizarTablaStock(url,token);
  } 
  else{
    tablaAdmin = RenderizarTabla(url,'/MovimientosInventarioMostrar',token);
  }
  var secundario = ['nombreInsumoCategoria','marcaInsumo'];
  HacerAutoCompletar('producto_buscar','nombreInsumo','/MovimientoInsumoAutocomplete',function (e, data) {
    $('#producto_buscar').typeahead('val', '');
    var proceso = $("#proceso").val();
    var cantidad = data.cantidadInsumoStock;
    if( (proceso == "Carga") || (proceso == "Descarga" && cantidad > 0) ){
      agregar_producto(data.idInsumo, data.nombreInsumo, data.perecederoInsumo, data.cantidadInsumoStock);
    }
    else{
      AlertaPersonalizada("Error","Este Insumo no tiene existencias")
    }
	});

});

$(document).on("click", ".insumoMovimientoVer", function () {
	$("#xlModal").modal("show");
	var id = $(this).attr('idInsumoMovimiento');
	$("#xlModal .modal-content").load(url + '/MovimientoInsumoVer/'+id, function () {
		FormatoDatos();
	});
});

$(document).on("click", ".insumoVer", function () {
	$("#xlModal").modal("show");
	var id = $(this).attr('idInsumo');
	$("#xlModal .modal-content").load(url + '/ConsultaStockVer/'+id, function () {
		FormatoDatos();
	});
});
$(document).on("click", ".ConsultaStockAjuste", function () {
	$("#smModal").modal("show");
	var id = $(this).attr('idInsumo');
	var stock = $(this).attr('stock');
	$("#smModal .modal-content").load(url + '/ConsultaStockAjuste/'+id, function () {
		FormatoDatos();
    //$("#stock").val(stock);
	});
});

$(document).on("change", "#tipo", function(){
  var tipo = $(this).val();
  if(tipo == 'Compra'){
    $(".caja_compra").attr("hidden", false);
    $("#n_documento").val("");
    $("#tipo_documento").val('').trigger('change');
    $("#proveedor").val('').trigger('change');
  }
  else{
    $(".caja_compra").attr("hidden", true);
    $("#n_documento").val("");
    $("#tipo_documento").val('').trigger('change');
    $("#proveedor").val('').trigger('change');
  }
})

$(document).on("keyup",".cant",function () {
  // if($("#proceso").val() == "Descarga"){
  //   var cantidad = $(this).val();
  //   var existencia = $(this).parents("tr").find(".existencia").val();
  //   if(cantidad > existencia){
  //     $(this).parents("tr").find(".cant").val(existencia);
  //   }
  // }
  totales();
 });
 $(document).on("keyup",".precio_compra",function () {
  // if($("#proceso").val() == "Descarga"){
  //   var cantidad = $(this).val();
  //   var existencia = $(this).parents("tr").find(".existencia").val();
  //   if(cantidad > existencia){
  //     $(this).parents("tr").find(".cant").val(existencia);
  //   }
  // }
  totales();
 });
 

 $(document).on("click",".Delete",function () { 
  $(this).parents("tr").remove();
  totales();
 });

 $(document).on("change",".presentacion",function () { 
    var precio = $("option:selected",$(this)).attr("precio");
    $(this).parents("tr").find(".precio_venta").val(precio);
    var costo = $("option:selected",$(this)).attr("costo");
    $(this).parents("tr").find(".precio_compra").val(costo);

    if($("#proceso").val() == "Descarga"){
      var existencia = $("option:selected",$(this)).attr("cantidad");
      $(this).parents("tr").find(".existencia").val(existencia);
      var cantidad = $(this).parents("tr").find(".cant").val();

      if( parseFloat(cantidad) > parseFloat(existencia) ){
        $(this).parents("tr").find(".cant").val(existencia);
      }
    }

    totales();
 
});

$(document).on("click", "#btnGuardar", function(){
  var formulario = "FrmMovimiento";
	var reglas = {
		tipo: {
			required: true,
		},
		concepto: {
			required: true,
		},
		proveedor: {
			required: true,
		},
		tipo_documento: {
			required: true,
		},
		ndocumento: {
			required: true,
		}
	};
	validarDatos(formulario, reglas);
	$('#FrmMovimiento').submit();
});

$(document).on("click", "#btnAjustar", function(){
  var idInsumo = $("#idInsumo").val();
  var cantidad = $("#cantidadAjustar").val();
  var presentacion = $("#presentacionAjuste").val();
  var costo = $("#costoAjustar").val();
  var unidad = $("option:selected",$("#presentacionAjuste")).attr("unidad");

  var datos = {
    idInsumo : idInsumo,
    cantidad : cantidad,
    presentacion : presentacion,
    unidad : unidad,
    costo : costo,
  }
  var validar = validarCamposAjuste();
  if(validar){
    $.ajax({
      type: 'POST',
      url: url + '/ConsultaStockAjuste',
      data:datos,
      dataType: 'json',
      success: function (respuesta) {
        Alerta(respuesta.codigo);
        if (respuesta.codigo == 200) {
          $("#smModal").modal("toggle");
          tablaAdmin.ajax.reload();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  }
});

function validarCamposAjuste() {
  var cantidad = $("#cantidadAjustar");
  var presentacion = $("#presentacionAjuste");
	var n = 0;
  (presentacion.val() == '') ? (presentacion.addClass('is-invalid'), n++) : presentacion.removeClass('is-invalid');
  (cantidad.val() == '') ? (cantidad.addClass('is-invalid'), n++) : cantidad.removeClass('is-invalid');

	return (n == 0) ? true : false;
}

var guardando = 0;
function AgregarEditar(){
  if(!guardando){
    guardando = 1;

    var proceso = $("#proceso").val();
    var detalles = crearListaDetalle();
    var FrmMovimiento = $("#FrmMovimiento");
    var Frm = false;
    if (window.FormData) { Frm = new FormData(FrmMovimiento[0]); }
    var ruta = (proceso == "Carga") ? "MovimientosInventarioAgregar" : (proceso == "Descarga") ? "/MovimientosInventarioDescarga" : "";

    if (detalles){
      $.ajax({
        type: 'POST',
        url: url+"/"+ruta,
			  cache: false,
        data: Frm ? Frm : Movimiento.serialize(),
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(respuesta){
          Alerta(respuesta.codigo);
          if (respuesta.codigo == 200) {
            setTimeout("reload();", 1500);
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
    else{
      guardando = 0;
    }
  }
}

function crearListaDetalle(){
  var i = 0;
  var exito  = true;
  var datos = "";
  var rowCount = $('#inventable >tbody >tr').length;
  if(rowCount > 0)  {
    $("#inventable>tbody tr").each(function(index) {
      if (index >= 0) {
        var id_prod = $(this).find(".id_p").text();
        var id_presentacion = $(this).find(".sel").val();
        var unidad = $("option:selected",$(this).find(".presentacion")).attr("unidad");
        var compra = $(this).find(".precio_compra").val();
        var venta = $(this).find(".precio_venta").val();
        var cant = $(this).find(".cant").val();
        var descp = $(this).find(".descp").val();
        var vence = $(this).find(".vence").val();
        if (venta!="" && cant != "" && parseInt(cant) > 0 && id_presentacion != ''){
          datos += id_prod + "|" + descp  + "|" + compra + "|" + venta + "|" + cant + "|" + unidad + "|" + vence + "|" + id_presentacion + "|" + unidad + "#";
          i = i + 1;
        }
        else{
          exito = false;
          datos = "";
          AlertaPersonalizada("Error","A ocurrido un problema al procesar los productos.");
        }
      }
      else{
        exito = false;
        AlertaPersonalizada("Error","No se a ingresado un detalle de productos.");
      }
    });
  }
  else{
    exito = false;
    AlertaPersonalizada("Error","No se a ingresado un detalle de productos.");
  }
  (exito == true) ? $("#datos").val(datos) : ""  ;
  
  return exito;
}

function round(value, decimals){
  return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function reload() {
	location.href = url + '/' + padre;
}

function totales(){
  var subtotal = 0;
  var total = 0;
  var totalcantidad = 0;
  var subcantidad = 0;
  var total_dinero = 0;
  var total_cantidad = 0;
  $("#inventable tbody tr").each(function()  {
    var compra = $(this).find(".precio_compra").val();
    var cantidad = parseInt($(this).find(".cant").val());
    subtotal = compra * cantidad;
    if (isNaN(cantidad) == true) {
      cantidad = 0;
    }
    totalcantidad += cantidad;
    if (isNaN(subtotal) == true) {
      subtotal = 0;
    }
    total += subtotal;
  });
  if (isNaN(total) == true) {
    total = 0;
  }
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);
  total_dinero = round(total,2);
  total_cantidad = round(totalcantidad,2);

  $('#total_dinero').html("<strong>" + total_dinero + "</strong>");
  $('#total_dineroh').val(total_dinero);
  $('#totcant').html(total_cantidad);

}

function agregar_producto(id_prod, descrip, per,cantidad) {
  var proceso = $("#proceso").val();
  var dataString = {
    id_producto : id_prod,
    proceso : proceso,
    cantidad:cantidad,
  };

  $.ajax({
    type: "POST",
    url: url+"/MovimientoInsumoConsulta",
    data: dataString,
    dataType: 'json',
    success: function(data)    {
      $("#producto_buscar").val("");
      if(proceso == "Carga"){
        var cp = data.costop;
        var perecedero = per;
        var select = data.select;
        var preciop = data.preciop;
        var unidadp = data.unidadp;
        if (perecedero == "1"){ caduca = "<div class='col-xs-2'><input type='date' class='form-control vence' value='' required></div>"; }
        else { caduca = "<div class='col-xs-2'><input type='hidden' class='vence' value='0000-00-00' ></div>"; }
        var unit = "<input type='hidden' class='unidad' value='" + unidadp + "'>";
        var tr_add = "";
        tr_add += '<tr>';
        tr_add += '<td class="id_p">' + id_prod + '</td>';
        tr_add += '<td>' + descrip + '</td>';
        tr_add += '<td>' + select + '</td>';
        tr_add += "<td><div class='col-xs-1'>" + unit + "<input type='text'  class='form-control precio_compra decimal' value='" + cp + "' style='width:80px;' required><input type='hidden' class='precio_venta' value='" + preciop + "'></div></td>";
        // tr_add += "<td><div class='col-xs-1'><input type='text'  class='form-control precio_venta decimal' value='" + preciop + "' style='width:80px;' required></div></td>";
        tr_add += "<td>" + caduca + '</td>';
        tr_add += "<td><div class='col-xs-1'><input type='text'  class='form-control cant decimal' style='width:60px;' value='1' required></div></td>";
        tr_add += "<td><a class='btn btn-danger btn-block Delete' href='#'><i class='fa fa-trash'></i></a></td>";
        tr_add += '</tr>';
      }
      else{
        var cp = data.costop;
        var select = data.select;
        var preciop = data.preciop;
        var unidadp = data.unidadp;
        var unit = "<input type='hidden' class='cantidad' value='" + cantidad + "'>";
        var tr_add = "";
        tr_add += '<tr>';
        tr_add += '<td class="id_p">' + id_prod + '</td>';
        tr_add += '<td>' + descrip + '</td>';
        tr_add += '<td>' + select + '</td>';
        tr_add += "<td><div class='col-xs-1'>" + unit + "<input type='text' readonly class='form-control precio_compra decimal' value='" + cp + "' style='width:80px;' required></td>";
        tr_add += "<td><div class='col-xs-1'><input type='text'  class='form-control precio_venta decimal' value='" + preciop + "' style='width:80px;' required></div></td>";
        tr_add += "<td><div class='col-xs-2'><input type='text' class='form-control existencia' value='' readonly></div></td>";
        tr_add += "<td><div class='col-xs-1'><input type='text'  class='form-control cant decimal' style='width:60px;' value='1' required></div></td>";
        tr_add += "<td><a class='btn btn-danger btn-block Delete' href='#'><i class='fa fa-trash'></i></a></td>";
        tr_add += '</tr>';
      }



      if (id_prod != "") {
        $("#inventable").prepend(tr_add);
        FormatoDatos();
        totales();
      }
    }
  });

  
}

function RenderizarTablaStock(url = '', token) {
  // url : url en la que se encuentra
  // token : token para cifrado de datos
  return $('#tablaAdmin').DataTable({
    "pageLength": 50,
    "serverSide": true,
    "searching": false,
    "order": [
      [0, "asc"]
    ],
    "ajax": {
      url: url + '/ConsultaStock',
      type: 'POST',
      data: function (d) {
          d.csrf_test_name = token;
          d.sucursal = $('#sucursal').val();
          d.busqueda = $("#productoBuscar").val();
          d.categoria = $('#categoria').val();
      },
    },
    "language": {
      "sProcessing": "Procesando...",
      "sLengthMenu": "Mostrar _MENU_ registros",
      "sZeroRecords": "No se encontraron resultados",
      "sEmptyTable": "Ningún dato disponible en esta tabla",
      "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
      "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix": "",
      "sSearch": "Buscar:",
      "sUrl": "",
      "sInfoThousands": ",",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
      },
      "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
      }
    },
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  });

}

$(document).on("change", "#sucursal", function (event) {
  tablaAdmin.ajax.reload();
});
$(document).on("change", "#categoria", function (event) {
  tablaAdmin.ajax.reload();
});
$(document).on("keyup", "#productoBuscar", function (event) {
  tablaAdmin.ajax.reload();
});