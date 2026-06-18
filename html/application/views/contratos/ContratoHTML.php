<style type='text/css'>
table.page_header {width: 100%;margin-left:53px;  margin-top: 25px;margin-bottom: 6px;  border:none; background-color: #FFFFFF; font-family:helvetica,serif;font-weight: bold; font-size: 14px;}
table.page_footer {width: 100%; border: none; background-color: #FFF;  padding: 2mm;color:#FFFFFF; font-family:helvetica,serif; font-weight:bold;}
div.note {border: solid 1mm #DDDDDD;background-color: #EEEEEE; padding: 2mm; border-radius: 2mm; width: 100%; }
ul.main { width: 95%; list-style-type: square; }
ul.main li { padding-bottom: 2mm; }
h1 { text-align: center; font-size: 20mm}
h3 { text-align:right; font-size: 14px; color:#000080}
table { vertical-align: middle; }
tr    { vertical-align: middle; }
p {margin: 0px 5px 0px 5px;}
span {margin: 5px;}
img { border: 1px #000000;}
</style>

<?php
date_default_timezone_set('America/El_Salvador');
$fecha_actual=date("Y-m-d");
function n_anio($fecha_i,$fecha_f){
  $date1 = new DateTime($fecha_i);
  $date2 = new DateTime($fecha_f);
  $diff = $date1->diff($date2);
  return ($diff->y );
}
function calcular_edad($fecha){
  list($A,$m,$d)=explode("-",$fecha);
  return( date("md") < $m.$d ? date("Y")-$A-1 : date("Y")-$A);
}

$nombre_patron = "Juan Perez";
$nombre_emple = "Juan Lopez";
$logo = "vendors/core/img/dms1.png";
?>
<page backtop="22mm" backbottom="15mm" backleft="10mm" backright="10mm" style="font-size: 10pt" backimgx="center" backimgy="bottom" backimgw="100%">
  <page_header>
    <table class="page_header">
      <tr >
        <td  style='width:50%;'>
          <img style='width:25%;' src='<?php echo $logo  ?>'>
        </td>
      </tr>

    </table>
  </page_header>

  <table style='width:100%; font-size:10pt;'>
    <tr>
      <th align=center style='width:90%;'> <p style="text-align:center"><strong><u>C O N T R A T O&nbsp;&nbsp;&nbsp; I N D I V I D U A L&nbsp;&nbsp;&nbsp; D E&nbsp;&nbsp;&nbsp; T R A B A J O</u></strong></p><br></th>
    </tr>
  </table>
  <table style='width:100%; font-size:10pt;'>
    <thead>

      <tr >
        <th style='width:50%; text-align:left'><p><strong><u>GENERALES&nbsp;&nbsp; DEL&nbsp; CONTRATANTE&nbsp;&nbsp; PATRONAL</u></strong></p></th>
        <th style='width:50%; text-align:left'><p><u><strong>GENERALES&nbsp;&nbsp; DEL(A)&nbsp; TRABAJADOR(A)</strong></u></p></th>
      </tr>
    </thead>
    <tbody >
      <tr>
        <td style='width:50%; text-align:left' ><p>Nombre: <strong> <?php echo $nombre_patron ?></strong></p></td>
        <td style='width:50%; text-align:left'><p>Nombre: <strong><?php echo $nombre_emple ?></strong></p></td>
      </tr>
    </tbody>
  </table>
</page>
