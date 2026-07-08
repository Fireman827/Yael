var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Empleados';
var tablaAdmin;
$(document).ready(function(){
	$(".divDatosUsuario").hide();
	tablaAdmin = RenderizarTabla(url,'/EmpleadosMostrar',token);
	
	var formulario = "FrmEmpleados";
	var reglas = {
		nombreEmpleado:{
			required: true
		},
		apellidoEmpleado:{
			required:true
		},
		nitEmpleado:{
			required:true
		},
		duiEmpleado:{
			required:true
		},
		direccionEmpleado:{
			required:true
		},
		telefono1Empleado:{
			required:true
		}
	};
	validarDatos(formulario,reglas);

	var formulario = "FrmEmpleadosRapido";
	var reglas = {
		nombreEmpleado:{
			required: true
		},
		apellidoEmpleado:{
			required:true
		},
		nitEmpleado:{
			required:true
		},
		duiEmpleado:{
			required:true
		},
		direccionEmpleado:{
			required:true
		},
		telefono1Empleado:{
			required:true
		}
	};
	validarDatos(formulario,reglas);

	$("#afp").click(function (event) {
		if ($(this).is(":checked")) {
			$("#afp").attr('checked', true);
			$('#afpEmpleado').val('SI');
		} else {
			$('#afpEmpleado').val('NO');
			$("#afp").attr('checked', false);
		}
	});

	$("#isss").click(function (event) {
		if ($(this).is(":checked")) {
			$("#isss").attr('checked', true);
			$('issspEmpleado').val('SI');
		} else {
			$('#isssEmpleado').val('NO');
			$("#isss").attr('checked', false);
		}
	});

	$("#renta").click(function (event) {
		if ($(this).is(":checked")) {
			$("#renta").attr('checked', true);
			$('#rentaEmpleado').val('SI');
		} else {
			$('#rentaEmpleado').val('NO');
			$("#renta").attr('checked', false);
		}
	});

});

$(document).on("click", "#hacerUsuario", function(event){
	if($(this).is(":checked")){
		$(".divDatosUsuario").show();
	}
	else{
		$(".divDatosUsuario").hide();
	}
});
  

$(document).on("click",".EmpleadosCambiarEstado", function(event){
	event.preventDefault()
	var idEmpleado = $(this).attr("idEmpleado");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idEmpleado=" + idEmpleado+"&csrf_test_name="+token;
	Swal.fire({
		title: 'Alerta!!',
		text: "Estas seguro que desea "+ accion+" este registro?!",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, '+accion,
		cancelButtonText: 'Cancelar'
	}).then((result) =>{
		if (result.value){
			$.ajax({
				type: "POST",
				url: url+"/EmpleadosCambiarEstado",
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

$(document).on("click",".EmpleadosEliminar", function(event){
	event.preventDefault()
	var idEmpleado = $(this).attr("idEmpleado");
	var dataString = "idEmpleado=" + idEmpleado + "&csrf_test_name="+token;
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
				url: url+"/EmpleadosEliminar",
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
		guardando = 1;
		var ruta = "";
		var valor = "";
		var proceso = $("#proceso").val();
		var validar = true;
		if(proceso == "Editar" || proceso == "Agregar"){
			$("#tablaEmpleadoFamilia tbody tr").each(function() {
				var camp1 = $(this).find(".nombre").text();
				var camp2 = $(this).find(".apellido").text();
				var camp3 = $(this).find(".parentesco").text();
				valor += camp1 + "/" + camp2 + "/" + camp3 + "#";
			});
			$("#familiaresEmpleado").val(valor);
			if ($('#familiaresEmpleado').val() == ""){
				validar = false;
			}
		}
		
		var FrmEmpleados = $("#FrmEmpleados");
		var Frm = false;
		if (window.FormData){ Frm = new FormData(FrmEmpleados[0]); }

		ruta = 	(proceso == "Editar")  ? "EmpleadosEditar" : (
			   	(proceso == "Agregar") ? "EmpleadosAgregar" : (
			   	(proceso == "AgregarRapido") ? "EmpleadosAgregarRapido" : (
				(proceso == "EditarRapido") ? "EmpleadosEditarRapido" : "" )));
		if (validar){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,				
				cache: false,
				data: Frm ? Frm : FrmEmpleados.serialize(),			
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
		} else {
			guardando = 0;
			AlertaPersonalizada('error','Debes agregar al menos un beneficiario');
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

$(document).on("click", "#AgregarEmpleadoFamilia", function() {
	var nombre=$("#nombre_f").val();
	var apellido=$("#apellido_f").val();
	var parentesco=$("#parentesco_f").val();
	var error=0;
	if(nombre=="" || apellido=="" || parentesco==""){
	  	var error=1;
	}
	if(error==1){
		AlertaPersonalizada('error','¡Por favor complete todos los campos!');
	} else {
		var existe1 = false;
		$("#tablaEmpleadoFamilia tbody tr").each(function() {
			camp1 = $(this).find(".nombre").text();
			camp2 = $(this).find(".apellido").text();
			camp3 = $(this).find(".parentesco").text();
			if (camp1 == nombre && camp2==apellido && camp3==parentesco) {
				existe1 = true;
			}
			$("#nombre_f").val("");
			$("#apellido_f").val("");
			$("#parentesco_f").val("");
		});
		if (existe1){
			AlertaPersonalizada('error','¡La persona ya existe!');
		} else {
			var app = "<tr><td class='nombre'>"+nombre+"</td><td class='apellido'>"+apellido+"</td><td class='parentesco'>"+parentesco+"</td><td class='text-center'><a class='btn btn-danger btn-block  EmpleadosFamiliaBorrar' type='button'> <span class='fa fa-trash'></span> </a></td></tr>";
			$("#tablaEmpleadoFamilia tbody").append(app);
		}
	}
});

$(document).on('click', '.EmpleadosFamiliaBorrar', function(e) {
	$(this).closest('tr').remove();
});

/** Control general de MODALES - Final*/
