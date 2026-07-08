$(document).ready(function () {
    const FrmReporte = $("#FrmReporte");
    if (FrmReporte.length === 0) return;

    if ($("#contenedorExportar").length === 0) {
        FrmReporte.after(`
            <div id="contenedorExportar" class="card-footer text-right">
                <div class="btn-group" role="group">
                    <button type="button" id="btnPDF" class="btn btn-danger">
                        <i class="fa fa-file-pdf"></i> PDF
                    </button>
                    <button type="button" id="btnExcel" class="btn btn-success">
                        <i class="fa fa-file-excel"></i> Excel
                    </button>
                    <button type="button" id="btnCSV" class="btn btn-primary">
                        <i class="fa fa-file-csv"></i> CSV
                    </button>
                </div>
            </div>
        `);
    }

    const baseURL = window.location.origin + "/fhbrestaurant/web/html/index.php/";

    $("#btnPDF").click(function () {
        FrmReporte.attr("action", baseURL + "Reportes/GenerarReporte?tipo=pdf");
        FrmReporte.attr("target", "_blank");
        FrmReporte.submit();
    });

    $("#btnExcel").click(function () {
        FrmReporte.attr("action", baseURL + "Reportes/GenerarReporte?tipo=excel");
        FrmReporte.attr("target", "_blank");
        FrmReporte.submit();
    });

    $("#btnCSV").click(function () {
        FrmReporte.attr("action", baseURL + "Reportes/GenerarReporte?tipo=csv");
        FrmReporte.attr("target", "_blank");
        FrmReporte.submit();
    });
});

