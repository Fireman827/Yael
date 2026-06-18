<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$datos = $_REQUEST['datos'];
// print_r($datos);
require __DIR__ . '/autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea

use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;


$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");

foreach($datos as $imp){
        if(urldecode($imp['productos']) != ""){

            $nombre_impresora = ($imp['impresoraTipo'] == "IP") ? $imp['IpImpresora'] : $imp['recursoCompartido'];
            $connector = ($imp['impresoraTipo'] == "IP") ? new NetworkPrintConnector($nombre_impresora, 9100) : (
                         ($imp['impresoraTipo'] == "LIN") ? new FilePrintConnector($nombre_impresora) : new WindowsPrintConnector($nombre_impresora) );
            $printer = new Printer($connector);
            $printer -> setFont(Printer::FONT_A);
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $textoencodificado = str_replace($latinchars, $encoded, urldecode($imp['productos']));
            list($encabezado,$detalle)=explode("|",$textoencodificado);
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $printer->setTextSize(2,2);
            $printer->text($encabezado);
            $printer->feed(1);

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setTextSize(1,1);
            $printer->text($detalle);
            $printer->feed(2);

            if($imp['comentario'] != ""){
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setTextSize(1,1);
                $printer->text($imp['comentario']);
                $printer->feed(1);
            }
            $printer->cut();

           $printer->close();
       }
}

function title(Printer $printer, $str)
{
   $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
   $printer -> text($str);
   $printer -> selectPrintMode();
}

?>
