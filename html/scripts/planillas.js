var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Planillas';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/PlanillasMostrar',token);
});

$(document).on("click",".m-t-n-xs",function(event){
	event.preventDefault();
    Swal.fire({
		title: '¿Desea generar la planilla?',
		text: "",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Generar',
		cancelButtonText: 'Cancelar',
	}).then((result) =>{
		if (result.value){
			$.ajax({
                type: "POST",
                url: url + "/PlanillasGenerar",
                data: {
                    'id':0
                },
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

$(document).on("click",".PlanillasEliminar", function(event){
	event.preventDefault()
	var idPeriodoPlanilla = $(this).attr("idPeriodoPlanilla");
	var dataString = "idPeriodoPlanilla=" + idPeriodoPlanilla + "&csrf_test_name="+token;
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
				url: url+"/PlanillasEliminar",
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

var guardando = 0;
function AgregarEditar(){
	if(!guardando){
		guardando = 1;
		
		var FrmPlanillas = $("#FrmPlanillas");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmPlanillas[0]);
		}
		if ($("#proceso").val() == "Editar"){
			ruta = "PlanillasEditar";			
		}
		if ($("#proceso").val() == "Agregar"){
			ruta = "PlanillasAgregar";
		}
			
		if(true){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				cache: false,
				data: Frm ? Frm : FrmPlanillas.serialize(),
				dataType: 'json',
				contentType: false,
				processData: false,
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
}

function reload() {
	location.href = url+'/'+padre;
}

function refresh() {
	//Actualizamos la página
	location.reload();
}

$(document).on("click",".PlanillasBoletasImprimir", function(event){
	event.preventDefault()
	var idPeriodo = $(this).attr("idPeriodo"); 
    window.open(url+'/'+padre+'/PlanillasBoletasImprimir/'+idPeriodo, '_blank');
});

$(document).on("click", ".PlanillasImprimir", function (event) {
	event.preventDefault()
	var idPeriodo = $(this).attr("idPeriodo");
	window.open(url + '/' + padre + '/PlanillasImprimir/' + idPeriodo, '_blank');
});