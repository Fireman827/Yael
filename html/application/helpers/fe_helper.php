<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('GblTraerConfiguracionFe')){
	// Funcion para obtener parametros de configuracion generales
	// $parametro = valor de configuracion que se desea obtener
	function GblTraerConfiguracionFe($parametro="",$idSucursal=""){
		$ci =& get_instance();
		$ci->load->model('CoreModel');
		if($idSucursal == ""){
			$idSucursal = $ci->session->idSucursal;
		}
		$entorno = GblTraerConfiguracion("entornoFE");
		$conf = $ci->CoreModel->TraerConfiguracionFe($parametro,$idSucursal);
		if($conf !== false){
			return $conf->$entorno;
		} else{
			return "No se encontro el parametro";
		}
	}
}

if (!function_exists('generarUuid')){
	// Funcion para generar un UUID
	function generarUuid($data = null) {
		// Generate 16 bytes (128 bits) of random data or use the data passed into the function.
		$data = $data ?? random_bytes(16);
		assert(strlen($data) == 16);
		// Set version to 0100
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		// Set bits 6-7 to 10
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		// Output the 36 character UUID.
		return mb_strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
	}

}

if (!function_exists('generarNumeroControl')){
	// Funcion para obtener el numero de control
	// $parametros = codigo de documento a generar, numero correlativo
	function generarNumeroControl($codigo,$numero){
		$identificacion = GblTraerConfiguracionFe("identificacion");
		$complemento = str_pad($numero, 15,"0",STR_PAD_LEFT);
		$control = "DTE-".$codigo."-".$identificacion."-".$complemento;
		// if($codigo == "03"){
			// $control = "DTE-03-".$identificacion."-".$complemento;
		// } else if($codigo == "01") {
			// $control = "DTE-01-".$identificacion."-".$complemento;
		// }
		return mb_strtoupper($control);
	}
}

if (!function_exists('firmarDte')){
	// Funcion para firmar el dte
	// $parametros = array con el json del documento a generar, nit del titular del certificado, clave para generacion de otrosDocumentos
	//configurada en la consola, url del aplicativo para firmar (debe confugurarse segun el certificado provisto por mh)
	//B3n@v1d35
	//Retorna el dte firmado para su trasmision
	/////////////////////////////////////////////////////////////////////////////////////
	function firmarDte($dte){
		$urlFirma = GblTraerConfiguracionFe("urlFirma");
		$nit = GblTraerConfiguracionFe("usuario");
		$clave = GblTraerConfiguracionFe("clave");

		$data = array(
			'nit' =>$nit,
			'activo' =>1,
			'passwordPri' =>$clave,
			'dteJson' => $dte
		);

		$data = json_encode($data);

		$options = array('http' => array(
			'method'  => 'POST',
			'content' => $data,
			'header'  =>
			"Content-Type: application/json;charset=UTF-8\r\n" .
			"Content-Length: " . strlen($data) . "\r\n"
		));
		$context  = stream_context_create($options);
		$response = file_get_contents($urlFirma, false, $context);
		$res = $arr = json_decode($response, true);
		return $res["body"];
	}
	////////////////////////////////////////////////////////////////////////////////////
}

if (!function_exists('loginMH')){
	// Funcion generar token de acceso (TOKEN VALIDO 24 HORAS)
	// $parametros = nit del titular del certificado, clave para generacion de otrosDocumentos,
	//url del endpoint del mh
	//12172312941032
	//B3n@v1d35
	//	$url = "https://apitest.dtes.mh.gob.sv/seguridad/auth";
	//Retorna el token de acceso para envio de documentos
	/////////////////////////////////////////////////////////////////////////////////////
	function loginMH(){
		$urlLogin = GblTraerConfiguracionFe("urlLogin");
		$usuario = GblTraerConfiguracionFe("usuario");
		$clave = GblTraerConfiguracionFe("claveApi");

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$urlLogin);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'DMS');
		curl_setopt($ch, CURLOPT_POSTFIELDS,
		"user=".$usuario."&pwd=".$clave);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		$response = curl_exec($ch);
		curl_close($ch);

		$res = $arr = json_decode($response, true);
		if($res["status"] == "OK"){
			$response = array(
				"token" => $res["body"]["token"],
				"status" => "OK"
			);
		} else {
			$response = array(
				"msj" => $res["body"]["descripcionMsg"],
				"status" => "NO"
			);
		}
		return $response;
	}
	////////////////////////////////////////////////////////////////////////////////////
}

