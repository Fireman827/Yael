var url = window.location.origin;
var token = $("#csrf_token_id").val();
var porcentajeRetencion = parseFloat('0.01');
var padre = 'Proveedores';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ProveedorMostrar',token);

	var formulario = "FrmProveedor";
	var tipoFormulario = $('#avanzado').val();
	if(tipoFormulario=='true'){
		var reglas = {
			nombreProveedor:{
				required: true
			},
			nrcProveedor:{
				required:true
			},
			nitProveedor:{
				required:true
			},
			direccionProveedor:{
				required:true
			},
			departamentoProveedor:{
				required:true
			},
			municipioProveedor:{
				required:true
			},

		};
		validarDatos(formulario,reglas);
	} else {
		var reglas = {
			nombreProveedor:{
				required: true
			},
			nrcProveedor:{
				required:true
			},
			direccionProveedor:{
				required:true
			},
		};
		validarDatos(formulario,reglas);
	}

	$('#departamentoProveedor').change(function(){
		var idDepartamento = $(this).val();
		if(idDepartamento!=0){
			$.ajax({
				type: "POST",
				url: url + "/ProveedorMunicipios",
				data: {
					'idDepartamento':idDepartamento
				},
				dataType: 'json',
				success: function (respuesta) {
					if(respuesta.municipios!=""){
						$('#municipioProveedor').html(respuesta.municipios);
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					AlertaPersonalizada('error', XMLHttpRequest.responseText);
				}
			});
		}
	});

});

$(document).on("click", "#agregarContacto", function(event) {
	event.preventDefault();
	var string_tr = "<tr><td><input type='text' class='form-control nombreContacto text-uppercase upper' name='nombreContacto' placeholder='Nombre'></td><td><input type='text' class='form-control cargoContacto text-uppercase upper' name='cargoContacto' placeholder='Cargo'></td><td><input type='text' class='form-control telefonoContacto tel' name='telefonoContacto' placeholder='0000-0000'></td><td><input type='text' class='form-control correoContacto' name='correoContacto' placeholder='alias@dominio.com' ></td><td><a class='btn btn-block btn-danger borrarContacto' role='button'><i class='fa fa-trash'></i></a></td></tr>";
	$("#tablaContactos tbody").append(string_tr);
	$('.tel').mask('0000-0000');
});

$(document).on("click",".borrarContacto",function(){
	$(this).parents('tr').remove();
});

function extraerDatos(){
	var arrayContacto = [];
	$('#tablaContactos tbody tr').each(function(){
		var nombre = $(this).children('td').children('.nombreContacto').val();
		var cargo = $(this).children('td').children('.cargoContacto').val();
		var telefono = $(this).children('td').children('.telefonoContacto').val();
		var correo = $(this).children('td').children('.correoContacto').val();

		var contacto = [nombre,cargo,telefono,correo];
		arrayContacto.push(contacto);
	});
	return arrayContacto;
}

$(document).on("click", "#btnGuardar", function(event){
  event.preventDefault();
	PermisosAgregarEditar();
});

$(document).on("click",".ProveedorCambiarEstado", function(event){
	event.preventDefault()
	var idProveedor = $(this).attr("idProveedor");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idProveedor=" + idProveedor+"&csrf_test_name="+token;
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
				url: url+"/ProveedorCambiarEstado",
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

$(document).on("click",".ProveedorEliminar", function(event){
	event.preventDefault()
	var idProveedor = $(this).attr("idProveedor");
	var dataString = "idProveedor=" + idProveedor + "&csrf_test_name="+token;
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
				url: url+"/ProveedorEliminar",
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
	if(!guardando && $('#avanzado').val()=='true'){
		guardando = 1;
		var contactos = extraerDatos();
		var contactosJSON = JSON.stringify(contactos);
		var nombre = $('#nombreProveedor').val();
		var razonSocial = $('#razonSocialProveedor').val();
		var departamento = $('#departamentoProveedor').val();
		var municipio = $('#municipioProveedor').val();
		var direccion = $('#direccionProveedor').val();
		var nit = $('#nitProveedor').val();
		var nrc = $('#nrcProveedor').val();
		var giro = $('#giroProveedor').val();
		var categoria = $('#categoriaProveedor').val();
		var telefono = $('#telefonoProveedor').val();
		var correo = $('#correoProveedor').val();
		var id = $('#idProveedor').val();

		var bancoProveedor = $('#bancoProveedor').val();
		var cuentaProveedor = $('#cuentaProveedor').val();
		var bancoProveedor2 = $('#bancoProveedor2').val();
		var cuentaProveedor2 = $('#cuentaProveedor2').val();

		var dataString = {
			'idProveedor': id,
			'nombreProveedor': nombre,
			'razonSocialProveedor': razonSocial,
			'departamentoProveedor': departamento,
			'municipioProveedor': municipio,
			'direccionProveedor': direccion,
			'nitProveedor': nit,
			'nrcProveedor': nrc,
			'giroProveedor': giro,
			'bancoProveedor': bancoProveedor,
			'cuentaProveedor': cuentaProveedor,
			'bancoProveedor2': bancoProveedor2,
			'cuentaProveedor2': cuentaProveedor2,
			'categoriaProveedor': categoria,
			'telefonoProveedor': telefono,
			'correoProveedor': correo,
			'porcentajeRetencionProveedor':porcentajeRetencion,
			'datosContactos': contactosJSON
		};

		var FrmProveedores = $("#FrmProveedor");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmProveedores[0]);
		}
		if ($("#proceso").val() == "Editar" && $('#avanzado').val() == "true"){
			ruta = "ProveedorEditarAvanzado";
		}
		if ($("#proceso").val() == "Agregar" && $('#avanzado').val() == "true"){
			ruta = "ProveedorAgregarAvanzado";
		}
		//Frm ? Frm : FrmProveedores.serialize()
		if(contactos.length!=0 && $('#avanzado').val() == "true"){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				data: dataString,
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
		} else {
			guardando = 0;
			AlertaPersonalizada('error','Debes ingresar almenos un contacto');
		}
	} else {
		guardando = 1;
		var nombre = $('#nombreProveedor').val();
		var direccion = $('#direccionProveedor').val();
		var nit = $('#nitProveedor').val();
		var nrc = $('#nrcProveedor').val();
		var telefono = $('#telefonoProveedor').val();
		var correo = $('#correoProveedor').val();
		var id = $('#idProveedor').val();

		var dataString = {
			'idProveedor': id,
			'nombreProveedor': nombre,
			'direccionProveedor': direccion,
			'nitProveedor': nit,
			'nrcProveedor': nrc,
			'telefonoProveedor': telefono,
			'correoProveedor': correo
		};

		var FrmProveedores = $("#FrmProveedor");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmProveedores[0]);
		}
		if ($("#proceso").val() == "Editar" && $('#avanzado').val() == "false") {
			ruta = "ProveedorEditar";
		}
		if ($("#proceso").val() == "Agregar" && $('#avanzado').val() == "false") {
			ruta = "ProveedorAgregar";
		}

		$.ajax({
			type: 'POST',
			url: url+'/'+ruta,
			data: dataString,
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
	var back = $("#backUrl").val();
	location.href = back ? url + "/" + back : url + "/" + padre;
}
