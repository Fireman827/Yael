<?php
/* Este script es el que se redirecciona a localhost donde esta el printer
y debe haber un apache corriendo con soporte php
Agregar el usuario al grupo en debian
usermod -a -G lp www-data
Permisos al puerto
su -c 'chmod 777 /dev/usb/lp0'
*/
header("Access-Control-Allow-Origin: *");


$printer="/dev/usb/lp2";

$string="";

$string.=chr(27).chr(112)."0"."25"."250";  // Abrir cajon
$string.=chr(27).chr(112)."48"."25"."250";  // Abrir cajon
$string.=chr(27).chr(112)."1"."25"."250";  // Abrir cajon
$string.=chr(27).chr(112)."49"."25"."250";  // Abrir cajon



$fp0=fopen($printer, 'wb');
fwrite($fp0,$string);
fclose($fp0);

?>