if (!function_exists('enviarDocumento')){
	// Funcion para fenviar la informacion a mh
	// $parametros = tokenm, ambiente, numero de envio, version del dte, tipo del dte, dte firmado
	//12172312941032
	//B3n@v1d35
	//	$url = "https://apitest.dtes.mh.gob.sv/fesv/recepciondte";
	//Retorna el token de acceso para envio de documentos
	/////////////////////////////////////////////////////////////////////////////////////
	function enviarDocumento($envio,$version,$tipo,$dteFirmado){

		$ambiente = GblTraerConfiguracionFe("ambiente");
		$urlEnvio = GblTraerConfiguracionFe("urlEnvio");
		$entorno = GblTraerConfiguracion("entornoFE");

		$ci =& get_instance();
		$ci->load->model('CoreModel');
		$confToken = $ci->CoreModel->TraerUnDato("FE_Configuraciones",array("parametro" => "token"));
		$fechaUltima = new DateTime($confToken->fechaRegistro);
		$fechaActual = new DateTime(date("Y-m-d H:i:s"));
		$diferencias = $fechaUltima->diff($fechaActual);
		$token ="";
		$tiempo = ($diferencias->d*24) + ($diferencias->h) + ($diferencias->m/60) + ($diferencias->s/3600);
		if($tiempo < 23){
			$token = $confToken->$entorno;
		}
		if($token == ""){
			$confTokenNew  = loginMH();
			if($confTokenNew["status"] =="OK"){
				$token = $confTokenNew["token"];
			} else {
				return $confTokenNew;
			}
			$array_token = array(
				$entorno => $token,
				'fechaRegistro' => date("Y-m-d H:i:s"),
			);
			$ci->CoreModel->EditarDatos('FE_Configuraciones',$array_token,array('parametro' => 'token'));
		}
		$authorization = "Authorization: ".$token;
		$data = array(
			'ambiente' => $ambiente,
			'idEnvio' => $envio,
			'version' => $version,
			'tipoDte' => $tipo,
			'documento' => $dteFirmado,
		);
		$data = json_encode($data);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$urlEnvio);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'DMS');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($response, true);
		if($res["estado"] == "PROCESADO"){
			$response = array(
				"codigoGeneracion" => $res["codigoGeneracion"],
				"selloRecibido" => $res["selloRecibido"],
				"fhProcesamiento" => $res["fhProcesamiento"],
				"status" => "OK"
			);
		} else {
			$response = array(
				"descripcionMsg" => $res["descripcionMsg"],
				"observaciones" => $res["observaciones"],
				"status" => "NO"
			);
		}
		return $response;
	}
	////////////////////////////////////////////////////////////////////////////////////
}
if (!function_exists('anularDocumento')){
	// Funcion para fenviar la informacion a mh
	// $parametros = tokenm, ambiente, numero de envio, version del dte, tipo del dte, dte firmado
	//12172312941032
	//B3n@v1d35
	//	$url = "https://apitest.dtes.mh.gob.sv/fesv/recepciondte";
	//Retorna el token de acceso para envio de documentos
	/////////////////////////////////////////////////////////////////////////////////////
	function anularDocumento($envio,$version,$dteFirmado){

		$ambiente = GblTraerConfiguracionFe("ambiente");
		$urlEnvio = GblTraerConfiguracionFe("urlAnulacion");
		$entorno = GblTraerConfiguracion("entornoFE");

		$ci =& get_instance();
		$ci->load->model('CoreModel');
		$confToken = $ci->CoreModel->TraerUnDato("FE_Configuraciones",array("parametro" => "token"));
		$fechaUltima = new DateTime($confToken->fechaRegistro);
		$fechaActual = new DateTime(date("Y-m-d H:i:s"));
		$diferencias = $fechaUltima->diff($fechaActual);
		$token ="";
		$tiempo = ($diferencias->d*24) + ($diferencias->h) + ($diferencias->m/60) + ($diferencias->s/3600);
		if($tiempo < 23){
			$token = $confToken->$entorno;
		}
		if($token == ""){
			$confTokenNew  = loginMH();
			if($confTokenNew["status"] =="OK"){
				$token = $confTokenNew["token"];
			} else {
				return $confTokenNew;
			}
			$array_token = array(
				$entorno => $token,
				'fechaRegistro' => date("Y-m-d H:i:s"),
			);
			$ci->CoreModel->EditarDatos('FE_Configuraciones',$array_token,array('parametro' => 'token'));
		}
		$authorization = "Authorization: ".$token;
		$data = array(
			'ambiente' => $ambiente,
			'idEnvio' => $envio,
			'version' => $version,
			'documento' => $dteFirmado,
		);
		$data = json_encode($data);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$urlEnvio);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'DMS');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($response, true);
		if($res["estado"] == "PROCESADO"){
			$response = array(
				"codigoGeneracion" => $res["codigoGeneracion"],
				"selloRecibido" => $res["selloRecibido"],
				"fhProcesamiento" => $res["fhProcesamiento"],
				"status" => "OK"
			);
		} else {
			$response = array(
				"descripcionMsg" => $res["descripcionMsg"],
				"observaciones" => $res["observaciones"],
				"status" => "NO"
			);
		}
		return $response;
	}
	////////////////////////////////////////////////////////////////////////////////////
}
if (!function_exists('contingirDocumento')){
	// Funcion para fenviar la informacion a mh
	// $parametros = tokenm, ambiente, numero de envio, version del dte, tipo del dte, dte firmado
	//12172312941032
	//B3n@v1d35
	//	$url = "https://apitest.dtes.mh.gob.sv/fesv/recepciondte";
	//Retorna el token de acceso para envio de documentos
	/////////////////////////////////////////////////////////////////////////////////////
	function contingirDocumento($version,$dteFirmado,$nit){

		$ambiente = GblTraerConfiguracionFe("ambiente");
		$urlEnvio = GblTraerConfiguracionFe("urlContingencia");
		$entorno = GblTraerConfiguracion("entornoFE");

		$ci =& get_instance();
		$ci->load->model('CoreModel');
		$confToken = $ci->CoreModel->TraerUnDato("FE_Configuraciones",array("parametro" => "token"));
		$fechaUltima = new DateTime($confToken->fechaRegistro);
		$fechaActual = new DateTime(date("Y-m-d H:i:s"));
		$diferencias = $fechaUltima->diff($fechaActual);
		$token ="";
		$tiempo = ($diferencias->d*24) + ($diferencias->h) + ($diferencias->m/60) + ($diferencias->s/3600);
		if($tiempo < 23){
			$token = $confToken->$entorno;
		}
		if($token == ""){
			$confTokenNew  = loginMH();
			if($confTokenNew["status"] =="OK"){
				$token = $confTokenNew["token"];
			} else {
				return $confTokenNew;
			}
			$array_token = array(
				$entorno => $token,
				'fechaRegistro' => date("Y-m-d H:i:s"),
			);
			$ci->CoreModel->EditarDatos('FE_Configuraciones',$array_token,array('parametro' => 'token'));
		}
		$authorization = "Authorization: ".$token;
		$data = array(
			'nit' => $nit,
			'documento' => $dteFirmado,
		);
		$data = json_encode($data);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$urlEnvio);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'DMS');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization, 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($response, true);
		if($res["estado"] == "RECIBIDO"){
			$response = array(
				"selloRecibido" => $res["selloRecibido"],
				"fechaHora" => $res["fechaHora"],
				"status" => "OK"
			);
		} else {
			$response = array(
				"mensaje" => $res["mensaje"],
				"observaciones" => $res["observaciones"],
				"status" => "NO"
			);
		}
		return $response;
	}
	////////////////////////////////////////////////////////////////////////////////////
}
