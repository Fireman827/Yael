var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Planillas';
var tablaAdmin;
$(document).ready(function(){
    var idPeriodoPlanilla = $('.m-t-n-xs').attr('id');
	tablaAdmin = RenderizarTabla(url,'/PlanillasDetalleMostrar/'+idPeriodoPlanilla,token);
});

$(document).on("click",".m-t-n-xs",function(event){
	reload();
});

$(document).on("click",".PlanillasGenerar",function(event){
	event.preventDefault();
	var idPeriodo = "0";
	var dataString = "idPeriodo=" + idPeriodo + "&csrf_test_name=" + $("#csrf_token_id").val();
	$.ajax({
        type: "POST",
        url: url + "/PlanillasGenerar",
        data: dataString,
        dataType: 'json',
        success: function (respuesta) {
            Alerta(respuesta.codigo);
            if (respuesta.codigo == 200) {					
                setTimeout("refresh();", 1500);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            AlertaPersonalizada('error', XMLHttpRequest.responseText);
        }
    });
});

$(document).on("click",".PlanillasCambiarEstado", function(event){
	event.preventDefault()
	var idPlanilla = $(this).attr("idPlanilla");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idPlanilla=" + idPlanilla+"&csrf_test_name="+token;
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
				url: url+"/PlanillasCambiarEstado",
				data: dataString,
				dataType: 'json',
				success: function (respuesta){
					Alerta(respuesta.codigo);
					if (respuesta.codigo == 200){
						setTimeout("reload();", 1500);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});
});

function reload() {
	location.href = url+'/'+padre;
}

function refresh() {
	//Actualizamos la página
	location.reload();
};

$(document).on("click",".PlanillasBoletaImprimir", function(event){
	event.preventDefault()
	var idPlanilla = $(this).attr("idPlanilla"); 
    window.open(url+'/'+padre+'/PlanillasBoletaImprimir/'+idPlanilla, '_blank');
});