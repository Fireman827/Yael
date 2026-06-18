<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('GblTraerConfiguracion')){
	// Funcion para obtener parametros de configuracion generales
		// $parametro = valor de configuracion que se desea obtener
	function GblTraerConfiguracion($parametro= ""){
    $ci =& get_instance();
		$ci->load->model('CoreModel');
		$conf = $ci->CoreModel->TraerConfiguracion($parametro);
		if($conf !== false){
			return $conf->valorConfiguracion;
		} else{
			return "No se encontro el parametro";
		}
	}
}
if(!function_exists("ValidarSesion")){
	function GblValidarSesion($obj){
		if ($obj->session->existeSesion == ""){
			redirect('InicioSesion', 'refresh');
		}
	}
}
if(!function_exists("GblPermisos")){
	function GblPermisos($instancia,$funcion,$controlador){
		if ($instancia->session->existeSesion == ""){
			redirect('InicioSesion', 'refresh');
		} else{
			$ci =& get_instance();
			$ci->load->model('CoreModel');
			$idUsuario = $instancia->session->idUsuario;
			if($funcion == "index"){
				$funcion = "";
			}
			$tienePermiso = $ci->CoreModel->PermisosUsuario($idUsuario,$funcion,$controlador);
			if($instancia->session->admin==0){
				return $tienePermiso;
			}	else{
				return true;
			}
		}
	}
}
if (!function_exists("IniciarTransaccion")){
	function IniciarTransaccion(){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		$ci->CoreModel->IniciarTransaccion();
	}
}
if (!function_exists("DeshacerTransaccion")){
	function DeshacerTransaccion(){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		$ci->CoreModel->DeshacerTransaccion();
	}
}
if (!function_exists("EjecutarTransaccion")){
	function EjecutarTransaccion()
	{
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		$ci->CoreModel->EjecutarTransaccion();
	}
}
if (!function_exists("TraerDatosTabla")){
	function TraerDatosTabla($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerDatosTabla($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion);
	}
}
if (!function_exists("TraerDatosTablaJoin")){
	function TraerDatosTablaJoin($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion="", $joins=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerDatosTablaJoin($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion, $joins);
	}
}
if (!function_exists("TraerDatosTablaJoinGroup")){
	function TraerDatosTablaJoinGroup($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion="", $joins="",$select="",$group=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerDatosTablaJoinGroup($tabla,$ordenCampos, $busqueda, $columnasValidas, $cantidad, $desde, $ordenDirecion, $condicion, $joins,$select,$group);
	}
}
if (!function_exists("TraerTotalDatos")){
	function TraerTotalDatos($tabla,$condicion="", $busqueda="", $columnasValidas="",$join="",$group = ""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerTotalDatos($tabla,$condicion, $busqueda, $columnasValidas,$join,$group);
	}
}
if (!function_exists("TraerDatos")){
	function TraerDatos($tabla,$condicion="",$ordenCampos=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerDatos($tabla,$condicion,$ordenCampos);
	}
}
if (!function_exists("TraerDatosRemonbrados")){
	function TraerDatosRenombrados($tabla,$campos = "",$condicion = "",$ordenCampos = "",$group = ""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerDatosRenombrados($tabla,$campos,$condicion,$ordenCampos,$group);
	}
}
if (!function_exists("TraerDatosJoin")){
	function TraerDatosJoin($tabla,$condicion="",$ordenCampos="",$join=[array("tabla","condicion","tipo","campos")],$group=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerDatosJoin($tabla,$condicion,$ordenCampos,$join,$group);
	}
}
if (!function_exists("TraerDatosComo")){
	function TraerDatosComo($tabla,$condicion1="Where",$condicion2="Like",$join=[],$group=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerDatosComo($tabla,$condicion1,$condicion2,$join,$group);
	}
}
if (!function_exists("TraerUnDato")){
	function TraerUnDato($tabla,$condicion,$order = ""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerUnDato($tabla,$condicion,$order);
	}
}
if (!function_exists("TraerUnDatoJoin")){
	function TraerUnDatoJoin($tabla,$condicion,$join=[array("tabla","condicion","tipo","campos")],$ordenCampos="",$group=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerUnDatoJoin($tabla,$condicion,$join,$ordenCampos,$group);
	}
}
if (!function_exists("TraerUnDatoJoinCampo")){
	function TraerUnDatoJoinCampo($tabla,$condicion,$join=[array("tabla","condicion","tipo","campos")],$campos="", $group=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerUnDatoJoinCampo($tabla,$condicion,$join,$campos, $group);
	}
}
if (!function_exists("TraerUnDatoIndividual")){
	function TraerUnDatoIndividual($tabla,$campo,$condicion){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerUnDatoIndividual($tabla,$campo,$condicion);
	}
}
if (!function_exists("TraerMaxValor")){
	function TraerMaxValor($tabla,$campo,$condicion=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->TraerMaxValor($tabla,$campo,$condicion);
	}
}
if (!function_exists("ExistenDatos")){
	function ExistenDatos($tabla,$condicion){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->ExistenDatos($tabla,$condicion);
	}
}
if (!function_exists("GuardarDatos")){
	function GuardarDatos($tabla,$datos){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->GuardarDatos($tabla,$datos);
	}
}
if (!function_exists("EditarDatos")){
	function EditarDatos($tabla,$datos,$condicion){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->EditarDatos($tabla,$datos,$condicion);
	}
}
if (!function_exists("ActualizarCorrelativo")){
	function  ActualizarCorrelativo($tabla,$condicion,$campo,$incremento){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->ActualizarCorrelativo($tabla,$condicion,$campo,$incremento);
	}
}
if (!function_exists("EliminarDatos")){
	function EliminarDatos($tabla,$condicion){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		if(ExistenDatos($tabla,$condicion)){
			return $ci->CoreModel->EliminarDatos($tabla,$condicion);
		} else{
			return true;
		}
	}
}
if (!function_exists("SimpleQuery")){
	function SimpleQuery($datos){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		return $ci->CoreModel->SimpleQuery($datos);
	}
}
if (!function_exists("MostrarError")){
	function MostrarError(){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		$error = $ci->CoreModel->MostrarError();
		return $error["code"].": ".$error["message"];
	}
}
if(!function_exists("EncriptarClave")){
	function EncriptarClave($claveReal){
		$ci =& get_instance();
		return $ci->encryption->encrypt($claveReal);
	}
}

