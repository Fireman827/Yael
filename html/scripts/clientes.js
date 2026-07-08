var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Clientes';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ClienteMostrar',token);

	var formulario = "FrmCliente";
	var tipoFormulario = $('#avanzadoCliente').val();
	if(tipoFormulario=='1'){
		var reglas = {
			nombreCliente:{
				required: true,
			},
			direccionCliente:{
				required: true,
			},
			telefonoCliente:{
				required: true
			},
			emailCliente:{
				email: true,
				required:true
			},
			departamentoCliente:{
				required:true
			},
			municipioCliente:{
				required: true
			}
			// duiCliente:{
			// 	required:true
			// },
			// nitCliente:{
			// 	required:true
			// },
			// nrcCliente:{
			// 	required:true
			// },
			// referenciaCliente:{
			// 	required:true
			// },
			// idCategoriaCliente:{
			// 	required:true
			// }
		};
		validarDatos(formulario,reglas);
	} else {
		var reglas = {
			nombreCliente:{
				required: true,
			},
			direccionCliente:{
				required: true,
			},
			telefonoCliente:{
				required: true
			},
			idCategoriaCliente:{
				required:true
			}
		};
		validarDatos(formulario,reglas);
	}
});
$(document).on("change",'#documentoFacturacionCliente', function(){
	var documentoFacturacionCliente = $(this).val();
	if(documentoFacturacionCliente=='CCF'){
		if($("#facturarConCliente").val() == "DUI"){
			$('#duiCliente').attr('required',true)
		} else {
			$('#nitCliente').attr('required',true)
		}
		$('#nrcCliente').attr('required',true)
		$('#giroCliente').attr('required',true)
		$('#giroCliente').attr('required',true)
		$('#telefonoCliente').attr('required',true)
		$('#emailCliente').attr('required',true)
		//$("#duiCliente").submit();
	}else {
		$('#nrcCliente').attr('required',false)
		$('#nitCliente').attr('required',false)
		$('#giroCliente').attr('required',false)
		$('#emailCliente').attr('required',false)
		$('#giroCliente').removeClass('is-invalid')
		$('#nrcCliente').removeClass('is-invalid')
		$('#duiCliente').attr('required',false)
		//$("#nitCliente").submit();
	}
	// validarDatos(formulario,reglas);
});
$(document).on("change",'#facturarConCliente', function(){
	var facturarConCliente = $(this).val();
	if(facturarConCliente=='DUI'){
			$('#duiCliente').attr('required',true)
			$('#nitCliente').attr('required',false)
	}else {
		$('#duiCliente').attr('required',false)
		$('#nitCliente').attr('required',true)
	}
	// $("#documentoFacturacionCliente").trigger("change");
});
$(document).on("change",'#departamentoCliente', function(){
	var idDepartamento = $(this).val();
	if(idDepartamento!=0){
		$.ajax({
			type: "POST",
			url: url + "/ClienteMunicipios",
			data: {
				'idDepartamento':idDepartamento
			},
			dataType: 'json',
			success: function (respuesta) {

				if(respuesta.municipios!=""){
					$('#municipioCliente').html(respuesta.municipios);
				}

			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
});

$(document).on("click",".ClienteCambiarEstado", function(event){
	event.preventDefault()
	var idCliente = $(this).attr("idCliente");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idCliente=" + idCliente+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea "+ accion+" este registro?!",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, '+accion,
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/ClienteCambiarEstado",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						tablaAdmin.ajax.reload(null, false);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

$(document).on("click",".ClienteEliminar", function(event){
	event.preventDefault()
	var idCliente = $(this).attr("idCliente");
	var dataString = "idCliente=" + idCliente + "&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro de eliminar este regitro?!",
		icon: 'question',
		target:'#page-top',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Eliminar',
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/ClienteEliminar",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						tablaAdmin.ajax.reload(null, false);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		var parametro = $("#parametro").val();
		guardando = 1;
		var FrmCliente = $("#FrmCliente");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmCliente[0]);
		}
		if ($("#proceso").val() == "Editar" && $('#avanzadoCliente').val() == "1"){
			ruta = "ClienteEditarAvanzado";
		}
		if ($("#proceso").val() == "Agregar" && $('#avanzadoCliente').val() == "1"){
			ruta = "ClienteAgregarAvanzado";
		}
		if ($("#proceso").val() == "Editar" && $('#avanzadoCliente').val() == "0") {
			ruta = "ClienteEditar";
		}
		if ($("#proceso").val() == "Agregar" && $('#avanzadoCliente').val() == "0") {
			ruta = "ClienteAgregar";
		}
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmUsuario.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta){
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200){
					if (parametro != ""){
						window.close();
					}else {
						setTimeout("reload();", 1500);
					}
				} else {
					guardando = 0;
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				guardando = 0;
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
}

function reload() {
	location.href = url+"/"+padre;
}
