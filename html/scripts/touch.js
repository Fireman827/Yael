var url = window.location.origin;
var token = $("#csrf_token_id").val();
var grupoGeneral = 0;//Variable para saber que grupo de productos el el ultimo
var grupoActual = 0;//Variable para saber en que grupo de productos en el pedido se encuentra

// Llena en cascada el select de Municipio del cobro según el departamento (código FE_CAT_012)
// seleccionado, y deja seleccionado el municipio indicado (código FE_CAT_013) si se provee.
function cargarMunicipiosPagoProducto(deptoCodigo, municipioSeleccionado){
  var $municipio = $("#municipioClientePagoProducto");
  $municipio.empty().append('<option value="">Seleccione</option>');
  var lista = (typeof municipiosPorDepto !== "undefined") ? municipiosPorDepto[deptoCodigo] : null;
  if(lista){
    lista.forEach(function(m){
      $municipio.append($('<option></option>').attr('value', m.codigo).text(m.valores));
    });
  }
  if(municipioSeleccionado){
    $municipio.val(municipioSeleccionado);
  }
}
$(document).on("click",".efectivo-opener",function(){
  var kb = $('#efectivoPagoProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click",".tarjeta-opener",function(){
  var kb = $('#tarjetaPagoProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click",".btc-opener",function(){
  var kb = $('#bitcoinPagoProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click",".pedidos-opener",function(){
  var kb = $('#pedidosYaPagoProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click",".transferencia-opener",function(){
  var kb = $('#transferenciaProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click",".envio-opener",function(){
  var kb = $('#envioProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});

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
$(document).on("click",".descuentopor-opener",function(){
  var kb = $('#descuentoPagoProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).on("click",".descuentodin-opener",function(){
  var kb = $('#descuentoDolarPagoProducto').getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) {
    kb.close();
  } else {
    kb.reveal();
  }
});
$(document).ready(function () {
  FormatoDatos();
  $('#efectivoPagoProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  $('#tarjetaPagoProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  $('#bitcoinPagoProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  $('#pedidosYaPagoProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  $('#descuentoPagoProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  $('#descuentoDolarPagoProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  $('#envioProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  $('#transferenciaProducto').keyboard({
    openOn : null,
    stayOpen : true,
    layout : 'num',
    restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
    preventPaste : true,  // prevent ctrl-v and right click
    autoAccept : true
  });
  // $('#authdescpo').keyboard({
  // 		openOn : null,
  // 		stayOpen : true,
  // 		layout : 'num',
  // 		restrictInput : true, // Prevent keys not in the displayed keyboard from being typed in
  // 		preventPaste : true,  // prevent ctrl-v and right click
  // 		autoAccept : true
  // 	});
  $(document).on('click','.nav-link.pestana' , function (e) {
    e.preventDefault()
    $(this).tab('show')
  })
  $('a.nav-link.pestana').on('shown.bs.tab', function (e) {
    var nuevaTab = $(e.target) // newly activated tab
    var antiguaTab = $(e.relatedTarget) // previous active tab
    cambiarColorTabs(nuevaTab,antiguaTab);
  })
  $(document).on('click','.nav-link.pestanaDet' , function (e) {
    e.preventDefault()
    $(this).tab('show')
  })
  $('a.nav-link.pestanaDet').on('shown.bs.tab', function (e) {
    var nuevaTab = $(e.target) // newly activated tab
    var antiguaTab = $(e.relatedTarget) // previous active tab
    cambiarColorTabs(nuevaTab,antiguaTab);
  })
  $(document).on('click','.nav-link.pestanaDetCancel' , function (e) {
    e.preventDefault()
    $(this).tab('show')
  })
  $('a.nav-link.pestanaDetCancel').on('shown.bs.tab', function (e) {
    var nuevaTab = $(e.target) // newly activated tab
    var antiguaTab = $(e.relatedTarget) // previous active tab
    cambiarColorTabs(nuevaTab,antiguaTab);
  })
  /*/ Control de Tabs (Final) /*/

  function cambiarColorTabs(nuevaTab,antiguaTab){
    (antiguaTab.hasClass("bg-default")) ? (nuevaTab.toggleClass('bg-default') 	,antiguaTab.toggleClass('bg-default') ) :
    (antiguaTab.hasClass("bg-success")) ? (nuevaTab.toggleClass('bg-success') 	,antiguaTab.toggleClass('bg-success') ) :
    (antiguaTab.hasClass("bg-primary")) ? (nuevaTab.toggleClass('bg-primary') 	,antiguaTab.toggleClass('bg-primary') ) :
    (antiguaTab.hasClass("bg-info")) 	? (nuevaTab.toggleClass('bg-info') 		,antiguaTab.toggleClass('bg-info') ) 	:
    (antiguaTab.hasClass("bg-warning")) ? (nuevaTab.toggleClass('bg-warning') 	,antiguaTab.toggleClass('bg-warning') ) :
    (antiguaTab.hasClass("bg-danger")) 	? (nuevaTab.toggleClass('bg-danger') 	,antiguaTab.toggleClass('bg-danger') ) 	: '';
  }

  HacerAutoCompletar('clienteProducto','nombreCliente','/ClienteAutocomplete',function (e, data) {
    $('#clienteProducto').typeahead('val', data.nombreCliente);
    $("#idClienteProducto").val(data.idCliente);
    $("#nombreClientePagoProducto").val(data.nombreCliente);
    $("#direccionClientePagoProducto").val(data.direccionCliente);
    if(data.facturarConCliente == "NIT"){
      $("#nitClientePagoProducto").val(data.nitCliente);
    } else {
      $("#nitClientePagoProducto").val(data.duiCliente);
    }
    $("#nrcClientePagoProducto").val(data.nrcCliente);
    $("#departamentoClientePagoProducto").val(data.departamento);
    cargarMunicipiosPagoProducto(data.departamento, data.municipio);
    $("#correoClientePagoProducto").val(data.emailCliente);
    $("#telefonoClientePagoProducto").val(data.telefonoCliente);
    $("#giroClientePagoProducto").val(data.giroCliente);
    $(".editarCliente").attr("idCliente",data.idmd);
    $(".editarCliente").removeAttr("hidden");
  });
  /************************************************************************************/
  /************* recomentar al saber como hacer con telcado virtual *******************/
  /************************************************************************************/
  HacerAutoCompletar('nombreClientePagoProducto','nombreCliente','/ClienteAutocomplete',function (e, data) {
    $("#idClienteProducto").val(data.idCliente);
    $("#nombreClientePagoProducto").val(data.nombreCliente);
    $("#direccionClientePagoProducto").val(data.direccionCliente);
    if(data.facturarConCliente == "NIT"){
      $("#nitClientePagoProducto").val(data.nitCliente);
    } else {
      $("#nitClientePagoProducto").val(data.duiCliente);
    }
    $("#nrcClientePagoProducto").val(data.nrcCliente);
    $("#departamentoClientePagoProducto").val(data.departamento);
    cargarMunicipiosPagoProducto(data.departamento, data.municipio);
    $("#correoClientePagoProducto").val(data.emailCliente);
    $("#telefonoClientePagoProducto").val(data.telefonoCliente);
    $("#giroClientePagoProducto").val(data.giroCliente);
    $('#clienteProducto').typeahead('val', data.nombreCliente);
    $(".editarCliente").attr("idCliente",data.idmd);
    $(".editarCliente").removeAttr("hidden");
  });
  /************************************************************************************/
  /************************************************************************************/

  // Si el cajero corrige manualmente el departamento del cliente, recargar las
  // opciones de municipio para ese departamento.
  $(document).on("change", "#departamentoClientePagoProducto", function(){
    cargarMunicipiosPagoProducto($(this).val(), "");
  });

  // $('.softkeys').softkeys({
  // 		  target : $('.softkeys').data('target'),
  // 		  layout : [
  // 			[ 	['1','!'],['2','@'],['3','#'], ],
  // 			[   ['4','$'],['5','%'],['6','^'], ],
  // 			[   ['7','&amp;'],['8','*'],['9','('], ],
  // 			[   ['0',')'],['.',''],'delete' ]
  // 		  ]
  // 		}).on("touchend",function(){
  // 			$($(this).data("target")).keyup();
  // 		});

  // $('.softkeyss').softkeys({
  // 	target : $('.softkeyss').data('target'),
  // 	layout : [
  // 		[ 	['1','!'],['2','@'],['3','#'], ],
  // 		[   ['4','$'],['5','%'],['6','^'], ],
  // 		[   ['7','&amp;'],['8','*'],['9','('], ],
  // 		[   ['0',')'],['.',''],'delete' ]
  // 	]
  // 	}).on("touchend",function(){
  // 		$($(this).data("target")).keyup();
  // 	});

  $(".dinamicos").hide();

  $(".home").click(function () {
    $(".dinamicos").hide();
    $("#divDatosDoc").hide();
    $("#botoneraPrimaria").show();
    $("#listaProductos").empty();
    $("#tablaDetalleCliente tbody").empty();
    $("#tablaDetalleCuenta tbody").empty();
    $("#tablaUnirCuenta tbody").empty()	;
    $("#listaServicios").empty();
    $("#listaCovers").empty();
    $("#generalModificadores").empty();
    $("#modificadoresTipo").empty();
    $("#listaModificadores").empty();
    $("#divMesasCuenta").empty();
    $("#listaProductosMostrar").empty();
    $("#idPedidoGuardado").val("");
    $("#idClienteProducto").val("");
    $("#clienteProducto").val("");
    $("#nombreClientePagoProducto").val("");
    $("#direccionClientePagoProducto").val("");
    $("#nitClientePagoProducto").val("");
    $("#nrcClientePagoProducto").val("");
    $("#telefonoClientePagoProducto").val("");
    $("#correoClientePagoProducto").val("");
    $("#giroClientePagoProducto").val("");
    $("#departamentoClientePagoProducto").val("");
    cargarMunicipiosPagoProducto("", "");
    $("#personaHacerCuenta").val("");
    $("#efectivoPagoProducto").val("");
    $("#tarjetaPagoProducto").val("");
    $("#bitcoinPagoProducto").val("");
    $("#pedidosYaPagoProducto").val("");
    $("#descuentoPagoProducto").val("");
    $("#descuentoDolarPagoProducto").val("");
    $("#totalPropPagoProducto").text("0.00");
    $("#totalPagoProducto").text("0.00");
    $("#vueltoPagoProducto").text("0.00");
    $("#CobrarCuentaFinal").val("0");

    if($("#autorizadoUsuario").val() == "0"){
      $("#descuentoPagoProducto").attr("permiso","0");
      $("#descuentoDolarPagoProducto").attr("permiso","0");
    }
    $("#totaltd").text("$0.00").attr("total","0.00");
    $("#totaltds").text("$0.00");
    $("#AgregarCuentaLlevarLocal").val("0");
    $("#comentarioGeneralOrden").val("");
    $("#propinaPagoProducto").text("0.00"),

    $("#porConsumoPagoProducto").prop('checked',false);
    $("#quitarPropina").prop('checked',false);
    setTimeout(function(){
      calculoTotal();
    },200);
    calculoTotals();
    $(document).find(".zonas").removeClass('bg-success');
  });


  /***********************************/
  /*************BOTONERA**************/
  /***********************************/
  $("#btnOrden").click(function () {
    $(".dinamicos").hide();
    $("#botoneraOrdenes").show();
    $("#botoneraHome").show();
    $(".pasos").hide();
    $("#listaProductosMostrar").empty();
    $("#botoneraPrimaria").hide();
  });

  /***********************************/
  /***********************************/
  $("#btnCuenta").click(function () {
    $(".dinamicos").hide();
    $("#tabCuentaActivaLocal").click();
    $("#botoneraHome").show();
    $(".pasos").hide();
    $("#listaProductosMostrar").empty();
    $("#botoneraPrimaria").hide();

    $.ajax({
      url: url+"/Touch/BuscarActivos",
      type: 'POST',
      dataType: 'json',
      data:
      {
        dat: 1,
      },
      success: function(data)
      {
        $("#tabCuentaActivaLocal").text("Local $"+ data.local);
        $("#tabCuentaActivaLlevar").text("LLevar $"+ data.llevar);
        $("#tabCuentaActivaDomicilio").text("Domicilio $"+ data.domicilio);
      }
    });

  });
  /***********************************/
  /***********************************/
  $(document).on("click", ".agregarCliente", function(){
    window.open(url+"/ClienteAgregarAvanzado/2121",'popup','width=1024,height=900');
  })
  $(document).on("click", ".editarCliente", function(){
    var idCliente = $(this).attr("idCliente");
    window.open(url+"/ClienteEditarAvanzado/"+idCliente+"/2121",'popup','width=1024,height=900');
  })
  /***********************************/
  /***********************************/
  $("#btnMovimiento").click(function () {
    $(".dinamicos").hide();
    $("#botoneraMovimientos").show();
    $("#botoneraHome").show();
    $(".pasos").hide();
    $("#listaProductosMostrar").empty();
    $("#botoneraPrimaria").hide();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $(".tipoOrden").click(function () {
    if($(".banderaAccionPasos").val()){
      $("#btnOrden").click();
    } else {
      $("#btnOrden").click();
      $("#listaProductosMostrar").empty();
      $("#nuevaOrden").hide();
    }
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $(".mesaOrden").click(function () {
    $("#listaProductosMostrar").empty();
    $("#divDatosZonaMesa").show();
    $("#nuevaOrden").show();
    $("#nuevaOrden .scrollmenu").hide();
    $("#divDatosCliente").hide();
    $("#botoneraPrimaria").hide();
    $(".pasos").show();
    $("#botoneraHome").show();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $(".cuentaOrden").click(function () {
    $("#divDatosZonaMesa").hide();
    $("#nuevaOrden").show();
    $("#nuevaOrden .scrollmenu").hide();
    $("#listaProductosMostrar").empty();
    $("#divDatosCliente").show();
    $("#botoneraPrecios").hide();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $(".elementosOrden").click(function () {
    $("#nuevaOrden").hide();
    $("#listaProductosMostrar").empty();
    $("#ordenarProducto").click();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  // $(".pasos").click(function () {
  // 	$(".pasos").removeClass('bg-success');
  // 	$(".pasos").addClass('bg-default');
  // 	$(this).toggleClass('bg-success bg-default');
  // });
  /***********************************/
  /***********************************/

  ////////////////////////////////////////////////////////////////
  //////////////////tab Cuentas Activas Detalle///////////////////
  $(document).on("click",".pestanaDet",function () {
    var tipo = $(this).attr("tipo");
    ActualizarCuentas(tipo);
    $("#botoneraHome").show();
    $("#botoneraPrimaria").hide();
  });
  ////////////////Fin tab Cuentas Activas Detalle ////////////////
  ////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////
  //////////////////tab Cuentas Activas Detalle///////////////////
  $(document).on("click","#tabCuentaActiva",function () {
    $("#tabCuentaActivaLocal").click();
    //$("#botoneraPrimaria").hide();
    $("#botoneraHome").show();

    $.ajax({
      url: url+"/Touch/BuscarActivos",
      type: 'POST',
      dataType: 'json',
      data:
      {
        dat: 1,
      },
      success: function(data)
      {
        $("#tabCuentaActivaLocal").text("Local $"+ data.local);
        $("#tabCuentaActivaLlevar").text("LLevar $"+ data.llevar);
        $("#tabCuentaActivaDomicilio").text("Domicilio $"+ data.domicilio);
      }
    });

  });
  ////////////////Fin tab Cuentas Activas Detalle ////////////////
  ////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////
  //////////////////tab Cuentas Canceladas Detalle///////////////////
  $(document).on("click",".pestanaDetCancel",function () {
    var tipo = $(this).attr("tipo");
    ActualizarCuentasCanceladas(tipo);
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();

  });
  ////////////////Fin tab Cuentas Activas Detalle ////////////////
  ////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////
  //////////////////tab Cuentas Activas Detalle///////////////////
  $(document).on("click","#tabCuentaCancelada",function () {
    $("#tabCuentaCanceladaLocal").click();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
  });
  ////////////////Fin tab Cuentas Activas Detalle ////////////////
  ////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Dividir Cuenta///////////////////
  $(document).on("click",".DividirCuenta",function () {
    var inpt = '<label for="authdescpo">Escribe en Nuevo Nombre del Cliente</label>';
    inpt+= '<div class="input-group mb-3">';
    inpt+= '  <input type="text" class="form-control siguiente" name="authdescpo" id="authdescpo" placeholder="">';
    inpt+= '  <div class="input-group-append">';
    inpt+= '    <a class="btn btn-default authdescpo-opener"><i class="fa fa-keyboard"></i></a>';
    inpt+= '  </div>';
    inpt+= '</div>';
    Swal.fire({
      title: "Alerta!",
      // text: "Escribe en Nuevo Nombre del Cliente:",
      // input: 'text',
      html: inpt,
      showCancelButton: true,
      // inputPlaceholder: "Escribe el nombre Aquí"
    }).then((result) => {
      if(result.isDismissed == false){
        // if (result.value!= "") {
        if ($("#authdescpo").val() != "") {
          // var nombre = result.value;
          var nombre = $("#authdescpo").val();
          var idPedido = $(this).attr("idPedido");
          DividirCuenta(idPedido,nombre);
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
    $(".swal2-input").addClass("bg-white");
    setTimeout(function(){
      // console.log('ok');
      $('#authdescpo').keyboard({
        openOn : null,
        stayOpen : true,
        layout : 'qwerty',
        restrictInput : true,
        preventPaste : true,
        autoAccept : true
      });
    },200);
  });
  ////////////////Fin Dividir Cuenta/////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Unir Cuenta///////////////////
  $(document).on("click",".UnirCuenta",function () {
    var idPedido = $(this).attr("idPedido");
    var idZona = $(this).attr("idZona");
    $("#idCuentaPrincipalUnir").val(idPedido);

    $("#tablaDetalleCuenta").hide();
    $("#accionesCuentaDetalle").hide();
    $("#tablaUnirCuenta").show();
    $("#accionUnirCuenta").show();

    listarCuentasLocal(idPedido,idZona);

  });
  ////////////////Fin Unir Cuenta/////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Btn Unir Cuenta///////////////////
  $(document).on("click",".btnUnirCuenta",function () {
    UnirCuenta();
  });
  ////////////////Fin Btn Unir Cuenta/////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Anular Cuenta/////////////////////
  $(document).on("click",".AnularCuenta",function () {
    var idPedido = $(this).attr("idPedido");
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
        // if (result.value!= "") {
        if ($("#authdescpo").val() != "") {
          var clave = $("#authdescpo").val();
          $.ajax({
            type: "POST",
            url: url+"/ValidarPermiso",
            data: {clave:clave},
            dataType: 'json',
            success: function (respuesta){
              var respuesta = respuesta.bandera;
              if(respuesta == "1"){
                $.ajax({
                  type: "POST",
                  url: url+"/AnularCuenta",
                  data: {
                    idPedido: idPedido
                  },
                  dataType: 'json',
                  success: function (respuesta){
                    Alerta(respuesta.codigo);
                    if(respuesta.codigo == 200){
                      ActualizarCuentas();
                    }
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown){
                    AlertaPersonalizada('error', XMLHttpRequest.responseText);
                  }
                });
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
  });
  //////////////////Fin Anular Cuenta/////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Imprimir Cuenta///////////////////
  $(document).on("click",".ImprimirCuenta",function () {
    var idPedido = $(this).attr("idPedido");
    imprimirCuenta(idPedido);

  });
  $(document).on("click",".ImprimirCuenta1",function () {
    var idPedido = $(this).attr("idPedido");
    imprimirCuenta(idPedido,"1");

  });
  $(document).on("click",".CambioMesa",function () {
    var idPedido = $(this).attr("idPedido");
    $("#ordenarProductoZonaMesa").attr('idPedido',idPedido);
    $("#espacioCuentas").hide();
    $("#divDatosZonaMesa").show();
    $("#nuevaOrden").show();
    $("#nuevaOrden .scrollmenu").hide();// $("#divDireccionCliente").hide();
    // var idZona = $("#zonaHacerCuenta").val();
  });
  ////////////////Fin Imprimir Cuenta/////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Imprimir Cuenta Cancelada///////////////////
  $(document).on("click",".ReImprimirFactura",function () {
    var idPedido = $(this).attr("idFactura");
    var idPedido1 = $(this).attr("idFactura1");
    var tipo = $(this).attr("tipo");
    if(tipo == "FAC" || tipo == "CCF"){
      imprimirFacturaProducto(idPedido1);

      // window.open(url + "/FacturasDoc/" + idPedido1,"","");
    }
    else{
      imprimirTicketProducto(idPedido);
    }

  });
  ////////////////Fin Imprimir Cuenta/////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  ///////////////////Agregar a Cuenta/////////////////
  $(document).on("click",".AgregarCuenta",function () {
    var idPedido = $(this).attr("idPedido");

    var precioRegular = $(this).attr("precioRegular");
    var precioEspecial = $(this).attr("precioEspecial");
    var precioEmpleado = $(this).attr("precioEmpleado");
    $("#precioRegularZonaCuenta").val(precioRegular);
    $("#precioEspecialZonaCuenta").val(precioEspecial);
    $("#precioEmpleadoZonaCuenta").val(precioEmpleado);
    if($("#precioRegularZonaCuenta").val() == "1"){
      var tipo = "regular";
    }if($("#precioEspecialZonaCuenta").val() == "1"){
      var tipo = "especial";
    }if($("#precioEmpleadoZonaCuenta").val() == "1"){
      var tipo = "empleado";
    }
    $("#tipoPrecioActual").val(tipo);

    $("#idPedidoGuardado").val(idPedido);
    $("#AgregarCuentaLlevarLocal").val("0");
    $("#CobrarCuentaFinal").val("0");

    $("#ordenarProducto").click();
  });
  ////////////////Fin Agregar a Cuenta////////////////
  ////////////////////////////////////////////////////
  ////////////////////////////////////////////////////
  ///////////////////Agregar a Cuenta/////////////////
  $(document).on("click",".AgregarCuentaLlevarLocal",function () {
    var idPedido = $(this).attr("idPedido");

    var precioRegular = $(this).attr("precioRegular");
    var precioEspecial = $(this).attr("precioEspecial");
    var precioEmpleado = $(this).attr("precioEmpleado");
    $("#precioRegularZonaCuenta").val(precioRegular);
    $("#precioEspecialZonaCuenta").val(precioEspecial);
    $("#precioEmpleadoZonaCuenta").val(precioEmpleado);
    if($("#precioRegularZonaCuenta").val() == "1"){
      var tipo = "regular";
    }if($("#precioEspecialZonaCuenta").val() == "1"){
      var tipo = "especial";
    }if($("#precioEmpleadoZonaCuenta").val() == "1"){
      var tipo = "empleado";
    }
    $("#tipoPrecioActual").val(tipo);

    $("#idPedidoGuardado").val(idPedido);
    $("#AgregarCuentaLlevarLocal").val("1");
    $("#CobrarCuentaFinal").val("0");

    $("#ordenarProducto").click();

  });
  ////////////////Fin Agregar a Cuenta////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Cobrar Cuenta/////////////////////
  $(document).on("click",".CobrarCuenta",function () {
    $("#CobrarCuentaFinal").val("1");

    var idPedido = $(this).attr("idPedido");


    var total = 0;

    $("#tablaDetalleCuenta tbody tr.prim[regalia='No']").each(function(){
      var precio = $(this).attr("precio");
      var cantidad = $(this).attr("cantidad");
      precio = precio * cantidad;
      total = total + parseFloat(precio);
    });
    $("#idPedidoGuardado").val(idPedido);
    var totalTexto = total.toFixed(2)
    $("#totalPagoProducto").text("$"+totalTexto);
    var propina = 0.00;
    if($("#cobroPropina").val()=="Si"){
      propina = total * $("#propina").val() / 100;
      $("#propinaPagoProducto").text("$"+propina.toFixed(2));
    }
    var totalFinal = parseFloat(total) + parseFloat(propina);
    $("#totalPropPagoProducto").text("$"+totalFinal.toFixed(2));
    //$("#efectivoPagoProducto").val(totalFinal.toFixed(2));
    //		$("#cajaProducto").attr("selected",true);
    $("#cajaProducto").trigger('change');
    $(".dinamicos").hide();
    $("#nuevaOrden").show();
    $("#nuevaOrden .scrollmenu").hide();
    $("#botoneraPrimaria").hide();
    $("#abrirCuenta").hide();
    $("#pagarProducto").hide();
    $("#agregarACuenta").hide();
    $("#finalizarCuenta").show();
    $("#divPagoProducto").show();

    $("#detalleCuenta").show();
    $("#accionesCuentaDetalle").show();
    if (total >= 0.00) {
      $("#pagarProducto").attr("disabled", false);
      $("#finalizarCuenta").attr("disabled", false);
    }
    //calcularTotalPago();;
    $("#cajaProducto").focus();
    //calcularTotalPago();;

    $.ajax({
      url: url+"/Touch/BuscarCliente",
      type: 'POST',
      dataType: 'json',
      data:
      {
        idPedido: idPedido
      },
      success: function(data)
      {
        $("#idClienteProducto").val(data.idCliente);
        $("#nombreClientePagoProducto").val(data.nombreCliente);
        $("#direccionClientePagoProducto").val(data.direccionCliente);
        $("#telefonoClientePagoProducto").val(data.telefonoCliente);
        $("#correoClientePagoProducto").val(data.correoCliente);
        $("#departamentoClientePagoProducto").val(data.departamentoCliente);
        cargarMunicipiosPagoProducto(data.departamentoCliente, data.municipioCliente);
        $("#duiClientePagoProducto").val(data.duiCliente);
        $("#nitClientePagoProducto").val(data.nitCliente);
        $("#nrcClientePagoProducto").val(data.nrcCliente);
        $("#giroClientePagoProducto").val(data.giroCliente);

        $('#clienteProducto').typeahead('val', data.nombreCliente);
        $(".editarCliente").attr("idCliente",data.idmd);
        if(data.idmd != ""){
          $(".editarCliente").removeAttr("hidden");
        }
      }
    });
    setTimeout(function(){
      calculoTotal();
    },200);

  });
  //////////////////Fin Cobrar Cuenta/////////////////
  ////////////////////////////////////////////////////

  ////////////////////////////////////////////////////
  //////////////////Editar Tipo Cuenta////////////////
  $(document).on("click",".editarTipoCuenta",function () {
    $(".banderaAccionPasos").val("1");
    $(".tipoOrden").show();
    //alert();
  });
  //////////////////Fin Editar Tipo Cuenta////////////
  ////////////////////////////////////////////////////


  /*****************************************************/
  /*****************************************************/
  /*******************FIN CUENTAS***********************/
  /*****************************************************/
  /*****************************************************/


  /***********************************/
  /***********************************/
  $("#btnMesa").click(function () {
    $(".dinamicos").hide();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    // $("#espacioOrdenes").show();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  /***********************************/
  /***********************************/
  $("#ordenarProductoZonaMesa").click(function () {
    if($(this).attr('idPedido') != ""){
      var idZona = $("#idZonaCuenta").val();
      var nombre = $("#nombreZonaCuenta").val();
      var idMesa = $("#mesaHacerCuenta").val();
      var idPedido = $(this).attr('idPedido');
      $.ajax({
        type: "POST",
        url: "CambioMesa",
        data: {
          idPedido: idPedido,
          idZona: idZona,
          idMesa: idMesa,
          nombre: nombre,
        },
        dataType: 'json',
        success: function (respuesta) {
          if (respuesta.codigo == 200) {
            $("#btnCuenta").click();
            setTimeout(function(){
              $("#cuenta"+idPedido).click();
            }, 200);
          }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          AlertaPersonalizada('error', XMLHttpRequest.responseText);
        }
      });
    } else {
      $("#botoneraPrimaria").hide();
      $("#divDatosZonaMesa").hide();
      $("#divDatosCliente").show();
      var tipo = $("#tipoPrecioActual").val();
      if(tipo == "regular"){
        $("#btnRegular").click();
      }if(tipo == "especial"){
        $("#btnEspecial").click();
      }if(tipo == "empleado"){
        $("#btnEmpleado").click();
      }
    }
    $(this).attr('idPedido','');
  });
  /***********************************/
  /***********************************/
  // $("#ordenarProductoZonaMesa").click(function () {
  //   $("#botoneraPrimaria").hide();
  //   $("#divDatosZonaMesa").hide();
  //   $("#divDatosCliente").show();
  //   var tipo = $("#tipoPrecioActual").val();
  //   if(tipo == "regular"){
  //     $("#btnRegular").click();
  //   }if(tipo == "especial"){
  //     $("#btnEspecial").click();
  //   }if(tipo == "empleado"){
  //     $("#btnEmpleado").click();
  //   }
  // });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#ordenarProducto").click(function () {

    $(".dinamicos").hide();
    $("#botoneraHome").show();
    $("#botoneraPrimaria").hide();
    $("#divPagoProducto").hide();
    $("#divHacerCuenta").hide();
    $("#totalesNormal").attr("hidden",false);
    var idCaja = $("#idCaja").val();
    $("#cajaProducto option[value='"+idCaja+"']").attr("selected",true);
    $("#cajaProducto").val(idCaja).trigger('change');

    if($("#diferentesPrecios").val() == "0"){
      $("#listaOrden").show();
      $("#nuevaOrden").show();
      $("#totalProductos").show();
      $("#nuevaOrden .scrollmenu").show();

    }
    else{
      $("#botoneraPrecios").show();
    }
    var tipo = $("#tipoPrecioActual").val();
    if(tipo == "regular"){
      $("#btnRegular").click();
    }if(tipo == "especial"){
      $("#btnEspecial").click();
    }if(tipo == "empleado"){
      $("#btnEmpleado").click();
    }

  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnLlevar").click(function () {

    $(".dinamicos").hide();
    $("#divPagoProducto").hide();
    $("#divHacerCuenta").hide();
    $("#tipoHacerCuenta option[value='llevar']").attr("selected",true);
    $("#tipoHacerCuenta").val("llevar").trigger('change');
    $("#totalesNormal").attr("hidden",false);
    var idCaja = $("#idCaja").val();
    $("#cajaProducto option[value='"+idCaja+"']").attr("selected",true);
    $("#cajaProducto").val(idCaja).trigger('change');
    if($("#cuentas").val() == "1"){
      $("#divDatosCliente").show();
      $("#nuevaOrden").show();
      $("#nuevaOrden .scrollmenu").hide();
    }
    else{
      if($("#diferentesPrecios").val() == "0"){
        $("#listaOrden").show();
        $("#nuevaOrden").show();
        $("#totalProductos").show();
      }
      else{
        $("#botoneraPrecios").show();
      }
    }
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    $(".pasos").show();

    $(".mesaOrden").hide();
    var tipo = $("#tipoPrecioActual").val();
    if(tipo == "regular"){
      $("#btnRegular").click();
    }if(tipo == "especial"){
      $("#btnEspecial").click();
    }if(tipo == "empleado"){
      $("#btnEmpleado").click();
    }
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnLocal").click(function () {

    $(".dinamicos").hide();
    $("#divPagoProducto").hide();
    $("#divHacerCuenta").hide();
    $("#tipoHacerCuenta option[value='local']").attr("selected",true);
    $("#tipoHacerCuenta").val("local").trigger('change');
    $("#totalesNormal").attr("hidden",false);
    var idCaja = $("#idCaja").val();
    $("#cajaProducto option[value='"+idCaja+"']").attr("selected",true);
    $("#cajaProducto").val(idCaja).trigger('change');

    if($("#cuentas").val() == "1"){
      $("#divDatosZonaMesa").show();
      $("#nuevaOrden").show();
      $("#nuevaOrden .scrollmenu").hide();
    }
    else{
      if($("#diferentesPrecios").val() == "0"){
        $("#listaOrden").show();
        $("#nuevaOrden").show();
        $("#totalProductos").show();
        $(".elementosOrden").click();
      }
      else{
        $("#botoneraPrecios").show();
      }
    }
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    $(".pasos").show();
    var tipo = $("#tipoPrecioActual").val();
    if(tipo == "regular"){
      $("#btnRegular").click();
    }if(tipo == "especial"){
      $("#btnEspecial").click();
    }if(tipo == "empleado"){
      $("#btnEmpleado").click();
    }

  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnDomicilio").click(function () {

    $(".dinamicos").hide();
    $("#divPagoProducto").hide();
    $("#divHacerCuenta").hide();
    $("#tipoHacerCuenta option[value='domicilio']").attr("selected",true);
    $("#tipoHacerCuenta").val("domicilio").trigger('change');
    $("#totalesNormal").attr("hidden",false);
    var idCaja = $("#idCaja").val();
    $("#cajaProducto option[value='"+idCaja+"']").attr("selected",true);
    $("#cajaProducto").val(idCaja).trigger('change');

    if($("#cuentas").val() == "1"){
      $("#divDatosCliente").show();
      $("#nuevaOrden").show();
      $("#nuevaOrden .scrollmenu").hide();
    }
    else{
      if($("#diferentesPrecios").val() == "0"){
        $("#listaOrden").show();
        $("#nuevaOrden").show();
        $("#totalProductos").show();
      }
      else{
        $("#botoneraPrecios").show();
      }
    }
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    $(".pasos").show();
    $(".mesaOrden").hide();
    var tipo = $("#tipoPrecioActual").val();
    if(tipo == "regular"){
      $("#btnRegular").click();
    }if(tipo == "especial"){
      $("#btnEspecial").click();
    }if(tipo == "empleado"){
      $("#btnEmpleado").click();
    }

  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnRecoger").click(function () {

    $(".dinamicos").hide();
    $("#divPagoProducto").hide();
    $("#divHacerCuenta").hide();
    $("#tipoHacerCuenta option[value='recoger']").attr("selected",true);
    $("#tipoHacerCuenta").val("recoger").trigger('change');
    $("#totalesNormal").attr("hidden",false);
    var idCaja = $("#idCaja").val();
    $("#cajaProducto option[value='"+idCaja+"']").attr("selected",true);
    $("#cajaProducto").val(idCaja).trigger('change');

    if($("#cuentas").val() == "1"){
      $("#divDatosCliente").show();
      $("#nuevaOrden").show();
      $("#nuevaOrden .scrollmenu").hide();
    }
    else{
      if($("#diferentesPrecios").val() == "0"){
        $("#listaOrden").show();
        $("#nuevaOrden").show();
        $("#totalProductos").show();
      }
      else{
        $("#botoneraPrecios").show();
      }
    }
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    $(".pasos").show();
    $(".mesaOrden").hide();
    var tipo = $("#tipoPrecioActual").val();
    if(tipo == "regular"){
      $("#btnRegular").click();
    }if(tipo == "especial"){
      $("#btnEspecial").click();
    }if(tipo == "empleado"){
      $("#btnEmpleado").click();
    }

  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $(".tipoPrecio").click(function () {
    $(".dinamicos").hide();

    var tipo =  $(this).attr("tipo");
    $("#tipoPrecioActual").val(tipo);
    $("#botoneraPrecios").hide();
    $("#listaOrden").show();
    $("#nuevaOrden").show();
    $("#totalProductos").show();
    $("#divPagoProducto").hide();
    $("#divHacerCuenta").hide();
    $("#nuevaOrden .scrollmenu").show();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    $("a.categoria[idc='T']").click();

  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnAgrupar").click(function () {
    grupoGeneral += 1 ;
    grupoActual = grupoGeneral;
    verGrupoProductos(grupoActual);
    $("#listaProductos tr.prim").each(function(){
      var grupo = parseInt($(this).attr("grupo"));
      //$(this).attr("grupo",grupo + 1);
    });
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnServicio").click(function () {

    $(".dinamicos").hide();
    $("#nuevoServicio").show();
    //$("#servicioPendiente").hide();
    $("#listaServicio").show();
    $("#listaServicios").show();
    $("#totalServicios").show();
    $("#divPagoServicio").hide();
    $("#cajaServicio option[value='2']").attr("selected",true);
    $("#cajaServicio").val("2").trigger('change');
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $(".servicioNuevo").click(function () {
    $(".dinamicos").hide();
    $("#nuevoServicio").show();
    //$("#servicioPendiente").hide();
    $("#listaServicio").show();
    $("#listaServicios").show();
    $("#totalServicios").show();
    $("#divPagoServicio").hide();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();

  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnMovEntrada").click(function () {

    $(".dinamicos").hide();
    $("#tipoMovimiento").val("Entrada");
    $("#labelTipoMov").text("Entrada de efectivo");
    $("#espacioMovimientos").show();
    //$("#cajaMovimiento").attr("selected",true);
    $("#cajaMovimiento").change();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    //FormatoDatos();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnMovSalida").click(function () {

    $(".dinamicos").hide();
    $("#tipoMovimiento").val("Salida");
    $("#labelTipoMov").text("Salida de efectivo");
    $("#espacioMovimientos").show();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
    //FormatoDatos();
  });
  /***********************************/
  /***********************************/

  /***********************************/
  /***********************************/
  $("#btnCover").click(function () {

    $(".dinamicos").hide();
    $("#listaCover").show();
    $("#listaCoverMostrar").show();
    $("#nuevoCover").show();
    $("#totalCover").show();
    $("#botoneraPrimaria").hide();
    $("#botoneraHome").show();
  });
  /***********************************/
  /***********************************/


  /************    servicios    *************/

  $(".servicioCategoria").click(function () {
    $(".servicioCategoria").removeClass('btn-success');
    $(".servicioCategoria").addClass('btn-default');
    $(this).toggleClass('btn-success btn-default');
    var idCategoria = $(this).attr("idc");
    var dataString = "idCategoria=" + idCategoria + "&csrf_test_name=" + token;
    $.ajax({
      type: "POST",
      url: "TraerServicioCategoria",
      data: dataString,
      dataType: 'json',
      success: function (respuesta) {
        if (respuesta.codigo == 200) {
          $("#listaServiciosMostrar").show();
          $("#listaSenoritasMostrar").hide();
          $("#divPagoServicio").hide();
          $("#listaNombresSenoritasMostrar").hide();

          $("#listaServiciosMostrar").html(respuesta.div);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  });
  $("#cajaServicio").change(function () {

    var idCaja = $(this).val();
    var dataString = "idCaja=" + idCaja + "&csrf_test_name=" + token;
    $.ajax({
      type: "POST",
      url: "TraerCaja",
      data: dataString,
      dataType: 'json',
      success: function (respuesta) {
        if (respuesta.codigo == 200) {
          $("#tipoPagoServicio").html(respuesta.div);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  });

  $("#buscarClienteServicio").change(function () {
    var nombre = $(this).find(":selected").attr("nombre");
    $("#clienteServicio").val(nombre);
  });

  $("#efectivoPagoServicio").on("keyup",function () {
    var total = parseFloat($("#totalPagoServicio").text().replace("$", ''));
    var monto = $(this).val();
    if(monto == ''){monto = 0;}
    var vuelto = (monto - total).toFixed(2);

    if (vuelto >= 0.00 && total > 0.00) { $("#pagarServicio").attr("disabled", false); }
    else { $("#pagarServicio").attr("disabled", true); }
    (vuelto < 0) ? vuelto =  "-"+parseFloat(-1 * vuelto).toFixed(2) : parseFloat(vuelto).toFixed(2);
    $("#vueltoPagoServicio").text(vuelto);
  });

  $("#descuentoPagoProducto").on("focus",function () {

    // <div class="form-group">
    //   <label for="correlativoClientePagoProducto">Correlativo</label>
    //   <div class="input-group mb-3">
    //     <input type="text" class="form-control upper tecladoPantalla" name="correlativoClientePagoProducto" id="correlativoClientePagoProducto" placeholder="Correlativo">
    //     <div class="input-group-append">
    //       <a class="btn btn-default tecladoOpener" data-target="correlativoClientePagoProducto"><i class="fa fa-keyboard"></i></a>
    //     </div>
    //   </div>
    // </div>
    var inpt = '<label for="authdescpo">Ingrese Codigo de Administrador</label>';
    inpt+= '<div class="input-group mb-3">';
    inpt+= '  <input type="password" class="form-control decimal siguiente" name="authdescpo" id="authdescpo" placeholder="">';
    inpt+= '  <div class="input-group-append">';
    inpt+= '    <a class="btn btn-default authdescpo-opener"><i class="fa fa-keyboard"></i></a>';
    inpt+= '  </div>';
    inpt+= '</div>';
    if($("#descuentoPagoProducto").attr("permiso") == "0"){
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
            $.ajax({
              type: "POST",
              url: url+"/ValidarPermiso",
              data: {clave:clave},
              dataType: 'json',
              success: function (respuesta){
                var respuesta = respuesta.bandera;
                if(respuesta == "1"){
                  $("#descuentoPagoProducto").attr("permiso","1");
                  $("#descuentoDolarPagoProducto").attr("permiso","1");
                  $("#descpor").html('<a class="btn btn-default descuentopor-opener"><i class="fa fa-keyboard"></i></a>');
                  $("#descdin").html('<a class="btn btn-default descuentodin-opener"><i class="fa fa-keyboard"></i></a>');
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
    }
  });

  $("#descuentoDolarPagoProducto").on("focus",function () {
    var inpt = '<label for="authdescpo">Ingrese Codigo de Administrador</label>';
    inpt+= '<div class="input-group mb-3">';
    inpt+= '  <input type="password" class="form-control decimal siguiente" name="authdescpo" id="authdescpo" placeholder="">';
    inpt+= '  <div class="input-group-append">';
    inpt+= '    <a class="btn btn-default authdescpo-opener"><i class="fa fa-keyboard"></i></a>';
    inpt+= '  </div>';
    inpt+= '</div>';
    if($("#descuentoDolarPagoProducto").attr("permiso") == "0"){
      Swal.fire({
        title: "Alerta!",
        // text: "Ingrese Codigo de Administrador",
        // input: 'text',
        html: inpt,
        showCancelButton: true,
        // inputPlaceholder: "Codigo"
      }).then((result) => {
        if(result.isDismissed == false){
          // if (result.value!= "") {
          if ($("#authdescpo").val() != "") {
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
                  $("#descuentoPagoProducto").attr("permiso","1");
                  $("#descuentoDolarPagoProducto").attr("permiso","1");
                  $("#descdin").html('<a class="btn btn-default descuentodin-opener"><i class="fa fa-keyboard"></i></a>');
                  $("#descpor").html('<a class="btn btn-default descuentopor-opener"><i class="fa fa-keyboard"></i></a>');
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
    }
  });

  $("#descuentoPagoProducto").on("keyup change",function () {
    calcularDescuentos("descuentoPagoProducto");
  });

  $("#descuentoDolarPagoProducto").on("keyup change",function () {
    // var monto = $(this).val();
    //calculoTotal();
    calcularDescuentos("descuentoDolarPagoProducto");

  });

  /************    Productos   *************/

  $(".categoria").click(function () {
    $("#divDatosDoc").hide();
    $("#divPagoProducto").hide();
    $("#divHacerCuenta").hide();
    var idCategoria = $(this).attr("idc");
    var tipoPrecio = $("#tipoPrecioActual").val();
    var dataString = "idCategoria=" + idCategoria + "&tipoPrecio=" + tipoPrecio +  "&csrf_test_name=" + token;
    $.ajax({
      type: "POST",
      url: "TraerProductoCategoria",
      data: dataString,
      dataType: 'json',
      success: function (respuesta) {
        // Alerta(respuesta.codigo);
        if (respuesta.codigo == 200) {
          $("#listaProductosMostrar").html(respuesta.div);
          $("#listaProductosMostrar").show();
          $("#modificadores").hide();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
    //var padre =  $(this).parent().parent().parent().parent();
    $(".categoria").removeClass('bg-success');
    $(".categoria").addClass('bg-default');
    $(this).toggleClass('bg-success bg-default');
  });

  $("#cajaProducto").change(function () {

    var idCaja = $(this).val();
    var dataString = "idCaja=" + idCaja + "&csrf_test_name=" + token;
    $.ajax({
      type: "POST",
      url: "TraerCaja",
      data: dataString,
      dataType: 'json',
      success: function (respuesta) {
        if (respuesta.codigo == 200) {
          $("#tipoPagoProducto").html(respuesta.div);
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  });

  $("#buscarClienteProducto").change(function () {
    var nombre = $(this).find(":selected").attr("nombre");
    $("#clienteProducto").val(nombre);
  });
  $("#tipoPagoProducto").change(function () {
    $.ajax({
      type: "POST",
      url: "TraerCorrelativo/"+$(this).val(),
      dataType: 'JSON',
      success: function (respuesta) {
        if (respuesta.codigo == 200) {
          $("#correlativoClientePagoProducto").val(respuesta.last);
        }
      }
    });
  });

  $(document).on("keyup change","#efectivoPagoProducto, #tarjetaPagoProducto, #bitcoinPagoProducto, #pedidosYaPagoProducto, #transferenciaProducto, #envioProducto",function() {
    setTimeout(function(){
      calculoTotal();
    },200);
  });
  // $("#efectivoPagoProducto").keyup(function () {
  // 	setTimeout(function(){
  // 		calculoTotal();
  // 	},200);
  // });
  //
  // $("#tarjetaPagoProducto").keyup(function () {
  // 	setTimeout(function(){
  // 		calculoTotal();
  // 	},200);
  // });
  //
  // $("#bitcoinPagoProducto").keyup(function () {
  // 	setTimeout(function(){
  // 		calculoTotal();
  // 	},200);
  // });
  //
  // $("#pedidosYaPagoProducto").keyup(function () {
  // 	setTimeout(function(){
  // 		calculoTotal();
  // 	},200);
  // });

  $("#tipoHacerCuenta").change(function () {
    if($(this).val() == 'domicilio'){
      $("#divVerificarZonaDelivery").show();
    } else {
      $("#divVerificarZonaDelivery").hide();
    }
    if($(this).val() == 'local'){
      $("#divZonaCuenta").show();
      $("#divDireccionCliente").hide();
      var idZona = $("#zonaHacerCuenta").val();
      // $.ajax({
      // 	type: "POST",
      // 	url: url + "/TraerMesasZona",
      // 	data: {
      // 		idZona : idZona
      // 	},
      // 	dataType: 'json',
      // 	success: function (respuesta) {
      // 		$("#divMesasCuenta").html(respuesta.div);
      // 		$("#divMesasCuenta").show();
      // 	},
      // 	error: function (XMLHttpRequest, textStatus, errorThrown) {
      // 		AlertaPersonalizada('error', XMLHttpRequest.responseText);
      // 	}
      // });
    }
    else{
      $("#divZonaCuenta").hide();
      $("#divMesasCuenta").hide();
      $("#divDireccionCliente").show();

    }
  });

  $("#zonaHacerCuenta").change(function () {
    $("#divZonaCuenta").attr("hidden",false);
    $("#divMesasCuenta").attr("hidden",false);
    var idZona = $("#zonaHacerCuenta").val();
    $.ajax({
      type: "POST",
      url: url + "/TraerMesasZona",
      data: {
        idZona : idZona
      },
      dataType: 'json',
      success: function (respuesta) {
        $("#divMesasCuenta").html(respuesta.div);
        $("#divMesasCuenta").show();
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  });
});

/***************************************************************************/
/*****************************UTILITARIOS***********************************/
/***************************************************************************/

$(document).on("click",".softkeys",function(){
  if($(this).data("target") == "#efectivoPagoProducto"){
    setTimeout(function(){
      calculoTotal();
    },200);
  }
});

$(document).on("click",".softkeyss",function(){
  if($(this).data("target") == "#efectivoPagoServicio"){
    $('#efectivoPagoServicio').keyup();
  }
});

$(document).on("click",".tecladoOpener",function () {
  var id = $(this).data("target");
  var kb = $("#"+id).getkeyboard();
  // close the keyboard if the keyboard is visible and the button is clicked a second time
  if (kb.isOpen) { kb.close(); } else { kb.reveal(); }
});

/***************************************************************************/
/************************SERVICIOS Y SEÑORITAS******************************/
/***************************************************************************/

$(document).on("click", ".servicio", function () {
  var idCategoria = $(this).attr("idc");
  var idServicio = $(this).attr("idp");
  var dataString = "idCategoria=" + idCategoria + "&idServicio=" + idServicio + "&csrf_test_name=" + token;
  $.ajax({
    type: "POST",
    url: "TraerSenoritaCategoria",
    data: dataString,
    dataType: 'json',
    success: function (respuesta) {
      if (respuesta.codigo == 200) {
        $("#listaServiciosMostrar").hide();
        $("#listaSenoritasMostrar").show();
        $("#listaSenoritasMostrar").html(respuesta.div);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });
});

$(document).on("click", ".senorita", function () {

  var idSenoritaCategoria = $(this).attr("idp");
  var idServicio = $(this).attr("idser");
  var idServicioCategoria = $(this).attr("idcli");
  var precio = $(this).find(".precio").val();
  var nombre = $(this).find("p").text();
  var nombre2 = $(this).find(".nombre").val();

  var dataString = "idCategoria=" + idSenoritaCategoria + "&idServicio=" + idServicio + "&idServicioCategoria=" + idServicioCategoria + "&nombre=" + nombre + "&nombre2=" + nombre2 + "&precio=" + precio + "&csrf_test_name=" + token;
  $.ajax({
    type: "POST",
    url: "TraerSenoritaOption",
    data: dataString,
    dataType: 'json',
    success: function (respuesta) {
      if (respuesta.codigo == 200) {
        $("#listaServiciosMostrar").hide();
        //$("#listaSenoritasMostrar").hide();

        $("#listaNombresSenoritasMostrar").show();
        $("#listaNombresSenoritasMostrar").html(respuesta.div);
      }
      else {
        $("#listaNombresSenoritasMostrar").html('');
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });

});

var iteracios = 0;
$(document).on("click", ".nombreSenorita", function () {
  var precio = $(this).find(".precio").val();
  var nombre = $(this).find(".nombre").val();
  var nombre2 = $(this).find(".nombre2").val();
  var idSenorita = $(this).find(".idSenorita").val();
  var idSenoritaCategoria = $(this).find(".idSenoritaCategoria").val();
  var idServicio = $(this).find(".idServicio").val();
  var idServicioCategoria = $(this).find(".idServicioCategoria").val();

  var tr = "<tr class='prim idt" + iteracios + " accordion-toggle collapsed' data-toggle='collapse' href='#ita" + iteracios + "'  idt='" + iteracios + "'>";
  tr += "<td class='cantidad' id='canttxt" + iteracios + "'>1</td>";
  tr += "<td>" + nombre2 + "</td>";
  tr += "<td class='pco'>$" + precio + "</td>";
  tr += "<input type='hidden' class='info' idServicioCatgoria='" + idServicioCategoria + "' idServicio='" + idServicio + "' idSenoritaCategoria='" + idSenoritaCategoria + "' idSenorita='" + idSenorita + "' nombre='" + nombre2 + "'  precio='" + precio + "'  value=''>";
  tr += "<td><a class='btn btn-danger btn-sm dells' style='margin-left:10%;' idt='" + iteracios + "'><i class='fa fa-trash'></i></a></td>";
  tr += "</tr>";

  $("#listaServicios").append(tr);
  calculoTotals();
  iteracios++;
});

/*****************************************************************************/
/*********************************PRODUCTOS***********************************/
/*****************************************************************************/


$(document).on('keydown', '.barcode', function(event) {
  if (event.keyCode==13)
  {
    barcode = $(this).val();
    $(this).val("");

    $.ajax({
      url: url+"/Touch/BuscarBarcode",
      type: 'POST',
      dataType: 'json',
      data: {
        barcode: barcode,
        "csrf_test_name" : token
      },
      success: function(xdatos)
      {
        if(xdatos.existe=='Si')
        {
          var idProducto = xdatos.idP;
          var precio = xdatos.precioVentaProducto;
          var precioEspecial = xdatos.precioVentaProducto;
          var nombre = xdatos.nombre;
          var tipo = $("#tipoPrecioActual").val();

          var datos = {
            "idProducto" : idProducto,
            "nivel" : 1,
            "iteracion" : iteracion,
            "csrf_test_name" : token
          }
          $.ajax({
            type: "POST",
            url: url + "/TraerProductoModificadores",
            data: datos,
            dataType: "json",
            success: function (response) {
              //$("#listaProductosMostrar").hide();
              //$("#modificadores").show();
              //$("#precioProducto").text("$"+precio).attr("precioOriginal",precio).attr("precio",precio);
              //$("#listaModificadores").html("").append(response.div);
              //$(".detalle"+iteracion+" .modificadoresTipo").append(response.modTipo);
              $("#generalModificadores").append(response.modTipo);
              $("div.detalle"+iteracion+" label.nombreProducto").text(nombre).attr("idProducto",idProducto);

              var tr = "<tr class='prim idt" + iteracion + "' grupo='"+grupoActual+"' idp='" + idProducto + "' idt='" + iteracion + "'>";
              tr += "	<td id='canttxt" + iteracion + "'>1</td>";
              tr += "	<td>" + nombre + "</td>";
              tr += "	<td id='preciotxt" + iteracion + "'>$" + precio + "</td>";
              tr += ' <td><a class="btn btn-sm btn-block btn-warning btnDescuentoLinea" idt="'+ iteracion +'"><i class="fa fa-percent"></i></a></td>';
              tr += ' <td><a class="btn btn-sm btn-block btn-primary" data-toggle="collapse" href="#ita'+ iteracion +'"><i class="fa fa-eye"></i></a></td>';
              if($("#idUsuarioCorte").val() == $("#idUsuarioSesion").val()){
                tr += ' <td><div class="icheck-success d-inline"><input type="checkbox" class="regalia" idt="'+ iteracion +'" precio="' + precio + '" id="regalia'+ iteracion +'" ><label for="regalia'+ iteracion +'"></label></div></td>';
              } else {
                tr += "<td></td>";
              }

              tr += "</tr>";
              tr += "<tr class='sec idt" + iteracion + " hide-table-padding'>";
              tr += "	<td colspan='6'>";
              tr += "		<div id='ita" + iteracion + "' class='collapse in p-0'>";
              tr += "			<div class=''>";
              tr += "				<div class='input-group'><button class='btn btn-danger btn-sm minus' it='" + iteracion + "'><i class='fa fa-minus'></i></button>";
              tr += "				<input type='hidden' value='"+tipo+"' class='tipoProductoDetalle' id='tipoPD" + iteracion + "'>";
              tr += "				<input type='hidden' value='"+response.cocinero+"' class='cocinero' id='coci" + iteracion + "'>";
              tr += "					<input type='text' value='1' class='cantidad' regalia='0' descuentoLinea='0' idt='" + iteracion + "' precioOriginal='" + precio + "' precio='" + precio + "' style='width:50px; text-align:center;' readonly id='cantidad" + iteracion + "'>";
              tr += "					<button class='btn btn-primary btn-sm plus' it='" + iteracion + "'><i class='fa fa-plus'></i></button>";
              tr += "					<a class='btn btn-danger btn-sm dell' style='margin-left:20px;' idt='" + iteracion + "'><i class='fa fa-trash'></i></a>";
              tr += "				</div>";
              tr += "				";
              tr += "			</div><hr>";
              tr += "			<div class='listaModificadores "+response.contenedor+iteracion+" col-12 mt-2'></div>";
              // if($("#servicioSenorita").val() == "1"){
              // 	if($("#tipoPrecioActual").val() == 'especial'){
              // 		tr += "			<div class='col-12'>";
              // 		tr += "				<label>Señorita:</label>";
              // 		tr += "				<select id='senorita"+iteracion+"' class='select2 col-12'>";
              // 		tr += "					"+response.option+"'";
              // 		tr += "				</select>";
              // 		tr += "			</div>";
              // 	}
              // }
              if(response.cocinero == "1"){
                tr += "			<div class='col-12'>";
                tr += "				<label>Cocinero:</label>";
                tr += "				<select id='cocinero"+iteracion+"' class='select2 col-12'>";
                tr += "					"+response.optionCo+"'";
                tr += "				</select>";
                tr += "			</div>";
              }
              tr += "			<div class='col-12'>";
              tr += "				<label>Comentario:</label>";
              tr += "				<div class='input-group mb-3'>";
              tr += "					<input type='text' id='comentario"+iteracion+"' class='form-control tecladoPantalla' >";
              tr += "					<div class='input-group-append'>";
              tr += "						<a class='btn btn-default tecladoOpener' data-target='comentario"+iteracion+"'><i class='fa fa-keyboard'></i></a>	";
              tr += "					</div>";
              tr += "				</div>";
              tr += "			</div>";
              tr += "		</div>";
              tr += "	</td>";
              tr += "</tr>";
              $("#listaProductos").append(tr);

              //$(tr).appendTo("#listaProductos );
              $("."+response.contenedor+iteracion).html(response.lista);
              setTimeout(function(){
                calculoTotal();
              },200);
              //$(".prim.idt" + iteracion + "").click();
              FormatoDatos();

              iteracion++;
            }
          });
        }
        else {
          AlertaPersonalizada('error', "No se encontro el barcode");
        }
      }

    })

  }
});



var iteracion = 0;
$(document).on("click", ".producto", function () {
  var idProducto = $(this).attr("idp");
  var precio = $(this).find(".precio").val();
  var precioEspecial = $(this).find(".precioEspecial").val();
  var nombre = $(this).find("p.nombre").text();
  var tipo = $("#tipoPrecioActual").val();

  var datos = {
    "idProducto" : idProducto,
    "nivel" : 1,
    "iteracion" : iteracion,
    "csrf_test_name" : token
  }
  $.ajax({
    type: "POST",
    url: url + "/TraerProductoModificadores",
    data: datos,
    dataType: "json",
    success: function (response) {
      //$("#listaProductosMostrar").hide();
      //$("#modificadores").show();
      //$("#precioProducto").text("$"+precio).attr("precioOriginal",precio).attr("precio",precio);
      //$("#listaModificadores").html("").append(response.div);
      //$(".detalle"+iteracion+" .modificadoresTipo").append(response.modTipo);
      $("#generalModificadores").append(response.modTipo);
      $("div.detalle"+iteracion+" label.nombreProducto").text(nombre).attr("idProducto",idProducto);

      var tr = "<tr class='prim idt" + iteracion + "' grupo='"+grupoActual+"' idp='" + idProducto + "' idt='" + iteracion + "'>";
      tr += "	<td id='canttxt" + iteracion + "'>1</td>";
      tr += "	<td>" + nombre + "</td>";
      tr += "	<td id='preciotxt" + iteracion + "'>$" + precio + "</td>";
      tr += ' <td><a class="btn btn-sm btn-block btn-warning btnDescuentoLinea" idt="'+ iteracion +'"><i class="fa fa-percent"></i></a></td>';
      tr += ' <td><a class="btn btn-sm btn-block btn-primary" data-toggle="collapse" href="#ita'+ iteracion +'"><i class="fa fa-eye"></i></a></td>';
      if($("#idUsuarioCorte").val() == $("#idUsuarioSesion").val()){
        tr += ' <td><div class="icheck-success d-inline"><input type="checkbox" class="regalia" idt="'+ iteracion +'" precio="' + precio + '" id="regalia'+ iteracion +'" ><label for="regalia'+ iteracion +'"></label></div></td>';
      } else {
        tr += "<td></td>";
      }

      tr += "</tr>";
      tr += "<tr class='sec idt" + iteracion + " hide-table-padding'>";
      tr += "	<td colspan='6'>";
      tr += "		<div id='ita" + iteracion + "' class='collapse in p-0'>";
      tr += "			<div class=''>";
      tr += "				<div class='input-group'><button class='btn btn-danger btn-sm minus' it='" + iteracion + "'><i class='fa fa-minus'></i></button>";
      tr += "				<input type='hidden' value='"+tipo+"' class='tipoProductoDetalle' id='tipoPD" + iteracion + "'>";
      tr += "				<input type='hidden' value='"+response.cocinero+"' class='cocinero' id='coci" + iteracion + "'>";
      tr += "					<input type='text' value='1' class='cantidad' regalia='0' descuentoLinea='0' idt='" + iteracion + "' precioOriginal='" + precio + "' precio='" + precio + "' style='width:50px; text-align:center;' readonly id='cantidad" + iteracion + "'>";
      tr += "					<button class='btn btn-primary btn-sm plus' it='" + iteracion + "'><i class='fa fa-plus'></i></button>";
      tr += "					<a class='btn btn-danger btn-sm dell' style='margin-left:20px;' idt='" + iteracion + "'><i class='fa fa-trash'></i></a>";
      tr += "				</div>";
      tr += "				";
      tr += "			</div><hr>";
      tr += "			<div class='listaModificadores "+response.contenedor+iteracion+" col-12 mt-2'></div>";
      // if($("#servicioSenorita").val() == "1"){
      // 	if($("#tipoPrecioActual").val() == 'especial'){
      // 		tr += "			<div class='col-12'>";
      // 		tr += "				<label>Señorita:</label>";
      // 		tr += "				<select id='senorita"+iteracion+"' class='select2 col-12'>";
      // 		tr += "					"+response.option+"'";
      // 		tr += "				</select>";
      // 		tr += "			</div>";
      // 	}
      // }
      if(response.cocinero == "1"){
        tr += "			<div class='col-12'>";
        tr += "				<label>Cocinero:</label>";
        tr += "				<select id='cocinero"+iteracion+"' class='select2 col-12'>";
        tr += "					"+response.optionCo+"'";
        tr += "				</select>";
        tr += "			</div>";
      }
      tr += "			<div class='col-12'>";
      tr += "				<label>Comentario:</label>";
      tr += "				<div class='input-group mb-3'>";
      tr += "					<input type='text' id='comentario"+iteracion+"' class='form-control tecladoPantalla' >";
      tr += "					<div class='input-group-append'>";
      tr += "						<a class='btn btn-default tecladoOpener' data-target='comentario"+iteracion+"'><i class='fa fa-keyboard'></i></a>	";
      tr += "					</div>";
      tr += "				</div>";
      tr += "			</div>";
      tr += "		</div>";
      tr += "	</td>";
      tr += "</tr>";
      $("#listaProductos").append(tr);

      //$(tr).appendTo("#listaProductos );
      $("."+response.contenedor+iteracion).html(response.lista);
      setTimeout(function(){
        calculoTotal();
      },200);
      //$(".prim.idt" + iteracion + "").click();
      FormatoDatos();

      iteracion++;
    }
  });

});


$(document).on("click",".zonas",function(){
  var idZona = $(this).attr("idz");
  var nombre = $(this).find(".nombre").val();
  var aumento = $(this).find(".aumento").val();
  var tipoAumento = $(this).find(".tipoAumento").val();
  var precioRegular = $(this).find(".precioRegular").val();
  var precioEspecial = $(this).find(".precioEspecial").val();
  var precioEmpleado = $(this).find(".precioEmpleado").val();
  $("#idZonaCuenta").val(idZona);
  $("#nombreZonaCuenta").val(nombre);
  $("#aumentoZonaCuenta").val(aumento);
  $("#tipoAumentoZonaCuenta").val(tipoAumento);
  $("#precioRegularZonaCuenta").val(precioRegular);
  $("#precioEspecialZonaCuenta").val(precioEspecial);
  $("#precioEmpleadoZonaCuenta").val(precioEmpleado);

  $("#divZonaCuenta").attr("hidden",false);
  $("#divMesasCuenta").attr("hidden",false);
  if($("#precioRegularZonaCuenta").val() == "1"){
    var tipo = "regular";
  }if($("#precioEspecialZonaCuenta").val() == "1"){
    var tipo = "especial";
  }if($("#precioEmpleadoZonaCuenta").val() == "1"){
    var tipo = "empleado";
  }
  $("#tipoPrecioActual").val(tipo);
  $.ajax({
    type: "POST",
    url: url + "/TraerMesasZona",
    data: {
      idZona : idZona
    },
    dataType: 'json',
    success: function (respuesta) {
      $("#divMesasCuenta").html(respuesta.div);
      $("#divMesasCuenta").show();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });

  var padre =  $(this).parent().parent().parent().parent();
  padre.find(".zonas").removeClass('bg-success');
  padre.find(".zonas").addClass('bg-default');
  $(this).toggleClass('bg-success bg-default');
});

var iteraciom = 0;
$(document).on("click", ".modificador", function () {
  if($(this).attr("multiseleccion") == "1"){
    var card = $(this);
    var contenedorli = card.attr("contenedorli");
    var iteracion = card.attr("iteracion");
    var idMod = card.attr("idMod");
    var nombreMod = card.attr("nombre");
    var aumento = card.find(".aumento").val();
    var maxSeleccion = parseInt(card.attr("maxseleccion")) || 0;

    var liContenedor = $(".liContenedor[contenedor='"+contenedorli+"']");
    var ul = liContenedor.find("> ul");
    if(ul.length == 0){
      ul = $("<ul></ul>");
      liContenedor.append(ul);
    }

    var existente = ul.find("li.liContenedorProducto[idMod='"+idMod+"']");
    if(existente.length > 0){
      existente.remove();
      card.removeClass('bg-success').addClass('bg-default');
    }
    else{
      var actuales = ul.find("li.liContenedorProducto").length;
      if(maxSeleccion > 0 && actuales >= maxSeleccion){
        AlertaPersonalizada('error', 'Máximo '+maxSeleccion+' opciones permitidas');
        return;
      }
      var liNuevo = "<li style='font-size:small; list-style:none;margin-left:-15px' class='liContenedorProducto' it='"+iteracion+"' aumento='"+aumento+"' idMod='"+idMod+"' nombre='"+nombreMod+"'>"+nombreMod+" (+"+aumento+")</li>";
      ul.append(liNuevo);
      card.removeClass('bg-default').addClass('bg-success');
    }

    var nprecio = 0;
    var precioOriginal = $("input[id='cantidad"+(iteracion)+"']").attr("precioOriginal");
    $("li.liContenedorProducto[it='"+(iteracion)+"']").each(function(){
      var aum =  $(this).attr("aumento");
      nprecio = nprecio + Number.parseFloat(aum);
    });
    var descLinea = parseFloat($("input[id='cantidad"+(iteracion)+"']").attr("descuentoLinea") || 0);
    var precio = (Number.parseFloat(precioOriginal) + Number.parseFloat(nprecio)) * (1 - descLinea/100);
    $("input[id='cantidad"+(iteracion)+"']").attr("precio",precio.toFixed(2));
    var labelDesc = descLinea > 0 ? " (-"+descLinea+"%)" : "";
    $("#preciotxt"+(iteracion)).text("").text("$"+precio.toFixed(2)+labelDesc);
    setTimeout(function(){
      calculoTotal();
    },200);

    return;
  }

  var padre =  $(this).parent().parent().parent().parent();
  padre.find(".modificador").removeClass('bg-success');
  padre.find(".modificador").addClass('bg-default');
  $(this).toggleClass('bg-success bg-default');

  var contenedor = $(this).attr("idContenedor");
  var contenedorli = $(this).attr("contenedorli");
  var varios = $(this).attr("varios");
  var aumento = $(this).find(".aumento").val();
  var idProd =  $(this).attr("idProdMod");
  var nivel = 2;
  var iteracion = $(this).attr("iteracion");

  var datos = {
    "idProducto" : idProd,
    "nivel" : nivel,
    "aumento" : aumento,
    "contenedor" : contenedor,
    "iteracion" : iteracion,
    "contenedorli" : contenedorli,
    "csrf_test_name" : token
  }

  if(varios != 0 ){
    $.ajax({
      type: "POST",
      url: url + "/TraerProductoModificadores",
      data: datos ,
      dataType: "json",
      success: function (response) {
        $("#"+contenedor+" .listaModificadoresDetalle").empty();
        $("#"+contenedor+" .modificadorTipoDetalle").html(response.modTipo);
        $(".liContenedor[contenedor='"+contenedorli+"']").find("ul").empty().remove();
        $(".liContenedor[contenedor='"+contenedorli+"']").append(response.lista);
        var nprecio = 0;
        var precioOriginal = $("input[id='cantidad"+(iteracion)+"']").attr("precioOriginal");
        $("li.liContenedorProducto[it='"+(iteracion)+"']").each(function(){
          var aumento =  $(this).attr("aumento");
          nprecio = nprecio + Number.parseFloat(aumento);
        });
        var precio = (Number.parseFloat(precioOriginal) + Number.parseFloat(nprecio));
        $("input[id='cantidad"+(iteracion)+"']").attr("precio",precio.toFixed(2));
        $("#preciotxt"+(iteracion)).text("").text("$"+precio.toFixed(2));
        //calcularValorProducto();
        setTimeout(function(){
          calculoTotal();
        },200);
      }
    });
  }
  else{
    datos["idModificador"] =  $(this).attr("idMod");
    datos["idProdModDet"] =  $(this).attr("idProdModDet");
    console.log($(this).attr("idProdModDet"));
    $.ajax({
      type: "POST",
      url: url + "/TraerProductoModificadoresDetallePrecio",
      data: datos,
      dataType: "json",
      success: function (response) {
        $("#"+contenedor+" .listaModificadoresDetalle").empty();
        $(".liContenedor[contenedor='"+contenedorli+"']").find("ul").empty().remove();
        $(".liContenedor[contenedor='"+contenedorli+"']").append(response.lista);
        var nprecio = 0;
        var precioOriginal = $("input[id='cantidad"+(iteracion)+"']").attr("precioOriginal");
        $("li.liContenedorProducto[it='"+(iteracion)+"']").each(function(){
          var aumento =  $(this).attr("aumento");
          nprecio = nprecio + Number.parseFloat(aumento);
        });
        var precio = (Number.parseFloat(precioOriginal) + Number.parseFloat(nprecio));
        $("input[id='cantidad"+(iteracion)+"']").attr("precio",precio.toFixed(2));
        $("#preciotxt"+(iteracion)).text("").text("$"+precio.toFixed(2));
        //calcularValorProducto();
        setTimeout(function(){
          calculoTotal();
        },200);
      }
    });
  }




});

$(document).on("click",".mesaHacerCuenta",function(){
  var ocupado = $(this).attr("ocupada");
  if(ocupado == "1"){
    $(this).removeClass('bg-danger');
  }
  var padre =  $(this).parent().parent().parent().parent();
  padre.find(".mesaHacerCuenta").removeClass('bg-success');
  padre.find(".mesaHacerCuenta[ocupada='0']").addClass('bg-default');
  padre.find(".mesaHacerCuenta[ocupada='1']").not($(this)).addClass('bg-danger');
  $(this).addClass('bg-success');

  var idMesa = $(this).find(".idMesa").val();
  $("#mesaHacerCuenta").val(idMesa);
});

$(document).on("click","#listaProductos .prim",function () {
  // $(".prim").removeClass('bg-success');
  // $(".prim").addClass('bg-default');
  // $(".collapse").collapse('hide');
  // //$(this).collapse('show');
  // $(this).toggleClass('bg-success bg-default');
  verGrupoProductos($(this).attr("grupo"));
  grupoActual = parseInt($(this).attr("grupo"));

  $("#listaProductosMostrar").hide();
  $("#divHacerCuenta").hide();
  $("#divPagoProducto").hide();
  $("#modificadores").show();
  var iteracion = $(this).attr("idt");
  $(".detalle").hide();
  $(".detalle"+iteracion).show();



});

$(document).on("click",".modTipo",function(){
  var padre =  $(this).parent("div");
  $(".modTipo",padre).removeClass('btn-success');
  $(".modTipo",padre).addClass('btn-default');
  $(this).toggleClass('btn-success btn-default');

  var idProducto = $(this).attr("idProd");
  var contenedor = $(this).attr("contenedor");
  var contenedorli = $(this).attr("contenedorli");
  var idModificadorTipo = $(this).attr("idModTipo");
  var idProductoModificadorTipo = $(this).attr("idProdModTipo");
  var varios = $(this).attr("varios");
  var nivel = $(this).attr("nivel");
  var iteracion = $(this).attr("iteracion");
  var multiSeleccion = $(this).attr("multiseleccion");
  var maxSeleccion = $(this).attr("maxseleccion");

  var seleccionados = [];
  $(".liContenedor[contenedor='"+contenedorli+"']").find("li.liContenedorProducto").each(function(){
    seleccionados.push($(this).attr("idMod"));
  });

  var datos = {
    'idModTipo' : idModificadorTipo,
    'idProd': idProducto,
    'contenedorli': contenedorli,
    'contenedor': contenedor,
    'idProdModTipo' : idProductoModificadorTipo,
    'varios': varios,
    'iteracion': iteracion,
    'multiSeleccion': multiSeleccion,
    'maxSeleccion': maxSeleccion,
    'seleccionados': JSON.stringify(seleccionados),
    "csrf_test_name" : token
  }

  $.ajax({
    type: "POST",
    url: url + "/TraerProductoModificadoresDetalle",
    data: datos,
    dataType: "json",
    success: function (response) {
      if(nivel == "1"){
        $(".detalle"+iteracion+" ·"+contenedor).empty();
        $(".detalle"+iteracion+" ."+contenedor).html(response.div);
      }
      else{
        $("#"+contenedor).empty();
        $("#"+contenedor).html(response.div);
      }
      $(".select2").select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccione una opcion',
      });
    }
  });

});

$(document).on("click", "#agregarProducto", function () {

  var nombre = $("#nombreProducto").text();
  var idProducto = $("#nombreProducto").attr("idProducto");
  var precio = $("#precioProducto").attr("precio");
  var tipo = $("#tipoHacerCuenta").val();

  var tr = "<tr class='prim idt" + iteracion + " accordion-toggle collapsed' data-toggle='collapse' href='#ita" + iteracion + "' idp='" + idProducto + "' idt='" + iteracion + "'>";
  tr += "	<td id='canttxt" + iteracion + "'>1</td>";
  tr += "	<td>" + nombre + "</td>";
  tr += "	<td>$" + precio + "</td>";
  tr += "</tr>";

  tr += "<tr class='sec idt" + iteracion + " hide-table-padding'>";
  tr += "	<td colspan='3'>";
  tr += "		<div id='ita" + iteracion + "' class='collapse in p-0'>";
  tr += "			<div class='input-group'><button class='btn btn-danger btn-sm minus' it='" + iteracion + "'><i class='fa fa-minus'></i></button>";
  tr += "				<input type='hidden' value='"+tipo+"' class='tipoProductoDetalle' id='tipoPD" + iteracion + "'>";
  tr += "				<input type='text' value='1' class='cantidad' precio='" + precio + "' style='width:50px; text-align:center;' readonly id='cantidad" + iteracion + "'>";
  tr += "				<button class='btn btn-primary btn-sm plus' it='" + iteracion + "'><i class='fa fa-plus'></i></button>";
  tr += "				<a class='btn btn-danger btn-sm dell' style='margin-left:20%;' idt='" + iteracion + "'><i class='fa fa-trash'></i></a>";
  tr += "			</div>";
  tr += "		</div>";
  tr += "	</td>";
  tr += "</tr>";
  $("#listaProductos").append(tr);
  setTimeout(function(){
    calculoTotal();
  },200);
  iteracion++;
});

$(document).on('click', '#pagarProducto', function () {
  pagarProducto();
});

$(document).on("click","#finalizarCuenta",function(){
  finalizarCuenta();
})

$(document).on("click","#abrirCuenta",function(){
  abrirCuenta();
});

$(document).on("click","#agregarACuenta",function(){
  agregarACuenta();
})

$(document).on("click","#cuentasLista tr.prim",function(){
  //$("#botoneraHome").show();
  $("#cuentasLista tr").removeClass("bg-success");
  if(!$(this).hasClass("hide-table-padding")){
    $("#botoneraPrimaria").hide();
    $("#detalleCuenta").show();
    $("#accionesCuentaDetalle").show();

    $("#tablaUnirCuenta").hide();
    $("#accionUnirCuenta").hide();
    $("#idCuentaPrincipalUnir").val("");
    $("#tablaDetalleCuenta").show();
    $("#accionesCuentaDetalle").show();

    $("#tablaUnirCuenta").hide();
    $("#accionesUnirDetalle").hide();

    //$(this).addClass("bg-success");
    var actual = $(this).attr("href");
    var idPedido = $(this).attr("idPedido");

    verDetalleCuenta(idPedido);

    $(".collapse").not(actual).hide(400);
    $(actual).toggle(400);
  }
});

$(document).on("click","#cuentasListaCancelada tr.prim",function(){

  $("#cuentasListaCancelada tr").removeClass("bg-success");
  if(!$(this).hasClass("hide-table-padding")){
    $("#botoneraPrimaria").hide();
    $("#detalleCuenta").show();

    $("#tablaUnirCuenta").hide();
    $("#accionUnirCuenta").hide();
    $("#idCuentaPrincipalUnir").val("");
    $("#tablaDetalleCuenta").show();
    $("#accionesCuentaDetalleCancelada").show();

    $("#tablaUnirCuenta").hide();
    $("#accionesUnirDetalle").hide();

    //$(this).addClass("bg-success");
    var actual = $(this).attr("href");
    var idPedido = $(this).attr("idPedido");

    verDetalleCuenta(idPedido);

    $(".collapse").not(actual).hide(400);
    $(actual).toggle(400);
  }
});

$(document).on("click",".accionCuentaDetalle",function(){
  var tipo = $(this).attr("tipo");
  var idPedido = "";
  var array = [];
  $("#tablaDetalleCuenta tbody tr.prim").each(function(){
    var check = $(this).find(".elemento");
    if(check.is(':checked')){
      idPedido = check.attr("idPedido");
      elemento = {
        idPedidoDetalle : check.attr("idPedidoDetalle"),
        precio : check.attr("precio")
      }
      array.push(elemento);
    }
  });
  if(array.length > 0){
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
                      var datos = {
                        idPedido : idPedido,
                        tipo: tipo,
                        motivo: motivo,
                        detalle : JSON.stringify(array)
                      }
                      $.ajax({
                        type: "POST",
                        url: url+"/CambiarEstadoDetalleCuenta",
                        data: datos,
                        dataType: 'json',
                        success: function (respuesta){
                          Alerta(respuesta.codigo);
                          if(respuesta.codigo == 200){
                            verDetalleCuenta(respuesta.idPedido);
                            var total = respuesta.total;
                            $("a.CobrarCuenta[idPedido='"+ idPedido +"']").attr("total",total);
                            $("#cuentasLista tr[idPedido='"+ idPedido +"'] td.totalPedido").text("$"+total);
                            setTimeout(function(){
                              calculoTotal();
                            },200);
                            // console.log(respuesta.servidor);
                            $.each(respuesta.servidor,function (index,server) {
                              if(server.productos !== undefined && server.productos != ""){
                                setTimeout(function(){
                                  $.post("http://"+server.servidor+"/imprimir/printComandaAnular.php", {
                                    datos: server.datos,
                                    productos: server.productos,
                                    encabezado: server.encabezado,
                                    titulo: server.titulo,
                                  });
                                },500);
                              }
                            });
                          }
                          //$("#tablaDetalleCuenta tbody").html(respuesta.tbody);
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown){
                          AlertaPersonalizada('error', XMLHttpRequest.responseText);
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

    // var datos = {
    // 	idPedido : idPedido,
    // 	tipo: tipo,
    // 	detalle : JSON.stringify(array)
    // }
    // $.ajax({
    // 	type: "POST",
    // 	url: url+"/CambiarEstadoDetalleCuenta",
    // 	data: datos,
    // 	dataType: 'json',
    // 	success: function (respuesta){
    // 		Alerta(respuesta.codigo);
    // 		if(respuesta.codigo == 200){
    // 			verDetalleCuenta(respuesta.idPedido);
    // 			var total = respuesta.total;
    // 			$("a.CobrarCuenta[idPedido='"+ idPedido +"']").attr("total",total);
    // 			$("#cuentasLista tr[idPedido='"+ idPedido +"'] td.totalPedido").text("$"+total);
    // 			calculoTotal();
    // 		}
    // 		//$("#tablaDetalleCuenta tbody").html(respuesta.tbody);
    // 	},
    // 	error: function(XMLHttpRequest, textStatus, errorThrown){
    // 		AlertaPersonalizada('error', XMLHttpRequest.responseText);
    // 	}
    // });
  }
});

$(document).on('click', '#pagarServicio', function () {
  pagarServicio();
});

/*****************************************************************************/
/**********************************COVER**************************************/
/*****************************************************************************/

var iteracionc = 0;
$(document).on("click", ".cover", function () {
  var idProducto = $(this).attr("idp");
  var precio = $(this).find(".precio").val();
  var nombre = $(this).find(".info-card p").text();

  var tr = "<tr class='prim idt" + iteracionc + " idp='" + idProducto + "' idt='" + iteracionc + "'>";
  tr += "<td id='canttxt" + iteracionc + "'>1</td>";
  tr += "<td>" + nombre + "</td>";
  tr += "<td class='pco'>$" + precio + "</td>";
  tr += "<td><a class='btn btn-danger btn-sm dellc' style='margin-left:10%;' idt='" + iteracionc + "'><i class='fa fa-trash'></i></a></td>";
  tr += "</tr>";
  $("#listaCovers").append(tr);
  calculoTotalc();
  iteracionc++;
  exiscov = 1;
});

$(document).on("click", ".minus", function () {
  var id = $(this).attr("it");
  var actual = $("#cantidad" + id).val();
  if (actual > 1) {
    actual--;
  }
  $("#cantidad" + id).val(actual);
  $("#canttxt" + id).text(actual);
  setTimeout(function(){
    calculoTotal();
  },200);
});

$(document).on("click", ".plus", function () {
  var id = $(this).attr("it");
  var actual = $("#cantidad" + id).val();
  actual++;
  $("#cantidad" + id).val(actual);
  $("#canttxt" + id).text(actual);
  setTimeout(function(){
    calculoTotal();
  },200);
});

$(document).on("click", ".minuss", function () {
  var id = $(this).attr("it");
  var actual = $("#cantidad" + id).val();
  if (actual > 1) {
    actual--;
  }
  $("#cantidad" + id).val(actual);
  $("#canttxt" + id).text(actual);
  calculoTotals();
});

$(document).on("click", ".pluss", function () {
  var id = $(this).attr("it");
  var actual = $("#cantidad" + id).val();
  var precio = $("#cantidad" + id).attr('precio');

  actual++;
  $("#cantidad" + id).val(actual);
  $("#canttxt" + id).text(actual);
  calculoTotals();
});

$(document).on("click", ".dell", function () {
  var id = $(this).attr("idt");
  $("#listaProductos > .idt" + id).remove();
  $(".detalle"+id).remove();

  setTimeout(function(){
    calculoTotal();
  },200);
});

$(document).on("click", ".dells", function () {
  var id = $(this).attr("idt");
  $("#listaServicios > .idt" + id).remove();
  calculoTotals();
});

$(document).on("click", ".dellc", function () {
  var id = $(this).attr("idt");
  $("#listaCovers > .idt" + id).remove();
  calculoTotalc();
});

$(document).on("click", ".delld", function () {
  var id = $(this).attr("idt");
  $("#tablaDetalleCuenta > tbody > .idt" + id).remove();
  $(".detalle"+id).remove();

  setTimeout(function(){
    calculoTotal();
  },200);
});

$(document).on("click", ".regalia", function () {
  var id = $(this).attr("idt");
  var precio = $(this).attr("precio");
  if($(this).is(":checked")){
    $("#cantidad"+id).attr("regalia",1);
    $("#preciotxt"+id).text("$0.00");
  }else{
    $("#cantidad"+id).attr("regalia",0);
    $("#preciotxt"+id).text("$"+precio);
  }

  setTimeout(function(){
    calculoTotal();
  },200);
});

$(document).on("click", "#btnRegresarMenu", function () {

  // $("#totalPagoServicio").text('').text($("#totaltds").text());
  // $("#listaServiciosMostrar").hide();
  // $("#listaSenoritasMostrar").hide();
  // $("#listaNombresSenoritasMostrar").hide();
  $("#divPagoProducto").hide();
  $(".dinamicos").hide();
  $("#listaProductosMostrar").empty();
  $("#botoneraPrecios").show();
});

$(document).on("click", ".btnFinalizarServicio", function () {

  $("#totalPagoServicio").text('').text($("#totaltds").text());
  $("#listaServiciosMostrar").hide();
  $("#listaSenoritasMostrar").hide();
  $("#listaNombresSenoritasMostrar").hide();
  $("#divPagoServicio").show();
});

$(document).on("click", "#facturarDoc", function () {
  pagarProductoPdf();
});

$(document).on("click", "#quitarPropina", function () {
  quitarPropina();
});

$(document).on("click", ".btnFinalizarOrden", function () {
  $("#tipoPagoProducto").trigger('change');
  $("#totalPagoProducto").text('').text($("#totaltd").text());
  var total = $("#totaltd").attr("total");
  var propina = "0.00";
  if($("#cobroPropina").val() == "Si" && $("#tipoHacerCuenta").val() != 'especial'){
    var propina = ( total * $("#propina").val() / 100 ).toFixed(2);
  }
  $("#propinaPagoProducto").text("$"+propina);
  $("#totalPropPagoProducto").text("$"+( parseFloat(total) + parseFloat(propina)  ).toFixed(2));
  $("#modificadores").hide();
  $("#listaProductosMostrar").hide();
  $("#divDatosDoc").hide();

  if($("#idPedidoGuardado").val() == ""){
    if($("#idUsuarioCorte").val() == $("#idUsuarioSesion").val()){
      $("#agregarACuenta").hide();
      $("#abrirCuenta").show();
      $("#pagarProducto").show();
      $("#finalizarCuenta").hide();
      $("#divPagoProducto").show();
    }
    else{
      $("#abrirCuenta").click();
    }
  }
  else{
    agregarACuenta();
    // $("#agregarACuenta").show();
    // $("#abrirCuenta").hide();
    // $("#pagarProducto").hide();
    // $("#finalizarCuenta").hide();
  }
});

/*****************************************************************************/
/*******************************MOVIMIENTOS***********************************/
/*****************************************************************************/

$(document).on("click",".btnFinalizarMovimiento",function(){
  $("#FrmMovimientoCaja").validate({
    rules: {
      movimientoRecibe:{
        required:true,
      },
      movimientoEntrega:{
        required:true,
      },
      movimientoConcepto:{
        required:true,
      },
      movimientoMonto:{
        required:true,
        number:true
      }
    },
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
      InsertarMovimiento();
    }
  });
  $("#FrmMovimientoCaja").submit();

});

/*****************************************************************************/
/*********************************FUNCIONES***********************************/
/*****************************************************************************/

function actualizarTimer(){
  $("#cuentasLista tr.prim").each(function(){
    var horai = $(this).find(".timer").val();
    var max = 20;
    //var horaii = horai.split(" ")[0];
    //alert(horaii);
    var deadline = new Date(horai).getTime();
    var now = new Date().getTime();
    var t = now - deadline;
    var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
    var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((t % (1000 * 60)) / 1000);
    var dif = "";

    if(hours	>	0)  { dif+=hours; }
    if(minutes<	10) { minutes = "0"+minutes; }
    if(dif	==	"") { dif+=minutes; }
    else {dif+=":"+minutes; }
    if(seconds<	10) { seconds = "0"+seconds; }

    dif+=":"+seconds;

    //   if(minutes >= max || hours > 0)   {
    // 	$(this).find(".timers").removeClass("text-success");
    // 	$(this).find(".timers").addClass("text-danger");
    // 	$(this).find(".timers").addClass("blinking");
    //   }
    $(this).find(".timers").html(""+dif);
  });
}

function calcularDescuentos(id) {

  if($("#cobroPropina").val() == "Si"){
    var subtotal = parseFloat($("#totalPagoProducto").text().split("$")[1]);
    var propina = parseFloat($("#propinaPagoProducto").text().split("$")[1]);
    var total = parseFloat(subtotal) + parseFloat(propina);
  } else {
    var subtotal = parseFloat($("#totalPagoProducto").text().split("$")[1]);
    var total = parseFloat($("#totalPagoProducto").text().split("$")[1]);
  }

  if(id == "descuentoDolarPagoProducto"){
    var monto = $("#descuentoDolarPagoProducto").val();

    if(monto == ''){monto = 0;}
    monto = parseFloat(monto);
    var porc = 0;
    if(monto > 0){
      porc = (subtotal - monto) / subtotal;
      // porc = (total - monto) / total;
    }
    var descuentoPor = (1- porc) * 100;
    if(monto == 0){
      descuentoPor = 0.00;
    }
    $("#descuentoPagoProducto").val(descuentoPor.toFixed(2));
  } else {
    var monto = $("#descuentoPagoProducto").val();

    if(monto == ''){monto = 0;}
    monto = parseFloat(monto);
    var desc = 0;
    if(monto > 0){
      desc = monto/100;
    }
    descuento = subtotal * desc;
    // descuento = total * desc;

    $("#descuentoDolarPagoProducto").val(descuento.toFixed(2));
  }

  $("#"+id).focus();
  setTimeout(function(){
    calculoTotal();
  },200);
}

function calcularTotalPago() {
  var productos = crearArrayProductos();
  var descuento = $("#descuentoDolarPagoProducto").val();
  if(descuento == ""){ descuento = 0; }

  if($("#cobroPropina").val() == "Si"){
    var total = $("#totalEnvioPagoProducto").text().replace("$", '');
    var subtotal = $("#totalEnvioPagoProducto").text().replace("$", '');
  } else {
    var total = $("#totalEnvioPagoProducto").text().replace("$", '');
  }
  total = total ;


  var efectivo = ($("#efectivoPagoProducto").val() == "") ? 0 : $("#efectivoPagoProducto").val();
  var tarjeta = ($("#tarjetaPagoProducto").val() == "") ? 0 : $("#tarjetaPagoProducto").val();
  var bitcoin = ($("#bitcoinPagoProducto").val() == "") ? 0 : $("#bitcoinPagoProducto").val();
  var pedidosYa = ($("#pedidosYaPagoProducto").val() == "") ? 0 : $("#pedidosYaPagoProducto").val();
  var transferencia = ($("#transferenciaProducto").val() == "") ? 0 : $("#transferenciaProducto").val();

  var monto = parseFloat(efectivo) + parseFloat(tarjeta) + parseFloat(bitcoin) + parseFloat(pedidosYa) + parseFloat(transferencia);

  var vuelto = (monto - total).toFixed(2);

  if (vuelto >= 0.00 && total >= 0.00) {
    $("#pagarProducto").attr("disabled", false);
    $("#finalizarCuenta").attr("disabled", false);
  }
  else {
    $("#pagarProducto").attr("disabled", true);
    $("#finalizarCuenta").attr("disabled", true);
  }
  (vuelto < 0) ? vuelto =  "-"+parseFloat(-1 * vuelto).toFixed(2) : parseFloat(vuelto).toFixed(2);
  $("#vueltoPagoProducto").text(vuelto);
}

function calculoTotal() {
  var total = 0;
  if($("#idPedidoGuardado").val() == "" || $("#CobrarCuentaFinal").val() == "0"){
    $("#listaProductos tr.sec").each(function () {
      var cantidad = $(this).find(".cantidad").val();
      var precio = $(this).find(".cantidad").attr('precio');
      var regalia = $(this).find(".cantidad").attr('regalia');
      if(regalia == 0){
        total += cantidad * precio;
      }
    });
  } else {
    $("#tablaDetalleCuenta tbody tr.prim").each(function () {
      var cantidad = $(this).attr("cantidad");
      var precio = $(this).attr('precio');
      var regalia = $(this).attr('regalia');

      if(regalia != "Si"){
        total += cantidad * precio;
      }
      console.log("Cantidad: "+ cantidad +  " | Precio: $" + precio + " | SubTotal: $" + (cantidad * precio) + " | Acumulado: $"+total);
    });
  }
  var descuento = $("#descuentoDolarPagoProducto").val();
  if(descuento == "") { descuento = 0; }

  var envio = $("#envioProducto").val();
  if(envio == "") { envio = 0; }

  $("#totaltd").text("$" + total.toFixed(2));
  $("#totaltd").attr("total", total.toFixed(2));

  $("#totalPagoProducto").text('').text($("#totaltd").text());
  var total = $("#totaltd").attr("total");
  var propina = 0;
  if($("#cobroPropina").val() == "Si" && $("#tipoHacerCuenta").val() != 'especial'){
    var propina = ( total * $("#propina").val() / 100 ).toFixed(2);
  }
  $("#propinaPagoProducto").text("$"+propina);
  $("#totalPropPagoProducto").text("$"+( parseFloat(total) + parseFloat(propina) - parseFloat(descuento)).toFixed(2));
  $("#totalEnvioPagoProducto").text("$"+( parseFloat(total) + parseFloat(propina) - parseFloat(descuento) + parseFloat(envio)).toFixed(2));


  setTimeout(function(){
    calcularTotalPago();
  },200);
}

function calculoTotals() {
  var total = 0;
  $("#listaServicios tr.prim").each(function () {
    var cantidad = $(this).find(".cantidad").text();
    var precio = $(this).find(".pco").text().replace("$", "");
    total += cantidad * precio;
  });
  $("#totaltds").text("$" + total.toFixed(2));
  $("#totalPagoServicio").text('').text($("#totaltds").text());
  $("#efectivoPagoServicio").keyup();


}

function calculoTotalc() {
  var total = 0;
  $("#listaCover tr.prim").each(function () {
    var cantidad = 1;
    var precio = $(this).find(".pco").text().split("$")[1];
    total += cantidad * precio;
  });
  $("#totaltcv").text("$" + total.toFixed(2));
}

function verGrupoProductos(grupo){
  var grupo = parseInt(grupo);
  $("#listaProductos tr.prim").css({"border-style":"none"});
  $("#listaProductos tr.prim[grupo='"+grupo+"']").css({"border-color": "#C1E0FF",
  "border-weight":"1px",
  "border-style":"solid"});
  // $("#listaProductos tr.prim").removeClass("bg-success");
  // $("#listaProductos tr.prim[grupo='"+grupo+"']").addClass("bg-success");
}

var guardandoMov = 0;
function InsertarMovimiento() {
  Swal.fire({
    title: 'Alerta!!',
    text: "Estas seguro que desea Realizar este Movimiento?!",
    type: 'warning',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Realizar',
    cancelButtonText: 'Cancelar',
  }).then((result) =>{
    if (result.value){
      if(!guardandoMov){
        guardandoMov = 1;
        var proceso = $("#proceso").val();
        var FrmMov = $("#FrmMovimientoCaja");
        var Frm = false;
        if (window.FormData){
          Frm = new FormData(FrmMov[0]);
        }
        $.ajax({
          type: 'POST',
          url: url+'/RealizarMovimientoCaja',
          cache: false,
          data: Frm ? Frm : FrmMov.serialize(),
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function (respuesta){
            //funcion de notificaciones, 4 parametros, tipo, titulo, subtitulo, mensaje
            //Codigo
            Alerta(respuesta.codigo);
            if (respuesta.codigo == 200){
              //console.log(respuesta);
              var id = respuesta.idFactura;
              imprimirTicketMovimientoCaja(id);
              setTimeout("reload();",500);
            } else{
              guardandoMov = 0;
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown){
            guardandoMov = 0;
            AlertaPersonalizada('error', XMLHttpRequest.responseText);
          }
        });
      }
    }
  });
  $(".swal2-input").addClass("bg-white");

}

function calcularValorProducto(){
  var aumento = 0;
  var select= $("");
  var modificadores = $("option[value!='']:selected","select.modificador");
  var n = 0;
  modificadores.each(function(){
    aumento = aumento + parseFloat($(this).attr("aumento"));
  });
  var precio = parseFloat($("#precioProducto").attr("precioOriginal"));
  precio =  (precio + aumento).toFixed(2);

  $("#precioProducto").text("$"+precio);
  $("#precioProducto").attr("precio",precio);
}

function crearArrayProductos(){
  var arrayProductos = [];
  $("#listaProductos tr.prim").each(function(){
    var producto = {};
    var iter = $(this).attr('idt');
    var idProducto = $(this).attr('idp');
    var grupo = $(this).attr('grupo');
    var cantidad = $("#cantidad"+ iter).val();
    var precio = $("#cantidad"+ iter).attr("precio");
    var precioOriginal = $("#cantidad"+ iter).attr("precioOriginal");
    var comentario = $("#comentario"+ iter).val();
    var tipo = $("#tipoPD"+ iter).val();
    var coci = $("#coci"+ iter).val();
    var regCheck = $("#regalia"+ iter);
    var regalia = 0;
    if(regCheck.is(':checked')){
      regalia = 1;
    }
    if(coci == '1'){
      var cocinero =  $("#cocinero"+ iter).val();
    }
    var arrayMod = [];
    var listaMod = $("#listaProductos tr.sec.idt" + iter).find(".listaModificadores ul li.liContenedor[idProd='"+idProducto+"']");
    listaMod.each(function(){
      var liContenedor = $(this);
      var varios = liContenedor.attr("varios");
      var idProdModTipo = liContenedor.attr("idProdModTipo");
      var nombreModTipo = liContenedor.attr("nombre");

      liContenedor.find(".liContenedorProducto").each(function(){
        var modificador = {};
        var aumento = $(this).attr("aumento");
        var idMod = $(this).attr("idMod");
        var nombreMod = $(this).attr("nombre");

        modificador.varios = varios;
        modificador.idProdModTipo = idProdModTipo;
        modificador.nombreModTipo = nombreModTipo;
        modificador.aumento = aumento;
        modificador.idMod = idMod;
        modificador.nombreMod = nombreMod;

        if(varios != "0"){
          modificador.subMod = crearArrayModificadores(liContenedor.find("ul li.liContenedor"));
        }
        arrayMod.push(modificador);
      });
    });

    producto.idProducto = idProducto;
    producto.cantidad = cantidad;
    producto.precio = precio;
    producto.grupo = grupo;
    producto.tipo = tipo;
    producto.regalia = regalia;
    producto.precioOriginal = precioOriginal;
    producto.comentario = comentario;
    producto.coci = coci;
    if($("#servicioSenorita").val() == "1"){
      if(tipo == 'especial'){
        producto.senorita = senorita;
      }
    }
    producto.cocinero = (coci == "1") ? cocinero : 0;
    producto.modificadores = arrayMod;

    arrayProductos.push(producto);
  });

  return arrayProductos;
}

function crearArrayModificadores(lista) {
  var arrayMod = [];
  var li = lista;
  //var li = lista.find(".liContenedorProducto ul .liContenedor");
  li.each(function(){
    var liContenedor = $(this);
    var varios = liContenedor.attr("varios");
    var idProdModTipo = liContenedor.attr("idProdModTipo");
    var nombreModTipo = liContenedor.attr("nombre");

    liContenedor.find(".liContenedorProducto").each(function(){
      var modificador = {};
      var aumento = $(this).attr("aumento");
      var idMod = $(this).attr("idMod");
      var nombreMod = $(this).attr("nombre");

      modificador.varios = varios;
      modificador.idProdModTipo = idProdModTipo;
      modificador.nombreModTipo = nombreModTipo;
      modificador.aumento = aumento;
      modificador.idMod = idMod;
      modificador.nombreMod = nombreMod;

      if(varios != "0"){
        modificador.subMod = crearArrayModificadores(liContenedor.find("ul li.liContenedor"));
      }
      arrayMod.push(modificador);
    });
  });

  return arrayMod;
}

function abrirCuenta(){


  var productos = crearArrayProductos();

  if(productos.length > 0 ){
    mostrarPreloader();
    var vuelto = $("#vueltoPagoProducto").text().replace("$","");
    var efectivo = $("#efectivoPagoProducto").val();
    var datos = {
      cliente : $("#clienteProducto").val(),
      idCliente: $("#idClienteProducto").val(),
      personas : $("#personaHacerCuenta").val(),
      direccion : $("#direccionCliente").val(),
      tipoCuenta : $("#tipoHacerCuenta").val(),
      idZona :	$("#idZonaCuenta").val(),
      zona : $("#nombreZonaCuenta").val(),
      tipoAumento : $("#tipoAumentoZonaCuenta").val(),
      aumento :$("#aumentoZonaCuenta").val(),
      comentario :$("#comentarioGeneralOrden").val(),
      idMesa : $("#mesaHacerCuenta").val(),
      total: $("#totaltd").attr("total"),
      productos: JSON.stringify(productos)
    }
    $.ajax({
      type: "POST",
      url: url+"/AbrirCuenta",
      data: datos,
      dataType: 'json',
      success: function (respuesta){
        Alerta(respuesta.codigo);
        if(respuesta.codigo == 200){
          $(".home").click();
          imprimirComandaCocina(respuesta.idPedido)
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });

  }

}

function agregarACuenta(){
  var idPedido = $("#idPedidoGuardado").val();
  var CuentaLlevarLocal= $("#AgregarCuentaLlevarLocal").val();

  var productos = crearArrayProductos();
  if(productos.length > 0){
    mostrarPreloader();
    var datos = {
      idPedido : $("#idPedidoGuardado").val(),
      total: $("#totaltd").attr("total"),
      comentario :$("#comentarioGeneralOrden").val(),
      llevarlocal: CuentaLlevarLocal,
      productos: JSON.stringify(productos)
    }
    $.ajax({
      type: "POST",
      url: url+"/AgregarACuenta",
      data: datos,
      dataType: 'json',
      success: function (respuesta){
        Alerta(respuesta.codigo);
        if(respuesta.codigo == 200){
          imprimirComandaCocina(idPedido);
          $(".home").click();
          // setTimeout("reload();",1000);
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
        quitarPreloader();
      }
    });
  }
}

function finalizarCuenta(){
  var isValid = validarInformacion1();
  if(isValid == ""){
    if($("#porConsumoPagoProducto").is(":checked")){
      var porConsumo = 1 ;
    } else {
      var porConsumo = 0 ;
    }
    var vuelto = $("#vueltoPagoProducto").text().replace("$","");
    var personas = $("#personaHacerCuenta").val();
    var efectivo = $("#efectivoPagoProducto").val();
    var tarjeta = $("#tarjetaPagoProducto").val();
    var bitcoin = $("#bitcoinPagoProducto").val();
    var pedidosYa = $("#pedidosYaPagoProducto").val();
    var transferencia = $("#transferenciaProducto").val();
    var envio = $("#envioProducto").val();
    var descuentoDolar = $("#descuentoDolarPagoProducto").val();
    var descuento = $("#descuentoPagoProducto").val();
    var tipoPago = $("option:selected", $("#tipoPagoProducto"));
    var bandera = 0;
    var datos = {
      cliente : $("#nombreClientePagoProducto").val(),
      idCliente : $("#idClienteProducto").val(),
      direccion : $("#direccionClientePagoProducto").val(),
      nit : $("#nitClientePagoProducto").val(),
      nrc : $("#nrcClientePagoProducto").val(),
      telefono : $("#telefonoClientePagoProducto").val(),
      correo : $("#correoClientePagoProducto").val(),
      giro : $("#giroClientePagoProducto").val(),
      departamento : $("#departamentoClientePagoProducto").val(),
      municipio : $("#municipioClientePagoProducto").val(),
      correlativo : $("#correlativoClientePagoProducto").val(),
      idPedido : $("#idPedidoGuardado").val(),
      porConsumo : porConsumo,
      idCaja : $("#cajaProducto").val(),
      tipoDoc : $("#tipoPagoProducto").val(),
      idPedidoGuardado : $("#idPedidoGuardado").val(),
      actual : $("option:selected", $("#tipoPagoProducto")).attr("actual"),
      idDoc : $("option:selected", $("#tipoPagoProducto")).attr("idDoc"),
      total : $("#totalPropPagoProducto").text().replace("$",""),
      propina : $("#propinaPagoProducto").text().replace("$",""),
      vuelto : vuelto,
      personas : personas,
      efectivo : efectivo,
      bitcoin : bitcoin,
      tarjeta : tarjeta,
      pedidosYa : pedidosYa,
      transferencia : transferencia,
      envio:envio,
      descuento : descuento,
      descuentoDolar : descuentoDolar,
    }

    $.ajax({
      type: "POST",
      url: url+"/FinalizarCuenta",
      data: datos,
      dataType: 'json',
      success: function (respuesta){
        Alerta(respuesta.codigo);
        if(respuesta.codigo == 200){
          var id = respuesta.idFactura;
          var id1 = respuesta.idFactura1;
          if(tipoPago.val() == "TIK" || tipoPago.val() == "VET"){
            if(tipoPago.attr("pdf") == "0"){
              imprimirTicketProducto(id,vuelto,efectivo);
            }
            else{
              imprimirFacturaProducto(id1,vuelto,efectivo);
            }
          } else {
            mostrarPreloader();
            $.ajax({
              type: "POST",
              url : url+"/crearDTE/"+id,
              dataType: "JSON",
              success: function(responsedte){
                if(tipoPago.attr("pdf") == "0"){
                  imprimirTicketProducto(id,vuelto,efectivo);
                }
                else{
                  imprimirFacturaProducto(id1,vuelto,efectivo);
                }
                quitarPreloader();
              },
              error: function(err){
                if(tipoPago.attr("pdf") == "0"){
                  imprimirTicketProducto(id,vuelto,efectivo);
                }
                else{
                  imprimirFacturaProducto(id1,vuelto,efectivo);
                }
                quitarPreloader();
                // console.log(err);
              }
            });
          }
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  } else {
    AlertaPersonalizada('Error',isValid);
  }
}

function ActualizarCuentas(tipo){
  $(".dinamicos").hide();
  setInterval("actualizarTimer();",1000);
  var searching = "<tr><td colspan='3'><img style='width:30px;' src='"+url+"/vendors/core/img/loading.gif'> <span class='blink_me' style='font-size:20pt; color:#FFF;'>Cargando datos ...</span></td></tr>";
  $("#cuentasLista").html(searching);
  $("#espacioCuentas").show();
  $.ajax({
    type: "POST",
    url: url+"/ActualizarCuentas",
    data :{
      tipo:tipo
    },
    dataType: 'json',
    success: function (respuesta) {
      if (respuesta.codigo == 200) {
        $("#cuentasLista").html(respuesta.datos);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });
}

function ActualizarCuentasCanceladas(tipo){
  $(".dinamicos").hide();
  //setInterval("actualizar_timer();",1000);
  var searching = "<tr><td colspan='3'><img style='width:30px;' src='"+url+"/vendors/core/img/loading.gif'> <span class='blink_me' style='font-size:20pt; color:#FFF;'>Cargando datos ...</span></td></tr>";
  $("#cuentasLista").html(searching);
  $("#espacioCuentas").show();
  $.ajax({
    type: "POST",
    url: url+"/ActualizarCuentasCanceladas",
    dataType: 'json',
    data:{
      tipo:tipo,
    },
    success: function (respuesta) {
      if (respuesta.codigo == 200) {
        $("#cuentasListaCancelada").html(respuesta.datos);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });
}

function listarCuentasLocal(idPedido = 0,idZona = 0){
  var searching = "<tr><td colspan='3'><img style='width:30px;' src='"+url+"/vendors/core/img/loading.gif'> <span class='blink_me' style='font-size:20pt; color:#FFF;'>Cargando datos ...</span></td></tr>";
  $("#tablaUnirCuenta tbody").html(searching);
  //$("#espacioCuentas").show();
  $.ajax({
    type: "POST",
    url: url+"/ActualizarCuentasLocal",
    data : {
      idPedido : idPedido,
      idZona : idZona
    },
    dataType: 'json',
    success: function (respuesta) {
      if (respuesta.codigo == 200) {
        $("#tablaUnirCuenta tbody").html(respuesta.datos);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });
}

function UnirCuenta(){
  var total = 0;
  var array = [];
  var idPedido = $("#idCuentaPrincipalUnir").val();
  $("#tablaUnirCuenta tbody tr").each(function(){
    var check = $(this).find(".cuentaUnir");
    if(check.is(':checked')){
      var monto = parseFloat(check.attr("total"));
      total = total + monto;
      elemento = {
        idPedidoSec : check.attr("idPedido"),
      }
      array.push(elemento);
    }
  });
  if(array.length > 0){
    var datos = {
      idPedido : idPedido,
      total : total,
      detalle : JSON.stringify(array),
    }
    $.ajax({
      type: "POST",
      url: url+"/UnirCuenta",
      data: datos,
      dataType: 'json',
      success: function (respuesta){
        Alerta(respuesta.codigo);
        if(respuesta.codigo == 200){
          $("#btnCuenta").click();
        }
        //$("#tablaDetalleCuenta tbody").html(respuesta.tbody);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  }
}

function DividirCuenta(idPedido,nombre){
  var total = 0;
  var array = [];
  $("#tablaDetalleCuenta tbody tr.prim").each(function(){
    var check = $(this).find(".elemento");
    if(check.is(':checked')){
      var monto = parseFloat(check.attr("precio"));
      total = total + monto;
      elemento = {
        idPedidoDetalle : check.attr("idPedidoDetalle"),
      }
      array.push(elemento);
    }
  });
  if(array.length > 0){
    var datos = {
      idPedido : idPedido,
      total : total,
      nombre : nombre,
      detalle : JSON.stringify(array),
    }
    $.ajax({
      type: "POST",
      url: url+"/DividirCuenta",
      data: datos,
      dataType: 'json',
      success: function (respuesta){
        Alerta(respuesta.codigo);
        if(respuesta.codigo == 200){
          $("#btnCuenta").click();
        }
        //$("#tablaDetalleCuenta tbody").html(respuesta.tbody);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  }
}

function verDetalleCuenta(idPedido){
  var datos = {
    idPedido : idPedido,
  }
  $.ajax({
    type: "POST",
    url: url+"/VerDetalleCuenta",
    data: datos,
    dataType: 'json',
    success: function (respuesta){
      $("#tablaDetalleCuenta tbody").html(respuesta.tbody);
      $("#tablaDetalleCliente tbody").html(respuesta.tbodyPedido);
      $("#cobroPropina").val(respuesta.cobroPropina);

      var total = 0;

      $("#tablaDetalleCuenta tbody tr.prim[regalia='No']").each(function(){
        var precio = $(this).attr("precio");
        total = total + parseFloat(precio);
      });
      total = total.toFixed(2);
      $("#idPedidoGuardado").val(idPedido);
      $("#totalPagoProducto").text("$"+total);
      var propina = 0.00;
      if($("#cobroPropina").val()=="Si"){
        propina = total * $("#propina").val() / 100;
        $("#propinaPagoProducto").text("$"+propina.toFixed(2));
      }
      var totalFinal = parseFloat(total) + parseFloat(propina);
      $("#totalPropPagoProducto").text("$"+totalFinal.toFixed(2));
    },
    error: function(XMLHttpRequest, textStatus, errorThrown){
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });
}
function validarInformacion(tipo=""){
  var totalFinal = parseFloat($(".totalPropPagoProducto").text().split("$")[1]);
  var idCliente = $("#idClienteProducto").val();
  var nitCliente = $("#nitClientePagoProducto").val();
  var direccionCliente = $("#direccionClientePagoProducto").val();
  var departamentoCliente = $("#departamentoClientePagoProducto").val();
  var municipioCliente = $("#municipioClientePagoProducto").val();
  var nrcCliente = $("#nrcClientePagoProducto").val();
  var giroCliente = $("#giroClientePagoProducto").val();
  var emailCliente = $("#correoClientePagoProducto").val();
  var telefonoCliente = $("#telefonoClientePagoProducto").val();

  var tipoImpresion = $("#tipoPagoProducto option:selected").val();

  var totalItem = $("#listaProductos tr").length;
  // console.log(totalItem);
  var	passdata = true;

  if(tipoImpresion == "FAC" || tipoImpresion == "TIK"){
    if(totalFinal >= 25000){
      if (nitCliente == "" || direccionCliente == "" || departamentoCliente== "" || municipioCliente == "" || emailCliente == "" || telefonoCliente == ""){
        passdata = false;
      }
    } else {
      if (direccionCliente == "" || departamentoCliente== "" || municipioCliente == ""){
        passdata = false;
      }
    }
  } else {
    if (nitCliente == "" || direccionCliente == "" || departamentoCliente== "" || municipioCliente == "" || nrcCliente == "" || giroCliente == "" || emailCliente == "" || telefonoCliente == ""){
      passdata = false;
    }
  }
  if(totalItem > 0){
    if(passdata || tipo=="Cotizacion" || tipoImpresion == "VET"){
      return "";
    } else {
      return "Debe completar los datos del cliente para poder continuar";
    }
  } else {
    return "Debe agregar al menos un item";
  }
}
function validarInformacion1(tipo=""){
  var totalFinal = parseFloat($(".totalPropPagoProducto").text().split("$")[1]);
  var idCliente = $("#idClienteProducto").val();
  var nitCliente = $("#nitClientePagoProducto").val();
  var direccionCliente = $("#direccionClientePagoProducto").val();
  var departamentoCliente = $("#departamentoClientePagoProducto").val();
  var municipioCliente = $("#municipioClientePagoProducto").val();
  var nrcCliente = $("#nrcClientePagoProducto").val();
  var giroCliente = $("#giroClientePagoProducto").val();
  var emailCliente = $("#correoClientePagoProducto").val();
  var telefonoCliente = $("#telefonoClientePagoProducto").val();

  var tipoImpresion = $("#tipoPagoProducto option:selected").val();

  var totalItem = $("#tablaDetalleCuenta tbody tr").length;
  // console.log(totalItem);
  var	passdata = true;

  if(tipoImpresion == "FAC" || tipoImpresion == "TIK"){
    if(totalFinal >= 25000){
      if (nitCliente == "" || direccionCliente == "" || departamentoCliente== "" || municipioCliente == "" || emailCliente == "" || telefonoCliente == ""){
        passdata = false;
      }
    } else {
      if (direccionCliente == "" || departamentoCliente== "" || municipioCliente == ""){
        passdata = false;
      }
    }
  } else {
    if (nitCliente == "" || direccionCliente == "" || departamentoCliente== "" || municipioCliente == "" || nrcCliente == "" || giroCliente == "" || emailCliente == "" || telefonoCliente == ""){
      passdata = false;
    }
  }
  if(totalItem > 0){
    if(passdata || tipo=="Cotizacion" || tipoImpresion == "VET"){
      return "";
    } else {
      return "Debe completar los datos del cliente para poder continuar";
    }
  } else {
    return "Debe agregar al menos un item";
  }
}
var sendding = false;
function pagarProducto(){
  if(!sendding){
    sendding = true;
    $("#pagarProducto").attr("disabled", true);
    var productos = crearArrayProductos();
    var isValid = validarInformacion();
    if(isValid == ""){
      if(productos.length > 0){
        if($("#porConsumoPagoProducto").is(":checked")){
          var porConsumo = 1 ;
        } else {
          var porConsumo = 0 ;
        }
        var vuelto = $("#vueltoPagoProducto").text().replace("$","");
        var efectivo = $("#efectivoPagoProducto").val();
        var personas = $("#personaHacerCuenta").val();
        var tarjeta = $("#tarjetaPagoProducto").val();
        var bitcoin = $("#bitcoinPagoProducto").val();
        var pedidosYa = $("#pedidosYaPagoProducto").val();
        var tipoPago = $("option:selected", $("#tipoPagoProducto"));
        var descuentoDolar = $("#descuentoDolarPagoProducto").val();
        var descuento = $("#descuentoPagoProducto").val();

        var transferencia = $("#transferenciaProducto").val();
        var envio = $("#envioProducto").val();


        var bandera = 0;
        if(tipoPago.attr("pdf") == "0"){
          bandera = 1;
          var datos = {
            cliente : $("#nombreClientePagoProducto").val(),
            idCliente : $("#idClienteProducto").val(),
            direccion : $("#direccionClientePagoProducto").val(),
            correlativo : $("#correlativoClientePagoProducto").val(),
            nit : $("#nitClientePagoProducto").val(),
            nrc : $("#nrcClientePagoProducto").val(),
            telefono : $("#telefonoClientePagoProducto").val(),
            correo : $("#correoClientePagoProducto").val(),
            giro : $("#giroClientePagoProducto").val(),
            departamento : $("#departamentoClientePagoProducto").val(),
            municipio : $("#municipioClientePagoProducto").val(),
            porConsumo : porConsumo,
            tipoCuenta : $("#tipoHacerCuenta").val(),
            idZona : $("#idZonaCuenta").val(),
            zona : $("#nombreZonaCuenta").val(),
            tipoAumento : $("#tipoAumentoZonaCuenta").val(),
            aumento :$("#tipoAumentoZonaCuenta").val(),
            idMesa : $("#mesaHacerCuenta").val(),
            idCaja : $("#cajaProducto").val(),
            recurso : $("#cajaProducto").attr("recurso"),
            ip : $("#cajaProducto").attr("ip"),
            tipoDoc : $("#tipoPagoProducto").val(),
            idPedidoGuardado : $("#idPedidoGuardado").val(),
            actual : $("option:selected", $("#tipoPagoProducto")).attr("actual"),
            idDoc : $("option:selected", $("#tipoPagoProducto")).attr("idDoc"),
            comentario :$("#comentarioGeneralOrden").val(),
            total : $("#totalPropPagoProducto").text().replace("$",""),
            propina : $("#propinaPagoProducto").text().replace("$",""),
            personas : personas,
            descuento : descuento,
            descuentoDolar : descuentoDolar,
            vuelto: vuelto,
            efectivo : efectivo,
            tarjeta : tarjeta,
            bitcoin : bitcoin,
            pedidosYa : pedidosYa,
            transferencia : transferencia,
            envio:envio,
            productos: JSON.stringify(productos)
          }
        } else {

          bandera = 1;
          var datos = {
            cliente : $("#nombreClientePagoProducto").val(),
            idCliente : $("#idClienteProducto").val(),
            direccion : $("#direccionClientePagoProducto").val(),
            nit : $("#nitClientePagoProducto").val(),
            nrc : $("#nrcClientePagoProducto").val(),
            telefono : $("#telefonoClientePagoProducto").val(),
            correo : $("#correoClientePagoProducto").val(),
            giro : $("#giroClientePagoProducto").val(),
            departamento : $("#departamentoClientePagoProducto").val(),
            municipio : $("#municipioClientePagoProducto").val(),
            correlativo : $("#correlativoClientePagoProducto").val(),
            tipoCuenta : $("#tipoHacerCuenta").val(),
            porConsumo : porConsumo,
            idZona :	$("#idZonaCuenta").val(),
            zona : $("#nombreZonaCuenta").val(),
            tipoAumento : $("#tipoAumentoZonaCuenta").val(),
            aumento :$("#aumentoZonaCuenta").val(),
            idMesa : $("#mesaHacerCuenta").val(),
            idCaja : $("#cajaProducto").val(),
            tipoDoc : $("#tipoPagoProducto").val(),
            idPedidoGuardado : $("#idPedidoGuardado").val(),
            actual : $("option:selected", $("#tipoPagoProducto")).attr("actual"),
            idDoc : $("option:selected", $("#tipoPagoProducto")).attr("idDoc"),
            comentario :$("#comentarioGeneralOrden").val(),
            total : $("#totalPropPagoProducto").text().replace("$",""),
            propina : $("#propinaPagoProducto").text().replace("$",""),
            personas: personas,
            vuelto: vuelto,
            descuento : descuento,
            descuentoDolar : descuentoDolar,
            efectivo : efectivo,
            tarjeta : tarjeta,
            bitcoin : bitcoin,
            pedidosYa : pedidosYa,
            transferencia : transferencia,
            envio:envio,
            productos: JSON.stringify(productos)
          }
          FormatoDatos();

        }
        if(bandera == 1){
          $.ajax({
            type: "POST",
            url: url+"/PagarProducto",
            data: datos,
            dataType: 'json',
            success: function (respuesta){
              bandera = 0;
              Alerta(respuesta.codigo);
              if(respuesta.codigo == 200){
                var id = respuesta.idFactura;
                var id1 = respuesta.idFactura1;
                var idPedido = respuesta.idPedido;
                //descargarInsumos(idPedido);
                imprimirComandaCocina(idPedido);
                if(tipoPago.val() == "TIK" || tipoPago.val() == "VET"){
                  imprimirTicketProducto(id,vuelto,efectivo);
                  $(".home").click();
                } else {
                  mostrarPreloader();
                  $.ajax({
                    type: "POST",
                    url : url+"/crearDTE/"+id,
                    dataType: "JSON",
                    success: function(responsedte){
                      if(tipoPago.attr("pdf") == "0"){
                        imprimirTicketProducto(id,vuelto,efectivo);
                        $(".home").click();
                        $(".editarCliente").removeAttr("idCliente");
                        $(".editarCliente").attr("hidden",true);
                        $(".clearafter").val("");
                      }
                      else{
                        imprimirFacturaProducto(id1,vuelto,efectivo);
                        $(".home").click();
                        $(".editarCliente").removeAttr("idCliente");
                        $(".editarCliente").attr("hidden",true);
                        $(".clearafter").val("");
                      }
                      quitarPreloader();
                    },
                    error: function(err){
                      if(tipoPago.attr("pdf") == "0"){
                        imprimirTicketProducto(id,vuelto,efectivo);
                        $(".home").click();
                        $(".editarCliente").removeAttr("idCliente");
                        $(".editarCliente").attr("hidden",true);
                        $(".clearafter").val("");
                      }
                      else{
                        imprimirFacturaProducto(id1,vuelto,efectivo);
                        $(".home").click();
                        $(".editarCliente").removeAttr("idCliente");
                        $(".editarCliente").attr("hidden",true);
                        $(".clearafter").val("");
                      }
                      quitarPreloader();
                    }
                  });
                }
              } else {
                sendding = false;
                $("#pagarProducto").attr("disabled", false);
              }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
              AlertaPersonalizada('error', XMLHttpRequest.responseText);
              sendding = false;
              $("#pagarProducto").attr("disabled", false);
            }
          });
        }
      }
    } else {
      AlertaPersonalizada('Error',isValid);
      sendding = false;
      $("#pagarProducto").attr("disabled", false);
    }
  }
}
/** funcion para validar datos de comprobantes de venta que se impriman a un pdf */
function pagarProductoPdf(){
  var productos = crearArrayProductos();
  if(productos.length > 0){
    if($("#porConsumoPagoProducto").is(":checked")){
      var porConsumo = 1 ;
    } else {
      var porConsumo = 0 ;
    }
    var vuelto = $("#vueltoPagoProducto").text().replace("$","");
    var efectivo = $("#efectivoPagoProducto").val();
    var personas = $("#personaHacerCuenta").val();
    var tarjeta = $("#tarjetaPagoProducto").val();
    var bitcoin = $("#bitcoinPagoProducto").val();
    var pedidosYa = $("#pedidosYaPagoProducto").val();
    var tipoPago = $("option:selected", $("#tipoPagoProducto"));
    var descuentoDolar = $("#descuentoDolarPagoProducto").val();
    var descuento = $("#descuentoPagoProducto").val();
    var bandera = 0;
    if(tipoPago.attr("pdf") == "1"){
      bandera = 1;
      var datos = {
        cliente : $("#nombreClientePagoProducto").val(),
        idCliente : $("#idClienteProducto").val(),
        direccion : $("#direccionClientePagoProducto").val(),
        nit : $("#nitClientePagoProducto").val(),
        nrc : $("#nrcClientePagoProducto").val(),
        correlativo : $("#correlativoClientePagoProducto").val(),
        tipoCuenta : $("#tipoHacerCuenta").val(),
        porConsumo : porConsumo,
        idZona :	$("#idZonaCuenta").val(),
        zona : $("#nombreZonaCuenta").val(),
        tipoAumento : $("#tipoAumentoZonaCuenta").val(),
        aumento :$("#aumentoZonaCuenta").val(),
        idMesa : $("#mesaHacerCuenta").val(),
        idCaja : $("#cajaProducto").val(),
        tipoDoc : $("#tipoPagoProducto").val(),
        idPedidoGuardado : $("#idPedidoGuardado").val(),
        actual : $("option:selected", $("#tipoPagoProducto")).attr("actual"),
        idDoc : $("option:selected", $("#tipoPagoProducto")).attr("idDoc"),
        comentario :$("#comentarioGeneralOrden").val(),
        total : $("#totalPropPagoProducto").text().replace("$",""),
        propina : $("#propinaPagoProducto").text().replace("$",""),
        personas : personas,
        descuento : descuento,
        descuentoDolar : descuentoDolar,
        vuelto: vuelto,
        efectivo : efectivo,
        bitcoin : bitcoin,
        tarjeta : tarjeta,
        pedidosYa : pedidosYa,
        productos: JSON.stringify(productos)
      }
    }
    if(bandera == 1){
      window.open(url + "/Touch");

      $.ajax({
        type: "POST",
        url: url+"/PagarProducto",
        data: datos,
        dataType: 'json',
        success: function (respuesta){
          bandera = 0;
          Alerta(respuesta.codigo);
          if(respuesta.codigo == 200){
            var id = respuesta.idFactura;
            var idPedido = respuesta.idPedido;
            //descargarInsumos(idPedido);
            imprimirComandaCocina(idPedido)
            window.open(url + "/FacturasDoc/" + id,"","");
            if(tipoPago.attr("pdf") == "1"){
              $(".home").click();
            }
          }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
          AlertaPersonalizada('error', XMLHttpRequest.responseText);
        }
      });
    }
  }
}

function pagarServicio(){
  var tr = $("#listaServicios tr.prim ");
  servicios = [];
  tr.each(function(){
    var info = $(this).find("input.info");
    ser = {
      cantidad : $(this).find(".cantidad").text(),
      descripcion : info.attr('nombre'),
      servicio : info.attr('idServicio'),
      servicioCategoria : info.attr('idServicioCategoria'),
      senorita : info.attr('idSenorita'),
      senoritaCategoria : info.attr('idSenoritaCategoria'),
      monto : info.attr('precio')
    }
    servicios.push(ser);
  });

  if(servicios.length > 0){
    var datos = {
      idCliente:$("#buscarClienteServicio").val(),
      cliente : $("#clienteServicio").val(),
      idCaja : $("#cajaServicio").val(),
      tipoDoc : $("#tipoPagoServicio").val(),
      actual : $("option:selected", $("#tipoPagoServicio")).attr("actual"),
      idDoc : $("option:selected", $("#tipoPagoServicio")).attr("idDoc"),
      total: $("#totaltds").text().replace("$",""),
      vuelto: $("#vueltoPagoServicio").text().replace("$",""),
      efectivo : $("#efectivoPagoServicio").val(),
      servicio: JSON.stringify(servicios)
    }
    $.ajax({
      type: "POST",
      url: url+"/PagarServicio",
      data: datos,
      dataType: 'json',
      success: function (respuesta){
        Alerta(respuesta.codigo);
        if(respuesta.codigo == 200){
          //console.log(respuesta);
          var id = respuesta.idFactura;
          imprimirTicketSenorita(id);
          $(".home").click();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        AlertaPersonalizada('error', XMLHttpRequest.responseText);
      }
    });
  }
}

function quitarPropina() {
  if($("#quitarPropina").is(":checked")){
    // var cobroAnterior = $("#cobroPropina").val();
    $("#cobroPropina").val("No");
    calculoTotal();
    // $("#cobroPropina").val(cobroAnterior);
  } else {
    var cobroAnterior = $("#cobroPropina").val();
    // if(cobroAnterior == "Si"){
    $("#cobroPropina").val("Si");
    calculoTotal();
    // $("#cobroPropina").val(cobroAnterior);
    // }

  }
}

/***********************************************************************************/
/**************************** FUNCIONES DE IMPRESION *******************************/
/***********************************************************************************/

function imprimirTicketSenorita(idFactura) {
  $.ajax({
    type: "POST",
    url: url + "/ImprimirTicketServicio/" + idFactura,
    data: '',
    dataType: 'json',
    success: function (respuesta) {
      Alerta(respuesta.codigo);
      if (respuesta.codigo == 200) {
        setTimeout(function(){
          $.post("http://"+respuesta.servidor+"/imprimir/printSenorita.php", {
            //$.post("http://localhost/imprimir/printSenorita.php", {
            datos: respuesta.datos,
          });
        },1000);

      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });

}

function imprimirTicketProducto(idFactura,vuelto="",efectivo="") {
  datos = {};
  if(efectivo != "" || vuelto !=""){
    datos = {
      vuelto : vuelto,
      efectivo : efectivo,
    }
  }
  $.ajax({
    type: "POST",
    url: url + "/ImprimirTiket/" + idFactura,
    data: datos,
    dataType: 'json',
    success: function (respuesta) {
      Alerta(respuesta.codigo);
      if (respuesta.codigo == 200) {
        $.post("http://"+respuesta.datos.servidor+"/imprimir/printTicket.php", {
          datos: respuesta.datos.ticket,
          reseniaGoogle:    respuesta.datos.reseniaGoogle    || '',
          reseniaFacebook:  respuesta.datos.reseniaFacebook  || '',
          reseniaInstagram: respuesta.datos.reseniaInstagram || '',
        });
        setTimeout(function(){
          location.reload();
        },1500);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });

}
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
    url: url + "/ImprimirVenta/" + idFactura,
    data: datos,
    dataType: 'json',
    success: function (respuesta) {
      Alerta(respuesta.codigo);
      if (respuesta.codigo == 200) {
        $.post("http://"+respuesta.datos.servidor+"/imprimir/print.php", {
          datos: respuesta.datos.ticket,
        });
        setTimeout(function(){
          location.reload();
        },1500);
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });
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
        setTimeout(function(){
          //console.log(respuesta.servicio);
          //$.post(""+url+"/imprimir/printMovimientoCaja.php", {
          $.post("http://"+respuesta.servidor+"/imprimir/printMovimientoCaja.php", {
            datos: respuesta.servicio,
            tipo: respuesta.tipo,
            recurso: respuesta.recurso,
            ip: respuesta.ip,
          });
        },1000);
        $(".home").click();
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });

}

function imprimirCuenta(idPedido,prop=""){
  $.ajax({
    type: "POST",
    url: url+"/ImprimirCuenta",
    data: {
      idPedido: idPedido,
      prop: prop
    },
    dataType: 'json',
    success: function (respuesta){
      if(respuesta.codigo == 200){
        setTimeout(function(){
          $.post("http://"+respuesta.servidor+"/imprimir/printCuenta.php", {
            //$.post("http://localhost/imprimir/printCuenta.php", {
            datos: respuesta.datos,
          });
        },1000);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown){
      // console.log(XMLHttpRequest);
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });
}

function imprimirComandaCocina(idPedido){
  $.ajax({
    type: "POST",
    url: url+"/ImprimirComandaCocina",
    data: {
      idPedido: idPedido
    },
    dataType: 'json',
    success: function (respuesta){
      if(respuesta.codigo == 200){
        //setTimeout("reload();",1000);
        $.each($.parseJSON(respuesta.servidor),function (index,value) {
          setTimeout(function(){
            //console.log(value);
            $.post("http://"+value.servidor+"/imprimir/printComanda.php", {
              datos: value.datos,
            });
          },500);
        });
        setTimeout(function() {
          quitarPreloader();
        },1000)
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown){
      console.error('ImprimirComandaCocina error:', XMLHttpRequest.status, textStatus);
      quitarPreloader();
    }
  });
}

function reImprimirTicketProducto(idFactura) {
  $.ajax({
    type: "POST",
    url: url + "/ImprimirTicketProducto/" + idFactura,
    data: '',
    dataType: 'json',
    success: function (respuesta) {
      Alerta(respuesta.codigo);
      if (respuesta.codigo == 200) {
        $.post("http://"+respuesta.servidor+"/imprimir/printProducto.php", {
          //$.post("http://localhost/imprimir/printProducto.php", {
          datos: respuesta.datos,
          tipo: respuesta.tipo,
          recurso: respuesta.recurso,
          ip: respuesta.ip,
        });

      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      AlertaPersonalizada('error', XMLHttpRequest.responseText);
    }
  });

}

function reload() {
  location.href = url + "/Touch";
}

$(document).on('click', '.icheck-success', function(event) {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
});

function mostrarPreloader()
{
  $('.preloader').css('height', '100%');
  $('.preloader').children().show();
}

function quitarPreloader()
{
  $('.preloader').css('height', 0);
  setTimeout(function () {
    $('.preloader').children().hide();
  }, 500);
}

// Descuento por línea de carrito
$(document).on("click", ".btnDescuentoLinea", function(){
  var iter = $(this).attr("idt");
  Swal.fire({
    title: 'Descuento en línea (%)',
    input: 'text',
    inputValue: parseFloat($("#cantidad"+iter).attr("descuentoLinea") || 0),
    showCancelButton: true,
    confirmButtonText: 'Aplicar',
    cancelButtonText: 'Cancelar',
    didOpen: function(){
      var inp = Swal.getInput();
      inp.style.color = 'white';
      inp.style.background = 'transparent';
      inp.style.border = '1px solid #aaa';
    },
    inputValidator: function(value){
      if(value === '' || isNaN(value)) return 'Ingrese un porcentaje válido';
      if(parseFloat(value) < 0 || parseFloat(value) > 100) return 'El descuento debe ser entre 0 y 100';
    }
  }).then(function(result){
    if(result.isConfirmed){
      var pct = parseFloat(result.value);
      var precioOriginal = parseFloat($("#cantidad"+iter).attr("precioOriginal"));
      var nprecio = 0;
      $("li.liContenedorProducto[it='"+iter+"']").each(function(){
        nprecio += parseFloat($(this).attr("aumento") || 0);
      });
      var base = precioOriginal + nprecio;
      var nuevo = base * (1 - pct/100);
      $("#cantidad"+iter).attr("descuentoLinea", pct);
      $("#cantidad"+iter).attr("precio", nuevo.toFixed(2));
      var labelDesc = pct > 0 ? " (-"+pct+"%)" : "";
      $("#preciotxt"+iter).text("$"+nuevo.toFixed(2)+labelDesc);
      setTimeout(function(){ calculoTotal(); }, 200);
    }
  });
});

// Descuento de empleado
$(document).on("click", "#btnDescuentoEmpleado", function(){
  var inpt = '<label for="codigoEmpleadoDesc">Ingrese Código del Empleado</label>';
  inpt += '<div class="input-group mb-3">';
  inpt += '  <input type="text" class="form-control" id="codigoEmpleadoDesc" placeholder="">';
  inpt += '</div>';
  Swal.fire({
    title: 'Descuento Empleado',
    html: inpt,
    showCancelButton: true,
    confirmButtonText: 'Aplicar',
    cancelButtonText: 'Cancelar',
    preConfirm: function(){
      var codigo = $("#codigoEmpleadoDesc").val();
      if(!codigo){ Swal.showValidationMessage('Ingrese el código'); return false; }
      return $.ajax({
        type: "POST",
        url: url+"/Touch/ValidarEmpleado",
        data: {codigo: codigo, csrf_test_name: token},
        dataType: 'json'
      }).then(function(resp){ return resp; })
        .catch(function(){ Swal.showValidationMessage('Error al validar'); });
    }
  }).then(function(result){
    if(result.isConfirmed && result.value){
      var resp = result.value;
      if(resp.bandera == "1"){
        var pct = parseFloat(resp.descuento) || 0;
        if(pct > 0){
          $("#descuentoPagoProducto").attr("permiso","1");
          $("#descpor").html('<a class="btn btn-default descuentopor-opener"><i class="fa fa-keyboard"></i></a>');
          $("#descuentoPagoProducto").val(pct.toFixed(2)).trigger("change");
          AlertaPersonalizada('success', 'Descuento '+pct+'% aplicado para '+resp.nombre);
        } else {
          AlertaPersonalizada('error', 'El descuento de empleado está configurado en 0%. Actualícelo en Configuraciones.');
        }
      } else {
        AlertaPersonalizada('error', 'Código de empleado no válido');
      }
    }
  });
});
