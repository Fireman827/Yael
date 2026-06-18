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
use Mike42\Escpos\ImagickEscposImage;

$datos1 = $_REQUEST["datos"];
$tipo = $_REQUEST["tipo"];
$recurso = $_REQUEST["recurso"];
$ip = $_REQUEST["ip"];
//$nombre_impresora = "192.168.0.220";
//$nombre_impresora = "/dev/usb/lp0";

$latinchars = array( 'ñ','á','é', 'í', 'ó','ú','ü','Ñ','Á','É','Í','Ó','Ú','Ü');
$encoded = array("\xa4","\xa0", "\x82","\xa1","\xa2","\xa3", "\x81","\xa5","\xb5","\x90","\xd6","\xe0","\xe9","\x9a");

// $nombre_impresora = "192.168.0.150";
$nombre_impresora = ($tipo == "IP") ? $ip : $recurso;
$connector = ($tipo == "IP") ? new NetworkPrintConnector($nombre_impresora, 9100) :
            (($tipo == "LIN") ? new FilePrintConnector($nombre_impresora) : new WindowsPrintConnector($nombre_impresora) );
//$connector =  new NetworkPrintConnector($nombre_impresora, 9100);
$printer = new Printer($connector);
for($ao=0; $ao<2; $ao++){
$printer -> setFont(Printer::FONT_A);
$printer->setJustification(Printer::JUSTIFY_CENTER);;

//$logo = EscposImage::load("logo.png",false,array('gd'));

    $textoencodificado = str_replace($latinchars, $encoded, urldecode($datos1['servicio']));
    list($encabezado,$datos,$pie)=explode("|",$textoencodificado);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setTextSize(1,1);
  //  $printer->bitImage($logo);
    $printer->text("\n");
    $printer->text($encabezado);
	$printer->feed(1);

    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->setTextSize(1,1);
    $printer->text($datos);
    $printer -> feed(1);

    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setTextSize(1,1);
    $printer->text($pie);
    $printer -> feed(2);
    $printer -> cut();

    if(isset($datos1['servicio11'])){
        $textoencodificado = str_replace($latinchars, $encoded, urldecode($datos1['servicio11']));
        list($encabezado,$datos)=explode("|",$textoencodificado);
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setTextSize(2,2);
        $printer->text($encabezado);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setTextSize(1,1);
        $printer->text($datos);
        $printer -> feed(2);
        $printer -> cut();
    }
    if(isset($datos1["servicio12"])){
      $lista = explode("@",urldecode($datos1["servicio12"]));
      for($k=0;$k<count($lista)-1;$k++){
      $textoencodificado = str_replace($latinchars, $encoded, $lista[$k+1]);
      list($encabezado,$nombrr,$datos)=explode("|",$textoencodificado);

      $printer->setJustification(Printer::JUSTIFY_CENTER);

      $logo = EscposImage::load("logodi2.jpg", false);



      $printer->setJustification(Printer::JUSTIFY_CENTER);
      $printer->setTextSize(2,2);
      $printer->text($encabezado);
	  $printer->text($nombrr);

      $printer->setJustification(Printer::JUSTIFY_LEFT);
      $printer->setTextSize(1,1);
      $printer->text($datos);
      $printer->cut();
      }
    }
}
    $printer -> pulse();

$printer -> close();

function title(Printer $printer, $str)
{
   $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH);
   $printer -> text($str);
   $printer -> selectPrintMode();
}

?>
