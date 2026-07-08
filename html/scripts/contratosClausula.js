var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'ContratosClausula';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/ContratosClausulaMostrar',token);
	
	var formulario = "FrmContratoClausula";
	var reglas = {
		nombreContratoClausula:{
			required: true
		},
		descripcionContratoClausula:{
			required:true
		}
	};
	validarDatos(formulario,reglas);

	CKEDITOR.replace('editor1');
});

$(document).on("click",".ContratosClausulaCambiarEstado", function(event){
	event.preventDefault()
	var idContratoClausula = $(this).attr("idContratoClausula");
	var accion = $(this).data("accion").toLowerCase();
	var dataString = "idContratoClausula=" + idContratoClausula+"&csrf_test_name="+token;
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
				url: url+"/ContratosClausulaCambiarEstado",
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

$(document).on("click",".ContratosClausulaEliminar", function(event){
	event.preventDefault()
	var idContratoClausula = $(this).attr("idContratoClausula");
	var dataString = "idContratoClausula=" + idContratoClausula + "&csrf_test_name="+token;
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
				url: url+"/ContratosClausulaEliminar",
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
		var id = $('#idContratoClausula').val();
		var nombre = $('#nombreContratoClausula').val();
		//var descripcion = $('#descripcionContratoClausula').val();
		var descripcion = CKEDITOR.instances['editor1'].getData();

		var dataString = {
			'idContratoClausula': id,
			'nombreContratoClausula': nombre,
			'descripcionContratoClausula': escape(descripcion)
		};
		
		var FrmContratoClausula = $("#FrmContratoClausula");
		var Frm = false;
		if (window.FormData){
			Frm = new FormData(FrmContratoClausula[0]);
		}
		if ($("#proceso").val() == "Editar"){
			ruta = "ContratosClausulaEditar";			
		}
		if ($("#proceso").val() == "Agregar"){
			ruta = "ContratosClausulaAgregar";
		}
		//Frm ? Frm : FrmContratoClausula.serialize()		
		if(cajaDocumentos.length!=0){
			$.ajax({
				type: 'POST',
				url: url+'/'+ruta,
				data: dataString,				
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
		} else {
			guardando = 0;
			AlertaPersonalizada('error','Debes ingresar almenos un contacto');
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

/** Control general de MODALES - Final*/
