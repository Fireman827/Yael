var url   = window.location.origin;
var token = $('#csrf_token_id').val();
var padre = 'Pagos';
var tablaAdmin;

$(document).ready(function () {

    /* ===============================
       DATATABLE (LISTADO)
       FIX: ruta correcta Pagos/PagosMostrar
    ================================ */
    if ($('#tablaAdmin').length) {
        tablaAdmin = RenderizarTabla(url, '/Pagos/PagosMostrar', token);
    }

    /* ===============================
       FORMULARIO (AGREGAR / EDITAR)
    ================================ */
    if ($('#FrmPagos').length) {

        var reglas = {
            nombrePago : { required: true },
            montoPago  : { required: true },
            fechaPago  : { required: true }
        };

        validarDatos('FrmPagos', reglas);

        $('#FrmPagos').on('submit', function (e) {
            e.preventDefault();
            AgregarEditar();
        });
    }

    /* ===============================
       CALENDARIO DE PAGOS
    ================================ */
    if (typeof pagosCalendario !== 'undefined' && $('#calendarioPagos').length) {

        var eventos = typeof pagosCalendario === 'string'
            ? JSON.parse(pagosCalendario)
            : pagosCalendario;

        if (eventos.length > 0) {
            var calendar = new FullCalendar.Calendar(
                document.getElementById('calendarioPagos'),
                {
                    locale      : 'es',
                    initialView : 'dayGridMonth',
                    height      : 'auto',
                    headerToolbar: {
                        left  : 'prev,next today',
                        center: 'title',
                        right : ''
                    },
                    events: eventos,
                    eventClick: function (info) {
                        info.jsEvent.preventDefault();
                        if (info.event.url) {
                            window.location.href = info.event.url;
                        }
                    }
                }
            );
            calendar.render();
        }
    }

});

/* ===============================
   AGREGAR / EDITAR PAGO
================================ */
function AgregarEditar() {

    var proceso = $('#proceso').val();
    var ruta    = (proceso === 'Agregar') ? '/Pagos/PagosAgregar' : '/Pagos/PagosEditar';

    $.ajax({
        url      : url + ruta,
        type     : 'POST',
        data     : $('#FrmPagos').serialize(),
        dataType : 'json',
        beforeSend: function () {
            $('button[type=submit]').prop('disabled', true);
        },
        success: function (resp) {
            $('button[type=submit]').prop('disabled', false);

            if (resp.codigo === 200) {
                Swal.fire({
                    icon             : 'success',
                    title            : 'Guardado',
                    text             : 'El pago fue registrado correctamente.',
                    timer            : 1800,
                    showConfirmButton : false
                }).then(function () {
                    window.location.href = url + '/Pagos';
                });
            } else if (resp.codigo === 400) {
                Swal.fire({ icon: 'warning', title: 'Datos incompletos', text: 'Completa todos los campos requeridos.' });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar el pago. Intenta de nuevo.' });
            }
        },
        error: function () {
            $('button[type=submit]').prop('disabled', false);
            Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
        }
    });
}

/* ===============================
   BORRAR PAGO (LÓGICO)
   Marca estadoPago = 'Borrado'
   No elimina el registro de BD
================================ */
$(document).on('click', '.btn-borrar-pago', function () {

    var idPago  = $(this).data('id');
    var nombre  = $(this).data('nombre');

    Swal.fire({
        icon              : 'warning',
        title             : '¿Borrar pago?',
        html              : 'Se marcará <strong>' + nombre + '</strong> como borrado.<br>No se eliminará de la base de datos.',
        showCancelButton  : true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor : '#6c757d',
        confirmButtonText : 'Sí, borrar',
        cancelButtonText  : 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.ajax({
            url      : url + '/Pagos/PagosBorrar',
            type     : 'POST',
            dataType : 'json',
            data     : { idPago: idPago },
            success  : function (resp) {
                if (resp.codigo === 200) {
                    Swal.fire({
                        icon             : 'success',
                        title            : 'Borrado',
                        text             : 'El pago fue marcado como borrado.',
                        timer            : 1500,
                        showConfirmButton : false
                    }).then(function () {
                        tablaAdmin.ajax.reload(null, false);
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo borrar el pago.' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
            }
        });
    });
});
