<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cocina extends CI_Controller {

	private $tabla = "";
	private $tablaPermisos = "";
	private $controlador = "Cocina";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
		$this->load->helper('Whatsapp');
	}
	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla2("plantilla2/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){

				$impresoras = TraerDatos("impresora",array("cuentaImpresora" => 0, "estadoImpresora" => "Activo", "idSucursalImpresora" => $this->session->idSucursal));

				$titulo = "Cocina";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-monitor",
					"controlador"=> "Cocina",
					"proceso"=> "Vista",
					"impresoras"=> $impresoras,
				);
				$extras = array(
					'css' => array(
						"vendors/core/css/touch.css",
					),
					'js' => array(
						"vendors/core/js/main.js",
						"scripts/cocina.js?q=".uniqid(),
					),
				);
				GblPlantilla2("cocina/MostrarCocina",$datosVista,$extras,$titulo);
			}
		}
	}

	/**************************************************************/
	/****************************COCINA****************************/
	/**************************************************************/
	public function OrdenesCocina(){
		$idImpresora = $this->input->post("idImpresora");

		$condicionCuentas = array(
			"estadoPedido"=>"Pendiente", 
			"estadoPedidoDetalle"=>"Activo", 
			"idSucursalPedido"=>$this->session->idSucursal
		);
		if($idImpresora != "All"){
			$condicionCuentas = array(
				"estadoPedido"=>"Pendiente", 
				"estadoPedidoDetalle"=>"Activo",
				"md5(producto.impresoraProducto) " => $idImpresora, 
				"idSucursalPedido"=>$this->session->idSucursal
			);
		}
		else{
			$condicionCuentas = array(
				"estadoPedido"=>"Pendiente", 
				"estadoPedidoDetalle"=>"Activo", 
				"idSucursalPedido"=>$this->session->idSucursal
			);	
		}
		// Nota: no se usa TraerDatosJoin aquí porque su agrupación (group_by) está deshabilitada;
		// con MIN(fechaHoraPedidoDetalle) sin GROUP BY, MySQL colapsa el resultado a una sola fila
		// "al azar" en vez de una tarjeta por pedido (esto ocultaba pedidos, ej. los de la web).
		$this->db->select("pedido.*, MIN(pedidoDetalle.fechaHoraPedidoDetalle) AS fecha, zonaMesa.nombreZonaMesa, zona.nombreZona, usuario.nombreUsuario");
		$this->db->from('pedido');
		$this->db->join('pedidoDetalle', 'pedido.idPedido = pedidoDetalle.idPedido', 'inner');
		$this->db->join('producto', 'producto.idProducto = pedidoDetalle.idProductoPedidoDetalle', 'left');
		$this->db->join('zonaMesa', 'pedido.idMesaPedido = zonaMesa.idZonaMesa', 'left');
		$this->db->join('zona', 'pedido.idZonaPedido = zona.idZona', 'left');
		$this->db->join('usuario', 'pedido.idUsuarioPedido = usuario.idUsuario', 'inner');
		$this->db->where($condicionCuentas);
		$this->db->group_by('pedido.idPedido');
		$this->db->order_by('fecha', 'ASC');
		$consultaCuentas = $this->db->get();
		$cuentas = ($consultaCuentas->num_rows() > 0) ? $consultaCuentas->result() : false;
		$datos = "";

		$div = "";
			if($cuentas !== false){
				$div.='<div class="row">';
				$pd=0;

				foreach ($cuentas as $c){
					$div.='
					<div class="col-3 pedidoDiv" idp="'.$c->idPedido.'">
								<div class="card pedido bg-secondary">
									<div class="card-header">
										<h3 class="card-title text-bold">'.strtoupper($c->tipoCuentaPedido).' - '.$c->nombreZona.' - M'.$c->nombreZonaMesa.'</h3>
									</div>
									<div class="card-body ">
										<h3 class="card-subtitle mb-2 text-bold">'.$c->nombreClientePedido.'</h3>
										
									</div>
									<div class="card-footer text-bold text-right">
									<p>Mesero: '.$c->nombreUsuario.'</p>
									<input type="hidden" class="idPedido" value="'.$c->idPedido.'" impresora="'.$idImpresora.'">
									<input type="hidden" class="timer" value="'.date_format(date_create($c->fecha),"Y-m-d H:i:s").'"><label class="timers"></label>
									</div>
								</div>
							</div>
					';
					// if ($pd == 3){
					// 	$div.='</div>
					// 	<div class="row">';
					// }
					// $pd++;
					//var_dump($tipo."  ".$precios);
				}
				$div.='</div>';
			}

			$respuesta["codigo"] = 200;
			$respuesta["div"] = $div;
			echo json_encode($respuesta);
	}

	public function VerificarCocina(){
		$idImpresora = $this->input->post("idImpresora");

		$condicionCuentas = array(
			"estadoPedido"=>"Pendiente", 
			"estadoPedidoDetalle"=>"Activo", 
			"idSucursalPedido"=>$this->session->idSucursal
		);
		if($idImpresora != "All"){
			$condicionCuentas = array(
				"estadoPedido"=>"Pendiente", 
				"estadoPedidoDetalle"=>"Activo",
				"md5(producto.impresoraProducto) " => $idImpresora, 
				"idSucursalPedido"=>$this->session->idSucursal
			);
		}
		else{
			$condicionCuentas = array(
				"estadoPedido"=>"Pendiente", 
				"estadoPedidoDetalle"=>"Activo", 
				"idSucursalPedido"=>$this->session->idSucursal
			);	
		}
		$joinCuentas = array(
			array(
				"tabla" => "pedidoDetalle",
				"condicion" => "pedido.idPedido = pedidoDetalle.idPedido",
				"tipo" => "left",
				"campos" => "COUNT(pedidoDetalle.idPedidoDetalle) AS cantidad"
			),
			array(
				"tabla" => "producto",
				"condicion" => "producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
				"tipo" => "left",
				"campos" => ""
			),
		);
		$cuentas =  TraerUnDatoJoin('pedido',$condicionCuentas,$joinCuentas,"pedido.idPedido DESC","");
		$datos = "";

		if($cuentas !== false){
			$respuesta["codigo"] = 200;
			$cantidad = $cuentas->cantidad;
		}else{
			$cantidad = 0;
			$respuesta["codigo"] = 500;
		}
		$respuesta["cantidad"] = $cantidad;

			echo json_encode($respuesta);
	}

	public function DetalleOrden(){
		if($this->input->method(TRUE) == "POST") {
			$idPedido = $this->input->post("idPedido");
			$idImpresora = $this->input->post("idImpresora");
			$join = array(
				array(
					"tabla" => "pedidoDetalle",
					"condicion" => "pedido.idPedido = pedidoDetalle.idPedido",
					"tipo" => "left",
					"campos" => "pedidoDetalle.estadoPedidoDetalle, MIN(fechaHoraPedidoDetalle) AS fecha, pedidoDetalle.senoritaPedidoDetalle"
				),
				array(
					"tabla" => "producto",
					"condicion" => "producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
					"tipo" => "left",
					"campos" => "producto.impresoraProducto"
				),
				array(
					"tabla"=>"zonaMesa",
					"condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
					"tipo" => "left",
					"campos" => "nombreZonaMesa"
				),
				array(
					"tabla"=>"usuario",
					"condicion" => "pedidoDetalle.senoritaPedidoDetalle = usuario.idUsuario",
					"tipo" => "left",
					"campos" => "nombreUsuario"
				),
			);
			if($idImpresora != "All"){
				$condicionCuentas = array(
					"pedido.idPedido"=>$idPedido,
					"estadoPedidoDetalle"=>"Activo",
					"md5(producto.impresoraProducto)" => $idImpresora, 
					"idSucursalPedido"=>$this->session->idSucursal
				);
			}
			else{
				$condicionCuentas = array(
					"pedido.idPedido"=>$idPedido,
					"estadoPedido"=>"Pendiente", 
					"estadoPedidoDetalle"=>"Activo", 
					"idSucursalPedido"=>$this->session->idSucursal
				);	
			}
			$pedido = TraerUnDatoJoin("pedido",$condicionCuentas,$join,"","pedido.idPedido");

			$tbody = '';

			$joinDetalle = array(
				array(
					"tabla"=>"producto",
					"condicion" => "pedidoDetalle.idProductoPedidoDetalle = producto.idProducto",
					"tipo" => "inner",
					"campos" => "nombreProducto",
				),
				array(
					"tabla"=>"usuario",
					"condicion" => "pedidoDetalle.senoritaPedidoDetalle = usuario.idUsuario",
					"tipo" => "left",
					"campos" => "nombreUsuario"
				),
			);
			if($idImpresora != "All"){
				$condicionDetalle = array(
					"idPedido" => $idPedido,
					"estadoPedidoDetalle" => "Activo",
					"md5(producto.impresoraProducto)" => $idImpresora
				);
			} else {
				$condicionDetalle = array(
					"idPedido" => $idPedido,
					"estadoPedidoDetalle" => "Activo",
				);
			}
			$detallePedido = TraerDatosJoin("pedidoDetalle", $condicionDetalle,"",$joinDetalle);
			if($detallePedido){
				$i = 0;
				foreach($detallePedido as $dp){
					$trPrim = '';
					$regalia = ($dp->regaliaPedidoDetalle) ? 'Si': 'No';
					$precio = ($dp->regaliaPedidoDetalle) ? '0.00': $dp->precioPedidoDetalle;
					$trPrim .= '<tr class="prim idt'.$i.' accordion-toggle" href="#ita'.$i.'" cantidad="'.$dp->cantidadPedidoDetalle.'" precio="'.$dp->precioPedidoDetalle.'" >';
					$trPrim .= '<td >'.$dp->cantidadPedidoDetalle.'</td>';
					$trPrim .= '<td >'.$dp->nombreProducto.'</td>';
					$trPrim .= '<td ><input type="hidden" class="timer" it="'.$i.'" value="'.date_format(date_create($dp->fechaHoraPedidoDetalle),"Y-m-d H:i:s").'"><label class="timers"></label></td>';
					$trPrim .= '<td ><a class="btn btn-sm btn-block btn-success finalizarPlato" it="'.$i.'" idImpresora="'.$idImpresora.'" idPedido="'.$dp->idPedido.'" idPedidoDetalle="'.$dp->idPedidoDetalle.'"><i class="fa fa-arrow-right"></i></a></td>';
					$trPrim .= '</tr>';
					
					
					
					$trSec = '';
					$trSec .= '<tr class="sec idt'.$i.' hide-table-padding">';
					$trSec .= '<td colspan="4">';
					$trSec .= '<div id="ita'.$i.'" class="in p-0 collapse">';
					$trSec .= '';
					if($dp->senoritaPedidoDetalle != "0"){
						$trSec .= '<div class="botoneraProductoDetalle"><label>Cocinero: </label><p>'.$dp->nombreUsuario.'</p>';
						$trSec .= '<label>Comentario: </label><p>'.$dp->comentarioPedidoDetalle.'</p></div><hr>';
					} else {

						$trSec .= '<div class="botoneraProductoDetalle"><label>Comentario: </label><p>'.$dp->comentarioPedidoDetalle.'</p></div><hr>';
					}
					
					$trSec .= '<div class="listaModificadores">';
					$trSec .= $this->llamarModificadores($dp->idPedidoDetalle);
					$trSec .= '</div>';
					$trSec .= '</div>';
					$trSec .= '</td>';
					$trSec .= '</tr>';

					$tbody .= $trPrim . $trSec;
					$i++;
				}
			}
			$txtTipo = '';
			$tbodyPedido = '';
			if($pedido){
				$txtTipo .= ($pedido->tipoCuentaPedido == "local") ? "<span class='badge badge-primary'>Local<span>" : ( 
					($pedido->tipoCuentaPedido == "llevar") ? "<span class='badge badge-success'>Llevar<span>" : (
						($pedido->tipoCuentaPedido == "domicilio") ? "<span class='badge badge-info'>Domicilio<span>" : (
							($pedido->tipoCuentaPedido == "Recoger") ? "<span class='badge badge-warning'>Domicilio<span>" : ""
						)
					) 
				);  
				$tbodyPedido .= "
				<tr>
					<td colspan='2'>Tipo</td><td>".$txtTipo."</td>
				</tr>
				<tr>
					<td>Cliente</td><td colspan='3'><label>".$pedido->nombreClientePedido."</td>
				</tr>
				<tr>
					<td>Mesa</td><td colspan='3'>".$pedido->zonaPedido." / Mesa #".$pedido->nombreZonaMesa."</td>
				</tr>
				<tr>
					<td colspan='4'><a class='btn btn-sm btn-block btn-success finalizarPlatoGeneral' idImpresora='$idImpresora' idPedido='$dp->idPedido' ><i class='fa fa-arrow-right'></i></a></td>
				</tr>
				";
				$respuesta['total'] = $pedido->totalPedido;
				$respuesta['tipoCuenta'] = $pedido->tipoCuentaPedido;
				$respuesta['zona'] = $pedido->zonaPedido;
				$respuesta['tipoAumento'] = $pedido->tipoAumentoPedido;
				$respuesta['aumento'] = $pedido->aumentoPedido;
				$respuesta['mesa'] = $pedido->nombreZonaMesa;
				$respuesta['nombre'] = $pedido->nombreClientePedido;
				$respuesta['direccion'] = $pedido->direccionClientePedido;
				$respuesta['usuario'] = $pedido->nombreUsuario;
				$respuesta['fecha'] = $pedido->fechaPedido;
				$respuesta['hora'] = $pedido->horaPedido;
			}
			$respuesta['tbody'] = $tbody;
			$respuesta['tbodyPedido'] = $tbodyPedido;

			echo json_encode($respuesta);			
		}
	}

	public function FinalizarPlato(){
		if($this->input->method(TRUE) == "POST") {
			$idPedidoDetalle = $this->input->post("idPedidoDetalle");
			$error = false;
			IniciarTransaccion();
			$condicion =  array(
				"idPedidoDetalle" => $idPedidoDetalle,
			);
			$datos = array( "estadoPedidoDetalle" => "Finalizado" , "aleatorioPedidoDetalle"=>uniqid() );
			$editar = EditarDatos("pedidoDetalle",$datos,$condicion);
			if(!$editar){$error =  true; $respuesta["codigo"] = 500 ;}
			if(!$error){
				$respuesta["codigo"] = 200;
				// Si ya no quedan platos activos en el pedido, la orden está lista: si viene de la web, se refleja en el rastreo del cliente
				$detalle = TraerUnDato("pedidoDetalle",array("idPedidoDetalle"=>$idPedidoDetalle));
				if($detalle){
					$pendientes = TraerDatos("pedidoDetalle",array("idPedido"=>$detalle->idPedido,"estadoPedidoDetalle"=>"Activo"));
					if(!$pendientes){
						$this->MarcarPedidoOnlineListo($detalle->idPedido);
					}
				}
			}
			(!$error) ? EjecutarTransaccion() : DeshacerTransaccion();
			echo json_encode($respuesta);
		}

	}

	/**
	 * Cuando cocina termina todos los platos de un pedido que vino de la web,
	 * marca el pedido online como "Listo" para que el cliente lo vea en su rastreo
	 * y, si WhatsApp está activo, le llega la notificación de "pedido listo".
	 */
	private function MarcarPedidoOnlineListo($idPedido){
		$pedidoOnline = TraerUnDato("pedidoonline",array("idPedidoRef"=>$idPedido));
		if($pedidoOnline){
			if(!in_array($pedidoOnline->estadoOnline,array("Recibido","EnPreparacion"))) return;

			EditarDatos("pedidoonline",array("estadoOnline"=>"Listo"),array("idPedidoOnline"=>$pedidoOnline->idPedidoOnline));

			// Si es un pedido a domicilio, notificar a Orion Logistics para asignar conductor
			if($pedidoOnline->tipoPedidoOnline === 'domicilio'){
				$this->EnviarPedidoAOrion($pedidoOnline);
			}

			$cfgPrueba = TraerUnDato('configuraciones',"parametroConfiguracion='WA_MODO_PRUEBA' AND estadoConfiguracion='Activo'");
			$modoPrueba = $cfgPrueba ? (strtoupper(trim($cfgPrueba->valorConfiguracion)) === 'TRUE') : true;
			if($modoPrueba) return;

			$clienteAcceso = TraerUnDato("clienteacceso",array("idClienteAcceso"=>$pedidoOnline->idClienteAccesoRef));
			if(!$clienteAcceso) return;
			$cliente = TraerUnDato("cliente",array("idCliente"=>$clienteAcceso->idClienteRef,"estadoCliente"=>"Activo"));
			if($cliente) WaEnviarEstado($cliente,$pedidoOnline,"Listo");
			return;
		}

		// Pedido creado directamente en el POS físico (sin registro en pedidoonline):
		// si es a domicilio, notificar a Orion Logistics para asignar conductor, igual que los pedidos web
		$pedido = TraerUnDato("pedido",array("idPedido"=>$idPedido));
		if($pedido && $pedido->tipoCuentaPedido === 'domicilio'){
			$this->EnviarPedidoFisicoAOrion($pedido);
		}
	}

	/**
	 * Envía el pedido de domicilio a Orion Logistics Services para que el
	 * despachador lo asigne a un conductor. La URL y la API key se leen de
	 * la tabla "configuraciones" (ORION_API_URL / ORION_API_KEY) para que
	 * la integración siga funcionando si Orion se traslada a otro servidor.
	 */
	private function EnviarPedidoAOrion($pedidoOnline){
		$pedido = TraerUnDato("pedido",array("idPedido"=>$pedidoOnline->idPedidoRef));
		if(!$pedido) return;

		$cliente = null;
		$clienteAcceso = TraerUnDato("clienteacceso",array("idClienteAcceso"=>$pedidoOnline->idClienteAccesoRef));
		if($clienteAcceso) $cliente = TraerUnDato("cliente",array("idCliente"=>$clienteAcceso->idClienteRef));

		$this->_enviarPedidoAOrion($pedido, array(
			'external_order_id' => $pedidoOnline->codigoSeguimientoOnline,
			'customer_name'     => $cliente ? $cliente->nombreCliente : null,
			'customer_phone'    => $cliente ? $cliente->telefonoCliente : null,
			'address'           => $pedido->direccionClientePedido,
			'latitude'          => $pedidoOnline->latitudOnline,
			'longitude'         => $pedidoOnline->longitudOnline,
			'notes'             => $pedidoOnline->notasOnline,
		));
	}

	/**
	 * Envía un pedido a domicilio creado directamente en el POS físico (sin
	 * registro en pedidoonline) a Orion Logistics, igual que los pedidos web.
	 */
	private function EnviarPedidoFisicoAOrion($pedido){
		$cliente = ($pedido->idCliente > 0) ? TraerUnDato("cliente",array("idCliente"=>$pedido->idCliente)) : null;

		$this->_enviarPedidoAOrion($pedido, array(
			'external_order_id' => 'POS-'.$pedido->idPedido,
			'customer_name'     => $cliente ? $cliente->nombreCliente : $pedido->nombreClientePedido,
			'customer_phone'    => $cliente ? $cliente->telefonoCliente : null,
			'address'           => $pedido->direccionClientePedido,
			'latitude'          => null,
			'longitude'         => null,
			'notes'             => null,
		));
	}

	/**
	 * Completa el payload común (items, total y punto de recolección del
	 * restaurante) y lo envía a Orion Logistics vía POST /api/pos/orders.
	 * La URL y la API key se leen de la tabla "configuraciones"
	 * (ORION_API_URL / ORION_API_KEY) para que la integración siga
	 * funcionando si Orion se traslada a otro servidor.
	 */
	private function _enviarPedidoAOrion($pedido, $datos){
		$cfgUrl = TraerUnDato('configuraciones',"parametroConfiguracion='ORION_API_URL' AND estadoConfiguracion='Activo'");
		$cfgKey = TraerUnDato('configuraciones',"parametroConfiguracion='ORION_API_KEY' AND estadoConfiguracion='Activo'");
		if(!$cfgUrl || empty($cfgUrl->valorConfiguracion) || !$cfgKey || empty($cfgKey->valorConfiguracion)){
			log_message('debug','Orion: integración no configurada (ORION_API_URL / ORION_API_KEY)');
			return;
		}

		$items = array();
		$detalles = TraerDatos("pedidodetalle",array("idPedido"=>$pedido->idPedido,"estadoPedidoDetalle !="=>"Borrado"));
		if($detalles){
			foreach($detalles as $d){
				$producto = TraerUnDato("producto",array("idProducto"=>$d->idProductoPedidoDetalle));
				$items[] = array(
					'name' => $producto ? $producto->nombreProducto : ('Producto #'.$d->idProductoPedidoDetalle),
					'qty'  => (int)$d->cantidadPedidoDetalle,
				);
			}
		}

		$cfgNombreEmpresa = TraerUnDato('configuraciones',"parametroConfiguracion='nombreEmpresa' AND estadoConfiguracion='Activo'");
		$cfgDireccionEmpresa = TraerUnDato('configuraciones',"parametroConfiguracion='direccionEmpresa' AND estadoConfiguracion='Activo'");
		$cfgRestauranteLat = TraerUnDato('configuraciones',"parametroConfiguracion='RESTAURANTE_LAT' AND estadoConfiguracion='Activo'");
		$cfgRestauranteLng = TraerUnDato('configuraciones',"parametroConfiguracion='RESTAURANTE_LNG' AND estadoConfiguracion='Activo'");

		$payload = array_merge(array(
			'source'           => 'fhbrestaurant',
			'pickup_name'      => $cfgNombreEmpresa ? $cfgNombreEmpresa->valorConfiguracion : null,
			'pickup_address'   => $cfgDireccionEmpresa ? $cfgDireccionEmpresa->valorConfiguracion : null,
			'pickup_latitude'  => $cfgRestauranteLat ? trim($cfgRestauranteLat->valorConfiguracion) : null,
			'pickup_longitude' => $cfgRestauranteLng ? trim($cfgRestauranteLng->valorConfiguracion) : null,
			'items'            => $items,
			'total_amount'     => $pedido->totalPedido,
		), $datos);

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL            => rtrim($cfgUrl->valorConfiguracion,'/').'/api/pos/orders',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => json_encode($payload),
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'X-API-Key: '.$cfgKey->valorConfiguracion,
			),
			CURLOPT_TIMEOUT        => 8,
			CURLOPT_SSL_VERIFYPEER => false,
		));
		$resp = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		log_message('debug', 'Orion pedido POST HTTP:'.$code.' | '.$resp);
	}

	public function llamarModificadores($idPedidoDetalle = 0,$idReferencia = 0){
		$mod = '<ul>';
		$modificadores = TraerDatos("pedidoSubDetalle",array("idPedidoDetalle"=>$idPedidoDetalle,"idReferenciaPedidoSubDetalle" => $idReferencia));
		if($modificadores){
			foreach($modificadores as $m){
				$nombre = ($m->nombrePedidoSubDetalle != "0") ? ' - '.$m->nombrePedidoSubDetalle : '';
				$mod .= '<li>'.$m->nombreModTipoPedidoSubDetalle.$nombre ;
				//$mod .='<ul><li>'.$m->nombrePedidoSubDetalle.' ('.$m->aumentoPedidoSubDetalle.')';
				if($m->variosPedidoSubDetalle == "1"){
					$idRef = $m->idPedidoSubDetalle;
					$subMod = $this->llamarModificadores($idPedidoDetalle , $idRef);
				}
				//$mod .='</li></ul>';
				$mod .= '</li>';
			}
		}
		$mod .= '</ul>';
		return $mod;
	}
	
}
/* End of file Cocina.php */
