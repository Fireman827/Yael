<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reportes extends CI_Controller {
	/****jhgjhg**/
	private $tabla = "";
	private $controlador = "Reportes";
	private $fpdf;
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
		$this->load->add_package_path(APPPATH . 'third_party/fpdf');
		$this->load->library('pdf');
			}
	
		// -------------------------------
		// Helpers defensivos para Reportes
		// -------------------------------
			private function _is_iterable_var($v) {
			return (is_array($v) || $v instanceof \Traversable);
			}

					/**
					* Normaliza el resultado de las funciones de datos.
					* Si la función devuelve 0 o false, devuelve [] para evitar foreach inválidos.
					*/
			private function _normalize_result($res) {
				if ($res === 0 || $res === false || $res === null) return [];
				return $res;
					}

/**
 * Limpia buffers antes de enviar PDF/Excel.
 * Úsalo justo antes de $this->fpdf->Output(...) o antes de escribir headers.
 */
private function _safe_output_clean() {
    // Si hay buffers abiertos, limpiarlos para evitar "Some data has already been output"
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    // También una limpieza final
    @ob_clean();
}



	public function index(){

	}
//Reporte de Cinta de Auditoría
function CintaAuditoria(){
	if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
		GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
	} else {
		if($this->input->method(TRUE) == "GET"){
			$titulo = "Cinta de Auditoria";
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fa fa-file-pdf",
				"controlador" => "Reportes",
				"proceso" => "CintaAuditoria",
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/reportes.js"
				),
			);
			GblPlantilla("reportes/cintaAuditoria",$datosVista,$extras,$titulo);
		}
	}
}

