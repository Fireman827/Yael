var url = window.location.origin;
var token = $("#csrf_token_id").val()
var padre = 'Respaldos';

$(document).on("click","#btnGuardar", function(){
	if($("#backupFile").val() != ""){
		AgregarEditar();
	} else {
		AlertaPersonalizada("Error","Seleccione un archivo");
	}
});
var guardando = 0;
function AgregarEditar() {
	if (!guardando) {
		guardando = 1;
		var FrmRestoreBackup = $("#FrmRestoreBackup");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmRestoreBackup[0]);
		}
		var ruta = "RespaldoHacer";
		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			cache: false,
			data: Frm ? Frm : FrmRestoreBackup.serialize(),
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta){
				Alerta(respuesta.codigo);
				if (respuesta.codigo == 200){
					setTimeout("reload();", 1500);
				} else{
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
	location.href = url + '/' + padre;
}
