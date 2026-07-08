$(document).ready(function () {

    /* =====================================================
       1️⃣  ACTUALIZAR CONTADOR CAMPANA (badge)
    ===================================================== */
    function actualizarContadorNotificaciones() {
        $.ajax({
            url      : base_url + 'Notificaciones/ajaxContador',
            type     : 'GET',
            dataType : 'json',
            success  : function (resp) {
                var badge = $('#campana-notificaciones');
                if (resp.total > 0) {
                    if (badge.length === 0) {
                        $('.fa-bell').after(
                            '<span id="campana-notificaciones" ' +
                            'class="badge badge-' + COLOR_COMPONENTES + ' navbar-badge">' +
                            resp.total + '</span>'
                        );
                    } else {
                        badge.text(resp.total);
                    }
                } else {
                    badge.remove();
                }
            }
        });
    }

    /* =====================================================
       2️⃣  MARCAR UNA NOTIFICACIÓN COMO LEÍDA
    ===================================================== */
    $(document).on('click', '.marcar-leida', function (e) {
        e.preventDefault();

        var id         = $(this).data('id');
        var contenedor = $('#notif-' + id);

        $.ajax({
            url      : base_url + 'Notificaciones/marcarLeidaAjax',
            type     : 'POST',
            dataType : 'json',
            data     : { idNotificacion: id },
            success  : function (resp) {
                if (resp.ok) {
                    contenedor.fadeOut(300, function () {
                        $(this).remove();
                        if ($('#lista-notificaciones .list-group-item').length === 0) {
                            $('#lista-notificaciones').html(
                                '<li class="list-group-item text-center text-muted">' +
                                '<i class="far fa-bell-slash mr-1"></i> No hay notificaciones</li>'
                            );
                        }
                    });
                    actualizarContadorNotificaciones();
                }
            }
        });
    });

    /* =====================================================
       3️⃣  MARCAR TODAS COMO LEÍDAS
    ===================================================== */
    $(document).on('click', '#btn-marcar-todas', function (e) {
        e.preventDefault();

        $.ajax({
            url      : base_url + 'Notificaciones/marcarTodasAjax',
            type     : 'POST',
            dataType : 'json',
            success  : function (resp) {
                if (resp.ok) {
                    $('#lista-notificaciones').html(
                        '<li class="list-group-item text-center text-muted">' +
                        '<i class="far fa-bell-slash mr-1"></i> No hay notificaciones</li>'
                    );
                    $('#campana-notificaciones').remove();
                    $('#btn-marcar-todas').hide();
                }
            }
        });
    });

    /* =====================================================
       4️⃣  CALENDARIO FULLCALENDAR
       El controlador arma el array con id, title, start,
       color y url — se pasa directo a FullCalendar.
       La variable pagosCalendario viene del <script> tag
       en la vista (json_encode desde el controlador).
    ===================================================== */
    var calendarEl = document.getElementById('calendarioPagos');

    if (calendarEl && typeof pagosCalendario !== 'undefined' && pagosCalendario.length > 0) {

        new FullCalendar.Calendar(calendarEl, {
            initialView : 'dayGridMonth',
            locale      : 'es',
            height      : 'auto',
            events      : pagosCalendario,
            eventClick  : function (info) {
                info.jsEvent.preventDefault();
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            }
        }).render();
    }

    /* =====================================================
       5️⃣  INICIALIZACIÓN
    ===================================================== */
    actualizarContadorNotificaciones();
    setInterval(actualizarContadorNotificaciones, 30000);

});
