<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require __DIR__ . '/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

$datos   = $_REQUEST["datos"];
$tipo    = $_REQUEST["tipo"];
$recurso = $_REQUEST["recurso"];
$ip      = $_REQUEST["ip"];

$latinchars = array('ñ','á','é','í','ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded    = array("\xa4","\xa0","\x82","\xa1","\xa2","\xa3","\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");

if ($tipo == "IP") {
    $connector = new NetworkPrintConnector($ip, 9100);
} else if ($tipo == "WIN") {
    $connector = new WindowsPrintConnector($recurso);
} else {
    $connector = new FilePrintConnector(str_replace("tiket","TIKET",strtolower($recurso)));
}

$printer = new Printer($connector);

$textoencodificado = str_replace($latinchars, $encoded, $datos);
list($encabezado, $detalle) = explode("|", $textoencodificado);

$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->setTextSize(2, 2);
$printer->text($encabezado);

$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->setTextSize(1, 1);
$printer->text($detalle);

$printer->feed(2);
$printer->cut();
$printer->close();
?>
