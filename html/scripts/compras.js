var url = window.location.origin;
var token = $("#csrf_token").val();
let itemsCompra = [];

$(document).ready(function () {

    $('.dropify').dropify();

    $('#boxFiscal').hide();
    $('#boxArchivo').hide();

    /* ================== TIPO DOCUMENTO ================== */
    $('#tipoDocumento').on('change', function () {
        let tipo = $(this).val();
        if (tipo === 'FACTURA' || tipo === 'CREDITO_FISCAL') {
            $('#boxFiscal').slideDown();
            $('#boxArchivo').slideDown();
        } else {
            $('#boxFiscal').slideUp();
            $('#boxArchivo').slideUp();
        }
    });

    /* ================== AUTOCOMPLETE INSUMOS ================== */
    HacerAutoCompletar(
        'producto_buscar',
        'nombreInsumo',
        '/MovimientoInsumoAutocomplete',
        function (e, data) {

            $('#producto_buscar').typeahead('val', '');

            agregarInsumo(
                data.idInsumo,
                data.nombreInsumo
            );
        }
    );
	

$(document).on("click", "#btnGuardarCompra", function () {

    let tipo = $("#tipoDocumento").val();

    if (tipo === 'FACTURA' || tipo === 'CREDITO_FISCAL') {

        let nit = $("input[name='nitProveedor']").val();
        let nrc = $("input[name='nrcProveedor']").val();
        let archivo = $("#archivoFactura").val();

        if (!nit || !nrc) {
            AlertaPersonalizada("Error", "NIT y NRC son obligatorios");
            return;
        }

        if (!archivo) {
            AlertaPersonalizada("Error", "Debe adjuntar el documento fiscal");
            return;
        }
    }

    if (!crearItemsCompra()) return;

    let form = new FormData($("#FrmCompra")[0]);
    form.append("items", JSON.stringify(itemsCompra));

    $.ajax({
        url: url + "/Compras/CompraGuardar",
        type: "POST",
        data: form,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (resp) {
            Alerta(resp.codigo);
            if (resp.codigo === 200) {
                setTimeout(() => location.href = url + "/Compras", 1200);
            }
        },
        error: function () {
            AlertaPersonalizada("Error", "Error al guardar compra");
        }
    });
});


/* =========================================================
   AGREGAR INSUMO
========================================================= */
function agregarInsumo(idInsumo, nombre) {

    $.post(url + "/MovimientoInsumoConsulta", {
        id_producto: idInsumo,
        proceso: 'Compra'
    }, function (data) {

        let tr = `
        <tr>
            <td class="idInsumo d-none">${idInsumo}</td>
            <td>${nombre}</td>
            <td>${data.select}</td>
            <td>
                <input type="text" class="form-control precio_compra decimal" step="any" value="${data.costop}">
            </td>
            <td>
                <input type="date" class="form-control vence">
            </td>
            <td>
                <input type="text" class="form-control cant" step="any">
            </td>
            <td>
                <button class="btn btn-danger btn-sm eliminar">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;

        $("#inventable tbody").prepend(tr);
        FormatoDatos();
    }, 'json');
}

/* =========================================================
   CREAR ITEMS JSON
========================================================= */
function crearItemsCompra() {

    itemsCompra = [];
    let total = 0;

    $("#inventable tbody tr").each(function () {

        let idInsumo = $(this).find(".idInsumo").text();
        let idPresentacion = $(this).find(".presentacion").val();
        let unidad = $("option:selected", $(this).find(".presentacion")).attr("unidad") || 1;
        let cantidad = parseFloat($(this).find(".cant").val()) || 0;
        let costo = $(this).find(".precio_compra").val().replace(",", ".");
		costo = parseFloat(costo) || 0;
        let fechaV = $(this).find(".vence").val();

        if (cantidad <= 0 || costo <= 0) return;

        let subtotal = cantidad * costo;
        total += subtotal;

        itemsCompra.push({
            idInsumo: idInsumo,
            cantidad: cantidad,
            costo: costo,
            subtotal: subtotal,
            idPresentacion: idPresentacion,
            unidad: unidad,
            fechaVencimiento: fechaV
        });
    });

    if (itemsCompra.length === 0) {
        AlertaPersonalizada("Error", "Debe agregar al menos un insumo");
        return false;
    }

    $("#subtotal").val(total.toFixed(2));
    $("#total").val(total.toFixed(2));
    $("#datos").val(JSON.stringify(itemsCompra));
    $("#items").val(JSON.stringify(itemsCompra));

    return true;
}

/* =========================================================
   EVENTOS
========================================================= */
$(document).on("keyup", ".cant, .precio_compra", function (e) {
    totales();
    if (e.key === "Enter") {
        $("#producto_buscar").focus();
    }
});

$(document).on("click", ".eliminar", function () {
    $(this).closest("tr").remove();
    totales();
});
$(document).on("blur", ".precio_compra", function () {
    let v = $(this).val();
    if (v) {
        v = v.replace(",", ".");
        $(this).val(v);
    }
});


/* =========================================================
   TOTALES
========================================================= */
function totales() {

    let total = 0;
    let totalCantidad = 0;

    $("#inventable tbody tr").each(function () {
        let cant = parseFloat($(this).find(".cant").val()) || 0;
        let precio = parseFloat(
            $(this).find(".precio_compra").val().replace(",", ".")
        ) || 0;

        totalCantidad += cant;
        total += cant * precio;
    });

    total = Math.round(total * 100) / 100;

    $("#totcant").html(totalCantidad);
    $("#total_dinero").html(total.toFixed(2));
    $("#total_dineroh").val(total.toFixed(2));
}

/* =========================================================
   ANULAR COMPRA
========================================================= */
$(document).on("click", ".btnAnularCompra", function () {

    let idCompra = $(this).data("id");

    Swal.fire({
        title: '¿Anular compra?',
        text: 'Esta acción revertirá el stock y el kardex',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: url + "/Compras/CompraAnular/" + idCompra,
                type: "POST",
                dataType: "json",
                success: function (resp) {

                    if (resp.codigo === 200) {
                        Swal.fire(
                            'Anulada',
                            resp.mensaje,
                            'success'
                        );

                        // recargar datatable
                        $('.datatable').DataTable().ajax.reload(null, false);

                    } else {
                        Swal.fire(
                            'Error',
                            resp.mensaje || 'No se pudo anular',
                            'error'
                        );
                    }
                },
                error: function () {
                    Swal.fire(
                        'Error',
                        'Error de comunicación con el servidor',
                        'error'
                    );
                }
            });
			}
		});
	});

});

/* =========================================================
   NUEVO PROVEEDOR
========================================================= */
$(document).on("click", "#btnNuevoProveedor", function () {
    window.location.href = url + '/ProveedorAgregar?back=CompraAgregar';
});
	