var url = window.location.origin;
var token = $("#csrf_token_id").val();
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/PagoMostrar',token);
});
function reload() {
	location.href = url+"/"+padre;
}
