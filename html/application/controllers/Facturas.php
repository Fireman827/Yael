<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Facturas extends CI_Controller
{

  private $tabla = "factura";
  private $controlador = "Facturas";
  private $fpdf;
  function __construct()
  {
    parent::__construct();
    $this->load->Model('CoreModel', "core");
    $this->load->add_package_path(APPPATH . 'third_party/fpdf');
    $this->load->library('venta');
    $this->load->add_package_path(APPPATH . 'third_party/phpqrcode');
    $this->load->library('Qr');
  }

  public function index(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      $titulo = "Facturas";
      $datosVista = array(
        "titulo" => $titulo,
        "icono" => "fa fa-receipt",
        "botones" => array(
          // array(
          //   "icono" => "fa fa-plus",
          //   'controlador' => $this->controlador,
          //   'url' => 'FacturasAgregar',
          //   'txt' => 'Agregar Factura',
          //   'posicion' => 'right', // left, right
          //   'tipo' => GblTraerConfiguracion('colorComponentes'), //primary, success, info, warning, danger
          //   'modal' => true,
          //   'id'=>'facturaAgregar'
          // ),
        ),
        "encabezados" => array(
          "ID" => 1,
          "Fecha" => 1,
          "Hora" => 1,
          "Numero de Control" => 3,
          "Cliente" => 2,
          "Documento" => 1,
          "Total" => 1,
          "Transmitido" => 1,
          "Estado" => 1,
          "Acciones" => 1
        ),
        "admin"=>$this->session->admin,
        "idSucursal"=>$this->session->idSucursal,
        "sucursales"=>TraerDatos('sucursal'),
      );
      $extras = array(
        'css' => array(),
        'js' => array(
          "scripts/facturas.js?q=".uniqid(),
        ),
      );
      GblPlantilla("plantilla/admin", $datosVista, $extras, $titulo);
    }
  }

  function FacturasMostrar(){
    // Espacio propio del plugin data tabla
    $draw = intval($this->input->post("draw"));
    $desdeFilas = intval($this->input->post("start"));
    $cantidadFilas = intval($this->input->post("length"));

    $order = $this->input->post("order");
    $busquedaAreglo = $this->input->post("search");
    $busquedaParametro = $busquedaAreglo['value'];
    $col = 0;
    $ordenDireccion = "";
    if (!empty($order)) {
      foreach ($order as $o) {
        $col = $o['column'];
        $ordenDireccion = $o['dir'];
      }
    }
    if ($ordenDireccion != "asc" && $ordenDireccion != "desc") {
      $ordenDireccion = "asc";
    }
    //Definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
    $columnasValidas = array(
      0 => 'idFactura',
      1 => 'tipoDocumentoFactura',
      2 => 'numeroDocumentoFactura',
      3 => 'cliente.nombreCliente',
      4 => 'totalFactura'
    );
    //Fin de definicion de los nombres de los campos por los que se podran hacer busquedas en la tabla
    if (!isset($columnasValidas[$col])) {
      $ordenCampos = null;
    } else {
      $ordenCampos = $columnasValidas[$col];
    }
    // Fin espacio del data tabla
    $sucursal = $this->input->post("sucursal");
    $this->session->idSucursal = $sucursal;
    $condicion = array('idSucursalFactura' => $sucursal, 'estadoFactura !=' => 'Borrado');
    $join = array(
      array(
        "tabla" => "documento",
        "condicion" => "documento.aliasDocumento = factura.tipoDocumentoFactura",
        "campos" => "documento.impresionPdf"
      ),
      array(
        "tabla" => "corteCaja",
			  "condicion" => "corteCaja.idCorteCaja = factura.idCorte"
			),
      array(
        "tabla" => "cliente",
        "condicion" => "cliente.idCliente = factura.idCliente",
        "campos" => "cliente.nombreCliente"
      ),
    );
    $Facturas = TraerDatosTablaJoin($this->tabla,$ordenCampos, $busquedaParametro, $columnasValidas, $cantidadFilas, $desdeFilas, $ordenDireccion,$condicion,$join);
    // print_r($Facturas);
    //Lectura de datos de la base para mostrar en el datatabla
    if ($Facturas != 0) {
      $datosMostrar = array();
      foreach ($Facturas as $Factura) {
        $estadoFactura = $Factura->estadoFactura;
        $impresionPdf = $Factura->impresionPdf;
        if ($estadoFactura == 'Activo') {
          $estadoSpan = "<span class='badge badge-primary font-bold'>Activo<span>";
        }
        if ($estadoFactura == 'Cobrado') {
          $estadoSpan = "<span class='badge badge-success font-bold'>Cobrado<span>";
        }
        if ($estadoFactura == 'Borrado') {
          $estadoSpan = "<span class='badge badge-danger font-bold'>Borrado<span>";
        }
        if ($estadoFactura == 'Anulado') {
          $estadoSpan = "<span class='badge badge-danger font-bold'>Anulado<span>";
        }
        if ($estadoFactura == 'Inactivo') {
          $estadoSpan = "<span class='badge badge-warning font-bold'>Inactivo<span>";
        }

        $tipoFactura = $Factura->tipoFactura;
        if($tipoFactura == 'Servicio'){
          $tipoFacturaTxt = 'Servicio';
        } else {
          $tipoFacturaTxt = 'Producto';
        }
        $menuOpciones = "
        <div class='input-group-prepend'>
        <button data-toggle='dropdown' class='btn btn-" . GblTraerConfiguracion('colorComponentes') . " btn-block btn-sm dropdown-toggle font-weight-bold' aria-expanded='false'><i class='mdi mdi-menu' aria-haspopup='false'></i> Menu</button>
        <div class='dropdown-menu dropdown-menu-right' x-placement='top-start'>";
        $estadoTransmitido = $Factura->selloRecibido;
				if($estadoTransmitido!="" || $Factura->tipoDocumentoFactura == "TIK"){
					$estadoTransmitidoSpan = "<span class='badge badge-primary font-bold'>Transmitido<span>";
				} else{

					$estadoTransmitidoSpan = "<span class='badge badge-danger font-bold'>No Transmitido<span>";
					$funcion = "VentasRetransmitir";
					$fechaInicio = strtotime($Factura->fechaFactura.' '.$Factura->horaFactura);
					$fechaFin = strtotime(date('Y-m-d H:i:s'));
					$horas = 24;
					if($Factura->tipoDocumentoFactura == "FAC"){
						$horas = 2000;
					}
					if($Factura->selloRecibidoAnulacion == "" && $estadoFactura != "Anulado" && GblTraerConfiguracion("facturacion_electronica") == "Si"){
						$horasTranscurridas = ($fechaFin-$fechaInicio)/3600;

						if ($horasTranscurridas<=$horas) {
							if(GblPermisos($this,$funcion,$this->controlador)){
								$menuOpciones .= "<a class='dropdown-item VentasRetransmitirModal' idVenta=".($Factura->idFactura)."><i class='fa fa-cloud-upload-alt'></i> Retransmitir</a>";
							}
						} else {
							if(GblPermisos($this,$funcion,$this->controlador)){
								$menuOpciones .= "<a class='dropdown-item VentasContingenciaModal' idVenta=".($Factura->idFactura)."><i class='fa fa-cloud-upload-alt'></i> Contingencia</a>";
							}
						}
					}
				}
				$funcion = "VentasAnular";
				if(GblPermisos($this,$funcion,$this->controlador)){
					if($Factura->selloRecibidoAnulacion == "" && $estadoFactura != "Anulado"){
						$menuOpciones .= "<a class='dropdown-item ".$funcion."' idFactura=".$Factura->idFactura."><i class='fa fa-times'></i> Anular</a>";
					}
				}
				$condicionCt = array(
					"estadoCorte" => "Vigente",
					"idTurnoVigente !="=>'',
					"idSucursalCorte"=>$this->session->idSucursal
				);
				////////////////////////////////////////////////
				////////////////////////////////////////////////
				$corte = TraerUnDato("corteCaja",$condicionCt);
				////////////////////////////////////////////////
				////////////////////////////////////////////////
				if($corte !== false){
					$funcion = "VentasDevolucion";
					if(GblPermisos($this,$funcion,$this->controlador)){
						if(($Factura->tipoDocumentoFactura=="TIK" || $Factura->tipoDocumentoFactura=="CCF") && $Factura->selloRecibidoAnulacion == "" && $estadoFactura != "Anulado"){
							$menuOpciones .= "<a class='dropdown-item ".$funcion."' idFactura=".$Factura->idFactura." href='". base_url().$funcion."/".md5($Factura->idFactura)."'><i class='fas fa-history'></i> Devolucion</a>";
						}
					}
				}

				$funcion = "VentasReimprimir";
				if(GblPermisos($this,$funcion,$this->controlador)){
					$menuOpciones .= "<a class='dropdown-item ".$funcion."' aliasDocumento='".$Factura->tipoDocumentoFactura."' idFactura=".md5($Factura->idFactura)."><i class='fa fa-print'></i> Reimprimir</a>";
				}
				if($Factura->selloRecibidoAnulacion == "" && $estadoFactura != "Anulado" && $Factura->tipoDocumentoFactura != "TIK"){
					$funcion = "VentasReenviar";
					if(GblPermisos($this,$funcion,$this->controlador)){
						$menuOpciones .= "<a class='dropdown-item VentasReenviarModal' idVenta=".($Factura->idFactura)."><i class='fa fa-reply'></i> Reenviar</a>";
					}
					// if(GblPermisos($this,$funcion,$this->controlador)){
					// 	$menuOpciones .= "<a class='dropdown-item ".$funcion."' idFactura=".$Factura->idFactura."><i class='fa fa-reply'></i> Reenviar</a>";
					// }
				}
				$funcion = "VentasPdf";
				if($Factura->tipoDocumentoFactura != "TIK"){
					if(GblPermisos($this,$funcion,$this->controlador)){
						$menuOpciones .= "<a target='_blank' class='dropdown-item' href='". base_url().$funcion."/".md5($Factura->idFactura)."'><i class='fa fa-file-pdf' ></i> PDF</a>";
					}
				}
        $funcion = "FacturasVer";
        if (GblPermisos($this, $funcion, $this->controlador)) {
            $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Ver' idFactura=" . md5($Factura->idFactura) . "><i class='far fa-eye' ></i> Ver Detalle</a>";
        }
        // $funcion = "FacturasReimprimir";
        // if (GblPermisos($this, $funcion, $this->controlador)) {
        //   $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='$tipoFacturaTxt' idFactura=" .$Factura->idFactura. " idFactura1=" .md5($Factura->idFactura). " pdf='".$impresionPdf."'><i class='fas fa-print' ></i> Reimprimir</a>";
        // }
        // if ($estadoFactura != 'Anulado') {
        //   $funcion = "FacturasCambiarEstado";
        //   if (GblPermisos($this, $funcion, $this->controlador) && $Factura->estadoCorte == "Vigente") {
        //     $menuOpciones .= "<a class='dropdown-item " . $funcion . "' data-accion='Anular' idFactura=" . md5($Factura->idFactura) . "><i class='far fa-window-close'></i> Anular</a>";
        //   }
        // }
        // $funcion = "FacturasCambiarEstado";
        // if (GblPermisos($this, $funcion, $this->controlador)) {
        //   $menuOpciones .= "<a class='dropdown-item " . $funcion . "'  data-accion='Eliminar' idFactura=" . md5($Factura->idFactura) . "><i class='fa fa-trash'></i> Eliminar</a>";
        // }
        $menuOpciones .= "</div></div>";
        $datosMostrar[] = array(
          $Factura->idFactura,
          fecha_d_m_a($Factura->fechaFactura),
          hora($Factura->horaFactura),
          $Factura->numeroControl,
          $Factura->nombreCliente,
          $Factura->numeroDocumentoFactura,
          "$".$Factura->totalFactura,
          $estadoTransmitidoSpan,
          $estadoSpan,
          $menuOpciones
        );
      }
      $totalFacturas = TraerTotalDatos($this->tabla);
      $output = array(
        "draw" => $draw,
        "recordsTotal" => $totalFacturas,
        "recordsFiltered" => $totalFacturas,
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

  function FacturasVer($idFactura){
    if($this->input->method(TRUE) == "GET") {
			$titulo = "Ver Venta";
			$condicionDatos = array('md5(idFactura)' => $idFactura);
			$datosVenta = TraerUnDato("factura", $condicionDatos);
			$idReferencia = $datosVenta->idAfectaFactura;
			$estadoFactura = $datosVenta->estadoFactura;
			$idPedido = $datosVenta->idReferenciaFactura;
			$tipoDocumento = $datosVenta->tipoDocumentoFactura;
			$idSucursalFactura = $datosVenta->idSucursalFactura;
			/*******************************************************************/
			/*******************************************************************/
			/****************************************************************/
		 	$DocumentoRef = '';
			$codigoGeneracionRef = '';
			$fechaFacturaRef = '';
			if($idReferencia!=""){
				$condicionDatosRef = array('idFactura' => $idReferencia);
				$datosVentaRef = TraerUnDato("factura", $condicionDatosRef);
				/****************************************************************/
        if($tipoDocumentoRef == "FAC"){
          $codigoDocumentoRef = "01";
        } else if($tipoDocumentoRef == "CCF") {
          $codigoDocumentoRef = "03";
        }
        $condicionDatosDocumentoRef = array('codigo' => $codigoDocumentoRef);
        $datosDocumentoRef = TraerUnDato("FE_CAT_002_TipodeDocumento", $condicionDatosDocumentoRef);
        $versionDocumentoRef = $datosDocumentoRef->version;
        $DocumentoRef = $datosDocumentoRef->valores;
			}

			$condicionDatos = array('idCliente' => $datosVenta->idCliente);
			$datosCliente = TraerUnDato("cliente", $condicionDatos);

			$departamento = TraerUnDato('FE_CAT_012_Departamento',array("codigo"=>$datosCliente->departamentoCliente));
      $departamentoClienteNombre = $departamento->valores;
      $municipio = TraerUnDato('FE_CAT_013_Municipio',array("codigo"=>$datosCliente->municipioCliente,"departamento" => $datosCliente->departamentoCliente));
      $municipioClienteNombre = $municipio->valores;

      $codigoDocumento = "00";
      if($tipoDocumento == "FAC"){
        $codigoDocumento = "01";
      } else if($tipoDocumento == "CCF") {
        $codigoDocumento = "03";
      }
      $condicionDatosDocumento = array('codigo' => $codigoDocumento);
      $datosDocumento = TraerUnDato("FE_CAT_002_TipodeDocumento", $condicionDatosDocumento);

			$condicionDatosModelo = array('codigo' => 1);
			$datosModelo = TraerUnDato("FE_CAT_003_ModelodeFacturacion", $condicionDatosModelo);

			$condicionDatosTransmision = array('codigo' => 1);
			$datosTransmision = TraerUnDato("FE_CAT_004_TipodeTransmision", $condicionDatosTransmision);

			$giro = '';
			$condicionDatosGiro = array('codigo' => $datosCliente->giroCliente);
			$datosGiros = TraerUnDato("FE_CAT_019_CodigodeActividadEco", $condicionDatosGiro);
			if ($datosGiros != false) {
				$giro = " - ".$datosGiros->valores;
			}

			$joinDet = array(
			array(
			"tabla" => "producto",
			"tipo" => "left",
			"condicion" => "pedidoDetalle.idProductoPedidoDetalle = producto.idProducto"
			),
			);

			$datosDetalle = TraerDatosJoin("pedidoDetalle",array("pedidoDetalle.idPedido" => $idPedido,"estadoPedidoDetalle !="=>"Borrado"),"",$joinDet );
			$datosVista = array(
				"titulo" => $titulo,
				"icono" => "fas fa-id-card-alt",
				"controlador" => $this->controlador,
				"titulo" => $titulo,
				"proceso" => "Ver",
				"datosVenta" => $datosVenta,
				"datosCliente" => $datosCliente,
				"datosDocumento" => $datosDocumento,
				"datosModelo" => $datosModelo,
				"datosTransmision" => $datosTransmision,
				"giro" => $giro,
				"idReferencia" => $idReferencia,
				"DocumentoRef" => $DocumentoRef,
				"codigoGeneracionRef" => $codigoGeneracionRef,
				"fechaFacturaRef" => $fechaFacturaRef,
				"departamentoClienteNombre" => $departamentoClienteNombre,
				"municipioClienteNombre" => $municipioClienteNombre,
				"datosDetalle" => $datosDetalle,

			);
			$extras = array(
				'css' => array(
				),
				'js' => array(
				),
			);
			$this->load->view("facturas/VentasVer",$datosVista);
		}
    // if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
    //     GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    // } else {
    //     if($this->input->method(TRUE) == "GET") {
    //
    //       $join = array(
    //         array(
    //           "tabla" => "usuario",
    //           "condicion" =>"factura.idUsuario = usuario.idUsuario",
    //           "tipo" => "inner",
    //           "campos" => "usuario.nombreUsuario"
    //         ),
    //         array(
    //           "tabla" => "cliente",
    //           "condicion" =>"factura.idCliente = cliente.idCliente",
    //           "tipo" => "left",
    //           "campos" => "cliente.nombreCliente"
    //         ),
    //       );
    //       $condicionFacDet = array("tipoFactura" => "Producto","estadoFactura" => "Cobrado","md5(idFactura)" =>$idFactura);
    //       $factura = TraerUnDatoJoin("factura",$condicionFacDet,$join);
    //
    //       $idPedido = $factura->idReferenciaFactura;
    //       $join = array(
    //         array(
    //           "tabla" => "producto",
    //           "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
    //           "tipo" => "inner",
    //           "campos" => "producto.nombreProducto"
    //         ),
    //       );
    //       $condicion = array("idPedido" => $idPedido,"estadoPedidoDetalle != "=>"Borrado");
    //       $facturaDetalle = TraerDatosJoin("pedidoDetalle",$condicion,"",$join);
    //
    //         $titulo = "Ver Factura";
    //         $datosVista = array(
    //             "titulo" => $titulo,
    //             "icono" => "fa fa-receipt",
    //             "controlador" => $this->controlador,
    //             "proceso" => "Ver",
    //             "factura" => $factura,
    //             "facturaDetalle" => $facturaDetalle,
    //         );
    //         $extras = array(
    //             'css' => array(),
    //             'js' => array(
    //             ),
    //         );
    //         $this->load->view("facturas/VentasVer",$datosVista);
    //     }
    // }
}
function VentasDevolucion($idFactura=""){
  // if(!GblPermisos($this,__FUNCTION__,$this->controlador)){
  // 	GblPlantilla("plantilla/permiso",array(),array(),"No autorizado");
  // } else{
    if($this->input->method(TRUE) == "GET"){
      $condicionDatos = array('md5(idFactura)' => $idFactura);
      $datosFactura = TraerUnDato("factura", $condicionDatos);
      $condicionCliente = array('idCliente' => $datosFactura->idCliente);
      $datosCliente = TraerUnDato("cliente", $condicionCliente);
      $cliente = $this->core->TraerDatos("cliente");
      $idSucursal=$this->session->idSucursal;
      $idUsuario=$this->session->idUsuario;
      $admin=$this->session->admin;
      $idPedido = $datosFactura->idReferenciaFactura;
      $condicion = $this->core->TraerDatos("FE_CAT_016_CondiciondelaOperacion");
      if($datosFactura !== false && $idFactura!=""){
        $condicionCaja =  array("estadoCorte"=>"Vigente","idUsuarioCorte"=>$idUsuario,"idSucursalCaja"=>$idSucursal,"estadoCaja" => "Activo","aperturaCaja"=> "1","turnoCaja"=> "1");
        if ($admin) {
          $condicionCaja =  array("estadoCorte"=>"Vigente","idSucursalCaja"=>$idSucursal,"estadoCaja" => "Activo","aperturaCaja"=> "1","turnoCaja"=> "1");
        }
        $joinCaja =  array(
          array(
            "tabla" => "corteCaja",
            "condicion" => "corteCaja.idCaja = caja.idCaja",
            "tipo" => "left",
            "campos" => "idUsuarioCorte, fechaCorte"
          ),
          // array(
          //   "tabla" => "corteTurno",
          //   "condicion" => "corteCaja.idCorteCaja = corteTurno.idCorte",
          //   "tipo" => "inner",
          //   "campos" => "corteCaja.idUsuarioCorteTurno"
          // )
        );
        $cajas = TraerDatosJoin('caja',$condicionCaja,"",$joinCaja);
        // print_r($cajas);
        $n = 1;
        $option="";
        $idCaja=0;
        foreach ($cajas as $fila)
        {
          if($n == 1)
          {
            $option .= "<option value='".$fila->idCaja."' selected>".$fila->nombreCaja."</option>";
            $idCaja=$fila->idCaja;
          }
          else
          {
            $option .= "<option value='".$fila->idCaja."'>".$fila->nombreCaja."</option>";
          }
          $n++;
        }
          $condiciondocumento = array(
            "idCajaCajaDocumento"=>$idCaja,
            // "idSucursalCaja"=>$idSucursal,
            // "idSucursalCajaDocumento"=>$idSucursal,
            "estadoCajaDocumento"=>"Activo",
          );

        $joinDoc = array(
          array(
            "tabla" => "documento",
            "condicion" => "cajaDocumento.idDocumentoCajaDocumento = documento.idDocumento AND documento.tipoDocumento='Nota'",
            "orden" => "left",
            "campos" => "aliasDocumento, nombreDocumento ,idCajaDocumento"
          ),
        );

        // print_r($condiciondocumento);
        $documentosCaja = TraerDatosJoin('cajaDocumento',$condiciondocumento,'',$joinDoc);
        // print_r($documentosCaja);
        $nD = 1;
        $optionD="";
        $nDocumento="";
        foreach ($documentosCaja as $filaD)
        {
          if($nD == 1)
          {
            $optionD .= "<option value='".$filaD->idCajaDocumento."' selected tipo='".$filaD->aliasDocumento."'>".$filaD->nombreDocumento."</option>";
            $nDocumento = $filaD->actualCajaDocumento;
          }
          else
          {
            $optionD .= "<option value='".$filaD->idCajaDocumento."' tipo='".$filaD->aliasDocumento."'>".$filaD->nombreDocumento."</option>";
          }
          $nD++;
        }
        $joinDet = array(
          array(
            "tabla" => "producto",
            "condicion" => "pedidoDetalle.idProductoPedidoDetalle = producto.idProducto"
          ),
        );
        $datosDetalle=TraerDatosJoin("pedidoDetalle",array("pedidoDetalle.idPedido" => $idPedido),"",$joinDet);
        $trAdd = "";
        if($datosDetalle != ""){
          foreach ($datosDetalle as $detalle){
            $idProducto = $detalle->idProducto;
            $idFacturaDetalle = $detalle->idPedidoDetalle;
            $cantidadPedidoDetalle = $detalle->cantidadPedidoDetalle;
            $precioPedidoDetalle = $detalle->precioPedidoDetalle;
            $precioIvaUnitarioPedidoDetalle = $detalle->precioPedidoDetalle;
            $costoUnitarioPedidoDetalle = $detalle->costoUnitarioPedidoDetalle;
            $subTotalPedidoDetalle = $cantidadPedidoDetalle * $precioPedidoDetalle;
            $subTotalDescuetoPedidoDetalle = 0;//$detalle->subTotalDescuetoPedidoDetalle;
            // $idPresentacionProductoPedidoDetalle = ;//$detalle->idPresentacionProductoPedidoDetalle;
            // $presentacionProductoPedidoDetalle = $detalle->presentacionProductoPedidoDetalle;
            // $unidadPresentacionProductoPedidoDetalle = $detalle->unidadPresentacionProductoPedidoDetalle;
            $descuentoPedidoDetalle = 0;//$detalle->descuentoPedidoDetalle;
            $nombreProducto = $detalle->nombreProducto;

            $condicionDetalleDevolucion = array(
              'idDetalleDevolucion' => $idFacturaDetalle,
              'estadoFacturaDetalle' => 'Activo',
            );
            $datosDevolucion = TraerUnDatoIndividual("pedidoDetalle","SUM(cantidadPedidoDetalle) as cant",$condicionDetalleDevolucion);
            if ($datosDevolucion[0]['cant']!='') {
              $cantidadDevolucion=$datosDevolucion[0]['cant'];
            }else {
              $cantidadDevolucion=0;
            }

            $condicion = array('productoPresentacion.idProducto' => $idProducto,'productoPresentacion.estadoProductoPresentacion' => 'Activo');
            $join = array(
              array('tabla' => 'FE_CAT_014_UnidaddeMedida', 'condicion' => 'productoPresentacion.idUnidadMedida = FE_CAT_014_UnidaddeMedida.codigo',"tipo"=>"left","campos"=>"valores"),
              array('tabla' => 'producto', 'condicion' => 'producto.idProducto = productoPresentacion.idProducto',"tipo" => "left","campos" => "costoPromedioProducto")
            );
            $datosProductoPresentacion = TraerUnDatoJoin("productoPresentacion",$condicion, $join);
            // print_r($datosProductoPresentacion);
            $trAdd .= '<tr class="'.$idPedidoDetalle.'"><input type="hidden" class="idPedidoDetalle" id="idPedidoDetalle" value="'.$idPedidoDetalle.'">';
            $trAdd .= '<td class="id_p" hidden><input type="hidden" class="nFila" id="nFila" value="'.$ordenPedidoDetalle.'"><input type="hidden" class="idProducto" id="idProducto" value="'.$idProducto.'"></td>';
            $trAdd .= '<td class="descripcionTd"><input type="hidden" class="unidadProductoPresentacion" id="unidadProductoPresentacion" value="'.$datosProductoPresentacion->unidadProductoPresentacion.'"><input type="hidden" class="idProductoPresentacion" id="idProductoPresentacion" value="'.$datosProductoPresentacion->idProductoPresentacion.'">' . $nombreProducto . '</td>';
            $trAdd .= '<td class="presentacionProducto">'.$datosProductoPresentacion->valores.'('.number_format($datosProductoPresentacion->unidadProductoPresentacion,2).')</td>';
            $trAdd .= "<td><div class='col-xs-1'><input type='text'  class='form-control form-control-sm costoUnitarioPedidoDetalle decimal' style='width:80px;' value='".number_format($costoUnitarioPedidoDetalle,4,'.','')."' readOnly></div></td>";
            $trAdd .= "<td><input type='hidden'  class='form-control form-control-sm precioUnitarioPedidoDetalle decimal' style='width:80px;' value='".number_format($precioUnitarioPedidoDetalle,4,'.','')."' ><input type='text'  class='form-control form-control-sm precioIvaUnitarioPedidoDetalle decimal' style='width:80px;' value='".number_format($precioIvaUnitarioPedidoDetalle,4,'.','')."' ></td>";
            $trAdd .= "<td class='tdPrecio'><input type='text'  class='form-control form-control-sm descuentoPedidoDetalle decimal' style='width:80px;' value='".number_format($descuentoPedidoDetalle,4,'.','')."' readOnly></div></td>";
            $trAdd .= "<td class='tdPrecio'><input type='text'  class='form-control form-control-sm cantidadPedidoDetalle decimal' value='".number_format($cantidadPedidoDetalle,4,'.','')."' style='width:80px;'  readOnly></div></td>";
            $trAdd .= "<td class='tdPrecioIva'><input type='text'  class='form-control form-control-sm cantidadaDevolver decimal' value='' style='width:80px;' ></div></td>";
            $trAdd .= "<td><div class='col-xs-1'><input type='text'  class='form-control form-control-sm cantidadDevolucion decimal' value='".number_format($cantidadDevolucion,4,'.','')."' style='width:80px;'  readOnly></div></td>";
            $trAdd .= "<td style='text-align:center;'><input type='text'  class='form-control form-control-sm subtotalDevolucion decimal' value='' style='width:80px;'  disabled></div></td>";
            $trAdd .= '</tr>';

          }
        }
        $titulo = "Devolucion";
        $datosVista = array(
          "datosFactura"=> $datosFactura,
          "datosCliente"=> $datosCliente,
          "detalles"=> $trAdd,
          "controlador" => $this->controlador,
          "idFactura" => $idFactura,
          "titulo" => $titulo,
          "proceso" => "Devolucion",
          "cliente" => $cliente,
          "cajas" => $option,
          "documentos" => $optionD,
          "nDocumento" => $nDocumento,
          "condicion" => $condicion,
        );
        $extras = array(
          'css' => array(
          ),
          'js' => array(
            "scripts/bigdecimal.js",
            "scripts/devolucion.js?v=".uniqid()
          ),
        );
        GblPlantilla("facturas/ventaDevolucion",$datosVista,$extras,$titulo);
      } else{
        GblPlantilla("plantilla/error",array(),array(),"Error");
      }
    } else if($this->input->method(TRUE) == "POST"){
      $datos = $this->input->post();
      // print_r($datos);
      $datosArray = array();
      $details = $this->input->post('details');
      $idCliente = $this->input->post('idCliente');
      $idCaja = $this->input->post('idCaja');
      $idFactura = $this->input->post('idFactura');
      $numeroDocumentoDev = $this->input->post('numeroDocumentoDev');
      $idCajaDocumento = $this->input->post('idDocumento');
      $tipoImpresion = $this->input->post('aliasDocumento');
      $totalGravado = $this->input->post('totalSumasDevolucion');
      $totalFinal = $this->input->post('totalDevolucion');
      $jsonArr = $this->input->post("details");
      $modoInventarioVenta = GblTraerConfiguracion('modoInventarioVenta');
      $error = false;
      $totalIva = $this->input->post('totalIvaDevolucion');
      $totalRetencion = $this->input->post('totalRetencionDevolucion');
      $totalDescuento = $this->input->post('totalDescuentoDevolucion');
      $descuentoPor = GblTraerConfiguracion('descuentoPor');
      $details = $this->input->post('details');
      foreach($datos as $i => $dato){
        (strpos($i, "hidden") === false) ? $datosArray[$i] = $dato : "";
        // echo $i;
      }
      IniciarTransaccion();

      $correlativoUltimo = 0;
      $datosGuardar = array(
        "idSucursalFactura"=>$this->session->idSucursal,
        "idCliente"=>$idCliente,
        "idAfectaFactura"=>$idFactura,
        "fechaFactura"=>date("Y-m-d"),
        "horaFactura"=>date("H:i:s"),
        "tipoDocumentoFactura"=>$tipoImpresion,
        "sumasFactura"=>$totalGravado,
        "ivaFactura"=>$totalIva,
        "retencionFactura"=>$totalRetencion,
        "descuentoFactura"=>$totalDescuento,
        "tipoDescuentoFactura"=>$descuentoPor,
        "totalFactura"=>$totalFinal,
        "idUsuario"=>$this->session->idUsuario,
        "tipoFactura" => $tipoImpresion
      );
      $joinTurno = array(
        array(
          "tabla"=>"corteTurno",
          "condicion"=>"corteTurno.idTurno = corteCaja.idTurnoVigente"
        ),
      );
      $corte = TraerUnDatoJoin("corteCaja",array("estadoCorte" => "Vigente","idCaja >"=>$idCaja,"idSucursalCorte"=>$this->session->idSucursal),$joinTurno);
      $idCorte = ($corte) ? $corte->idCorteCaja : 0;
      $idCaja = ($corte) ? $corte->idCaja : 0;
      $idUsuarioCorteTurno = ($corte) ? $corte->idUsuarioCorteTurno: 0;
      $idTurnoVigente = ($corte) ? $corte->idTurnoVigente: 0;
      $codigoGeneracion = generarUuid();
      $condicionDoc = array("idCajaDocumento" => $idCajaDocumento);
      $documentoId = TraerUnDato("cajaDocumento",$condicionDoc);
      $condicionDoc = array("idDocumento" => $documentoId->idDocumentoCajaDocumento);
      $documentoFact = TraerUnDato("documento",$condicionDoc);
      $numeroControl = generarNumeroControl($documentoFact->codigo,$numeroDocumentoDev);
      $datosGuardar["metodoPagoFactura"] = "Contado";
      $datosGuardar["numeroControl"] = $numeroControl;
      $datosGuardar["codigoGeneracion"] = $codigoGeneracion;
      $datosGuardar["numeroDocumentoFactura"] = $numeroDocumentoDev;
      $datosGuardar["idVendedor"] = $this->session->idUsuario;
      $datosGuardar["idCajaDocumento"] = $idCajaDocumento;
      $datosGuardar["idDocumento"] = $documentoId->idDocumentoCajaDocumento;
      $datosGuardar["idCajaFactura"] = $idCaja;
      $datosGuardar["idCorte"] = $idCorte;
      $datosGuardar["idTurno"] = $idTurnoVigente;
      $guardar = GuardarDatos("factura",$datosGuardar);
      $idGuardado = $guardar;
      if($guardar){
        $actualizarCorrelativo = ActualizarCorrelativo("cajaDocumento",array("idCajaDocumento" =>$idCajaDocumento),"actualCajaDocumento", ($numeroDocumentoDev+1));
      }
      $inserteditems=0;
      $array = json_decode($jsonArr, true);
      foreach ($array as $fila){
        $idProducto = $fila["idProducto"];
        $idFacturaDetalle = $fila["idFacturaDetalle"];
        $precio = $fila["precioUnitarioFacturaDetalle"];
        $subTotalDescuetoFacturaDetalle = $fila["subtotal"];
        $precioIva = $fila["precioIvaUnitarioFacturaDetalle"];
        $insumoCantidad = $fila["cantidadaDevolver"];
        $insumoPresentacion = $fila["ProductoPresentacion"];
        $idInsumoPresentacion = $fila["idProductoPresentacion"];
        $costoInsumoPresentacion = $fila["costoUnitarioFacturaDetalle"];
        $unidadInsumoPresentacion = $fila["unidadProductoPresentacion"];
        $precioVenta = $fila["precioIvaUnitarioFacturaDetalle"];
        $descuento = $fila["descuento"];

        $datosDetalle = array(
          'idFactura' => $idGuardado,
          'idFacturaDevolucion' => $idFactura,
          'idDetalleDevolucion' => $idFacturaDetalle,
          'idProducto' => $idProducto,
          'cantidadFacturaDetalle' => $insumoCantidad,
          'precioUnitarioFacturaDetalle' => $precio,
          'precioIvaUnitarioFacturaDetalle' => $precioIva,
          'costoUnitarioFacturaDetalle' => $costoInsumoPresentacion,
          'subTotalFacturaDetalle' => $precio * $insumoCantidad,
          'subTotalIvaFacturaDetalle' => $precioIva * $insumoCantidad,
          'subTotalDescuetoFacturaDetalle' => $subTotalDescuetoFacturaDetalle,
          'idPresentacionProductoFacturaDetalle' => $idInsumoPresentacion,
          'presentacionProductoFacturaDetalle' => $insumoPresentacion,
          'unidadPresentacionProductoFacturaDetalle' => $unidadInsumoPresentacion,
          'descuentoFacturaDetalle' => $descuento,
        );
        $guardarDetalle = GuardarDatos("facturaDetalle",$datosDetalle);
        $inserteditems++;
          if ($guardarDetalle){
            $condicionDatos = array('idProductoPresentacion' =>$idInsumoPresentacion,);
            $datosProductoPresentacion = TraerUnDato("productoPresentacion", $condicionDatos);
            $unidad = $datosProductoPresentacion->unidadProductoPresentacion;
            $cantidadreal =$unidad * $insumoCantidad;

            if ($tipoImpresion=='NDC') {

              $response_lotes = $this->lotes->CargaLote(
                "Devolucion",
                $guardarDetalle,
                $idProducto,
                $idInsumoPresentacion,
                $this->session->idSucursal,
                $insumoCantidad,
                $cantidadreal,
                $unidad,
                $datosProductoPresentacion->costoProductoPresentacion,
                "",
                "",
                "");

            }

          }
        }
        if($inserteditems==0)
        {
          $error = true;
        }

      if($guardar&&$error==false){
        EjecutarTransaccion();
        $datosRespuesta["codigo"]=200;
        $datosRespuesta["idGuardado"]=$idGuardado;
        $datosRespuesta["idGuardadom"]=md5($idGuardado);
        $datosRespuesta["tipoImpresion"]=$tipoImpresion;
        $datosRespuesta["correlativoUltimo"]=$numeroDocumentoDev;

      } else{
        // DeshacerTransaccion();
        $datosRespuesta["codigo"]=500;
      }
      echo json_encode($datosRespuesta);
    }
  // }
}
  function FacturasAgregar(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      if($this->input->method(TRUE) == "GET") {
        $titulo = "Agregar Factura";
        $datosVista = array(
          "titulo" => $titulo,
          "icono" => "fas fa-print",
          "controlador" => $this->controlador,
          "proceso" => "Agregar",
        );
        $extras = array(
          'css' => array(),
          'js' => array(
            "scripts/facturas.js?q=".uniqid(),
          ),
        );
        $this->load->view("facturas/FacturaAgregar",$datosVista);
        // GblPlantilla("Facturas/FacturasAgregar", $datosVista, $extras, $titulo);
      } else if ($this->input->method(TRUE) == "POST") {
        $nombreFactura = $this->input->post("nombreFactura");
        $recursoCompartidoFactura = $this->input->post("recursoCompartidoFactura");
        $condicionExiste = array(
          "nombreFactura" => $nombreFactura,
          "idSucursalFactura" => $this->session->idSucursal
        );
        $existe = ExistenDatos($this->tabla, $condicionExiste);
        if ($existe == 0) {
          $datosFacturas = array(
            "idSucursalFactura" => $this->session->idSucursal,
            "nombreFactura" => $nombreFactura,
            "recursoCompartidoFactura" => $recursoCompartidoFactura,
            "estadoFactura" => 'Activo'
          );
          IniciarTransaccion();
          $guardar = GuardarDatos($this->tabla, $datosFacturas);
          ($guardar == false) ? $error = true : $error = false;
          if ($error) {
            DeshacerTransaccion();
            $datosRespuesta["codigo"] = 402;
          } else {
            EjecutarTransaccion();
            $datosRespuesta["codigo"] = 200;
          }
        } else {
          $datosRespuesta["codigo"] = 400;
        }
        echo json_encode($datosRespuesta);
      }
    }
  }

  function FacturasEditar($idFactura = ""){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      GblPlantilla("plantilla/permiso", array(), array(), "No autorizado");
    } else {
      if ($this->input->method(TRUE) == "GET") {
        $datosFacturas = TraerUnDato($this->tabla, array('md5(idFactura)' => $idFactura));
        $titulo = "Editar Factura";
        $datosVista = array(
          "titulo" => $titulo,
          "icono" => "fas fa-print",
          "controlador" => $this->controlador,
          "idFactura" => $idFactura,
          "proceso" => "Editar",
          "datosFacturas" => $datosFacturas
        );
        $extras = array(
          'css' => array(),
          'js' => array(
            "scripts/facturas.js?q=".uniqid(),
          ),
        );
        $this->load->view("facturas/FacturaEditar",$datosVista);
        //GblPlantilla("Facturas/FacturasEditar", $datosVista, $extras, $titulo);
      } else if ($this->input->method(TRUE) == "POST") {
        $idFactura = $this->input->post("idFactura");
        $nombreFactura = $this->input->post("nombreFactura");
        $recursoCompartidoFactura = $this->input->post("recursoCompartidoFactura");
        $TraerFactura = TraerUnDatoIndividual("factura","idFactura",array("md5(idFactura)" => $idFactura));
        $factura = $TraerFactura[0]["idFactura"];
        $condicionExiste = array(
          "nombreFactura" => $nombreFactura,
          "idSucursalFactura" => $this->session->idSucursal,
          "md5(idFactura) !="=> $idFactura
        );
        $existe = ExistenDatos($this->tabla, $condicionExiste);
        if ($existe == 0) {
          $datosFacturas = array(
            "idSucursalFactura" => $this->session->idSucursal,
            "nombreFactura" => $nombreFactura,
            "recursoCompartidoFactura" => $recursoCompartidoFactura,
            'aleatorioFactura' => uniqid()
          );
          IniciarTransaccion();
          $condicion = array("md5(idFactura)" => $idFactura);
          $guardar = EditarDatos($this->tabla, $datosFacturas,$condicion);
          ($guardar == false) ? $error = true : $error = false;
          if ($error) {
            DeshacerTransaccion();
            $datosRespuesta["codigo"] = 500;
          } else {
            EjecutarTransaccion();
            $datosRespuesta["codigo"] = 200;
          }
        } else {
          $datosRespuesta["codigo"] = 400;
        }
        echo json_encode($datosRespuesta);
      }
    }
  }
  function VentasPdf($id) {
    $condicionDatos = array('md5(idFactura)' => $id);
    $datosVenta = TraerUnDato("factura", $condicionDatos);
    $idReferencia = $datosVenta->idAfectaFactura;
    $idRef = $datosVenta->idReferenciaFactura;
    $tipoDocumentoFactura = $datosVenta->tipoDocumentoFactura;
    $estadoFactura = $datosVenta->estadoFactura;

    /*******************************************************************/
    /*******************************************************************/
    /****************************************************************/
    if($idReferencia!=""){
      $condicionDatosRef = array('idFactura' => $idReferencia);
      $datosVentaRef = TraerUnDato("factura", $condicionDatosRef);
      /****************************************************************/
      // $codigoGeneracionRef = $datosVentaRef->numeroDocumentoFactura;
      $codigoGeneracionRef = $datosVentaRef->codigoGeneracion;
      $tipoDocumentoRef = $datosVentaRef->tipoDocumentoFactura;
      $fechaFacturaRef = $datosVentaRef->fechaFactura;
      $condicionDatosDocumentoRef = array('idDocumento' => $datosVentaRef->idDocumento);
      if($tipoDocumentoRef == "FAC"){
        $codigoDocumentoRef = "01";
      } else if($tipoDocumentoRef == "CCF") {
        $codigoDocumentoRef = "03";
      }
      $condicionDatosDocumentoRef = array('codigo' => $codigoDocumentoRef);
      $datosDocumentoRef = TraerUnDato("FE_CAT_002_TipodeDocumento", $condicionDatosDocumentoRef);
      $versionDocumentoRef = $datosDocumentoRef->version;
      $DocumentoRef = $datosDocumentoRef->valores;
    }

    $condicionDatos = array('idCliente' => $datosVenta->idCliente);
    $datosCliente = TraerUnDato("cliente", $condicionDatos);

    if($tipoDocumentoFactura == "FAC"){
      $nombreDocumento = "FACTURA CONSUMIDOR FINAL";
      $codigoDocumento = "01";
    } else if($tipoDocumentoFactura == "CCF") {
      $nombreDocumento = "COMPROBANTE DE CRÉDITO FISCAL";
      $codigoDocumento = "03";
    }
    $condicionVersion = array('codigo' => $codigoDocumento);
    $datosVersion = TraerUnDato("FE_CAT_002_TipodeDocumento", $condicionVersion);

    $emisor = array(
      'nit' => GblTraerConfiguracionFe('nitEmisor'),
      'nrc' => GblTraerConfiguracionFe('nrcEmisor'),
      'nombre' => GblTraerConfiguracionFe('nombreEmisor'),
      'cod_giro' => GblTraerConfiguracionFe('codGiroEmisor'),
      'giro' => GblTraerConfiguracionFe('giroEmisor'),
      'nombre_comercial' => GblTraerConfiguracionFe('nombreComercialEmisor'),
      'tipo_establecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
      'departamento' => GblTraerConfiguracionFe('departamentoEmisor'),
      'municipio' => GblTraerConfiguracionFe('municipioEmisor'),
      'complemento' => GblTraerConfiguracionFe('direccionEmisor'),
      'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
      'correo' => GblTraerConfiguracionFe('correoEmisor'),
      'logo' => GblTraerConfiguracion('logoEmpresa'),
    );
    $departamento = TraerUnDato('FE_CAT_012_Departamento',array("codigo"=>$emisor["departamento"]));
    $emisor["departamentoNombre"] = $departamento->valores;
    $municipio = TraerUnDato('FE_CAT_013_Municipio',array("codigo"=>$emisor["municipio"],"departamento" => $emisor["departamento"]));
    $emisor["municipioNombre"] = $municipio->valores;

    $condicionDatosModelo = array('codigo' => 1);
    $datosModelo = TraerUnDato("FE_CAT_003_ModelodeFacturacion", $condicionDatosModelo);

    $condicionDatosTransmision = array('codigo' => 1);
    $datosTransmision = TraerUnDato("FE_CAT_004_TipodeTransmision", $condicionDatosTransmision);

    $condicionDatosGiro = array('codigo' => $datosCliente->giroCliente);
    $datosGiros = TraerUnDato("FE_CAT_019_CodigodeActividadEco", $condicionDatosGiro);

    //ob_start();
    $this->fpdf = new Venta('P','mm','letter');
    $this->fpdf->SetMargins(7,15);
    $this->fpdf->SetTopMargin(10);
    $this->fpdf->SetLeftMargin(8);
    //Numeracion de paginas
    $this->fpdf->AliasNbPages();
    //Salto automatico de pagina margen de 20 mm
    $this->fpdf->SetAutoPageBreak(true,5);
    //Agrega la pagina a trabajar
    $this->fpdf->AddPage();
    $setX = 7;
    $setY = 5;
    $path = $emisor["logo"];
    // $path = base_url($emisor["logo"]);
    if($estadoFactura == "Anulado"){
      $imagen_anulacion = GblTraerConfiguracion("imagen_anulacion");
      $this->fpdf->Image($imagen_anulacion,0,0,220,280);
    }
    $this->fpdf->Image($path,$setX,$setY,45,22);
    // $this->fpdf->Image($path,$setX,$setY,25,25);

    $this->fpdf->SetFont('Helvetica','B', 9);

    // Salto de línea
    $this->fpdf->Ln(1);
    $this->fpdf->SetY(10);
    $this->fpdf->SetX(98);
    $this->fpdf->Cell(110, 5,utf8_decode("DOCUMENTO TRIBUTARIO ELECTRÓNICO"),'TLR', 1, 'C');
    // $this->fpdf->SetY(15);
    $this->fpdf->SetFont('Helvetica','B', 10);
    $this->fpdf->SetX(98);
    $this->fpdf->Cell(110, 5,utf8_decode($nombreDocumento),'BLR', 1, 'C');

    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->SetX(98);
    $this->fpdf->Cell(30, 4,utf8_decode("Código generación: "),'L', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(80, 4,$datosVenta->codigoGeneracion,'R', 1, 'L');
    $this->fpdf->SetX(98);
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(30, 4,utf8_decode("Sello de recepción: "),'L', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(80, 4,$datosVenta->selloRecibido,'R', 1, 'L');
    $this->fpdf->SetX(98);
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(30, 4,utf8_decode("Número de control: "),'L', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(80, 4,$datosVenta->numeroControl,'R', 1, 'L');

    $this->fpdf->SetX(98);
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(30, 4,utf8_decode("Modelo de facturacion: "),'L', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(35, 4,utf8_decode($datosModelo->valores),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(20, 4,utf8_decode("Version Json: "),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(25, 4,$datosVersion->version,'R', 1, 'L');

    $this->fpdf->SetX(98);
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(30, 4,utf8_decode("Tipo de transmisión: "),'L', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(35, 4,utf8_decode($datosTransmision->valores),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(20, 4,utf8_decode("Fecha emisión: "),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(25, 4,Fecha_D_M_A($datosVenta->fechaFactura),'R', 1, 'L');

    $this->fpdf->SetX(98);
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(30, 4,utf8_decode("Hora emisión: "),'LB', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(80, 4,Hora($datosVenta->horaFactura),'RB', 1, 'L');

    $this->qr = new QRcode();
    $ecc = 'H';
    $pixel_size = 5;
    $frame_size = 1;

    $data = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".$datosVenta->codigoGeneracion."&fechaEmi=".$datosVenta->fechaFactura;
    $file = "vendors/core/img/qrs/".$id.".png";
    $this->qr::png($data, $file, $ecc, $pixel_size, $frame_size);
    $setX = 98;
    $setY = 48;
    $this->fpdf->Image($file,$setX,$setY,25,25);
    unlink($file);

    if ($datosVenta->codigoGeneracion!= ""){
      $file1 = "vendors/core/img/qrs/".$id."_codigoGeneracion.png";
      $this->qr::png($datosVenta->codigoGeneracion, $file1, $ecc, $pixel_size, $frame_size);
      $setX = 133;
      $setY = 57;
      $this->fpdf->Image($file1,$setX,$setY,15,15);
      unlink($file1);
    }
    if ($datosVenta->selloRecibido!= ""){
      $file2 = "vendors/core/img/qrs/".$id."_selloRecibido.png";
      $this->qr::png($datosVenta->selloRecibido, $file2, $ecc, $pixel_size, $frame_size);
      $setX = 163;
      $setY = 57;
      $this->fpdf->Image($file2,$setX,$setY,15,15);
      unlink($file2);
    }
    if ($datosVenta->numeroControl!= ""){
      $file3 = "vendors/core/img/qrs/".$id."_numeroControl.png";
      $this->qr::png($datosVenta->numeroControl, $file3, $ecc, $pixel_size, $frame_size);
      $setX = 193;
      $setY = 57;
      $this->fpdf->Image($file3,$setX,$setY,15,15);
      unlink($file3);
    }

    ///encabezados
    $this->fpdf->SetX(7);
    $this->fpdf->SetY(30);
    $this->fpdf->SetFont('Helvetica','B', 10);
    $this->fpdf->Cell(90, 5,utf8_decode($emisor["nombre_comercial"]),0, 1, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(90, 4,utf8_decode($emisor["giro"]),0, 1, 'L');
    $this->fpdf->Cell(90, 4,utf8_decode($emisor["complemento"]),0, 1, 'L');
    $this->fpdf->Cell(90, 4,utf8_decode(mb_strtoupper($emisor["municipioNombre"].", ".$emisor["departamentoNombre"].", El Salvador")),0, 1, 'L');
    $this->fpdf->Cell(90, 4,utf8_decode("TELEFONO: ".$emisor["telefono"]),0, 1, 'L');
    // $this->fpdf->Cell(90, 4,utf8_decode("Telefono: ".$emisor["telefono"])." "." 2684-8500 fax 2662-0646",0, 1, 'L');
    $this->fpdf->Cell(90, 4,utf8_decode("CORREO: ".$emisor["correo"]),0, 1, 'L');
    $this->fpdf->Cell(90, 4,utf8_decode("NIT: ".$emisor["nit"]),0, 1, 'L');
    $this->fpdf->Cell(90, 4,utf8_decode("NRC: ".$emisor["nrc"]),0, 1, 'L');

    $this->fpdf->Ln(10);
    $this->fpdf->SetFont('Helvetica','', 5);
    $this->fpdf->Cell(90, 5,"",0, 0, 'L');
    $this->fpdf->Cell(25, 5,utf8_decode("Portal Ministerio de Hacienda"),0, 0, 'C');
    $this->fpdf->Cell(8, 5,utf8_decode(""),0, 0, 'C');
    $this->fpdf->Cell(20, 5,utf8_decode("Código generación"),0, 0, 'C');
    $this->fpdf->Cell(10, 5,utf8_decode(""),0, 0, 'C');
    $this->fpdf->Cell(20, 5,utf8_decode("Sello recibido"),0, 0, 'C');
    $this->fpdf->Cell(10, 5,utf8_decode(""),0, 0, 'C');
    $this->fpdf->Cell(20, 5,utf8_decode("Número de control"),0, 1, 'C');
    if($idReferencia != ""){
      $this->fpdf->Ln(-3);
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Cell(200, 5,"Documentos Relacionados",'B', 1, 'L');

      $this->fpdf->Cell(60, 4,utf8_decode("Tipo de Documento"),0, 0, 'C');
      $this->fpdf->Cell(60, 4,utf8_decode("No. de  Documento"),0, 0, 'C');
      $this->fpdf->Cell(60, 4,utf8_decode("Fecha del Documento"),0, 1, 'C');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(60, 4,utf8_decode($DocumentoRef),0, 0, 'C');
      $this->fpdf->Cell(60, 4,utf8_decode($codigoGeneracionRef),0, 0, 'C');
      $this->fpdf->Cell(60, 4,utf8_decode($fechaFacturaRef),0, 0, 'C');
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Ln(10);
    }

    $this->fpdf->Ln(-3);
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(200, 5,"Informacion del cliente",'B', 1, 'L');

    $this->fpdf->Cell(20, 4,utf8_decode("NOMBRE: "),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(140, 4,utf8_decode($datosCliente->nombreCliente),0, 1, 'L');
    $this->fpdf->SetFont('Helvetica','B', 7);
    if ($datosCliente->facturarConCliente == "DUI"){
      // if ($datosVenta->idDocumento == 2){
      // $this->fpdf->Cell(20, 4,utf8_decode(""),0, 0, 'L');
      $this->fpdf->Cell(20, 4,utf8_decode("DUI: "),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(110, 4,utf8_decode($datosCliente->duiCliente),0, 0, 'L');
    }
    else
    {
      // $this->fpdf->Cell(20, 4,utf8_decode(""),0, 0, 'L');
      $this->fpdf->Cell(20, 4,utf8_decode("NIT: "),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(110, 4,utf8_decode($datosCliente->nitCliente),0, 0, 'L');
    }

    ////////////////////////

    $this->fpdf->SetFont('Helvetica','B', 7);
    if ($datosVenta->tipoDocumentoFactura != 'FAC'){
      $this->fpdf->Cell(15, 4,utf8_decode("NRC: "),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(55, 4,utf8_decode($datosCliente->nrcCliente),0, 1, 'L');
    } else {
      $this->fpdf->Cell(15, 4,utf8_decode(""),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(55, 4,utf8_decode(""),0, 1, 'L');
      // $this->fpdf->Cell(130, 4,utf8_decode(""),0, 0, 'L');
    }

    /////////////////////////
    $departamento = TraerUnDato('FE_CAT_012_Departamento',array("codigo"=>$datosCliente->departamentoCliente));
    $departamentoClienteNombre = $departamento->valores;
    $municipio = TraerUnDato('FE_CAT_013_Municipio',array("codigo"=>$datosCliente->municipioCliente,"departamento" => $datosCliente->departamentoCliente));
    $municipioClienteNombre = $municipio->valores;
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(20, 5,utf8_decode("TELEFONO: "),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(107, 5,utf8_decode($datosCliente->telefonoCliente),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(18, 5,utf8_decode("CORREO: "),'0', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(50, 5,utf8_decode($datosCliente->emailCliente),'', 1, 'L');
    $this->fpdf->SetFont('Helvetica','B', 7);
    $this->fpdf->Cell(20, 5,utf8_decode("DIRECCION: "),0, 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->MultiCell(175,5,utf8_decode(mb_strtoupper($datosCliente->direccionCliente.", ".$municipioClienteNombre.", ".$departamentoClienteNombre)),0,'L',0);
    // $this->fpdf->Cell(100, 5,utf8_decode($datosCliente->direccionCliente.", ".$municipioClienteNombre.", ".$departamentoClienteNombre),0, 0, 'L');
    if($datosVenta->tipoDocumentoFactura != "FAC"){
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Cell(20, 4,utf8_decode("ACTIVIDAD: "),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->MultiCell(175,5,utf8_decode(mb_strtoupper($datosCliente->giroCliente." - ".$datosGiros->valores)),0,'L',0);
    }
    // $this->fpdf->Cell(110, 4,utf8_decode($datosCliente->giroCliente),0, 0, 'L');
    $this->fpdf->Ln(3);
    //////////////////////////


    $array_data = array(
      0 => array(utf8_decode("Cant"),15,"C"),
      1 => array(utf8_decode("Codigo"),28,"C"),
      2 => array(utf8_decode("Descripción"),75,"L"),
      3 => array(utf8_decode("Precio unitario"),20,"R"),
      4 => array(utf8_decode("Descuento"),20,"R"),
      5 => array(utf8_decode("V. N. S."),12,"R"),
      6 => array(utf8_decode("V. E."),10,"R"),
      7 => array(utf8_decode("Venta gravada"),20,"R"),
    );
    $this->fpdf->LineWriteB($array_data, 1,5);

    $joinDet = array(
      array(
        "tabla" => "producto",
        "tipo" => "left",
        "condicion" => "pedidoDetalle.idProductoPedidoDetalle = producto.idProducto"
      ),
    );
    $datosDetalle = TraerDatosJoin("pedidoDetalle",array("pedidoDetalle.idPedido" => $idRef),"",$joinDet);
    $setYValor = $this->fpdf->GetY();
    $this->fpdf->Line(8, $setYValor, 8, $setYValor+140);
    $this->fpdf->Line(208, $setYValor, 208, $setYValor+140);
    $gravado = 0;
    $descuentos = 0;
    $totalIva = 0;
    $nDatosPedidoDetalle = 0;
    foreach ($datosDetalle as $fila){
      $cantidad = $fila->cantidadPedidoDetalle;
      if($datosVenta->tipoDocumentoFactura != "FAC"){
        $precioUnitarioMostrar = $fila->precioPedidoDetalle / (1 + GblTraerConfiguracionFe("iva"));
        $descuentoMostrar = 0;//(($fila->precioUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
        $subTotalMostrar = ($cantidad * $precioUnitarioMostrar);// / (1 + GblTraerConfiguracionFe("iva")));// - ($descuentoMostrar * $cantidad);
        // $subTotalMostrar = ($fila->subTotalIvaPedidoDetalle / (1 + GblTraerConfiguracionFe("iva")));// - ($descuentoMostrar * $cantidad);
      } else {
        $precioUnitarioMostrar = $fila->precioPedidoDetalle;
        $descuentoMostrar = 0;//(($fila->precioIvaUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
        $subTotalMostrar = $cantidad * $precioUnitarioMostrar;
        // $subTotalMostrar = ($fila->subTotalIvaPedidoDetalle) - ($descuentoMostrar * $cantidad);
      }
      $gravado += $subTotalMostrar;
      $descuentos += $descuentoMostrar;
      if($fila->nombreProducto != ""){
        $array_data = array(
          0 => array(number_format($cantidad,2),15,"C"),
          1 => array(utf8_decode($fila->barcodeProducto),28,"C"),
          2 => array(utf8_decode($fila->nombreProducto),75,"L"),
          3 => array(number_format($precioUnitarioMostrar,4),20,"R"),
          4 => array(number_format($descuentoMostrar,4),20,"R"),
          5 => array(utf8_decode(""),12,"R"),
          6 => array(utf8_decode(""),10,"R"),
          7 => array(number_format($subTotalMostrar,4),20,"R"),
        );
      } else {
        $array_data = array(
          0 => array("",43,"C"),
          1 => array(utf8_decode(mb_strtoupper($fila->observacion)),75,"L"),
        );
        $nDatosPedidoDetalle += 1;
      }
      $this->fpdf->LineWriteB($array_data, 0,4);
      $nDatosPedidoDetalle += 1;
    }
    $valor_altura = $nDatosPedidoDetalle*4;
    $alturaReal = 136 - $valor_altura;
    if($datosVenta->tipoDocumentoFactura != "FAC"){
      $alturaReal = 124 - $valor_altura;
    }
    if($idReferencia != ""){
      $alturaReal = 106 - $valor_altura;
    }
    $this->fpdf->Ln($alturaReal);
    $this->fpdf->SetFont('Helvetica','B', 7);
    /*******************************************/
    /*******************************************/
    /*******************************************/
    $iva = round($gravado * GblTraerConfiguracionFe("iva"),2);
    $subtotal = round($gravado,2);
    $nosujeto=0;
    $exento=0;
    $total = round($gravado,2);
    if($datosVenta->tipoDocumentoFactura == "CCF"){
      $total = round($gravado+$iva,2);
    }
    $ivaRet = 0;
    if($datosCliente->retieneIvaCliente){
      if($datosVenta->tipoDocumentoFactura == "CCF"){
        $ivaRet = $gravado * GblTraerConfiguracionFe("ivaRet");
      } else {
        $ivaRet = ($gravado/(1+GblTraerConfiguracionFe("iva"))) * GblTraerConfiguracionFe("ivaRet");
      }
      $total = round($total-$ivaRet,2);
    }
    $retencion = round(0,2);

    if($datosCliente->retieneRentaCliente){
      if($codigoDocumento == "CCF"){
        $retencion = $gravado * GblTraerConfiguracionFe("ivaRent");
      } else {
        $retencion = ($gravado/(1+GblTraerConfiguracionFe("iva"))) * GblTraerConfiguracionFe("ivaRent");
      }
      $total = round($total-$retencion,2);
    }
    /*******************************************/
    /*******************************************/
    $valorVenta = explode(".", number_format($total,2,".",""));
    $entero = $valorVenta[0];
    if($entero > 0){
      $entero = num2letras($entero);
    } else{
      $entero = "CERO";
    }
    $centavos = $valorVenta[1];
    $this->fpdf->Cell(20, 8,utf8_decode("Valor en letras: "),'LTB', 0, 'L');
    $this->fpdf->SetFont('Helvetica','', 7);
    $this->fpdf->Cell(120, 8,utf8_decode($entero." con ".$centavos."/100"),"TB", 0, 'L');
    $this->fpdf->SetFont('Helvetica','',6);
    // $this->fpdf->Cell(21, 5,utf8_decode("Total operación"),"T", 0, 'L');
    // $this->fpdf->Cell(13, 5,utf8_decode("No sujetas ".$datosVenta->noSujetoFactura),"T", 0, 'L');
    // $this->fpdf->Cell(13, 5,utf8_decode("Exentas ".$datosVenta->excentoFactura),"T", 0, 'L');
    // $this->fpdf->Cell(13, 5,utf8_decode("Gravadas ".$datosVenta->sumasFactura),"TR", 1, 'L');
    $this->fpdf->SetX(148);
    $array_data = array(
      0 => array(utf8_decode("Total operación"),15,"C"),
      1 => array(utf8_decode("No sujetas ".number_format($nosujeto,2)),15,"C"),
      2 => array(utf8_decode("Exentas ".number_format($exento,2)),15,"L"),
      3 => array(utf8_decode("Gravadas ".number_format($gravado,2)),15,"R"),
    );
    $this->fpdf->LineWriteB1($array_data, 1,4);

    $this->fpdf->SetFont('Helvetica','',6);
    $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
    $this->fpdf->Cell(45, 4,utf8_decode("Suma de operacion sin impuestos"),1, 0, 'L');
    $this->fpdf->Cell(15, 4,utf8_decode(number_format($gravado,2)),1, 1, 'R');

    $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
    $this->fpdf->Cell(45, 4,utf8_decode("Retención Renta"),1, 0, 'L');
    $this->fpdf->Cell(15, 4,utf8_decode(number_format($retencion,2)),1, 1, 'R');

    $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
    $this->fpdf->Cell(45, 4,utf8_decode("IVA Retenido"),1, 0, 'L');
    $this->fpdf->Cell(15, 4,utf8_decode(number_format($ivaRet,2)),1, 1, 'R');
    if($datosVenta->tipoDocumentoFactura != "FAC"){
      $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("IVA 13%"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($iva,2)),1, 1, 'R');

      $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Sub total"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($total,2)),1, 1, 'R');
      $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Monto total de la operacion"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($total,2)),1, 1, 'R');

      $this->fpdf->SetFont('Helvetica','',6);
      $this->fpdf->Cell(140, 4,utf8_decode(""),"LB", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Total a pagar"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($total,2)),1, 1, 'R');
    } else {
      $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Monto total de la operacion"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($total,2)),1, 1, 'R');

      $this->fpdf->SetFont('Helvetica','',6);
      $this->fpdf->Cell(140, 4,utf8_decode(""),"LB", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Total a pagar"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($total,2)),1, 1, 'R');
    }

    ob_clean();
    $filename = $id.".pdf";
    $this->fpdf->Output($filename, "I");
    // exit;
    //$this->imprimirReporteComision($fechaInicio,$fechaFinal);
  }
  function FacturasCambiarEstado(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      $datosRespuesta["codigo"] = 403;
    } else {
      if ($this->input->method(TRUE) == "POST") {
        $idFactura = $this->input->post("idFactura");
        $estado = $this->input->post("accion");
        $comentario = $this->input->post("comentario");
        $TraerFactura = TraerUnDatoIndividual("factura","idFactura",array("md5(idFactura)" => $idFactura));
        $factura = $TraerFactura[0]["idFactura"];
        IniciarTransaccion();
        $error =  false;
        $datosFactura = array(
          "idFactura" => $factura,
          "tipoFacturaModificacion" => $estado,
          "comentarioFacturaModificacion" => $comentario,
          "idUsuarioFacturaModificacion" => $this->session->idUsuario,
          "estadoFacturaModificacion" => "Activo"
        );
        $guardar = GuardarDatos("facturaModificacion",$datosFactura);

        if($guardar){

          $nestado = ($estado =='anular') ? 'Anulado' : ( ($estado =='borrar') ? 'Borrado' : '');
          $datosFacturas = array(
            "estadoFactura" => $nestado,
            "aleatorioFactura" => uniqid()
          );
          // var_dump($estado);
          // var_dump($datosFacturas);
          $condicion = array("md5(idFactura)" => $idFactura);
          $editar = EditarDatos($this->tabla, $datosFacturas, $condicion);
          if ($editar) {
            $datosRespuesta["codigo"] = 200;
          } else {
            $error = true;
            $datosRespuesta["codigo"] = 501;
          }
        }
        else{
          $error = true;
          $datosRespuesta["codigo"] = 501;
        }

        if($error){
          DeshacerTransaccion();
        }
        else{
          EjecutarTransaccion();
        }

        echo json_encode($datosRespuesta);

      }
    }
  }

  function FacturasEliminar(){
    if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
      $datosRespuesta["codigo"] = 403;
    } else {
      if ($this->input->method(TRUE) == "POST") {
        $idFactura = $this->input->post("idFactura");
        $datosFacturas = array(
          "estadoFactura" => 'Borrado'
        );
        $condicion = array("md5(idFactura)" => $idFactura);
        IniciarTransaccion();
        $editar = EditarDatos($this->tabla, $datosFacturas, $condicion);
        if ($editar) {
          EjecutarTransaccion();
          $datosRespuesta["codigo"] = 200;
        } else {
          DeshacerTransaccion();
          $datosRespuesta["codigo"] = 500;
        }
      }
    }
    echo json_encode($datosRespuesta);
  }
  function VentasError($idVenta){
    if($this->input->method(TRUE) == "GET") {
      $factura = TraerUnDato("factura",array("idFactura" => $idVenta));
      $titulo = "Error";
      $datosVista = array(
        "titulo" => $titulo,
        "icono" => "fas fa-exclamation-triangle",
        "controlador" => $this->controlador,
        "factura" => $factura,
      );
      $extras = array(
        'css' => array(
        ),
        'js' => array(
        ),
      );
      $this->load->view("facturas/Error",$datosVista);
    } else if ($this->input->method(TRUE) == "POST") {
    }
  }
  function VentasReenviar($idVenta){
    if($this->input->method(TRUE) == "GET") {
      $join = array(
        array(
          'tabla' => 'cliente',
          'tipo' => 'inner',
          'condicion' => 'cliente.idCliente=factura.idCliente',
          'campos' => 'cliente.emailCliente'
        )
      );
      $factura = TraerUnDatoJoin("factura",array("idFactura" => $idVenta),$join);
      $titulo = "Reenviar Correo";
      $datosVista = array(
        "titulo" => $titulo,
        "icono" => "fas fa-exclamation-triangle",
        "controlador" => $this->controlador,
        "factura" => $factura,
      );
      $extras = array(
        'css' => array(
        ),
        'js' => array(
        ),
      );
      $this->load->view("facturas/Reenviar",$datosVista);
    } else if ($this->input->method(TRUE) == "POST") {
    }
  }
  function VentasContingencia($idVenta){
    if($this->input->method(TRUE) == "GET") {
      $factura = TraerUnDato("factura",array("idFactura" => $idVenta));
      $titulo = "Evento de Contingencia";

      $datosContingencia = TraerDatos("FE_CAT_005_TipodeContingencia");

      $datosVista = array(
        "titulo" => $titulo,
        "icono" => "fas fa-exclamation-triangle",
        "controlador" => $this->controlador,
        "factura" => $factura,
        "contingencia" => $datosContingencia,
      );
      $extras = array(
        'css' => array(
        ),
        'js' => array(
        ),
      );
      $this->load->view("facturas/Contingencia",$datosVista);
    } else if ($this->input->method(TRUE) == "POST") {
    }
  }
  function FacturasDoc($idFactura){
    if($this->input->method(TRUE) == "GET"){

      $fechaInicio = date("Y-m-d");
      $fechaFinal = date("Y-m-d");
      $sucursal = $this->session->idSucursal;

      $join = array(
        array(
          "tabla" => "usuario",
          "condicion" =>"factura.idUsuario = usuario.idUsuario",
          "tipo" => "inner",
          "campos" => "usuario.nombreUsuario"
        ),
        array(
          "tabla" => "cliente",
          "condicion" =>"factura.idCliente = cliente.idCliente",
          "tipo" => "left",
          "campos" => "cliente.nombreCliente"
        ),
      );
      $condicionFacDet = array("tipoFactura" => "Producto", "idSucursalFactura" => $sucursal,"estadoFactura" => "Cobrado","md5(idFactura)" =>$idFactura);
      $factura = TraerDatosJoin("factura",$condicionFacDet,"",$join);

      $this->fpdf = new Pdf();
      // $this->fpdf->SetMargins(10,5);
      $this->fpdf->SetTopMargin(2);
      $this->fpdf->SetLeftMargin(10);
      $this->fpdf->AliasNbPages();
      $this->fpdf->SetAutoPageBreak(true,1);
      $this->fpdf->AddFont("courier new","","courier.php");
      $iva=GblTraerConfiguracion('iva')/100;
      foreach ($factura as $fila)
      {
        $documento = $fila->tipoDocumentoFactura." ".$fila->numeroDocumentoFactura;
        $idPedido = $fila->idReferenciaFactura;
        $join = array(
          array(
            "tabla" => "producto",
            "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
            "tipo" => "inner",
            "campos" => "producto.nombreProducto"
          ),
        );
        $condicion = array("idPedido" => $idPedido);
        $facturaDetalle = TraerDatosJoin("pedidoDetalle",$condicion,"",$join);

        $cuen_ss = count($facturaDetalle);
        $ff = 5 * $cuen_ss;
        $nn = $ff+110;
        $this->fpdf->AddPage('P','Letter',0);


        //$this->fpdf->Image($logob,160,4,50,15);
        $set_x = 0;
        $set_y = 12;


        $set_y = 32;
        $set_x = 2;
        $numero_doc = $fila->numeroDocumentoFactura;
        // list($numm,$ads) = explode("_", $numero_doc);
        $hora = $fila->horaFactura;
        $fecha = $fila->fechaFactura;
        $tipo = $fila->tipoDocumentoFactura;
        $id_cliente = $fila->idCliente;
        $nombreFactura = $fila->nombreFactura;
        $porConsumo = $fila->porConsumoFactura;
        $direccionFactura = $fila->direccionFactura;
        $nitFactura = $fila->nitFactura;
        $nrcFactura = $fila->nrcFactura;
        $id_empleado = $fila->idUsuario;
        $total_exento = 0;//
        $total_gravado = $fila->totalFactura;
        $total = $fila->totalFactura;
        $propina = $fila->propinaFactura;
        $descuento = 0;
        $total_pago = $total - $descuento + $propina;
        $id_vendedor = $id_empleado;
        $nombre_cli = $fila->nombreCliente;
        $nombre_em = $fila->nombreUsuario;

      if($tipo == "FAC") {
        /************************encabezado**********************************************/
        $this->fpdf->SetFont('Arial','',10);
        $this->fpdf->SetXY(70,33);
        $this->fpdf->Cell(37,6,str_replace("-","       ", fecha_d_m_a($fecha)),0,0,"L");
        $this->fpdf->SetXY(28,39);
        $this->fpdf->MultiCell(140,6,utf8_decode($nombreFactura),0,"L",0);
        $this->fpdf->SetXY(31,44);
        $this->fpdf->MultiCell(140,6,utf8_decode($nitFactura),0,"L",0);
        $this->fpdf->SetXY(33,49);
        $this->fpdf->MultiCell(140,6,utf8_decode(substr($direccionFactura,0,90)),0,"L",0);
        /*********************************inician detalles*************************************/
        $x = 9; $y = 58; $m=0; $total_gravado=0; $total_final = 0;
        /**********************************************************************/
          if($cuen_ss > 0) {
            foreach ($facturaDetalle as $filaDetalle) {
              $cantidad = $filaDetalle->cantidadPedidoDetalle;
              $precio_venta = $filaDetalle->precioPedidoDetalle;
              $gravado = $cantidad * $precio_venta;
              $exento = 0;
              if($gravado > 0)  { $lee = "G";}
              else if($exento > 0) { $precio_venta = $precio_venta / 1.13; }
              $subt=$precio_venta*$cantidad;

              $total_final=$total_final+$subt;
              if ($exento==0) {
                $precio_sin_iva =round($precio_venta,4);
                $subt_sin_iva=round($precio_venta*$cantidad,4);
                $subt_gravado=round($subt_sin_iva,4);
                $total_gravado=$subt_sin_iva+$total_gravado;
              } else {
                $precio_sin_iva =round($precio_venta,4);
                $subt_sin_iva=$precio_venta*$cantidad;
                $subt_exento=sprintf("%.2f",$subt_sin_iva);
                $total_exento=$subt_sin_iva+$total_exento;
              }
              $precio_sin_iva_print=round($precio_sin_iva,4);
              $subt_sin_iva_print=round($subt_sin_iva,4);
              if($porConsumo == "0"){
                $nombre = $filaDetalle->nombreProducto;
                $this->fpdf->SetFont('Arial','',8);
                $this->fpdf->SetXY($x,$y+$m);
                $this->fpdf->Cell(10,5,number_format($cantidad,0),0,0,"C");
                $this->fpdf->SetXY($x+9,$y+$m);
                $this->fpdf->MultiCell(65,5,utf8_decode(substr($nombre,0,38)),0,"L",0);
                $this->fpdf->SetFont('Arial','',8);
                if($precio_sin_iva_print > 0) {
                  $this->fpdf->SetXY($x+58,$y+$m);
                  $this->fpdf->Cell(10,5,"$".utf8_decode(number_format($precio_sin_iva_print,2,".",",")),0,0,"R");
                }
                if($subt_gravado>0){
                  $this->fpdf->SetXY($x+79,$y+$m);
                  $this->fpdf->Cell(12,5,"$".utf8_decode(number_format($subt_gravado,2,".",",")),0,0,"R");
                }
                $in = ceil(strlen($nombre)/30);
                $m+=$in*5.0 ;
              }
            }
            if($porConsumo == "1"){
              $nombre = "Cobro Por Consumo";
              $this->fpdf->SetFont('Arial','',8);
              $this->fpdf->SetXY($x,$y);
              $this->fpdf->Cell(10,5,number_format(1,0),0,0,"C");
              $this->fpdf->SetXY($x+9,$y);
              $this->fpdf->MultiCell(65,5,utf8_decode($nombre),0,"L",0);
              $this->fpdf->SetFont('Arial','',8);
              if($precio_sin_iva_print > 0) {
                $this->fpdf->SetXY($x+58,$y);
                $this->fpdf->Cell(10,5,"$".utf8_decode(number_format($total_final,2,".",",")),0,0,"R");
              }
              if($subt_gravado>0){
                $this->fpdf->SetXY($x+79,$y);
                $this->fpdf->Cell(12,5,"$".utf8_decode(number_format($total_final,2,".",",")),0,0,"R");
              }
            }
          }

          $calc_iva=round($iva*$total_gravado,4);
          $total_iva_format=sprintf("%.2f",$calc_iva);
          $total_value=sprintf("%.2f",$total);
          $sp3=10;
          $total_fin=$total_exento+$total_gravado;
          $total_value_exento=sprintf("%.2f",$total_exento);
          $total_value_gravado=sprintf("%.2f",$total_gravado);
          $total_value_fin=sprintf("%.2f",$total_fin);
          $subtotal_gravado=round($total_gravado,2);
          $subtotal_exento=$total_exento;
          $total_final_todos=round($subtotal_exento+$subtotal_gravado,4);
          $subtotal_gravado_print=sprintf("%.2f",$subtotal_gravado);
          // $total_final_todoss=sprintf("%.2f",$total_final_todos-$retencion);
          $total_final_todoss=sprintf("%.2f",$total_final_todos);

          list($entero,$decimal)=explode('.',$total_final_todoss);
          $enteros_txt=num2letras($entero);
          if($entero=='100' && $decimal=='00'){
            $enteros_txt="CIEN";
          }
          if(strlen($decimal)==1){
            $decimales_txt=$decimal."0";
          }
          else{
            $decimales_txt=$decimal;
          }

          $cadena_salida_txt= $enteros_txt." dolares con ".$decimales_txt."/100 ctvs";
          $this->fpdf->SetFont('Arial','',9);
          $this->fpdf->SetXY(10,129);
          $this->fpdf->MultiCell(49,4,utf8_decode("         ".ucfirst($cadena_salida_txt)),0,"L",0);

          $this->fpdf->SetXY(88,129);
          $this->fpdf->Cell(12,6,"$".utf8_decode(number_format($total_value_gravado,2,".","")),0,"R",0);
          $this->fpdf->SetXY(88,135);
          $this->fpdf->Cell(12,6,"$".utf8_decode(number_format($subtotal_gravado_print,2,".","")),0,"R",0);
          // if($retencion>0) {
          //   $this->fpdf->SetXY(88,141);
          //   $this->fpdf->Cell(12,5,utf8_decode(number_format($retencion,2,".","")),0,"L",0);
          // }
          $this->fpdf->SetXY(88,147);
          $this->fpdf->Cell(12,6,"$".utf8_decode(number_format($total_final_todoss,2,".","")),0,"R",0);
      }
      if($tipo == "CCF") {
        $mx = 0; // Margen X
        $my = 0; // Margen Y
        $h = 5; //Ancho de linea

        /* Posicion encabezados   [X,Y,Ancho]*/
        $pFecha      = [83,48,47];
        $pCliente    = [18,55,107];
        $pDireccion  = [21,60,53];
        $pDpto       = [92,60,33];
        $pNit        = [15,66,52];
        $pNrc        = [85,66,25];
        $pGiro       = [111,67,38];
        $pPago       = [37,72,35];

        /* Posicion detalles   [X,Y,Ancho]*/
        $pCant = [10,91,12];
        $pDesc = [21,91,70];
        $pPuni = [93,91,13];
        $pCtot = [110,91,18];

        /* Posicion totales   [X,Y,Ancho]*/
        $pTxtCant = [15,175,65];
        $pSum     = [110,175,18];
        $pIva     = [110,181,18];
        $pSubTot  = [110,187,18];
        $pTot     = [110,203,18];

        /************************encabezado**********************************************/
        $this->fpdf->SetFont('Arial','',9);
        $this->fpdf->SetXY($pFecha[0]+$mx,$pFecha[1]+$my);
        $this->fpdf->Cell($pFecha[2],$h,str_replace("-","                 ", $fecha),0,0,"L");
        $this->fpdf->SetXY($pCliente[0]+$mx,$pCliente[1]+$my);
        $this->fpdf->MultiCell($pCliente[2],$h,utf8_decode($nombreFactura),0,"L",0);
        $this->fpdf->SetXY($pDireccion[0]+$mx,$pDireccion[1]+$my);
        $this->fpdf->MultiCell($pDireccion[2],$h,utf8_decode(substr($direccionFactura,0,26)."..."),0,"L",0);
        $this->fpdf->SetXY($pNit[0]+$mx,$pNit[1]+$my);
        $this->fpdf->Cell($pNit[2],$h,utf8_decode($nitFactura),0,0,"L");
        $this->fpdf->SetXY($pNrc[0]+$mx,$pNrc[1]+$my);
        $this->fpdf->Cell($pNrc[2],$h,utf8_decode($nrcFactura),0,0,"L");
        /**********************************************************************/
        $x = 1+$mx; $y = 90+$my; $m=0; $total_gravado=0; $total_final = 0;
        /**********************************************************************/
        if($cuen_ss > 0) {
          foreach ($facturaDetalle as $filaDetalle) {
            $cantidad = $filaDetalle->cantidadPedidoDetalle;
            $precio_venta = $filaDetalle->precioPedidoDetalle;
            $gravado = $cantidad * $precio_venta;
            $descuento = 0;
            $exento = 0;
            $subt=$precio_venta*$cantidad;
            $total_final=$total_final+$subt;
            $precio_venta = $precio_venta - ($precio_venta * ($descuento / 100));
            if ($exento==0) {
              $precio_sin_iva =round($precio_venta/(1+($iva)),4);
              $subt_sin_iva=round(($precio_venta/(1+($iva)))*$cantidad,4);
              $subt_gravado=round($subt_sin_iva,4);
              $total_gravado=$subt_sin_iva+$total_gravado;
            } else {
              $precio_sin_iva =round($precio_venta,4);
              $subt_sin_iva=$precio_venta*$cantidad;
              $subt_exento=sprintf("%.2f",$subt_sin_iva);
              $total_exento=$subt_sin_iva+$total_exento;
            }
            $precio_sin_iva_print=round($precio_sin_iva,4);
            $subt_sin_iva_print=round($subt_sin_iva,4);

            $nombre = $filaDetalle->nombreProducto;
            $this->fpdf->SetXY($pCant[0]+$mx,$pCant[1]+$my+$m);
            $this->fpdf->Cell($pCant[2],$h,number_format($cantidad,0),0,0,"C");
            $this->fpdf->SetXY($pDesc[0]+$mx,$pDesc[1]+$my+$m);
            $this->fpdf->MultiCell($pDesc[2],$h,utf8_decode($nombre),0,"L",0);
            if($precio_sin_iva_print > 0) {
              $this->fpdf->SetXY($pPuni[0]+$mx,$pPuni[1]+$my+$m);
              $this->fpdf->Cell($pPuni[2],$h,"$".utf8_decode(number_format($precio_sin_iva_print,2,".",",")),0,0,"R");
            }
            if($subt_gravado>0) {
              $this->fpdf->SetXY($pCtot[0]+$mx,$pCtot[1]+$my+$m);
              $this->fpdf->Cell($pCtot[2],$h,"$".utf8_decode(number_format($subt_gravado,2,".",",")),0,0,"R");
            }
            $in = ceil(strlen($nombre)/38);
            $m+=$in*$h;
          }
        }

        $calc_iva=round($iva*$total_gravado,4);
        $total_iva_format=sprintf("%.2f",$calc_iva);

        $total_value=sprintf("%.2f",$total);
        $total_fin=$total_exento+$total_gravado;
        $total_value_exento=sprintf("%.2f",$total_exento);
        $total_value_gravado=sprintf("%.2f",$total_gravado);
        $total_value_fin=sprintf("%.2f",$total_fin);

        $subtotal_gravado=round($total_gravado+$calc_iva,2);
        $subtotal_exento=$total_exento;
        $total_final_todos=round($subtotal_exento+$subtotal_gravado,4);

        $subtotal_gravado_print=sprintf("%.2f",$subtotal_gravado);

        // $total_final_todoss=sprintf("%.2f",$total_final_todos-$retencion);
        $total_final_todoss=sprintf("%.2f",$total_final_todos);

        //$total_final_format=sprintf("%.2f",$total_final-$retencion);
        list($entero,$decimal)=explode('.',$total_final_todoss);
        $enteros_txt = ($entero=='100' && $decimal=='00') ? "CIEN" : num2letras($entero);
        $decimales_txt = (strlen($decimal)==1) ? $decimal."0" : $decimal;

        $cadena_salida_txt= $enteros_txt." dolares con ".$decimales_txt."/100 ctvs";

        $this->fpdf->SetXY($pTxtCant[0]+$mx,$pTxtCant[1]+$my);
        $this->fpdf->MultiCell($pTxtCant[2],$h,utf8_decode(ucfirst($cadena_salida_txt)),0,"L",0);

        $this->fpdf->SetXY($pSum[0]+$mx,$pSum[1]+$my);
        $this->fpdf->Cell($pSum[2],$h,"$".number_format($total_value_gravado,2,".",","),0,"L",0);
        $this->fpdf->SetXY($pIva[0]+$mx,$pIva[1]+$my);
        $this->fpdf->Cell($pIva[2],$h,"$".number_format($total_iva_format,2,".",","),0,"L",0);
        $this->fpdf->SetXY($pSubTot[0]+$mx,$pSubTot[1]+$my);
        $this->fpdf->Cell($pSubTot[2],$h,"$".number_format($subtotal_gravado_print,2,".",","),0,"L",0);
        // if ($retencion>0) {
        //   $this->fpdf->SetXY(104+$mx,177+$my);
        //   $this->fpdf->Cell(13,5,number_format($retencion,2,".",","),0,"L",0);
        // }
        $this->fpdf->SetXY($pTot[0]+$mx,$pTot[1]+$my);
        $this->fpdf->Cell($pTot[2],$h,"$".number_format($total_final_todoss,2,".",","),0,"L",0);
      }

      }
      $filename = str_replace(' ','_',$nombreFactura).".pdf";
      ob_clean();
      $this->fpdf->Output($filename, "I");
      exit;
    }
  }
}
/* End of file Facturas.php */
