var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'PrestacionesConfigurar';
var tablaAdmin;
$(document).ready(function(){
	var formulario = "FrmPrestacionesConfigurar";
	var reglas = {
		isssCotizacion:{
			required: true
		},
		afpCotizacion:{
			required:true
		},
		techoIsssCotizacion:{
			required:true
		}
	};
	validarDatos(formulario,reglas);
});

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		
		var FrmPrestacionesConfigurar = $("#FrmPrestacionesConfigurar");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmPrestacionesConfigurar[0]);
		}

		ruta = "PrestacionesConfigurarEditar";			
				
		$.ajax({
            type: 'POST',
            url: url+'/PrestacionesConfigurarEditar',
            data: Frm ? Frm : FrmPrestacionesConfigurar.serialize(),
			contentType: false,
			processData: false,				
            dataType: 'json',
            success: function (respuesta){
                Alerta(respuesta.codigo);
                if (respuesta.codigo == 200){
                    setTimeout("reload();", 1500);
                    guardando = 0;
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
	location.href = url+'/'+padre;
}

function refresh() {
	//Actualizamos la página
	location.reload();
};

/** Control general de MODALES - Final*/
