var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Configuraciones';
var formulario = 'FrmConfiguraciones';
$(document).ready(function(){
	var reglas = {
	};
	validarDatos(formulario,reglas);
});
$(document).on("change",'#facturacionElectronica', function(){
	if($(this).val() == "Si"){
		$(".feOptions").removeAttr("hidden");
		if($("#entornoFE").val() == "prueba"){
			$(".prueba").removeAttr("hidden");
			$(".produccion").attr("hidden",true);
		} else {
			$(".produccion").removeAttr("hidden");
			$(".prueba").attr("hidden",true);
		}
	} else {
		$(".feOptions").attr("hidden",true);
	}
});
$(document).on("change",'#entornoFE', function(){
	if($(this).val() == "prueba"){
		$(".prueba").removeAttr("hidden");
		$(".produccion").attr("hidden",true);
	} else {
		$(".produccion").removeAttr("hidden");
		$(".prueba").attr("hidden",true);
	}
});
$(document).on("change",'#cobroPropina', function(){
	if($(this).val() == "Si"){
		$(".propina").removeAttr("hidden");
	} else {
		$(".propina").attr("hidden",true);
	}
});
$(document).on("click",'#guardarDatos', function(){
	AgregarEditar();
});
$(document).on("change",'#departamentoEmisor', function(){
	var idDepartamento = $(this).val();
	if(idDepartamento!=0){
		$.ajax({
			type: "POST",
			url: url + "/Clientes/ClienteMunicipios",
			data: {
				'idDepartamento':idDepartamento
			},
			dataType: 'json',
			success: function (respuesta) {

				if(respuesta.municipios!=""){
					$('#municipioEmisor').html(respuesta.municipios);
				}

			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				AlertaPersonalizada('error', XMLHttpRequest.responseText);
			}
		});
	}
});
var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		var parametro = $("#parametro").val();
		var FrmCliente = $("#FrmConfiguraciones");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmCliente[0]);
		}
		ruta = "ConfiguracionesEditar";
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
					setTimeout("reload();", 1500);
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
	// location.href = url + '/' + padre;
	location.reload();
}
