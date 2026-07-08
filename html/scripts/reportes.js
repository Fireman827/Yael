var url = window.location.origin;
var token = $("#csrf_token_id").val();
var padre = 'Reportes';
var tablaAdmin;

$(document).ready(function(){

    HacerAutoCompletar('producto_buscar','nombreProducto','/ReporteProductoAutocomplete',function (e, data) {
        $('#producto_buscar').typeahead('val', '');
        $("#idProducto").val(data.idProducto);
        $("#labelProducto").text("Producto: " + data.nombreProducto);
    });

    HacerAutoCompletar('insumo_buscar','nombreInsumo','/ReporteInsumoAutocomplete',function (e, data) {
        $('#insumo_buscar').typeahead('val', '');
        $("#idInsumo").val(data.idInsumo);
        $("#labelInsumo").text("Insumo: " + data.nombreInsumo);
    });
});


// ======================================================================
//      BOTÓN SUBMIT PDF
// ======================================================================
$(document).on('click',"#btnSubmit",function(e){
    e.preventDefault();

    var proceso = $("#proceso").val();
    var FrmReporte = $("#FrmReporte");

    var ruta =
        (proceso == "ReporteVenta")              ? "VerReporteVenta" :
        (proceso == "ReporteUtilidad")           ? "VerReporteUtilidad" :
        (proceso == "ReporteVentaMesero")        ? "VerReporteVentaMesero" :
        (proceso == "ReporteUtilidadProducto")   ? "VerReporteUtilidadProducto" :
        (proceso == "ReporteKardex")             ? "VerReporteKardex" :
        (proceso == "ReporteInventario")         ? "VerReporteInventario" :
        (proceso == "ReporteGeneralUtilidad")    ? "VerReporteGeneralUtilidad" :
        (proceso == "ReporteReposicion")         ? "VerReporteReposicion" :
        (proceso == "ReporteVencimiento")        ? "VerReporteVencimiento" :
        (proceso == "CintaAuditoria")            ? "VerReporteCintaAuditoria" :
        (proceso == "ReporteDetallePedido")      ? "VerReporteDetallePedido" :
        (proceso == "ReporteAdvalorem")          ? "VerReporteAdvalorem" :
        (proceso == "ReporteMarca")              ? "VerReporteMarca" :
        (proceso == "ReporteCompra")             ? "VerReporteCompra" :
        (proceso == "ReporteConteo")             ? "VerReporteConteo" :
        (proceso == "ReporteVentaItem")          ? "VerReporteVentaItem" :
                                                   "";

    FrmReporte.attr("method","GET");
    FrmReporte.attr("target","_blank");
    FrmReporte.attr("action", url + "/" + ruta);

    FrmReporte.submit();
});


// ======================================================================
//      BOTÓN EXCEL ADMIN
// ======================================================================
$(document).on("click", "#btnExcelAdmin", function(e) {
    e.preventDefault();

    var proceso = $("#proceso").val();
    var FrmReporte = $("#FrmReporte");

    var rutas = {
        "CintaAuditoria": "ExcelCintaAuditoria",
        "ReporteVenta": "ExcelVenta",
        "ReporteUtilidad": "ExcelUtilidad",
        "ReporteUtilidadProducto": "ExcelUtilidadProducto",
        "ReporteDetallePedido": "ExcelDetallePedido",
        "ReporteReposicion": "ExcelReposicion",
        "ReporteInventario": "ExcelInventario",
        "ReporteKardex": "ExcelKardex",
        "ReporteVencimiento": "ExcelVencimiento",
        "ReporteMarca": "ExcelMarca",
        "ReporteCompra": "ExcelCompra",
        "ReporteConteo": "ExcelConteo",
        "ReporteVentaItem": "ExcelVentaItem",
        "ReporteMarcaContador": "ExcelReporteMarcaContador",
		"ReporteVentaMesero": "ExcelVentaMesero",
        "ReporteGeneralUtilidad": "ExcelGeneralUtilidad"
    };

    var ruta = rutas[proceso];

    if (!ruta) {
        alert("Ruta Excel Admin no configurada para: " + proceso);
        return;
    }

    FrmReporte.attr("method", "GET");
    FrmReporte.attr("target", "_blank");
    FrmReporte.attr("action", url + "/Reportes/" + ruta);

    FrmReporte.submit();
});


// ======================================================================
//      BOTÓN EXCEL CONTADOR
// ======================================================================
$(document).on("click", "#btnExcelContador", function(e) {
    e.preventDefault();

    var proceso = $("#proceso").val();
    var FrmReporte = $("#FrmReporte");

    var rutas = {
        "CintaAuditoria": "ExcelCintaAuditoria",
        "ReporteVenta": "ExcelVenta",
        "ReporteUtilidad": "ExcelUtilidad",
        "ReporteUtilidadProducto": "ExcelUtilidadProducto",
        "ReporteDetallePedido": "ExcelDetallePedidoContador",
        "ReporteReposicion": "ExcelReposicion",
        "ReporteInventario": "ExcelInventario",
        "ReporteKardex": "ExcelKardex",
        "ReporteVencimiento": "ExcelVencimiento",
        "ReporteMarca": "ExcelMarca",
        "ReporteCompra": "ExcelCompra",
        "ReporteConteo": "ExcelConteo",
        "ReporteVentaItem": "ExcelVentaItem",
		"ReporteVentaMesero": "ExcelVentaMeseroContador",
        "ReporteMarcaContador": "ExcelReporteMarcaContador",

        // versión contador independiente
		"ReporteDetallePedido": "ExcelDetallePedidoContador",
        "ReporteGeneralUtilidad": "ExcelGeneralUtilidad"
    };

    var ruta = rutas[proceso];

    if (!ruta) {
        alert("Ruta Excel Contador no configurada para: " + proceso);
        return;
    }

    FrmReporte.attr("method", "GET");
    FrmReporte.attr("target", "_blank");
    FrmReporte.attr("action", url + "/Reportes/" + ruta);

    // Indicar tipo excel al controlador
    FrmReporte.append('<input type="hidden" name="tipo" value="excel">');

    FrmReporte.submit();
});


function reload() {
    location.href = url+'/'+padre;
}
