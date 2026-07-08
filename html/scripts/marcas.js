var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Marcas';
var tablaAdmin;
$(document).ready(function(){
	tablaAdmin = RenderizarTabla(url,'/MarcaMostrar',token);
	hora();
});


$(document).on("click","#marcaEntrada", function(event){
	event.preventDefault();
	var dataString = "&csrf_test_name="+token;
                Swal.fire({
                    title: "Alerta!",
                    text: "Ingrese Codigo de Usuario",
                    showCancelButton: true,
                    input: 'password',
                    inputAttributes: {
                        type:"newpassword",
                        placeholder: "Codigo",
                        readonly : true,
                        onfocus:"this.removeAttribute('readonly');"
                      },
                }).then((result) => {
                    if(result.isDismissed == false){
                        if (result.value!= "") {
                            var clave = result.value;
                            $.ajax({
                                type: "POST",
                                url: url+"/MarcaEntrada",
                                data: {clave:clave},
                                dataType: 'json',
                                success: function (respuesta){
                                    if(respuesta.codigo == 500){
                                        Swal.fire({
                                            icon: 'error',
                                            title: '¡Codigo Invalido!',
                                            showConfirmButton: false,
                                            timer: 1000
                                        });
                                    }
									if(respuesta.codigo == 502){
                                        Swal.fire({
                                            icon: 'error',
                                            title: '¡Ya tiene una Marcacion Vigente!',
                                            showConfirmButton: false,
                                            timer: 1000
                                        });
                                    } else {
                                        Alerta(respuesta.codigo);
                                        tablaAdmin.ajax.reload(null, false);
                                    } 
                                },
                                error: function(XMLHttpRequest, textStatus, errorThrown){
                                    AlertaPersonalizada('error', XMLHttpRequest.responseText);
                                }
                            });
                        } 
                        else {
                            Swal.fire({
                                icon: 'error',
                                title: '¡Debes llenar el campo!',
                                showConfirmButton: false,
                                timer: 1000
                            });
                        }
                    }
                });
            
        

});

$(document).on("click","#marcaSalida", function(event){
	event.preventDefault()

        Swal.fire({
            title: "Alerta!",
            text: "Ingrese Codigo de Usuario",
            showCancelButton: true,
            input: 'password',
            inputAttributes: {
                type:"newpassword",
                placeholder: "Codigo",
                readonly : true,
                onfocus:"this.removeAttribute('readonly');"
                },
        }).then((result) => {
            if(result.isDismissed == false){
                if (result.value!= "") {
                    var clave = result.value;
                    $.ajax({
                        type: "POST",
                        url: url+"/MarcaSalida",
                        data: {clave:clave},
                        dataType: 'json',
                        success: function (respuesta){
                            if(respuesta.codigo == 500){
                                Swal.fire({
                                    icon: 'error',
                                    title: '¡Codigo Invalido!',
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                            }
							if(respuesta.codigo == 502){
                                Swal.fire({
                                    icon: 'error',
                                    title: '¡No tiene una Entrada Registrada!',
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                            } else {
                                Alerta(respuesta.codigo);
                                tablaAdmin.ajax.reload(null, false);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown){
                            AlertaPersonalizada('error', XMLHttpRequest.responseText);
                        }
                    });
                } 
                else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Debes llenar el campo!',
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            }
        });
           
});

function hora() {
    setInterval(function(){
        var dt = new Date();
        var tiempo = (dt.getHours() > 12) ? "PM" : "AM"; 
        var hora = (dt.getHours() > 12) ? dt.getHours() - 12 : dt.getHours(); 
        var minuto = (dt.getMinutes() < 10) ? "0"+dt.getMinutes(): dt.getMinutes(); 
        var segundo = (dt.getSeconds() < 10) ? "0"+dt.getSeconds(): dt.getSeconds(); 
        var time = hora + ":" + minuto + ":" + segundo + " " + tiempo;
        $("#divHora").html("<h1>"+time+"</h1>");
    },1000);
}

function reload() {
	location.href = url+'/'+padre;
}

/** Control general de MODALES - Final*/
