<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require __DIR__ . '/autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

$datos = urldecode($_REQUEST["datos"]);
$nombre_impresora = "192.168.0.220";
//$nombre_impresora = "/dev/usb/lp0";

$texto = strtoupper($_REQUEST['datos']);

//$printer="/dev/usb/lp1";

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");

$textoencodificado = str_replace($latinchars, $encoded, $texto);
list($encabezado,$datos)=explode("|",$textoencodificado);

$connector = new NetworkPrintConnector($nombre_impresora, 9100);
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);

$logo = EscposImage::load("logodi2.jpg", false);

// $printer->bitImage($logo);

$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->setTextSize(2,2);
$printer->text($encabezado);

$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->setTextSize(1,1);
$printer->text($datos);

$printer->cut();
$printer->pulse();
$printer->close();


?>
