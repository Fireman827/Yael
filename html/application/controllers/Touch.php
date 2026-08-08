<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Touch extends CI_Controller {

	private $tabla = "";
	private $tablaPermisos = "";
	private $controlador = "Touch";
	function __construct(){
		parent::__construct();
		$this->load->Model('CoreModel',"core");
	}
	public function index(){
		if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
			GblPlantilla2("plantilla2/permiso",array(),array(),"No autorizado");
		} else{
			if($this->input->method(TRUE) == "GET"){
				$this->load->helper('vfd');
				vfd_show_welcome();

				$categorias = TraerDatos('productoCategoria',array("estadoProductoCategoria"=>"Activo"));
				$productos = TraerDatos('producto',array("estadoProducto"=>"Activo"));
				$serviciosCategoria = TraerDatos('servicioCategoria',array("estadoServicioCategoria"=>"Activo"),"prioridadServicio ASC");
				$zonas = TraerDatos('zona',array("idSucursalZona"=>$this->session->idSucursal,"visibleZona"=>"1","estadoZona"=>"Activo"));
				//$servicios = TraerDatos('servicio');
				$senoritaCategoria = TraerDatos('senoritaCategoria',array("estadoSenoritaCategoria"=>"Activo"));
				$join = array(
					array(
						"tabla" => "servicioCategoria",
						"condicion" => "servicio.idServicioCategoria = servicioCategoria.idServicioCategoria",
						"campos"=>"servicioCategoria.nombreServicioCategoria"
					)
				);
				$servicios = TraerDatosJoin('servicio','','',$join);

				$condicion = array('productoCategoriaEspecifica.idProductoCategoria' => 4);
				$ordenCampos = "";
				$join = array(array("tabla" => "productoCategoriaEspecifica", "condicion" => "productoCategoriaEspecifica.idProducto=producto.idProducto"));
				$bebidas = TraerDatosJoin('producto',$condicion,$ordenCampos,$join);

				$condicionDoc =  array("tipoDocumento" => "Compra");
				$joinDocumento = array(
					array(
						"tabla" => "cajaDocumento",
						"condicion" => "tipoDocumento.idDocumento = cajaDocumento.idDocumentoCajaDocumento"
					),

				);
				$documentos = TraerDatos('documento',$condicionDoc);

				$condicionCli =  array("estadoCliente" => "Activo");
				$clientes = TraerDatos('cliente',$condicionCli);

				$condicionFac =  array("tipoFactura"=>"Servicio","estadoFactura" => "Pendiente");
				$facPendienteServicio = TraerDatos('factura',$condicionFac);

				$condicionCaja =  array("estadoCorte"=>"Vigente","idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCaja"=>$this->session->idSucursal,"estadoCaja" => "Activo","aperturaCaja"=> "1","turnoCaja"=> "1");
				$joinCaja =  array(
					array(
						"tabla" => "corteCaja",
						"condicion" => "corteCaja.idCaja = caja.idCaja",
						"tipo" => "left",
						"campos" => "corteCaja.idUsuarioCorte,fechaCorte"
					),
					array(
						"tabla"=>"corteTurno",
						"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
					),
					array(
						"tabla" => "impresora",
						"condicion" => "caja.impresoraCaja = impresora.idImpresora",
						"tipo" => "left",
						"campos" => "recursoCompartidoImpresora, ipImpresora"
					),
				);
				$cajas = TraerDatosJoin('caja',$condicionCaja,"",$joinCaja,"caja.idCaja");

				// $cobroPropina = TraerUnDatoIndividual("configuraciones","valorConfiguracion",array("idConfiguracion" => "19"))[0]["valorConfiguracion"];
				// $propina = TraerUnDatoIndividual("configuraciones","valorConfiguracion",array("idConfiguracion" => "20"))[0]["valorConfiguracion"];
				// $valPropina = ($cobroPropina == "No	") ? "0.00" : $propina;
				//
				$cobroPropina = GblTraerConfiguracion("cobroPropina");
				$valPropina = ($cobroPropina == "No	") ? "" : GblTraerConfiguracion("valorPropina");

				//$corte = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorte"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal));
				$joinTurno = array(
					array(
						"tabla"=>"corteTurno",
						"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
					),
				);
				$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);

				$idCorte = ($corte) ? $corte->idCorteCaja : 0;
				$idCaja = ($corte) ? $corte->idCaja : 0;
				$idUsuarioCorteTurno = ($corte) ? $corte->idUsuarioCorteTurno: 0;

				// Catálogos de Hacienda (departamento/municipio) para poder editar/corregir
				// los datos del cliente al momento del cobro, igual que en el registro online.
				$deptos = TraerDatos('FE_CAT_012_Departamento', "codigo != '00'", 'codigo');
				$departamentos = array();
				if($deptos){
					foreach($deptos as $d){
						$departamentos[] = (object)array(
							'codigo'  => $d->codigo,
							'valores' => $d->valores,
						);
					}
				}

				$munis = TraerDatos('FE_CAT_013_Municipio', array());
				$municipiosPorDepto = array();
				if($munis){
					foreach($munis as $m){
						$municipiosPorDepto[$m->departamento][] = array(
							'codigo'  => $m->codigo,
							'valores' => $m->valores,
						);
					}
				}

				$titulo = "Touch";
				$datosVista = array(
					"titulo"=> $titulo,
					"icono"=> "fa fa-monitor",
					"controlador"=> "Touch",
					"proceso"=> "Vista",
					"categorias"=> $categorias,
					"productos"=> $productos,
					"servicios"=> $servicios,
					"serviciosCategoria"=> $serviciosCategoria,
					"senoritaCategoria"=> $senoritaCategoria,
					"bebidas"=> $bebidas,
					"documentos"=> $documentos,
					"cajas"=> $cajas,
					"idCaja"=> $idCaja,
					"clientes"=> $clientes,
					"zonas"=> $zonas,
					// "cuentas"=> $cuentas,
					"cobroPropina"=> $cobroPropina,
					"propina"=> $valPropina,
					"idCorte"=> $idCorte,
					"idUsuarioCorte"=> $idUsuarioCorteTurno,
					"facPendienteServicio"=> $facPendienteServicio,
					"departamentos"=> $departamentos,
					"municipiosPorDepto"=> $municipiosPorDepto,
				);
				$extras = array(
					'css' => array(
						"vendors/core/css/touch.css",
						"vendors/core/css/keyboard.css",
						"vendors/plugins/Minimal-Virtual-Keyboard/softkeys-0.0.1.css",
						"vendors/plugins/TypeAhead/typeahead.css",
					),
					'js' => array(
						"vendors/plugins/Minimal-Virtual-Keyboard/softkeys-0.0.1.js",
						"vendors/plugins/TypeAhead/typeahead.jquery.min.js",
						"vendors/core/js/main.js",
						"scripts/touch.js?q=".uniqid(),
					),
				);
				GblPlantilla2("touch/MostrarTouch",$datosVista,$extras,$titulo);
				// }
				// else{
				// 	$TraerAperturaActual = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("estadoCorte" => "Vigente"));
				// 	if ($TraerAperturaActual !=0) {
				// 		$titulo = "Cortes de Caja";
				// 		$aperturaActual = $TraerAperturaActual[0]["idCorteCaja"];
				// 		$local = TraerUnDatoIndividual("factura","SUM(totalFactura) as dato",array("idCorte" => $aperturaActual, "tipoFactura" => "Producto"))[0]["dato"];
				// 		$senorita = TraerUnDatoIndividual("factura","SUM(totalFactura) as dato",array("idCorte" => $aperturaActual, "tipoFactura" => "Producto Especial"))[0]["dato"];
				// 		$servicios = TraerUnDatoIndividual("factura","SUM(totalFactura) as dato",array("idCorte" => $aperturaActual, "tipoFactura" => "Servicio"))[0]["dato"];
				// 		$propina = TraerUnDatoIndividual("factura","SUM(propinaFactura) as dato",array("idCorte" => $aperturaActual))[0]["dato"];
				// 		$subtotal = $local + $propina;
				// 		$totalventa = $subtotal + $senorita;
				// 		$total = $totalventa + $servicios;

				// 		$corte = array(
				// 			'local' => $local,
				// 			'senorita' => $senorita,
				// 			'subtotal' => $subtotal,
				// 			'propina' => $propina,
				// 			'totalventa' => $totalventa,
				// 			'servicios' => $servicios,
				// 			'total' => $total,
				// 			'idCorteCaja' => $aperturaActual,
				// 		);
				// 	}else {
				// 		$titulo = "Apertura de Caja";
				// 		$corte = array(
				// 			'local' => 0,
				// 			'senorita' => 0,
				// 			'subtotal' => 0,
				// 			'propina' => 0,
				// 			'totalventa' => 0,
				// 			'servicios' => 0,
				// 			'total' => 0,
				// 			'idCorteCaja' => 0,
				// 		);
				// 	}
				// 	$datosVista = array(
				// 		"titulo"=> $titulo,
				// 		"icono"=> "fa fa-money",
				// 		"admin"=>$this->session->admin,
				// 		"idSucursal"=>$this->session->idSucursal,
				// 		"sucursales"=>TraerDatos('sucursal'),
				// 		"corte"=>$corte,
				// 	);
				// 	$extras = array(
				// 		'css' => array(),
				// 		'js' => array(
				// 			"scripts/cortes.js"
				// 		),
				// 	);
				// 	GblPlantilla2("corte/Corte",$datosVista,$extras,$titulo);
				// }
			}
		}
	}
	/**************************************************************/
	/**************************CUENTAS*****************************/
	/**************************************************************/
	public function ActualizarCuentas(){
		$tipo = $this->input->post("tipo");

		$joinTurno = array(
			array(
				"tabla"=>"corteTurno",
				"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
			),
		);
		$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);

		$idCorte = ($corte) ? $corte->idCorteCaja : 0;
		$idCaja = ($corte) ? $corte->idCaja : 0;
		$idUsuarioCorte = ($corte) ? $corte->idUsuarioCorteTurno: 0;

		$condicionCuentas = array("estadoPedido"=>"Pendiente","tipoCuentaPedido"=>$tipo, "idSucursalPedido"=>$this->session->idSucursal);
		if($idUsuarioCorte != $this->session->idUsuario && GblTraerConfiguracion("cuentaMeseroSeparada") == "Si"){
			$condicionCuentas['pedido.idUsuarioPedido'] = $this->session->idUsuario;
		}
		$joinCuentas = array(
			array(
				"tabla" => "zonaMesa",
				"condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
				"tipo" => "left",
				"campos" => "zonaMesa.nombreZonaMesa"
			),
			array(
				"tabla" => "zona",
				"condicion" => "pedido.idZonaPedido = zona.idZona",
				"tipo" => "left",
				"campos" => "zona.nombreZona, zona.precioRegularZona, zona.precioEspecialZona, zona.precioEmpleadoZona"
			),
			array(
				"tabla" => "usuario",
				"condicion" => "pedido.idUsuarioPedido = usuario.idUsuario",
				"tipo" => "inner",
				"campos" => "usuario.nombreUsuario"
			),
			array(
				"tabla" => "cliente",
				"condicion" => "pedido.idCliente = cliente.idCliente",
				"tipo" => "left",
				"campos" => "cliente.nombreCliente as nombreClientePos, cliente.telefonoCliente as telefonoClientePos"
			),
		);
		$cuentas =  TraerDatosJoin('pedido',$condicionCuentas,"pedido.idPedido DESC",$joinCuentas);
		$datos = "";
		if($cuentas !== false){
			foreach ($cuentas as $c){
				$nombreClienteCuenta = ($c->origenPedido == "web" && !empty($c->nombreClientePos)) ? "[WEB] ".$c->nombreClientePos : $c->nombreClientePedido;
				$telefonoClienteCuenta = ($c->origenPedido == "web" && !empty($c->telefonoClientePos)) ? "<br><small>".$c->telefonoClientePos."</small>" : "";
				$datos .= '<tr idPedido="'.$c->idPedido.'" class="prim" id="cuenta'.$c->idPedido.'"  href="#collapse'.$c->idPedido.'">
				<td class="col-lg-2">Orden # '.$c->idPedido.'</td>
				<td class="col-lg-2">'.$nombreClienteCuenta.$telefonoClienteCuenta.'</td>
				<td class="col-lg-4">'.ucfirst($c->tipoCuentaPedido);
				if($c->tipoCuentaPedido == "local"){
					$datos.= " / Mesa #".$c->nombreZonaMesa." / ".$c->nombreZona;
				}
				$datos.='</td>
				<td class="col-lg-2 "><input type="hidden" class="timer" value="'.date_format(date_create($c->fechaPedido." ".$c->horaPedido),"Y-m-d H:i:s").'"><label class="timers"></label></td>
				<td class="col-lg-2 totalPedido" idPedido="'.$c->idPedido.'">$ '.$c->totalPedido.'</td>
				</tr>
				<tr class="hide-table-padding">
				<td colspan="5">
				<div id="collapse'.$c->idPedido.'" class="collapse in p-3">';
				if($c->tipoCuentaPedido == "local"){
					$datos.= '<a class="btn btn-primary btn-lg DividirCuenta" role="button" idPedido="'.$c->idPedido.'"><i class="fa fa-arrows-alt"></i></a>
					<a style="margin-left:2%;" class="btn btn-primary btn-lg UnirCuenta" role="button"  idZona="'.$c->idZonaPedido.'" idPedido="'.$c->idPedido.'"><i class="fa fa-compress-arrows-alt"></i></a>';
				}
				$regular = ($c->precioRegularZona != "") ? $c->precioRegularZona : 1;
				$datos.='<a style="margin-left:2%;" class="btn btn-primary btn-lg AgregarCuenta" role="button" precioRegular="'.$regular.'" precioEspecial="'.$c->precioEspecialZona.'" precioEmpleado="'.$c->precioEmpleadoZona.'" idPedido="'.$c->idPedido.'"><i class="fa fa-plus"></i></a>';
				if($c->tipoCuentaPedido == "local"){
					$datos.='<a style="margin-left:2%;" class="btn btn-default btn-lg AgregarCuentaLlevarLocal" role="button" precioRegular="'.$regular.'" precioEspecial="'.$c->precioEspecialZona.'" precioEmpleado="'.$c->precioEmpleadoZona.'" idPedido="'.$c->idPedido.'"><i class="fa fa-plus"></i></a>';
				}
				$datos.= '<a style="margin-left:2%;" class="btn btn-success btn-lg ImprimirCuenta" role="button" idPedido="'.$c->idPedido.'"><i class="fa fa-receipt"></i></a>';
				$datos.= '<a style="margin-left:2%;" class="btn btn-primary btn-lg ImprimirCuenta1" role="button" idPedido="'.$c->idPedido.'"><i class="fa fa-receipt"></i></a>';
				if($idUsuarioCorte == $this->session->idUsuario){
					$datos.= '<a style="margin-left:2%;" class="btn btn-info btn-lg CambioMesa" role="button" idPedido="'.$c->idPedido.'"><i class="fa fa-coffee"></i></a>';
					$datos.= '<a style="margin-left:2%;" class="btn btn-warning btn-lg CobrarCuenta" role="button" total="'.$c->totalPedido.'" idPedido="'.$c->idPedido.'"><i class="fa fa-dollar-sign"></i></a>';
				}
				if($this->session->idUsuario == $idUsuarioCorte){
					$datos.= '<a style="margin-left:2%;" class="btn btn-danger  btn-lg AnularCuenta" role="button" idPedido="'.$c->idPedido.'"><i class="fa fa-times"></i></a>';
				}
				$datos.= '</div>
				</td>
				</tr>';
			}
		} else {
			$datos = "<tr><td colspan='5'>No se encontraron datos</td></tr>";
		}
		// $data = array();
		$data["codigo"] = 200;
		$data["datos"] = $datos;
		echo json_encode($data);
	}
	public function ActualizarCuentasCanceladas(){
		$tipo = $this->input->post("tipo");

		$joinTurno = array(
			array(
				"tabla"=>"corteTurno",
				"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
			),
		);
		$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);

		$idCorte = ($corte) ? $corte->idCorteCaja : 0;
		$idUsuarioCorteTurno = ($corte) ? $corte->idUsuarioCorteTurno: 0;

		$fechaHoy = date("Y-m-d");
		$condicionCuentas = array("fechaPedido" => $fechaHoy,"estadoPedido"=>"Finalizado","tipoCuentaPedido"=>$tipo, "idSucursalPedido"=>$this->session->idSucursal);
		if($idUsuarioCorteTurno != $this->session->idUsuario && GblTraerConfiguracion("cuentaMeseroSeparada") == "Si"){
			$condicionCuentas['pedido.idUsuarioPedido'] = $this->session->idUsuario;
		}
		$joinCuentas = array(
			array(
				"tabla" => "zonaMesa",
				"condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
				"tipo" => "left",
				"campos" => "zonaMesa.nombreZonaMesa"
			),
			array(
				"tabla" => "factura",
				"condicion" => "pedido.idPedido = factura.idReferenciaFactura",
				"tipo" => "left",
				"campos" => "factura.idFactura, factura.tipoDocumentoFactura"
			),
			array(
				"tabla" => "zona",
				"condicion" => "pedido.idZonaPedido = zona.idZona",
				"tipo" => "left",
				"campos" => "zona.nombreZona"
			),
			array(
				"tabla" => "usuario",
				"condicion" => "pedido.idUsuarioPedido = usuario.idUsuario",
				"tipo" => "inner",
				"campos" => "usuario.nombreUsuario"
			),
			array(
				"tabla" => "cliente",
				"condicion" => "pedido.idCliente = cliente.idCliente",
				"tipo" => "left",
				"campos" => "cliente.nombreCliente as nombreClientePos, cliente.telefonoCliente as telefonoClientePos"
			),
		);
		$cuentas =  TraerDatosJoin('pedido',$condicionCuentas,"pedido.idPedido DESC",$joinCuentas);
		$datos = "";
		if($cuentas !== false){
			foreach ($cuentas as $c){
				$nombreClienteCuenta = ($c->origenPedido == "web" && !empty($c->nombreClientePos)) ? "[WEB] ".$c->nombreClientePos : $c->nombreClientePedido;
				$telefonoClienteCuenta = ($c->origenPedido == "web" && !empty($c->telefonoClientePos)) ? "<br><small>".$c->telefonoClientePos."</small>" : "";
				$datos .= '<tr idPedido="'.$c->idPedido.'" class="prim" id="cuenta'.$c->idPedido.'"  href="#collapse'.$c->idPedido.'">
				<td class="col-lg-2">Orden # '.$c->idPedido.'</td>
				<td class="col-lg-2">'.$nombreClienteCuenta.$telefonoClienteCuenta.'</td>
				<td class="col-lg-4">'.ucfirst($c->tipoCuentaPedido);
				if($c->tipoCuentaPedido == "local"){
					$datos.= " / Mesa #".$c->nombreZonaMesa." / ".$c->nombreZona;
				}
				$datos.='</td>
				<td class="col-lg-2">'.date_format(date_create($c->fechaPedido." ".$c->horaPedido),"h:i A").'</td>
				<td class="col-lg-2 totalPedido" idPedido="'.$c->idPedido.'">$ '.$c->totalPedido.'</td>
				</tr>
				<tr class="hide-table-padding">
				<td colspan="5">
				<div id="collapse'.$c->idPedido.'" class="collapse in p-3">';

				$datos.='<a style="margin-left:2%;" class="btn btn-success btn-lg ReImprimirFactura" role="button" tipo="'.$c->tipoDocumentoFactura.'" idFactura="'.md5($c->idFactura).'" idFactura1="'.md5($c->idFactura).'" idPedido="'.$c->idPedido.'"><i class="fa fa-receipt"></i></a>';
				if($idUsuarioCorteTurno == $this->session->idUsuario){
					$datos.='<a style="margin-left:2%;" class="btn btn-danger  btn-lg AnularFactura" role="button" idFactura="'.$c->idFactura.'" idPedido="'.$c->idPedido.'"><i class="fa fa-times"></i></a>';
				}
				$datos.='</div>
				</td>
				</tr>';
			}
		} else {
			$datos = "<tr><td colspan='5'>No se encontraron datos</td></tr>";
		}
		// $data = array();
		$data["codigo"] = 200;
		$data["datos"] = $datos;
		echo json_encode($data);
	}
	public function ActualizarCuentasLocal(){
		if($this->input->method(TRUE) == "POST"){
			$idPedido = $this->input->post("idPedido");
			$idZona = $this->input->post("idZona");
			$joinTurno = array(
				array(
					"tabla"=>"corteTurno",
					"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
				),
			);
			$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);

			$idCorte = ($corte) ? $corte->idCorteCaja : 0;
			$idUsuarioCorteTurno = ($corte) ? $corte->idUsuarioCorteTurno: 0;

			$condicionCuentas = array(
				"estadoPedido"=>"Pendiente",
				"idSucursalPedido"=>$this->session->idSucursal,
				"tipoCuentaPedido"=>"local",
				"idPedido !=" =>$idPedido,
				//"zona.idZona" => $idZona
			);
			if($idUsuarioCorteTurno != $this->session->idUsuario && GblTraerConfiguracion("cuentaMeseroSeparada") == "Si"){
				$condicionCuentas['pedido.idUsuarioPedido'] = $this->session->idUsuario;
			}
			$joinCuentas = array(
				array(
					"tabla" => "zonaMesa",
					"condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
					"tipo" => "left",
					"campos" => "zonaMesa.nombreZonaMesa"
				),
				array(
					"tabla" => "zona",
					"condicion" => "pedido.idZonaPedido = zona.idZona",
					"tipo" => "left",
					"campos" => "zona.nombreZona"
				),
				array(
					"tabla" => "usuario",
					"condicion" => "pedido.idUsuarioPedido = usuario.idUsuario",
					"tipo" => "inner",
					"campos" => "usuario.nombreUsuario"
				),
				array(
					"tabla" => "cliente",
					"condicion" => "pedido.idCliente = cliente.idCliente",
					"tipo" => "left",
					"campos" => "cliente.nombreCliente as nombreClientePos, cliente.telefonoCliente as telefonoClientePos"
				),
			);
			$cuentas =  TraerDatosJoin('pedido',$condicionCuentas,"pedido.idPedido DESC",$joinCuentas);
			$datos = "";
			if($cuentas !== false){
				foreach ($cuentas as $c){
					$nombreClienteCuenta = ($c->origenPedido == "web" && !empty($c->nombreClientePos)) ? "[WEB] ".$c->nombreClientePos : $c->nombreClientePedido;
					$telefonoClienteCuenta = ($c->origenPedido == "web" && !empty($c->telefonoClientePos)) ? "<br><small>".$c->telefonoClientePos."</small>" : "";
					$datos .= '<tr idPedido="'.$c->idPedido.'"  id="cuenta'.$c->idPedido.'"  href="#collapse'.$c->idPedido.'">
					<td class="col-lg-3">Ord. # '.$c->idPedido.'</td>
					<td class="col-lg-4">'.$nombreClienteCuenta.$telefonoClienteCuenta.'</td>
					<td class="col-lg-4">'."/M#".$c->nombreZonaMesa." / ".$c->nombreZona . '</td>';
					$datos.='
					<td class="col-lg-2 totalPedido" idPedido="'.$c->idPedido.'">$'.$c->totalPedido.'</td>
					<td><div class="icheck-success d-inline"><input type="checkbox" class="cuentaUnir" total="'.$c->totalPedido.'" idPedido="'.$c->idPedido.'" id="cuentaUnir'.$c->idPedido.'"><label for="cuentaUnir'.$c->idPedido.'"></label></div></td>
					</tr>';

				}
			} else {
				$datos = "<tr><td colspan='5'>No se encontraron datos</td></tr>";
			}
			// $data = array();
			$data["codigo"] = 200;
			$data["datos"] = $datos;
			echo json_encode($data);
		}
	}
	public function AnularCuenta(){
		if($this->input->method(TRUE) == "POST"){
			$idPedido = $this->input->post("idPedido");

			$datosPedido =  array(
				"estadoPedido" => "Anulado",
			);
			$wherePedido = array('idPedido' => $idPedido);
			IniciarTransaccion();
			$guardar = EditarDatos('pedido',$datosPedido,$wherePedido);
			if($guardar){
				EjecutarTransaccion();
				$respuesta['codigo'] = 200;
			}
			else{
				DeshacerTransaccion();
				$respuesta['codigo'] = 504;
			}
			echo json_encode($respuesta);
		}
	}
	/**************************************************************/
	/**************************CUENTAS*****************************/
	/**************************************************************/
	public function TraerProductoCategoria(){

		$idCategoria = $this->input->post("idCategoria");
		$tipo = $this->input->post("tipoPrecio");
		//var_dump($tipo);
		if($tipo == ""){
			$tipo = "regular";
		}
		$condicion="";
		$condicion = array("producto.estadoProducto" => "Activo",'productoCategoriaEspecifica.estadoProductoCategoriaEspecifica' => "Activo" );
		if($idCategoria != "T"){
			$condicion = array('productoCategoriaEspecifica.idProductoCategoria' => $idCategoria, "producto.estadoProducto" => "Activo",'productoCategoriaEspecifica.estadoProductoCategoriaEspecifica' => "Activo" );
		}
		$ordenCampos = "";
		$join = array(
			array(
				"tabla" => "productoCategoriaEspecifica",
				"condicion" => "productoCategoriaEspecifica.idProducto=producto.idProducto",
				"tipo" => "inner",
				"campos" => "DISTINCT(productoCategoriaEspecifica.idProducto)"
			)
		);
		$productos = TraerDatosJoin('producto',$condicion,$ordenCampos,$join);
		$div = "";
		if($productos !== false){
			$div.='<div class="row">';
			$pd=0;

			foreach ($productos as $prod){
				if($tipo == "regular"){$precios = $prod->precioVentaProducto;}
				if($tipo == "especial"){$precios = $prod->precioEspecialProducto;}
				if($tipo == "empleado"){$precios = $prod->precioEmpleadoProducto;}
				// $default = GblTraerConfiguracion("logoEmpresa");
				$default = "vendors/core/img/black.png";
				$ruta = ($prod->imagenProducto  == "") ? base_url().$default : base_url().$prod->imagenProducto;
				$div.='<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
				<div class="card text-white text-center producto" idp="'.$prod->idProducto.'" >
				<img class="card-img" style="opacity: 50% ; min-height: 100px;" src="'.$ruta.'">
				<div class="card-img-overlay" >
				<p class="nombre text-bold" style="font-size: small; margin-left : -20%; margin-right : -20%;">'.$prod->nombreProducto.'</p>
				<input type="hidden" class="precio" value="'.$precios.'">
				<input type="hidden" class="precioEspecial" value="'.$precios.'">
				</div>
				<div class="card-footer" style="" >
				<h1 class="text-bold" style="font-size: small; margin-top : -10%; margin-bottom: -10%;">$'.$precios.'</h1>
				</div>
				</div>
				</div>';
				if ($pd == 5){
					// $div.='</div>
					// <div class="row">';
				}
				$pd++;
				//var_dump($tipo."  ".$precios);
			}
			$div.= ' </div>';
			$div.='</div>';
		}
		$respuesta["codigo"] = 200;
		$respuesta["div"] = $div;
		echo json_encode($respuesta);
	}
	public function TraerProductoModificadores(){

		$idProducto = $this->input->post("idProducto");
		$nivel = $this->input->post("nivel");
		$aumento = $this->input->post("aumento");
		$iteracion = $this->input->post("iteracion");
		if($nivel == 1){
			$condicion = array(
				"idProducto" => $idProducto,
				"estadoModificador" => "Activo",
			);
			$condicionP = array(
				"idProducto" => $idProducto,
				"estadoProductoModificador" => "Activo",
			);
			$contenedor = "listaModificadores";
		}
		else{
			$condicion = array(
				"idProducto" => $idProducto,
				"estadoModificador" => "Activo",

			);
			$condicionP = array(
				"idProducto" => $idProducto,
				"estadoProductoModificador" => "Activo",
			);
			$contenedor = $this->input->post("contenedor")." .listaModificadoresDetalle";
		}
		$cocinero = 0;
		$joinProd =  array(
			array(
				"tabla" => "impresora",
				"condicion" => "impresora.idImpresora = producto.impresoraProducto",
				"tipo" => "left",
				"campos" => "idImpresora, nombreImpresora"
			),
		);
		$cocineroImpresora = TraerUnDatoJoin("producto",array("cocineroImpresora" => "1","idProducto"=>$idProducto),$joinProd);
		if($cocineroImpresora){
			$cocinero = 1;
		}

		$ordenCampos = "productoModificador.idModificadorTipo ASC";
		$productos = TraerDatos('productoModificador',$condicionP,$ordenCampos);
		$producto = TraerUnDato('modificador',$condicion);
		$senorita = TraerDatos('senorita',array('estadoSenorita'=>'Activo'));
		$cocineros = TraerDatos('usuario',array('rolUsuario'=>'10'));
		$optionSenorita = '';
		if($senorita){
			foreach($senorita as $se){
				$optionSenorita .= '<option value="'.$se->idSenorita.'">'.$se->nombreSenorita.'</option>';
			}
		}
		$optionCocinero = '';
		$optionCocinero .= '<option value=""></option>';
		if($cocineros){
			foreach($cocineros as $co){
				$optionCocinero .= '<option value="'.$co->idUsuario.'">'.$co->nombreUsuario.'</option>';
			}
		}
		$lista = "<ul>";
		if($nivel != 1){
			$lista .= "<li style='font-size:small; list-style:none;margin-left:-15px' class='liContenedorProducto' it='$iteracion' aumento='$aumento' idModTipo='$producto->idModificadorTipo' idMod='$producto->idModificador' nombre='$producto->nombreModificador'>$producto->nombreModificador (+$aumento)</li>";
			$lista .= "<ul>";
		}
		$modTipo = "";
		$modTipo = ($nivel == 1) ? "<div class='detalle detalle".$iteracion."' ><label>Producto:</label> <label idProducto='' class='nombreProducto'></label><br><div class='modificadoresTipo'>": "";
		if($productos !== false){
			$pd=0;
			foreach ($productos as $prod){
				if($prod->multiSeleccionProductoModificador == 1){
					$contenedorli = uniqid();
					$lista .= "<li style='font-size:small;list-style:none;margin-left:-15px' iteracion='".$iteracion."' class='liContenedor' contenedor='".$contenedorli."' nombre='".$prod->nombreProductoModificador."' varios='".$prod->variosProductoModificador."' multiseleccion='1' maxseleccion='".$prod->cantidadProductoModificador."' idModTipo='".$prod->idModificadorTipo."' idProd='".$prod->idProducto."' idProdModTipo='".$prod->idProductoModificador."' >$prod->nombreProductoModificador</li>";
					$modTipo .= '<a class="btn btn-default col-3 mb-1 ml-1 modTipo" nivel="'.$nivel.'" iteracion="'.$iteracion.'" contenedorli="'.$contenedorli.'" contenedor="'.$contenedor.'" varios="'.$prod->variosProductoModificador.'" multiseleccion="1" maxseleccion="'.$prod->cantidadProductoModificador.'" idModTipo="'.$prod->idModificadorTipo.'" idProd="'.$prod->idProducto.'" idProdModTipo="'.$prod->idProductoModificador.'">'.$prod->nombreProductoModificador.'</a>';
				}
				else{
					for ($i = 0; $i < $prod->cantidadProductoModificador; $i++) {
						$contenedorli = uniqid();
						if($prod->cantidadProductoModificador > 1){
							$lista .= "<li style='font-size:small;list-style:none;margin-left:-15px' iteracion='".$iteracion."' class='liContenedor' contenedor='".$contenedorli."' nombre='".$prod->nombreProductoModificador."' varios='".$prod->variosProductoModificador."'  idModTipo='".$prod->idModificadorTipo."' idProd='".$prod->idProducto."' idProdModTipo='".$prod->idProductoModificador."' >$prod->nombreProductoModificador (".($i+1).")</li>";
							$modTipo .= '<a class="btn btn-default col-3 mb-1 ml-1 modTipo" nivel="'.$nivel.'" iteracion="'.$iteracion.'" contenedorli="'.$contenedorli.'" contenedor="'.$contenedor.'" varios="'.$prod->variosProductoModificador.'" idModTipo="'.$prod->idModificadorTipo.'" idProd="'.$prod->idProducto.'" idProdModTipo="'.$prod->idProductoModificador.'">'.$prod->nombreProductoModificador.' ('.($i+1).')</a>';
						}
						else{
							$lista .= "<li style='font-size:small;list-style:none;margin-left:-15px' iteracion='".$iteracion."' class='liContenedor' contenedor='".$contenedorli."' nombre='".$prod->nombreProductoModificador."'  varios='".$prod->variosProductoModificador."'  idModTipo='".$prod->idModificadorTipo."' idProd='".$prod->idProducto."' idProdModTipo='".$prod->idProductoModificador."' >$prod->nombreProductoModificador</li>";
							$modTipo .= '<a class="btn btn-default col-3 mb-1 ml-1 modTipo" nivel="'.$nivel.'" iteracion="'.$iteracion.'"  contenedorli="'.$contenedorli.'" contenedor="'.$contenedor.'" varios="'.$prod->variosProductoModificador.'" idModTipo="'.$prod->idModificadorTipo.'" idProd="'.$prod->idProducto.'" idProdModTipo="'.$prod->idProductoModificador.'">'.$prod->nombreProductoModificador.'</a>';
						}
					}
				}
			}
		}
		if($nivel != 1){
			$lista .="</ul>";
			$lista .="</li>";
			$lista .="</li>";
		}

		$lista .="</ul>";
		$modTipo .= ($nivel == 1) ? "</div><div class='listaModificadores'></div></div>": "";
		$respuesta["codigo"] = 200;
		$respuesta["contenedor"] = $contenedor;
		$respuesta["modTipo"] =$modTipo;
		$respuesta["lista"] =$lista;
		$respuesta["option"] =$optionSenorita;
		$respuesta["optionCo"] =$optionCocinero;
		$respuesta["cocinero"] =$cocinero;
		echo json_encode($respuesta);
	}
	public function TraerProductoModificadoresDetalle(){

		$idModTipo = $this->input->post("idModTipo");
		$idProd = $this->input->post("idProd");
		$idProdModTipo = $this->input->post("idProdModTipo");
		$varios = $this->input->post("varios");
		$contenedorli = $this->input->post("contenedorli");
		$iteracion = $this->input->post("iteracion");
		$multiSeleccion = $this->input->post("multiSeleccion");
		$maxSeleccion = $this->input->post("maxSeleccion");
		$seleccionados = json_decode($this->input->post("seleccionados"));
		if(!is_array($seleccionados)){
			$seleccionados = array();
		}

		$condicion = array(
			"productoModificadorDetalle.idProducto" => $idProd,
			"productoModificadorDetalle.estadoProductoModificadorDetalle" => "Activo",
			"productoModificadorDetalle.idModificadorTipoProductoModificadorDetalle" => $idModTipo,
		);

		$join= array(
			array(
				"tabla" => "modificador",
				"condicion" => "productoModificadorDetalle.idModificador = modificador.idModificador",
				"tipo" => "left",
				"campos" => "modificador.idProducto AS idProdMod"
			),
		);

		$ordenCampos = "productoModificadorDetalle.idModificador ASC";
		$productos = TraerDatosJoin('productoModificadorDetalle',$condicion,$ordenCampos,$join);
		$div = "";
		if($productos !== false){
			$div.='<div class="row">';
			$pd=0;
			$id= uniqid();
			foreach ($productos as $prod){
				$varios = ($prod->idProdMod != 0) ? 1 : 0;
				$claseSeleccion = (in_array($prod->idModificador, $seleccionados)) ? 'bg-success' : 'bg-default';
				$div.='<div class="col-lg-2">
				<div class="card-container mt-2">
				<div class="card-custom modificador '.$claseSeleccion.'" iteracion="'.$iteracion.'"  style="height:110px;" contenedorli="'.$contenedorli.'"  idProdMod="'.$prod->idProdMod.'" varios="'.$varios.'" idContenedor="'.$id.'" idProdModDet="'.$prod->idProductoModificadorDetalle.'" idMod="'.$prod->idModificador.'" nombre="'.$prod->nombreProductoModificadorDetalle.'" multiseleccion="'.$multiSeleccion.'" maxseleccion="'.$maxSeleccion.'">
				<input type="hidden" class="aumento" value="'.$prod->aumentoProductoModificadorDetalle.'">
				<div class="info-card">
				<p class="nombre" style="font-size: small;">'.$prod->nombreProductoModificadorDetalle.'</p>
				<p style="font-size: small;">(+'.$prod->aumentoProductoModificadorDetalle.')</p>
				</div>
				</div>
				</div>
				</div>';
				if ($pd == 3){
					$div.='</div>
					<div class="row">';
				}
				$pd++;
			}
			$div.='</div>

			<div id="'.$id.'" class="col-12 mt-3 ml-2" >
			<div class="modificadorTipoDetalle "></div>
			<div class="listaModificadoresDetalle"></div>
			</div>
			';
		}
		$respuesta["codigo"] = 200;
		$respuesta["div"] = $div;
		$respuesta["iteracion"] = $iteracion;
		echo json_encode($respuesta);
	}
	public function TraerProductoModificadoresDetallePrecio(){

		$idProducto = $this->input->post("idProducto");
		$idModificador = $this->input->post("idModificador");
		$idProdModDet = $this->input->post("idProdModDet");
		$contenedorli = $this->input->post("contenedorli");
		$iteracion = $this->input->post("iteracion");

		$condicion = array(
			"productoModificadorDetalle.idProductoModificadorDetalle" => $idProdModDet,
		);
		$ordenCampos = "productoModificadorDetalle.idModificador ASC";
		// $join= array(
		// 	array(
		// 		"tabla" => "modificador",
		// 		"condicion" => "productoModificadorDetalle.idModificador = modificador.idModificador",
		// 		"tipo" => "left",
		// 		"campos" => "productoModificadorDetalle.idProducto AS idProdMod, modificador.nombreModificador"
		// 	),
		// );
		//$productos = TraerDatosJoin('productoModificadorDetalle',$condicion,$ordenCampos,$join);
		$productos = TraerUnDato('productoModificadorDetalle',$condicion);
		if($productos !== false){
			$lista 	= "<ul>";
			$lista .= " <li style='list-style:none;margin-left:-15px' class='liContenedorProducto' it='$iteracion' aumento='$productos->aumentoProductoModificadorDetalle' idMod='$productos->idModificador' nombre='$productos->nombreProductoModificadorDetalle' >$productos->nombreProductoModificadorDetalle (+$productos->aumentoProductoModificadorDetalle)</li>";
			//$lista .= "	</li>";
			$lista .= "</ul>";
		}

		$respuesta["codigo"] = 200;
		$respuesta["lista"] = $lista;
		echo json_encode($respuesta);
	}
	public function TraerServicioCategoria(){

		$idCategoria = $this->input->post("idCategoria");
		$condicion="";
		if($idCategoria != "T"){
			$condicion = array('servicio.idServicioCategoria' => $idCategoria);
		}
		$join = array(
			array(
				"tabla" => "servicioCategoria",
				"condicion" => "servicio.idServicioCategoria = servicioCategoria.idServicioCategoria",
				"campos"=>"servicioCategoria.nombreServicioCategoria"
			)
		);
		$servicios = TraerDatosJoin('servicio',$condicion,'',$join);
		$div = "";
		if($servicios !== false){
			$div.='<div class="row">';
			$pd=0;
			foreach ($servicios as $prod){
				$div.='<div class="col-lg-3">
				<div class="card-container mt-2">
				<div class="card-custom servicio" idp="'.$prod->idServicio.'" nombreCategoria="'.$prod->nombreServicioCategoria.'" idc="'.$prod->idServicioCategoria.'">
				<div class="info-card">
				<p>('.$prod->tiempoServicio.' MIN)</p>
				</div>
				</div>
				</div>
				</div>';
				if ($pd == 3){
					$div.='</div>
					<div class="row">';
				}
				$pd++;
			}
			$div.='</div>';
		}
		$respuesta["codigo"] = 200;
		$respuesta["div"] = $div;
		echo json_encode($respuesta);
	}
	public function TraerSenoritaCategoria(){

		$idCategoria = $this->input->post("idCategoria");
		$idServicio = $this->input->post("idServicio");

		$condicion = array(
			"estadoSenoritaCategoria"=>"Activo",
		);
		$senoritas = TraerDatos('senoritaCategoria',$condicion);
		$servicio = TraerUnDato('servicio',array("idServicio"=>$idServicio));
		$servicioDetalle = TraerDatos('servicioDetalle',array("idServicio"=>$idServicio));
		$servicioCategoria = TraerUnDato('servicioCategoria',array("idServicioCategoria"=>$idCategoria));

		$senoritaServicio = array();
		foreach($senoritas as $sen){
			$sen->montoServicioDetalle = '';
			foreach($servicioDetalle as $ser2){
				if($sen->idSenoritaCategoria == $ser2->idSenoritaCategoriaServicioDetalle){
					$sen->montoServicioDetalle = $ser2->montoServicioDetalle;
				}
			}
			$sen->tiempoServicio = $servicio->tiempoServicio;
			$sen->nombreServicioCategoria = $servicioCategoria->nombreServicioCategoria;
			$sen->idServicioCategoria = $servicioCategoria->idServicioCategoria;
			array_push($senoritaServicio,$sen);
		}
		$div = "";
		if($senoritaServicio !== false){
			$div.='<div class="row">';
			$pd=0;
			foreach ($senoritas as $prod){
				$div.='<div class="col-lg-3">
				<div class="card-container mt-2">
				<div class="card-custom senorita" idp="'.$prod->idSenoritaCategoria.'" idser="'.$idServicio.'" idcli="'.$prod->idServicioCategoria.'">
				<div class="info-card">
				<input type="hidden" class="precio" value="'.$prod->montoServicioDetalle.'">
				<input type="hidden" class="nombre" value="'.$prod->nombreServicioCategoria.' ('.$prod->tiempoServicio.' MIN) '.$prod->nombreSenoritaCategoria.'">
				<p>	('.$prod->tiempoServicio.' MIN)<br>
				'.$prod->nombreSenoritaCategoria.'</p>
				</div>
				</div>
				</div>
				</div>';
				if ($pd == 3){
					$div.='</div>
					<div class="row">';
				}
				$pd++;
			}
			$div.='</div>';
		}
		$respuesta["codigo"] = 200;
		$respuesta["div"] = $div;
		echo json_encode($respuesta);
	}
	public function TraerSenoritaOption(){

		$idCategoria = $this->input->post("idCategoria");
		$idServicio = $this->input->post("idServicio");
		$idServicioCategoria = $this->input->post("idServicioCategoria");
		$nombre = $this->input->post("nombre");
		$nombre2 = $this->input->post("nombre2");
		$precio = $this->input->post("precio");

		$condicion = array(
			"idSenoritaCategoria"=>$idCategoria,
			"estadoSenorita"=>"Activo",
		);
		$senoritas = TraerDatos('senorita',$condicion);

		if($senoritas !== false){
			$div = "";
			$div.='<div class="row">';
			$pd=0;
			foreach ($senoritas as $prod){
				$div.='<div class="col-lg-3">
				<div class="card-container mt-5">
				<div class="card-custom nombreSenorita" >
				<div class="info-card">
				<input type="hidden" class="precio" value="'.$precio.'">
				<input type="hidden" class="nombre" value="'.$nombre.'">
				<input type="hidden" class="nombre2" value="'.$nombre2.' - '.$prod->apodoSenorita.'">
				<input type="hidden" class="idSenorita" value="'.$prod->idSenorita.'">
				<input type="hidden" class="idSenoritaCategoria" value="'.$prod->idSenoritaCategoria.'">
				<input type="hidden" class="idServicio" value="'.$idServicio.'">
				<input type="hidden" class="idServicioCategoria" value="'.$idServicioCategoria.'">
				<p>	'.$prod->apodoSenorita.'</p>
				</div>
				</div>
				</div>
				</div>';
				if ($pd == 3){
					$div.='</div>
					<div class="row">';
				}
				$pd++;
			}
			$div.='</div>';
			$respuesta["codigo"] = 200;
			$respuesta["div"] = $div;
		}
		else{
			$respuesta["codigo"] = 500;
		}
		echo json_encode($respuesta);
	}
	public function TraerCorrelativo($tipo){
		$condicion = array("tipoDocumentoFactura"=>$tipo);
		$cajas = TraerUnDato('factura',$condicion,'fechaRegistroFactura DESC');
		$last = 1;
		if($cajas !== false){
			$last = $cajas->numeroDocumentoFactura + 1;
		}
			$respuesta["codigo"] = 200;
			$respuesta["last"] = $last;
		echo json_encode($respuesta);
	}
	public function TraerCaja(){

		$idCaja = $this->input->post("idCaja");

		$condicion = array(
			"idCajaCajaDocumento"=>$idCaja,
			"estadoCajaDocumento"=>"Activo",
		);
		$join = array(
			array(
				"tabla" => "documento",
				"condicion" => "cajaDocumento.idDocumentoCajaDocumento = documento.idDocumento",
				"orden" => "left",
				"campos" => "impresionPdf, aliasDocumento, nombreDocumento, idDocumento"
			),
		);
		$cajas = TraerDatosJoin('cajaDocumento',$condicion,'',$join);

		if($cajas !== false){
			$div = "";
			//$div.='<option value=""></option>';
			foreach ($cajas as $prod){
				$div.='<option idDoc="'.$prod->idCajaDocumento.'" idDocumento="'.$prod->idDocumento.'" pdf="'.$prod->impresionPdf.'" actual="'.$prod->actualCajaDocumento.'" value="'.$prod->aliasDocumento.'">'.$prod->nombreDocumento.'</option>';
			}
			$respuesta["codigo"] = 200;
			$respuesta["div"] = $div;
		}
		else{
			$respuesta["codigo"] = 500;
		}
		echo json_encode($respuesta);
	}
	public function TraerMesasZona(){

		$idZona = $this->input->post("idZona");

		$condicion = array(
			"idZona"=>$idZona,
			"estadoZonaMesa !="=>"Inactivo",
			"estadoZonaMesa !="=>"Borrado",
		);
		$mesas = TraerDatos('zonaMesa',$condicion);

		if($mesas !== false){
			$div = "<label>Mesas</label>";
			$div.='<div class="row">';
			$pd=0;
			foreach ($mesas as $prod){
				$ocupada = ($prod->estadoZonaMesa == "Ocupada") ? "1" : "0";
				$bgocupada = ($prod->estadoZonaMesa == "Ocupada") ? "bg-danger" : "";
				$div.='<div class="col-lg-2 ">
				<div class="card-container mt-2 ">
				<div class="card-custom mesaHacerCuenta '.$bgocupada.'" ocupada="'.$ocupada.'" style="height:110px;">
				<div class="info-card">
				<input type="hidden" class="idMesa" value="'.$prod->idZonaMesa.'">
				<input type="hidden" class="idZona" value="'.$prod->idZona.'">
				<p>	Mesa #'.$prod->nombreZonaMesa.'</p>
				</div>
				</div>
				</div>
				</div>';
				if ($pd == 5){
					$div.='</div>
					<div class="row">';
				}
				$pd++;
			}
			$div.='</div>';
			$respuesta["codigo"] = 200;
			$respuesta["div"] = $div;
		}
		else{
			$respuesta["codigo"] = 500;
		}
		echo json_encode($respuesta);
	}

	// Lista de zonas de delivery activas (solo lectura), para que el cajero
	// pueda verificar visualmente si la ubicación del cliente cae dentro de
	// alguna. La creación/edición/eliminación de zonas sigue siendo exclusiva
	// del admin (AdminOnline).
	public function ZonasDeliveryMapa(){
		$zonas = TraerDatos('zonadelivery',array("estadoZonaDelivery"=>"Activo"));
		$datos = array();
		if($zonas){
			foreach ($zonas as $z){
				$datos[] = array(
					"idZonaDelivery"=>$z->idZonaDelivery,
					"nombreZonaDelivery"=>$z->nombreZonaDelivery,
					"colorZonaDelivery"=>$z->colorZonaDelivery,
					"poligonoZonaDelivery"=>json_decode($z->poligonoZonaDelivery)
				);
			}
		}
		echo json_encode(array("codigo"=>200,"zonas"=>$datos));
	}

	// Verifica si un punto (lat,lng) cae dentro de alguna zona de delivery activa.
	public function VerificarZonaDelivery(){
		$lat = $this->input->post("lat");
		$lng = $this->input->post("lng");

		if(!is_numeric($lat) || !is_numeric($lng)){
			echo json_encode(array("codigo"=>400,"mensaje"=>"Coordenadas inválidas."));
			return;
		}

		$zona = EncontrarZonaDelivery($lat,$lng);
		if($zona){
			echo json_encode(array(
				"codigo"=>200,
				"enZona"=>true,
				"zona"=>array(
					"idZonaDelivery"=>$zona->idZonaDelivery,
					"nombreZonaDelivery"=>$zona->nombreZonaDelivery,
					"colorZonaDelivery"=>$zona->colorZonaDelivery
				)
			));
		} else {
			echo json_encode(array("codigo"=>200,"enZona"=>false));
		}
	}

	public function PagarServicio(){

		if($this->input->method(TRUE) == "POST"){
			$idCliente = $this->input->post("idCliente");
			$idCaja = $this->input->post("idCaja");
			$cliente = $this->input->post("cliente");
			$tipoDoc = $this->input->post("tipoDoc");
			$idDoc = $this->input->post("idDoc");
			$actual = $this->input->post("actual");
			$total = $this->input->post("total");
			$vuelto = $this->input->post("vuelto");
			$efectivo = $this->input->post("efectivo");
			$servicio = json_decode($this->input->post("servicio"));
			$TraerAperturaActual = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("estadoCorte" => "Vigente","idUsuarioCorte"=>$this->session->idUsuario));
			$aperturaActual = $TraerAperturaActual[0]["idCorteCaja"];
			IniciarTransaccion();

			$datosFactura =  array(
				"tipoFactura" => "Servicio",
				"idCliente" => $idCliente,
				"fechaFactura" => date('Y-m-d'),
				"horaFactura" => date('H:i:s'),
				"tipoDocumentoFactura" => $tipoDoc,
				"numeroDocumentoFactura" => ($actual + 1),
				"totalFactura" => $total,
				"vueltoFactura" => $vuelto,
				"efectivoFactura" => $efectivo,
				"idUsuario" => $this->session->idUsuario,
				"idCorte" => $aperturaActual,
				"idCajaFactura" => $idCaja,
				"nombreFactura" => $cliente,
				"estadoFactura" => "Cobrado",
			);

			$guardar = GuardarDatos('factura',$datosFactura);
			if($guardar){
				$idFactura = $guardar;
				$d = 0; //cantidad de detalles de servicios en factura
				$s = 0; // cantidad de servicios registrados
				foreach($servicio as $ser){
					$datosFacturaDetalle = array(
						"idFactura" => $idFactura,
						"idServicio" => $ser->servicio,
						"cantidadFacturaDetalle" => $ser->cantidad,
						"costoUnitarioFacturaDetalle" => $ser->monto,
						"subTotalFacturaDetalle" => $ser->monto,
						"descuentoFacturaDetalle" => "0.00",
						"estadoFacturaDetalle" => "Activo"
					);
					$guardarDetalle = GuardarDatos("facturaDetalle",$datosFacturaDetalle);

					if($guardarDetalle){
						$d++;
						$datosServicio =  array(
							"idSenorita" => $ser->senorita,
							"idServicio" => $ser->servicio,
							"montoSenoritaServicio" => $ser->monto,
							"idFacturaSenoritaServicio" => $guardarDetalle,
							"estadoSenoritaServicio" => "Finalizado"
						);
						$guardarServicio = GuardarDatos("senoritaServicio",$datosServicio);
						if($guardarServicio){
							$s++;
						}
						else{
							//DeshacerTransaccion();
							$respuesta['codigo'] = 502;
						}
					}
					else{
						//DeshacerTransaccion();
						$respuesta['codigo'] = 501;
					}
				}
				if($d == $s){
					$datosDoc = array(
						"actualCajaDocumento" => ($actual + 1)
					);
					$condicionDoc = array(
						"idCajaDocumento" => $idDoc,
					);
					$nDoc = EditarDatos("cajaDocumento",$datosDoc,$condicionDoc);
					if($nDoc){
						EjecutarTransaccion();
						$respuesta['codigo'] = 200;
						$respuesta['idFactura'] = md5($idFactura);
					}
					else{
						DeshacerTransaccion();
						$respuesta['codigo'] = 504;
					}
				}
				else{
					DeshacerTransaccion();
					$respuesta['codigo'] = 503;
				}
			}
			else{
				DeshacerTransaccion();
				$respuesta['codigo'] = 500;
			}

			echo json_encode($respuesta);

		}
	}
	public function CambioMesa(){
		if($this->input->method(TRUE) == "POST"){
			$idZona = $this->input->post("idZona");
			$zona = $this->input->post("nombre");
			$idMesa = $this->input->post("idMesa");
			$idPedido = $this->input->post("idPedido");

			$TraerMesaAnterior = TraerUnDatoIndividual("pedido","idMesaPedido",array("idPedido" => $idPedido));
			$idMesaAnterior = $TraerMesaAnterior[0]["idMesaPedido"];

			$datosPedido =  array(
				"idZonaPedido" => $idZona,
				"zonaPedido" => $zona,
				"idMesaPedido" => $idMesa,
				'aleatorioPedido' => uniqid()
			);
			IniciarTransaccion();
			$editarPedido = EditarDatos('pedido',$datosPedido,array('idPedido' => $idPedido));

			$datosMesa =  array(
				"estadoZonaMesa" => 'Activo',
				'aleatorioZonaMesa' => uniqid()
			);
			$editarMesaAnterior = EditarDatos('zonaMesa',$datosMesa,array('idZonaMesa' => $idMesaAnterior));

			$datosMesa =  array(
				"estadoZonaMesa" => 'Ocupada',
				'aleatorioZonaMesa' => uniqid()
			);
			$editarMesa = EditarDatos('zonaMesa',$datosMesa,array('idZonaMesa' => $idMesa));
			if($editarPedido && $editarMesa && $editarMesaAnterior){
				EjecutarTransaccion();
				$respuesta['codigo'] = 200;
			}
			else{
				DeshacerTransaccion();
				$respuesta['codigo'] = 504;
			}
			echo json_encode($respuesta);
		}
	}
	/**
	 * Guarda en `cliente` las correcciones hechas por el cajero a los datos
	 * del cliente al momento del cobro (dirección, teléfono, correo, NIT/DUI,
	 * NRC, giro, departamento y municipio). Solo actualiza los campos que
	 * llegaron con valor, para no borrar datos existentes con campos vacíos.
	 */
	private function _actualizarClienteDesdeFormulario($idCliente,$direccion,$telefono,$correo,$departamento,$municipio,$nit,$nrc,$giro){
		$idCliente = (int)$idCliente;
		if($idCliente <= 0){ return; }
		$cliente = $this->core->TraerUnDato('cliente',['idCliente' => $idCliente]);
		if(!$cliente){ return; }

		$direccion = (string)$direccion;
		$telefono = (string)$telefono;
		$correo = (string)$correo;
		$departamento = (string)$departamento;
		$municipio = (string)$municipio;
		$nit = (string)$nit;
		$nrc = (string)$nrc;
		$giro = (string)$giro;

		$campos = array();
		if($direccion !== ''){ $campos['direccionCliente'] = $direccion; }
		if($telefono !== ''){ $campos['telefonoCliente'] = $telefono; }
		if($correo !== ''){ $campos['emailCliente'] = $correo; }
		if($departamento !== ''){ $campos['departamentoCliente'] = $departamento; }
		if($municipio !== ''){ $campos['municipioCliente'] = $municipio; }
		if($nrc !== ''){ $campos['nrcCliente'] = $nrc; }
		if($giro !== ''){ $campos['giroCliente'] = $giro; }
		if($nit !== ''){
			if($cliente->facturarConCliente == "NIT"){ $campos['nitCliente'] = $nit; }
			else { $campos['duiCliente'] = $nit; }
		}

		if(!empty($campos)){
			EditarDatos('cliente',$campos,['idCliente' => $idCliente]);
		}
	}

	public function PagarProducto(){
		if($this->input->method(TRUE) == "POST"){
			$cliente = $this->input->post("cliente");
			$tipoCuenta = $this->input->post("tipoCuenta");
			$porConsumo = $this->input->post("porConsumo");
			$tipoAumento = $this->input->post("tipoAumento");
			$descuento = $this->input->post("descuento");
			$personas = $this->input->post("personas");
			$descuentoDolar = $this->input->post("descuentoDolar");
			$aumento = $this->input->post("aumento");
			$idZona = $this->input->post("idZona");
			$zona = $this->input->post("zona");
			$idMesa = $this->input->post("idMesa");
			$total = $this->input->post("total");
			$comentarioGeneral = $this->input->post("comentario");
			$propina = $this->input->post("propina");

			$transferencia = $this->input->post("transferencia");
			$envio = $this->input->post("envio");

			$coci = $this->input->post("coci");
			$productos = json_decode($this->input->post("productos"));
			/********************************************/
			$idPedidoGuardado = $this->input->post("idPedidoGuardado");
			// if($idPedidoGuardado>0){
			// 	$factura = TraerUnDato("factura",array("idReferenciaFactura" => $idPedidoGuardado));
			// 	$idFactura = $factura->idFactura;
			// 	$totalFactura = $factura->totalFactura;

			// 	$pedido = TraerUnDatoIndividual("pedido","totalPedido",array("idPedido" => $idPedidoGuardado));
			// 	$totalPedido = $pedido[0]["totalPedido"];
			// }
			$TraerAperturaActual = TraerUnDatoIndividual("corteCaja","idCorteCaja,idTurnoVigente",array("estadoCorte" => "Vigente","idUsuarioCorte"=>$this->session->idUsuario));
			$idCorte = $TraerAperturaActual[0]["idCorteCaja"];
			$idTurnoCorte = $TraerAperturaActual[0]["idTurnoVigente"];
			$joinTurno = array(
				array(
					"tabla"=>"corteTurno",
					"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
				),
			);
			$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);

			$idCorte = ($corte) ? $corte->idCorteCaja : 0;
			$idTurnoCorte = ($corte) ? $corte->idTurnoVigente : 0;
			$idUsuarioCorteTurno = ($corte) ? $corte->idUsuarioCorteTurno: 0;
			/********************************************/

			IniciarTransaccion();
			$idPedido = 0;
			$pass = true;
			// if($idPedidoGuardado >0){
			// 	$idPedido = $idPedidoGuardado;
			// 	$totalPedido+=$total;
			// 	$totalFactura+=$total;
			// 	$datosFactura =  array(
			// 		'totalFactura' => $totalFactura,
			// 	);
			// 	$datosPedido =  array(
			// 		'totalPedido' => $totalPedido,
			// 	);
			// 	$wherePedido = array('idPedido'=>$idPedidoGuardado);
			// 	$whereFactura = array('idReferenciaFactura'=>$idPedidoGuardado);
			// 	$guardarPedido = EditarDatos('pedido',$datosPedido,$wherePedido);
			// 	$guardarFactura = EditarDatos('factura',$datosFactura,$whereFactura);
			// 	if(!$guardarFactura || !$guardarPedido){
			// 		$pass = false;
			// 	}
			// } else {

			// }
			$datosFactura =  array(
				"idSucursalPedido" => $this->session->idSucursal,
				"tipoCuentaPedido" => $tipoCuenta,
				"idZonaPedido" => $idZona,
				"zonaPedido" => $zona,
				"tipoAumentoPedido" => $tipoAumento,
				"aumentoPedido" => $aumento,
				"idMesaPedido" => $idMesa,
				"totalPedido" => $total,
				"nombreClientePedido" => $cliente,
				"personasPedido" => $personas,
				"idUsuarioPedido" => $this->session->idUsuario,
				"fechaPedido" => date('Y-m-d'),
				"horaPedido" => date('H:i:s'),
				"estadoPedido" => "Finalizado",
			);

			$guardar = GuardarDatos('pedido',$datosFactura);
			if($guardar){ $idPedido = $guardar; } else { $pass = false; }

			$idCaja = $this->input->post("idCaja");
			$cliente = $this->input->post("cliente");
			$direccion = $this->input->post("direccion");
			$nrc = $this->input->post("nrc");
			$nit = $this->input->post("nit");
			$correlativo = $this->input->post("correlativo");
			$idCliente = $this->input->post("idCliente");
			$telefono = $this->input->post("telefono");
			$correo = $this->input->post("correo");
			$giro = $this->input->post("giro");
			$departamento = $this->input->post("departamento");
			$municipio = $this->input->post("municipio");
			$this->_actualizarClienteDesdeFormulario($idCliente,$direccion,$telefono,$correo,$departamento,$municipio,$nit,$nrc,$giro);
			$tipoDoc = $this->input->post("tipoDoc");
			$idDoc = $this->input->post("idDoc");
			$actual = $this->input->post("actual");
			$total = $this->input->post("total");
			$vuelto = $this->input->post("vuelto");
			$efectivo = $this->input->post("efectivo");
			$tarjeta = $this->input->post("tarjeta");
			$bitcoin = $this->input->post("bitcoin");
			$pedidosYa = $this->input->post("pedidosYa");

			$joinDocumento = array(
				array(
					"tabla" => "documento",
					"condicion" => "documento.idDocumento = cajaDocumento.idDocumentoCajaDocumento",
					"tipo" => "left"
				),
			);
			$datosDocumento = TraerUnDatoJoin("cajaDocumento",array("idCajaCajaDocumento" => $idCaja,"aliasDocumento" => $tipoDoc,"estadoCajaDocumento"=>"Activo"),$joinDocumento);
			$codigoDocumento = "00";
			if($tipoDoc == "FAC"){
				$codigoDocumento = "01";
			} else if($tipoDoc == "CCF") {
				$codigoDocumento = "03";
			}
			if($tipoDoc == "FAC" || $tipoDoc == "CCF"){
				$numeroControl = generarNumeroControl($codigoDocumento,$correlativo);
				$codigoGeneracion = generarUuid();
			} else {
				$numeroControl = "";
				$codigoGeneracion = "";
			}
			$datosFac = array(
				"tipoFactura" => "Producto",
				"idCliente" => $idCliente,
				"idSucursalFactura" => $this->session->idSucursal,
				"idReferenciaFactura" => $idPedido,
				"fechaFactura" => date('Y-m-d'),
				"horaFactura" => date('H:i:s'),
				"tipoDocumentoFactura" => $tipoDoc,
				"resolucionFactura" => $datosDocumento->numeroResolucionCajaDocumento,
				"serieFactura" => $datosDocumento->serieCajaDocumento,
				"direccionFactura" => $direccion,
				"nrcFactura" => $nrc,
				"nitFactura" => $nit,
				"tipoCuentaFactura" => $tipoCuenta,
				"porConsumoFactura" => $porConsumo,
				"tipoPagoFactura" => "Contado",
				"numeroDocumentoFactura" => $correlativo,
				"totalFactura" => $total - $propina,
				"descuentoFactura" => $descuento,
				"descuentoDolarFactura" => $descuentoDolar,
				"propinaFactura" => $propina,
				"vueltoFactura" => $vuelto,
				"efectivoFactura" => $efectivo,
				"tarjetaFactura" => $tarjeta,
				"bitcoinFactura" => $bitcoin,
				"pedidosYaFactura" => $pedidosYa,
				"transferenciaFactura" => $transferencia,
				"envioFactura" => $envio,
				"idUsuario" => $this->session->idUsuario,
				"idCorte" => $idCorte,
				"idTurno"=>$idTurnoCorte,
				"idCajaFactura" => $idCaja,
				"nombreFactura" => $cliente,
				"numeroControl" => $numeroControl,
				"codigoGeneracion" => $codigoGeneracion,
				"estadoFactura" => "Cobrado",
			);
			$guardarFac = GuardarDatos('factura',$datosFac);
			if($guardarFac){
				$idFactura = $guardarFac;
				$datosDoc = array(
					"actualCajaDocumento" => ($actual + 1),
					'aleatorioCajaDocumento' => uniqid()
				);
				$condicionDoc = array( "idCajaDocumento" => $idDoc );
				$nDoc = EditarDatos("cajaDocumento",$datosDoc,$condicionDoc);
				if(!$nDoc){ $pass=false; }
				else{
					$mesas = TraerDatos("pedido",array("idMesaPedido"=>$idMesa,"estadoPedido"=>"Pendiente"));
					if($mesas){
						$cMesas = count($mesas);
						if($cMesas <= 1) {
							EditarDatos("zonaMesa",array('estadoZonaMesa'=>"Activo"),array("idZonaMesa" => $idMesa));
						}
					}
				}
			} else { $pass= false; }
			if($pass){
				$error = false;
				// $idPedido = $guardar;
				$d = 0; //cantidad de detalles de servicios en factura
				$s = 0; // cantidad de servicios registrados
				foreach($productos as $ser){
					$tipo = ($ser->tipo == "regular") ? "Producto" : (
						($ser->tipo == "especial") ? "Producto Especial" : (
							($ser->tipo == "empleado") ? "Producto Empleado" : '') );
							$datosFacturaDetalle = array(
								"idPedido" => $idPedido,
								"tipoPedido" => $tipo,
								"idCorte" => $idCorte,
								"idProductoPedidoDetalle" => $ser->idProducto,
								"cantidadPedidoDetalle" => $ser->cantidad,
								"precioPedidoDetalle" => $ser->precio,
								"precioOriginalPedidoDetalle" => $ser->precioOriginal,
								"regaliaPedidoDetalle" => $ser->regalia,
								"grupoPedidoDetalle" => $ser->grupo,
								//"senoritaPedidoDetalle" => ($ser->tipo == "especial" && GblTraerConfiguracion("ServicioSenorita") == "Si") ? $ser->senorita : 0,
								"senoritaPedidoDetalle" => $ser->cocinero,
								"comentarioPedidoDetalle" => $ser->comentario,
								"estadoPedidoDetalle" => "Activo"
							);
							$guardarDetalle = GuardarDatos("pedidoDetalle",$datosFacturaDetalle);
							if($guardarDetalle){
								$d++;
								$modificadores = $ser->modificadores;
								$mod = $this->guardarModificadores($guardarDetalle,$modificadores);
								if($mod == true ){
									$error = true;
									$respuesta['codigo'] = 502;
									break;
								}
								// Copia el detalle a facturaDetalle (usado por Producto mas vendido,
								// reportes de venta por item, utilidad por producto, etc.) — antes
								// solo se guardaba en pedidoDetalle y facturaDetalle quedaba vacia.
								$datosFacturaDetalleReal = array(
									"idFactura" => $idFactura,
									"idProducto" => $ser->idProducto,
									"idServicio" => 0,
									"cantidadFacturaDetalle" => $ser->cantidad,
									"precioUnitarioFacturaDetalle" => $ser->precio,
									"costoUnitarioFacturaDetalle" => 0,
									"subTotalFacturaDetalle" => $ser->precio * $ser->cantidad,
									"descuentoFacturaDetalle" => 0,
									"comentarioFacturaDetalle" => $ser->comentario,
									"estadoFacturaDetalle" => "Activo",
									"aleatorioFacturaDetalle" => uniqid(),
								);
								$guardarFacturaDetalle = GuardarDatos("facturaDetalle",$datosFacturaDetalleReal);
								if(!$guardarFacturaDetalle){
									$error = true;
									$respuesta['codigo'] = 506;
									break;
								}
							}
							else{
								$error = true;
								$respuesta['codigo'] = 501;
								break;
							}
						}
						if(!$error){
							$error = false;
							if($comentarioGeneral != ""){
								$datosComentario =  array(
									"idPedido" => $idPedido,
									"comentarioPedidoComentario" => $comentarioGeneral,
									"idUsuarioPedidoComentario" => $this->session->idUsuario,
									"fechaHoraPedidoComentario" => date('Y-m-d H:i:s'),
									"estadoPedidoComentario" => "Activo",
								);
								$comentario = GuardarDatos('pedidoComentario',$datosComentario);
								if($comentario){
									$error = false;
								}
								else{
									$error = true;
									$respuesta["codigo"] = 505;
								}
							}
						}
						if(!$error){
							EjecutarTransaccion();
							$respuesta['codigo'] = 200;
							$respuesta['idFactura'] = md5($idFactura);
							$respuesta['idFactura1'] = md5($idFactura);
							$respuesta['idPedido'] = $idPedido;
							if(GblTraerConfiguracion('DescargaInsumoVenta') == 'Si'){
								$respuesta['descarga'] = $this->DescargarInsumosPedido($idPedido);
							}
							$this->load->helper('vfd');
							vfd_show_total(
								'$'.number_format($total, 2),
								'$'.number_format($vuelto, 2)
							);
						}
						else{
							DeshacerTransaccion();
							$respuesta['codigo'] = 504;
						}
					}
					else{
						DeshacerTransaccion();
						$respuesta['codigo'] = 503;
					}
					echo json_encode($respuesta);
				}
			}
			public function VfdItem(){
				if($this->input->method(TRUE) == "POST"){
					$cantidad = $this->input->post("cantidad");
					$precio   = $this->input->post("precio");
					$producto = $this->input->post("producto");
					$this->load->helper('vfd');
					vfd_show_product($cantidad, '$'.$precio, $producto);
				}
				echo json_encode(["ok" => true]);
			}

			public function AbrirCuenta(){
				$cliente = $this->input->post("cliente");
				$idCliente =  $this->input->post("idCliente");
				if($idCliente=='')
				{
					$idCliente=0;
					$direccion = $this->input->post("direccion");
				}
				else {
					$clientedata = $this->core->TraerUnDato('cliente',['idCliente' => $idCliente]);
					if($clientedata)
					{
						$direccion =  $clientedata->direccionCliente;
					}
					else
					{
						$direccion ="";
					}
				}
				$personas = $this->input->post("personas");

				$tipoCuenta = $this->input->post("tipoCuenta");
				$tipoAumento = $this->input->post("tipoAumento");
				$aumento = $this->input->post("aumento");
				$idZona = $this->input->post("idZona");
				$zona = $this->input->post("zona");
				$idMesa = $this->input->post("idMesa");
				$comentarioGeneral = $this->input->post("comentario");
				$total = $this->input->post("total");
				$productos = json_decode($this->input->post("productos"));
				//$TraerAperturaActual = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("estadoCorte" => "Vigente","idUsuarioCorte"=>$this->session->idUsuario));
				//$idCorte = $TraerAperturaActual[0]["idCorteCaja"];

				IniciarTransaccion();
				$idPedido = 0;
				$pass = true;
				$datosFactura =  array(
					"idSucursalPedido" => $this->session->idSucursal,
					"tipoCuentaPedido" => $tipoCuenta,
					"idZonaPedido" => ($idZona != "") ? $idZona : 0,
					"zonaPedido" => $zona,
					"tipoAumentoPedido" => ($tipoAumento != "") ? $tipoAumento : "Ninguno",
					"aumentoPedido" => ($aumento != "") ? $aumento : 0.00,
					"idMesaPedido" => ($idMesa != "") ? $idMesa : 0 ,
					"totalPedido" => $total,
					"nombreClientePedido" => $cliente,
					"direccionClientePedido" => $direccion,
					"personasPedido" => $personas,
					"idUsuarioPedido" => $this->session->idUsuario,
					"fechaPedido" => date('Y-m-d'),
					"horaPedido" => date('H:i:s'),
					//"idCortePedido" => $idCorte,
					"estadoPedido" => "Pendiente",
					"idCliente" => $idCliente,
				);

				$guardar = GuardarDatos('pedido',$datosFactura);
				if($guardar){
					$idPedido = $guardar;
				} else {
					$pass = false;
				}
				if($pass){
					$error = false;

					// $idPedido = $guardar;
					$d = 0; //cantidad de detalles de servicios en factura
					$s = 0; // cantidad de servicios registrados
					foreach($productos as $ser){
						$tipo = ($ser->tipo == "regular") ? "Producto" : (
							($ser->tipo == "especial") ? "Producto Especial" : (
								($ser->tipo == "empleado") ? "Producto Empleado" : '') );
								$datosFacturaDetalle = array(
									"idPedido" => $idPedido,
									"tipoPedido" => $tipo,
									//"idCorte" => $idCorte,
									"idProductoPedidoDetalle" => $ser->idProducto,
									"cantidadPedidoDetalle" => $ser->cantidad,
									"regaliaPedidoDetalle" => $ser->regalia,
									"precioPedidoDetalle" => $ser->precio,
									"grupoPedidoDetalle" => $ser->grupo,
									"precioOriginalPedidoDetalle" => $ser->precioOriginal,
									//"senoritaPedidoDetalle" => ($ser->tipo == "especial" && GblTraerConfiguracion('ServicioSenorita') == 'Si') ? $ser->senorita : 0,
									"senoritaPedidoDetalle" => $ser->cocinero,
									"comentarioPedidoDetalle" => $ser->comentario,
									"estadoPedidoDetalle" => "Activo"
								);
								$guardarDetalle = GuardarDatos("pedidoDetalle",$datosFacturaDetalle);
								if($guardarDetalle){
									$d++;
									$modificadores = $ser->modificadores;
									$mod = $this->guardarModificadores($guardarDetalle,$modificadores);
									if($mod == true ){
										$error = true;
										$respuesta['codigo'] = 502;
										break;
									}

								}
								else{
									$error = true;
									$respuesta['codigo'] = 501;
									break;
								}
							}
							if(!$error){
								$error = false;
								if($tipoCuenta == "local" && $idMesa != ""){
									$mesa = EditarDatos('zonaMesa',array('estadoZonaMesa'=>'Ocupada',"aleatorioZonaMesa"=>uniqid()),array('idZonaMesa' => $idMesa));
									if($mesa){
										$error = false;
									}
									else{
										$error = true;
										$respuesta["codigo"] = 503;
									}
								}
							}
							if(!$error){
								$error = false;
								if($comentarioGeneral != ""){
									$datosComentario =  array(
										"idPedido" => $idPedido,
										"comentarioPedidoComentario" => $comentarioGeneral,
										"idUsuarioPedidoComentario" => $this->session->idUsuario,
										"fechaHoraPedidoComentario" => date('Y-m-d H:i:s'),
										"estadoPedidoComentario" => "Activo",
									);
									$comentario = GuardarDatos('pedidoComentario',$datosComentario);
									if($comentario){
										$error = false;
									}
									else{
										$error = true;
										$respuesta["codigo"] = 504;
									}
								}
							}
							if(!$error){
								EjecutarTransaccion();
								$respuesta['codigo'] = 200;
								$respuesta['idPedido'] = $idPedido;
							}
							else{
								DeshacerTransaccion();
							}
						}
						else{
							DeshacerTransaccion();
							$respuesta['codigo'] = 500;
						}
						echo json_encode($respuesta);
					}
					public function AgregarACuenta(){
						$total = $this->input->post("total");
						$comentarioGeneral = $this->input->post("comentario");
						$idPedidoGuardado = $this->input->post("idPedido");
						$llevarLocal = $this->input->post("llevarlocal");
						$productos = json_decode($this->input->post("productos"));

						$pedido = TraerUnDatoIndividual("pedido","totalPedido,idZonaPedido",array("idPedido" => $idPedidoGuardado));
						$totalPedido = ($pedido) ? $pedido[0]["totalPedido"] : 0;
						$idZonaPedido = ($pedido) ? $pedido[0]["idZonaPedido"] : 0;
						//$TraerAperturaActual = TraerUnDatoIndividual("corteCaja","idCorteCaja",array("estadoCorte" => "Vigente","idUsuarioCorte"=>$this->session->idUsuario));
						//$idCorte = $TraerAperturaActual[0]["idCorteCaja"];

						IniciarTransaccion();
						$idPedido = 0;
						$pass = true;
						$idPedido = $idPedidoGuardado;
						$totalPedido += $total;
						$datosPedido =  array(
							'totalPedido' => $totalPedido,
							'aleatorioPedido' => uniqid()
						);
						$wherePedido = array('idPedido'=>$idPedidoGuardado);
						$guardarPedido = EditarDatos('pedido',$datosPedido,$wherePedido);
						if(!$guardarPedido){
							$pass = false;
						}
						if($pass){
							$error = false;
							// $idPedido = $guardar;
							$d = 0; //cantidad de detalles de servicios en factura
							$s = 0; // cantidad de servicios registrados
							foreach($productos as $ser){
								$tipo = ($ser->tipo == "regular") ? "Producto" : (
									($ser->tipo == "especial") ? "Producto Especial" : (
										($ser->tipo == "empleado") ? "Producto Empleado" : '') );
										$datosFacturaDetalle = array(
											"idPedido" => $idPedido,
											"tipoPedido" => $tipo,
											//"idCorte" => $idCorte,
											"idProductoPedidoDetalle" => $ser->idProducto,
											"cantidadPedidoDetalle" => $ser->cantidad,
											"precioPedidoDetalle" => $ser->precio,
											"grupoPedidoDetalle" => $ser->grupo,
											"precioOriginalPedidoDetalle" => $ser->precioOriginal,
											"regaliaPedidoDetalle" => $ser->regalia,
											// "senoritaPedidoDetalle" => ($ser->tipo == "especial" && GblTraerConfiguracion("ServicioSenorita") == "Si") ? $ser->senorita : 0,
											"senoritaPedidoDetalle" => $ser->cocinero,
											"comentarioPedidoDetalle" => $ser->comentario,
											"llevarLocalPedidoDetalle" => $llevarLocal,
											"estadoPedidoDetalle" => "Activo"
										);
										$guardarDetalle = GuardarDatos("pedidoDetalle",$datosFacturaDetalle);
										if($guardarDetalle){
											$d++;
											$modificadores = $ser->modificadores;
											$mod = $this->guardarModificadores($guardarDetalle,$modificadores);
											if($mod == true ){
												$error = true;
												$respuesta['codigo'] = 502;
												break;
											}
										}
										else{
											$error = true;
											$respuesta['codigo'] = 501;
											break;
										}
									}
									if(!$error){
										$error = false;
										if($comentarioGeneral != ""){
											$datosComentario =  array(
												"idPedido" => $idPedido,
												"comentarioPedidoComentario" => $comentarioGeneral,
												"idUsuarioPedidoComentario" => $this->session->idUsuario,
												"fechaHoraPedidoComentario" => date('Y-m-d H:i:s'),
												"estadoPedidoComentario" => "Activo",
											);
											$comentario = GuardarDatos('pedidoComentario',$datosComentario);
											if($comentario){
												$error = false;
											}
											else{
												$error = true;
												$respuesta["codigo"] = 503;
											}
										}
									}

									if(!$error){
										EjecutarTransaccion();
										$respuesta['codigo'] = 200;
										$respuesta['idPedido'] = $idPedido;
									}
									else{
										DeshacerTransaccion();
										$respuesta['codigo'] = 504;
									}
								}
								else{
									DeshacerTransaccion();
									$respuesta['codigo'] = 503;
								}
								echo json_encode($respuesta);

							}
							public function FinalizarCuenta(){
								$idPedido = $this->input->post("idPedido");
								$porConsumo = $this->input->post("porConsumo");
								$idCaja = $this->input->post("idCaja");
								$tipoDoc = $this->input->post("tipoDoc");
								$idDoc = $this->input->post("idDoc");
								$actual = $this->input->post("actual");
								$total = $this->input->post("total");
								$vuelto = $this->input->post("vuelto");
								$efectivo = $this->input->post("efectivo");
								$bitcoin = $this->input->post("bitcoin");
								$tarjeta = $this->input->post("tarjeta");
								$pedidosYa = $this->input->post("pedidosYa");
								$transferencia = $this->input->post("transferencia");
								$envio = $this->input->post("envio");
								$propina = $this->input->post("propina");
								$descuento = $this->input->post("descuento");
								$descuentoDolar = $this->input->post("descuentoDolar");
								$nombreCliente = $this->input->post("cliente");
								$direccion = $this->input->post("direccion");
								$nrc = $this->input->post("nrc");
								$nit = $this->input->post("nit");
								$correlativo = $this->input->post("correlativo");
								$idCliente = $this->input->post("idCliente");
								$telefono = $this->input->post("telefono");
								$correo = $this->input->post("correo");
								$giro = $this->input->post("giro");
								$departamento = $this->input->post("departamento");
								$municipio = $this->input->post("municipio");
								$this->_actualizarClienteDesdeFormulario($idCliente,$direccion,$telefono,$correo,$departamento,$municipio,$nit,$nrc,$giro);
								// $cliente = TraerUnDatoIndividual("pedido","nombreClientePedido",array("idPedido" => $idPedido));
								// $nombreCliente = $cliente[0]["nombreClientePedido"];
								$tipoCuenta = TraerUnDatoIndividual("pedido","tipoCuentaPedido",array("idPedido" => $idPedido))[0]["tipoCuentaPedido"];

								$joinTurno = array(
									array(
										"tabla"=>"corteTurno",
										"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
									),
								);
								$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);

								$idCorte = ($corte) ? $corte->idCorteCaja : 0;
								$idCaja = ($corte) ? $corte->idCaja : 0;
								$idTurnoCorte = ($corte) ? $corte->idTurnoVigente : 0;
								$idUsuarioCorteTurno = ($corte) ? $corte->idUsuarioCorteTurno: 0;

								IniciarTransaccion();

								if($tipoCuenta == "local"){

									$idMesa = TraerUnDatoIndividual("pedido","idMesaPedido",array("idPedido" => $idPedido))[0]["idMesaPedido"];
									$mesas = TraerDatos("pedido",array("idMesaPedido"=>$idMesa,"estadoPedido"=>"Pendiente"));
									if($mesas){
										$cMesas = count($mesas);
										if($cMesas == 1) {
											$liberar = EditarDatos("zonaMesa",array('estadoZonaMesa'=>"Activo","aleatorioZonaMesa" =>uniqid()),array("idZonaMesa" => $idMesa));
											if($liberar){
												$error = false;
											}
											else{
												$respuesta['codigo'] = 503;
											}
										}
									}
								}

								$pedido = EditarDatos('pedido',array('estadoPedido'=>'Finalizado'),array('idPedido' => $idPedido));

								$joinDocumento = array(
									array(
										"tabla" => "documento",
										"condicion" => "documento.idDocumento = cajaDocumento.idDocumentoCajaDocumento",
										"tipo" => "left"
									),
								);
								$datosDocumento = TraerUnDatoJoin("cajaDocumento",array("idCajaCajaDocumento" => $idCaja,"aliasDocumento" => $tipoDoc,"estadoCajaDocumento"=>"Activo"),$joinDocumento);

								if($pedido){
									$datosFac = array(
										"tipoFactura" => "Producto",
										"idCliente" => $idCliente,
										"idSucursalFactura" => $this->session->idSucursal,
										"idReferenciaFactura" => $idPedido,
										"fechaFactura" => date('Y-m-d'),
										"horaFactura" => date('H:i:s'),
										"tipoDocumentoFactura" => $tipoDoc,
										"direccionFactura" => $direccion,
										"resolucionFactura" => $datosDocumento->numeroResolucionCajaDocumento,
										"serieFactura" => $datosDocumento->serieCajaDocumento,
										"nrcFactura" => $nrc,
										"nitFactura" => $nit,
										"tipoCuentaFactura" => $tipoCuenta,
										"porConsumoFactura" => $porConsumo,
										"tipoPagoFactura" => "Contado",
										"numeroDocumentoFactura" => ($tipoDoc == "FAC" || $tipoDoc == "CCF") ? $correlativo : ($actual + 1),
										// "tipoCuentaFactura" => $tipoCuenta,
										// "numeroDocumentoFactura" => ($actual + 1),
										"totalFactura" => $total - $propina,
										"propinaFactura" => $propina,
										"descuentoFactura" => $descuento,
										"descuentoDolarFactura" => $descuentoDolar,
										"vueltoFactura" => $vuelto,
										"efectivoFactura" => $efectivo,
										"tarjetaFactura" => $tarjeta,
										"bitcoinFactura" => $bitcoin,
										"pedidosYaFactura" => $pedidosYa,
										"transferenciaFactura" => $transferencia,
										"envioFactura" => $envio,
										"idUsuario" => $this->session->idUsuario,
										"idCorte" => $idCorte,
										"idTurno" => $idTurnoCorte,
										"idCajaFactura" => $idCaja,
										"nombreFactura" => $nombreCliente,
										"estadoFactura" => "Cobrado",
									);
									$guardarFac = GuardarDatos('factura',$datosFac);
									if($guardarFac){
										$idFactura = $guardarFac;
										// Copia el detalle del pedido a facturaDetalle (usado por Producto mas
										// vendido, reportes de venta por item, utilidad por producto, etc.) —
										// antes esta cuenta se facturaba sin dejar nunca detalle en facturaDetalle.
										$detallePedidoFactura = TraerDatos("pedidoDetalle",array("idPedido" => $idPedido,"estadoPedidoDetalle" => "Activo"));
										if($detallePedidoFactura){
											foreach($detallePedidoFactura as $detPed){
												$datosFacturaDetalleReal = array(
													"idFactura" => $idFactura,
													"idProducto" => $detPed->idProductoPedidoDetalle,
													"idServicio" => 0,
													"cantidadFacturaDetalle" => $detPed->cantidadPedidoDetalle,
													"precioUnitarioFacturaDetalle" => $detPed->precioPedidoDetalle,
													"costoUnitarioFacturaDetalle" => 0,
													"subTotalFacturaDetalle" => $detPed->precioPedidoDetalle * $detPed->cantidadPedidoDetalle,
													"descuentoFacturaDetalle" => 0,
													"comentarioFacturaDetalle" => $detPed->comentarioPedidoDetalle,
													"estadoFacturaDetalle" => "Activo",
													"aleatorioFacturaDetalle" => uniqid(),
												);
												GuardarDatos("facturaDetalle",$datosFacturaDetalleReal);
											}
										}
										$datosDoc = array(
											"actualCajaDocumento" => ($actual + 1),
											'aleatorioCajaDocumento' => uniqid()
										);
										$condicionDoc = array(
											"idCajaDocumento" => $idDoc,
										);
										$nDoc = EditarDatos("cajaDocumento",$datosDoc,$condicionDoc);
										if($nDoc){
											// if($tipoCuenta == "local"){

											// 	$idMesa = TraerUnDatoIndividual("pedido","idMesaPedido",array("idPedido" => $idPedido))[0]["idMesaPedido"];
											// 	$mesas = TraerDatos("pedido",array("idMesaPedido"=>$idMesa,"estadoPedido"=>"Pendiente"));
											// 	var_dump($mesas);
											// 	if($mesas){
											// 		$cMesas = count($mesas);
											// 		if($cMesas <= 1) {
											// 			$liberar = EditarDatos("zonaMesa",array('estadoZonaMesa'=>"Activo","aleatorioZonaMesa" =>uniqid()),array("idZonaMesa" => $idMesa));
											// 			if($liberar){
											// 				$error = false;
											// 				$respuesta['codigo'] = 200;
											// 				$respuesta['idFactura'] = $idFactura;
											// 			}
											// 			else{
											// 				$respuesta['codigo'] = 503;
											// 			}
											// 		}
											// 	}
											// }
											// else {
											// }
											$error = false;
											$respuesta['codigo'] = 200;
											$respuesta['idFactura'] = md5($idFactura);
											$respuesta['idFactura1'] = md5($idFactura);

										}else {
											$error = true;
											$respuesta['codigo'] = 502;
										}

									} else {
										$error = true;
										$respuesta['codigo'] = 501;
									}
								}
								else{
									$error = true;
									$respuesta['codigo'] = 500;
								}

								($error) ? DeshacerTransaccion() : EjecutarTransaccion();

								if(GblTraerConfiguracion('DescargaInsumoVenta') == 'Si'){
									$this->DescargarInsumosPedido($idPedido);
								}
								echo json_encode($respuesta);
							}
							public function VerDetalleCuenta(){
								if($this->input->method(TRUE) == "POST") {
									$idPedido = $this->input->post("idPedido");
									$join = array(
										array(
											"tabla"=>"zonaMesa",
											"condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
											"tipo" => "left",
											"campos" => "zonaMesa.nombreZonaMesa"
										),
										array(
											"tabla"=>"usuario",
											"condicion" => "pedido.idUsuarioPedido = usuario.idUsuario",
											"tipo" => "inner",
											"campos" => "usuario.nombreUsuario"
										),
									);
									$pedido = TraerUnDatoJoin("pedido",array("idPedido"=>$idPedido),$join);

									$joinCocinero = array(
										array(
											"tabla"=>"pedidoDetalle",
											"condicion" => "pedidoDetalle.senoritaPedidoDetalle = usuario.idUsuario",
											"tipo" => "inner",
										),
									);
									$cocineros = TraerDatosJoin("usuario",array("pedidoDetalle.idPedido"=>$idPedido,"pedidoDetalle.senoritaPedidoDetalle !="=>"0","activoUsuario"=>"1"),"",$joinCocinero);

									$tbody = '';

									$joinDetalle = array(
										array(
											"tabla"=>"producto",
											"condicion" => "pedidoDetalle.idProductoPedidoDetalle = producto.idProducto",
											"tipo" => "inner",
											"campos" => "producto.nombreProducto",
										),
									);
									$detallePedido = TraerDatosJoin("pedidoDetalle",array("idPedido"=>$idPedido,"estadoPedidoDetalle !="=>"Borrado"),"",$joinDetalle);
									if($detallePedido){
										$i = 0;
										foreach($detallePedido as $dp){
											$trPrim = '';
											$regalia = ($dp->regaliaPedidoDetalle) ? 'Si': 'No';
											$precio = ($dp->regaliaPedidoDetalle) ? '0.00': $dp->precioPedidoDetalle;
											$trPrim .= '<tr class="prim idt'.$i.' accordion-toggle" cantidad="'.$dp->cantidadPedidoDetalle.'" precio="'.$dp->precioPedidoDetalle.'" regalia="'.$regalia.'" >';
											$trPrim .= '<td>'.$dp->cantidadPedidoDetalle.'</td>';
											$trPrim .= '<td>'.$dp->nombreProducto.'</td>';
											$trPrim .= '<td>'.$precio.'</td>';
											$trPrim .= '<td>'.$regalia.'</td>';
											$trPrim .= '<td><a class="btn btn-sm btn-block btn-primary" data-toggle="collapse" href="#ita'.$i.'"><i class="fa fa-eye"></i></a></td>';
											$trPrim .= '<td><div class="icheck-success d-inline"><input type="checkbox" class="elemento" precio="'.$dp->precioPedidoDetalle.'" idPedido="'.$dp->idPedido.'" idPedidoDetalle="'.$dp->idPedidoDetalle.'" id="elemento'.$i.'" ><label for="elemento'.$i.'"></label></div></td>';
											$trPrim .= '</tr>';

											$trSec = '';
											$trSec .= '<tr class="sec idt'.$i.' hide-table-padding">';
											$trSec .= '<td colspan="6">';
											$trSec .= '<div id="ita'.$i.'" class="in p-0 collapse">';
											$trSec .= '<div class="botoneraProductoDetalle"><label>Comentario: </label><p>'.$dp->comentarioPedidoDetalle.'</p></div><hr>';
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
									$txtTipo = ($pedido->tipoCuentaPedido == "local") ? "<span class='badge badge-primary'>Local<span>" : (
										($pedido->tipoCuentaPedido == "llevar") ? "<span class='badge badge-success'>Llevar<span>" : (
											($pedido->tipoCuentaPedido == "domicilio") ? "<span class='badge badge-info'>Domicilio<span>" : (
												($pedido->tipoCuentaPedido == "Recoger") ? "<span class='badge badge-warning'>Domicilio<span>" : ""
												)
												)
											);
											$tbodyPedido = "
											<tr>
											<td>Total</td><td>$ ".$pedido->totalPedido."</td>
											<td>Tipo</td><td>".$txtTipo."</td>
											<td hidden><a class='btn btn-sm btn-block btn-default editarTipoCuenta'><i class='fas fa-pencil-alt'></i></a></td>
											</tr>
											<tr>
											<td>Cliente</td><td colspan='3'><label>".$pedido->nombreClientePedido." </td>
											<td hidden><a class='btn btn-sm btn-block btn-default'><i class='fas fa-pencil-alt'></i></a></td>
											</tr>
											<tr>
											<td>Mesa</td><td colspan='3'>".$pedido->zonaPedido." / Mesa #".$pedido->nombreZonaMesa."</td>
											<td hidden><a class='btn btn-sm btn-block btn-default'><i class='fas fa-pencil-alt'></i></a></td>
											</tr>
											<tr>
											<td>Mesero</td><td colspan='4'>".$pedido->nombreUsuario."</td>
											</tr>
											<tr>
											<td>Estado</td><td colspan='4'>".$pedido->estadoPedido."</td>
											</tr>
											";
											if($cocineros){
												$tbodyPedido .= "<tr>";
												$tbodyPedido .= "<td>Cocineros</td><td colspan='3'>";
												foreach($cocineros as $c){
													$tbodyPedido .= $c->nombreUsuario."<br>";
												}
												$tbodyPedido .= "</td></tr>";
											}
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
											$respuesta['tbody'] = $tbody;
											$respuesta['tbodyPedido'] = $tbodyPedido;
											$cobroPropina = GblTraerConfiguracion("cobroPropina");
											$respuesta['cobroPropina'] = $cobroPropina;

											echo json_encode($respuesta);
										}
									}
									public function CambiarEstadoDetalleCuenta(){
										if($this->input->method(TRUE) == "POST") {
											$idPedido = $this->input->post("idPedido");
											$tipo = $this->input->post("tipo");
											$motivo = $this->input->post("motivo");
											$detalle = json_decode($this->input->post("detalle"));
											$error = false;
											$total = 0;
											$espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);

											IniciarTransaccion();
											$arrayServidor = array();
											if ($tipo == "borrar") {
												$join = array(
													array(
														"tabla"=>"zonaMesa",
														"condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
														"tipo" => "left",
														"campos" => "zonaMesa.nombreZonaMesa"
													),
												);
												$condicionPedido =  array(
													"idPedido" => $idPedido
												);
												$pedido = TraerUnDatoJoin("pedido",$condicionPedido,$join);
												$salto = "\n";
												$divisor = "|";
												$linea = str_pad("",42,"_",STR_PAD_BOTH).$salto;

												$impresoras = TraerDatos("impresora",array('cocinaImpresora'=>'1',"estadoImpresora"=>"Activo","idSucursalImpresora" => $this->session->idSucursal));

												$espacios = 15;
												if($impresoras){
													foreach($impresoras  as $im){
														$datos = array(
															"idImpresora" => $im->idImpresora,
															"nombreImpresora" => $im->nombreImpresora,
															"recursoCompartido" => $im->recursoCompartidoImpresora,
															"IpImpresora" => $im->IpImpresora,
															"impresoraTipo" => $im->tipoImpresora
														);
														$arrayServidor[$im->idImpresora]['servidor'] = $im->servidorImpresora;
														$arrayServidor[$im->idImpresora]["datos"] = $datos;
														$arrayServidor[$im->idImpresora]["titulo"] = $espacioInicio.str_pad("ANULACION",$espacios," ",STR_PAD_BOTH).$salto;
														$arrayServidor[$im->idImpresora]["titulo"] .= $espacioInicio.str_pad("ZONA: ".$pedido->zonaPedido,$espacios," ",STR_PAD_BOTH).$salto;
														$arrayServidor[$im->idImpresora]["titulo"] .= $espacioInicio.str_pad("MESA # ".$pedido->nombreZonaMesa,$espacios," ",STR_PAD_BOTH).$salto;
														$arrayServidor[$im->idImpresora]["encabezado"] = $linea;
														$arrayServidor[$im->idImpresora]["encabezado"] .= $espacioInicio."MOTIVO: ".$motivo.$salto;
														$arrayServidor[$im->idImpresora]["encabezado"] .= $linea;
														$arrayServidor[$im->idImpresora]["encabezado"] .= $espacioInicio."CANT. DETALLE                           ".$salto;
														$arrayServidor[$im->idImpresora]["encabezado"] .= $linea;
														$arrayServidor[$im->idImpresora]["productos"] = "";
													}
												}
											}

											foreach($detalle as $d){
												$condicion =  array(
													"idPedidoDetalle" => $d->idPedidoDetalle,
												);
												if($tipo == "dar"   ){ $datos = array( "regaliaPedidoDetalle" => 1 , "idUsuarioPedidoDetalle" => $this->session->idUsuario , "motivoPedidoDetalle" => $motivo , "aleatorioPedidoDetalle"=>uniqid()); }
												if($tipo == "quitar"){ $datos = array( "regaliaPedidoDetalle" => 0 , "idUsuarioPedidoDetalle" => $this->session->idUsuario , "motivoPedidoDetalle" => $motivo , "aleatorioPedidoDetalle"=>uniqid()); }
												if($tipo == "borrar"){ $datos = array( "estadoPedidoDetalle" => "Borrado" , "idUsuarioPedidoDetalle" => $this->session->idUsuario , "motivoPedidoDetalle" => $motivo , "aleatorioPedidoDetalle"=>uniqid() ); }

												$editar = EditarDatos("pedidoDetalle",$datos,$condicion);

												if($editar){
													//$total  = $total + $d->precio;
													$total = TraerUnDatoIndividual("pedidoDetalle","SUM(precioPedidoDetalle * cantidadPedidoDetalle)",array("idPedido" => $idPedido, "estadoPedidoDetalle !=" => "Borrado", "regaliaPedidoDetalle" => "0"))[0]["SUM(precioPedidoDetalle * cantidadPedidoDetalle)"];
													$condicionPedido =  array(
														"idPedido" => $idPedido
													);
													if(is_null($total)){
														$total = "0.00";
													}
													$datosPedido = array("totalPedido" => $total,"aleatorioPedido"=>uniqid());
													$editarPedido = EditarDatos("pedido",$datosPedido,$condicionPedido);
													if(!$editarPedido){
														$error =  true;
														$respuesta["codigo"] = 501;
														break;
													}
													if ($tipo == "borrar") {
														$joinDet = array(
															array(
																"tabla" => "producto",
																"condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
																"tipo" => "inner",
																"campos" => "nombreProducto,impresoraProducto"
															)
														);
														$condicionPedido = array("idPedidoDetalle"=>$d->idPedidoDetalle);
														$pedidoDetalle = TraerUnDatoJoin("pedidoDetalle",$condicionPedido,$joinDet);
														$espacioCant = 5;
														$espacioDes = 17;
														$cantidad = $pedidoDetalle->cantidadPedidoDetalle;
														$cantidad = $pedidoDetalle->cantidadPedidoDetalle;
														$descripcion = substr($pedidoDetalle->nombreProducto,0,40);
														$cuenta = $espacioInicio.str_pad($cantidad,$espacioCant," ",STR_PAD_RIGHT);
														$cuenta .= str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT).$salto;
														$arrayServidor[$pedidoDetalle->impresoraProducto]["productos"] .= urlencode($cuenta);
													}
												}	else {
													$error =  true;
													$respuesta["codigo"] = 500;
													break;
												}
											}
											if(!$error){
												$respuesta["codigo"] = 200;
												$respuesta["total"] = $total;
												$respuesta["idPedido"] = $idPedido;
												$respuesta["servidor"] = $arrayServidor;
											}
											(!$error) ? EjecutarTransaccion() : DeshacerTransaccion();
											echo json_encode($respuesta);
										}
									}
									public function DividirCuenta(){
										if($this->input->method(TRUE) == "POST") {
											$idPedido = $this->input->post("idPedido");
											$total = $this->input->post("total");
											$nombre = $this->input->post("nombre");
											$detalle = json_decode($this->input->post("detalle"));
											$joinTurno = array(
												array(
													"tabla"=>"corteTurno",
													"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
												),
											);
											$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);
											$idCorte = ($corte) ? $corte->idCorteCaja : 0;

											$pedido = TraerUnDato("pedido",array("idPedido"=>$idPedido));
											IniciarTransaccion();
											$error = false;
											$totalPedido = $pedido->totalPedido;
											$datosFactura =  array(
												"idSucursalPedido" => $this->session->idSucursal,
												"tipoCuentaPedido" => $pedido->tipoCuentaPedido,
												"idZonaPedido" => $pedido->idZonaPedido,
												"zonaPedido" => $pedido->zonaPedido,
												"tipoAumentoPedido" => $pedido->tipoAumentoPedido,
												"aumentoPedido" => $pedido->aumentoPedido,
												"idMesaPedido" => $pedido->idMesaPedido,
												"totalPedido" => $total,
												"nombreClientePedido" => $nombre,
												"idUsuarioPedido" => $this->session->idUsuario,
												"fechaPedido" => date('Y-m-d'),
												"horaPedido" => date('H:i:s'),
												"idCortePedido" => $idCorte,
												"estadoPedido" => "Pendiente",
											);

											$guardar = GuardarDatos('pedido',$datosFactura);
											if($guardar){
												$nIdPedido = $guardar;
												foreach($detalle as $d){
													$datosDetalle = array(
														"idPedido" => $nIdPedido
													);
													$condicion = array(
														"idPedidoDetalle" => $d->idPedidoDetalle
													);
													$editar = EditarDatos("pedidoDetalle",$datosDetalle,$condicion);
													if(!$editar){
														$error = true;
														$respuesta["codigo"] = 501;
														break;
													}
													else{
														$datosDetalle = array(
															"totalPedido" => ($totalPedido - $total),
															"aleatorioPedido" => uniqid()
														);
														$condicion = array(
															"idPedido" => $idPedido
														);
														$editarPedido = EditarDatos("pedido",$datosDetalle,$condicion);
														if(!$editarPedido){
															$error = true;
															$respuesta["codigo"] = 502;
															break;
														}
														else{
															$respuesta["codigo"] = 200;
														}
													}
												}
											}
											else{
												$error =  true;
												$respuesta["codigo"] = 500;
											}

											(!$error) ? EjecutarTransaccion() : DeshacerTransaccion();

											echo json_encode($respuesta);

										}
									}
									public function UnirCuenta(){
										if($this->input->method(TRUE) == "POST") {
											$idPedido = $this->input->post("idPedido");
											$total = $this->input->post("total");
											$detalle = json_decode($this->input->post("detalle"));

											$respuesta["codigo"] = 200;
											$error = false;

											IniciarTransaccion();
											foreach($detalle as $d){
												$datosDetalle = array(
													"idPedido" => $idPedido
												);
												$condicion = array(
													"idPedido" => $d->idPedidoSec
												);
												$editar = EditarDatos("pedidoDetalle",$datosDetalle,$condicion);
												if(!$editar){
													$error = true;
													$respuesta["codigo"] = 500;
													break;
												}
												else{
													$datosDetalle = array(
														"estadoPedido" => "Inactivo"
													);
													$editar = EditarDatos("pedido",$datosDetalle,$condicion);
													if(!$editar){
														$error = true;
														$respuesta["codigo"] = 501;
														break;
													}
												}
											}
											if(!$error){
												$condicion = array(
													"idPedido" => $idPedido
												);
												$editarPedido = ActualizarCorrelativo("pedido",$condicion,"totalPedido",$total);
												if(!$editarPedido){
													$error = true;
													$respuesta["codigo"] = 502;
												}
											}
											(!$error) ? EjecutarTransaccion() : DeshacerTransaccion();

											echo json_encode($respuesta);
										}
									}
									public function guardarModificadores($idCuenta = 0, $arrayMod = array(), $idReferencia = 0){
										$error = false;
										if(count($arrayMod) > 0){
											foreach($arrayMod as $mod){
												$datosModificador =  array(
													"idPedidoDetalle" => $idCuenta,
													"idReferenciaPedidoSubDetalle" => $idReferencia,
													"variosPedidoSubDetalle" => $mod->varios,
													"nombreModTipoPedidoSubDetalle" => $mod->nombreModTipo,
													"aumentoPedidoSubDetalle" => (isset($mod->aumento)) ? $mod->aumento : 0,
													"idModPedidoSubDetalle" => (isset($mod->idMod)) ? $mod->idMod : 0,
													"nombrePedidoSubDetalle" => (isset($mod->nombreMod)) ? $mod->nombreMod : '',
													"estadoPedidoSubDetalle" => "Pendiente"
												);
												$guardarModificador = GuardarDatos("pedidoSubDetalle",$datosModificador);
												if($guardarModificador){
													if($mod->varios != "0"){
														$idRef =  $guardarModificador;
														$array = $mod->subMod;
														$subMod = $this->guardarModificadores($idCuenta,$array,$idRef);
														if($subMod == true){
															$error = true;
															break;
														}
													}
												}
												else{
													$error = true;
													break;
												}

											}
										}
										return $error;
									}
									public function llamarModificadores($idPedidoDetalle = 0,$idReferencia = 0){
										$mod = '<ul>';
										$modificadores = TraerDatos("pedidoSubDetalle",array("idPedidoDetalle"=>$idPedidoDetalle,"idReferenciaPedidoSubDetalle" => $idReferencia));
										if($modificadores){
											foreach($modificadores as $m){
												$nombre = ($m->nombrePedidoSubDetalle != "0") ? ' - '.$m->nombrePedidoSubDetalle.' ('.$m->aumentoPedidoSubDetalle.')' : '';
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
									public function RealizarMovimientoCaja(){

										if ($this->input->method(TRUE) == "POST") {
											$caja = $this->input->post("cajaMovimiento");
											$recibe = $this->input->post("movimientoRecibe");
											$entrega = $this->input->post("movimientoEntrega");
											$concepto = $this->input->post("movimientoConcepto");
											$monto = $this->input->post("movimientoMonto");
											$tipo = $this->input->post("tipoMovimiento");
											$joinTurno = array(
												array(
													"tabla"=>"corteTurno",
													"condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
												),
											);
											$corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>0,"idUsuarioCorteTurno"=>$this->session->idUsuario,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);
											$idCorte = ($corte) ? $corte->idCorteCaja : 0;
											$idTurnoCorte = ($corte) ? $corte->idTurnoVigente: 0;

											$datosMovimiento = array(
												"idSucursalCajaMovimiento" => $this->session->idSucursal,
												"idUsuarioCajaMovimiento" => $this->session->idUsuario,
												"idCaja" => $caja,
												"idCorte" => $idCorte,
												"idTurnoCajaMovimiento" => $idTurnoCorte,
												"tipoCajaMovimiento" => $tipo,
												"recibeCajaMovimiento" => $recibe,
												"entregaCajaMovimiento" => $entrega,
												"conceptoCajaMovimiento" => $concepto,
												"montoCajaMovimiento" => $monto,
												"estadoCajaMovimiento" => "Activo",
											);
											IniciarTransaccion();
											$guardar = GuardarDatos("cajaMovimiento", $datosMovimiento);
											if ($guardar) {
												EjecutarTransaccion();
												$datosRespuesta["idFactura"] = $guardar;
												$datosRespuesta["codigo"] = 200;
											} else {
												DeshacerTransaccion();
												$datosRespuesta["codigo"] = 500;
											}

											echo json_encode($datosRespuesta);
										}
									}
									/** crea un array con los datos del descargo de insumos y su respectivo detalle por producto */
									public function DescargarInsumosPedido($idPedido){
										//$idPedido = $this->input->post("idPedido");
										$condicionExiste = array('idPedido'=>$idPedido);
										$existe = ExistenDatos("pedido", $condicionExiste);
										// $error = false;
										$factura = TraerUnDato("factura",array("idReferenciaFactura" => $idPedido));
										$arrayProd = 0;
										$arrayInsumos = 0;
										$total = 0;
										if($existe){
											$arrayProd = array();
											$arrayInsumos = array();
											$productos = TraerDatos("pedidoDetalle",array("idPedido"=>$idPedido,"estadoPedidoDetalle"=> "Activo"));
											$tipoDescargo = '';
											if($productos){
												if(GblTraerConfiguracion("DescargaInsumoHeredado") == "Si"){
													$tipoDescargo = 'Descargo por Herencia';
													foreach($productos as $p){
														$idPedidoDetalle = $p->idPedidoDetalle;
														$detalle =  array(
															"idProducto" => $p->idProductoPedidoDetalle,
															"cantidadProducto" => $p->cantidadPedidoDetalle,
															"idPedidoDetalle" => $idPedidoDetalle
														);
														array_push($arrayProd,$detalle);
														$modificadores = TraerDatos("pedidoSubDetalle",array("idPedidoDetalle"=>$idPedidoDetalle,"variosPedidoSubDetalle"=> "1"));
														if($modificadores){
															foreach($modificadores as $mod){
																$idMod = $mod->idModPedidoSubDetalle;
																$join = array(
																	array(
																		"tabla" => "producto",
																		"condicion" => "modificador.idProducto = producto.idProducto",
																		"tipo"=>"inner",
																		"campos" => "producto.idProducto as idProd"
																	),
																);
																$traerProducto = TraerUnDato("modificador",array("idModificador" => $idMod));
																if($traerProducto){
																	$idProd = $traerProducto->idProducto;
																	$detalle2 =  array(
																		"idProducto"=>$idProd,
																		"cantidadProducto" =>$p->cantidadPedidoDetalle,
																		"idPedidoDetalle" => $idPedidoDetalle
																	);
																	array_push($arrayProd,$detalle2);
																}
															}
														}
													}
													if($arrayProd){
														foreach($arrayProd as $pr){
															$join = array(
																array(
																	"tabla" => "insumoPresentacion",
																	"condicion" => "insumoPresentacion.idPresentacion = productoInsumo.idPresentacionProductoInsumo",
																	"tipo" => "inner",
																	"campos" => "unidadInsumoPresentacion as unidad, precioInsumoPresentacion as precio"
																),
																array(
																	"tabla" => "insumo",
																	"condicion" => "insumo.idInsumo = productoInsumo.idInsumo",
																	"tipo" => "inner",
																	"campos" => "costoPromedioInsumo as costo"
																),
															);
															$insumos = TraerDatosJoin("productoInsumo",array("idProducto"=>$pr['idProducto'],"estadoProductoInsumo"=>"Activo","idModificador"=> "0"),"",$join,"productoInsumo.idInsumo");
															if($insumos){
																foreach($insumos as $in){
																	$ncantidad = ($in->cantidadProductoInsumo * $pr['cantidadProducto']);
																	$total += ($in->costoPromedioInsumo * $ncantidad);
																	$insumo = array(
																		'idPedidoDetalle' => $pr['idPedidoDetalle'],
																		'idProducto' => $pr['idProducto'],
																		'idInsumo' => $in->idInsumo,
																		'idPresentacion' => $in->idPresentacionProductoInsumo,
																		'cantidadInsumo' => $ncantidad,
																		'unidadPresentacion' => $in->unidad,
																		'costo' => $in->costo,
																		'precio' => $in->precio,
																	);
																	array_push($arrayInsumos,$insumo);
																}
															}
														}
													}
												}
												else{
													$tipoDescargo = 'Descargo no por Herencia';
													foreach($productos as $p){
														$idPedidoDetalle = $p->idPedidoDetalle;
														$cantidadPedido = $p->cantidadPedidoDetalle;
														$idProducto = $p->idProductoPedidoDetalle;
														// $detalle =  array(
														// 	"idProducto" => $p->idProductoPedidoDetalle,
														// 	"cantidadProducto" => $p->cantidadPedidoDetalle,
														// 	"idPedidoDetalle" => $idPedidoDetalle
														// );
														// array_push($arrayProd,$detalle);
														$joinInsumo = array(
															array(
																"tabla" => "insumoPresentacion",
																"condicion" => "insumoPresentacion.idPresentacion = productoInsumo.idPresentacionProductoInsumo",
																"tipo" => "inner",
																"campos" => "unidadInsumoPresentacion as unidad, precioInsumoPresentacion as precio"
															),
															array(
																"tabla" => "insumo",
																"condicion" => "insumo.idInsumo = productoInsumo.idInsumo",
																"tipo" => "inner",
																"campos" => "costoPromedioInsumo  as costo"
															),
														);
														$insumosGenerales = TraerDatosJoin("productoInsumo",array("idProducto"=>$idProducto,"estadoProductoInsumo"=>"Activo","idModificador"=> "0"),"",$joinInsumo,"productoInsumo.idInsumo");
														if($insumosGenerales){
															foreach($insumosGenerales as $in){
																$ncantidad = ($in->cantidadProductoInsumo * $cantidadPedido);
																$total += ($in->costo * $in->unidad * $ncantidad);
																$insumo = array(
																	'idPedidoDetalle' => $idPedidoDetalle,
																	'idProducto' => $idProducto,
																	'idInsumo' => $in->idInsumo,
																	'idPresentacion' => $in->idPresentacionProductoInsumo,
																	'cantidadInsumo' => $ncantidad,
																	'unidadPresentacion' => $in->unidad,
																	'costo' => $in->costo * $in->unidad,
																	'precio' => $in->precio,
																);
																array_push($arrayInsumos,$insumo);
															}
														}
														$modificadores = TraerDatos("pedidoSubDetalle",array("idPedidoDetalle"=>$idPedidoDetalle,"variosPedidoSubDetalle"=> "1"));
														if($modificadores){
															foreach($modificadores as $mod){
																$idMod = $mod->idModPedidoSubDetalle;
																$insumosModificador = TraerDatosJoin("productoInsumo",array("idProducto"=>$idProducto,"estadoProductoInsumo"=>"Activo","idModificador"=> $idMod),"",$joinInsumo,"productoInsumo.idInsumo");
																if($insumosModificador){
																	foreach($insumosModificador as $in){
																		$ncantidad = ($in->cantidadProductoInsumo * $cantidadPedido);
																		$total += ($in->costo * $in->unidad * $ncantidad);
																		$insumo = array(
																			'idPedidoDetalle' => $idPedidoDetalle,
																			'idProducto' => $idProducto,
																			'idInsumo' => $in->idInsumo,
																			'idPresentacion' => $in->idPresentacionProductoInsumo,
																			'cantidadInsumo' => $ncantidad,
																			'unidadPresentacion' => $in->unidad,
																			'costo' => $in->costo * $in->unidad,
																			'precio' => $in->precio,
																		);
																		array_push($arrayInsumos,$insumo);
																	}
																}
															}
														}
													}
												}
												if($arrayInsumos){
													$factura = TraerUnDato("factura",array("idReferenciaFactura"=>$idPedido));
													if($factura){
														$datosDescargo = array(
															"idSucursalInsumoMovimiento" => $this->session->idSucursal,
															"categoriaInsumoMovimiento" => "Descarga",
															"tipoMovimientoInsumo" => "Venta",
															"idPedidoInsumoMovimiento" => $idPedido,
															"idFacturaInsumoMovimiento" => $factura->idFactura,
															"descripcionInsumoMovimiento" => "Descargo de Insumos por Venta (".$factura->tipoDocumentoFactura."".$factura->numeroDocumentoFactura.")",
															"totalInsumoMovimiento" => $total,
															"tipoDocumentoInsumoMovimiento" => ($factura) ? $factura->tipoDocumentoFactura : '',
															"numeroDocumentoInsumoMovimiento" => ($factura) ? $factura->numeroDocumentoFactura : '',
															"idUsuarioInsumoMovimiento" => $this->session->idUsuario,
															"estadoInsumoMovimiento" => "Activo"
														);
														IniciarTransaccion();
														$descargo = GuardarDatos("insumoMovimiento",$datosDescargo);
														if($descargo){
															$error = false;
															$idMovimiento = $descargo;
															foreach($arrayInsumos as $ai){
																$datosDetalle = array(
																	'idInsumoMovimiento' => $idMovimiento,
																	'idPedidoMovimientoDetalle' => $ai['idPedidoDetalle'],
																	'idProductoInsumo' => $ai['idProducto'],
																	'idInsumo' => $ai['idInsumo'],
																	'cantidadInsumoMovimientoDetalle' => $ai['cantidadInsumo'],
																	'costoInsumoMovimientoDetalle' => $ai['costo'],
																	'precioInsumoMovimientoDetalle' => $ai['precio'],
																	'idPresentacionInsumoMovimientoDetalle' => $ai['idPresentacion'],
																	'idUsuarioInsumoMovimientoDetalle' => $this->session->idUsuario,
																	'estadoInsumoMovimientoDetalle' => 'Activo',
																);
																$guardarDetalle = GuardarDatos("insumoMovimientoDetalle",$datosDetalle);
																if($guardarDetalle){
																	$loteActualizado = true;
																	$condicionExiste = array('idProductoInsumoLote'=>$ai['idInsumo'],'cantidadInsumoLote >'=>'0');
																	$existe = ExistenDatos('insumoLote', $condicionExiste);
																	if($existe){
																		$lotes = TraerDatos("insumoLote",array("idProductoInsumoLote" => $ai['idInsumo'],"cantidadInsumoLote >"=>"0"),"fechaRegistroInsumoLote ASC" );
																		//$lotes = TraerDatos("insumoLote",array("idProductoInsumoLote" => $ai['idInsumo']),"fechaRegistroInsumoLote ASC" );
																		if($lotes){
																			$cantidad = $ai['cantidadInsumo'];
																			$unidad = $ai['unidadPresentacion'];
																			$cantidad = $cantidad * $unidad;
																			foreach($lotes as $l){
																				$existencia = $l->cantidadInsumoLote;
																				$descuento = ($cantidad >= $existencia) ? $existencia : $cantidad;
																				$cantidad = $cantidad - $existencia;
																				$actualizarLote = ActualizarCorrelativo("insumoLote",array("idInsumoLote" =>$l->idInsumoLote),"cantidadInsumoLote", -$descuento );
																				if($actualizarLote){
																					if($cantidad <= 0){
																						$loteActualizado = true;
																						break 1;
																					}
																				} else {
																					$respuesta['codigo'] = 503;
																					$respuesta['query']=$this->db->last_query();
																					$error = true;
																					break 2;
																				}
																			}
																		}else{
																			$respuesta['codigo'] = 502;
																			$error = true;
																			break;
																		}
																	}
																	if($loteActualizado){
																		$actualizarStock = ActualizarCorrelativo("insumoStock",array("idInsumo" =>$ai['idInsumo']),"cantidadInsumoStock", - ($ai['cantidadInsumo'] * $ai['unidadPresentacion']) );
																		if($actualizarStock){
																			$respuesta['codigo'] = 200;
																			$error = false;
																		}else{
																			$respuesta['codigo'] = 504;
																			$error = true;
																			break;
																		}
																	}
																}else{
																	$respuesta['codigo'] = 501;
																	$error = true;
																	break;
																}
															}
														}else{
															$respuesta['codigo'] = 500;
															$error = true;
														}
														($error == true) ? DeshacerTransaccion() : EjecutarTransaccion();
													}
													else{
														$respuesta['codigo'] = 603;
														$error = true;
													}
												}
												else{
													$respuesta['codigo'] = 602;
													$error = true;
												}
											}
											else{
												$respuesta['codigo'] = 601;
												$error = true;
											}
										}
										else{
											$respuesta['codigo'] = 600;
											$error = true;
										}
										$respuesta["error"] = $error;
										// echo json_encode($respuesta);
										return $respuesta;
									}
									function ValidarPermiso(){
										if($this->input->method(TRUE) == "POST"){
											$clave = $this->input->post("clave");
											$user = ExistenDatos("usuario",array("codigoUsuario"=>$clave,"autorizadoUsuario"=> "1","idSucursalUsuario"=>$this->session->idSucursal));
											$user1 = ExistenDatos("usuario",array("codigoAutorizacionUsuario"=>$clave,"autorizadoUsuario"=> "1","idSucursalUsuario"=>$this->session->idSucursal));
											$datos['bandera'] = ($user == true) ? "1" : ($user1 == true ? "1" : "0");
											echo json_encode($datos);
										}
									}
									function ClienteAutocomplete(){
										if($this->input->method(TRUE) == "POST"){
											$busquedaParametro = $this->input->post("query");

											$sucursal = $this->session->idSucursal;
											$condicionWhere = array('idSucursalCliente' => $sucursal,'estadoCliente' => 'Activo');
											$condicionLike = array('nombreCliente' => $busquedaParametro);
											$join=array(
												array(
													"tabla" => "FE_CAT_012_Departamento",
													"condicion" => "CAST(FE_CAT_012_Departamento.codigo AS UNSIGNED) = CAST(cliente.departamentoCliente AS UNSIGNED)",
													"tipo" => "left",
													"campos" => "FE_CAT_012_Departamento.valores as departamento, md5(cliente.idCliente) as idmd"
												),
												array(
													"tabla" => "FE_CAT_013_Municipio",
													"condicion" => "CAST(FE_CAT_013_Municipio.codigo AS UNSIGNED) = CAST(cliente.municipioCliente AS UNSIGNED) AND CAST(FE_CAT_013_Municipio.departamento AS UNSIGNED) = CAST(cliente.departamentoCliente AS UNSIGNED)",
													"tipo" => "left",
													"campos" => "FE_CAT_013_Municipio.valores as municipio"
												),
											);
											$Insumos = TraerDatosComo("cliente",$condicionWhere,$condicionLike,$join);
											echo json_encode($Insumos);
										}

									}

									function BuscarBarcode()
									{
										$busquedaParametro = trim($this->input->post("barcode"));
										$producto = $this->core->TraerUnDato('producto',['barcodeProducto !=' => "", 'estadoProducto' => 'Activo','barcodeProducto' => $busquedaParametro]);

										if($producto)
										{
											$xdatos['nombre'] = $producto->nombreProducto;
											$xdatos['idP'] = $producto->idProducto;
											$xdatos['precioVentaProducto'] = $producto->precioVentaProducto;
											$xdatos['precioEspecialProducto'] = $producto->precioEspecialProducto;
											$xdatos['existe'] ='Si';

										}
										else
										{
											$xdatos['existe'] ='No';
										}
										echo json_encode($xdatos);
									}

									function BuscarCliente()
									{
										$busquedaParametro = trim($this->input->post("idPedido"));
										$pedido = $this->core->TraerUnDato('pedido',['idPedido' => $busquedaParametro]);
										if(!$pedido){
											echo json_encode(array("idCliente"=>"","idmd"=>"","nombreCliente"=>"","direccionCliente"=>"","telefonoCliente"=>"","correoCliente"=>"","departamentoCliente"=>"","municipioCliente"=>"","duiCliente"=>"","nitCliente"=>"","nrcCliente"=>"","giroCliente"=>""));
											return;
										}

										$cliente = $this->core->TraerUnDato('cliente',['idCliente' => $pedido->idCliente]);

										if($cliente)
										{
											$xdatos['idCliente']           = $cliente->idCliente;
											$xdatos['idmd']                = md5($cliente->idCliente);
											$xdatos['nombreCliente']       = $cliente->nombreCliente;
											$xdatos['direccionCliente']    = $cliente->direccionCliente;
											$xdatos['telefonoCliente']     = $cliente->telefonoCliente;
											$xdatos['correoCliente']       = $cliente->emailCliente;
											// Códigos del catálogo de Hacienda (no nombres), para que coincidan
											// con los selects de departamento/municipio del cobro.
											$xdatos['departamentoCliente'] = $cliente->departamentoCliente;
											$xdatos['municipioCliente']    = $cliente->municipioCliente;
											$xdatos['duiCliente']          = $cliente->duiCliente;
											$xdatos['nitCliente']          = ($cliente->facturarConCliente == "NIT") ? $cliente->nitCliente : $cliente->duiCliente;
											$xdatos['nrcCliente']          = $cliente->nrcCliente;
											$xdatos['giroCliente']         = $cliente->giroCliente;
										}
										else
										{
											$xdatos['idCliente']           = '';
											$xdatos['idmd']                = '';
											$xdatos['nombreCliente']       = $pedido->nombreClientePedido;
											$xdatos['direccionCliente']    = $pedido->direccionClientePedido ?? '';
											$xdatos['telefonoCliente']     = '';
											$xdatos['correoCliente']       = '';
											$xdatos['departamentoCliente'] = '';
											$xdatos['municipioCliente']    = '';
											$xdatos['duiCliente']          = '';
											$xdatos['nitCliente']          = '';
											$xdatos['nrcCliente']          = '';
											$xdatos['giroCliente']         = '';
										}
										echo json_encode($xdatos);
									}
									function BuscarActivos()
									{
										$arrayPendiente = array(
											'local' => 0,
											'llevar' => 0,
											'domicilio' => 0,
										);
										$this->db->select(" SUM(pedido.totalPedido) as total, pedido.tipoCuentaPedido FROM `pedido` WHERE estadoPedido='Pendiente' GROUP BY pedido.tipoCuentaPedido",'FALSE');
										$query= $this->db->get();

										$data = $query->result();

										foreach ($data as $key) {
											$arrayPendiente[$key->tipoCuentaPedido] = number_format($key->total,2);
										}

										echo json_encode($arrayPendiente);


									}

								}
								/* End of file Touch.php */
