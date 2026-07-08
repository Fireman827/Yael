<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require __DIR__ . '/autoload.php';
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

$ticket = mb_strtoupper(urldecode($_REQUEST["datos"]));
$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");

$textoencodificado = str_replace($latinchars, $encoded, $ticket);
list($encabezado,$dte,$cuerpo,$pie,$tipo_imp,$ip,$rec)=explode("|",$textoencodificado);
if($tipo_imp =="IP"){
  $connector = new NetworkPrintConnector($ip,9100);
} else if($tipo_imp =="WIN"){
  $connector = new WindowsPrintConnector("".$rec."");
} else {
  $connector = new FilePrintConnector(mb_strtolower(str_replace("tiket","TIKET",strtolower($rec))));
}
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);

 $logo = EscposImage::load("logo.png");
 $printer->bitImage($logo);

$textoencodificado = $ticket;
list($encabezado,$dte,$cuerpo,$pie)=explode("|",$textoencodificado);

$printer->setJustification(Printer::JUSTIFY_CENTER);

$printer->setTextSize(1,1);
$printer->text($encabezado);
$printer->text($dte);

$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->setTextSize(1,1);
$printer->text($cuerpo);

$printer->setTextSize(2,2);
$printer->text($pie);
$printer -> feed(1);

$printer -> cut();
//$printer -> pulse();
$printer -> pulse(0,25,300);
$printer -> pulse(1,25,300);
//$printer -> pulse(1,1);
$printer -> close();

function title(Printer $printer, $str)
{
  $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
  $printer -> text($str);
  $printer -> selectPrintMode();
}

?>
