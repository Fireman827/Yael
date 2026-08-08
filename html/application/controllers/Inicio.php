<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {
	private $tablaUsuario = "usuario";

	public function index(){
		$idSucursal = $this->session->idSucursal;

		$verDashboardCompleto = $this->_verDashboardCompleto();

		if($verDashboardCompleto){
			$resumenDashboard   = $this->_resumenDashboardHoy($idSucursal);
			$pedidosDeliveryHoy = $this->_pedidosDeliveryHoy($idSucursal);

			$cfgLat = TraerUnDato('configuraciones',array("parametroConfiguracion"=>"RESTAURANTE_LAT","idSucursalConfiguracion"=>$idSucursal,"estadoConfiguracion"=>"Activo"));
			$cfgLng = TraerUnDato('configuraciones',array("parametroConfiguracion"=>"RESTAURANTE_LNG","idSucursalConfiguracion"=>$idSucursal,"estadoConfiguracion"=>"Activo"));
		} else {
			$resumenDashboard   = array();
			$pedidosDeliveryHoy = array();
			$cfgLat = null;
			$cfgLng = null;
		}

		$datosVista = array(
			"verDashboardCompleto" => $verDashboardCompleto,
			"resumenDashboard"   => $resumenDashboard,
			"pedidosDeliveryHoy" => $pedidosDeliveryHoy,
			"restauranteLat"     => $cfgLat ? $cfgLat->valorConfiguracion : 13.6929,
			"restauranteLng"     => $cfgLng ? $cfgLng->valorConfiguracion : -89.2182,
		);
		GblPlantilla("tablero",$datosVista,array(),'Inicio');
	}

	// El mapa y el resumen de ventas/gastos solo se muestran a Administrador y Cajero
	private function _verDashboardCompleto(){
		if($this->session->admin == 1){
			return true;
		}
		$datosUsuario = TraerUnDato($this->tablaUsuario,array('idUsuario'=>$this->session->idUsuario));
		if(!$datosUsuario){
			return false;
		}
		$datosRol = TraerUnDato('usuarioRoles',array('idRol'=>$datosUsuario->rolUsuario));
		return $datosRol && $datosRol->nombreRol == 'Cajero';
	}

	/**
	 * Resumen del día (panel de inicio): ventas, gastos
	 * (compras + gastos fijos de caja + planilla pagada) y producto
	 * más vendido. Todo filtrado por sucursal y fecha de hoy.
	 */
	private function _resumenDashboardHoy($idSucursal){
		$hoy = date('Y-m-d');

		$ventas = $this->db->query(
			"SELECT COALESCE(SUM(totalFactura),0) AS total FROM factura
			 WHERE idSucursalFactura = ? AND estadoFactura = 'Cobrado' AND fechaFactura = ?",
			array($idSucursal,$hoy)
		)->row();

		$compras = $this->db->query(
			"SELECT COALESCE(SUM(totalPagar),0) AS total FROM compra
			 WHERE idSucursal = ? AND estadoCompra = 0 AND fechaCompra = ?",
			array($idSucursal,$hoy)
		)->row();

		$gastosFijos = $this->db->query(
			"SELECT COALESCE(SUM(montoCajaMovimiento),0) AS total FROM cajamovimiento
			 WHERE idSucursalCajaMovimiento = ? AND tipoCajaMovimiento = 'Salida'
			   AND estadoCajaMovimiento = 'Activo' AND DATE(fechaRegistroCajaMovimiento) = ?",
			array($idSucursal,$hoy)
		)->row();

		$planilla = $this->db->query(
			"SELECT COALESCE(SUM(liquidoPlanilla),0) AS total FROM planilla
			 WHERE idSucursalPlanilla = ? AND estadoPlanilla = 'Activo'
			   AND pagadoPlanilla = 'true' AND fechaRegistroPlanilla = ?",
			array($idSucursal,$hoy)
		)->row();

		$topProducto = $this->db->query(
			"SELECT p.nombreProducto AS nombre, p.imagenProducto AS imagen, SUM(fd.cantidadFacturaDetalle) AS cantidad
			 FROM facturadetalle fd
			 INNER JOIN factura f ON f.idFactura = fd.idFactura
			 INNER JOIN producto p ON p.idProducto = fd.idProducto
			 WHERE f.idSucursalFactura = ? AND f.estadoFactura = 'Cobrado'
			   AND fd.estadoFacturaDetalle = 'Activo' AND f.fechaFactura = ?
			 GROUP BY fd.idProducto
			 ORDER BY cantidad DESC
			 LIMIT 1",
			array($idSucursal,$hoy)
		)->row();

		// Mismo dia calendario del año pasado, para comparar el avance de ventas de hoy
		// contra la meta implicita de "vender igual o mas que ese mismo dia el año pasado".
		$fechaAnioAnterior = date('Y-m-d', strtotime('-1 year', strtotime($hoy)));
		$ventasAnioAnterior = $this->db->query(
			"SELECT COALESCE(SUM(totalFactura),0) AS total FROM factura
			 WHERE idSucursalFactura = ? AND estadoFactura = 'Cobrado' AND fechaFactura = ?",
			array($idSucursal,$fechaAnioAnterior)
		)->row();

		$ventasTotal          = (float)$ventas->total;
		$comprasTotal         = (float)$compras->total;
		$gastosFijosTotal     = (float)$gastosFijos->total;
		$planillaTotal        = (float)$planilla->total;
		$gastosTotal          = $comprasTotal + $gastosFijosTotal + $planillaTotal;
		$ventasAnioAnteriorTotal = (float)$ventasAnioAnterior->total;

		return array(
			'ventas'              => $ventasTotal,
			'compras'             => $comprasTotal,
			'gastosFijos'         => $gastosFijosTotal,
			'planilla'            => $planillaTotal,
			'gastosTotal'         => $gastosTotal,
			'utilidad'            => $ventasTotal - $gastosTotal,
			'topProducto' => $topProducto ? array(
				'nombre'   => $topProducto->nombre,
				'imagen'   => $topProducto->imagen,
				'cantidad' => (float)$topProducto->cantidad,
			) : null,
			'fechaAnioAnterior'   => $fechaAnioAnterior,
			'ventasAnioAnterior'  => $ventasAnioAnteriorTotal,
			'diferenciaAnio'      => $ventasTotal - $ventasAnioAnteriorTotal,
		);
	}

	/**
	 * Pedidos de delivery del día con coordenadas de geolocalización,
	 * para mostrarlos como pines en el mapa del panel de inicio.
	 */
	private function _pedidosDeliveryHoy($idSucursal){
		$hoy = date('Y-m-d');

		$rows = $this->db->query(
			"SELECT po.idPedidoOnline, po.codigoSeguimientoOnline, po.estadoOnline,
			        po.latitudOnline, po.longitudOnline,
			        p.direccionClientePedido, p.totalPedido, p.nombreClientePedido
			 FROM pedidoonline po
			 INNER JOIN pedido p ON p.idPedido = po.idPedidoRef
			 WHERE po.tipoPedidoOnline = 'domicilio'
			   AND po.latitudOnline IS NOT NULL AND po.longitudOnline IS NOT NULL
			   AND p.idSucursalPedido = ? AND DATE(po.fechaHoraOnline) = ?
			   AND po.estadoOnline != 'Cancelado'
			 ORDER BY po.idPedidoOnline DESC",
			array($idSucursal,$hoy)
		)->result();

		$pedidos = array();
		foreach($rows as $r){
			$pedidos[] = array(
				'idPedidoOnline' => (int)$r->idPedidoOnline,
				'codigo'    => $r->codigoSeguimientoOnline,
				'estado'    => $r->estadoOnline,
				'lat'       => (float)$r->latitudOnline,
				'lng'       => (float)$r->longitudOnline,
				'direccion' => $r->direccionClientePedido,
				'cliente'   => $r->nombreClientePedido,
				'total'     => (float)$r->totalPedido,
			);
		}
		return $pedidos;
	}

	/**
	 * Consulta a Orion Logistics el estado de asignación/conductor de un
	 * pedido a domicilio, identificado por su código de seguimiento
	 * (external_order_id enviado en EnviarPedidoAOrion, Cocina.php).
	 * Devuelve null si Orion no está configurado, no responde, o el
	 * pedido aún no tiene conductor asignado.
	 */
	public function estadoConductor(){
		if(!$this->session->existeSesion){
			echo json_encode(array('codigo'=>403,'mensaje'=>'Sesión inválida.','data'=>null));
			return;
		}
		$codigo = $this->input->post('codigo');
		if(empty($codigo)){
			echo json_encode(array('codigo'=>400,'mensaje'=>'Falta código de seguimiento','data'=>null));
			return;
		}

		$cfgUrl = TraerUnDato('configuraciones',"parametroConfiguracion='ORION_API_URL' AND estadoConfiguracion='Activo'");
		$cfgKey = TraerUnDato('configuraciones',"parametroConfiguracion='ORION_API_KEY' AND estadoConfiguracion='Activo'");
		if(!$cfgUrl || empty($cfgUrl->valorConfiguracion) || !$cfgKey || empty($cfgKey->valorConfiguracion)){
			echo json_encode(array('codigo'=>200,'mensaje'=>'Orion no está configurado.','data'=>null));
			return;
		}

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL            => rtrim($cfgUrl->valorConfiguracion,'/').'/api/pos/orders/'.rawurlencode($codigo),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER     => array('X-API-Key: '.$cfgKey->valorConfiguracion),
			CURLOPT_TIMEOUT        => 6,
			CURLOPT_SSL_VERIFYPEER => false,
		));
		$resp = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if($resp === false || $code != 200){
			echo json_encode(array('codigo'=>200,'mensaje'=>'No se pudo obtener el estado de Orion.','data'=>null));
			return;
		}

		$body = json_decode($resp, true);
		if(!is_array($body)){
			echo json_encode(array('codigo'=>200,'mensaje'=>'Respuesta inválida de Orion.','data'=>null));
			return;
		}

		// Orion puede devolver el pedido directamente o envuelto en "order"/"data".
		$orden = isset($body['order']) ? $body['order'] : (isset($body['data']) ? $body['data'] : $body);
		$conductor = isset($orden['driver']) ? $orden['driver'] : (isset($orden['conductor']) ? $orden['conductor'] : null);

		$data = array(
			'estado'         => isset($orden['status']) ? $orden['status'] : (isset($orden['estado']) ? $orden['estado'] : null),
			'conductorNombre'=> $conductor ? (isset($conductor['name']) ? $conductor['name'] : (isset($conductor['nombre']) ? $conductor['nombre'] : null)) : null,
			'conductorTelefono'=> $conductor ? (isset($conductor['phone']) ? $conductor['phone'] : (isset($conductor['telefono']) ? $conductor['telefono'] : null)) : null,
			'conductorLat'   => $conductor && isset($conductor['lat']) ? (float)$conductor['lat'] : (isset($orden['driver_lat']) ? (float)$orden['driver_lat'] : null),
			'conductorLng'   => $conductor && isset($conductor['lng']) ? (float)$conductor['lng'] : (isset($orden['driver_lng']) ? (float)$orden['driver_lng'] : null),
		);

		echo json_encode(array('codigo'=>200,'mensaje'=>'OK','data'=>$data));
	}
	public function NoEncontrado(){
		GblPlantilla("plantilla/404",array(),array(),'No encontrado');
	}
	public function InicioSesion(){
		$this->load->view('inicioSesion');
	}
	public function CerrarSesion(){
		$this->session->sess_destroy();
		redirect('InicioSesion', 'refresh');
	}
	public function IniciarSesion(){
		$usuarioUsuario = $this->input->post("usuarioUsuario");
		$claveUsuario = $this->input->post("claveUsuario");
		$condicionExiste = array('usuarioUsuario' => $usuarioUsuario);
		if(ExistenDatos($this->tablaUsuario, $condicionExiste)){
			$datosUsuario = TraerUnDato($this->tablaUsuario,$condicionExiste);
			if ($datosUsuario !== false){
				if ($datosUsuario->activoUsuario==1){
					if(DesencriptarClave($datosUsuario->claveUsuario) == $claveUsuario)
					{
						$condicionExisteRol = array('idRol' => $datosUsuario->rolUsuario);
						$datosRol = TraerUnDato('usuarioRoles',$condicionExisteRol);
						$datosUsuarioSesion = array(
							'idUsuario'=>$datosUsuario->idUsuario,
							'idCaja'=> 0,
							'idCorte'=> 0,
							'idTurno'=> 0,
							'usuario'  => $datosUsuario->usuarioUsuario,
							'nombre'=>$datosUsuario->nombreUsuario,
							'autorizadoUsuario'=>$datosUsuario->autorizadoUsuario,
							'idSucursal'=>$datosUsuario->idSucursalUsuario,
							'admin'=>$datosUsuario->adminUsuario,
							'superAdmin'=>$datosUsuario->superAdminUsuario,
							'existeSesion' => true
						);
						$this->session->set_userdata($datosUsuarioSesion);
						$datosRespuesta["tipo"] = "exito";
						$datosRespuesta['titulo']='Información';
						$datosRespuesta['subtitulo']='';
						$datosRespuesta['rol']=($datosUsuario->adminUsuario==1)? 'inicio':$datosRol->rutaRol;

						$condicionPago = array('pagado' => 0);
						$datosPago = TraerUnDatoJoinCampo('pagos',$condicionPago,array(),'DATEDIFF(Concat(anio,"-",mes,"-",dia),CURDATE()) as numeroDias','');
						$numeroDias = $datosPago->numeroDias;
						if ($numeroDias<= 0 && $numeroDias>=-4 ) {
				      if ($numeroDias == -4) {
				        $datosRespuesta["mensaje"] = "Fecha de pago a expirado, por favor realice el pago";
				      }else if ($numeroDias == -3) {
				        $datosRespuesta["mensaje"] = "Fecha de pago expira hoy ";
				      }else {
				        $datosRespuesta["mensaje"] = "Fecha de pago proxima a vencer, dias restantes ".($numeroDias+4);
				      }
				    }
						if ($numeroDias<=-5 ) {
				       $datosRespuesta["mensaje"] = "Fecha de pago  vencida, realice el pago para seguir usando el sistema ";
				    }
						if ($numeroDias>0 ) {
							$datosRespuesta["mensaje"] = "Bienvenido ".$datosUsuario->nombreUsuario;
				    }
						$datosRespuesta['numeroDias']=$numeroDias;
					}
					else{
						$datosRespuesta["tipo"] = "error";
						$datosRespuesta["titulo"] = "Error";
						$datosRespuesta['subtitulo']='';
						$datosRespuesta["mensaje"] = "La clave ingresada es incorrecta!";
					}
				}
				else{
					$datosRespuesta["tipo"] = "error";
					$datosRespuesta["titulo"] = "Error";
					$datosRespuesta['subtitulo']='';
					$datosRespuesta["mensaje"] = "El usuario ha sido desactivado!";
				}
			}
			else{
				$datosRespuesta["tipo"] = "error";
				$datosRespuesta["titulo"] = "Error";
				$datosRespuesta['subtitulo']='';
				$datosRespuesta["mensaje"] = "No se pudo iniciar sesión!";
			}
		}else{
			$datosRespuesta["tipo"] = "error";
			$datosRespuesta["titulo"] = "Error";
			$datosRespuesta['subtitulo']='';
			$datosRespuesta["mensaje"] = "El usuario ingresado no existe!";
		}
		//Se imprimen los datos
		echo json_encode($datosRespuesta);
	}
	public function IniciarSesionClave(){
		$claveUsuario = $this->input->post("claveUsuario");
		$condicionExiste = array('codigoUsuario' => $claveUsuario);
		if(ExistenDatos($this->tablaUsuario, $condicionExiste)){
			$datosUsuario = TraerUnDato($this->tablaUsuario,$condicionExiste);
			if ($datosUsuario !== false){
				if ($datosUsuario->activoUsuario==1){
					$condicionExisteRol = array('idRol' => $datosUsuario->rolUsuario);
					$datosRol = TraerUnDato('usuarioRoles',$condicionExisteRol);
						$datosUsuarioSesion = array(
							'idUsuario'=>$datosUsuario->idUsuario,
							'usuario'  => $datosUsuario->usuarioUsuario,
							'nombre'=>$datosUsuario->nombreUsuario,
							'idSucursal'=>$datosUsuario->idSucursalUsuario,
							'admin'=>$datosUsuario->adminUsuario,
							'superAdmin'=>$datosUsuario->superAdminUsuario,
							'existeSesion' => true
						);
						$this->session->set_userdata($datosUsuarioSesion);
						$datosRespuesta["tipo"] = "exito";
						$datosRespuesta['titulo']='Información';
						$datosRespuesta['subtitulo']='';
						$datosRespuesta['rol']=($datosUsuario->adminUsuario==1)? 'inicio':$datosRol->rutaRol;
						$condicionPago = array('pagado' => 0);
						$datosPago = TraerUnDatoJoinCampo('pagos',$condicionPago,array(),'DATEDIFF(Concat(anio,"-",mes,"-",dia),CURDATE()) as numeroDias','');
						$numeroDias = $datosPago->numeroDias;
						if ($numeroDias>= -1 && $numeroDias<=5 ) {
				      if ($numeroDias == -1) {
				        $datosRespuesta["mensaje"] = "Fecha de pago a expirado, por favor realice el pago";
				      }else if ($numeroDias == 0) {
				        $datosRespuesta["mensaje"] = "Fecha de pago expira hoy ";
				      }else {
				        $datosRespuesta["mensaje"] = "Fecha de pago proxima a vencer, dias restantes ".$numeroDias;
				      }
				    }
						if ($numeroDias<=-2 ) {
				       $datosRespuesta["mensaje"] = "Fecha de pago  vencida, realice el pago para seguir usando el sistema ";
				    }
						if ($numeroDias>5 ) {
							$datosRespuesta["mensaje"] = "Bienvenido ".$datosUsuario->nombreUsuario;
				    }
						$datosRespuesta['numeroDias']=$numeroDias;


				}
				else{
					$datosRespuesta["tipo"] = "error";
					$datosRespuesta["titulo"] = "Error";
					$datosRespuesta['subtitulo']='';
					$datosRespuesta["mensaje"] = "El usuario ha sido desactivado!";
				}
			}
			else{
				$datosRespuesta["tipo"] = "error";
				$datosRespuesta["titulo"] = "Error";
				$datosRespuesta['subtitulo']='';
				$datosRespuesta["mensaje"] = "No se pudo iniciar sesión!";
			}
		}else{
			$datosRespuesta["tipo"] = "error";
			$datosRespuesta["titulo"] = "Error";
			$datosRespuesta['subtitulo']='';
			$datosRespuesta["mensaje"] = "El codigo ingresado no existe!";
		}
		//Se imprimen los datos
		echo json_encode($datosRespuesta);
	}
}
