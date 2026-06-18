<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require __DIR__ . '/autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea

use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

$datos = $_REQUEST["datos"];

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");

$nombre_impresora = ($datos["tipo"] == "IP") ? $datos["IpImpresora"] : $datos["recursoCompartido"];
$connector = ($datos["tipo"] == "IP") ? new NetworkPrintConnector($nombre_impresora, 9100) :
            (($datos["tipo"] == "LIN") ? new FilePrintConnector($nombre_impresora) : new WindowsPrintConnector($nombre_impresora) );

      $printer = new Printer($connector);
      $printer -> setFont(Printer::FONT_A);
      $printer->setJustification(Printer::JUSTIFY_CENTER);
      $printer->feed(1);
      $printer->setTextSize(2,2);
      $printer->text("Exito!");
      $printer->setTextSize(1,1);
      $printer->feed(2);
      $printer->text("Impresora: ".$nombre_impresora);
      $printer->feed(2);
      $printer -> cut();
      $printer -> close();


function title(Printer $printer, $str)
{
   $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
   $printer -> text($str);
   $printer -> selectPrintMode();
}

?>
