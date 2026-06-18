<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pago extends CI_Controller {
	private $tabla = "pagos";
	private $tablaPagosDetalle = "pagosDetalle";
	//private $tablaPermisos = "usuarioPermisos";
	private $controlador = "Pago";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
		$this->load->add_package_path(APPPATH . 'third_party/upload_file');
		$this->load->library('uploadFile');
	}
	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
		} else{
			$titulo = "Pagos";
			// INICIO SUCURSAL
			$selectSucursal = '';
			if ($this->session->admin) {
				$selectSucursal = TraerDatos('sucursal');
			}else if ($this->session->gerente) {
				$joinUsuarioGerente = array(
					array(
						"tabla" => "usuarioGerente",
						"condicion" => "usuarioGerente.idSucursal = sucursal.idSucursal"
					)
				);
				$selectSucursal = TraerDatosJoin("sucursal",array('idUsuario' => $this->session->idUsuario),'sucursal.idSucursal ASC',$joinUsuarioGerente);
			}else {
				$selectSucursal = TraerDatos('sucursal',array('idSucursal' => $this->session->idSucursal));
			}
			// FIN SUCURSAL
			$datosVista = array(
				"titulo"=> $titulo,
				"icono"=> "fa fa-users",
				"botones" => array(
				),
				"encabezados"=>array(
					"N"=>1,
					"Numero"=>2,
					"Cliente"=>2,
					"Monto"=>1,
					"Metodo Pago"=>2,
					"Periodo"=>1,
					"Fecha Pago"=>2,
					"Acciones"=>1,
				),
				"admin"=>$this->session->admin,
				"idSucursal"=>$this->session->idSucursal,
				"sucursales"=>$selectSucursal,
			);
			$extras = array(
				'css' => array(),
				'js' => array(
					"scripts/pago.js"
				),
			);
			GblPlantilla("plantilla/admin",$datosVista,$extras,$titulo);
		}
	}

	function PagoMostrar(){
		// Espacio propio del plugin data tabla
		$draw = intval($this->input->post("draw"));
		$desdeFilas = intval($this->input->post("start"));
		$cantidadFilas = intval($this->input->post("length"));
		$order = $this->input->post("order");
		$busquedaAreglo = $this->input->post("search");
		$busquedaParametro = $busquedaAreglo['value'];
		$col = 0;
		$ordenDireccion = "";
		if (!empty($order)){
			foreach ($order as $o){
				$col = $o['column'];
				$ordenDireccion = $o['dir'];
			}
		}
		if ($ordenDireccion != "asc" && $ordenDireccion != "desc"){
			$ordenDireccion = "asc";
		}
		//Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		$columnasValidas = array(
			0 => 'total',
			1 => 'metodoPago',
			2 => 'fechaPago',
		);
		//Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
		if (!isset($columnasValidas[$col])){
			$ordenCampos = null;
		} else {
			$ordenCampos = $columnasValidas[$col];
		}
		// Fin espacio del data tabla
		$sucursal = $this->input->post("sucursal");
		$this->session->idSucursal = $sucursal;
		$join = array(
		);
		$condicion = array('idSucursal' => $sucursal,'pagado =' => 1);
		$pagos = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion, $condicion,$join);
		//Lectura de datos de la base para mostrar en el datatabla
		if ($pagos != 0){
			$datosMostrar = array();
			$iN=0;
			foreach ($pagos as $pago){
				$iN++;
				$menuOpciones = "
				<div class='input-group-prepend'>
				<button data-toggle='dropdown' class='btn btn-".GblTraerConfiguracion('colorComponentes')." btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
				<div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";
				$funcion ="PagoRecibo";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item' href='". base_url()."PagoRecibo/".md5($pago->idPago)."' target='_blank'><i class='far fa-ticket' ></i> Recibo</a>";
				}
				$menuOpciones .= "
				</div>
				</div>";
				$datosMostrar[] = array(
					$iN,
					$pago->idPago,
					$pago->nombre,
					'$'.number_format($pago->total,2,".",""),
					$pago->metodoPago,
					meses($pago->mes).' '.$pago->anio,
					Fecha_D_M_A($pago->fechaPago),
					$menuOpciones,
				);
			}
			$totalPagos = $this->core->TraerTotalDatosJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$join);
			$output = array(
				"draw" => $draw,
				"recordsTotal" => $totalPagos,
				"recordsFiltered" => $totalPagos,
				"data" => $datosMostrar
			);
		} else {
			$output = array(
				"draw" => $draw,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => 0
			);
		}
		echo json_encode($output);
		exit();
	}

	function PagoAgregar(){
		if($this->input->method(TRUE) == "GET"){
			$condicionPagoDias = array('pagado' => 0);
			$datosPagoDias = TraerUnDatoJoinCampo('pagos',$condicionPagoDias,array(),'DATEDIFF(Concat(anio,"-",mes,"-",dia),CURDATE()) as numeroDias','');
			$numeroDias = $datosPagoDias->numeroDias;

			$totalPagar = 0;
			$datosCuentasBancarias = TraerDatos('cuentasBancarias', array('estado' => 'Activo'));
			$condicionPago = array('pagado' => 0);
			$datosPago = TraerUnDato('pagos',$condicionPago);
			$fechaPago = $datosPago->anio.'-'.$datosPago->mes.'-'.$datosPago->dia;
			$fechaInicio = strtotime('-1 months', strtotime($fechaPago));
			$fechaInicio = date('Y-m-d' , $fechaInicio);
			$fechaFin = strtotime('-1 days', strtotime($fechaPago));
			$fechaFin = date('Y-m-d' , $fechaFin);

			$alojamientoMensual = GblTraerConfiguracion('alojamientoMensual');
			$valorTransaccion = GblTraerConfiguracion('valorTransaccion');
			$facturacionElectronica = GblTraerConfiguracion('facturacion_electronica');
			$tipoCobroFe = GblTraerConfiguracion('tipoCobroFe');

			$numeroFactura = 0;
			$totalPagarFactura = 0;
			$numeroCreditoFiscal = 0;
			$totalPagarCreditoFiscal = 0;
			$numeroSujetoExcluido = 0;
			$totalPagarSujetoExcluido = 0;
			if ($facturacionElectronica == 'Si') {
				if ($tipoCobroFe == 'Por Documento') {
					$numeroFactura = TraerTotalDatos('factura',"tipoDocumentoFactura='FAC' AND fechaFactura BETWEEN '$fechaInicio' AND '$fechaFin'");
					$totalPagarFactura = $numeroFactura*$valorTransaccion;
					$numeroCreditoFiscal = TraerTotalDatos('factura',"tipoDocumentoFactura='CCF' AND fechaFactura BETWEEN '$fechaInicio' AND '$fechaFin'");
					$totalPagarCreditoFiscal = $numeroCreditoFiscal*$valorTransaccion;
					$numeroSujetoExcluido = TraerTotalDatos('compras',"tipoCompra='FSE' AND fecha BETWEEN '$fechaInicio' AND '$fechaFin'");
					$totalPagarSujetoExcluido = $numeroSujetoExcluido*$valorTransaccion;
				}
			}

			$totalPagar = $alojamientoMensual+$totalPagarFactura+$totalPagarCreditoFiscal+$totalPagarSujetoExcluido;
			$datosVista = array(
				'datosPago' => $datosPago,
				'numeroDias' => $numeroDias,
				'datosCuentasBancarias' => $datosCuentasBancarias,
				'facturacionElectronica' => $facturacionElectronica,
				'alojamientoMensual' => $alojamientoMensual,
				'valorTransaccion' => $valorTransaccion,
				'numeroFactura' => $numeroFactura,
				'totalPagarFactura' => $totalPagarFactura,
				'numeroCreditoFiscal' => $numeroCreditoFiscal,
				'totalPagarCreditoFiscal' => $totalPagarCreditoFiscal,
				'numeroSujetoExcluido' => $numeroSujetoExcluido,
				'totalPagarSujetoExcluido' => $totalPagarSujetoExcluido,
				'totalPagar' => $totalPagar,
			);
			$this->load->view('pago/pago',$datosVista);
		}
	}
	function PagoAgregarTransferencia(){
		if($this->input->method(TRUE) == "POST"){
			$idPago = $this->input->post("idPagoTransferencia");
			$condicionPago = array('idPago' => $idPago);
			$datosPago = TraerUnDato('pagos',$condicionPago);
			$diaBase = $datosPago->dia;
			$mesBase = $datosPago->mes;
			$anioBase = $datosPago->anio;
			$idCuentaBancaria = $this->input->post("idCuentaBancaria");
			$nombre = $this->input->post("nombreTransferencia");
			$correo = $this->input->post("correoTransferencia");
			$total = $this->input->post("totalTransferencia");
			$detallePago = json_decode($this->input->post("detallePagoTransferencia"),true);
			$condicionExiste = array('idPago' => $idPago,'pagado' => 1);
			$existe = ExistenDatos($this->tabla, $condicionExiste);
			if($existe==0){
				if ($_FILES['capturaTransferencia']['name'] != "") {
					$nombreImagen = "_".uniqid();
					$this->imagen = new UploadFile();
					$subirImagen = $this->imagen->subirArchivo('capturaTransferencia',$nombreImagen,"./vendors/core/img/capturaTransferencia/");
					if ($subirImagen['response']) {
						$capturaTransferencia = 'vendors/core/img/capturaTransferencia/'.$subirImagen['info']['file_name'];
						$imageType = $subirImagen['info']['image_type'];
						$fileName = $subirImagen['info']['file_name'];
						$datosPago = array(
							'fechaPago' => date("Y-m-d"),
							'horaPago' => date("H:i:s"),
							'pagado'=>1,
							"idCuentaBancaria"=>$idCuentaBancaria,
							"nombre"=>$nombre,
							"correo"=>$correo,
							"total"=>$total,
							"metodoPago"=>'Trasferencia',
							"capturaTransferencia"=>$capturaTransferencia,
							"idSucursal"=>$this->session->idSucursal,
						);
						IniciarTransaccion();
						$condicionPago = array("idPago" => $idPago);
						$editar = EditarDatos($this->tabla,$datosPago,$condicionPago);
						if($editar){
							$error = false;
							foreach($detallePago as $detalle){
								$datosDetallePago = array(
									"idPago" => $idPago,
									"cantidad" => $detalle["cantidadDetallePago"],
									"precio" => $detalle["precioDetallePago"],
									"subtotal" => $detalle["subtotalDetallePago"],
									"descripcion" => $detalle["descripcionDetallePago"],
								);
								$guardarDetallePago = GuardarDatos($this->tablaPagosDetalle,$datosDetallePago);
								if(!$guardarDetallePago){
									$error = true;
								}
							}
							if($mesBase == 12){
								$mesNuevo = 1;
								$anioNuevo = $anioBase+1;
							}else{
								$mesNuevo = $mesBase+1;
								$anioNuevo = $anioBase;
							}
							$datosProximoPago = array(
								"mes" => $mesNuevo,
								"anio" => $anioNuevo,
								"dia" => $diaBase,
							);
							$guardarDetallePago = GuardarDatos($this->tabla,$datosProximoPago);
							if(!$error AND $guardarDetallePago){
								//------ Correo Electronico ------//
								$condicionPagoCorreo = array('idPago' => $idPago);
								$joinPagoCorreo = array(
									array(
										"tabla" => "cuentasBancarias",
										"condicion" => "cuentasBancarias.idCuentaBancaria = pagos.idCuentaBancaria",
										"campos" => "cuentasBancarias.nombreBanco",
										"tipo" => "left"
									)
								);
								$datosPagoCorreo = TraerUnDatoJoin("pagos",$condicionPagoCorreo,$joinPagoCorreo);
								$condicionPagoDetalleCorreo = array('idPago' => $idPago);
								$datosPagoDetalleCorreo = TraerDatos('pagosDetalle', $condicionPagoDetalleCorreo,'cantidad asc');
								$datosVistaRecibo = array(
									'datosPago' => $datosPagoCorreo,
									'datosPagoDetalle' => $datosPagoDetalleCorreo,
								);

								$attachment =array(
									array("name" => $fileName, "extension" => $imageType, "url" => $capturaTransferencia),
								);
								$contenido = $this->load->view("pago/recibo", $datosVistaRecibo,TRUE);
								$arrayto = array(
									array('email' => trim($correo),'name' => $nombre),
									array('email' => trim(GblTraerConfiguracion('correoEnvioComprobante')),'name' => GblTraerConfiguracion('nombreEnvioComprobante'))
								);
								$send = MailSend($arrayto,'Comprobante de pago',$contenido,$attachment);
								//------ Correo Electronico ------//

								EjecutarTransaccion();
								$datosRespuesta["codigo"]=200;
								$datosRespuesta["idPago"]=md5($idPago);
							} else{
								DeshacerTransaccion();
								$datosRespuesta["codigo"]=501;
							}
						} else {
							//La acción no pudo ser realizada
							DeshacerTransaccion();
							$datosRespuesta["codigo"]=402;
						}
					}else {
						$datosRespuesta["codigo"] = 501;
					}
				}else {
					$datosRespuesta["codigo"] = 500;
				}

			} else {
				//La acción no se pudo realizar porque ya existe un registro con los mismos datos
				$datosRespuesta["codigo"]=400;
			}
			echo json_encode($datosRespuesta);
		}
	}

	function PagoAgregarTarjeta(){
		if($this->input->method(TRUE) == "POST"){
			$idPago = $this->input->post("idPagoTarjeta");
			$condicionPago = array('idPago' => $idPago);
			$datosPago = TraerUnDato('pagos',$condicionPago);
			$diaBase = $datosPago->dia;
			$mesBase = $datosPago->mes;
			$anioBase = $datosPago->anio;
			$nombre = $this->input->post("nombreTarjeta");
			$correo = $this->input->post("correoTarjeta");
			$numeroTarjeta = str_replace(' ','',$this->input->post("numeroTarjeta"));
			$mmaa = explode("/",$this->input->post("mmaaTarjeta"));
			$mm = $mmaa[0];
			$aa = "20".$mmaa[1];
			$ccv = $this->input->post("ccvTarjeta");
			$total = $this->input->post("totalTarjeta");
			$detallePago = json_decode($this->input->post("detallePagoTarjeta"),true);
			$condicionExiste = array('idPago' => $idPago,'pagado' => 1);
			$existe = ExistenDatos($this->tabla, $condicionExiste);
			if($existe==0){
				$curlWompi = curl_init();
				$clientIdWompi = GblTraerConfiguracion('clientIdWompi');
				$clientSecretWompi = GblTraerConfiguracion('clientSecretWompi');
				curl_setopt_array($curlWompi, array(
					CURLOPT_URL => "https://id.wompi.sv/connect/token",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=$clientIdWompi&client_secret=$clientSecretWompi&audience=wompi_api",
					CURLOPT_HTTPHEADER => array(
						"content-type: application/x-www-form-urlencoded"
					),
				));
				$responseWompi = curl_exec($curlWompi);
				$errorWompi = curl_error($curlWompi);
				curl_close($curlWompi);
				if ($errorWompi) {
					$datosRespuesta["tipo"]='error';
					$datosRespuesta["mensaje"]='¡No se puede procesar el pago!';
					$datosRespuesta["err"] = $errorWompi;
				}else {
					$dataDecodeWompi = json_decode($responseWompi,true);
					$accessToken = $dataDecodeWompi['access_token'];
					$jsonDataWompi = array(
						"tarjetaCreditoDebido" => array(
							"numeroTarjeta"=> $numeroTarjeta,
							"cvv"=> $ccv,
							"mesVencimiento"=> $mm,
							"anioVencimiento"=> $aa
						),
						"monto"=> $total,
						"emailCliente" => $correo,
						"nombreCliente" => $nombre,
						"formaPago"=> "PagoNormal",
					);
					$curlWompi2 = curl_init();
					curl_setopt_array($curlWompi2, array(
						CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra",
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 90,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => json_encode($jsonDataWompi),
						CURLOPT_HTTPHEADER => array(
							"authorization: Bearer $accessToken",
							"Content-Type: application/json-patch+json"
						),
					));
					$responseWompi2 = curl_exec($curlWompi2);
					$errorWompi2 = curl_error($curlWompi2);
					curl_close($curlWompi2);
					$res = "";
					if ($errorWompi2){
						$datosRespuesta["tipo"]='error';
						$datosRespuesta["mensaje"]='¡No se puede procesar el pago 2!';
						$xdatos["err"] = $errorWompi2;
					}else {
						$res = json_decode($responseWompi2,True);
						if (in_array("esAprobada", $res)) {
							if ($res["esAprobada"]) {
								$idTransaccion = $res["idTransaccion"];
								$codigoAutorizacion = $res["codigoAutorizacion"];
								$datosPago = array(
									'fechaPago' => date("Y-m-d"),
									'horaPago' => date("H:i:s"),
									'pagado'=>1,
									"nombre"=>$nombre,
									"correo"=>$correo,
									"total"=>$total,
									"metodoPago"=>'Tarjeta',
									"idTransaccion"=> $idTransaccion,
									"codigoAutorizacion"=> $codigoAutorizacion,
									"idSucursal"=>$this->session->idSucursal,
								);
								IniciarTransaccion();
								$condicionPago = array("idPago" => $idPago);
								$editar = EditarDatos($this->tabla,$datosPago,$condicionPago);
								if($editar){
									$error = false;
									foreach($detallePago as $detalle){
										$datosDetallePago = array(
											"idPago" => $idPago,
											"cantidad" => $detalle["cantidadDetallePago"],
											"precio" => $detalle["precioDetallePago"],
											"subtotal" => $detalle["subtotalDetallePago"],
											"descripcion" => $detalle["descripcionDetallePago"],
										);
										$guardarDetallePago = GuardarDatos($this->tablaPagosDetalle,$datosDetallePago);
										if(!$guardarDetallePago){
											$error = true;
										}
									}
									if($mesBase == 12){
										$mesNuevo = 1;
										$anioNuevo = $anioBase+1;
									}else{
										$mesNuevo = $mesBase+1;
										$anioNuevo = $anioBase;
									}
									$datosProximoPago = array(
										"mes" => $mesNuevo,
										"anio" => $anioNuevo,
										"dia" => $diaBase,
									);
									$guardarDetallePago = GuardarDatos($this->tabla,$datosProximoPago);

									if(!$error AND $guardarDetallePago){
										//------ Correo Electronico ------//
										$condicionPagoCorreo = array('idPago' => $idPago);
										$joinPagoCorreo = array(
											array(
												"tabla" => "cuentasBancarias",
												"condicion" => "cuentasBancarias.idCuentaBancaria = pagos.idCuentaBancaria",
												"campos" => "cuentasBancarias.nombreBanco",
												"tipo" => "left"
											)
										);
										$datosPagoCorreo = TraerUnDatoJoin("pagos",$condicionPagoCorreo,$joinPagoCorreo);
										$condicionPagoDetalleCorreo = array('idPago' => $idPago);
										$datosPagoDetalleCorreo = TraerDatos('pagosDetalle', $condicionPagoDetalleCorreo,'cantidad asc');
										$datosVistaRecibo = array(
											'datosPago' => $datosPagoCorreo,
											'datosPagoDetalle' => $datosPagoDetalleCorreo,
										);

										$contenido = $this->load->view("pago/recibo", $datosVistaRecibo,TRUE);
										$arrayto = array(
											array('email' => trim($correo),'name' => $nombre),
											array('email' => trim(GblTraerConfiguracion('correoEnvioComprobante')),'name' => GblTraerConfiguracion('nombreEnvioComprobante'))
										);
										$send = MailSend($arrayto,'Comprobante de pago',$contenido,array());
										//------ Correo Electronico ------//

										EjecutarTransaccion();
										$datosRespuesta["tipo"]='exito';
										$datosRespuesta["mensaje"]='¡Transacción realizada con exito!';
										$datosRespuesta["idPago"]=md5($idPago);
									} else{
										DeshacerTransaccion();
										$datosRespuesta["tipo"]='error';
										$datosRespuesta["mensaje"]='¡Transacción no pudo ser realizada!';
									}
								} else {
									//La acción no pudo ser realizada
									DeshacerTransaccion();
									$datosRespuesta["tipo"]='advertencia';
									$datosRespuesta["mensaje"]='¡Transacción no pudo ser realizada!';
								}
							}else {
								$xdatos["tipo"] = "error";
								$xdatos["mensaje"] = "No se pudo procesar el pago";
							}
						}else {
							if(isset($res["mensajes"])){
								$xdatos["tipo"] = "error";
								$xdatos["mensaje"] = $res["mensajes"][0];
								$xdatos["error"] = _error();
							}else {
								$xdatos["tipo"] = "error";
								$xdatos["mensaje"] = "Fallo al pagar";
								$xdatos["error"] = _error();
							}
						}
					}
				}
			} else {
				//La acción no se pudo realizar porque ya existe un registro con los mismos datos
				$datosRespuesta["tipo"]='advertencia';
				$datosRespuesta["mensaje"]='¡Ya existe un registro con estos datos!';
			}
			echo json_encode($datosRespuesta);
		}
	}

	function PagoRecibo($idPago){
		if($this->input->method(TRUE) == "GET"){
			$condicionPago = array('md5(idPago)' => $idPago);
			$joinPago = array(
				array(
					"tabla" => "cuentasBancarias",
					"condicion" => "cuentasBancarias.idCuentaBancaria = pagos.idCuentaBancaria",
					"campos" => "cuentasBancarias.nombreBanco",
					"tipo" => "left"
				)
			);
			$datosPago = TraerUnDatoJoin("pagos",$condicionPago,$joinPago);
			$condicionPagoDetalle = array('md5(idPago)' => $idPago);
			$datosPagoDetalle = TraerDatos('pagosDetalle', $condicionPagoDetalle,'cantidad asc');
			$datosVista = array(
				'datosPago' => $datosPago,
				'datosPagoDetalle' => $datosPagoDetalle,
			);
			$this->load->view('pago/recibo',$datosVista);
		}
	}
}
/* End of file Usuarios.php */
