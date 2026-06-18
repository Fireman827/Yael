<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require __DIR__ . '/autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

// RECIBIR DATOS DE IMPRESORA
$tipo = $_REQUEST["tipo"];
$recurso = $_REQUEST["recurso"];
$ip = $_REQUEST["ip"];

//$datos = $_REQUEST["datos"];
//$nombre_impresora = "192.168.0.57";
$nombre_impresora = "ticket";

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");

$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);

//$logo = EscposImage::load("logodi2.jpg", false);
$servicios = json_decode($datos["servicio"],true);

//print_r($servicios[$j])."\n";

    $textoencodificado = str_replace($latinchars, $encoded, $datos);
    list($encabezado,$detalle)=explode("|",$textoencodificado);
    $i = 0;
    // $printer->bitImage($logo);
    $printer -> feed();
    $printer->setJustification(Printer::JUSTIFY_CENTER);

    $printer->setTextSize(2,2);
    $printer->text($encabezado);

    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->setTextSize(1,1);
    $printer->text($detalle);

    $printer -> feed(2);
    $printer -> cut();


$printer -> close();

function title(Printer $printer, $str)
{
   $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
   $printer -> text($str);
   $printer -> selectPrintMode();
}

?>
