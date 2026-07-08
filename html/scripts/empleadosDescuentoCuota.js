var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'EmpleadosDescuentoCuota';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/EmpleadosDescuentoCuotaMostrar',token);
	
	var formulario = "FrmEmpleadosDescuentoCuota";
	var reglas = {
		empleadoEmpleadoDescuentoCuota:{
			required: true
		},
		idInstitucionEmpleadoDescuentoCuota:{
			required:true
		},
		montoEmpleadoDescuentoCuota:{
			required:true
		},
        numeroCuotasEmpleadoDescuentoCuota:{
			required:true
		},
        idPeriodoEmpleadoDescuentoCuota:{
			required:true
		},
		descripcionEmpleadoDescuentoCuota:{
			required:true
		}
	};
	validarDatos(formulario,reglas);

	HacerAutoCompletar('buscarEmpleado','nombreEmpleado','/EmpleadosDescuentoCuotaAutocompleteEmpleado',function (e, data) {
		$('#empleadoEmpleadoDescuentoCuota').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
		$('#idEmpleadoEmpleadoDescuentoCuota').val(data.idEmpleado);
	});
});

function onAutocompleted(e, data) {
	$('#empleadoEmpleadoDescuentoCuota').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
	$('#idEmpleadoEmpleadoDescuentoCuota').val(data.idEmpleado);
}

$(document).on("click",".EmpleadosDescuentoCuotaCambiarEstado", function(event){
	event.preventDefault()
	var idEmpleadoDescuentoCuota = $(this).attr("idEmpleadoDescuentoCuota");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idEmpleadoDescuentoCuota=" + idEmpleadoDescuentoCuota+"&csrf_test_name="+token;
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
				url: url+"/EmpleadosDescuentoCuotaCambiarEstado",
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

$(document).on("click",".EmpleadosDescuentoCuotaEliminar", function(event){
	event.preventDefault()
	var idEmpleadoDescuentoCuota = $(this).attr("idEmpleadoDescuentoCuota");
	var dataString = "idEmpleadoDescuentoCuota=" + idEmpleadoDescuentoCuota + "&csrf_test_name="+token;
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
				url: url+"/EmpleadosDescuentoCuotaEliminar",
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
		
		var FrmEmpleadosDescuentoCuota = $("#FrmEmpleadosDescuentoCuota");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmEmpleadosDescuentoCuota[0]);
		}
		if ($("#proceso").val() == "Editar"){
			ruta = "EmpleadosDescuentoCuotaEditar";			
		}
		if ($("#proceso").val() == "Agregar"){
			ruta = "EmpleadosDescuentoCuotaAgregar";
		}
			
		if(true){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				cache: false,
				data: Frm ? Frm : FrmEmpleadosDescuentoCuota.serialize(),
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
};