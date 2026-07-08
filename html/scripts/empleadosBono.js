var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'EmpleadosBono';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/EmpleadosBonoMostrar',token);
	
	var formulario = "FrmEmpleadosBono";
	var reglas = {
		empleadoEmpleadoBono:{
			required: true
		},
		montoEmpleadoBono:{
			required:true
		},
        idPeriodoEmpleadoBono:{
			required:true
		},
		descripcionEmpleadoBono:{
			required:true
		}
	};
	validarDatos(formulario,reglas);

	HacerAutoCompletar('buscarEmpleado','nombreEmpleado','/EmpleadosBonoAutocompleteEmpleado',function (e, data) {
		$('#empleadoEmpleadoBono').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
		$('#idEmpleadoEmpleadoBono').val(data.idEmpleado);
	});
});

function onAutocompleted(e, data) {
	$('#empleadoEmpleadoBono').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
	$('#idEmpleadoEmpleadoBono').val(data.idEmpleado);
}

$(document).on("click",".EmpleadosBonoCambiarEstado", function(event){
	event.preventDefault()
	var idEmpleadoBono = $(this).attr("idEmpleadoBono");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idEmpleadoBono=" + idEmpleadoBono+"&csrf_test_name="+token;
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
				url: url+"/EmpleadosBonoCambiarEstado",
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

$(document).on("click",".EmpleadosBonoEliminar", function(event){
	event.preventDefault()
	var idEmpleadoBono = $(this).attr("idEmpleadoBono");
	var dataString = "idEmpleadoBono=" + idEmpleadoBono + "&csrf_test_name="+token;
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
				url: url+"/EmpleadosBonoEliminar",
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
		
		var FrmEmpleadosBono = $("#FrmEmpleadosBono");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmEmpleadosBono[0]);
		}
		if ($("#proceso").val() == "Editar"){
			ruta = "EmpleadosBonoEditar";			
		}
		if ($("#proceso").val() == "Agregar"){
			ruta = "EmpleadosBonoAgregar";
		}
			
		if(true){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				cache: false,
				data: Frm ? Frm : FrmEmpleadosBono.serialize(),
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
