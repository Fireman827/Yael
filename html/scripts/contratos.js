var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Contratos';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ContratosMostrar',token);
	
	var formulario = "FrmContratos";
	var reglas = {
		empleadoContrato:{
			required: true
		},
		duiContrato:{
			required:true
		},
		nitContrato:{
			required:true
		},
		desdeContrato:{
			required:true
		},
		hastaContrato:{
			required:true
		},
		horarioContrato:{
			required:true
		}
	};
	validarDatos(formulario,reglas);

	HacerAutoCompletar('buscarEmpleado','nombreEmpleado','/ContratosAutocompleteEmpleado',function (e, data) {
		$('#empleadoContrato').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
		$('#idEmpleadoContrato').val(data.idEmpleado);
		$('#duiContrato').val(data.duiEmpleado);
		$('#nitContrato').val(data.nitEmpleado);
	});
});

function onAutocompleted(e, data) {
	$('#empleadoContrato').val(data.nombreEmpleado+" "+data.apellidoEmpleado);
	$('#idEmpleadoContrato').val(data.idEmpleado);
	$('#duiContrato').val(data.duiEmpleado);
	$('#nitContrato').val(data.nitEmpleado);
}

$(document).on("click",".ContratosPdf",function(event){
	event.preventDefault();
	var idContrato = $(this).attr("idContrato"); 
    window.open(url+'/'+padre+'/ContratosPdf/'+idContrato, '_blank');
});

$(document).on("click",".ContratosCambiarEstado", function(event){
	event.preventDefault();
	var idContrato = $(this).attr("idContrato");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idContrato=" + idContrato+"&csrf_test_name="+token;
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
				url: url+"/ContratosCambiarEstado",
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

$(document).on("click",".ContratosEliminar", function(event){
	event.preventDefault()
	var idContrato = $(this).attr("idContrato");
	var dataString = "idContrato=" + idContrato + "&csrf_test_name="+token;
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
				url: url+"/ContratosEliminar",
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
		
		var FrmContratos = $("#FrmContratos");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmContratos[0]);
		}
		if ($("#proceso").val() == "Editar"){
			ruta = "ContratosEditar";			
		}
		if ($("#proceso").val() == "Agregar"){
			ruta = "ContratosAgregar";
		}
			
		if(true){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				cache: false,
				data: Frm ? Frm : FrmContratos.serialize(),
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
	location.reload();
};

/** Control general de MODALES - Final*/