if(!function_exists("DesencriptarClave")){
	function DesencriptarClave($claveEncriptada){
		$ci =& get_instance();
		return $ci->encryption->decrypt($claveEncriptada);
	}
}

if(!function_exists("num2letras")){
  function num2letras($num, $fem = true, $dec = true) {
    $matuni[0]  = "cero";
    $matuni[2]  = "dos";
    $matuni[3]  = "tres";
    $matuni[4]  = "cuatro";
    $matuni[5]  = "cinco";
    $matuni[6]  = "seis";
    $matuni[7]  = "siete";
    $matuni[8]  = "ocho";
    $matuni[9]  = "nueve";
    $matuni[10] = "diez";
    $matuni[11] = "once";
    $matuni[12] = "doce";
    $matuni[13] = "trece";
    $matuni[14] = "catorce";
    $matuni[15] = "quince";
    $matuni[16] = "dieciseis";
    $matuni[17] = "diecisiete";
    $matuni[18] = "dieciocho";
    $matuni[19] = "diecinueve";
    $matuni[20] = "veinte";
    $matunisub[2] = "dos";
    $matunisub[3] = "tres";
    $matunisub[4] = "cuatro";
    $matunisub[5] = "quin";
    $matunisub[6] = "seis";
    $matunisub[7] = "sete";
    $matunisub[8] = "ocho";
    $matunisub[9] = "nove";

    $matdec[2] = "veint";
    $matdec[3] = "treinta";
    $matdec[4] = "cuarenta";
    $matdec[5] = "cincuenta";
    $matdec[6] = "sesenta";
    $matdec[7] = "setenta";
    $matdec[8] = "ochenta";
    $matdec[9] = "noventa";
    $matsub[3]  = 'mill';
    $matsub[5]  = 'bill';
    $matsub[7]  = 'mill';
    $matsub[9]  = 'trill';
    $matsub[11] = 'mill';
    $matsub[13] = 'bill';
    $matsub[15] = 'mill';
    $matmil[4]  = 'millones';
    $matmil[6]  = 'billones';
    $matmil[7]  = 'de billones';
    $matmil[8]  = 'millones de billones';
    $matmil[10] = 'trillones';
    $matmil[11] = 'de trillones';
    $matmil[12] = 'millones de trillones';
    $matmil[13] = 'de trillones';
    $matmil[14] = 'billones de trillones';
    $matmil[15] = 'de billones de trillones';
    $matmil[16] = 'millones de billones de trillones';

    $num = trim((string)@$num);
    if($num[0] == '-') {
      $neg = 'menos ';
      $num = substr($num, 1);
    } else
      $neg = '';
    while ($num[0] == '0') $num = substr($num, 1);
    if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
    $zeros = true;
    $punt = false;
    $ent = '';
    $fra = '';
    for($c = 0; $c < strlen($num); $c++) {
      $n = $num[$c];
      if(!(strpos(".,'''", $n) === false)) {
        if ($punt) break;
        else{
          $punt = true;
          continue;
        }
      } elseif (! (strpos('0123456789', $n) === false)) {
        if ($punt) {
          if ($n != '0') $zeros = false;
          $fra .= $n;
        } else {
          $ent .= $n;
        }
      } else
        break;

    }
    $ent = '     ' . $ent;
    if ($dec and $fra and ! $zeros) {
      $fin = ' coma';
      for ($n = 0; $n < strlen($fra); $n++) {
        if (($s = $fra[$n]) == '0')
          $fin .= ' cero';
        elseif ($s == '1')
          $fin .= $fem ? ' un' : ' un';
        else
          $fin .= ' ' . $matuni[$s];
      }
    } else $fin = '';
    if ((int)$ent === 0) return 'cero ' . $fin;
    $tex = '';
    $sub = 0;
    $mils = 0;

    $neutro = false;
    while(($num = substr($ent, -3))!= '   '){
      $ent = substr($ent, 0, -3);
      if(++$sub < 3 and $fem) {
        $matuni[1] = 'un';
        $subcent = 'os';
      } else {
        $matuni[1] = $neutro ? 'un' : 'uno';
        $subcent = 'os';
      }
      $t = '';
      $n2 = substr($num, 1);
      if ($n2 == '00') {

      } elseif ($n2 < 21){
        $t = ' ' . $matuni[(int)$n2];
      } elseif ($n2 < 30) {
        $n3 = $num[2];
        if ($n3 != 0) $t = 'i' . $matuni[$n3];
        $n2 = $num[1];
        $t = ' ' . $matdec[$n2] . $t;
      } else {
        $n3 = $num[2];
        if ($n3 != 0) $t = ' y ' . $matuni[$n3];
        $n2 = $num[1];
        $t = ' ' . $matdec[$n2] . $t;
      }
      $n = $num[0];
      if($n == 1) {
        $t = ' ciento' . $t;
      } elseif($n == 5){
        $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
      } elseif($n != 0){
        $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
      }
      if($sub == 1) {
      } elseif (! isset($matsub[$sub])) {
          if ($num == 1) {
            $t = ' mil';
          } elseif ($num > 1){
            $t .= ' mil';
          }
      } elseif ($num == 1) {
          $t .= ' ' . $matsub[$sub] . 'on';
      } elseif ($num > 1){
          $t .= ' ' . $matsub[$sub] . 'ones';
      }
      if ($num == '000') $mils ++;
      elseif ($mils != 0) {
          if(isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
          $mils = 0;
      }
      $neutro = true;
      $tex = $t . $tex;
    }
    $tex = $neg . substr($tex, 1) . $fin;
    return ($tex);
  }
}

if(!function_exists("meses")){
  function meses($mes){
    switch ($mes) {
      case 1:
        $month = "ENERO";
        break;
      case 2:
        $month = "FEBRERO";
        break;
      case 3:
        $month = "MARZO";
        break;
      case 4:
        $month = "ABRIL";
        break;
      case 5:
        $month = "MAYO";
        break;
      case 6:
        $month = "JUNIO";
        break;
      case 7:
        $month = "JULIO";
        break;
      case 8:
        $month = "AGOSTO";
        break;
      case 9:
        $month = "SEPTIEMBRE";
        break;
      case 10:
        $month = "OCTUBRE";
        break;
      case 11:
        $month = "NOVIEMBRE";
        break;
      case 12:
        $month = "DICIEMBRE";
        break;
      default:
        $month = $mes;
        break;
    }
    return $month;
  }
}

if(!function_exists("numero_anios")){
  function numero_anios($fecha_i,$fecha_f){
    $date1 = new DateTime($fecha_i);
    $date2 = new DateTime($fecha_f);
    $diff = $date1->diff($date2);
    return ($diff->y );
  }
}

if(!function_exists("calcular_edad")){
  function calcular_edad($fecha){
    list($A,$m,$d)=explode("-",$fecha);
    return( date("md") < $m.$d ? date("Y")-$A-1 : date("Y")-$A);
  }
}


if(!function_exists("numero_meses")){
  //numero de meses transcurridos entre dos fechas
  function numero_meses($fechaini,$fechafin){
    $fechainicial = new DateTime($fechaini);

    $fechafinal = new DateTime($fechafin);
    $diferencia = $fechainicial->diff($fechafinal);
    $meses = ( $diferencia->y * 12 ) + $diferencia->m;
    return $meses;
  }
}

if(!function_exists("diferenciaHoras")){
  //numero de horas transcurridas entre dos fechas
  function diferenciaHoras($fechaInicio,$fechaFin){
    $datetime1 = date_create($fechaInicio);
    $datetime2 = date_create($fechaFin);

    $contador = date_diff($datetime1, $datetime2);
    $differenceFormat = '%H:%I:%S';
    return $contador->format($differenceFormat);
  }
}
if(!function_exists("diferenciaDias")){
  //numero de horas transcurridas entre dos fechas
  function diferenciaDias($fechaInicio,$fechaFin){
    $datetime1 = date_create($fechaInicio);
    $datetime2 = date_create($fechaFin);

    $contador = date_diff($datetime1, $datetime2);
    $differenceFormat = '%a';
    return $contador->format($differenceFormat);
  }
}
if(!function_exists("sumarHoras")){
  function sumarHoras($hora1, $hora2)
  {
    list($h, $m, $s) = explode(':', $hora2); //Separo los elementos de la segunda hora
    $a = new DateTime($hora1); //Creo un DateTime
    $b = new DateInterval(sprintf('PT%sH%sM%sS', $h, $m, $s)); //Creo un DateInterval
    $a->add($b); //SUMO las horas
    return $a->format('H:i:s'); //Retorno la Suma
  }
}

if(!function_exists("restarHoras")){
  function restarHoras($hora1, $hora2){
      list($h, $m, $s) = explode(':', $hora2); //Separo los elementos de la segunda hora
      $a = new DateTime($hora1); //Creo un DateTime
      $b = new DateInterval(sprintf('PT%sH%sM%sS', $h, $m, $s)); //Creo un DateInterval
      $a->sub($b); //RESTO las horas
      return $a->format('H:i:s'); //Retorno la Resta
  }
}


if(!function_exists("letras")){
  //funcion que contiene un array de letras en spanish
  function letras($n){
    $mes = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    return $mes[$n-1];
  }
}

if(!function_exists("Hora")){
  //Formatea una hora
  function Hora($hora)
  {
    $hora_pre = date_create($hora);
    $hora_pos = date_format($hora_pre, 'g:i A');
    return $hora_pos;
  }
}

if(!function_exists("Fecha_A_M_D")){
  //Formatea una fecha de Dia Mes Año a Año Mes Dia
  function fecha_A_M_D($fecha){
    $dia = substr($fecha,0,2);
    $mes = substr($fecha,3,2);
    $a = substr($fecha,6,4);
    $fecha = "$a-$mes-$dia";
    return $fecha;
  }
}

if(!function_exists("Fecha_D_M_A")){
  //Formatea una Fecha de Año Mes Dia a Dia Mes Año
  function Fecha_D_M_A($fecha){
    $dia = substr($fecha,8,2);
    $mes = substr($fecha,5,2);
    $a = substr($fecha,0,4);
    $fecha = "$dia-$mes-$a";
    return $fecha;
  }
}

if(!function_exists("Zfill")){
  //Formatea numero con una cantidad de ceros a la izquierda
  function Zfill($texto,$cantidad) {
    return str_pad($texto,$cantidad,"0",STR_PAD_LEFT);
    }
}

if(!function_exists("Mayuscula")){
  //Formatea un texto a mayusculas
  function Mayuscula($cadena) {
    $mayusculas = strtr(strtoupper(utf8_encode($cadena)),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
    return $mayusculas;
    }
}

if(!function_exists("Minuscula")){
  //Formatea un texto a minuscula
  function Minuscula($cadena) {
    $mayusculas = strtr(strtolower(utf8_encode($cadena)),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
    return $mayusculas;
    }
}

if(!function_exists("PeriodoFechas")){
  //Formatea un rango de fechas en formato 'Año-Mes-Dia' y describe en letras el periodo entre estas
  function PeriodoFechas($fecha1,$fecha2){

    $periodo = "";
			list($a,$m,$d) = explode("-", $fecha1);
			list($a1,$m1,$d1) = explode("-", $fecha2);
			if($a == $a1){
				if($m==$m1){
					if($d==$d1){
						$periodo = "$d1 DE ".meses($m)." DE $a";
					} else {
						$periodo = "DEL $d AL $d1 DE ".meses($m)." DE $a";
					}
				} else {
					$periodo = "DEL $d DE ".meses($m)." AL $d1 DE ".meses($m1)." DE $a";
				}
			} else {
				$periodo = "DEL $d DE ".meses($m)." DEL $a AL $d1 DE ".meses($m1)." DE $a1";
			}
    return $periodo;
  }
}

if(!function_exists("PeriodoFechasHora")){
  //Formatea un rango de fechas en formato 'Año-Mes-Dia Hora-Minuto-Segundo' y describe en letras el periodo entre estas
  function PeriodoFechasHora($fecha1,$fecha2){

    $periodo = "";
      list($fecha1,$hora1) = explode(" ",$fecha1);
      list($fecha2,$hora2) = explode(" ",$fecha2);
			list($a,$m,$d) = explode("-", $fecha1);
			list($a1,$m1,$d1) = explode("-", $fecha2);
			if($a == $a1){
				if($m==$m1){
					if($d==$d1){
						$periodo = "$d1 DE ".meses($m)." DE $a ";
            if($hora1 == $hora2){
              $periodo .= " A LAS ".Hora($hora1);
            }
            else{
              $periodo .= " DE LAS ".Hora($hora1)." A LAS ".Hora($hora2);
            }
					} else {
            if($hora1 == $hora2){
              $periodo = "DEL $d AL $d1 DE ".meses($m)." DE $a";
              $periodo .= " A LAS ".Hora($hora1);
            }
            else{
              //$periodo = "DEL $d"." DE LAS ".Hora($hora1)." AL $d1 "." A LAS ".Hora($hora2)." DE ".meses($m)." DE $a";
              $periodo = "DEL $d DE ".meses($m)." A LAS ".Hora($hora1)." AL $d1 DE ".meses($m1)." A LAS ".Hora($hora2)." DE $a";
            }
					}
				} else {
          $periodo = "DEL $d DE ".meses($m)." A LAS ".Hora($hora1)." AL $d1 DE ".meses($m1)." A LAS ".Hora($hora2)." DE $a";
				}
			} else {
				$periodo = "DEL $d DE ".meses($m)." DEL $a"." A LAS ".Hora($hora1)." AL $d1 DE ".meses($m1)." DE $a1"." A LAS ".Hora($hora2);
			}
    return $periodo;
  }
}

$precios_sistema = array(
  'precio',
  'precio1',
  'precio2',
  'precio3',
  'precio4',
  'precio5',
  'precio6',
);
if(!function_exists("ObtenerPrecios")){
	function ObtenerPrecios($id_presentacion,$n_p=0)
	{
	  global $precios_sistema;
	  if ($n_p==0) {
	    $n_p=count($precios_sistema);
	  }
	  $array_precios = array();
	  $sql=_query("SELECT * FROM insumoPresentacion WHERE idInsumoPresentacion='$id_presentacion'");
	  if (_num_rows($sql)>0) {
	    $precios=_fetch_array($sql);
	    foreach ($precios_sistema as $key => $value) {
	      if ($key<$n_p) {
	        $array_precios[]=$precios[$value];
	      }
	    }
	  }
	  else {
	    $array_precios[0]=0.00;
	  }
	  return $array_precios;
	}
}

if(!function_exists("ObtenerPresentacion")){
	function ObtenerPresentacion()
	{
	  $id_presentacion =$_REQUEST['id_presentacion'];
	  $sql=_fetch_array(_query("SELECT * FROM insumoPresentacion WHERE idInsumoPresentacion=$id_presentacion"));
	  $precio=$sql['precio'];
	  $unidad=$sql['unidad'];
	  $descripcion=$sql['descripcion'];
	  $costo=$sql['costo'];
	  $xdatos['precio']=$precio;
	  $xdatos['costo']=$costo;
	  $xdatos['unidad']=$unidad;
	  $xdatos['descripcion']=$descripcion;
	  $xdatos['pbarcode']=$sql['barcode'];;
	  return $xdatos;
	}
}

/*los usan compras y cargas de inventario*/
function getStockfar()
{
  $id_producto = $_REQUEST['id_producto'];
  $tipo = $_REQUEST['tipo'];
  $id_usuario=$_SESSION['id_usuario'];
  $id_presentacione=0;
  $r_precios=_fetch_array(_query("SELECT precio FROM frm_usuario WHERE id_usuario=$id_usuario"));
  $precios=$r_precios['precio'];
  $limit="LIMIT ".$precios;
  if($tipo == "D")
  {
    $clause = "p.id_producto = '$id_producto'";
  }
  else
  {
	   $id_producto = $id_producto;
      $sql_aux = _query("SELECT id_pproducto as id_presentacion, id_producto FROM frm_presentacion_producto WHERE  barcode='$id_producto' AND estado='Activo'  ");
      if(_num_rows($sql_aux)>0)
      {
        $dats_aux = _fetch_array($sql_aux);
        $id_producto = $dats_aux["id_producto"];
        $id_presentacione = $dats_aux["id_presentacion"];
        $clause = "p.id_producto = '$id_producto'";
      }
      else
      {
        $clause = "p.barcode = '$id_producto'";
      }
  }
  $sql1 = "SELECT p.id_producto, p.descripcion
  FROM frm_producto AS p
  WHERE $clause";
  $stock1=_query($sql1);
  if (_num_rows($stock1)>0)
  {
    $row1=_fetch_array($stock1);
    $descipcion = $row1["descripcion"];
    $id_producto = $row1["id_producto"];
    $i=0;
    $unidadp=0;
    $preciop=0;
    $costop=0;
    $descripcionp=0;
    $pbarcode="";
    $anda = "";
    if($id_presentacione > 0)
    {
      $anda = " AND prp.id_pproducto = '$id_presentacione'";
    }
    $sql_p=_query("SELECT frm_presentacion.nombre, prp.descripcion,
      prp.id_pproducto as id_presentacion,prp.unidad,prp.costo,prp.precio,prp.barcode
      FROM frm_presentacion_producto AS prp
      JOIN frm_presentacion ON frm_presentacion.id_presentacion=prp.id_presentacion
      WHERE prp.id_producto='$id_producto'
      AND prp.estado='Activo'
      $anda ORDER BY prp.unidad DESC");
      $select="<select class='sel form-control'>";
      while ($row=_fetch_array($sql_p))
      {
        if ($i==0)
        {
          $unidadp=$row['unidad'];
          $costop=$row['costo'];
          $preciop=$row['precio'];
          $descripcionp=$row['descripcion'];
          $pbarcode=$row['barcode'];
          $xc=0;
        }
        $select.="<option value='".$row["id_presentacion"]."'>".$row["nombre"]." (".$row["unidad"].")</option>";
        $i=$i+1;
      }
      $select.="</select>";
      $xdatos['select']= $select;
      $xdatos['descrip']= $descipcion;
      $xdatos['id_p']= $id_producto;
      $xdatos['costop']= $costop;
      $xdatos['preciop']= $preciop;
      $xdatos['unidadp']= $unidadp;
      $xdatos['descripcionp']= $descripcionp;
      $xdatos['pbarcode']= $pbarcode;
      $xdatos['i']=$i;

      $sql_perece="SELECT * FROM frm_producto WHERE id_producto='$id_producto'";
      $result_perece=_query($sql_perece);
      $row_perece=_fetch_array($result_perece);
      $perecedero=$row_perece['perecedero'];
      $xdatos['perecedero'] = $perecedero;
      $xdatos['categoria']=$row_perece['id_categoria'];
      $xdatos['typeinfo']="Success";
      return ($xdatos);
    }
    else
    {
      $xdatos['typeinfo']="Error";
      $xdatos['msg']="El codigo ingresado no pertenece a ningun producto";
      return ($xdatos);
    }
}
/*lo usa descargo de inventario*/
function getStockExisfar($value='')
{
  // code...
  $id_producto = $_REQUEST['id_producto'];
  $ubicacion = $_REQUEST['ubicacion'];
  $tipo = $_REQUEST['tipo'];
  $id_usuario=$_SESSION['id_usuario'];
  $id_presentacione=0;
  $r_precios=_fetch_array(_query("SELECT precio FROM frm_usuario WHERE id_usuario=$id_usuario"));
  $precios=$r_precios['precio'];
  $limit="LIMIT ".$precios;
  if($tipo == "D")
  {
    $clause = "p.id_producto = '$id_producto'";
  }
  else
  {
      $id_producto = intval($id_producto);

      $sql_aux = _query("SELECT id_pproducto as id_presentacion, id_producto FROM frm_presentacion_producto
        WHERE id_pproducto='$id_producto' AND estado='Activo' OR  barcode='$id_producto' AND estado='Activo'");
      if(_num_rows($sql_aux)>0)
      {
        $dats_aux = _fetch_array($sql_aux);
        $id_producto = $dats_aux["id_producto"];
        $id_presentacione = $dats_aux["id_presentacion"];
        $clause = "p.id_producto = '$id_producto'";
      }
      else
      {
        $clause = "p.barcode = '$id_producto'";
      }
  }
  $sql1 = "SELECT p.id_producto, p.descripcion
           FROM frm_producto AS p
           WHERE $clause";
  $stock1=_query($sql1);
  if (_num_rows($stock1)>0)
  {
    $row1=_fetch_array($stock1);
    $descipcion = $row1["descripcion"];
    $id_producto = $row1["id_producto"];
    $sql_exis = _query("SELECT sum(cantidad) as stock FROM frm_existencias_ubicacion
                        WHERE id_producto = '$id_producto' AND id_ubicacion='$ubicacion'");
    $datos_exis = _fetch_array($sql_exis);
    $stockv = $datos_exis["stock"];
    if($stockv>0)
    {
      $i=0;
      $unidadp=0;
      $preciop=0;
      $costop=0;
      $descripcionp=0;
      $anda = "";
      if($id_presentacione > 0)
      {
        $anda = " AND prp.id_pproducto = '$id_presentacione'";
      }
      $sql_p=_query("SELECT frm_presentacion.nombre, prp.descripcion,
                     prp.id_pproducto as id_presentacion,prp.unidad,prp.costo,prp.precio
                     FROM frm_presentacion_producto AS prp
                     JOIN frm_presentacion ON frm_presentacion.id_presentacion=prp.id_presentacion
                     WHERE prp.id_producto='$id_producto'
                     AND prp.estado='Activo'
                     $anda");
      $select="<select class='sel form-control'>";
      while ($row=_fetch_array($sql_p))
      {
        if ($i==0)
        {
          $unidadp=$row['unidad'];
          $costop=$row['costo'];
          $preciop=$row['precio'];
          $descripcionp=$row['descripcion'];
        }
        $select.="<option value='".$row["id_presentacion"]."'>".$row["nombre"]." (".$row["unidad"].")</option>";
        $i=$i+1;
      }
      $select.="</select>";
      $xdatos['stock']= $stockv;
      $xdatos['select']= $select;
      $xdatos['descrip']= $descipcion;
      $xdatos['id_p']= $id_producto;
      $xdatos['costop']= $costop;
      $xdatos['preciop']= $preciop;
      $xdatos['unidadp']= $unidadp;
      $xdatos['descripcionp']= $descripcionp;
      $xdatos['i']=$i;

      $sql_perece="SELECT * FROM frm_producto WHERE id_producto='$id_producto'";
      $result_perece=_query($sql_perece);
      $row_perece=_fetch_array($result_perece);
      $perecedero=$row_perece['perecedero'];
      $xdatos['perecedero'] = $perecedero;
      $xdatos['categoria']=$row_perece['id_categoria'];
      $xdatos['typeinfo']="Success";
      return ($xdatos);
    }
    else
    {
      $sql_exis = _query("SELECT existencia FROM frm_producto WHERE id_producto = '$id_producto'");
      $datos_exis = _fetch_array($sql_exis);
      $stockv = $datos_exis["existencia"];
      if($stockv>0)
      {
        $xdatos['typeinfo']="Error";
        $xdatos['msg']="El producto seleccionado no posee existencias en esta ubicacion";
        return ($xdatos);
      }
      else
      {
        $xdatos['typeinfo']="Error";
        $xdatos['msg']="El producto seleccionado no posee existencias";
        return ($xdatos);
      }
    }
  }
  else
  {
    $xdatos['typeinfo']="Error";
    $xdatos['msg']="El codigo ingresado no pertenece a ningun producto";
    return ($xdatos);
  }
}