function VerReporteCintaAuditoria() {
	if($this->input->method(TRUE) == "GET"){
		$fechaInicio = $this->input->get("fechaInicio");
		$fechaFinal = $this->input->get("fechaFinal");
		$sucursal = $this->session->idSucursal;

		$join = array(
			array(
				"tabla" => "usuario",
				"condicion" =>"factura.idUsuario = usuario.idUsuario",
				"tipo" => "inner",
				"campos" => "usuario.nombreUsuario"
			),
			array(
				"tabla" => "cliente",
				"condicion" =>"factura.idCliente = cliente.idCliente",
				"tipo" => "left",
				"campos" => "cliente.nombreCliente"
			),
		);

		$condicionFacDet = array(
			"fechaFactura >=" => $fechaInicio,
			"fechaFactura <=" => $fechaFinal,
			"tipoFactura" => "Producto",
			"idSucursalFactura" => $sucursal,
			"estadoFactura" => "Cobrado",
			"tipoDocumentoFactura" => "TIK"
		);

		$factura = TraerDatosJoin("factura",$condicionFacDet,"",$join);

		$periodoVigente = "";
		list($a,$m,$d) = explode("-", $fechaInicio);
		list($a1,$m1,$d1) = explode("-", $fechaFinal);

		if($a == $a1){
			if($m==$m1){
				$periodoVigente = ($d==$d1)
					? "$d1 DE ".meses($m)." DE $a"
					: "DEL $d AL $d1 DE ".meses($m)." DE $a";
			} else {
				$periodoVigente = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
			}
		} else {
			$periodoVigente = "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
		}
		
				// =============================================
				//     ARMAR TABLA PARA EXPORTAR A EXCEL
				// =============================================

		$tabla = [];  

				// Encabezados (EDITABLES)
		$tabla[] = [
				"DOCUMENTO","FECHA","CAJERO","CLIENTE",
				"PRODUCTO","CANTIDAD","PRECIO","SUBTOTAL",
				"TOTAL FACTURA","PROPINA","DESCUENTO","TOTAL FINAL"
					];

				// normalizar la fuente de datos (ejemplo $datosVista)
				$fuente = $this->_normalize_result($datosVista ?? null);

			if (!$this->_is_iterable_var($fuente) || count((array)$fuente) === 0) {
			log_message('error', "VerReporteX: fuente de datos no iterable o vacía (X = nombreReporte)");
				// Para que el PDF no rompa, definir fuente como array vacío
			$fuente = [];
				}

	foreach ($factura as $fila) {

		$idPedido = $fila->idReferenciaFactura;

		$joinProd = [
			[
				"tabla" => "producto",
				"condicion" => "producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
				"tipo"      => "inner",
				"campos"    => "producto.nombreProducto"
			],
		];

		$detalle = TraerDatosJoin("pedidoDetalle", ["idPedido" => $idPedido], "", $joinProd);

		if ($detalle !== 0) {
			foreach ($detalle as $d) {

				$sub = floatval($d->cantidadPedidoDetalle) * floatval($d->precioPedidoDetalle);

				$totalFinal = floatval($fila->totalFactura) 
							- floatval($fila->descuentoDolarFactura) 
							+ floatval($fila->propinaFactura);

				$tabla[] = [
					$fila->tipoDocumentoFactura . " " . $fila->numeroDocumentoFactura,
					$fila->fechaFactura . " " . $fila->horaFactura,
					$fila->nombreUsuario,
					$fila->nombreCliente,
					$d->nombreProducto,
					floatval($d->cantidadPedidoDetalle),
					floatval($d->precioPedidoDetalle),
					$sub,
					floatval($fila->totalFactura),
					floatval($fila->propinaFactura),
					floatval($fila->descuentoDolarFactura),
					$totalFinal
				];
			}
		}
	}
}

// Guardar tabla para Excel (aunque esté vacía guardamos encabezados)
if (!isset($tabla) || !is_array($tabla)) $tabla = [];
$this->session->set_userdata("tabla_reporte", $tabla);

		$this->fpdf = new Pdf();
		$this->fpdf->SetTopMargin(2);
		$this->fpdf->SetLeftMargin(10);
		$this->fpdf->AliasNbPages();
		$this->fpdf->SetAutoPageBreak(true,1);
		$this->fpdf->AddFont("courier new","","courier.php");

		foreach ($factura as $fila)
		{
			$idPedido = $fila->idReferenciaFactura;

			$joinDetalle = array(
				array(
					"tabla" => "producto",
					"condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
					"tipo" => "inner",
					"campos" => "producto.nombreProducto"
				),
			);

			$facturaDetalle = TraerDatosJoin("pedidoDetalle",array("idPedido" => $idPedido),"",$joinDetalle);

			$cuen_ss = count($facturaDetalle);
			$ff = 5 * $cuen_ss;
			$nn = $ff+110;

			$this->fpdf->AddPage('P', array(80, $nn));

			$set_x = 2;
			$set_y = 32;

			$numero_doc = $fila->numeroDocumentoFactura;
			$hora = $fila->horaFactura;
			$fecha = $fila->fechaFactura;

			$total = $fila->totalFactura;
			$propina  = $fila->propinaFactura;
			$descuento = $fila->descuentoDolarFactura;
			$total_pago = $total - $descuento + $propina;

			$this->fpdf->SetXY($set_x, $set_y);
			$this->fpdf->SetFont('courier new','',13);
			$this->fpdf->Cell(70,5,utf8_decode("TIQUETE # ".$numero_doc),0,1,'C');
			$this->fpdf->Line($set_x,$set_y+5,78,$set_y+5);

			$this->fpdf->SetFont('courier new','',8.5);
			$this->fpdf->SetXY($set_x, $set_y+5);
			$this->fpdf->Cell(70,5,utf8_decode("FECHA:".fecha_d_m_a($fecha)."  ".hora($hora)),0,1,'L');

			$this->fpdf->SetXY($set_x, $set_y+9);
			$this->fpdf->Cell(70,5,utf8_decode("CAJERO: ".$fila->nombreUsuario),0,1,'L');
			$this->fpdf->Line($set_x,$set_y+15,78,$set_y+15);

			$set_y = 45;
			$this->fpdf->SetFont('courier new','',8);

			$this->fpdf->SetXY($set_x, $set_y+5);
			$this->fpdf->Cell(46,5,utf8_decode("DESCRIPCION."),0,1,'L');

			$this->fpdf->SetXY($set_x+46, $set_y+5);
			$this->fpdf->Cell(10,5,"CANT.",0,1,'L');

			$this->fpdf->SetXY($set_x+56, $set_y+5);
			$this->fpdf->Cell(10,5,"P.V.",0,1,'L');

			$this->fpdf->SetXY($set_x+66, $set_y+5);
			$this->fpdf->Cell(10,5,"SUBT.",0,1,'L');

			$set_y = 55;
			$this->fpdf->Line($set_x,$set_y,78,$set_y);

			$dd = 0;
			foreach ($facturaDetalle as $filaDetalle)
			{
				$cantidad = $filaDetalle->cantidadPedidoDetalle;
				$precio = $filaDetalle->precioPedidoDetalle;
				$sub = number_format($precio * $cantidad,2);
				$nombre = $filaDetalle->nombreProducto;

				$this->fpdf->SetFont('courier new','',7);
				$this->fpdf->SetXY($set_x, $set_y+ $dd);
				$this->fpdf->Cell(46,5,utf8_decode(substr($nombre,0,30)),0,1,'L');

				$this->fpdf->SetXY($set_x+46, $set_y+ $dd);
				$this->fpdf->Cell(10,5,$cantidad,0,1,'R');

				$this->fpdf->SetXY($set_x+56, $set_y+ $dd);
				$this->fpdf->Cell(10,5,$precio,0,1,'R');

				$this->fpdf->SetXY($set_x+66, $set_y+ $dd);
				$this->fpdf->Cell(10,5,$sub,0,1,'R');

				$dd += 5;
			}

			$this->fpdf->Line($set_x,$set_y+$ff,78,$set_y+$ff);

			$set_y = 56+$ff;
			$this->fpdf->SetFont('courier new','',10);

			$this->fpdf->SetXY($set_x, $set_y);
			$this->fpdf->Cell(40,5,"TOTAL GRAVADO",0,1,'L');

			$this->fpdf->SetXY($set_x+40, $set_y);
			$this->fpdf->Cell(15,5,"$".number_format($total,2),0,1,'L');

			$this->fpdf->SetXY($set_x, $set_y+5);
			$this->fpdf->Cell(40,5,"TOTAL EXENTO",0,1,'L');

			$this->fpdf->SetXY($set_x+40, $set_y+5);
			$this->fpdf->Cell(15,5,"$0.00",0,1,'L');

			if(GblTraerConfiguracion("cobroPropina") == "Si"){
				$this->fpdf->SetXY($set_x, $set_y+10);
				$this->fpdf->Cell(40,5,"PROPINA",0,1,'L');

				$this->fpdf->SetXY($set_x+40, $set_y+10);
				$this->fpdf->Cell(15,5,"$".number_format($propina,2),0,1,'L');
			}

			$this->fpdf->SetXY($set_x, $set_y+15);
			$this->fpdf->Cell(40,5,"DESCUENTO",0,1,'L');

			$this->fpdf->SetXY($set_x+40, $set_y+15);
			$this->fpdf->Cell(15,5,"$".number_format($descuento,2),0,1,'L');

			$this->fpdf->SetXY($set_x, $set_y+20);
			$this->fpdf->Cell(40,5,"TOTAL",0,1,'L');

			$this->fpdf->SetXY($set_x+40, $set_y+20);
			$this->fpdf->Cell(15,5,"$".number_format($total_pago,2),0,1,'L');

			$set_y = 77+$ff;
			$this->fpdf->SetFont('courier new','',8);
			$this->fpdf->SetXY($set_x, $set_y);
			$this->fpdf->Cell(70,5,"E = EXENTO  G = GRAVADO",0,1,'C');
		}

		$filename = "cinta_auditoria_".str_replace(' ','_',$periodoVigente).".pdf";
		ob_clean();
		// antes de enviar el PDF, limpiamos buffers que puedan tener warnings o salida accidental
$this->_safe_output_clean();

try {
    $this->fpdf->Output($filename, "I");
} catch (Exception $e) {
    log_message('error', "FPDF Output error: " . $e->getMessage());
    show_error("Error generando PDF. Revisa logs.");
}
exit;

	}
	
	public function ExcelVenta()
{
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // =============================================
    // ENCABEZADOS CORPORATIVOS (A1:F6)
    // =============================================
    for ($i = 0; $i < 6; $i++) {
        if (!isset($tabla[$i][0])) continue;

        $fila = $i + 1;
        $sheet->setCellValue("A$fila", $tabla[$i][0]);
        $sheet->mergeCells("A$fila:I$fila");
        $sheet->getStyle("A$fila")->getAlignment()->setHorizontal('center');
        if ($i <= 1) {
            $sheet->getStyle("A$fila")->getFont()->setBold(true)->setSize(14);
        }
    }

    // =============================================
    // ENCABEZADO DE TABLA (FILA 8)
    // =============================================
    $filaEnc = 8;
    $columna = "A";

    foreach ($tabla[0] as $titulo) {
        $sheet->setCellValue($columna . $filaEnc, $titulo);
        $sheet->getStyle($columna . $filaEnc)->getFont()->setBold(true);
        $sheet->getColumnDimension($columna)->setAutoSize(true);
        $columna++;
    }

    // =============================================
    // DETALLE (desde fila 9)
    // =============================================
    $fila = 9;

    for ($i = 1; $i < count($tabla); $i++) {
        $columna = "A";
        foreach ($tabla[$i] as $valor) {
            $sheet->setCellValue($columna . $fila, $valor);
            $columna++;
        }
        $fila++;
    }

    // =============================================
    // DESCARGA
    // =============================================
    $filename = "Reporte_Venta_" . date("Ymd_His") . ".xlsx";
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelVentaContador()
{
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Columna omitida para contabilidad: "TIPO" (tipo de cuenta) es un dato operativo de salón,
    // no forma parte del Libro de Ventas / IVA que necesita el contador.
    $columnasOmitidas = [3];

    // =============================================
    // ENCABEZADOS CORPORATIVOS (A1:F6)
    // =============================================
    for ($i = 0; $i < 6; $i++) {
        if (!isset($tabla[$i][0])) continue;

        $fila = $i + 1;
        $sheet->setCellValue("A$fila", $tabla[$i][0]);
        $sheet->mergeCells("A$fila:I$fila");
        $sheet->getStyle("A$fila")->getAlignment()->setHorizontal('center');
        if ($i <= 1) {
            $sheet->getStyle("A$fila")->getFont()->setBold(true)->setSize(14);
        }
    }

    // =============================================
    // ENCABEZADO DE TABLA (se omiten columnas no esenciales para contabilidad)
    // =============================================
    $filaEnc = 8;
    $columna = "A";

    foreach ($tabla[0] as $idx => $titulo) {
        if (in_array($idx, $columnasOmitidas)) continue;
        $sheet->setCellValue($columna . $filaEnc, $titulo);
        $sheet->getStyle($columna . $filaEnc)->getFont()->setBold(true);
        $sheet->getColumnDimension($columna)->setAutoSize(true);
        $columna++;
    }

    // =============================================
    // DETALLE
    // =============================================
    $fila = 9;

    for ($i = 1; $i < count($tabla); $i++) {
        $columna = "A";
        foreach ($tabla[$i] as $idx => $valor) {
            if (in_array($idx, $columnasOmitidas)) continue;
            $sheet->setCellValue($columna . $fila, $valor);
            $columna++;
        }
        $fila++;
    }

    // =============================================
    // DESCARGA
    // =============================================
    $filename = "Reporte_Venta_Contador_" . date("Ymd_His") . ".xlsx";
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}



// ============================================
//     EXPORTAR CINTA DE AUDITORÍA A EXCEL
// ============================================
function ExcelCintaAuditoria() {

	if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
		echo "No autorizado";
		exit;
	}

	$tabla = $this->session->userdata("tabla_reporte");

	if(!$tabla || count($tabla) == 0){
		echo "No hay datos para exportar.";
		exit;
	}

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	$fila = 1;
	foreach ($tabla as $row) {
		$columna = "A";
		foreach ($row as $value) {
			$sheet->setCellValue($columna.$fila, $value);
			$columna++;
		}
		$fila++;
	}

	$writer = new Xlsx($spreadsheet);
	$filename = "CintaAuditoria_Excel_".date("Ymd_His").".xlsx";

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Cache-Control: max-age=0');

	$writer->save("php://output");
	exit;
}



	function ReporteReposicion(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Reporte de Reposicion";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-file-pdf",
					"controlador" => "Reportes",
					"proceso" => "ReporteReposicion",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/reportes.js"
					),
				);
				GblPlantilla("reportes/reporteReposicion",$datosVista,$extras,$titulo);
			}
		}
	}
	function VerReporteReposicion() {

    if ($this->input->method(TRUE) !== "GET") return;

    $sucursal = $this->session->idSucursal;

    // -------- CONSULTA PRINCIPAL ---------
    $condicion = array(
        "idSucursalInsumo" => $sucursal,
        "estadoInsumo" => "Activo",
        "insumoStock.cantidadInsumoStock <= " => "insumo.stockMinimoInsumo"
    );

    $join = array(
        array(
            "tabla" => "insumoStock",
            "condicion" => "insumo.idInsumo = insumoStock.idInsumoStock",
            "tipo" => "inner",
            "campos" => "insumoStock.cantidadInsumoStock"
        ),
    );

    $stocks = TraerDatosJoin("insumo", $condicion, '', $join);
    $stocks = $this->_normalize_result($stocks);


    // ======================================================
    //   ARMAR TABLA PARA EXPORTAR A EXCEL - REPOSICIÓN
    // ======================================================

    $tabla = [];

    // Encabezados del Excel coherentes con el PDF
    $tabla[] = [
        "CODIGO",
        "NOMBRE",
        "EXISTENCIA MINIMA",
        "EXISTENCIA ACTUAL"
    ];

    if ($this->_is_iterable_var($stocks)) {
        foreach ($stocks as $fila) {
            $tabla[] = [
                $fila->idInsumo,
                $fila->nombreInsumo,
                $fila->stockMinimoInsumo,
                $fila->cantidadInsumoStock
            ];
        }
    }

    // Guardar tabla
    $this->session->set_userdata("tabla_reporte", $tabla);


    // ======================================================
    //                      PDF
    // ======================================================

    $this->fpdf = new Pdf();
    $this->fpdf->SetTopMargin(10);
    $this->fpdf->SetLeftMargin(12);
    $this->fpdf->AliasNbPages();
    $this->fpdf->SetAutoPageBreak(true,20);
    $this->fpdf->AddPage('P','Letter',0);

    $path = GblTraerConfiguracion("logoEmpresa");
    $this->fpdf->Image($path, 5, 5, 35, 30);

    $this->fpdf->SetXY(0,0);
    $this->fpdf->SetFont('Arial','I',8);
    $this->fpdf->Cell(210, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');

    $this->fpdf->SetXY(0,15);
    $this->fpdf->SetFont('Helvetica','',14);
    $this->fpdf->Cell(216,4,"REPORTE DE EXISTENCIAS MINIMAS",0,1,'C');

    $this->fpdf->SetXY(0,20);
    $this->fpdf->SetFont('Helvetica','',10);
    $periodoVigente = date("d")." DE ".meses(date("m"))." DE ".date("Y");
    $this->fpdf->Cell(216,4,"AL: ".$periodoVigente,0,1,'C');

    $this->fpdf->Ln(12);

    // Encabezado PDF
    $this->fpdf->SetFont('Helvetica','B',13);
    $array_data = array(
        0 => array("CODIGO",22,"C"),
        1 => array("NOMBRE",110,"C"),
        2 => array("EXISTENCIA MINIMA",30,"C"),
        3 => array("EXISTENCIA ACTUAL",30,"C"),
    );
    $this->fpdf->LineWriteB($array_data, 1, 6);

    // Detalle PDF
    $this->fpdf->SetFont('Helvetica','',11);

    if ($this->_is_iterable_var($stocks)) {
        foreach ($stocks as $fila) {
            $array_data = array(
                1 => array($fila->idInsumo,22,"L"),
                2 => array($fila->nombreInsumo,110,"L"),
                3 => array($fila->stockMinimoInsumo,30,"R"),
                4 => array($fila->cantidadInsumoStock,30,"R"),
            );
            $this->fpdf->LineWriteB($array_data, 1, 6);
        }
    }

    $filename = "Reporte_existencias_minimas_".date("Ymd").".pdf";
    $this->_safe_output_clean();
    $this->fpdf->Output($filename,"I");
    exit;
}

	
	function ReporteVencimiento(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Reporte de productos proximos a vencer";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-file-pdf",
					"controlador" => "Reportes",
					"proceso" => "ReporteVencimiento",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/reportes.js"
					),
				);
				GblPlantilla("reportes/reporteVencimiento",$datosVista,$extras,$titulo);
			}
		}
	}	

	function VerReporteVencimiento() 
{
    if ($this->input->method(TRUE) !== "GET") return;

    // ----------- PARAMETROS -------------
    $dias = intval($this->input->get("dias"));
    if ($dias <= 0) $dias = 1;

    $hoy = date("Y-m-d");
    $fechaVence = date("Y-m-d", strtotime("$hoy + $dias days"));

    $sucursal = $this->session->idSucursal;

    // ----------- CONSULTA A BD -------------
    $condicion = array(
        "idSucursalInsumo" => $sucursal,
        "estadoInsumo" => "Activo",
        "insumoLote.fechaVencimientoInsumoLote <=" => $fechaVence,
        "insumoLote.estadoInsumoLote" => "Activo",
        "insumoLote.cantidadInsumoLote >" => 0
    );

    // ⚠ CORRECCIÓN IMPORTANTE:
    // El join correcto es con idInsumo, no idInsumoLote
    $join = array(
        array(
            "tabla" => "insumoLote",
            "condicion" => "insumo.idInsumo = insumoLote.idInsumo",
            "tipo" => "inner",
            "campos" => "insumoLote.fechaVencimientoInsumoLote, insumoLote.cantidadInsumoLote"
        ),
    );

    $stocks = TraerDatosJoin("insumo", $condicion, 'insumo.nombreInsumo ASC', $join);

    // NORMALIZAR
    $stocks = $this->_normalize_result($stocks);

    // ======================================================
    //           ARMAR TABLA EXCEL
    // ======================================================

    $tabla = [];
    $tabla[] = ["CODIGO", "NOMBRE", "VENCIMIENTO", "CANTIDAD"];

    foreach ($stocks as $fila) 
    {
        $tabla[] = [
            $fila->idInsumo,
            $fila->nombreInsumo,
            fecha_d_m_a($fila->fechaVencimientoInsumoLote),
            $fila->cantidadInsumoLote
        ];
    }

    // Guardar tabla para Excel
    $this->session->set_userdata("tabla_reporte", $tabla);

    // ======================================================
    //                  GENERAR PDF
    // ======================================================

    $this->fpdf = new Pdf();

    $this->fpdf->SetTopMargin(10);
    $this->fpdf->SetLeftMargin(12);
    $this->fpdf->AliasNbPages();
    $this->fpdf->SetAutoPageBreak(true,20);
    $this->fpdf->AddPage('P','Letter',0);

    // LOGO
    $path = GblTraerConfiguracion("logoEmpresa");
    $this->fpdf->Image($path, 5, 5, 35, 30);

    // TÍTULOS
    $this->fpdf->SetXY(0,0);
    $this->fpdf->SetFont('Arial','I',8);
    $this->fpdf->Cell(210, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');

    $this->fpdf->SetXY(0,15);
    $this->fpdf->SetFont('Helvetica','', 14);
    $this->fpdf->Cell(216, 4,"REPORTE DE PRODUCTOS A VENCER", 0, 1, 'C');

    $this->fpdf->SetXY(0,20);
    $this->fpdf->Cell(216, 4,"VENCIMIENTO A ".$dias." DIAS", 0, 1, 'C');

    $this->fpdf->SetXY(0,25);
    $periodoVigente = date("d")." DE ".meses(date("m"))." DE ".date("Y");
    $this->fpdf->Cell(216, 4,"AL: ".$periodoVigente, 0, 1, 'C');

    $this->fpdf->Ln(12);

    // ENCABEZADO TABLA
    $this->fpdf->SetFont('Helvetica', 'B', 13);
    $array_data = array(
        0 => array("CODIGO",22,"C"),
        1 => array("NOMBRE",103,"C"),
        2 => array("VENCIMIENTO",35,"C"),
        3 => array("CANTIDAD",35,"C"),
    );
    $this->fpdf->LineWriteB($array_data, 1, 6);

    // DETALLE
    $this->fpdf->SetFont('Helvetica', '', 11);

    foreach ($stocks as $fila)
    {
        $array_data = array(
            1 => array($fila->idInsumo,22,"L"),
            2 => array($fila->nombreInsumo,103,"L"),
            3 => array(fecha_d_m_a($fila->fechaVencimientoInsumoLote),35,"C"),
            4 => array($fila->cantidadInsumoLote,35,"R"),
        );
        $this->fpdf->LineWriteB($array_data, 1, 6);
    }

    // SALIDA PDF
    $filename = "Reporte_vencimientos_".$dias."_dias_".date("Ymd").".pdf";
    $this->_safe_output_clean();
    $this->fpdf->Output($filename, "I");
    exit;
}


/* ===============================================================
   EXCEL DETALLE PEDIDO — ADMIN
   =============================================================== */
public function ExcelDetallePedido()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // TITULO
    $sheet->setCellValue("A1", "REPORTE DETALLE DE PEDIDO");
    $sheet->mergeCells("A1:I1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    // Encabezados
    $filaExcel = 3;
    $col = "A";
    foreach ($tabla[0] as $titulo) {
        $sheet->setCellValue($col.$filaExcel, $titulo);
        $sheet->getStyle($col.$filaExcel)->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Datos
    $filaExcel = 4;
    for ($i=1; $i<count($tabla); $i++) {
        $col = "A";
        foreach ($tabla[$i] as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $col++;
        }
        $filaExcel++;
    }

    // Descargar
    $filename = "Reporte_Detalle_Pedido_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}
/* ===============================================================
   EXCEL DETALLE PEDIDO — CONTADOR
   =============================================================== */
public function ExcelDetallePedidoContador()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Columna omitida para contabilidad: "TIPO" (tipo de cuenta) es un dato operativo de salón.
    $columnasOmitidas = [0];

    // TITULO
    $sheet->setCellValue("A1", "REPORTE DETALLE DE PEDIDO");
    $sheet->mergeCells("A1:H1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    // Encabezados (se omiten columnas no esenciales para contabilidad)
    $filaExcel = 3;
    $col = "A";
    foreach ($tabla[0] as $idx => $titulo) {
        if (in_array($idx, $columnasOmitidas)) continue;
        $sheet->setCellValue($col.$filaExcel, $titulo);
        $sheet->getStyle($col.$filaExcel)->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Datos
    $filaExcel = 4;
    for ($i=1; $i<count($tabla); $i++) {
        $col = "A";
        foreach ($tabla[$i] as $idx => $valor) {
            if (in_array($idx, $columnasOmitidas)) continue;
            $sheet->setCellValue($col.$filaExcel, $valor);
            $col++;
        }
        $filaExcel++;
    }

    // Descargar
    $filename = "Reporte_Detalle_Pedido_Contador_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}


public function ExcelKardex()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Kardex");

    // Título principal
    $sheet->setCellValue("A1", "REPORTE DE KARDEX");
    $sheet->mergeCells("A1:H1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $row = 3;

    // Escribir toda la tabla exactamente como está en $tabla
    foreach ($tabla as $fila) {

        $col = "A";

        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$row, $valor);

            // Auto ancho
            $sheet->getColumnDimension($col)->setAutoSize(true);

            $col++;
        }

        $row++;
    }

    // Descargar Excel
    $filename = "Kardex_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelInventario()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Inventario");

    // Título principal
    $sheet->setCellValue("A1", "REPORTE DE INVENTARIO");
    $sheet->mergeCells("A1:H1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {
        $col = "A";

        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $filaExcel++;
    }

    // Descargar archivo
    $filename = "Inventario_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelGeneralUtilidad()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("General Utilidad");

    // Título
    $sheet->setCellValue("A1", "REPORTE GENERAL DE UTILIDAD");
    $sheet->mergeCells("A1:H1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {
        $col = "A";
        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $filaExcel++;
    }

    // Descargar archivo
    $filename = "General_Utilidad_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelReposicion()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Reposicion");

    // Título principal
    $sheet->setCellValue("A1", "REPORTE DE REPOSICIÓN");
    $sheet->mergeCells("A1:G1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {
        $col = "A";

        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $filaExcel++;
    }

    // Descargar archivo
    $filename = "Reposicion_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}


public function ExcelVencimiento()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Vencimientos");

    // Título principal
    $sheet->setCellValue("A1", "REPORTE DE PRODUCTOS POR VENCER");
    $sheet->mergeCells("A1:F1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {

        $col = "A";

        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $filaExcel++;
    }

    // Descargar archivo
    $filename = "Vencimientos_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}


public function ExcelMarca()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Marcas");

    // Título principal
    $sheet->setCellValue("A1", "REPORTE POR MARCA");
    $sheet->mergeCells("A1:E1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {

        $col = "A";

        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $filaExcel++;
    }

    // Descargar archivo
    $filename = "Reporte_Marca_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelReporteMarcaContador()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $fechaInicio = $this->input->get("fechaInicio");
    $fechaFinal  = $this->input->get("fechaFinal");
    $idUsuario   = $this->input->get("idUsuario");

    // =============== OBTENER USUARIOS ===============
    if($idUsuario != "T"){
        $usuarios = TraerDatos("usuario",["idUsuario"=>$idUsuario]);
    } else {
        $usuarios = TraerDatos("usuario",[
            'idUsuario >' => "0",
            'activoUsuario' => 1,
            'idSucursalUsuario' => $this->session->idSucursal
        ]);
    }

    // ============= PREPARAR LIBRO =============
    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Marcaciones_Contador");

    // Título
    $sheet->setCellValue("A1","REPORTE RESUMIDO DE MARCACIONES");
    $sheet->mergeCells("A1:F1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    // Encabezados
    $fila = 3;
    $encabezados = ["USUARIO","FECHA","HORAS TRAB.","HORAS DÍA","DEBE","EXTRA"];
    $col = "A";

    foreach ($encabezados as $titulo){
        $sheet->setCellValue($col.$fila, $titulo);
        $sheet->getStyle($col.$fila)->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    $fila = 4;

    // ============= ARMAR REPORTE RESUMIDO =============
    $dias = diferenciaDias($fechaInicio,$fechaFinal);

    foreach($usuarios as $usu){

        for($i=0; $i <= $dias; $i++){

            $diaActual = date("Y-m-d",strtotime($fechaInicio." + ".$i." days"));

            $marcas = TraerDatos("marca",[
                "fechaHoraEntradaMarca >=" => "$diaActual 00:00:01",
                "fechaHoraEntradaMarca <=" => "$diaActual 23:59:59",
                "estadoMarca" => "Inactivo",
                "idUsuarioMarca"=>$usu->idUsuario
            ],"fechaHoraEntradaMarca ASC");

            if(!$marcas) continue;

            // SUMAR HORAS TRABAJADAS DEL DÍA
            $totalDia = "00:00:00";
            foreach($marcas as $m){
                $totalDia = sumarHoras($totalDia,
                    diferenciaHoras($m->fechaHoraSalidaMarca,$m->fechaHoraEntradaMarca)
                );
            }

            $horaDia = GblTraerConfiguracion("horasTrabajoEmpleado");

            // Calcular DEBE / EXTRA
            $debe = "";
            $extra = "";

            if($horaDia > $totalDia){
                $debe = diferenciaHoras($horaDia,$totalDia);
            } else {
                $extra = diferenciaHoras($totalDia,$horaDia);
            }

            // === AGREGAR FILA ===
            $sheet->setCellValue("A".$fila, $usu->nombreUsuario);
            $sheet->setCellValue("B".$fila, date("d-m-Y",strtotime($diaActual)));
            $sheet->setCellValue("C".$fila, $totalDia);
            $sheet->setCellValue("D".$fila, $horaDia);
            $sheet->setCellValue("E".$fila, $debe);
            $sheet->setCellValue("F".$fila, $extra);

            $fila++;
        }
    }

    // Descargar archivo
    $filename = "Marcaciones_Contador_".date("Ymd_His").".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelVentaItem()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Venta_Item");

    // Título principal
    $sheet->setCellValue("A1", "REPORTE DE VENTA POR ITEM");
    $sheet->mergeCells("A1:G1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {

        $col = "A";
        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $filaExcel++;
    }

    // Descargar archivo
    $filename = "Venta_Item_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelUtilidadProducto()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Utilidad_Producto");

    // Encabezado principal
    $sheet->setCellValue("A1", "REPORTE DE UTILIDAD POR PRODUCTO");
    $sheet->mergeCells("A1:H1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {

        $col = "A";
        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $filaExcel++;
    }

    // Descargar archivo
    $filename = "UtilidadProducto_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}


public function ExcelAdvalorem()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Advalorem");

    // Título principal
    $sheet->setCellValue("A1", "REPORTE ADVALOREM");
    $sheet->mergeCells("A1:H1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $filaExcel = 3;

    foreach ($tabla as $fila) {

        $col = "A";

        foreach ($fila as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $filaExcel++;
    }

    // Descargar archivo
    $filename = "Advalorem_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

public function ExcelUtilidad()
{
    // Permisos (usa tu helper global como en otros métodos)
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    // Recuperar tabla desde sesión
    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        log_message('error', "ExcelUtilidad: tabla_reporte vacía o no es array");
        echo "No hay datos para exportar.";
        return;
    }

    // Cargar PhpSpreadsheet
    require_once FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("UtilidadProducto");

    // Opcional: título
    $sheet->setCellValue("A1", "REPORTE DE UTILIDAD POR PRODUCTO");
    $sheet->mergeCells("A1:I1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Empezar en fila 3 (respetar título)
    $filaExcel = 3;

    foreach ($tabla as $fila) {
        $col = 'A';
        foreach ($fila as $valor) {
            // Escribir celda
            $sheet->setCellValue($col.$filaExcel, $valor);
            // Auto size (no es muy eficiente en hojas enormes, pero está bien para reportes)
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        $filaExcel++;
    }

    // Preparar descarga
    $filename = "UtilidadProducto_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

	
	//Reporte de las Ventas en un periodo de tiempo
	function ReporteVenta(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Reporte de Ventas	";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-file-pdf",
					"controlador" => "Reportes",
					"proceso" => "ReporteVenta",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/reportes.js"
					),
				);
				GblPlantilla("reportes/reporteVenta",$datosVista,$extras,$titulo);
			}
		}
	}
	function ReporteVentaItem(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else {
			if($this->input->method(TRUE) == "GET"){
				$titulo = "Reporte de Ventas Detallado";
				$datosVista = array(
					"titulo" => $titulo,
					"icono" => "fa fa-file-pdf",
					"controlador" => "Reportes",
					"proceso" => "ReporteVentaItem",
				);
				$extras = array(
					'css' => array(),
					'js' => array(
						"scripts/reportes.js?q=".uniqid()
					),
				);
				GblPlantilla("reportes/reporteVentaItem",$datosVista,$extras,$titulo);
			}
		}
	}
	
	function VerReporteVenta() {
    if ($this->input->method(TRUE) != "GET") return;

    $fechaInicio = $this->input->get("fechaInicio");
    $fechaFinal = $this->input->get("fechaFinal");
    $tipoReporte = $this->input->get("tipoReporte");
    $sucursal = $this->session->idSucursal;

    // Condición base
    $condicionFacDet = array(
        "fechaFactura >=" => $fechaInicio,
        "fechaFactura <=" => $fechaFinal,
        "tipoFactura" => "Producto",
        "idSucursalFactura" => $sucursal,
        "estadoFactura" => "Cobrado",
    );

    // Filtrado por tipo de pago (si aplica)
    if ($tipoReporte == "VE") {
        $condicionFacDet["efectivoFactura !="] = 0;
    } elseif ($tipoReporte == "VT") {
        $condicionFacDet["tarjetaFactura !="] = 0;
    } elseif ($tipoReporte == "VB") {
        $condicionFacDet["bitcoinFactura !="] = 0;
    } elseif ($tipoReporte == "TR") {
        $condicionFacDet["transferenciaFactura !="] = 0;
    } elseif ($tipoReporte == "VP") {
        $condicionFacDet["pedidosYaFactura !="] = 0;
    }

    // Traer facturas
    $factura = TraerDatos("factura", $condicionFacDet, "fechaFactura DESC, horaFactura DESC");
    $factura = $this->_normalize_result($factura);

    // Periodo legible
    $periodoVigente = "";
    if (!empty($fechaInicio) && !empty($fechaFinal)) {
        list($a,$m,$d) = explode("-", $fechaInicio);
        list($a1,$m1,$d1) = explode("-", $fechaFinal);
        if ($a == $a1) {
            if ($m == $m1) {
                if ($d == $d1) {
                    $periodoVigente = "$d1 DE ".meses($m)." DE $a";
                } else {
                    $periodoVigente = "DEL $d AL $d1 DE ".meses($m)." DE $a";
                }
            } else {
                $periodoVigente = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        } else {
            $periodoVigente = "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }
    }

    // ======================================================
    //   ARMAR TABLA PARA EXPORTAR A EXCEL - REPORTE VENTA
    // ======================================================
    $tabla = [];
    // Encabezados EXACTOS (coinciden con el PDF)
    $tabla[] = ["DOC N.", "RESOL.", "N SERIE", "TIPO", "FECHA Y HORA", "CLIENTE", "TOTAL", "IVA", "PROPINA"];

    // Llenar tabla con las mismas filas que se usan en el PDF
    foreach ($factura as $fila) {
        // calcular total según tipoReporte (mismo criterio que PDF)
        if ($tipoReporte == "RG") {
            $total = $fila->totalFactura;
        } elseif ($tipoReporte == "VE") {
            $total = $fila->efectivoFactura - $fila->vueltoFactura;
        } elseif ($tipoReporte == "VT") {
            $total = $fila->tarjetaFactura;
        } elseif ($tipoReporte == "TR") {
            $total = $fila->transferenciaFactura;
        } elseif ($tipoReporte == "VB") {
            $total = $fila->bitcoinFactura;
        } elseif ($tipoReporte == "VP") {
            $total = $fila->pedidosYaFactura;
        } else {
            $total = $fila->totalFactura;
        }

        $documento = $fila->tipoDocumentoFactura . " " . $fila->numeroDocumentoFactura;
        $resolucion = $fila->resolucionFactura ?? "";
        $serie = $fila->serieFactura ?? "";
        $tipoCuenta = strtoupper($fila->tipoCuentaFactura ?? "");
        $fechaHora = ($fila->fechaFactura ?? "") . " " . ($fila->horaFactura ?? "");
        $cliente = $fila->nombreFactura ?? "";
        $porcentajeIva = floatval(GblTraerConfiguracion("iva") ?? 0);
        $iva = ($total * ($porcentajeIva / 100));
        $propina = floatval($fila->propinaFactura ?? 0);

        $tabla[] = [
            $documento,
            $resolucion,
            $serie,
            $tipoCuenta,
            $fechaHora,
            $cliente,
            number_format($total, 2, '.', ''),
            number_format($iva, 2, '.', ''),
            number_format($propina, 2, '.', '')
        ];
    }

    // Guardar tabla para Excel (incluso si está vacía dejamos encabezado)
    if (!isset($tabla) || !is_array($tabla)) $tabla = [];
    $this->session->set_userdata("tabla_reporte", $tabla);

    // ======================================================
    //   GENERAR PDF (mantengo tu código, con limpieza de salida)
    // ======================================================
    $this->fpdf = new Pdf();

    $this->fpdf->SetMargins(10,20);
    $this->fpdf->SetTopMargin(10);
    $this->fpdf->SetLeftMargin(10);
    $this->fpdf->AliasNbPages();
    $this->fpdf->SetAutoPageBreak(true,20);
    $this->fpdf->AddPage('L','LEGAL',0);
    $setX = 10;
    $setY = 5;
    $path = GblTraerConfiguracion("logoEmpresa");
    $this->fpdf->Image($path,$setX,$setY,30,25);

    $tipoPago = ($tipoReporte == "VE") ? "EFECTIVO" : (
        ($tipoReporte == "VT") ? "TARJETA" : (
            ($tipoReporte == "VB") ? "BITCOIN" : (
                ($tipoReporte == "TR") ? "TRANSFERENCIA" : (
                    ($tipoReporte == "VP") ? "PEDIDOS YA" : "GENERAL"
                )
            )
        )
    );

    $this->fpdf->SetX(0);
    $this->fpdf->SetXY(310,0);
    $this->fpdf->SetFont('Arial','I',8);
    $this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
    $this->fpdf->SetFont('Helvetica','', 14);
    $this->fpdf->SetXY(160,15);
    $this->fpdf->Cell(30, 4,"REPORTE DE VENTAS", 0, 1, 'C');
    $this->fpdf->SetXY(160,20);
    $this->fpdf->Cell(30, 4,"TIPO DE PAGO: ".$tipoPago, 0, 1, 'C');
    $this->fpdf->SetFont('Helvetica','', 10);
    $this->fpdf->SetXY(160,25);
    $this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

    $this->fpdf->Ln(12);

    // Totales
    $totalGeneral = 0;
    $totalGeneralPropina = 0;
    $totalGeneralIva = 0;
    $h = 6;
    $this->fpdf->SetFont('Helvetica', 'B', 10);
    $array_data = array(
        0 => array("DOC N.",25,"C"),
        1 => array("RESOL.",50,"C"),
        2 => array("N SERIE",30,"C"),
        3 => array("TIPO",25,"C"),
        4 => array("FECHA Y HORA",40,"C"),
        5 => array("CLIENTE",90,"C"),
        6 => array("TOTAL",25,"C"),
        7 => array("IVA",25,"C"),
        8 => array("PROPINA",25,"C"),
    );
    $this->fpdf->LineWriteB($array_data, 1, $h);
    $this->fpdf->SetFont('Helvetica', '', 10);

    if (!empty($factura)) {
        foreach ($factura as $fila) {
            // calcular total de nuevo (para PDF)
            if ($tipoReporte == "RG") {
                $total = $fila->totalFactura;
            } elseif ($tipoReporte == "VE") {
                $total = $fila->efectivoFactura - $fila->vueltoFactura;
            } elseif ($tipoReporte == "VT") {
                $total = $fila->tarjetaFactura;
            } elseif ($tipoReporte == "TR") {
                $total = $fila->transferenciaFactura;
            } elseif ($tipoReporte == "VB") {
                $total = $fila->bitcoinFactura;
            } elseif ($tipoReporte == "VP") {
                $total = $fila->pedidosYaFactura;
            } else {
                $total = $fila->totalFactura;
            }

            $documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
            $tipo = strtoupper($fila->tipoCuentaFactura ?? "");
            $fecha = ($fila->fechaFactura ?? "")." ".($fila->horaFactura ?? "");
            $cliente = $fila->nombreFactura ?? "";
            $resolucion = $fila->resolucionFactura ?? "";
            $serie = $fila->serieFactura ?? "";
            $propina = floatval($fila->propinaFactura ?? 0);
            $porcentajeIva = floatval(GblTraerConfiguracion("iva") ?? 0);
            $iva = ($total * ($porcentajeIva / 100));
            $array_data = array(
                1 => array($documento,25,"L"),
                2 => array($resolucion,50,"C"),
                3 => array($serie,30,"C"),
                4 => array($tipo,25,"C"),
                5 => array($fecha,40,"C"),
                6 => array(utf8_decode($cliente),90,"L"),
                7 => array("$".number_format($total,2),25,"R"),
                8 => array("$".number_format($iva,2),25,"R"),
                9 => array("$".number_format($propina,2),25,"R"),
            );
            $this->fpdf->LineWriteB($array_data, 1, $h);

            $totalGeneral += $total;
            $totalGeneralPropina += $propina;
            $totalGeneralIva += $iva;
        }
    }

    // Total final en PDF
    $array_data = array(
        1 => array("TOTAL",260,"C","B"),
        2 => array("$".number_format($totalGeneral,2),25,"R","B"),
        3 => array("$".number_format($totalGeneralIva,2),25,"R","B"),
        4 => array("$".number_format($totalGeneralPropina,2),25,"R","B"),
    );
    $this->fpdf->LineWriteB($array_data, 1, $h);

    // Salida PDF
    $filename = "Reporte_de_venta_".str_replace(' ','_',$periodoVigente).".pdf";
    $this->_safe_output_clean();
    try {
        $this->fpdf->Output($filename, "I");
    } catch (Exception $e) {
        log_message('error', "FPDF Output error: " . $e->getMessage());
        show_error("Error generando PDF. Revisa logs.");
    }
    exit;
}

	
	function cargarAperturas() {
		if($this->input->method(TRUE) == "POST"){
			$fechaInicio = $this->input->post("f1");
			$fechaFinal = $this->input->post("f2");
			$datosRespuesta["bandera"] = "";
			if($fechaInicio != "" && $fechaFinal != ""){
				$datosAperturas = TraerDatos('corteCaja',array("fechaCorte >= " => $fechaInicio, "fechaCorte <=" => $fechaFinal), 'fechaCorte DESC');
				$aperturas = "<option value='' >Seleccione una apertura</option>";
				foreach ($datosAperturas as $apertura):
					$aperturas .= "<option value='".md5($apertura->idCorteCaja)."' >".fecha_d_m_a($apertura->fechaCorte)." ".hora($apertura->horaCorte)."</option>";
				endforeach;
			}
			$datosRespuesta["aperturas"] = $aperturas;
			echo json_encode($datosRespuesta);
		}
	}
	

								//Reporte de las Ventas en un periodo de tiempo
								function ReporteCompra(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Compras";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteCompra",
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteCompra",$datosVista,$extras,$titulo);
										}
									}
								}
								public function VerReporteCompra() {
    if($this->input->method(TRUE) == "GET"){
        $fechaInicio = $this->input->get("fechaInicio");
        $fechaFinal = $this->input->get("fechaFinal");
        $sucursal = $this->session->idSucursal;

        $condicionCompra = array(
            "idSucursalInsumoMovimiento" => $sucursal,
            "tipoMovimientoInsumo" => "Compra"
        );
        $joinCompra = array(
            array(
                "tabla" => "proveedor",
                "condicion" => "insumoMovimiento.idProveedorInsumoMovimiento = proveedor.idProveedor"
            )
        );
        $compras = TraerDatosJoin("insumoMovimiento",$condicionCompra,"fechaHoraInsumoMovimiento DESC",$joinCompra);

        // Construcción del periodo
        $periodoVigente = "";
        list($a,$m,$d) = explode("-", $fechaInicio);
        list($a1,$m1,$d1) = explode("-", $fechaFinal);
        if($a == $a1){
            if($m==$m1){
                if($d==$d1){
                    $periodoVigente="$d1 DE ".meses($m)." DE $a";
                } else {
                    $periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
                }
            } else {
                $periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        } else {
            $periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }

        // ======================================================
        //   ARMAR TABLA PARA EXPORTAR A EXCEL - REPORTE COMPRA
        // ======================================================

        $tabla = [];

        // Encabezado corporativo (mismo info que PDF)
        $tabla[] = [ GblTraerConfiguracion("nombreEmpresa") ];
        $tabla[] = [ "REPORTE DE COMPRAS" ];
        $tabla[] = [ "PERIODO: ".$periodoVigente ];
        $tabla[] = [ "VALORES EXPRESADOS EN: DÓLARES DE LOS ESTADOS UNIDOS DE AMÉRICA" ];
        $tabla[] = [ "NIT: ".GblTraerConfiguracion("nitPatrono") ];
        $tabla[] = [ "NRC: ".GblTraerConfiguracion("nrcPatrono") ];
        $tabla[] = []; // línea vacía

        // Encabezados de la tabla (idénticos a las columnas del PDF)
        $tabla[] = [
            "FECHA",
            "PROVEEDOR",
            "NRC",
            "TIPO DOC",
            "N DOC",
            "DESCRIPCION",
            "TOTAL"
        ];

        // Misma fuente de datos y mismos campos que usa el PDF (líneas del bloque LineWriteB)
        if ($compras !== 0) {
            foreach ($compras as $fila) {
                $fechaArr = explode(" ", $fila->fechaHoraInsumoMovimiento ?? ($fila->fecha ?? ""));
                $fecha       = $fechaArr[0] ?? "";
                $proveedor   = $fila->nombreProveedor ?? "";
                $nrc         = $fila->nrcProveedor ?? "";
                $tipo        = $fila->tipoDocumentoInsumoMovimiento ?? "";
                $nDoc        = $fila->numeroDocumentoInsumoMovimiento ?? "";
                $descripcion = $fila->descripcionInsumoMovimiento ?? "";
                $total       = floatval($fila->totalInsumoMovimiento ?? 0);

                $tabla[] = [
                    $fecha,
                    $proveedor,
                    $nrc,
                    $tipo,
                    $nDoc,
                    $descripcion,
                    number_format($total, 2, '.', '')
                ];
            }
        }

        // Guardar tabla para Excel (aunque esté vacía guardamos encabezados)
        if (!isset($tabla) || !is_array($tabla)) $tabla = [];
        $this->session->set_userdata("tabla_reporte", $tabla);

        // ======================================================
        //   GENERAR PDF (sin modificaciones: mantén tu PDF tal cual)
        // ======================================================

        $this->fpdf = new Pdf();

        $this->fpdf->SetMargins(10,20);
        $this->fpdf->SetTopMargin(10);
        $this->fpdf->SetLeftMargin(10);
        //Numeracion de paginas
        $this->fpdf->AliasNbPages();
        //Salto automatico de pagina margen de 20 mm
        $this->fpdf->SetAutoPageBreak(true,20);
        //Agrega la pagina a trabajar
        $this->fpdf->AddPage('L','LEGAL',0);
        $setX = 10;
        $setY = 5;
        //$path = base_url("vendors/core/img/dms.png");
        $path = GblTraerConfiguracion("logoEmpresa");
        $this->fpdf->Image($path,$setX,$setY,30,25);

        // Movernos a la derecha
        // Título

        $this->fpdf->SetX(0);
        $this->fpdf->SetXY(310,0);
        $this->fpdf->SetFont('Arial','I',8);
        $this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
        $this->fpdf->SetFont('Helvetica','', 14);
        $this->fpdf->SetXY(160,15);
        $this->fpdf->Cell(30, 4,"REPORTE DE COMPRAS", 0, 1, 'C');
        $this->fpdf->SetFont('Helvetica','', 10);
        $this->fpdf->SetXY(160,25);
        $this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

        // Salto de línea
        $this->fpdf->Ln(12);
        //LISTA VARIABLES PARA ALMACENAR TOTAL
        $n = 1;


        $total = 0;
        $totalGeneral = 0;
        $setY=30;
        $setX=25;
        $h = 6;
        $this->fpdf->SetFont('Helvetica', 'B', 10);
        $array_data = array(
            0 => array("FECHA",40,"C"),
            1 => array("PROVEEDOR",100,"C"),
            2 => array("NRC",25,"C"),
            3 => array("TIPO DOC",20,"C"),
            4 => array("N DOC",25,"C"),
            5 => array("DESCRIPCION",100,"C"),
            6 => array("TOTAL",25,"C"),
        );
        $this->fpdf->LineWriteB($array_data, 1,$h);
        $this->fpdf->SetFont('Helvetica', '', 10);
        if($compras !==0){
            foreach($compras as $fila){
                $fechaArr = explode(" ",$fila->fechaHoraInsumoMovimiento ?? ($fila->fecha ?? ""));
                $fecha = $fechaArr[0] ?? "";
                $proveedor = $fila->nombreProveedor ?? "";
                $nrc = $fila->nrcProveedor ?? "";
                $tipo = $fila->tipoDocumentoInsumoMovimiento ?? "";
                $nDoc = $fila->numeroDocumentoInsumoMovimiento ?? "";
                $descripcion = $fila->descripcionInsumoMovimiento ?? "";
                $total = floatval($fila->totalInsumoMovimiento ?? 0);

                $array_data = array(
                    0 => array($fecha,40,"C"),
                    1 => array($proveedor,100,"C"),
                    2 => array($nrc,25,"C"),
                    3 => array($tipo,20,"C"),
                    4 => array($nDoc,25,"C"),
                    5 => array($descripcion,100,"C"),
                    6 => array("$".number_format($total,2),25,"C"),
                );
                $this->fpdf->LineWriteB($array_data, 1,$h);
                $totalGeneral += $total;
            }
            $array_data = array(
                1 => array("TOTAL",310,"C","B"),
                2 => array("$".number_format($totalGeneral,2),25,"R","B"),
            );
            $this->fpdf->LineWriteB($array_data, 1,$h);
        }

        $filename = "Reporte_de_compra_".str_replace(' ','_',$periodoVigente).".pdf";
        ob_clean();
        // antes de enviar el PDF, limpiamos buffers que puedan tener warnings o salida accidental
        $this->_safe_output_clean();

        try {
            $this->fpdf->Output($filename, "I");
        } catch (Exception $e) {
            log_message('error', "FPDF Output error: " . $e->getMessage());
            show_error("Error generando PDF. Revisa logs.");
        }
        exit;

        //$this->imprimirReporteComision($fechaInicio,$fechaFinal);
    }
}

// =====================
// Excel Admin
// =====================
public function ExcelCompra()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Company + title header
    $sheet->setCellValue("A1", $tabla[0][0] ?? GblTraerConfiguracion("nombreEmpresa"));
    $sheet->mergeCells("A1:J1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A2", $tabla[1][0] ?? "REPORTE DE COMPRAS");
    $sheet->mergeCells("A2:J2");
    $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle("A2")->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A3", $tabla[2][0] ?? "PERIODO:");
    $sheet->mergeCells("A3:J3");
    $sheet->getStyle("A3")->getAlignment()->setHorizontal('center');

    // NIT / NRC lines if exist
    $sheet->setCellValue("A4", $tabla[3][0] ?? "");
    $sheet->mergeCells("A4:J4");
    $sheet->setCellValue("A5", $tabla[4][0] ?? "");
    $sheet->mergeCells("A5:J5");
    $sheet->setCellValue("A6", $tabla[5][0] ?? "");
    $sheet->mergeCells("A6:J6");

    // encabezados a partir de la fila 8
    $filaEncabezados = 8;
    $col = "A";
    $headerRow = $tabla[7] ?? [];
    foreach ($headerRow as $titulo) {
        $sheet->setCellValue($col.$filaEncabezados, $titulo);
        $sheet->getStyle($col.$filaEncabezados)->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Datos (desde índice 8 en $tabla -> fila 9 en excel)
    $filaExcel = $filaEncabezados + 1;
    for ($i = 8; $i < count($tabla); $i++) {
        $col = "A";
        foreach ($tabla[$i] as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $col++;
        }
        $filaExcel++;
    }

    // Descargar
    $filename = "Reporte_Compra_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

// =====================
// Excel Contador (versión contador independiente — aquí igual a admin)
// =====================
public function ExcelCompraContador()
{
    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Replicar exactamente la misma estructura que ExcelCompra
    $sheet->setCellValue("A1", $tabla[0][0] ?? GblTraerConfiguracion("nombreEmpresa"));
    $sheet->mergeCells("A1:J1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A2", $tabla[1][0] ?? "REPORTE DE COMPRAS");
    $sheet->mergeCells("A2:J2");
    $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle("A2")->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A3", $tabla[2][0] ?? "PERIODO:");
    $sheet->mergeCells("A3:J3");
    $sheet->getStyle("A3")->getAlignment()->setHorizontal('center');

    $sheet->setCellValue("A4", $tabla[3][0] ?? "");
    $sheet->mergeCells("A4:J4");
    $sheet->setCellValue("A5", $tabla[4][0] ?? "");
    $sheet->mergeCells("A5:J5");
    $sheet->setCellValue("A6", $tabla[5][0] ?? "");
    $sheet->mergeCells("A6:J6");

    $filaEncabezados = 8;
    $col = "A";
    $headerRow = $tabla[7] ?? [];
    foreach ($headerRow as $titulo) {
        $sheet->setCellValue($col.$filaEncabezados, $titulo);
        $sheet->getStyle($col.$filaEncabezados)->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    $filaExcel = $filaEncabezados + 1;
    for ($i = 8; $i < count($tabla); $i++) {
        $col = "A";
        foreach ($tabla[$i] as $valor) {
            $sheet->setCellValue($col.$filaExcel, $valor);
            $col++;
        }
        $filaExcel++;
    }

    $filename = "Reporte_Compra_Contador_".date("Ymd_His").".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}
								
								//Reporte de las ventasd por mesero
								function ReporteVentaMesero(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Ventas Mesero";
											$meseros = TraerDatos("usuario", array('idUsuario >' => "0",'activoUsuario' => 1, 'idSucursalUsuario' => $this->session->idSucursal));
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteVentaMesero",
												"meseros" => $meseros,
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteVentaMesero",$datosVista,$extras,$titulo);
										}
									}
								}
								// ============================================================================
//    REPORTE: VENTA POR MESERO
//    Incluye:
//      - VerReporteVentaMesero (PDF)
//      - ExcelVentaMesero (Admin)
//      - ExcelVentaMeseroContador (Contador)
// ============================================================================

public function VerReporteVentaMesero() {
    if($this->input->method(TRUE) == "GET"){

        $fechaInicio = $this->input->get("fechaInicio");
        $fechaFinal  = $this->input->get("fechaFinal");
        $idUsuario   = $this->input->get("idUsuario");
        $sucursal    = $this->session->idSucursal;

        // =======================
        //     TRAER DATOS
        // =======================

        $joinFac = array(
            array(
                "tabla" => "pedido",
                "condicion" => "factura.idReferenciaFactura = pedido.idPedido",
                "tipo" => "inner"
            )
        );

        $condicion = array(
            "pedido.idUsuarioPedido" => $idUsuario,
            "fechaFactura >="        => $fechaInicio,
            "fechaFactura <="        => $fechaFinal,
            "tipoFactura"            => "Producto",
            "idSucursalFactura"      => $sucursal,
            "estadoFactura"          => "Cobrado"
        );

        $factura = TraerDatosJoin("factura", $condicion, "fechaFactura DESC", $joinFac);

        // =======================
        //   GENERAR PERIODO
        // =======================

        $periodoVigente = "";
        list($a,$m,$d)   = explode("-", $fechaInicio);
        list($a1,$m1,$d1) = explode("-", $fechaFinal);

        if($a == $a1){
            if($m == $m1){
                if($d == $d1){
                    $periodoVigente = "$d1 DE ".meses($m)." DE $a";
                } else {
                    $periodoVigente = "DEL $d AL $d1 DE ".meses($m)." DE $a";
                }
            } else {
                $periodoVigente = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        } else {
            $periodoVigente = "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }

        // =====================================================================
        //   ARMAR TABLA PARA EXPORTAR A EXCEL — MISMO MODELO DE REPORTES ANTERIORES
        // =====================================================================

        $tabla = [];

        // Encabezados corporativos
        $tabla[] = [ GblTraerConfiguracion("nombreEmpresa") ];
        $tabla[] = [ "REPORTE DE VENTAS POR MESERO" ];
        $tabla[] = [ "PERIODO: ".$periodoVigente ];
        $tabla[] = [ "VALORES EXPRESADOS EN DÓLARES" ];
        $tabla[] = [ "NIT: ".GblTraerConfiguracion("nitPatrono") ];
        $tabla[] = [ "NRC: ".GblTraerConfiguracion("nrcPatrono") ];
        $tabla[] = [];

        // Encabezados de la tabla
        $tabla[] = [
            "DOCUMENTO",
            "TIPO CUENTA",
            "FECHA Y HORA",
            "CLIENTE",
            "TOTAL",
            "ESTADO"
        ];

        // Normalizamos datos para Excel
        $datosVista = $factura;
        $fuente = $this->_normalize_result($datosVista ?? null);

        if (!$this->_is_iterable_var($fuente)) $fuente = [];

        foreach ($fuente as $fila) {

            $documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
            $tipo      = strtoupper($fila->tipoCuentaFactura ?? "");
            $fechaFull = ($fila->fechaFactura ?? "")." ".($fila->horaFactura ?? "");
            $cliente   = $fila->nombreFactura ?? "";
            $total     = floatval($fila->totalFactura ?? 0);
            $estado    = $fila->estadoFactura ?? "";

            $tabla[] = [
                $documento,
                $tipo,
                $fechaFull,
                $cliente,
                $total,
                $estado
            ];
        }

        // Guardar para Excel
        $this->session->set_userdata("tabla_reporte", $tabla);


        // =====================================================================
        //                           GENERAR PDF
        // =====================================================================

        $this->fpdf = new Pdf();
        $this->fpdf->SetMargins(10,20);
        $this->fpdf->SetTopMargin(10);
        $this->fpdf->AliasNbPages();
        $this->fpdf->SetAutoPageBreak(true,20);
        $this->fpdf->AddPage('L','LETTER',0);

        $path = GblTraerConfiguracion("logoEmpresa");
        $this->fpdf->Image($path,10,5,30,25);

        $this->fpdf->SetXY(235,0);
        $this->fpdf->SetFont('Arial','I',8);
        $this->fpdf->Cell(40, 10, 'Fecha de impresión: '.date('d-m-Y'), 0, 0, 'R');

        $this->fpdf->SetXY(125,15);
        $this->fpdf->SetFont('Helvetica','', 14);
        $this->fpdf->Cell(30, 4,"REPORTE DE VENTAS POR MESERO", 0, 1, 'C');

        $this->fpdf->SetFont('Helvetica','', 10);
        $this->fpdf->SetXY(125,20);
        $this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

        $this->fpdf->Ln(12);

        $this->fpdf->SetFont('Helvetica', 'B', 14);
        $array_data = array(
            0 => array("DOCUMENTO",35,"C"),
            1 => array("TIPO CUENTA",35,"C"),
            2 => array("FECHA Y HORA",50,"C"),
            3 => array("CLIENTE",89,"C"),
            4 => array("TOTAL",23,"C"),
            5 => array("ESTADO",25,"C"),
        );
        $this->fpdf->LineWriteB($array_data, 1,6);

        $this->fpdf->SetFont('Helvetica','',12);

        if($factura !== 0){
            foreach($factura as $fila){

                $documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
                $tipo = strtoupper($fila->tipoCuentaFactura);
                $fecha = $fila->fechaFactura." ".$fila->horaFactura;
                $cliente = utf8_decode($fila->nombreFactura);
                $total = "$".number_format($fila->totalFactura,2);
                $estado = $fila->estadoFactura;

                $array_data = array(
                    1 => array($documento,35,"L"),
                    2 => array($tipo,35,"C"),
                    3 => array($fecha,50,"C"),
                    4 => array($cliente,89,"L"),
                    5 => array($total,23,"R"),
                    6 => array($estado,25,"C","B"),
                );
                $this->fpdf->LineWriteB($array_data, 1,6);
            }
        }

        $filename = "Reporte_venta_mesero_".str_replace(" ","_",$periodoVigente).".pdf";

        ob_clean();
        $this->_safe_output_clean();

        try {
            $this->fpdf->Output($filename, "I");
        } catch (Exception $e) {
            log_message('error',"FPDF output error: ".$e->getMessage());
            show_error("Error generando PDF.");
        }

        exit;
    }
}

# ===================================================================
# EXCEL ADMIN — ExcelVentaMesero
# ===================================================================

public function ExcelVentaMesero()
{
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // ========= ENCABEZADOS CORPORATIVOS =========
    for ($i = 0; $i <= 5; $i++) {
        if (!isset($tabla[$i][0])) continue;

        $fila = $i + 1;
        $sheet->setCellValue("A$fila", $tabla[$i][0]);
        $sheet->mergeCells("A$fila:F$fila");
        $sheet->getStyle("A$fila")->getAlignment()->setHorizontal('center');

        if ($i <= 1) {
            $sheet->getStyle("A$fila")->getFont()->setBold(true)->setSize(14);
        }
    }

    // ========= ENCABEZADOS DE TABLA =========
    $filaEnc = 8;
    $col = "A";

    foreach ($tabla[7] as $titulo) {
        $sheet->setCellValue($col . $filaEnc, $titulo);
        $sheet->getStyle($col . $filaEnc)->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // ========= FILAS DE DATOS =========
    $fila = 9;
    for ($i = 8; $i < count($tabla); $i++) {
        $col = "A";
        foreach ($tabla[$i] as $valor) {
            $sheet->setCellValue($col . $fila, $valor);
            $col++;
        }
        $fila++;
    }

    // ========= DESCARGAR EXCEL =========
    $filename = "Reporte_Venta_Mesero_" . date("Ymd_His") . ".xlsx";
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

# ===================================================================
# EXCEL CONTADOR — ExcelVentaMeseroContador
# ===================================================================

public function ExcelVentaMeseroContador() {

    if (!GblPermisos($this,__FUNCTION__,$this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");
    if (empty($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH."vendor/autoload.php";

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Columnas omitidas para contabilidad: TIPO CUENTA, CLIENTE y ESTADO son datos
    // de servicio/salón, no necesarios para conciliar ventas o comisiones por mesero.
    $columnasOmitidas = [1, 3, 5];

    // Encabezados corporativos
    for ($i=0; $i<=5; $i++){
        if (isset($tabla[$i][0])) {
            $sheet->setCellValue("A".($i+1), $tabla[$i][0]);
            $sheet->mergeCells("A".($i+1).":C".($i+1));
            $sheet->getStyle("A".($i+1))->getAlignment()->setHorizontal('center');
            if ($i <= 1) $sheet->getStyle("A".($i+1))->getFont()->setBold(true);
        }
    }

    // Encabezados de tabla (se omiten columnas no esenciales para contabilidad)
    $filaEnc = 8;
    $columna = "A";
    foreach ($tabla[7] as $idx => $titulo){
        if (in_array($idx, $columnasOmitidas)) continue;
        $sheet->setCellValue($columna.$filaEnc, $titulo);
        $sheet->getStyle($columna.$filaEnc)->getFont()->setBold(true);
        $sheet->getColumnDimension($columna)->setAutoSize(true);
        $columna++;
    }

    // Datos
    $fila = 9;
    for ($i=8; $i < count($tabla); $i++){
        $columna = "A";
        foreach ($tabla[$i] as $idx => $valor){
            if (in_array($idx, $columnasOmitidas)) continue;
            $sheet->setCellValue($columna.$fila, $valor);
            $columna++;
        }
        $fila++;
    }

    $filename = "Reporte_Venta_Mesero_Contador_".date("Ymd_His").".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}


								//Reporte de Utilidad Por Venta
								function ReporteUtilidad(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Utilidades";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteUtilidad",
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteUtilidad",$datosVista,$extras,$titulo);
										}
									}
								}
								function VerReporteUtilidad() {

    if ($this->input->method(TRUE) != "GET") return;

    $fechaInicio = $this->input->get("fechaInicio");
    $fechaFinal  = $this->input->get("fechaFinal");
    $tipoReporte = $this->input->get("tipoReporte");
    $sucursal    = $this->session->idSucursal;

    // ==========================
    // CONSULTA PRINCIPAL
    // ==========================
    $joinFac = array(
        array(
            "tabla"     => "insumoMovimiento as mov",
            "condicion" => "factura.idFactura = mov.idFacturaInsumoMovimiento",
            "tipo"      => "left",
            "campos"    => "mov.totalInsumoMovimiento"
        ),
    );

    $condicionFacDet = array(
        "fechaFactura >="    => $fechaInicio,
        "fechaFactura <="    => $fechaFinal,
        "tipoFactura"        => "Producto",
        "idSucursalFactura"  => $sucursal,
        "estadoFactura"      => "Cobrado"
    );

    $factura = TraerDatosJoin(
        "factura",
        $condicionFacDet,
        "fechaFactura DESC, horaFactura DESC",
        $joinFac
    );

    // ==========================
    // PERIODO
    // ==========================
    list($a,$m,$d)   = explode("-", $fechaInicio);
    list($a1,$m1,$d1)= explode("-", $fechaFinal);

    if ($a == $a1) {
        if ($m == $m1) {
            $periodoVigente = ($d == $d1)
                ? "$d1 DE ".meses($m)." DE $a"
                : "DEL $d AL $d1 DE ".meses($m)." DE $a";
        } else {
            $periodoVigente = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
        }
    } else {
        $periodoVigente =
            "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
    }

    // ======================================================
    //   ARMAR TABLA EXCEL - UTILIDAD GENERAL (MISMO PDF)
    // ======================================================

    $tabla = [];
    $tabla[] = [
    "DOCUMENTO",
    "TIPO",
    "FECHA",
    "HORA",
    "VENTA",
    "COSTO",
    "UTILIDAD"
];

    if (!empty($factura)) {

        foreach ($factura as $fila) {

            $documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
            $tipo      = strtoupper($fila->tipoCuentaFactura);
            $fecha = $fila->fechaFactura;
			$hora  = $fila->horaFactura;
            $venta     = floatval($fila->totalFactura);
            $costo     = floatval($fila->totalInsumoMovimiento);
            $utilidad  = $venta - $costo;

            $tabla[] = [
				$documento,
				$tipo,
				$fecha,
				$hora,
				$venta,
				$costo,
				$utilidad
			];

        }
    }

    // Guardar en sesión para Excel
    $this->session->set_userdata("tabla_reporte", $tabla);

    // ======================================================
    //   PDF (igual que tu versión actual)
    // ======================================================

    $this->fpdf = new Pdf();

    $this->fpdf->SetMargins(10,20);
    $this->fpdf->SetTopMargin(10);
    $this->fpdf->SetLeftMargin(10);
    $this->fpdf->AliasNbPages();
    $this->fpdf->SetAutoPageBreak(true,20);
    $this->fpdf->AddPage('P','LETTER',0);

    $path = GblTraerConfiguracion("logoEmpresa");
    $this->fpdf->Image($path,10,5,30,25);

    $this->fpdf->SetXY(235,0);
    $this->fpdf->SetFont('Arial','I',8);
    $this->fpdf->Cell(40,10,'Fecha de impresión: '.date('d-m-Y'),0,0,'R');

    $this->fpdf->SetXY(95,15);
    $this->fpdf->SetFont('Helvetica','',14);
    $this->fpdf->Cell(30,4,"REPORTE DE UTILIDAD POR VENTA",0,1,'C');

    $this->fpdf->SetXY(95,20);
    $this->fpdf->SetFont('Helvetica','',10);
    $this->fpdf->Cell(30,4,"PERIODO: ".$periodoVigente,0,1,'C');

    $this->fpdf->Ln(12);

    $h = 6;
    $this->fpdf->SetFont('Helvetica','B',12);
    $this->fpdf->LineWriteB([
        0 => array("DOCUMENTO",30,"C"),
		1 => array("TIPO",25,"C"),
		2 => array("FECHA",25,"C"),
		3 => array("HORA",20,"C"),
		4 => array("VENTA",25,"C"),
		5 => array("COSTO",25,"C"),
		6 => array("UTILIDAD",25,"C"),
    ],1,$h);

    $this->fpdf->SetFont('Helvetica','',10);

    $totalVen=0; $totalCos=0; $totalUtil=0;

    if (!empty($factura))
    foreach ($factura as $fila) {

        $documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
        $tipo      = strtoupper($fila->tipoCuentaFactura);
        $fecha = $fila->fechaFactura;
		$hora  = $fila->horaFactura;
        $venta     = $fila->totalFactura;
        $costo     = $fila->totalInsumoMovimiento;
        $utilidad  = $venta - $costo;

        $this->fpdf->LineWriteB([
			1 => [$documento,30,"L"],
			2 => [$tipo,25,"C"],
			3 => [$fecha,25,"C"],
			4 => [$hora,20,"C"],
			5 => ["$".number_format($venta,2),25,"R"],
			6 => ["$".number_format($costo,2),25,"R"],
			7 => ["$".number_format($utilidad,2),25,"R"],
					],1,$h);


        $totalVen += $venta;
        $totalCos += $costo;
        $totalUtil += $utilidad;
    }

    $this->fpdf->SetFont('Helvetica','B',10);
    $this->fpdf->LineWriteB([
        1 => ["TOTALES",120,"C"],
        2 => ["$".number_format($totalVen,2),25,"R"],
        3 => ["$".number_format($totalCos,2),25,"R"],
        4 => ["$".number_format($totalUtil,2),25,"R"],
    ],1,$h);

    $filename = "Reporte_utilidad_".str_replace(" ","_",$periodoVigente).".pdf";
    $this->_safe_output_clean();
    $this->fpdf->Output($filename,"I");
    exit;
}


								//Reporte de Marcas
								function ReporteMarca(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Marcaciones";
											$usuarios = TraerDatos("usuario", array('idUsuario >' => "0",'activoUsuario' => 1, 'idSucursalUsuario' => $this->session->idSucursal));
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteMarca",
												"usuarios" => $usuarios,
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteMarca",$datosVista,$extras,$titulo);
										}
									}
								}
								function VerReporteMarca(){
    if($this->input->method(TRUE) == "GET"){

        $fechaInicio = $this->input->get("fechaInicio");
        $fechaFinal  = $this->input->get("fechaFinal");
        $idUsuario   = $this->input->get("idUsuario");

        // ==============================
        // PERIODO
        // ==============================
        list($a,$m,$d)   = explode("-", $fechaInicio);
        list($a1,$m1,$d1)= explode("-", $fechaFinal);

        if($a == $a1){
            if($m==$m1){
                if($d==$d1){
                    $periodoVigente="$d1 DE ".meses($m)." DE $a";
                } else {
                    $periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
                }
            } else {
                $periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        } else {
            $periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }

        // ======================================================
        //     ARMAR TABLA PARA EXPORTAR A EXCEL - MARCACIONES
        // ======================================================

        // Encabezados del Excel
        $tabla = [];
        $tabla[] = [
            "USUARIO",
            "FECHA",
            "HORA ENTRADA",
            "HORA SALIDA",
            "HORAS TRABAJADAS",
            "HORAS POR DÍA",
            "DEBE",
            "EXTRA"
        ];

        // ======================================================
        // OBTENER USUARIOS
        // ======================================================
        if($idUsuario != "T"){
            $usuarios = TraerDatos("usuario",["idUsuario"=>$idUsuario]);
        } else {
            $usuarios = TraerDatos(
                "usuario",
                ['idUsuario >' => "0",'activoUsuario' => 1, 'idSucursalUsuario' => $this->session->idSucursal]
            );
        }

        $dias = diferenciaDias($fechaInicio,$fechaFinal);

        foreach($usuarios as $usu){

            $idUsuarioMarca = $usu->idUsuario;

            $d = 0;
            while($d <= $dias){

                $diaActual = date("Y-m-d",strtotime($fechaInicio." + ".$d." days"));
                $d++;

                $condicionMarca = [
                    "fechaHoraEntradaMarca >=" => $diaActual." 00:00:01",
                    "fechaHoraEntradaMarca <=" => $diaActual." 23:59:59",
                    "estadoMarca" => "Inactivo",
                    "idUsuarioMarca"=>$idUsuarioMarca
                ];

                $marcas = TraerDatos("marca",$condicionMarca,"fechaHoraEntradaMarca ASC");

                if($marcas){
                    $horaDia = GblTraerConfiguracion("horasTrabajoEmpleado");

                    foreach($marcas as $m){

                        $fecha     = date_format(date_create($m->fechaHoraEntradaMarca),"d-m-Y");
                        $entrada   = date_format(date_create($m->fechaHoraEntradaMarca),"h:i:s A");
                        $salida    = date_format(date_create($m->fechaHoraSalidaMarca),"h:i:s A");

                        $trabajado = diferenciaHoras($m->fechaHoraSalidaMarca,$m->fechaHoraEntradaMarca);
                        $diferencia= diferenciaHoras($horaDia,$trabajado);

                        // ¿Debe o Extra?
                        $debe  = "";
                        $extra = "";

                        if($horaDia > $trabajado){
                            $debe = $diferencia;
                        } else {
                            $extra = $diferencia;
                        }

                        // ==== AGREGAR FILA A EXCEL ====
                        $tabla[] = [
                            $usu->nombreUsuario,
                            $fecha,
                            $entrada,
                            $salida,
                            $trabajado,
                            $horaDia,
                            $debe,
                            $extra
                        ];
                    }
                }
            }
        }

        // Guardar para Excel
        $this->session->set_userdata("tabla_reporte", $tabla);

        // ======================================================
        // -------------- PDF (NO SE MODIFICA) ------------------
        // ======================================================

        $this->fpdf = new Pdf();
        $this->fpdf->SetMargins(10,20);
        $this->fpdf->SetTopMargin(10);
        $this->fpdf->SetLeftMargin(10);
        $this->fpdf->AliasNbPages();
        $this->fpdf->SetAutoPageBreak(true,20);
        $this->fpdf->AddPage('L','LETTER',0);

        $path = GblTraerConfiguracion("logoEmpresa");
        $this->fpdf->Image($path,10,5,30,25);

        $this->fpdf->SetXY(235,0);
        $this->fpdf->SetFont('Arial','I',8);
        $this->fpdf->Cell(40, 10, 'Fecha de impresión: '.date('d-m-Y'), 0, 0, 'R');

        $this->fpdf->SetXY(125,15);
        $this->fpdf->SetFont('Helvetica','', 14);
        $this->fpdf->Cell(30, 4,"REPORTE DE MARCACIONES", 0, 1, 'C');

        $this->fpdf->SetXY(125,20);
        $this->fpdf->SetFont('Helvetica','', 10);
        $this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

        $this->fpdf->Ln(12);

        // == PDF IGUAL AL ORIGINAL (NO MODIFICADO) ==
        // Para mantener compatibilidad visual

        if($idUsuario != "T"){
            $usuarios = TraerDatos("usuario",["idUsuario"=>$idUsuario]);
        } else {
            $usuarios = TraerDatos("usuario",['idUsuario >' => "0",'activoUsuario' => 1, 'idSucursalUsuario' => $this->session->idSucursal]);
        }

        $dias = diferenciaDias($fechaInicio,$fechaFinal);

        $h = 6;

        foreach($usuarios as $usu){
            $this->fpdf->SetFont('Helvetica', 'B', 18);
            $this->fpdf->LineWriteB([
                0 => [$usu->nombreUsuario,260,"L","B"],
            ],0,$h);

            $d = 0;
            while($d <= $dias){
                $diaActual = date("Y-m-d",strtotime($fechaInicio." + ".$d." days"));
                $d++;

                $condicionMarca = [
                    "fechaHoraEntradaMarca >=" => $diaActual." 00:00:01",
                    "fechaHoraEntradaMarca <=" => $diaActual." 23:59:59",
                    "estadoMarca" => "Inactivo",
                    "idUsuarioMarca"=>$usu->idUsuario
                ];

                $marcas = TraerDatos("marca",$condicionMarca,"fechaHoraEntradaMarca ASC");

                if($marcas){
                    $this->fpdf->SetFont('Helvetica', 'B', 12);
                    $this->fpdf->LineWriteB([
                        1 => ["Hora Entrada",60,"C"],
                        2 => ["Hora Salida",60,"C"],
                        3 => ["Horas Trabajadas",40,"C"],
                        4 => ["Horas Por Dia",40,"C"],
                        5 => ["Debe",30,"C"],
                        6 => ["Extra",30,"C"],
                    ],1,$h);

                    $this->fpdf->SetFont('Helvetica', '', 12);

                    $horaDia = GblTraerConfiguracion("horasTrabajoEmpleado");

                    foreach($marcas as $m){

                        $entrada = date_format(date_create($m->fechaHoraEntradaMarca),"d-m-Y h:i:s A");
                        $salida  = date_format(date_create($m->fechaHoraSalidaMarca),"d-m-Y h:i:s A");

                        $trabajado = diferenciaHoras($m->fechaHoraSalidaMarca,$m->fechaHoraEntradaMarca);
                        $diferencia= diferenciaHoras($horaDia,$trabajado);

                        $debe  = "";
                        $extra = "";

                        if($horaDia > $trabajado){
                            $debe = $diferencia;
                        } else {
                            $extra = $diferencia;
                        }

                        $this->fpdf->LineWriteB([
                            1 => [$entrada,60,"C"],
                            2 => [$salida,60,"C"],
                            3 => [$trabajado,40,"C"],
                            4 => [$horaDia,40,"C"],
                            5 => [$debe,30,"C"],
                            6 => [$extra,30,"C"],
                        ],1,$h);
                    }

                    $this->fpdf->Ln();
                }
            }
        }

        $filename = "Reporte_de_marcacion_".str_replace(' ','_',$periodoVigente).".pdf";
        $this->_safe_output_clean();
        $this->fpdf->Output($filename, "I");
        exit;
    }
}

								

								//Reporte de COnteo de Inventario
								function ReporteConteo(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Conteo de Inventario";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteConteo",
												"insumoCategoria" => TraerDatos('insumoCategoria', array("idSucursalInsumoCategoria" => $this->session->idSucursal, "estadoInsumoCategoria !=" => "Borrado")),
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteConteo",$datosVista,$extras,$titulo);
										}
									}
								}
								public function VerReporteConteo()
{
    if ($this->input->method(TRUE) !== "GET") {
        show_404();
        return;
    }

    $categoriaInsumo = $this->input->get("categoriaInsumos");
    $periodoVigente = "";

    /* ======================================================
       OBTENER DATOS EXACTAMENTE COMO EL PDF
       ====================================================== */

    $joinInsumo = [
        [
            "tabla" => "insumoPresentacion as pre",
            "condicion" => "insumo.idInsumo = pre.idInsumo",
            "tipo" => "left",
            "campos" => "pre.unidadInsumoPresentacion AS unidad,costoInsumoPresentacion"
        ],
        [
            "tabla" => "insumoStock as stock",
            "condicion" => "insumo.idInsumo = stock.idInsumo",
            "tipo" => "left",
            "campos" => "stock.cantidadInsumoStock AS stock"
        ],
        [
            "tabla" => "presentacion",
            "condicion" => "pre.idPresentacion = presentacion.idPresentacion",
            "tipo" => "left",
            "campos" => "presentacion.nombrePresentacion"
        ],
    ];

    $condicionInsumo = [
        "pre.unidadInventarioInsumoPresentacion" => "1",
        "insumo.estadoInsumo" => "Activo",
        "pre.estadoInsumoPresentacion" => "Activo",
    ];

    $categoria = 'GENERAL';

    if ($categoriaInsumo != "T") {
        $condicionInsumo["insumo.idCategoriaInsumo"] = $categoriaInsumo;

        $cat = TraerUnDato("insumoCategoria", ["idInsumoCategoria" => $categoriaInsumo]);

        if ($cat) {
            $categoria = $cat->nombreInsumoCategoria;
        }
    }

    $insumo = TraerDatosJoin("insumo", $condicionInsumo, "", $joinInsumo);
    $datosVista = $insumo;

    /* ======================================================
       GENERAR TABLA PARA EXCEL
       ====================================================== */

    $tabla = [];
    $tabla[] = [
        "CORR.",
        "DESCRIPCION DEL PRODUCTO",
        "COSTO",
        "EXISTENCIA",
        "REAL"
    ];

    if (is_array($datosVista)) {
        foreach ($datosVista as $fila) {

            $stock = floatval($fila->stock ?? 0);
            $unidad = floatval($fila->unidad ?? 1);
            $existenciaSistema = ($unidad > 0) ? $stock / $unidad : 0;

            $tabla[] = [
                $fila->idInsumo ?? "",
                ($fila->nombreInsumo ?? "") . " (" . ($fila->nombrePresentacion ?? "") . ")",
                number_format(floatval($fila->costoInsumoPresentacion ?? 0), 4, '.', ''),
                number_format($existenciaSistema, 4, '.', ''),
                "" // columna REAL: se llena a mano durante el conteo físico, igual que en el PDF
            ];
        }
    }

    // Guardar tabla para Excel
    $this->session->set_userdata("tabla_reporte", $tabla);

    /* ======================================================
       PDF (NO TOCADO — SOLO PEGADO ÍNTEGRO)
       ====================================================== */

    $this->fpdf = new Pdf();
    $this->fpdf->SetMargins(10, 20);
    $this->fpdf->SetTopMargin(10);
    $this->fpdf->SetLeftMargin(10);
    $this->fpdf->AliasNbPages();
    $this->fpdf->SetAutoPageBreak(true, 20);
    $this->fpdf->AddPage('P', 'LETTER', 0);

    // LOGO
    $setX = 5;
    $setY = 5;
    $path = GblTraerConfiguracion("logoEmpresa");
    $this->fpdf->Image($path, $setX, $setY, 40, 30);

    // Fecha
    $this->fpdf->SetXY(305, 0);
    $this->fpdf->SetFont('Arial', 'I', 8);
    $this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: ' . date('d-m-Y')), 0, 0, 'R');

    // Título
    $this->fpdf->SetXY(95, 20);
    $this->fpdf->SetFont('Helvetica', 'B', 14);
    $this->fpdf->Cell(30, 4, GblTraerConfiguracion("nombreEmpresa"), 0, 1, 'C');

    $this->fpdf->SetXY(95, 25);
    $this->fpdf->SetFont('Helvetica', '', 12);
    $this->fpdf->Cell(30, 4, "HOJA DE CONTEO DE INVENTARIO", 0, 1, 'C');

    // Categoría
    $this->fpdf->SetFont('Helvetica', '', 12);
    $this->fpdf->SetXY(95, 30);
    $this->fpdf->Cell(30, 4, "CATEGORIA: " . $categoria, 0, 1, 'C');

    $this->fpdf->Ln(5);

    // ENCABEZADOS PDF
    $setY = 40;
    $setX = 10;
    $h = 5;

    $this->fpdf->SetFont('Arial', 'B', 10);
    $this->fpdf->SetXY($setX, $setY);
    $this->fpdf->Cell(25, $h, "CORR.", 1, 1, 'C', 0);
    $setX += 25;
    $this->fpdf->SetXY($setX, $setY);

    $this->fpdf->Cell(80, $h, "DESCRIPCION DEL PRODUCTO", 1, 1, 'C', 0);
    $setX += 80;
    $this->fpdf->SetXY($setX, $setY);

    $this->fpdf->Cell(30, $h, "COSTO", 1, 1, 'C', 0);
    $setX += 30;
    $this->fpdf->SetXY($setX, $setY);

    $this->fpdf->Cell(30, $h, "EXISTENCIA", 1, 1, 'C', 0);
    $setX += 30;
    $this->fpdf->SetXY($setX, $setY);

    $this->fpdf->Cell(30, $h, "REAL", 1, 1, 'C', 0);
    $setX = 10;
    $this->fpdf->SetXY($setX, $setY + $h);

    // DETALLE PDF
    if ($insumo) {
        foreach ($insumo as $fila) {

            $stock = $fila->stock / $fila->unidad;

            $this->fpdf->SetFont('Arial', '', 8);
            $array_data = [
                [Zfill($fila->idInsumo, 8), 25, "C"],
                [$fila->nombreInsumo . " (" . $fila->nombrePresentacion . ")", 80, "L"],
                [number_format($fila->costoInsumoPresentacion, 4), 30, "C"],
                [number_format($stock, 4), 30, "C"],
                ["", 30, "C"],
            ];
            $this->fpdf->LineWriteB($array_data, 1, $h);
        }
    }

    $filename = "Reporte_de_Conteo.pdf";

    $this->_safe_output_clean();
    $this->fpdf->Output($filename, "I");
    exit;
}
public function ExcelConteo()
{
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
        echo "No autorizado";
        return;
    }

    $tabla = $this->session->userdata("tabla_reporte");

    if (empty($tabla) || !is_array($tabla)) {
        echo "No hay datos para exportar.";
        return;
    }

    require_once FCPATH . 'vendor/autoload.php';
    $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $excel->getActiveSheet();

    // Título
    $sheet->setCellValue("A1", "REPORTE DE CONTEO DE INVENTARIO");
    $sheet->mergeCells("A1:H1");
    $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

    // Encabezados
    $fila = 3;
    $col = "A";
    foreach ($tabla[0] as $titulo) {
        $sheet->setCellValue($col . $fila, $titulo);
        $sheet->getStyle($col . $fila)->getFont()->setBold(true);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Datos
    $fila = 4;
    for ($i = 1; $i < count($tabla); $i++) {
        $col = "A";
        foreach ($tabla[$i] as $valor) {
            $sheet->setCellValue($col . $fila, $valor);
            $col++;
        }
        $fila++;
    }

    $filename = "Reporte_Conteo_" . date("Ymd_His") . ".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
    $writer->save('php://output');
    exit;
}
public function ExcelConteoContador()
{
    return $this->ExcelConteo();
}

								

								//Reporte de Utilidad por Venta Por Producto
								function ReporteUtilidadProducto(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Utilidad por Producto";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteUtilidadProducto",
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteUtilidadProducto",$datosVista,$extras,$titulo);
										}
									}
								}
								function VerReporteUtilidadProducto() {
    if ($this->input->method(TRUE) == "GET") {

        $fechaInicio  = $this->input->get("fechaInicio");
        $fechaFinal   = $this->input->get("fechaFinal");
        $idProducto   = $this->input->get("idProducto");

        $nombreProducto = "";
        $producto = TraerUnDato("producto", ["idProducto" => $idProducto]);
        if ($producto) $nombreProducto = $producto->nombreProducto;

        // ---- CONSULTA PRINCIPAL (MISMA QUE PDF) ----
        $joinFac = array(
            array(
                "tabla" => "insumoMovimiento as mov",
                "condicion" =>"factura.idFactura = mov.idFacturaInsumoMovimiento",
                "tipo" => "left",
                "campos" => "mov.totalInsumoMovimiento,mov.idPedidoInsumoMovimiento"
            ),
            array(
                "tabla" => "insumoMovimientoDetalle as movDet",
                "condicion" =>
                    "mov.idInsumoMovimiento = movDet.idInsumoMovimiento 
                     AND movDet.idProductoInsumo = '".$idProducto."'",
                "tipo" => "left",
                "campos" =>
                    "movDet.idProductoInsumo,
                     movDet.idPedidoMovimientoDetalle,
                     SUM(movDet.costoInsumoMovimientoDetalle) AS subCosto"
            ),
        );

        $condicionFacDet = array(
            "factura.fechaFactura >=" => $fechaInicio,
            "factura.fechaFactura <=" => $fechaFinal,
            "idSucursalFactura" => $this->session->idSucursal,
            "estadoFactura" => "Cobrado",
            "factura.tipoFactura" => "Producto",
        );

        $factura = TraerDatosJoin(
            "factura",
            $condicionFacDet,
            "fechaFactura DESC, horaFactura DESC",
            $joinFac,
            "factura.idFactura"
        );

        // ---------- PERIODO -------------
        list($a,$m,$d) = explode("-", $fechaInicio);
        list($a1,$m1,$d1) = explode("-", $fechaFinal);

        if ($a == $a1) {
            if ($m == $m1) {
                $periodoVigente = ($d == $d1)
                    ? "$d1 DE ".meses($m)." DE $a"
                    : "DEL $d AL $d1 DE ".meses($m)." DE $a";
            } else {
                $periodoVigente =
                    "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        } else {
            $periodoVigente =
                "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }

        // ======================================================
        //   ARMAR TABLA PARA EXPORTAR A EXCEL - UTILIDAD PRODUCTO
        // ======================================================

        $tabla = [];

        // Encabezados coherentes con el PDF
        $tabla[] = [
            "DOCUMENTO",
            "TIPO",
            "FECHA",
            "CANTIDAD",
            "PRECIO",
            "COSTO",
            "UTILIDAD"
        ];

        // Recorrer las mismas facturas que usa el PDF
        if ($factura) {

            foreach ($factura as $fila) {

                $documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
                $tipo      = strtoupper($fila->tipoCuentaFactura);
                $fecha     = $fila->fechaFactura." ".$fila->horaFactura;
                $idPedido  = $fila->idReferenciaFactura;
                $costo     = $fila->subCosto;

                // Consulta de cantidad y precio por pedido
                $condicionPed = array(
                    "pedido.idPedido" => $idPedido,
                    "pedDet.idProductoPedidoDetalle" => $idProducto,
                    "pedDet.estadoPedidoDetalle !=" => "Borrado"
                );

                $joinPed = array(
                    array(
                        "tabla" => "pedidoDetalle as pedDet",
                        "condicion" =>"pedido.idPedido = pedDet.idPedido",
                        "tipo" => "inner",
                        "campos" =>
                            "SUM(pedDet.cantidadPedidoDetalle) AS cantidad,
                             SUM(pedDet.precioPedidoDetalle * pedDet.cantidadPedidoDetalle) AS precio"
                    ),
                );

                $pedido = TraerUnDatoJoin("pedido", $condicionPed, $joinPed, "", "pedido.idPedido");

                if ($pedido) {

                    $cantidad = floatval($pedido->cantidad);
                    $precio   = floatval($pedido->precio);
                    $utilidad = $precio - $costo;

                    // ---- AGREGAR AL EXCEL ----
                    $tabla[] = [
                        $documento,
                        $tipo,
                        $fecha,
                        $cantidad,
                        $precio,
                        $costo,
                        $utilidad
                    ];
                }
            }
        }

        // Guardar tabla en la sesión para Excel
        $this->session->set_userdata("tabla_reporte", $tabla);

        // ======================================================
        //                   PDF (SIN CAMBIOS)
        // ======================================================

        $this->fpdf = new Pdf();
        $this->fpdf->SetMargins(10,20);
        $this->fpdf->SetTopMargin(10);
        $this->fpdf->SetLeftMargin(10);
        $this->fpdf->AliasNbPages();
        $this->fpdf->SetAutoPageBreak(true,20);
        $this->fpdf->AddPage('P','LETTER',0);

        $path = GblTraerConfiguracion("logoEmpresa");
        $this->fpdf->Image($path,10,5,30,25);

        $this->fpdf->SetXY(235,0);
        $this->fpdf->SetFont('Arial','I',8);
        $this->fpdf->Cell(40,10,'Fecha de impresión: '.date('d-m-Y'),0,0,'R');

        $this->fpdf->SetXY(95,15);
        $this->fpdf->SetFont('Helvetica','',14);
        $this->fpdf->Cell(30,4,"REPORTE DE UTILIDAD POR VENTA",0,1,'C');

        $this->fpdf->SetXY(95,20);
        $this->fpdf->SetFont('Helvetica','',10);
        $this->fpdf->Cell(30,4,"PERIODO: ".$periodoVigente,0,1,'C');

        $this->fpdf->SetXY(20,35);
        $this->fpdf->Cell(200,4,"PRODUCTO: ".strtoupper(utf8_decode($nombreProducto)),0,1,'L');

        $this->fpdf->Ln(5);

        // Encabezado tabla PDF
        $this->fpdf->SetFont('Helvetica','B',10);
        $h = 6;
        $enc = array(
            0 => array("DOCUMENTO",30,"C"),
            1 => array("TIPO VENTA",30,"C"),
            2 => array("FECHA Y HORA",40,"C"),
            3 => array("CANTIDAD",25,"C"),
            4 => array("PRECIO",25,"C"),
            5 => array("COSTO",25,"C"),
            6 => array("UTILIDAD",25,"C"),
        );
        $this->fpdf->LineWriteB($enc,1,$h);

        // --- DETALLE PDF ---
        $this->fpdf->SetFont('Helvetica','',10);

        $totalCan=0; $totalVen=0; $totalCos=0; $totalUtil=0;

        foreach ($tabla as $i => $row) {

            if ($i==0) continue; // saltar encabezados

            $this->fpdf->LineWriteB(array(
                1 => array($row[0],30,"L"),
                2 => array($row[1],30,"C"),
                3 => array($row[2],40,"C"),
                4 => array($row[3],25,"C"),
                5 => array("$".number_format($row[4],2),25,"R"),
                6 => array("$".number_format($row[5],2),25,"R"),
                7 => array("$".number_format($row[6],2),25,"R"),
            ),1,$h);

            $totalCan += $row[3];
            $totalVen += $row[4];
            $totalCos += $row[5];
            $totalUtil += $row[6];
        }

        // ----- TOTALES PDF -----
        $this->fpdf->SetFont('Helvetica','B',10);
        $this->fpdf->LineWriteB(array(
            1 => array("TOTALES",100,"C"),
            2 => array($totalCan,25,"C"),
            3 => array("$".number_format($totalVen,2),25,"R"),
            4 => array("$".number_format($totalCos,2),25,"R"),
            5 => array("$".number_format($totalUtil,2),25,"R"),
        ),1,$h);

        // SALIDA
        $filename = "Reporte_utilidad_".str_replace(" ","_",$periodoVigente).".pdf";
        $this->_safe_output_clean();
        $this->fpdf->Output($filename,"I");
        exit;
    }
}


								//Reporte de movimientos por insumo
								function ReporteKardex(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Kardex";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteKardex",
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteKardex",$datosVista,$extras,$titulo);
										}
									}
								}
								function VerReporteKardex() {
	if($this->input->method(TRUE) == "GET"){
		$fechaInicio = $this->input->get("fechaInicio");
		$fechaInicio = ($fechaInicio != "") ? $fechaInicio : "0000-00-00";
		$fechaFinal = $this->input->get("fechaFinal");
		$fechaFinal = ($fechaFinal != "") ? $fechaFinal : "0000-00-00";
		$idInsumo= $this->input->get("idInsumo");

		// Valores por defecto
		$nombreInsumo = '';
		$categoriaInsumo = '';
		$unidadMedidaInsumo = 1;
		$nombrePresentacion = '';
		$unidadPresentacion = '';
		$costoPromedio = 0;
		$fechaRegistro = "";

		$joinCat = array(
			array(
				"tabla" => "insumoCategoria",
				"condicion" =>"insumo.idCategoriaInsumo = insumoCategoria.idInsumoCategoria",
				"tipo" => "left",
				"campos" => "nombreInsumoCategoria",
			),
			array(
				"tabla" => "insumoPresentacion AS pre",
				"condicion" =>"insumo.idInsumo = pre.idInsumo",
				"tipo" => "left",
				"campos" => "unidadInsumoPresentacion, pre.idPresentacion,pre.costoInsumoPresentacion",
			),
			array(
				"tabla" => "insumoCosto AS cos",
				"condicion" =>"insumo.idInsumo = cos.idInsumo AND cos.estadoInsumoCosto != 'Borrado' AND CAST(cos.fechaRegistroInsumoCosto AS DATE) >='$fechaInicio' AND CAST(cos.fechaRegistroInsumoCosto AS DATE) <='$fechaFinal'",
				"tipo" => "left",
				"campos" => "MAX(fechaRegistroInsumoCosto) AS fecha",
			),
			array(
				"tabla" => "presentacion",
				"condicion" =>"presentacion.idPresentacion = pre.idPresentacion",
				"tipo" => "left",
				"campos" => "nombrePresentacion, unidadPresentacion",
			)
		);

		$condicionInsumo = array(
			"insumo.idInsumo" => $idInsumo,
			"pre.unidadInventarioInsumoPresentacion" => "1",
			"pre.estadoInsumoPresentacion" => "Activo",
		);

		// Datos Generales del Insumo (puede devolver 0 si no existe)
		$insumo = TraerUnDatoJoin("insumo",$condicionInsumo,$joinCat);
		if($insumo && $insumo !== 0){
			$nombreInsumo = $insumo->nombreInsumo ?? '';
			$categoriaInsumo = $insumo->nombreInsumoCategoria ?? '';
			$unidadMedidaInsumo = floatval($insumo->unidadInsumoPresentacion ?? 1);
			$nombrePresentacion = $insumo->nombrePresentacion ?? '';
			$unidadPresentacion = $insumo->unidadPresentacion ?? '';
			$costoPromedio = floatval($insumo->costoPromedioInsumo ?? 0) * $unidadMedidaInsumo;
			$fechaRegistro = $insumo->fecha ?? "";
		}

		// Movimientos join
		$joinFac = array(
			array(
				"tabla" => "insumoMovimientoDetalle as movDet",
				"condicion" =>"insumoMovimiento.idInsumoMovimiento = movDet.idInsumoMovimiento",
				"tipo" => "left",
				"campos" => "CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) as fecha, SUM(movDet.cantidadInsumoMovimientoDetalle) AS cantidad, movDet.idPedidoMovimientoDetalle, movDet.costoInsumoMovimientoDetalle AS subCosto, insumoMovimiento.categoriaInsumoMovimiento, insumoMovimiento.descripcionInsumoMovimiento, insumoMovimiento.tipoDocumentoInsumoMovimiento, insumoMovimiento.numeroDocumentoInsumoMovimiento, insumoMovimiento.idProveedorInsumoMovimiento, insumoMovimiento.idSucursalInsumoMovimiento, insumoMovimiento.fechaHoraInsumoMovimiento"
			),
			array(
				"tabla" => "insumoPresentacion as pre",
				"condicion" =>"(movDet.idPresentacionInsumoMovimientoDetalle = pre.idPresentacion AND movDet.idInsumo = pre.idInsumo)",
				"tipo" => "left",
				"campos" => "pre.unidadInsumoPresentacion AS unidad"
			),
			array(
				"tabla" => "sucursal",
				"condicion" =>"insumoMovimiento.idSucursalInsumoMovimiento = sucursal.idSucursal",
				"tipo" => "left",
				"campos" => "sucursal.nombreSucursal"
			),
			array(
				"tabla" => "proveedor",
				"condicion" =>"insumoMovimiento.idProveedorInsumoMovimiento = proveedor.idProveedor",
				"tipo" => "left",
				"campos" => "proveedor.nombreProveedor"
			),
		);

		$condicionFacDet = array(
			"CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) >=" => $fechaInicio,
			"CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) <=" => $fechaFinal,
			"movDet.idInsumo" => $idInsumo,
			"insumoMovimiento.idSucursalInsumoMovimiento" => $this->session->idSucursal,
			"insumoMovimiento.estadoInsumoMovimiento"=>"Activo",
			"pre.estadoInsumoPresentacion" => "Activo"
		);

		// lista de movimientos
		$movimientos = TraerDatosJoin("insumoMovimiento",$condicionFacDet,"insumoMovimiento.fechaHoraInsumoMovimiento ASC",$joinFac,"insumoMovimiento.idInsumoMovimiento");

		// Cargas/Descargas totales en periodo
		$joinCarga = array(
			array(
				"tabla" => "insumoMovimientoDetalle as movDet",
				"condicion" =>"insumoMovimiento.idInsumoMovimiento = movDet.idInsumoMovimiento",
				"tipo" => "left",
				"campos" => "SUM(movDet.cantidadInsumoMovimientoDetalle * pre.unidadInsumoPresentacion) AS cantidad"
			),
			array(
				"tabla" => "insumoPresentacion as pre",
				"condicion" =>"(movDet.idPresentacionInsumoMovimientoDetalle = pre.idPresentacion AND movDet.idInsumo = pre.idInsumo)",
				"tipo" => "left",
				"campos" => "pre.unidadInsumoPresentacion AS unidad"
			),
		);

		$condicionCarga =  array(
			"insumoMovimiento.categoriaInsumoMovimiento"=>"Carga",
			"CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) >=" => $fechaInicio,
			"insumoMovimiento.estadoInsumoMovimiento"=>"Activo",
			"pre.estadoInsumoPresentacion"=>"Activo",
			"movDet.idInsumo"=>$idInsumo,
		);
		$condicionDescarga =  array(
			"insumoMovimiento.categoriaInsumoMovimiento"=>"Descarga",
			"CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) >=" => $fechaInicio,
			"insumoMovimiento.estadoInsumoMovimiento"=>"Activo",
			"pre.estadoInsumoPresentacion"=>"Activo",
			"movDet.idInsumo"=>$idInsumo,
		);

		$totalStock = TraerUnDato("insumoStock",array("idInsumo"=>$idInsumo));
		$totalCargas = TraerUnDatoJoin("insumoMovimiento",$condicionCarga,$joinCarga);
		$totalDescargas = TraerUnDatoJoin("insumoMovimiento",$condicionDescarga,$joinCarga);

		// normalizar fuentes para evitar foreach inválidos
		$movimientos = $this->_normalize_result($movimientos);
		$totalStock = ($totalStock === 0 || $totalStock === false || $totalStock === null) ? null : $totalStock;
		$totalCargas = ($totalCargas === 0 || $totalCargas === false || $totalCargas === null) ? null : $totalCargas;
		$totalDescargas = ($totalDescargas === 0 || $totalDescargas === false || $totalDescargas === null) ? null : $totalDescargas;

		// periodo legible
		$periodoVigente = "";
		if ($fechaInicio && $fechaFinal && strpos($fechaInicio,'-') !== false && strpos($fechaFinal,'-') !== false) {
			list($a,$m,$d) = explode("-", $fechaInicio);
			list($a1,$m1,$d1) = explode("-", $fechaFinal);
			if($a == $a1){
				if($m==$m1){
					if($d==$d1){
						$periodoVigente="$d1 DE ".meses($m)." DE $a";
					} else {
						$periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
					}
				} else {
					$periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
				}
			} else {
				$periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
			}
		}

		// ======================================================
		//   ARMAR TABLA PARA EXPORTAR A EXCEL - REPORTE KARDEX (IGUAL AL PDF)
		// ======================================================

		$tabla = [];

		// SECCIÓN 1 — STOCK ACTUAL
		$tabla[] = ["STOCK ACTUAL"];
		$tabla[] = []; // línea en blanco
		$tabla[] = [
			"ID",
			"INSUMO",
			"PRESENTACIÓN",
			"UNIDAD",
			"COSTO",
			"STOCK",
			"EXISTENCIA FINAL",
			"VALOR TOTAL"
		];

		if ($insumo && $insumo !== 0) {
			// calcular stock y valor si existe $totalStock
			$stock_val = ($totalStock && isset($totalStock->cantidadInsumoStock)) ? floatval($totalStock->cantidadInsumoStock) : 0;
			$unidad = floatval($insumo->unidadInsumoPresentacion ?? 1);
			$stock_calc = $stock_val / ($unidad > 0 ? $unidad : 1);
			$valor = $stock_calc * floatval($insumo->costoInsumoPresentacion ?? 0);

			$tabla[] = [
				Zfill($insumo->idInsumo ?? 0, 8),
				$insumo->nombreInsumo ?? '',
				$insumo->nombrePresentacion ?? '',
				$insumo->unidad ?? '',
				floatval($insumo->costoInsumoPresentacion ?? 0),
				floatval($stock_calc),
				floatval($stock_calc),
				floatval($valor)
			];
		}

		// Separador y título de movimientos
		$tabla[] = [];
		$tabla[] = ["MOVIMIENTOS POR FECHA"];
		$tabla[] = [];

		// Encabezados movimientos
		$tabla[] = [
			"FECHA",
			"DOCUMENTO",
			"TIPO",
			"ENTRADA",
			"SALIDA",
			"EXISTENCIA",
			"COSTO PROMEDIO",
			"TOTAL VALORIZADO"
		];

		// Llenar movimientos (si existen)
		$existencia_acc = ($totalStock && isset($totalStock->cantidadInsumoStock)) ? floatval($totalStock->cantidadInsumoStock) : 0;
		$car = ($totalCargas && isset($totalCargas->cantidad)) ? floatval($totalCargas->cantidad) : 0;
		$descar = ($totalDescargas && isset($totalDescargas->cantidad)) ? floatval($totalDescargas->cantidad) : 0;
		$stockActual = $existencia_acc - $car + $descar;

		if ($this->_is_iterable_var($movimientos) && count((array)$movimientos) > 0) {
			foreach ($movimientos as $fila) {
				// determinar entrada/Salida segun categoria
				$carga = 0;
				$descarga = 0;
				if (isset($fila->categoriaInsumoMovimiento) && strtolower($fila->categoriaInsumoMovimiento) == 'carga') {
					$carga = floatval($fila->cantidad ?? 0);
				} else {
					$descarga = floatval($fila->cantidad ?? 0);
				}

				// entrada/salida en unidad inventario (intentar mantener la misma lógica del PDF)
				$unidad = floatval($fila->unidad ?? 1);
				$entran = ($carga * $unidad) / ($unidadMedidaInsumo > 0 ? $unidadMedidaInsumo : 1);
				$salen = ($descarga * $unidad) / ($unidadMedidaInsumo > 0 ? $unidadMedidaInsumo : 1);

				// costo promedio aproximado (si subCosto viene agrupado)
				$costoUnitario = isset($fila->subCosto) && floatval($fila->subCosto) != 0 ? floatval($fila->subCosto) * ($unidadMedidaInsumo / max(1,$unidad)) : $costoPromedio;

				// actualizar existencia acumulada
				$stockActual = $stockActual + ($entran - $salen);

				$totalMovimiento = $stockActual * $costoPromedio;

				$tabla[] = [
					isset($fila->fecha) ? Fecha_D_M_A($fila->fecha) : '',
					(isset($fila->tipoDocumentoInsumoMovimiento) ? $fila->tipoDocumentoInsumoMovimiento.' '.Zfill($fila->numeroDocumentoInsumoMovimiento ?? '', 9) : ''),
					isset($fila->categoriaInsumoMovimiento) ? $fila->categoriaInsumoMovimiento : '',
					floatval($entran),
					floatval($salen),
					floatval($stockActual),
					floatval($costoUnitario),
					floatval($totalMovimiento)
				];
			}
		}

		// Resumen final (igual que el PDF)
		$tabla[] = [];
		$tabla[] = ["RESUMEN"];
		$tabla[] = [
			"INICIAL_UNIDADES",
			"ENTRADAS_UNIDADES",
			"SALIDAS_UNIDADES",
			"SALDOS_UNIDADES",
			"VALOR_INICIAL",
			"VALOR_ENTRADAS",
			"VALOR_SALIDAS",
			"VALOR_SALDOS",
			"REF_ULTIMO_COSTO",
			"FECHA_ULTIMO_COSTO",
			"COSTO_UNITARIO_PROMEDIO"
		];

		// valores resumen (basicos y defensivos)
		$inicial_unidades = ($totalStock && isset($totalStock->cantidadInsumoStock)) ? floatval($totalStock->cantidadInsumoStock) / max(1,$unidadMedidaInsumo) : 0;
		$entradas_unidades = $car / max(1,$unidadMedidaInsumo);
		$salidas_unidades = $descar / max(1,$unidadMedidaInsumo);
		$saldo_unidades = ($inicial_unidades + $entradas_unidades - $salidas_unidades);

		$valor_inicial = $inicial_unidades * $costoPromedio;
		$valor_entradas = $entradas_unidades * $costoPromedio;
		$valor_salidas = $salidas_unidades * $costoPromedio;
		$valor_saldos = $saldo_unidades * $costoPromedio;

		$tabla[] = [
			$inicial_unidades,
			$entradas_unidades,
			$salidas_unidades,
			$saldo_unidades,
			$valor_inicial,
			$valor_entradas,
			$valor_salidas,
			$valor_saldos,
			(isset($ultimoCosto) ? $ultimoCosto : $costoPromedio),
			(isset($fechaRegistro) ? Fecha_D_M_A($fechaRegistro) : ''),
			$costoPromedio
		];

		// Guardar tabla para Excel (aunque esté vacía guardamos encabezados)
		if (!isset($tabla) || !is_array($tabla)) $tabla = [];
		$this->session->set_userdata("tabla_reporte", $tabla);

		// ======================================================
		//                     GENERAR PDF (igual al original)
		// ======================================================

		$this->fpdf = new Pdf();

		$this->fpdf->SetMargins(10,20);
		$this->fpdf->SetTopMargin(10);
		$this->fpdf->SetLeftMargin(10);
		$this->fpdf->AliasNbPages();
		$this->fpdf->SetAutoPageBreak(true,20);
		$this->fpdf->AddPage('L','LEGAL',0);
		$setX = 5;
		$setY = 5;
		$path = GblTraerConfiguracion("logoEmpresa");
		$this->fpdf->Image($path,$setX,$setY,70,50);

		// Títulos y encabezados (mantengo tu diseño)
		$this->fpdf->SetX(0);
		$this->fpdf->SetXY(305,0);
		$this->fpdf->SetFont('Arial','I',8);
		$this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
		$this->fpdf->SetXY(125,35);
		$this->fpdf->SetFont('Helvetica','B', 18);
		$this->fpdf->Cell(30, 4,GblTraerConfiguracion("nombreEmpresa"), 0, 1, 'C');
		$this->fpdf->SetXY(125,45);
		$this->fpdf->SetFont('Helvetica','', 14);
		$this->fpdf->Cell(30, 4,"INVENTARIO", 0, 1, 'C');
		$this->fpdf->SetXY(260,45);
		$this->fpdf->Cell(30, 4,"NIT: ".GblTraerConfiguracion("nitPatrono"), 0, 1, 'L');
		$this->fpdf->SetXY(260,55);
		$this->fpdf->Cell(30, 4,"NRC: ".GblTraerConfiguracion("nrcPatrono"), 0, 1, 'L');
		$this->fpdf->SetFont('Helvetica','', 12);
		$this->fpdf->SetXY(125,50);
		$this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');
		$this->fpdf->SetFont('Helvetica','', 10);
		$this->fpdf->SetXY(125,55);
		$this->fpdf->Cell(30, 4,"VALORES EXPRESADOS EN: DOLARES DE LOS ESTADOS UNIDOS DE AMERICA", 0, 1, 'C');

		$this->fpdf->SetFont('Arial','B',16);
		$this->fpdf->SetXY(20,65);
		$this->fpdf->Cell(30,4,"PRODUCTO: ".strtoupper($nombreInsumo), 0, 1, 'L');

		$this->fpdf->SetFont('Arial','',12);
		$this->fpdf->SetXY(28,71);
		$this->fpdf->Cell(30,4,"CATEGORIA: ".strtoupper($categoriaInsumo), 0, 1, 'L');

		$this->fpdf->SetFont('Arial','',12);
		$this->fpdf->SetXY(210,71);
		$this->fpdf->Cell(30,4,"TIPO COSTO: PROMEDIO", 0, 1, 'L');
		$this->fpdf->SetXY(270,71);
		$this->fpdf->Cell(30,4,"UNIDAD: ".$nombrePresentacion." (".$unidadPresentacion.")", 0, 1, 'L');

		// Salto de línea
		$this->fpdf->Ln(5);

		// Encabezados tabla PDF (mantengo las posiciones y tamaños originales)
		$set_y = 85;
		$set_x = 10;
		$h = 6;

		$this->fpdf->SetFont('Arial','B',8);
		$this->fpdf->SetXY($set_x, $set_y);
		$this->fpdf->Cell(10,10,"CORR.",1,1,'C',0);
		$this->fpdf->SetXY($set_x+10, $set_y);
		$this->fpdf->Cell(30,10,"NUM. DOC",1,1,'C',0);
		$this->fpdf->SetXY($set_x+40, $set_y);
		$this->fpdf->Cell(25,10,"FECHA",1,1,'C',0);
		$this->fpdf->SetXY($set_x+65, $set_y);
		$this->fpdf->Cell(20,10,"SUCURSAL",1,1,'C',0);
		$this->fpdf->SetXY($set_x+85, $set_y);
		$this->fpdf->Cell(90,5,"CONCEPTO DE LA OPERACION","LTR",1,'C',0);
		$this->fpdf->SetXY($set_x+85, $set_y+5);
		$this->fpdf->Cell(90,5,"PROVEEDOR/NACIONAL            REFC","LRB",1,'L',0);
		$this->fpdf->SetXY($set_x+175, $set_y);
		$this->fpdf->Cell(40,5,"UNIDAD","LTR",1,'C',0);
		$this->fpdf->SetXY($set_x+175, $set_y+5);
		$this->fpdf->Cell(20,5,"ENTRAN","LB",1,'C',0);
		$this->fpdf->SetXY($set_x+195, $set_y+5);
		$this->fpdf->Cell(20,5,"SALEN","BR",1,'C',0);
		$this->fpdf->SetXY($set_x+215, $set_y);
		$this->fpdf->Cell(25,10,"COSTO",1,1,'C',0);
		$this->fpdf->SetXY($set_x+240, $set_y);
		$this->fpdf->Cell(25,10,"VALOR",1,1,'C',0);
		$this->fpdf->SetXY($set_x+265, $set_y);
		$this->fpdf->Cell(70,5,"VALOR",1,1,'C',0);
		$this->fpdf->SetXY($set_x+265, $set_y+5);
		$this->fpdf->Cell(20,5,"UNIDADES",1,1,'C',0);
		$this->fpdf->SetXY($set_x+285, $set_y+5);
		$this->fpdf->Cell(25,5,"COSTOS",1,1,'C',0);
		$this->fpdf->SetXY($set_x+310, $set_y+5);
		$this->fpdf->Cell(25,5,"VALOR",1,1,'C',0);

		$this->fpdf->SetFont('Arial','',10);

		// Imprimir movimientos (manteniendo tu lógica)
		
		if (!$movimientos || count($movimientos) == 0) {
    log_message('error', "KARDEX: NO HAY MOVIMIENTOS PARA MOSTRAR");
    // imprime mensaje visual temporal
    $this->fpdf->SetFont('Arial', 'B', 20);
    $this->fpdf->SetXY(20, 120);
    $this->fpdf->Cell(200, 10, "NO HAY MOVIMIENTOS EN ESTE PERIODO", 0, 0, 'L');
    $this->fpdf->Output("debug.pdf", "I");
    exit;
}

		if($movimientos){
			$j = 1; //correlativo
			$stock = ($totalStock && isset($totalStock->cantidadInsumoStock)) ? $totalStock->cantidadInsumoStock : 0;
			$car = ($totalCargas && isset($totalCargas->cantidad)) ? $totalCargas->cantidad : 0;
			$descar = ($totalDescargas && isset($totalDescargas->cantidad)) ? $totalDescargas->cantidad: 0;
			$stockActual = $stock - $car + $descar;
			$total = $stockActual;

			foreach ($movimientos as $fila){
				$fechaUltimoCosto = TraerDatosRenombrados("insumoCosto",array("MAX(fechaRegistroInsumoCosto)"=>"fecha"),array("idInsumo"=>$idInsumo,"CAST(fechaRegistroInsumoCosto AS DATE) =" =>$fila->fecha));
				$totalCosto = TraerDatosRenombrados("insumoCosto",array("costoPromedioInsumoCosto"=>"costo"),array("idInsumo"=>$idInsumo,"fechaRegistroInsumoCosto" =>$fechaUltimoCosto[0]->fecha));
				$totalCosto = ($totalCosto) ? TraerDatosRenombrados("insumoCosto",array("costoPromedioInsumoCosto"=>"costo"),array("idInsumo"=>$idInsumo,"fechaRegistroInsumoCosto" =>$fechaUltimoCosto[0]->fecha))[0]->costo : 0;

				if($fila->categoriaInsumoMovimiento == "Carga"){
					$unidad = $fila->unidad;
					$ultimoCosto = $totalCosto;
					$carga = $fila->cantidad ;
					$descarga = 0;
					$descripcion = $fila->nombreProveedor." ".$fila->descripcionInsumoMovimiento;
					$total = ($total + ($carga * $unidad));
				}
				if($fila->categoriaInsumoMovimiento == "Descarga"){
					$ultimoCosto = $costoPromedio;
					$unidad = $fila->unidad;
					$carga = 0;
					$descarga = $fila->cantidad;
					$descripcion = $fila->descripcionInsumoMovimiento;
					$total = ($total - ($descarga * $unidad));
				}

				$entran = ($carga * $unidad / $unidadMedidaInsumo);
				$salen = ($descarga * $unidad / $unidadMedidaInsumo);
				$costoUnitario = ($fila->subCosto * $unidadMedidaInsumo / $unidad);
				$costoTotal = ($entran + $salen) * $costoUnitario;
				$totalExistencia = ($total / $unidadMedidaInsumo);
				$totalCosto = $costoPromedio;
				$totalFinal = $totalExistencia * $totalCosto;

				$array_data = array(
					0 => array($j,10,"C"),
					1 => array($fila->tipoDocumentoInsumoMovimiento." ".Zfill($fila->numeroDocumentoInsumoMovimiento,9),30,"L","B"),
					2 => array(Fecha_D_M_A($fila->fecha),25,"C"),
					3 => array($fila->nombreSucursal,20,"C"),
					4 => array($descripcion,90,"L"),
					5 => array(number_format($entran,4),20,"C"),
					6 => array(number_format($salen,4),20,"C"),
					7 => array("$".number_format($costoUnitario,4,".",","),25,"R"),
					8 => array("$".number_format($costoTotal,4,".",","),25,"R"),
					9 => array(number_format($totalExistencia,4),20,"C"),
					10 => array("$".number_format($totalCosto,4,".",","),25,"R"),
					11 => array("$".number_format($totalFinal,4,".",","),25,"R"),
				);
				$this->fpdf->LineWriteB($array_data, 1,$h);
				$j ++;
			}

			// RESUMEN (PDF)
			$this->fpdf->Ln();
			$h = 8;
			$set_y = $this->fpdf->GetY();
			$set_x = $this->fpdf->GetX() + 30;

			$this->fpdf->SetXY($set_x, $set_y);
			$this->fpdf->SetFont('Arial', 'B', 14);
			$this->fpdf->Cell(210,8," RESUMEN:",1,1,'L',0);
			$this->fpdf->SetFont('Arial', '', 12);
			$this->fpdf->SetXY($set_x + 30, $set_y);
			$this->fpdf->Cell(210,8,strtoupper($nombreInsumo),0,1,'L',0);
			$this->fpdf->SetFont('Arial', 'B', 12);
			$this->fpdf->SetXY($set_x, $set_y+8);
			$this->fpdf->Cell(50,6,"INICIAL",1,1,'C',0);
			$this->fpdf->SetXY($set_x +50, $set_y+8);
			$this->fpdf->Cell(55,6,"ENTRADAS",1,1,'C',0);
			$this->fpdf->SetXY($set_x +105, $set_y+8);
			$this->fpdf->Cell(55,6,"SALIDAS",1,1,'C',0);
			$this->fpdf->SetXY($set_x +160, $set_y+8);
			$this->fpdf->Cell(50,6,"SALDOS",1,1,'C',0);

			$this->fpdf->SetFont('Arial', 'B', 12);
			$this->fpdf->SetXY($set_x-30, $set_y+14);
			$this->fpdf->Cell(30,6,"UNIDADES",1,1,'L',0);
			$this->fpdf->SetXY($set_x-30, $set_y+20);
			$this->fpdf->Cell(30,6,"VALORES",1,1,'L',0);

			$this->fpdf->SetFont('Arial', '', 12);
			$this->fpdf->SetXY($set_x, $set_y+14);
			$this->fpdf->Cell(50,6,number_format(($stockActual / $unidadMedidaInsumo),4),1,1,'C',0);
			$this->fpdf->SetXY($set_x+50, $set_y+14);
			$this->fpdf->Cell(55,6,number_format(($car  / $unidadMedidaInsumo),4),1,1,'C',0);
			$this->fpdf->SetXY($set_x+105, $set_y+14);
			$this->fpdf->Cell(55,6,number_format(($descar  / $unidadMedidaInsumo),4),1,1,'C',0);
			$this->fpdf->SetXY($set_x+160, $set_y+14);
			$this->fpdf->Cell(50,6,number_format(($total / $unidadMedidaInsumo),4),1,1,'C',0);

			$this->fpdf->SetXY($set_x, $set_y+20);
			$this->fpdf->Cell(50,6,"$".number_format(($stockActual * $costoPromedio / $unidadMedidaInsumo),4),1,1,'C',0);
			$this->fpdf->SetXY($set_x+50, $set_y+20);
			$this->fpdf->Cell(55,6,"$".number_format(($car *  $costoPromedio / $unidadMedidaInsumo),4),1,1,'C',0);
			$this->fpdf->SetXY($set_x+105, $set_y+20);
			$this->fpdf->Cell(55,6,"$".number_format(($descar * $costoPromedio/ $unidadMedidaInsumo ),4),1,1,'C',0);
			$this->fpdf->SetXY($set_x+160, $set_y+20);
			$this->fpdf->Cell(50,6,"$".number_format(($total * $costoPromedio / $unidadMedidaInsumo),4),1,1,'C',0);

			$this->fpdf->SetXY($set_x+90, $set_y+30);
			$this->fpdf->Cell(30,6,"VALORES DE COSTOS NO INCLUYEN IMPUESTOS",0,1,'C',0);

			$this->fpdf->SetXY($set_x+220, $set_y+10);
			$this->fpdf->Cell(30,6,"Ref. Ultimo Costo: $".number_format($ultimoCosto ?? $costoPromedio,4),0,1,'L',0);
			$this->fpdf->SetXY($set_x+220, $set_y+15);
			$this->fpdf->Cell(30,6,"Fecha: ".(isset($fechaRegistro) ? Fecha_D_M_A($fechaRegistro) : ''),0,1,'L',0);

			$this->fpdf->SetFont('Arial', '', 20);
			$this->fpdf->SetXY($set_x+150, $set_y+45);
			$this->fpdf->Cell(30,6,"Costo Unitario Promedio: $".number_format($costoPromedio,4),0,1,'L',0);
		}

		$filename = "Reporte_de_kardex_".str_replace(' ','_',$periodoVigente).".pdf";
		// limpiar buffers antes de enviar el PDF
		$this->_safe_output_clean();

		try {
			$this->fpdf->Output($filename, "I");
		} catch (Exception $e) {
			log_message('error', "FPDF Output error: " . $e->getMessage());
			show_error("Error generando PDF. Revisa logs.");
		}
		exit;
	}
}
																	

								//Reporte de Valoracion de inventario
								function ReporteInventario(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Inventario";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteInventario",
												"insumoCategoria" => TraerDatos('insumoCategoria', array("idSucursalInsumoCategoria" => $this->session->idSucursal, "estadoInsumoCategoria !=" => "Borrado")),
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteInventario",$datosVista,$extras,$titulo);
										}
									}
								}
								
								public function VerReporteInventario() {
    if ($this->input->method(TRUE) != "GET") {
        show_error('Método no permitido', 405);
        return;
    }

    // --------------------------------------------------
    // Captura filtros del GET
    // --------------------------------------------------
    $fechaInicio     = $this->input->get("fechaInicio");
    $fechaFinal      = $this->input->get("fechaFinal");
    $categoriaInsumo = $this->input->get("categoriaInsumos");
    $tipoDescarga    = $this->input->get("tipo") ?? "pdf"; // pdf o excel

    // --------------------------------------------------
    // Formatear periodo
    // --------------------------------------------------
    $periodoVigente = "";
    list($a,$m,$d)   = explode("-", $fechaInicio);
    list($a1,$m1,$d1) = explode("-", $fechaFinal);

    if ($a == $a1) {
        if ($m==$m1) {
            if ($d==$d1) {
                $periodoVigente = "$d1 DE ".meses($m)." DE $a";
            } else {
                $periodoVigente = "DEL $d AL $d1 DE ".meses($m)." DE $a";
            }
        } else {
            $periodoVigente = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
        }
    } else {
        $periodoVigente = "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
    }

    // --------------------------------------------------
    // Configurar joins y condiciones
    // --------------------------------------------------
    $joinInsumo = [
        ["tabla"=>"insumoPresentacion as pre","condicion"=>"insumo.idInsumo = pre.idInsumo","tipo"=>"left","campos"=>"pre.unidadInsumoPresentacion AS unidad"],
        ["tabla"=>"insumoStock as stock","condicion"=>"insumo.idInsumo = stock.idInsumo","tipo"=>"left","campos"=>"stock.cantidadInsumoStock AS stock"],
        ["tabla"=>"presentacion","condicion"=>"pre.idPresentacion = presentacion.idPresentacion","tipo"=>"left","campos"=>"presentacion.nombrePresentacion"]
    ];

    $condicionInsumo = [
        "pre.unidadInventarioInsumoPresentacion" => "1",
        "insumo.estadoInsumo" => "Activo",
        "pre.estadoInsumoPresentacion" => "Activo"
    ];

    $categoria = 'GENERAL';
    if($categoriaInsumo != "T"){
        $condicionInsumo["insumo.idCategoriaInsumo"] = $categoriaInsumo;
        $catObj = TraerUnDato("insumoCategoria",["idInsumoCategoria"=>$categoriaInsumo]);
        $categoria = $catObj->nombreCategoria ?? "GENERAL";
    }

    $insumos = TraerDatosJoin("insumo",$condicionInsumo,"",$joinInsumo);

    // --------------------------------------------------
    // Preparar tabla base para Excel
    // --------------------------------------------------
    $tabla = [];
    $tabla[] = ["ID","PRODUCTO / INSUMO","PRESENTACIÓN","CATEGORÍA","COSTO","PRECIO","STOCK","VALOR TOTAL"];

    // --------------------------------------------------
    // Configurar PDF
    // --------------------------------------------------
    $this->fpdf = new Pdf();
    $this->fpdf->SetMargins(10,20);
    $this->fpdf->SetAutoPageBreak(true,20);
    $this->fpdf->AliasNbPages();
    $this->fpdf->AddPage('L','LEGAL',0);

    // Logo y encabezados
    $setX=5;$setY=5;
    $this->fpdf->Image(GblTraerConfiguracion("logoEmpresa"),$setX,$setY,70,50);
    $this->fpdf->SetFont('Helvetica','B',18);
    $this->fpdf->SetXY(125,35);
    $this->fpdf->Cell(30,4,GblTraerConfiguracion("nombreEmpresa"),0,1,'C');
    $this->fpdf->SetFont('Helvetica','',14);
    $this->fpdf->SetXY(125,45);
    $this->fpdf->Cell(30,4,"INVENTARIO",0,1,'C');
    $this->fpdf->SetFont('Helvetica','',12);
    $this->fpdf->SetXY(125,50);
    $this->fpdf->Cell(30,4,"PERIODO: ".$periodoVigente,0,1,'C');
    $this->fpdf->SetFont('Helvetica','B',12);
    $this->fpdf->SetXY(20,60);
    $this->fpdf->Cell(30,4,"CATEGORÍA: ".$categoria,0,1,'C');

    // --------------------------------------------------
    // Iterar insumos
    // --------------------------------------------------
    if($insumos){
        $totalCantidad = $totalCosto = $totalEntra = $totalSale = $totalGeneral = $totalFinal = 0;
        foreach($insumos as $fila){
            // costos inicial/final
            $condCosto = ["insumoCosto.idInsumo"=>$fila->idInsumo,
                "CAST(insumoCosto.fechaRegistroInsumoCosto AS DATE) >="=>$fechaInicio,
                "CAST(insumoCosto.fechaRegistroInsumoCosto AS DATE) <="=>$fechaFinal];

            $fechaIni = TraerDatosRenombrados("insumoCosto",["MIN(fechaRegistroInsumoCosto)"=>"fecha"],$condCosto);
            $costoFechaInicio = $fechaIni[0]->fecha ? TraerUnDato("insumoCosto",["fechaRegistroInsumoCosto"=>$fechaIni[0]->fecha,"idInsumo"=>$fila->idInsumo])->costoPromedioInsumoCosto : 0;

            $fechaFin = TraerDatosRenombrados("insumoCosto",["MAX(fechaRegistroInsumoCosto)"=>"fecha"],$condCosto);
            $costoFechaFinal = $fechaFin[0]->fecha ? TraerUnDato("insumoCosto",["fechaRegistroInsumoCosto"=>$fechaFin[0]->fecha,"idInsumo"=>$fila->idInsumo])->costoPromedioInsumoCosto : 0;

            // movimientos
            $joinMov = [
                ["tabla"=>"insumoMovimientoDetalle as movDet","condicion"=>"insumoMovimiento.idInsumoMovimiento = movDet.idInsumoMovimiento","tipo"=>"left","campos"=>"SUM(movDet.cantidadInsumoMovimientoDetalle * pre.unidadInsumoPresentacion) AS cantidad"],
                ["tabla"=>"insumoPresentacion as pre","condicion"=>"(movDet.idPresentacionInsumoMovimientoDetalle = pre.idPresentacion AND movDet.idInsumo = pre.idInsumo)","tipo"=>"left","campos"=>""]
            ];

            $condMovCarga = ["CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) >="=>$fechaInicio,
                "CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) <="=>$fechaFinal,
                "movDet.idInsumo"=>$fila->idInsumo,
                "insumoMovimiento.estadoInsumoMovimiento"=>"Activo",
                "pre.estadoInsumoPresentacion"=>"Activo",
                "insumoMovimiento.categoriaInsumoMovimiento"=>"Carga"];

            $condMovDescarga = ["CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) >="=>$fechaInicio,
                "CAST(insumoMovimiento.fechaHoraInsumoMovimiento AS DATE) <="=>$fechaFinal,
                "movDet.idInsumo"=>$fila->idInsumo,
                "insumoMovimiento.estadoInsumoMovimiento"=>"Activo",
                "pre.estadoInsumoPresentacion"=>"Activo",
                "insumoMovimiento.categoriaInsumoMovimiento"=>"Descarga"];

            $movCarga = TraerUnDatoJoin("insumoMovimiento",$condMovCarga,$joinMov);
            $movDescarga = TraerUnDatoJoin("insumoMovimiento",$condMovDescarga,$joinMov);

            // calculos
            $stock = $fila->stock / $fila->unidad;
            $costo = $costoFechaFinal * $fila->unidad;
            $total = $stock * $costo;

            $existenciaAnterior = ($fila->stock + ($movDescarga->cantidad ?? 0) - ($movCarga->cantidad ?? 0))/$fila->unidad;
            $cantidadEntra = ($movCarga->cantidad ?? 0)/$fila->unidad;
            $cantidadSale  = ($movDescarga->cantidad ?? 0)/$fila->unidad;
            $existenciaFinal = $existenciaAnterior + $cantidadEntra - $cantidadSale;

            $precioUnitario = property_exists($fila,'precio') ? floatval($fila->precio) : 0;

            // tabla Excel
            $tabla[] = [
                Zfill($fila->idInsumo,8),
                $fila->nombreInsumo,
                $fila->nombrePresentacion ?? "",
                $fila->nombreCategoria ?? "",
                floatval($costoFechaInicio * $fila->unidad),
                $precioUnitario,
                $existenciaFinal,
                $total
            ];

            // PDF
            $array_data = [
                0 => [Zfill($fila->idInsumo,8),25,"C"],
                1 => [$fila->nombreInsumo." (".$fila->nombrePresentacion.")",100,"L"],
                2 => [number_format($existenciaAnterior,4),30,"C"],
                3 => ["$".number_format($costoFechaInicio * $fila->unidad,4),30,"R"],
                4 => [number_format($cantidadEntra,4),30,"C"],
                5 => [number_format($cantidadSale,4),30,"C"],
                6 => [number_format($existenciaFinal,4),30,"C"],
                7 => ["$".number_format($costo,4),30,"R"],
                8 => ["$".number_format($total,4),30,"R"]
            ];
            $this->fpdf->LineWriteB($array_data,1,6);

            // totales
            $totalCantidad += $existenciaAnterior;
            $totalCosto += $costoFechaInicio * $fila->unidad;
            $totalEntra += $cantidadEntra;
            $totalSale += $cantidadSale;
            $totalGeneral += $existenciaFinal;
            $totalFinal += $total;
        }
    }

    // Guardar tabla en sesión
    $this->session->set_userdata("tabla_reporte",$tabla);

    // --------------------------------------------------
    // Salida
    // --------------------------------------------------
    ob_clean();
    $this->_safe_output_clean();

    if($tipoDescarga == "excel"){
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $hoja = $objPHPExcel->getActiveSheet();
        $hoja->setTitle('Inventario');

        $col = 'A';
        foreach($tabla[0] as $encabezado){
            $hoja->setCellValue($col.'1',$encabezado);
            $col++;
        }

        $fila = 2;
        for($i=1;$i<count($tabla);$i++){
            $col='A';
            foreach($tabla[$i] as $dato){
                $hoja->setCellValueExplicit($col.$fila,strval($dato),PHPExcel_Cell_DataType::TYPE_STRING);
                $col++;
            }
            $fila++;
        }

        foreach(range('A',$col) as $columnID){
            $hoja->getColumnDimension($columnID)->setAutoSize(true);
        }

        $archivo = "ReporteInventario_".str_replace(' ','_',$periodoVigente).".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$archivo.'"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        $objWriter->save('php://output');
        exit;
    } else {
        // PDF
        $filename = "Reporte_de_inventario_".str_replace(' ','_',$periodoVigente).".pdf";
        $this->fpdf->Output($filename,"I");
        exit;
    }
}


								
// ==========================================================
//   MOSTRAR FORMULARIO REPORTE GENERAL UTILIDAD
// ==========================================================
function ReporteGeneralUtilidad(){
    if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
        GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
        return;
    }

    if($this->input->method(TRUE) == "GET"){
        $titulo = "Reporte General de Ventas";

        $datosVista = array(
            "titulo" => $titulo,
            "icono" => "fa fa-file-pdf",
            "controlador" => "Reportes",
            "proceso" => "ReporteGeneralUtilidad",
        );

        $extras = array(
            'css' => array(),
            'js' => array("scripts/reportes.js"),
        );

        GblPlantilla("reportes/reporteGeneralUtilidad",$datosVista,$extras,$titulo);
    }
}



// ==========================================================
//   VER REPORTE GENERAL UTILIDAD (PDF + GUARDA TABLA EXCEL)
// ==========================================================
function VerReporteGeneralUtilidad(){

    if($this->input->method(TRUE) != "GET") return;

    $fechaInicio = $this->input->get("fechaInicio");
    $fechaFinal  = $this->input->get("fechaFinal");

    // ======================================================
    //   ARMAR PERIODO
    // ======================================================
    $periodoVigente = "";
    list($a,$m,$d)   = explode("-", $fechaInicio);
    list($a1,$m1,$d1)= explode("-", $fechaFinal);

    if($a == $a1){
        if($m==$m1){
            if($d==$d1){
                $periodoVigente="$d1 DE ".meses($m)." DE $a";
            } else {
                $periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
            }
        } else {
            $periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
        }
    } else {
        $periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
    }



    // ======================================================
    //   OBTENER PRODUCTOS VENDIDOS (ORIGINAL)
    // ======================================================
    $join = array(
        array(
            "tabla" => "pedido",
            "condicion" =>"pedido.idPedido = factura.idReferenciaFactura",
        ),
        array(
            "tabla" => "pedidoDetalle",
            "condicion" =>"pedido.idPedido = pedidoDetalle.idPedido",
            "campos" => "pedidoDetalle.idPedidoDetalle, pedidoDetalle.idProductoPedidoDetalle AS id, SUM(pedidoDetalle.cantidadPedidoDetalle) AS cantidad, SUM(pedidoDetalle.precioPedidoDetalle * pedidoDetalle.cantidadPedidoDetalle) AS precio"
        ),
        array(
            "tabla" => "producto",
            "condicion" =>"pedidoDetalle.idProductoPedidoDetalle = producto.idProducto",
            "campos" => "producto.nombreProducto, producto.idProducto"
        ),
    );

    $condicion = array(
        "CAST(CONCAT(fechaFactura,' ',horaFactura) AS DATETIME) >=" => $fechaInicio." 00:00:00",
        "CAST(CONCAT(fechaFactura,' ',horaFactura) AS DATETIME) <=" => $fechaFinal." 23:59:59",
        "tipoFactura"   => "Producto",
        "estadoFactura" => "Cobrado",
        "estadoPedidoDetalle!=" => "Borrado",
        "idSucursalFactura"     => $this->session->idSucursal,
    );

    $productos = TraerDatosJoin("factura", $condicion, "cantidad DESC", $join, "id");



    // ======================================================
    //   GUARDAR TABLA PARA EXCEL (ADMIN / CONTADOR)
    // ======================================================
    $tabla = [];

    // Encabezado
    $tabla[] = [ GblTraerConfiguracion("nombreEmpresa") ];
    $tabla[] = [ "REPORTE GENERAL DE UTILIDAD" ];
    $tabla[] = [ "PERIODO: ".$periodoVigente ];
    $tabla[] = [ "EXPRESADO EN DÓLARES" ];
    $tabla[] = [ "NIT: ".GblTraerConfiguracion("nitPatrono") ];
    $tabla[] = [ "NRC: ".GblTraerConfiguracion("nrcPatrono") ];
    $tabla[] = [];

    // Encabezados tabla
    $tabla[] = [
        "ID",
        "PRODUCTO",
        "CANTIDAD",
        "PRECIO",
        "UTILIDAD (aprox.)"
    ];

    if($productos){
        foreach($productos as $p){
            $utilidad = $p->precio; // sin costo exacto (modelo original no lo provee)
            $tabla[] = [
                $p->id,
                $p->nombreProducto,
                $p->cantidad,
                $p->precio,
                $utilidad
            ];
        }
    }

    // Guardarlo para Excel
    $this->session->set_userdata("tabla_reporte", $tabla);



    // ======================================================
    //   GENERAR PDF (ORIGINAL + CORREGIDO)
    // ======================================================
    $this->fpdf = new Pdf();

    $this->fpdf->SetMargins(10,20);
    $this->fpdf->SetTopMargin(10);
    $this->fpdf->SetLeftMargin(10);
    $this->fpdf->AliasNbPages();
    $this->fpdf->SetAutoPageBreak(true,20);

    $this->fpdf->AddPage('P','LETTER',0);

    $setX = 5;
    $setY = 5;
    $h = 5;

    $path = GblTraerConfiguracion("logoEmpresa");
    $this->fpdf->Image($path,$setX,$setY,40,30);


    // Encabezado
    $this->fpdf->SetXY(165,0);
    $this->fpdf->SetFont('Arial','I',8);
    $this->fpdf->Cell(40, 10, utf8_decode('Fecha impresión: '.date('d-m-Y')), 0, 0, 'R');

    $this->fpdf->SetXY(95,30);
    $this->fpdf->SetFont('Helvetica','B', 14);
    $this->fpdf->Cell(30, 4,GblTraerConfiguracion("nombreEmpresa"), 0, 1, 'C');

    $this->fpdf->SetXY(95,40);
    $this->fpdf->SetFont('Helvetica','', 12);
    $this->fpdf->Cell(30, 4,"REPORTE DE ESTADO GENERAL", 0, 1, 'C');

    $this->fpdf->SetXY(95,45);
    $this->fpdf->SetFont('Helvetica','', 10);
    $this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

    $this->fpdf->Ln(5);


    // Tabla PDF
    $this->fpdf->SetFont('Arial','B',10);
    $this->fpdf->Cell(15,$h,"ID",1,0,'C');
    $this->fpdf->Cell(90,$h,"PRODUCTO",1,0,'C');
    $this->fpdf->Cell(25,$h,"CANT.",1,0,'C');
    $this->fpdf->Cell(35,$h,"PRECIO",1,0,'C');
    $this->fpdf->Cell(35,$h,"UTILIDAD",1,1,'C');

    $this->fpdf->SetFont('Arial','',9);

    if($productos){
        foreach($productos as $p){
            $util = $p->precio; // sin costo exacto (no disponible)

            $this->fpdf->Cell(15,$h,$p->id,1,0,'C');
            $this->fpdf->Cell(90,$h,utf8_decode($p->nombreProducto),1,0,'L');
            $this->fpdf->Cell(25,$h,number_format($p->cantidad,2),1,0,'C');
            $this->fpdf->Cell(35,$h,number_format($p->precio,2),1,0,'R');
            $this->fpdf->Cell(35,$h,number_format($util,2),1,1,'R');
        }
    }


    // OUTPUT
    $filename = "Reporte_General_Utilidad_".str_replace(' ','_',$periodoVigente).".pdf";

    ob_clean();
    $this->fpdf->Output($filename, "I");
    exit;
}

						

								
								

function ReporteDetallePedido(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte de Detalles en Cuentas";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteDetallePedido",
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteDetallePedido",$datosVista,$extras,$titulo);
										}
									}
								}

/* ===============================================================
   REPORTE DETALLE PEDIDO – PDF + SET DE DATOS PARA EXCEL
   =============================================================== */
public function VerReporteDetallePedido()
{
    if ($this->input->method(TRUE) == "GET") {

        $fechaInicio = $this->input->get("fechaInicio");
        $fechaFinal  = $this->input->get("fechaFinal");
        $sucursal    = $this->session->idSucursal;

        /* ======================================================
           JOIN
        ====================================================== */
        $join = [
            [
                "tabla"     => "usuario",
                "condicion" => "usuario.idUsuario = pedidoDetalle.idUsuarioPedidoDetalle",
                "tipo"      => "left",
                "campos"    => "nombreUsuario, 
                                IF(regaliaPedidoDetalle = 1 AND estadoPedidoDetalle = 'Activo','Si','') AS regalia"
            ],
            [
                "tabla"     => "producto",
                "condicion" => "producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
                "tipo"      => "left",
                "campos"    => "nombreProducto, 
                                IF(estadoPedidoDetalle = 'Borrado','Si','') AS borrado"
            ],
            [
                "tabla"     => "pedido",
                "condicion" => "pedidoDetalle.idPedido = pedido.idPedido",
                "tipo"      => "left",
                "campos"    => "tipoCuentaPedido, fechaPedido, horaPedido, motivoPedidoDetalle"
            ]
        ];

        /* ======================================================
           REGALÍAS
        ====================================================== */
        $condRegalias = [
            "fechaPedido >="         => $fechaInicio,
            "fechaPedido <="         => $fechaFinal,
            "estadoPedido !="        => "Anulado",
            "estadoPedidoDetalle !=" => "Borrado",
            "regaliaPedidoDetalle"   => "1",
            "idSucursalPedido"       => $sucursal
        ];

        $regalias = TraerDatosJoin("pedidoDetalle", $condRegalias, "fechaPedido DESC, horaPedido DESC", $join);

        /* ======================================================
           BORRADOS
        ====================================================== */
        $condBorrados = [
            "fechaPedido >="         => $fechaInicio,
            "fechaPedido <="         => $fechaFinal,
            "estadoPedido !="        => "Anulado",
            "estadoPedidoDetalle"    => "Borrado",
            "idSucursalPedido"       => $sucursal
        ];

        $borrados = TraerDatosJoin("pedidoDetalle", $condBorrados, "fechaPedido DESC, horaPedido DESC", $join);

        /* ======================================================
           PERIODO
        ====================================================== */
        list($a,$m,$d)   = explode("-", $fechaInicio);
        list($a1,$m1,$d1)= explode("-", $fechaFinal);

        if ($a == $a1) {
            if ($m == $m1) {
                $periodo = ($d == $d1)
                    ? "$d DE ".meses($m)." DE $a"
                    : "DEL $d AL $d1 DE ".meses($m)." DE $a";
            } else {
                $periodo = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        } else {
            $periodo = "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }

        /* ======================================================
   Lista final que se usará para PDF y Excel
   ====================================================== */

// Asegurar que siempre sean arrays
$regalias = is_array($regalias) ? $regalias : [];
$borrados = is_array($borrados) ? $borrados : [];

$fuentes = array_merge($regalias, $borrados);

// Construir tabla reporte
$tabla = [];
$tabla[] = [
    "TIPO",
    "FECHA",
    "USUARIO",
    "PRODUCTO",
    "CANTIDAD",
    "PRECIO",
    "REGALIA",
    "BORRADO",
    "MOTIVO"
];

foreach ($fuentes as $f) {
    $fecha = Fecha_D_M_A($f->fechaPedido)." ".Hora($f->horaPedido);

    $tabla[] = [
        strtoupper($f->tipoCuentaPedido),
        $fecha,
        $f->nombreUsuario,
        $f->nombreProducto,
        floatval($f->cantidadPedidoDetalle),
        number_format($f->precioPedidoDetalle, 2),
        $f->regalia,
        $f->borrado,
        $f->motivoPedidoDetalle
    ];
}

// Guardar en sesión
$this->session->set_userdata("tabla_reporte", $tabla);


        /* ======================================================
           PDF
        ====================================================== */
        $this->fpdf = new Pdf();
        $this->fpdf->SetMargins(10,20);
        $this->fpdf->SetTopMargin(10);
        $this->fpdf->SetLeftMargin(10);
        $this->fpdf->AliasNbPages();
        $this->fpdf->SetAutoPageBreak(true,20);
        $this->fpdf->AddPage('L','LEGAL',0);

        // Logo
        $path = GblTraerConfiguracion("logoEmpresa");
        $this->fpdf->Image($path, 10, 5, 30, 25);

        // Fecha impresión
        $this->fpdf->SetXY(235,0);
        $this->fpdf->SetFont('Arial','I',8);
        $this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');

        // Título
        $this->fpdf->SetXY(95,15);
        $this->fpdf->SetFont('Helvetica','', 14);
        $this->fpdf->Cell(30, 4,"REPORTE DE DETALLES EN ORDENES", 0, 1, 'C');

        // Período
        $this->fpdf->SetFont('Helvetica','', 10);
        $this->fpdf->SetXY(95,20);
        $this->fpdf->Cell(30, 4,"PERIODO: ".$periodo, 0, 1, 'C');

        $this->fpdf->Ln(12);

        // Encabezado tabla
        $this->fpdf->SetFont('Helvetica', 'B', 10);
        $h = 6;

        $cab = [
            1 => ["TIPO", 15, "C"],
            2 => ["FECHA Y HORA", 40, "C"],
            3 => ["USUARIO", 30, "C"],
            4 => ["PRODUCTO", 85, "C"],
            5 => ["CANT.", 20, "C"],
            6 => ["PRECIO", 25, "C"],
            7 => ["REG", 15, "C"],
            8 => ["BORR", 15, "C"],
            9 => ["MOTIVO", 90, "C"],
        ];
        $this->fpdf->LineWriteB($cab, 1, $h);

        // Filas PDF
        $this->fpdf->SetFont('Helvetica','',10);

        foreach ($fuentes as $f) {
            $fecha = Fecha_D_M_A($f->fechaPedido)." ".Hora($f->horaPedido);

            $row = [
                1 => [strtoupper($f->tipoCuentaPedido),15,"C"],
                2 => [$fecha,40,"C"],
                3 => [$f->nombreUsuario,30,"C"],
                4 => [$f->nombreProducto,85,"L"],
                5 => [$f->cantidadPedidoDetalle,20,"C"],
                6 => ["$".number_format($f->precioPedidoDetalle,2),25,"R"],
                7 => [$f->regalia,15,"C"],
                8 => [$f->borrado,15,"C"],
                9 => [$f->motivoPedidoDetalle,90,"L"],
            ];

            $this->fpdf->LineWriteB($row, 1, $h);
        }

        $filename = "Reporte_detalle_pedidos_" . str_replace(" ", "_", $periodo) . ".pdf";

        $this->_safe_output_clean();
        $this->fpdf->Output($filename, "I");
        exit;
    }
}


							

								//Reporte Advalorem
								function ReporteAdvalorem(){
									if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
										GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
									} else {
										if($this->input->method(TRUE) == "GET"){
											$titulo = "Reporte Advalorem";
											$datosVista = array(
												"titulo" => $titulo,
												"icono" => "fa fa-file-pdf",
												"controlador" => "Reportes",
												"proceso" => "ReporteAdvalorem",
											);
											$extras = array(
												'css' => array(),
												'js' => array(
													"scripts/reportes.js"
												),
											);
											GblPlantilla("reportes/reporteAdvalorem",$datosVista,$extras,$titulo);
										}
									}
								}
								function VerReporteAdvalorem() {
									if($this->input->method(TRUE) == "GET"){
										$fechaInicio = $this->input->get("fechaInicio");
										$fechaFinal = $this->input->get("fechaFinal");
										$sucursal = $this->session->idSucursal;
										$joinFac = array(
											array(
												"tabla" => "insumoMovimiento",
												"condicion" =>"insumoMovimiento.idInsumoMovimiento = insumoMovimientoDetalle.idInsumoMovimiento",
												"tipo" => "left",
												"campos" => "SUM(cantidadInsumoMovimientoDetalle) AS cantidad",
											),
											array(
												"tabla" => "producto",
												"condicion" =>"producto.idProducto = insumoMovimientoDetalle.idProductoInsumo",
												"tipo" => "left",
												"campos" => "nombreProducto",
											),
											array(
												"tabla" => "pedidoDetalle",
												"condicion" =>"pedidoDetalle.idPedidoDetalle = insumoMovimientoDetalle.idPedidoMovimientoDetalle",
												"tipo" => "left",
												"campos" => "precioPedidoDetalle ",
											),
											array(
												"tabla" => "insumo",
												"condicion" =>"insumo.idInsumo = insumoMovimientoDetalle.idInsumo",
												"tipo" => "left",
												"campos" => "insumo.idInsumo ,nombreInsumo, AdvaloremTipoInsumo ,montoSugeridoInsumo",
											),
											array(
												"tabla" => "insumoPresentacion",
												"condicion" =>"insumoPresentacion.idInsumo = insumoMovimientoDetalle.idInsumo AND insumoPresentacion.idPresentacion = insumoMovimientoDetalle.idPresentacionInsumoMovimientoDetalle",
												"tipo" => "left",
												"campos" => "unidadInsumoPresentacion",
											),
										);
										$condicionFacDet = array(
											"advaloremInsumo" => 1,
											"CAST(fechaHoraInsumoMovimiento AS DATE) >=" => $fechaInicio,
											"CAST(fechaHoraInsumoMovimiento AS DATE) <=" => $fechaFinal,
											"estadoInsumoPresentacion" => "Activo",
											"idSucursalInsumoMovimiento" => $sucursal
										);

										$facturaAdvalorem = TraerDatosJoin("insumoMovimientoDetalle",$condicionFacDet,"insumo.idInsumo ASC",$joinFac,"insumo.idInsumo, pedidoDetalle.precioPedidoDetalle");

										$periodoVigente = "";
										list($a,$m,$d) = explode("-", $fechaInicio);
										list($a1,$m1,$d1) = explode("-", $fechaFinal);
										if($a == $a1){
											if($m==$m1){
												if($d==$d1){
													$periodoVigente="$d1 DE ".meses($m)." DE $a";
												} else {
													$periodoVigente="DEL $d AL $d1 DE ".meses($m)." DE $a";
												}
											} else {
												$periodoVigente="DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
											}
										} else {
											$periodoVigente="DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
										}
										
										// ======================================================
//   ARMAR TABLA PARA EXPORTAR A EXCEL - ADVALOREM
// ======================================================

$tabla = [];  

// Encabezados (coherentes con tu PDF)
$tabla[] = [
    "PRODUCTO",
    "CANTIDAD",
    "PRECIO UNITARIO",
    "SUBTOTAL",
    "PORCENTAJE ADVALOREM",
    "MONTO ADVALOREM",
    "TOTAL FINAL",
    "FECHA"
];

// normalizar la fuente de datos (ejemplo $datosVista)
$fuente = $this->_normalize_result($datosVista ?? null);

if (!$this->_is_iterable_var($fuente) || count((array)$fuente) === 0) {
    log_message('error', "VerReporteX: fuente de datos no iterable o vacía (X = nombreReporte)");
    // Para que el PDF no rompa, definir fuente como array vacío
    $fuente = [];
}

    foreach ($datosVista as $fila) {

        // Valores base
        $cantidad   = floatval($fila->cantidad ?? 0);
        $precio     = floatval($fila->precio ?? $fila->precioUnidad ?? 0);
        $porcAdv    = floatval($fila->porcentaje ?? $fila->porcentajeAdvalorem ?? 0);

        // Cálculos
        $subtotal   = $cantidad * $precio;
        $montoAdv   = ($subtotal * $porcAdv) / 100;
        $totalFinal = $subtotal + $montoAdv;

        $tabla[] = [
            $fila->producto ?? $fila->nombreProducto ?? "",
            $cantidad,
            $precio,
            $subtotal,
            $porcAdv,
            $montoAdv,
            $totalFinal,
            $fila->fecha ?? $fila->fechaFactura ?? ""
        ];
    }
}

// Guardar tabla para Excel (aunque esté vacía guardamos encabezados)
if (!isset($tabla) || !is_array($tabla)) $tabla = [];
$this->session->set_userdata("tabla_reporte", $tabla);


										//ob_start();
										$this->fpdf = new Pdf();

										$this->fpdf->SetMargins(10,20);
										$this->fpdf->SetTopMargin(10);
										$this->fpdf->SetLeftMargin(10);
										//Numeracion de paginas
										$this->fpdf->AliasNbPages();
										//Salto automatico de pagina margen de 20 mm
										$this->fpdf->SetAutoPageBreak(true,20);
										//Agrega la pagina a trabajar
										$this->fpdf->AddPage('P','LEGAL',0);
										$setX = 5;
										$setY = 5;
										$path = GblTraerConfiguracion("logoEmpresa");
										$this->fpdf->Image($path,$setX,$setY,25,15);

										// Movernos a la derecha
										// Título
										$this->fpdf->SetX(0);
										$this->fpdf->SetXY(170,0);
										$this->fpdf->SetFont('Arial','I',8);
										$this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
										$this->fpdf->SetXY(90,5);
										$this->fpdf->SetFont('Helvetica','B', 12);
										$this->fpdf->Cell(30, 4,GblTraerConfiguracion("nombreEmpresa"), 0, 1, 'C');
										$this->fpdf->SetXY(90,10);
										$this->fpdf->SetFont('Helvetica','', 10);
										$this->fpdf->Cell(30, 4,"IMPUESTO ADVALOREM", 0, 1, 'C');
										$this->fpdf->SetFont('Helvetica','', 8);
										$this->fpdf->SetXY(90,15);
										$this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');
										$this->fpdf->SetFont('Helvetica','', 8);
										$this->fpdf->SetXY(90,20);
										$this->fpdf->Cell(30, 4,"VALORES EXPRESADOS EN: DOLARES DE LOS ESTADOS UNIDOS DE AMERICA", 0, 1, 'C');


										// Salto de línea
										$this->fpdf->Ln();
										//LISTA VARIABLES PARA ALMACENAR TOTAL
										$n = 1;

										$total = 0;
										$setY=25;
										$setX=25;
										$h = 4;
										$this->fpdf->SetFont('Helvetica', 'B', 8);
										$array_data = array(
											1 => array("CODIGO",20,"C"),
											2 => array("PRODUCTO",60,"C"),
											3 => array("%IMP",15,"C"),
											4 => array("CANTIDAD",20,"C"),
											5 => array("SUGERIDO",20,"C"),
											6 => array("PUBLICO",20,"C"),
											7 => array("DIFER(-IVA)",20,"C"),
											8 => array("CONTRIBUCION",25,"C"),
										);
										$this->fpdf->LineWriteB($array_data, 1,$h);
										$this->fpdf->SetFont('Helvetica', '', 8);
										if($facturaAdvalorem !==0){
											$totalVen = 0;
											$totalCos = 0;
											$totalUtil = 0;
											$porcentajeTabaco = GblTraerConfiguracion("impuestoAdvaloremTabaco");
											$porcentajeAlcohol = GblTraerConfiguracion("impuestoAdvaloremAlcohol");
											$iva = GblTraerConfiguracion("iva");
											$totalContribucion = 0;
											foreach ($facturaAdvalorem as $fila)
											{
												//$documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
												$idInsumo = Zfill($fila->idInsumo,10);
												$nombreInsumo = $fila->nombreInsumo;
												$porcentaje = ($fila->AdvaloremTipoInsumo == "Tabaco") ? $porcentajeTabaco : $porcentajeAlcohol;
												$producto = $fila->nombreProducto;
												$cantidad = $fila->cantidad;
												$precio = $fila->precioPedidoDetalle;
												$montoSugerido = $fila->montoSugeridoInsumo;
												$diferencia = ( $precio - $montoSugerido ) /  ( (100 + $iva) / 100 );
												$contribucion = ($porcentaje / 100) * $cantidad * $diferencia;
												$totalContribucion += $contribucion;
												$array_data = array(
													1 => array($idInsumo,20,"C"),
													2 => array(utf8_decode($nombreInsumo),60,"C"),
													3 => array($porcentaje,15,"C"),
													4 => array(number_format($cantidad,4,".",","),20,"C"),
													5 => array(number_format($montoSugerido,4,".",","),20,"R"),
													6 => array(number_format($precio,4,".",","),20,"R"),
													7 => array(number_format($diferencia,4,".",","),20,"R"),
													8 => array(number_format($contribucion,4,".",","),25,"R"),
												);
												$this->fpdf->LineWriteB($array_data, 1,$h);
											}
											$h = 6;
											$this->fpdf->SetFont('Helvetica', '', 10);
											$array_data = array(
												1 => array("TOTAL IMPUESTO DEL PERIODO",175,"C","B"),
												2 => array(number_format($totalContribucion,4,".",","),25,"R","B"),
											);
											$this->fpdf->LineWriteB($array_data, 1,$h);
										}
										$this->fpdf->Ln();

										$filename = "Reporte_Advalorem_".str_replace(' ','_',$periodoVigente).".pdf";
										ob_clean();
										// antes de enviar el PDF, limpiamos buffers que puedan tener warnings o salida accidental
$this->_safe_output_clean();

try {
    $this->fpdf->Output($filename, "I");
} catch (Exception $e) {
    log_message('error', "FPDF Output error: " . $e->getMessage());
    show_error("Error generando PDF. Revisa logs.");
}
exit;

										//$this->imprimirReporteComision($fechaInicio,$fechaFinal);
									}
								

								function VerReporteVentaItem() {

    if ($this->input->method(TRUE) == "GET") {

        $fechaInicio = $this->input->get("fechaInicio");
        $fechaFinal  = $this->input->get("fechaFinal");

        // PERIODO (comentarios y espacio para futuras opciones)
        list($a,$m,$d) = explode("-", $fechaInicio);
        list($a1,$m1,$d1)= explode("-", $fechaFinal);
        if($a == $a1){
            if($m==$m1){
                $periodoVigente = ($d==$d1) ? "$d1 DE ".meses($m)." DE $a" : "DEL $d AL $d1 DE ".meses($m)." DE $a";
            } else {
                $periodoVigente = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
            }
        } else {
            $periodoVigente = "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
        }

        // ======================================================
        //   OBTENER DATOS PARA PDF Y PARA EXCEL
        // ======================================================
        $join = array(
            array(
                "tabla"     => "pedido",
                "condicion" => "pedido.idPedido = factura.idReferenciaFactura",
            ),
            array(
                "tabla"     => "pedidoDetalle",
                "condicion" => "pedido.idPedido = pedidoDetalle.idPedido",
                "campos"    => "pedidoDetalle.idProductoPedidoDetalle AS id,
                                SUM(pedidoDetalle.cantidadPedidoDetalle) AS cantidad,
                                pedidoDetalle.precioPedidoDetalle AS precio,
                                pedidoDetalle.regaliaPedidoDetalle"
            ),
            array(
                "tabla"     => "producto",
                "condicion" => "pedidoDetalle.idProductoPedidoDetalle = producto.idProducto",
                "campos"    => "producto.nombreProducto"
            ),
        );

        $condicion = array(
            "factura.fechaFactura >=" => $fechaInicio,
            "factura.fechaFactura <=" => $fechaFinal,
            "factura.estadoFactura"    => "Cobrado",
            "factura.tipoFactura"      => "Producto",
            "estadoPedidoDetalle !="   => "Borrado"
        );

        $productos = TraerDatosJoin(
            "factura",
            $condicion,
            "cantidad DESC",
            $join,
            "id,regaliaPedidoDetalle"
        );

        // NORMALIZAR la fuente para evitar foreach inválidos
        $productos = $this->_normalize_result($productos);

        // ======================================================
        //   ARMAR TABLA PARA EXPORTAR A EXCEL (MISMA DATA QUE PDF)
        // ======================================================
        $tabla = [];
        $tabla[] = ["No.", "PRODUCTO", "CANTIDAD", "PRECIO", "SUBTOTAL"];

        if (!empty($productos)) {
            $n = 1;
            foreach ($productos as $p) {
                $precio = ($p->regaliaPedidoDetalle == "1") ? 0.00 : floatval($p->precio);
                $subtotal = floatval($p->cantidad) * $precio;
                $tabla[] = [
                    $n,
                    $p->nombreProducto,
                    $p->cantidad,
                    number_format($precio, 2, '.', ''),    // formato para Excel amigable
                    number_format($subtotal, 2, '.', '')
                ];
                $n++;
            }
        }

        // Guardar tabla para Excel — clave esperada por ExcelVenta()
        $this->session->set_userdata("tabla_reporte", $tabla);

        // DEBUG TEMPORAL (borra en producción)
        log_message('debug', 'VerReporteVentaItem: tabla_reporte count=' . count($tabla));
        if (count($tabla) > 1) {
            log_message('debug', 'VerReporteVentaItem sample row=' . json_encode($tabla[1]));
        }

        // ======================================================
        //   GENERAR PDF (igual que antes) - limpieza y salida
        // ======================================================
        $this->fpdf = new Pdf();
        $this->fpdf->SetMargins(25,20);
        $this->fpdf->AliasNbPages();
        $this->fpdf->SetAutoPageBreak(true,15);
        $this->fpdf->AddPage('P','LETTER',0);
        $setX = 10;
        $setY = 5;
        $path = GblTraerConfiguracion("logoEmpresa");
        $this->fpdf->Image($path,$setX,$setY,30,25);

        $this->fpdf->SetX(0);
        $this->fpdf->SetXY(235,0);
        $this->fpdf->SetFont('Arial','I',8);
        $this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
        $this->fpdf->SetXY(95,15);
        $this->fpdf->SetFont('Helvetica','', 14);
        $this->fpdf->Cell(30, 4,"REPORTE DE PRODUCTOS VENDIDOS", 0, 1, 'C');
        $this->fpdf->SetFont('Helvetica','', 10);
        $this->fpdf->SetXY(95,20);
        $this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

        $this->fpdf->Ln(15);
        $this->fpdf->SetFont('Helvetica', '', 10);
        $this->fpdf->Ln(5);

        $array_data = array(
            0 => array("No.",10,"C","B"),
            1 => array("Nombre Producto",125,"C","B"),
            2 => array("Cantidad",25,"C","B")
        );

        $h = 5;
        $this->fpdf->LineWriteB($array_data, 1,$h);

        if (!empty($productos)) {
            $nua = 1;
            foreach($productos as $p){
                $array_data = array(
                    0 => array($nua,10,"L"),
                    1 => array(utf8_decode(mb_strtoupper($p->nombreProducto)),125,"L"),
                    2 => array($p->cantidad,25,"C")
                );
                $this->fpdf->LineWriteB($array_data, 1,$h);
                $nua++;
            }
        }

        $filename = "Reporte_ventas_item_".$fechaInicio."_".$fechaFinal.".pdf";
        $this->_safe_output_clean();
        $this->fpdf->Output($filename, "I");
        exit;
    }
}

								
								function ReporteRevisionInsumo($idCorte) {
									if($this->input->method(TRUE) == "GET"){
										$join = array(
											array(
												"tabla" => "caja",
												"condicion" =>"corteCaja.idCaja = caja.idCaja",
												"tipo" => "inner",
												"campos" => "caja.nombreCaja"
											),
											array(
												"tabla" => "usuario",
												"condicion" =>"corteCaja.idUsuarioCorte = usuario.idUsuario",
												"tipo" => "inner",
												"campos" => "usuario.nombreUsuario"
											),
										);
										$idCorte = $idCorte;
										$condicion = array("md5(idCorteCaja)" => $idCorte );
										$corte = TraerUnDatoJoin("corteCaja",$condicion,$join);

										$fechaApertura = $corte->fechaCorte;
										$horaApertura = $corte->horaCorte;
										$fechaCierre = explode(" ",$corte->fechaHoraCorteCierre)[0];
										$horaCierre = explode(" ",$corte->fechaHoraCorteCierre)[1];
										$revisionInsumo = $corte->revisionInsumo;

										$joinTurno = array(
											array(
												"tabla" => "usuario",
												"condicion" =>"corteTurno.idUsuarioCorteTurno = usuario.idUsuario",
												"tipo" => "left",
												"campos" => "usuario.nombreUsuario"
											),
										);
										$condicionTurno = array("md5(idCorte)" => $idCorte );
										$turnos = TraerDatosJoin("corteTurno",$condicionTurno,"",$joinTurno);


										$periodoVigente = PeriodoFechas($fechaApertura,$fechaCierre);

										//ob_start();
										$this->fpdf = new Pdf();

										$this->fpdf->SetMargins(10,20);
										$this->fpdf->SetTopMargin(10);
										$this->fpdf->SetLeftMargin(10);
										//Numeracion de paginas
										$this->fpdf->AliasNbPages();
										//Salto automatico de pagina margen de 20 mm
										$this->fpdf->SetAutoPageBreak(true,20);
										//Agrega la pagina a trabajar
										$this->fpdf->AddPage('P','LETTER',0);
										$setX = 10;
										$setY = 5;
										$path = GblTraerConfiguracion("logoEmpresa");

										// $path = base_url("vendors/core/img/dms.png");
										$this->fpdf->Image($path,$setX,$setY,30,25);

										// Movernos a la derecha
										// Título
										$this->fpdf->SetX(0);
										$this->fpdf->SetXY(235,0);
										$this->fpdf->SetFont('Arial','I',8);
										$this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
										$this->fpdf->SetXY(95,15);
										$this->fpdf->SetFont('Helvetica','', 14);
										$this->fpdf->Cell(30, 4,"REPORTE RESUMEN DE CORTE DE CAJA", 0, 1, 'C');
										$this->fpdf->SetFont('Helvetica','', 10);
										$this->fpdf->SetXY(95,20);
										$this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

										// Salto de línea
										$this->fpdf->Ln(12);
										//LISTA VARIABLES PARA ALMACENAR TOTAL
										$n = 1;


										$this->fpdf->SetFont('Helvetica', '', 10);
										$total = 0;
										$setY=30;
										$setX=25;
										$h = 5;

										if($turnos){
											$general = 0;
											foreach($turnos as $t){
												$fechaAperturaTurno = explode(" ",$t->fechaCorteTurno)[0];
												$horaAperturaTurno = $t->horaCorteTurno;
												$fechaCierreTurno = explode(" ",$t->fechaHoraCierreCorteTurno)[0];
												$horaCierreTurno = explode(" ",$t->fechaHoraCierreCorteTurno)[1];
												$turno = $t->corteTurno;
												$apertura = $t->montoTurnoCorteCaja;
												$cierre = $t->montoTurnoCierreCorteCaja;
												$nombreCaja = $corte->nombreCaja;

												$corteHistoriaDocumento = TraerUnDatoIndividual("corteHistorialDocumento","MAX(idCorteHistorial) AS idHistorial",array("idTurno"=> $t->idTurno));
												$idHistorial = ($corteHistoriaDocumento) ? $corteHistoriaDocumento[0]["idHistorial"] : 0;

												$movimientos = TraerDatos("cajaMovimiento",array("idTurnoCajaMovimiento"=>$t->idTurno));
												$propinadet = TraerDatosRenombrados("factura",array("SUM(propinaFactura)" => "total"),array("md5(idCorte)"=>$idCorte,'estadoFactura' => 'Cobrado'),"","");
												$documentos = TraerDatosRenombrados("corteHistorialDocumento",array("tipoCorteDocumento"=>"tipo","inicioDocumento"=>"inicio","finDocumento"=>"fin","totalNumeroDocumento"=>"cantidad","totalDocumento"=>"total"),array("idTurno" => $t->idTurno,"idCorteHistorial"=>$idHistorial),"","tipoCorteDocumento");
												$historial = TraerDatosRenombrados("corteHistorialDocumento",array("SUM(totalDocumento)"=>"total"),array("idTurno" => $t->idTurno,"idCorteHistorial"=>$idHistorial),"","");
												$descuentos = TraerDatosRenombrados("corteHistorialDocumento",array("SUM(totalDescuentoDocumento)"=>"total"),array("idTurno" => $t->idTurno,"idCorteHistorial"=>$idHistorial),"","");
												$entradas = TraerDatosRenombrados("cajaMovimiento",array("SUM(montoCajaMovimiento)"=>"total"),array("idTurnoCajaMovimiento" => $t->idTurno,"tipoCajaMovimiento"=>"Entrada"),"","idTurno");
												$salidas = TraerDatosRenombrados("cajaMovimiento",array("SUM(montoCajaMovimiento)"=>"total"),array("idTurnoCajaMovimiento" => $t->idTurno,"tipoCajaMovimiento"=>"Salida"),"","idTurno");
												$propinadet = ($propinadet) ? $propinadet[0]->total : "0";
												$historial = ($historial) ? $historial[0]->total : "0";
												$descuentos = ($descuentos) ? $descuentos[0]->total : "0";
												$entradas = ($entradas) ? $entradas[0]->total : "0";
												$salidas = ($salidas) ? $salidas[0]->total : "0";

												$this->fpdf->SetFont('Helvetica', '', 12);
												$array_data = array(0 => array("Turno ".$turno,195,"C","B"));
												$this->fpdf->SetFont('Helvetica', '', 10);

												$this->fpdf->LineWriteB($array_data, 0,$h);

												$array_data = array(0 => array("Resumen General",195,"C","B"));
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$periodo = PeriodoFechasHora($fechaAperturaTurno." ".$horaAperturaTurno,$fechaCierreTurno." ".$horaCierreTurno);
												$array_data = array(0 => array("Fecha",30,"L","B"),1 => array(Minuscula($periodo),165,"C"));
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$array_data = array(
													0 => array("Caja",30,"L","B"),
													1 => array(utf8_decode($nombreCaja),40,"C"),
													2 => array("Empleado",30,"L","B"),
													3 => array(utf8_decode($t->nombreUsuario),95,"C"),

												);
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$array_data = array(
													0 => array("Apertura",30,"L","B"),
													1 => array("$".number_format($apertura,2,".",","),70,"C"),
													2 => array("Facturado",30,"L","B"),
													3 => array("$".number_format($historial,2,".",","),65,"C"),
												);
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$array_data = array(
													4 => array("Entradas",30,"L","B"),
													5 => array("$".number_format($entradas,2,".",","),70,"C"),
													6 => array("Salidas",30,"L","B"),
													7 => array("$".number_format($salidas,2,".",","),65,"C"),
												);
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$total = $historial + $entradas - $salidas + $apertura - $descuentos;
												$general += $total;

												$array_data = array(0 => array("Descuentos",100,"C","B"),1 => array("$".number_format($descuentos,2,".",","),95,"C"));
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$array_data = array(0 => array("Total",100,"C","B"),1 => array("$".number_format($total,2,".",","),95,"C"));
												$this->fpdf->LineWriteB($array_data, 1,$h);


												$array_data = array(0 => array("Recibido",100,"C","B"),1 => array("$".number_format($cierre,2,".",","),95,"C"));
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$array_data = array(0 => array("Diferencia",100,"C","B"),1 => array("$".number_format($total - $cierre,2,".",","),95,"C"));
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$array_data = array(0 => array("Propina",100,"C","B"),1 => array("$".number_format($propinadet,2,".",","),95,"C"));
												$this->fpdf->LineWriteB($array_data, 1,$h);
												$this->fpdf->Ln();

												if($documentos){
													$array_data = array(0 => array("Resumen de Documentos",195,"C","B"));
													$this->fpdf->LineWriteB($array_data, 1,$h);

													$array_data = array(
														0 => array("Documento",95,"C","B"),
														1 => array("Inicia",25,"C","B"),
														2 => array("Termina",25,"C","B"),
														3 => array("Cantidad",25,"C","B"),
														4 => array("Total",25,"C","B"),
													);
													$this->fpdf->LineWriteB($array_data, 1,$h);

													foreach($documentos as $d){
														$array_data = array(
															0 => array($d->tipo,95,"C"),
															1 => array($d->inicio,25,"C"),
															2 => array($d->fin,25,"C"),
															3 => array($d->cantidad,25,"C"),
															4 => array("$".$d->total,25,"R"),
														);
														$this->fpdf->LineWriteB($array_data, 1,$h);
													}
												}
												if($movimientos){
													$array_data = array(0 => array("Movimientos de Caja",195,"C","B"));
													$this->fpdf->LineWriteB($array_data, 1,$h);

													$array_data = array(
														0 => array("Tipo",25,"C","B"),
														1 => array("Recibe",45,"C","B"),
														2 => array("Entrega",45,"C","B"),
														3 => array("Motivo",55,"C","B"),
														4 => array("Total",25,"C","B"),
													);
													$this->fpdf->LineWriteB($array_data, 1,$h);
													foreach($movimientos as $m){
														$array_data = array(
															0 => array($m->tipoCajaMovimiento,25,"C"),
															1 => array(utf8_decode($m->recibeCajaMovimiento),45,"C"),
															2 => array(utf8_decode($m->entregaCajaMovimiento),45,"C"),
															3 => array(utf8_decode($m->conceptoCajaMovimiento),55,"C"),
															4 => array("$".$m->montoCajaMovimiento,25,"R"),
														);
														$this->fpdf->LineWriteB($array_data, 1,$h);
													}
												}

												$this->fpdf->Ln(5);
											}
										}

										$this->fpdf->Ln(5);

										$this->fpdf->SetFont('Helvetica', '', 16);
										$array_data = array(0 => array(utf8_decode("Consolidado Total del Día"),145,"C"),1=>array("$".number_format($general,2,".",","),50,"C","B"));
										$this->fpdf->LineWriteB($array_data, 1,10);

										$this->fpdf->Ln(5);

										$join = array(
											array(
												"tabla" => "pedido",
												"condicion" =>"pedido.idPedido = factura.idReferenciaFactura",
											),
											array(
												"tabla" => "pedidoDetalle",
												"condicion" =>"pedido.idPedido = pedidoDetalle.idPedido",
												"tipo" => "",
												"campos" => "pedidoDetalle.idProductoPedidoDetalle AS id, SUM(pedidoDetalle.cantidadPedidoDetalle) AS cantidad, pedidoDetalle.precioPedidoDetalle AS precio,pedidoDetalle.regaliaPedidoDetalle"
												// "campos" => "pedidoDetalle.idProductoPedidoDetalle AS id, SUM(pedidoDetalle.cantidadPedidoDetalle) AS cantidad, pedidoDetalle.precioPedidoDetalle AS precio,pedidoDetalle.regaliaPedidoDetalle"
											),
											array(
												"tabla" => "producto",
												"condicion" =>"pedidoDetalle.idProductoPedidoDetalle = producto.idProducto",
												"tipo" => "",
												"campos" => "producto.nombreProducto"
											),
										);
										$condicion = array(
											"md5(factura.idCorte)" => $idCorte,
											"factura.estadoFactura" => "Cobrado",
											"factura.tipoFactura" => "Producto",
											"pedidoDetalle.estadoPedidoDetalle IN ('Finalizado','Activo')",
										);
										// $condicion = array(
										// 		"md5(factura.idCorte)" => $idCorte,
										// 		"CAST(CONCAT(fechaFactura,' ',horaFactura) AS DATETIME) >=" => $fechaApertura." ".$horaApertura,
										// 		"CAST(CONCAT(fechaFactura,' ',horaFactura) AS DATETIME)<=" => $fechaCierre." ".$horaCierre,
										// 		"tipoFactura" => "Producto",
										// 		"pedidoDetalle.estadoPedidoDetalle" => "Activo",
										// );
										$productos = TraerDatosJoin("factura" ,$condicion,"cantidad DESC",$join,"id,regaliaPedidoDetalle");
										if($productos){
											$this->fpdf->SetFont('Helvetica', '', 12);
											$array_data = array(0 => array("Conteo de Productos Vendidos",195,"C","B"));
											$this->fpdf->LineWriteB($array_data, 0,$h);
											$this->fpdf->SetFont('Helvetica', '', 10);
											$this->fpdf->Ln(5);

											$array_data = array(
												0 => array("No.",10,"C","B"),
												1 => array("Nombre Producto",110,"C","B"),
												2 => array("Cantidad",25,"C","B"),
												3 => array("Precio",25,"C","B"),
												4 => array("Total",25,"C","B"),
											);
											$this->fpdf->LineWriteB($array_data, 1,$h);
											$totalGen = 0;
											$nua=1;
											foreach($productos as $p){
												$precio = ($p->regaliaPedidoDetalle == "1") ? "0.00" : $p->precio;
												$total = ($p->cantidad * $precio);
												$array_data = array(
													0 => array($nua,10,"L"),
													1 => array(utf8_decode(mb_strtoupper($p->nombreProducto)),110,"L"),
													2 => array($p->cantidad,25,"C"),
													3 => array("$".$p->precio,25,"R"),
													4 => array("$".number_format($total,2),25,"R"),
												);
												$this->fpdf->LineWriteB($array_data, 1,$h);
												$totalGen += $total;
												$nua++;
											}
											$array_data = array(
												0 => array("TOTAL",170,"C","B"),
												1 => array("$".number_format($totalGen,2),25,"R","B"),
											);
											$this->fpdf->LineWriteB($array_data, 1,$h);
										}

										$this->fpdf->Ln(5);

										$condicionLista = array(
											'md5(idCorte)' => $idCorte,
										);
										$join = array(
											array(
												"tabla" => "insumo",
												"condicion" => "insumo.idInsumo = corteRevisionInsumo.idInsumo",
												"tipo" => "inner",
												"campos" => "insumo.nombreInsumo",
											),
											array(
												"tabla" => "insumoCategoria",
												"condicion" => "insumo.idCategoriaInsumo = insumoCategoria.idInsumoCategoria",
												"tipo" => "inner",
												"campos" => "insumoCategoria.idInsumoCategoria AS id, insumoCategoria.nombreInsumoCategoria",
											),
										);
										$categoriaInsumo = TraerDatosJoin("corteRevisionInsumo" ,$condicionLista,"",$join,"id");

										if($categoriaInsumo){
											$this->fpdf->SetFont('Helvetica', '', 12);
											$array_data = array(0 => array("Conteo de Insumos",195,"C","B"));
											$this->fpdf->LineWriteB($array_data, 0,$h);
											$this->fpdf->SetFont('Helvetica', '', 10);

											$this->fpdf->Ln(5);
											foreach($categoriaInsumo as $l){
												$array_data = array(0 => array(utf8_decode($l->nombreInsumoCategoria),195,"C","B"));
												$this->fpdf->LineWriteB($array_data, 1,$h);
												$array_data = array(
													0 => array("Nombre",40,"L","B"),
													1 => array("Presentacion",30,"L","B"),
													2 => array("Exist. Inicio",25,"C","B"),
													3 => array("Entradas",25,"C","B"),
													4 => array("Salidas",25,"C","B"),
													5 => array("Exist. Final",25,"C","B"),
													6 => array("Ajuste",25,"C","B"),
												);
												$this->fpdf->LineWriteB($array_data, 1,$h);

												$n =1;

												$condicionLista = array(
													'md5(idCorte)' => $idCorte,
													'insumoCategoria.idInsumoCategoria' => $l->id
												);
												$lista = TraerDatosJoin("corteRevisionInsumo", $condicionLista,"",$join,"corteRevisionInsumo.idInsumo");
												foreach ($lista as $key)
												{
													$idInsumo = $key->idInsumo;
													$nombreInsumo = $key->nombreInsumo;
													$existenciaInicioInsumo = $key->existenciaInicioInsumo;
													$existenciaFinInsumo = $key->existenciaFinInsumo;
													$descripcionInsumoPresentacion = $key->descripcionInsumoPresentacion;
													$unidadInsumoPresentacion = $key->unidadInsumoPresentacion;
													$joinFacDet = array(
														array(
															"tabla" => "insumoMovimiento",
															"condicion" =>"insumoMovimientoDetalle.idInsumoMovimiento = insumoMovimiento.idInsumoMovimiento",
															"tipo" => "",
															"campos" => "SUM(insumoMovimientoDetalle.cantidadInsumoMovimientoDetalle) AS cantidad"
														),
													);
													$condicionExiste = array(
														"insumoMovimientoDetalle.idInsumo" => $idInsumo,
														"fechaHoraInsumoMovimiento >=" => $fechaApertura." ".$horaApertura,
														"fechaHoraInsumoMovimiento <=" => $fechaCierre." ".$horaCierre,
														"insumoMovimiento.categoriaInsumoMovimiento" => "Carga",
													);
													$entradaInsumo = TraerDatosJoin("insumoMovimientoDetalle", $condicionExiste,'',$joinFacDet,"insumoMovimientoDetalle.idInsumo");

													$condicionExiste = array(
														"insumoMovimientoDetalle.idInsumo" => $idInsumo,
														"fechaHoraInsumoMovimiento >=" => $fechaApertura,
														"fechaHoraInsumoMovimiento <=" => $fechaCierre,
														"insumoMovimiento.categoriaInsumoMovimiento" => "Descarga",
													);
													$salidaInsumo = TraerDatosJoin("insumoMovimientoDetalle", $condicionExiste,'',$joinFacDet,"insumoMovimientoDetalle.idInsumo");

													$entradaInsumo = ($entradaInsumo) ? $entradaInsumo[0]->cantidad : "0";
													$salidaInsumo = ($salidaInsumo) ? $salidaInsumo[0]->cantidad : "0";

													$diferencia = $existenciaInicioInsumo + ( ($entradaInsumo-$salidaInsumo) / $unidadInsumoPresentacion );
													$diferencia = number_format($diferencia,2);
													$array_data = array(
														0 => array(utf8_decode(Minuscula($nombreInsumo)),40,"L"),
														1 => array(utf8_decode(Minuscula($descripcionInsumoPresentacion)),30,"L"),
														2 => array($existenciaInicioInsumo,25,"C"),
														3 => array($entradaInsumo,25,"C"),
														4 => array($salidaInsumo,25,"C"),
														5 => array($diferencia,25,"C"),
														6 => array($existenciaFinInsumo,25,"C"),
													);
													$this->fpdf->LineWriteB($array_data, 1,$h);
													$n+=1;
												}
												$this->fpdf->Ln(5);
											}
										}

										$filename = "Reporte_Consolidado_".str_replace(' ','_',$periodoVigente).".pdf";
										ob_clean();
										// antes de enviar el PDF, limpiamos buffers que puedan tener warnings o salida accidental
$this->_safe_output_clean();

try {
    $this->fpdf->Output($filename, "I");
} catch (Exception $e) {
    log_message('error', "FPDF Output error: " . $e->getMessage());
    show_error("Error generando PDF. Revisa logs.");
}
exit;

										//$this->imprimirReporteComision($fechaInicio,$fechaFinal);
									}
								}
								
								
								// function ReporteRevisionInsumo($idCorte) {
								// 	if($this->input->method(TRUE) == "GET"){
								// 		$join = array(
								// 			array(
								// 				"tabla" => "caja",
								// 				"condicion" =>"corteCaja.idCaja = caja.idCaja",
								// 				"tipo" => "inner",
								// 				"campos" => "caja.nombreCaja"
								// 			),
								// 			array(
								// 				"tabla" => "usuario",
								// 				"condicion" =>"corteCaja.idUsuarioCorte = usuario.idUsuario",
								// 				"tipo" => "inner",
								// 				"campos" => "usuario.nombreUsuario"
								// 			),
								// 		);
								// 		$idCorte = $idCorte;
								// 		$condicion = array("md5(idCorteCaja)" => $idCorte );
								// 		$corte = TraerUnDatoJoin("corteCaja",$condicion,$join);

								// 		$fechaApertura = $corte->fechaCorte;
								// 		$horaApertura = $corte->horaCorte;
								// 		$fechaCierre = explode(" ",$corte->fechaHoraCorteCierre)[0];
								// 		$horaCierre = explode(" ",$corte->fechaHoraCorteCierre)[1];
								// 		$revisionInsumo = $corte->revisionInsumo;

								// 		$joinTurno = array(
								// 			array(
								// 				"tabla" => "usuario",
								// 				"condicion" =>"corteTurno.idUsuarioCorteTurno = usuario.idUsuario",
								// 				"tipo" => "left",
								// 				"campos" => "usuario.nombreUsuario"
								// 			),
								// 		);
								// 		$condicionTurno = array("md5(idCorte)" => $idCorte );
								// 		$turnos = TraerDatosJoin("corteTurno",$condicionTurno,"",$joinTurno,"corteTurno.idCorte");


								// 		$periodoVigente = PeriodoFechas($fechaApertura,$fechaCierre);

								// 		//ob_start();
								// 		$this->fpdf = new Pdf();

								// 		$this->fpdf->SetMargins(10,20);
								// 		$this->fpdf->SetTopMargin(10);
								// 		$this->fpdf->SetLeftMargin(10);
								// 		//Numeracion de paginas
								// 		$this->fpdf->AliasNbPages();
								// 		//Salto automatico de pagina margen de 20 mm
								// 		$this->fpdf->SetAutoPageBreak(true,20);
								// 		//Agrega la pagina a trabajar
								// 		$this->fpdf->AddPage('P','LETTER',0);
								// 		$setX = 10;
								// 		$setY = 5;
								// 		$path = base_url("vendors/core/img/dms.png");
								// 		$this->fpdf->Image($path,$setX,$setY,30,25);

								// 		// Movernos a la derecha
								// 		// Título
								// 		$this->fpdf->SetX(0);
								// 		$this->fpdf->SetXY(235,0);
								// 		$this->fpdf->SetFont('Arial','I',8);
								// 		$this->fpdf->Cell(40, 10, utf8_decode('Fecha de impresión: '.date('d-m-Y')), 0, 0, 'R');
								// 		$this->fpdf->SetXY(95,15);
								// 		$this->fpdf->SetFont('Helvetica','', 14);
								// 		$this->fpdf->Cell(30, 4,"REPORTE RESUMEN DE CORTE DE CAJA", 0, 1, 'C');
								// 		$this->fpdf->SetFont('Helvetica','', 10);
								// 		$this->fpdf->SetXY(95,20);
								// 		$this->fpdf->Cell(30, 4,"PERIODO: ".$periodoVigente, 0, 1, 'C');

								// 		// Salto de línea
								// 		$this->fpdf->Ln(12);
								// 		//LISTA VARIABLES PARA ALMACENAR TOTAL
								// 		$n = 1;


								// 		$this->fpdf->SetFont('Helvetica', '', 10);
								// 		$total = 0;
								// 		$setY=30;
								// 		$setX=25;
								// 		$h = 5;

								// 		if($turnos){
								// 			$general = 0;
								// 			foreach($turnos as $t){
								// 				$fechaAperturaTurno = explode(" ",$t->fechaCorteTurno)[0];
								// 				$horaAperturaTurno = $t->horaCorteTurno;
								// 				$fechaCierreTurno = explode(" ",$t->fechaHoraCierreCorteTurno)[0];
								// 				$horaCierreTurno = explode(" ",$t->fechaHoraCierreCorteTurno)[1];
								// 				$turno = $t->corteTurno;
								// 				$apertura = $t->montoTurnoCorteCaja;
								// 				$cierre = $t->montoTurnoCierreCorteCaja;
								// 				$nombreCaja = $corte->nombreCaja;

								// 				$corteHistoriaDocumento = TraerUnDatoIndividual("corteHistorialDocumento","MAX(idCorteHistorial) AS idHistorial",array("idTurno"=> $t->idTurno));
								// 				$idHistorial = ($corteHistoriaDocumento) ? $corteHistoriaDocumento[0]["idHistorial"] : 0;

								// 				$movimientos = TraerDatos("cajaMovimiento",array("idTurnoCajaMovimiento"=>$t->idTurno));
								// 				$documentos = TraerDatosRenombrados("corteHistorialDocumento",array("tipoCorteDocumento"=>"tipo","inicioDocumento"=>"inicio","finDocumento"=>"fin","totalNumeroDocumento"=>"cantidad","totalDocumento"=>"total"),array("idTurno" => $t->idTurno,"idCorteHistorial"=>$idHistorial),"","tipoCorteDocumento");
								// 				$historial = TraerDatosRenombrados("corteHistorialDocumento",array("SUM(totalDocumento)"=>"total"),array("idTurno" => $t->idTurno),"","idTurno");
								// 				$entradas = TraerDatosRenombrados("cajaMovimiento",array("SUM(montoCajaMovimiento)"=>"total"),array("idTurnoCajaMovimiento" => $t->idTurno,"tipoCajaMovimiento"=>"Entrada"),"","idTurno");
								// 				$salidas = TraerDatosRenombrados("cajaMovimiento",array("SUM(montoCajaMovimiento)"=>"total"),array("idTurnoCajaMovimiento" => $t->idTurno,"tipoCajaMovimiento"=>"Salida"),"","idTurno");
								// 				$historial = ($historial) ? $historial[0]->total : "0";
								// 				$entradas = ($entradas) ? $entradas[0]->total : "0";
								// 				$salidas = ($salidas) ? $salidas[0]->total : "0";

								// 				$this->fpdf->SetFont('Helvetica', '', 12);
								// 				$array_data = array(0 => array("Turno ".$turno,195,"C","B"));
								// 				$this->fpdf->SetFont('Helvetica', '', 10);

								// 				$this->fpdf->LineWriteB($array_data, 0,$h);

								// 				$array_data = array(0 => array("Resumen General",195,"C","B"));
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);

								// 				$periodo = PeriodoFechasHora($fechaAperturaTurno." ".$horaAperturaTurno,$fechaCierreTurno." ".$horaCierreTurno);
								// 				$array_data = array(0 => array("Fecha",30,"L","B"),1 => array(Minuscula($periodo),165,"C"));
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);

								// 				$array_data = array(
								// 					0 => array("Caja",30,"L","B"),
								// 					1 => array(utf8_decode($nombreCaja),35,"C"),
								// 					2 => array("Empleado",30,"L","B"),
								// 					3 => array(utf8_decode($t->nombreUsuario),30,"C"),
								// 					4 => array("Apertura",35,"L","B"),
								// 					5 => array("$".number_format($apertura,2,".",","),35,"C"),
								// 				);
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);

								// 				$array_data = array(
								// 					0 => array("Facturado",30,"L","B"),
								// 					1 => array("$".number_format($historial,2,".",","),35,"C"),
								// 					2 => array("Entradas",30,"L","B"),
								// 					3 => array("$".number_format($entradas,2,".",","),30,"C"),
								// 					4 => array("Gastos",35,"L","B"),
								// 					5 => array("$".number_format($salidas,2,".",","),35,"C"),
								// 				);
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);

								// 				$total = $historial + $entradas - $salidas;
								// 				$general += $total;
								// 				$array_data = array(0 => array("Total",30,"L","B"),1 => array("$".number_format($total,2,".",","),165,"C"));
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);

								// 				$array_data = array(0 => array("Recibido",30,"L","B"),1 => array("$".number_format($cierre,2,".",","),165,"C"));
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);

								// 				if($documentos){
								// 					$array_data = array(0 => array("Resumen de Documentos",195,"C","B"));
								// 					$this->fpdf->LineWriteB($array_data, 1,$h);

								// 					$array_data = array(
								// 						0 => array("Documento",95,"C","B"),
								// 						1 => array("Inicia",25,"C","B"),
								// 						2 => array("Termina",25,"C","B"),
								// 						3 => array("Cantidad",25,"C","B"),
								// 						4 => array("Total",25,"C","B"),
								// 					);
								// 					$this->fpdf->LineWriteB($array_data, 1,$h);

								// 					foreach($documentos as $d){
								// 						$array_data = array(
								// 							0 => array($d->tipo,95,"C"),
								// 							1 => array($d->inicio,25,"C"),
								// 							2 => array($d->fin,25,"C"),
								// 							3 => array($d->cantidad,25,"C"),
								// 							4 => array("$".$d->total,25,"R"),
								// 						);
								// 						$this->fpdf->LineWriteB($array_data, 1,$h);
								// 					}
								// 				}
								// 				if($movimientos){
								// 					$array_data = array(0 => array("Movimientos de Caja",195,"C","B"));
								// 					$this->fpdf->LineWriteB($array_data, 1,$h);

								// 					$array_data = array(
								// 						0 => array("Tipo",25,"C","B"),
								// 						1 => array("Recibe",45,"C","B"),
								// 						2 => array("Entrega",45,"C","B"),
								// 						3 => array("Motivo",55,"C","B"),
								// 						4 => array("Total",25,"C","B"),
								// 					);
								// 					$this->fpdf->LineWriteB($array_data, 1,$h);
								// 					foreach($movimientos as $m){
								// 						$array_data = array(
								// 							0 => array($m->tipoCajaMovimiento,25,"C"),
								// 							1 => array(utf8_decode($m->recibeCajaMovimiento),45,"C"),
								// 							2 => array(utf8_decode($m->entregaCajaMovimiento),45,"C"),
								// 							3 => array(utf8_decode($m->conceptoCajaMovimiento),55,"C"),
								// 							4 => array("$".$m->montoCajaMovimiento,25,"R"),
								// 						);
								// 						$this->fpdf->LineWriteB($array_data, 1,$h);
								// 					}
								// 				}

								// 				$this->fpdf->Ln(5);
								// 			}
								// 		}

								// 		$this->fpdf->Ln(5);

								// 		$this->fpdf->SetFont('Helvetica', '', 16);
								// 		$array_data = array(0 => array(utf8_decode("Consolidado Total del Día"),145,"C"),1=>array("$".number_format($general,2,".",","),50,"C","B"));
								// 		$this->fpdf->LineWriteB($array_data, 1,10);

								// 		$this->fpdf->Ln(5);

								// 		$join = array(
								// 					array(
								// 						"tabla" => "pedido",
								// 						"condicion" =>"pedido.idPedido = factura.idReferenciaFactura",
								// 					),
								// 					array(
								// 						"tabla" => "pedidoDetalle",
								// 						"condicion" =>"pedido.idPedido = pedidoDetalle.idPedido",
								// 						"tipo" => "",
								// 						"campos" => "pedidoDetalle.idProductoPedidoDetalle AS id, SUM(pedidoDetalle.cantidadPedidoDetalle) AS cantidad, SUM(pedidoDetalle.precioPedidoDetalle) AS precio"
								// 					),
								// 					array(
								// 						"tabla" => "producto",
								// 						"condicion" =>"pedidoDetalle.idProductoPedidoDetalle = producto.idProducto",
								// 						"tipo" => "",
								// 						"campos" => "producto.nombreProducto"
								// 					),
								// 				);
								// 		$condicion = array(
								// 				"md5(factura.idCorte)" => $idCorte,
								// 				"CAST(CONCAT(fechaFactura,' ',horaFactura) AS DATETIME) >=" => $fechaApertura." ".$horaApertura,
								// 				"CAST(CONCAT(fechaFactura,' ',horaFactura) AS DATETIME)<=" => $fechaCierre." ".$horaCierre,
								// 				"tipoFactura" => "Producto",
								// 		);
								// 		$productos = TraerDatosJoin("factura" ,$condicion,"",$join,"id");
								// 		if($productos){
								// 			$this->fpdf->SetFont('Helvetica', '', 12);
								// 			$array_data = array(0 => array("Conteo de Productos Vendidos",195,"C","B"));
								// 			$this->fpdf->LineWriteB($array_data, 0,$h);
								// 			$this->fpdf->SetFont('Helvetica', '', 10);
								// 			$this->fpdf->Ln(5);

								// 			$array_data = array(
								// 					0 => array("Nombre Producto",120,"C","B"),
								// 					1 => array("Cantidad",25,"C","B"),
								// 					2 => array("Precio",25,"C","B"),
								// 					3 => array("Total",25,"C","B"),
								// 				);
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);
								// 			foreach($productos as $p){
								// 				$array_data = array(
								// 					0 => array(Minuscula(utf8_decode($p->nombreProducto)),120,"C"),
								// 					2 => array($p->cantidad,25,"C"),
								// 					3 => array("$".$p->precio,25,"R"),
								// 					4 => array("$".number_format(($p->cantidad * $p->precio),2),25,"R"),
								// 				);
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);
								// 			}
								// 		}

								// 		$this->fpdf->Ln(5);

								// 		$condicionLista = array(
								// 			'md5(idCorte)' => $idCorte,
								// 		);
								// 		$join = array(
								// 			array(
								// 				"tabla" => "insumo",
								// 				"condicion" => "insumo.idInsumo = corteRevisionInsumo.idInsumo",
								// 				"tipo" => "inner",
								// 				"campos" => "insumo.nombreInsumo",
								// 			),
								// 			array(
								// 				"tabla" => "insumoCategoria",
								// 				"condicion" => "insumo.idCategoriaInsumo = insumoCategoria.idInsumoCategoria",
								// 				"tipo" => "inner",
								// 				"campos" => "insumoCategoria.idInsumoCategoria AS id, insumoCategoria.nombreInsumoCategoria",
								// 			),
								// 		);
								// 		$categoriaInsumo = TraerDatosJoin("corteRevisionInsumo" ,$condicionLista,"",$join,"id");

								// 		if($categoriaInsumo){
								// 			$this->fpdf->SetFont('Helvetica', '', 12);
								// 			$array_data = array(0 => array("Conteo de Insumos",195,"C","B"));
								// 			$this->fpdf->LineWriteB($array_data, 0,$h);
								// 			$this->fpdf->SetFont('Helvetica', '', 10);

								// 			$this->fpdf->Ln(5);
								// 			foreach($categoriaInsumo as $l){
								// 				$array_data = array(0 => array(utf8_decode($l->nombreInsumoCategoria),195,"C","B"));
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);
								// 				$array_data = array(
								// 					0 => array("Nombre",40,"L","B"),
								// 					1 => array("Presentacion",30,"L","B"),
								// 					2 => array("Exist. Inicio",25,"C","B"),
								// 					3 => array("Entradas",25,"C","B"),
								// 					4 => array("Salidas",25,"C","B"),
								// 					5 => array("Exist. Final",25,"C","B"),
								// 					6 => array("Ajuste",25,"C","B"),
								// 				);
								// 				$this->fpdf->LineWriteB($array_data, 1,$h);

								// 					$n =1;

								// 					$condicionLista = array(
								// 						'md5(idCorte)' => $idCorte,
								// 						'insumoCategoria.idInsumoCategoria' => $l->id
								// 					);
								// 					$lista = TraerDatosJoin("corteRevisionInsumo", $condicionLista,"",$join,"corteRevisionInsumo.idInsumo");
								// 					foreach ($lista as $key)
								// 					{
								// 						$idInsumo = $key->idInsumo;
								// 						$nombreInsumo = $key->nombreInsumo;
								// 						$existenciaInicioInsumo = $key->existenciaInicioInsumo;
								// 						$existenciaFinInsumo = $key->existenciaFinInsumo;
								// 						$descripcionInsumoPresentacion = $key->descripcionInsumoPresentacion;
								// 						$unidadInsumoPresentacion = $key->unidadInsumoPresentacion;
								// 						$joinFacDet = array(
								// 							array(
								// 								"tabla" => "insumoMovimiento",
								// 								"condicion" =>"insumoMovimientoDetalle.idInsumoMovimiento = insumoMovimiento.idInsumoMovimiento",
								// 								"tipo" => "",
								// 								"campos" => "SUM(insumoMovimientoDetalle.cantidadInsumoMovimientoDetalle) AS cantidad"
								// 							),
								// 						);
								// 						$condicionExiste = array(
								// 								"insumoMovimientoDetalle.idInsumo" => $idInsumo,
								// 								"fechaHoraInsumoMovimiento >=" => $fechaApertura." ".$horaApertura,
								// 								"fechaHoraInsumoMovimiento <=" => $fechaCierre." ".$horaCierre,
								// 								"insumoMovimiento.categoriaInsumoMovimiento" => "Carga",
								// 						);
								// 						$entradaInsumo = TraerDatosJoin("insumoMovimientoDetalle", $condicionExiste,'',$joinFacDet,"insumoMovimientoDetalle.idInsumo");

								// 						$condicionExiste = array(
								// 							"insumoMovimientoDetalle.idInsumo" => $idInsumo,
								// 							"fechaHoraInsumoMovimiento >=" => $fechaApertura,
								// 							"fechaHoraInsumoMovimiento <=" => $fechaCierre,
								// 							"insumoMovimiento.categoriaInsumoMovimiento" => "Descarga",
								// 						);
								// 						$salidaInsumo = TraerDatosJoin("insumoMovimientoDetalle", $condicionExiste,'',$joinFacDet,"insumoMovimientoDetalle.idInsumo");

								// 						$entradaInsumo = ($entradaInsumo) ? $entradaInsumo[0]->cantidad : "0";
								// 						$salidaInsumo = ($salidaInsumo) ? $salidaInsumo[0]->cantidad : "0";

								// 						$diferencia = $existenciaInicioInsumo + ( ($entradaInsumo-$salidaInsumo) / $unidadInsumoPresentacion );
								// 						$diferencia = number_format($diferencia,2);
								// 						$array_data = array(
								// 							0 => array(utf8_decode(Minuscula($nombreInsumo)),40,"L"),
								// 							1 => array(utf8_decode(Minuscula($descripcionInsumoPresentacion)),30,"L"),
								// 							2 => array($existenciaInicioInsumo,25,"C"),
								// 							3 => array($entradaInsumo,25,"C"),
								// 							4 => array($salidaInsumo,25,"C"),
								// 							5 => array($diferencia,25,"C"),
								// 							6 => array($existenciaFinInsumo,25,"C"),
								// 						);
								// 						$this->fpdf->LineWriteB($array_data, 1,$h);
								// 						$n+=1;
								// 					}
								// 					$this->fpdf->Ln(5);
								// 				  }
								// 			  }

								// 		$filename = "Reporte_de_venta_".str_replace(' ','_',$periodoVigente).".pdf";
								// 		ob_clean();
								// 		// antes de enviar el PDF, limpiamos buffers que puedan tener warnings o salida accidental
								//			$this->_safe_output_clean();
								//				try {
								//			$this->fpdf->Output($filename, "I");
								//			} catch (Exception $e) {
								//			log_message('error', "FPDF Output error: " . $e->getMessage());
								//			show_error("Error generando PDF. Revisa logs.");
								//						}
								//			exit;

								// 		//$this->imprimirReporteComision($fechaInicio,$fechaFinal);
								// 	}
								// }

								function ReporteProductoAutocomplete(){
									if($this->input->method(TRUE) == "POST"){
										$busquedaParametro = $this->input->post("query");

										$sucursal = $this->session->idSucursal;
										$condicionWhere = array('idSucursalProducto' => $sucursal,'estadoProducto' => 'Activo');
										$condicionLike = array('nombreProducto' => $busquedaParametro);

										$productos = TraerDatosComo("producto",$condicionWhere,$condicionLike);
										echo json_encode($productos);
									}
								}

								function ReporteInsumoAutocomplete(){
									if($this->input->method(TRUE) == "POST"){
										$busquedaParametro = $this->input->post("query");

										$sucursal = $this->session->idSucursal;
										$condicionWhere = array('idSucursalInsumo' => $sucursal,'estadoInsumo' => 'Activo');
										$condicionLike = array('nombreInsumo' => $busquedaParametro);
										$join=array(
											array(
												"tabla" => "insumoStock",
												"condicion" => "insumoStock.idInsumo = insumo.idInsumo",
												"tipo" => "inner",
												"campos" => "insumoStock.cantidadInsumoStock"
											),
										);
										$Insumos = TraerDatosComo("insumo",$condicionWhere,$condicionLike,$join,"insumo.idInsumo");
										echo json_encode($Insumos);
									}
								}
							
}
							/* End of file Usuarios.php */
