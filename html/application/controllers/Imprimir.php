<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Imprimir extends CI_Controller
{

  private $controlador = "Imprimir";
  function __construct()
  {
    parent::__construct();
    $this->load->Model('CoreModel', "core");
  }

  public function index(){
    if ($this->input->method(TRUE) == "GET") {

    }
  }
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  function AbrirGaveta(){
    if ($this->input->method(TRUE) == "POST") {
      $idSucursal = $this->session->idSucursal;
      $condicionImp = array("idSucursalImpresora" => $idSucursal,"cobroImpresora" => "1");
      $impresora = TraerUnDato("impresora",$condicionImp);
      if($impresora){
        $tipo = $impresora->tipoImpresora;
        $recurso = $impresora->recursoCompartidoImpresora;
        $ip = $impresora->IpImpresora;
        $servidor = $impresora->servidorImpresora;
      } else {
        $tipo = "WIN";
        $recurso = "ticket";
        $ip = "localhost";
        $servidor = base_url();
      }
      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["tipo"] = $tipo;
      $datosRespuesta["recursoCompartido"] = $recurso;
      $datosRespuesta["IpImpresora"] = $ip;
      $datosRespuesta["servidor"] = $servidor;
    }

    echo json_encode($datosRespuesta);
  }
  function ImprimirTiket($idFactura = ''){
    if ($this->input->method(TRUE) == "POST") {
      $ticket = "";
      $emisor = array(
        'nit' => GblTraerConfiguracionFe('nitEmisor'),
        'nrc' => GblTraerConfiguracionFe('nrcEmisor'),
        'nombre' => GblTraerConfiguracionFe('nombreEmisor'),
        'codigoGiro' => GblTraerConfiguracionFe('codGiroEmisor'),
        'giro' => GblTraerConfiguracionFe('giroEmisor'),
        'nombreComercial' => GblTraerConfiguracionFe('nombreComercialEmisor'),
        'tipoEstablecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
        'departamento' => GblTraerConfiguracionFe('departamentoEmisor'),
        'municipio' => GblTraerConfiguracionFe('municipioEmisor'),
        'complemento' => GblTraerConfiguracionFe('direccionEmisor'),
        'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
        'correo' => GblTraerConfiguracionFe('correoEmisor')
      );
      $departamento = TraerUnDato('FE_CAT_012_Departamento',array("codigo"=>$emisor["departamento"]));
      $emisor["departamentoNombre"] = $departamento->valores;
      $municipio = TraerUnDato('FE_CAT_013_Municipio',array("codigo"=>$emisor["municipio"],"departamento" => $emisor["departamento"]));
      $emisor["municipioNombre"] = $municipio->valores;
      $condicionFac = array("md5(idFactura)" => $idFactura);
      $factura = TraerUnDato("factura",$condicionFac);
      $joinFacDet = array(
        array(
          "tabla" => "producto",
          "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
          "tipo" => "inner",
          "campos" => "producto.nombreProducto"
        )
      );
      $joinCaja = array(
        array(
          'tabla' => "cajaDocumento",
          'condicion' => "cajaDocumento.idCajaCajaDocumento=caja.idCaja",
          'tipo' => "inner",
          'campos' => "fechaAutorizacionCajaDocumento,fechaResolucionCajaDocumento,numeroResolucionCajaDocumento,inicioCajaDocumento,finalCajaDocumento,serieCajaDocumento"
        ),
        array(
          'tabla' => "documento",
          'condicion' => "cajaDocumento.idDocumentoCajaDocumento=documento.idDocumento",
          'tipo' => "inner",
          'campos' => 'aliasDocumento'
        ),
      );
      $condicionCaja = array('idCaja' => $factura->idCajaFactura, 'documento.aliasDocumento' => "TIK");
      $caja = TraerUnDatoJoin("caja",$condicionCaja,$joinCaja);

      $condicionDatoscli = array('idCliente' => $factura->idCliente);
      $datosCliente = TraerUnDato("cliente", $condicionDatoscli);

      $condicionCajero = array('idUsuario' => $factura->idUsuario);
      $cajero = TraerUnDato("usuario",$condicionCajero);

      /*******************************************************/
      $propina = 0;
      $propinaCalculada = 0;
      $cobroPropina = GblTraerConfiguracion("cobroPropina");
      if($cobroPropina == "Si"){
        $propina = GblTraerConfiguracion("valorPropina");
      }
      $propinaFactura = $factura->propinaFactura;
      /******************************************************/
      $inicio = str_pad(" ",2," ",STR_PAD_BOTH);
      $salto = "\n";
      $linea = str_pad("",42,"_",STR_PAD_BOTH).$salto;
      $divisor = "|";
      $espaciosEncabezado = 14;
      $espacios = 40;
      if($emisor["nombreComercial"] != ""){
        $ticket.=str_pad($emisor["nombreComercial"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      if($emisor["complemento"] != ""){
        $ticket.=str_pad($emisor["complemento"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      $ticket.=str_pad($emisor["departamentoNombre"].", ".$emisor["municipioNombre"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      if($emisor["telefono"] != ""){
        $ticket.=str_pad("TEL. ".$emisor["telefono"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      if($emisor["nit"] != ""){
        $ticket.=str_pad("NIT. ".$emisor["nit"]."  NRC. ".$emisor["nrc"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      if($emisor["giro"] != ""){
        $ticket.=str_pad($emisor["giro"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      /*************************************************/
      /*************************************************/
      if($caja->numeroResolucionCajaDocumento != ""){
        $ticket.=str_pad("RESOLUCION: ".$caja->numeroResolucionCajaDocumento,$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      // $ticket.=str_pad("FECHA DE RESOLUCION: ".$caja->fechaResolucionCajaDocumento,$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      if($caja->fechaAutorizacionCajaDocumento != ""){
        $ticket.=str_pad("FECHA DE AUTORIZACION: ".$caja->fechaAutorizacionCajaDocumento,$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      if($caja->serieCajaDocumento != ""){
        $ticket.=str_pad("SERIE AUTORIZADA: ".$caja->serieCajaDocumento,$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      if($caja->inicioCajaDocumento != ""){
        $ticket.=str_pad("DEL: ".$caja->inicioCajaDocumento." AL: ".$caja->finalCajaDocumento,$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      $ticket.=str_pad("TICKET #".$factura->numeroDocumentoFactura,$espaciosEncabezado," ",STR_PAD_BOTH).$salto;

      /*************************************************/
      /*************************************************/
      $ticket.=$divisor;
      $ticket.=$linea;
      $ticket.=str_pad("FECHA: ".fecha_d_m_a($factura->fechaFactura)." ".hora($factura->horaFactura),$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=str_pad("CLIENTE: ".$datosCliente->nombreCliente,$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=str_pad("CAJA: ".$caja->nombreCaja,$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=str_pad("CAJERO: ".$cajero->nombreUsuario,$espacios," ",STR_PAD_RIGHT).$salto;
      // $ticket.=str_pad("VENDEDOR: ".$vendedor->nombreUsuario,$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=$linea;
      $ticket.=$divisor;

      $condicionFacDet = array("pedidoDetalle.idPedido" => $factura->idReferenciaFactura,"estadoPedidoDetalle !="=>"Borrado");
      $facturaDetalle = TraerDatosJoin("pedidoDetalle",$condicionFacDet,'',$joinFacDet);

      $ticket .= "CANT. ".str_pad("DETALLE",24," ",STR_PAD_RIGHT)." P.U    SUBT".$salto;
      // $ticket .= " CANT. ".str_pad("DETALLE",24," ",STR_PAD_RIGHT)." P.U   SUBT".$salto;
      $total = 0;
      if($facturaDetalle!=null){
        foreach ($facturaDetalle as $detalle) {
          $descripcion = $detalle->nombreProducto;
          $precio = $detalle->precioPedidoDetalle;
          if($detalle->regaliaPedidoDetalle){
            $precio=0;
          }
          $cantidad = $detalle->cantidadPedidoDetalle;
          $descuento = 0;
          $subtotal = $cantidad * $precio;
          $total += $subtotal;
          $arrayNprod = $this->dtl($descripcion,18);
          for ($i=0; $i < count($arrayNprod); $i++) {
            $descrip = $arrayNprod[$i];
            if($i == 0){
              $ticket .= str_pad(number_format($cantidad,2,".",""),7," ",STR_PAD_BOTH);
              $ticket .= str_pad(mb_strtoupper($descrip),19," ",STR_PAD_RIGHT);
              $ticket .= str_pad(number_format($precio,2),8," ",STR_PAD_LEFT);
              $ticket .= str_pad(number_format($subtotal,2),8," ",STR_PAD_LEFT).$salto;
            } else {
              $ticket .= $inicio.str_pad("",5," ",STR_PAD_BOTH);
              $ticket .= str_pad(mb_strtoupper($descrip),19," ",STR_PAD_RIGHT).$salto;
            }
          }
          if($descuento >0){
            $ticket .= str_pad("DESC (".number_format($descuento,2,".","")." %)",19," ",STR_PAD_RIGHT);
            $ticket.=$salto;
          }
        }
      }
      $ticket .= $linea;
      $ticket.=$divisor;
      $ticket.="SUMAS       ".str_pad("$".number_format($total,2),9," ",STR_PAD_LEFT).$salto;
      if($factura->descuentoFactura>0){
        $ticket.="DESCUENTO   ".str_pad("$".number_format($factura->descuentoDolarFactura,2),9," ",STR_PAD_LEFT).$salto;
      }
      ////////////////////////////////////////////////////////////////////////////////////
      if($propinaFactura > 0){
        $propinaCalculada = $propinaFactura;
        $ticket.= "PROP. (".$propina."%):".str_pad("$".number_format($propinaCalculada,2),9," ",STR_PAD_LEFT).$salto;
        $total += $propinaCalculada;
        ////////////////////////////////////////////////////////////////////////////////////
      }
      $ticket.="TOTAL       ".str_pad("$".number_format($total-$factura->descuentoFactura,2),9," ",STR_PAD_LEFT).$salto;
      if($factura->efectivoFactura>0){
        $ticket.="EFECTIVO    ".str_pad("$".number_format($factura->efectivoFactura,2),9," ",STR_PAD_LEFT).$salto;
      }
      if($factura->tarjetaFactura>0){
        $ticket.="TARJETA     ".str_pad("$".number_format($factura->tarjetaFactura,2),9," ",STR_PAD_LEFT).$salto;
      }
      if($factura->bitcoinFactura>0){
        $ticket.="BITCOIN     ".str_pad("$".number_format($factura->bitcoinFactura,2),9," ",STR_PAD_LEFT).$salto;
      }
      if($factura->pedidosYaFactura>0){
        $ticket.="PEDIDOS YA  ".str_pad("$".number_format($factura->pedidosYaFactura,2),9," ",STR_PAD_LEFT).$salto;
      }
      if($factura->transferenciaFactura>0){
        $ticket.="TRANSFERENCIA".str_pad("$".number_format($factura->transferenciaFactura,2),9," ",STR_PAD_LEFT).$salto;
      }
      // $ticket.=" EFECTIVO      ".str_pad("$".number_format($factura->efectivoFactura,2),9," ",STR_PAD_LEFT).$salto;
      $ticket.="CAMBIO      ".str_pad("$".number_format($factura->vueltoFactura,2),9," ",STR_PAD_LEFT).$salto;

      $condicion = array("idImpresora" => "2");
      $impresion = TraerUnDato("impresora",$condicion);
      $tipoImpresora = $impresion->tipoImpresora;
      $IpImpresora = $impresion->IpImpresora;
      $recursoCompartidoImpresora = $impresion->recursoCompartidoImpresora;
      $ticket .= $divisor.$tipoImpresora;
      $ticket .= $divisor.$IpImpresora;
      $ticket .= $divisor.$recursoCompartidoImpresora;
      $servidor = $impresion->servidorImpresora;


      $datos["ticket"] = urlencode($ticket);
      $datos["servidor"] = $servidor;
      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = $datos;
    }

    echo json_encode($datosRespuesta);
  }
  function ImprimirVenta($idFactura = ''){
    if ($this->input->method(TRUE) == "POST") {
      $ticket = "";
      $emisor = array(
        'nit' => GblTraerConfiguracionFe('nitEmisor'),
        'nrc' => GblTraerConfiguracionFe('nrcEmisor'),
        'nombre' => GblTraerConfiguracionFe('nombreEmisor'),
        'codigoGiro' => GblTraerConfiguracionFe('codGiroEmisor'),
        'giro' => GblTraerConfiguracionFe('giroEmisor'),
        'nombreComercial' => GblTraerConfiguracionFe('nombreComercialEmisor'),
        'tipoEstablecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
        'departamento' => GblTraerConfiguracionFe('departamentoEmisor'),
        'municipio' => GblTraerConfiguracionFe('municipioEmisor'),
        'complemento' => GblTraerConfiguracionFe('direccionEmisor'),
        'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
        'correo' => GblTraerConfiguracionFe('correoEmisor')
      );
      $departamento = TraerUnDato('FE_CAT_012_Departamento',array("codigo"=>$emisor["departamento"]));
      $emisor["departamentoNombre"] = $departamento->valores;
      $municipio = TraerUnDato('FE_CAT_013_Municipio',array("codigo"=>$emisor["municipio"],"departamento" => $emisor["departamento"]));
      $emisor["municipioNombre"] = $municipio->valores;
      $condicionFac = array("md5(idFactura)" => $idFactura);
      $factura = TraerUnDato("factura",$condicionFac);
      $joinFacDet = array(
        array(
          "tabla" => "producto",
          "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
          "tipo" => "inner",
          "campos" => "producto.nombreProducto"
        ),
      );

      $condicionDatoscli = array('idCliente' => $factura->idCliente);
      $datosCliente = TraerUnDato("cliente", $condicionDatoscli);

      $condicionCajero = array('idUsuario' => $factura->idUsuario);
      $cajero = TraerUnDato("usuario",$condicionCajero);
      /*******************************************************/
      $propina = 0;
      $propinaCalculada = 0;
      $cobroPropina = GblTraerConfiguracion("cobroPropina");
      if($cobroPropina == "Si"){
        $propina = GblTraerConfiguracion("valorPropina");
      }
      $propinaFactura = $factura->propinaFactura;
      /******************************************************/
      $inicio = str_pad(" ",2," ",STR_PAD_BOTH);
      $linea = str_pad("",48,"_",STR_PAD_BOTH);
      $salto = "\n";
      $divisor = "|";
      $espaciosEncabezado = 14;
      $espacios = 44;
      $ticket.=str_pad($emisor["nombreComercial"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      $ticket.=str_pad($emisor["complemento"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      $ticket.=str_pad($emisor["departamentoNombre"].", ".$emisor["municipioNombre"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      if($emisor["telefono"]!=""){
        $ticket.=str_pad("TEL. ".$emisor["telefono"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      if($emisor["nit"] != ""){
        $ticket.=str_pad("NIT. ".$emisor["nit"]."  NRC. ".$emisor["nrc"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      }
      $ticket.=str_pad($emisor["giro"],$espaciosEncabezado," ",STR_PAD_BOTH).$salto;
      $ticket.=$divisor;
      $ticket.=$linea;
      $ticket.=$inicio.str_pad(mb_strtoupper($factura->tipoDocumentoFactura),$espacios," ",STR_PAD_BOTH).$salto;
      $ticket.=$inicio."FECHA: ".str_pad(fecha_d_m_a($factura->fechaFactura)." ".hora($factura->horaFactura),$espacios - 7," ",STR_PAD_RIGHT).$salto;
      $ticket.=$inicio.str_pad("COD. GEN.",$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=$inicio.str_pad($factura->codigoGeneracion,$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=$inicio.str_pad("NUM. CONTROL.",$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=$inicio.str_pad($factura->numeroControl,$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=$inicio.str_pad("SELLO RECEPCION.",$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=$inicio.str_pad($factura->selloRecibido,$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=$linea;
      $ticket.=$salto;
      $ticket.=str_pad("CLIENTE: ".$datosCliente->nombreCliente,$espacios," ",STR_PAD_RIGHT).$salto;
      $ticket.=str_pad("CAJERO: ".$cajero->nombreUsuario,$espacios," ",STR_PAD_RIGHT).$salto;
      // $ticket.=str_pad("VENDEDOR: ".$vendedor->nombreUsuario,$espacios," ",STR_PAD_RIGHT).$salto;
      // $ticket.=$linea;
      // $ticket.=$divisor;
      // $ticket.=$factura->idFactura;
      $ticket.=$divisor;
      $ticket.=$factura->codigoGeneracion;
      $ticket.=$divisor;
      $ticket.=$factura->fechaFactura;
      $ticket.=$divisor;
      $ticket.=GblTraerConfiguracionFe("ambiente");
      $ticket.=$divisor;
      $ticket.=$linea;
      $ticket.=$salto;
      $ticket .= " CANT. ".str_pad("DETALLE",24," ",STR_PAD_RIGHT)."   P.U      SUBT".$salto;
      $total = 0;

      $condicionFacDet = array("pedidoDetalle.idPedido" => $factura->idReferenciaFactura,"estadoPedidoDetalle !="=>"Borrado");
      $facturaDetalle = TraerDatosJoin("pedidoDetalle",$condicionFacDet,"",$joinFacDet);
      // print_r($facturaDetalle);
      $descuento = 0;
      $iva = 0;
      if($facturaDetalle!=null){
        foreach ($facturaDetalle as $detalle) {
          $cantidad = $detalle->cantidadPedidoDetalle;
          if($factura->tipoDocumentoFactura != "FAC"){
            $precioUnitarioMostrar = $detalle->precioPedidoDetalle / (1 + GblTraerConfiguracionFe("iva"));
            $iva += $detalle->precioPedidoDetalle - $precioUnitarioMostrar;
            $descuentoMostrar = 0;//(($fila->precioUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
            $subTotalMostrar = ($cantidad * $precioUnitarioMostrar);// / (1 + GblTraerConfiguracionFe("iva")));// - ($descuentoMostrar * $cantidad);
            // $subTotalMostrar = ($fila->subTotalIvaPedidoDetalle / (1 + GblTraerConfiguracionFe("iva")));// - ($descuentoMostrar * $cantidad);
          } else {
            $precioUnitarioMostrar = $detalle->precioPedidoDetalle;
            $descuentoMostrar = 0;//(($fila->precioIvaUnitarioPedidoDetalle * $fila->descuentoPedidoDetalle)/100);
            $subTotalMostrar = $cantidad * $precioUnitarioMostrar;
            // $subTotalMostrar = ($fila->subTotalIvaPedidoDetalle) - ($descuentoMostrar * $cantidad);
          }
            $total += $subTotalMostrar;
            $arrayNprod = $this->dtl($detalle->nombreProducto,21);
            for ($i=0; $i < count($arrayNprod); $i++) {
              $descrip = $arrayNprod[$i];
              if($i == 0){
                $ticket .= str_pad(round($cantidad,2),7," ",STR_PAD_BOTH);
                $ticket .= str_pad(mb_strtoupper($descrip),21," ",STR_PAD_RIGHT);
                $ticket .= str_pad(number_format($precioUnitarioMostrar,2),10," ",STR_PAD_LEFT);
                $ticket .= str_pad(number_format($subTotalMostrar,2),10," ",STR_PAD_LEFT);
                $ticket .= $salto;
              } else {
                $ticket .= $inicio.str_pad("",5," ",STR_PAD_BOTH);
                $ticket .= str_pad(mb_strtoupper($descrip),21," ",STR_PAD_RIGHT);
                $ticket .= $salto;
              }
            }
            if($descuento >0){
              $ticket .= str_pad("DESC (".number_format($descuento,2)." %)",18," ",STR_PAD_RIGHT);
              $ticket.=$salto;
            }
          }
      }
      $ticket .= $linea;
      $ticket.=$divisor;

      if($factura->tipoDocumentoFactura == 'CCF')
      {
        $ticket.=" SUMAS         ".str_pad("$".number_format($total,2),9," ",STR_PAD_LEFT).$salto;
        if($factura->descuentoFactura>0){
          $ticket.=" DESCUENTO     ".str_pad("$".number_format($factura->descuentoDolarFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        $ticket.=" IVA           ".str_pad("$".number_format($iva,2),9," ",STR_PAD_LEFT).$salto;
        ////////////////////////////////////////////////////////////////////////////////////
        $ticket.=" SUBTOTAL      ".str_pad("$".number_format($total+$iva,2),9," ",STR_PAD_LEFT).$salto;
        if($propinaFactura > 0){
          $propinaCalculada = $propinaFactura;
          $ticket.= " PROPINA (".$propina."%):".str_pad("$".number_format($propinaCalculada,2),9," ",STR_PAD_LEFT).$salto;
          $total += $propinaCalculada;
          ////////////////////////////////////////////////////////////////////////////////////
        }
        $ticket.=" TOTAL         ".str_pad("$".number_format($total+$iva,2),9," ",STR_PAD_LEFT).$salto;
        if($factura->efectivoFactura>0){
          $ticket.=" EFECTIVO      ".str_pad("$".number_format($factura->efectivoFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->tarjetaFactura>0){
          $ticket.=" TARJETA       ".str_pad("$".number_format($factura->tarjetaFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->bitcoinFactura>0){
          $ticket.=" BITCOIN       ".str_pad("$".number_format($factura->bitcoinFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->pedidosYaFactura>0){
          $ticket.=" PEDIDOS YA    ".str_pad("$".number_format($factura->pedidosYaFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->transferenciaFactura>0){
          $ticket.=" TRANSFERENCIA ".str_pad("$".number_format($factura->transferenciaFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        // $ticket.=" EFECTIVO ".str_pad("$".number_format($factura->efectivoFactura,2),9," ",STR_PAD_LEFT).$salto;
        $ticket.=" CAMBIO        ".str_pad("$".number_format($factura->vueltoFactura,2),9," ",STR_PAD_LEFT).$salto;
        $condicion = array("idImpresora" => "2");
        $impresion = TraerUnDato("impresora",$condicion);
        $tipoImpresora = $impresion->tipoImpresora;
        $IpImpresora = $impresion->IpImpresora;
        $recursoCompartidoImpresora = $impresion->recursoCompartidoImpresora;
        $ticket .= $divisor.$tipoImpresora;
        $ticket .= $divisor.$IpImpresora;
        $ticket .= $divisor.$recursoCompartidoImpresora;
        $servidor = $impresion->servidorImpresora;
        $datos["ticket"] = urlencode($ticket);
        $datos["servidor"] = $servidor;
        $datosRespuesta["codigo"] = 200;
        $datosRespuesta["datos"] = $datos;
      }
      else {
        $ticket.=" SUMAS         ".str_pad("$".number_format($total,2),9," ",STR_PAD_LEFT).$salto;
        if($factura->descuentoFactura>0){
          $ticket.=" DESCUENTO     ".str_pad("$".number_format($factura->descuentoFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        ////////////////////////////////////////////////////////////////////////////////////
        if($propinaFactura > 0){
          $propinaCalculada = $propinaFactura;
          $ticket.= " PROPINA (".$propina."%):".str_pad("$".number_format($propinaCalculada,2),9," ",STR_PAD_LEFT).$salto;
          $total += $propinaCalculada;
          ////////////////////////////////////////////////////////////////////////////////////
        }
        $ticket.=" TOTAL         ".str_pad("$".number_format($total-$factura->descuentoFactura,2),9," ",STR_PAD_LEFT).$salto;
        if($factura->efectivoFactura>0){
          $ticket.=" EFECTIVO      ".str_pad("$".number_format($factura->efectivoFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->tarjetaFactura>0){
          $ticket.=" TARJETA       ".str_pad("$".number_format($factura->tarjetaFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->bitcoinFactura>0){
          $ticket.=" BITCOIN       ".str_pad("$".number_format($factura->bitcoinFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->pedidosYaFactura>0){
          $ticket.=" PEDIDOS YA    ".str_pad("$".number_format($factura->pedidosYaFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        if($factura->transferenciaFactura>0){
          $ticket.=" TRANSFERENCIA ".str_pad("$".number_format($factura->transferenciaFactura,2),9," ",STR_PAD_LEFT).$salto;
        }
        // $ticket.=" EFECTIVO ".str_pad("$".number_format($factura->efectivoFactura,2),9," ",STR_PAD_LEFT).$salto;
        $ticket.=" CAMBIO        ".str_pad("$".number_format($factura->vueltoFactura,2),9," ",STR_PAD_LEFT).$salto;
        $condicion = array("idImpresora" => "2");
        $impresion = TraerUnDato("impresora",$condicion);
        $tipoImpresora = $impresion->tipoImpresora;
        $IpImpresora = $impresion->IpImpresora;
        $recursoCompartidoImpresora = $impresion->recursoCompartidoImpresora;
        $ticket .= $divisor.$tipoImpresora;
        $ticket .= $divisor.$IpImpresora;
        $ticket .= $divisor.$recursoCompartidoImpresora;
        $servidor = $impresion->servidorImpresora;
        $datos["ticket"] = urlencode($ticket);
        $datos["servidor"] = $servidor;
        $datosRespuesta["codigo"] = 200;
        $datosRespuesta["datos"] = $datos;
      }


    }

    echo json_encode($datosRespuesta);
  }
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  function ImprimirTicketServicio($idFactura = ''){
    if ($this->input->method(TRUE) == "POST") {

      $condicionFac = array("idFactura" => $idFactura);
      $factura = TraerUnDato("factura",$condicionFac);
      $joinFacDet = array(
        array(
          "tabla" => "senoritaServicio as senSer",
          "condicion" =>"facturaDetalle.idFacturaDetalle = senSer.idFacturaSenoritaServicio",
          "tipo" => "inner",
          "campos" => "senSer.idSenorita"
        ),
        array(
          "tabla" => "senorita as sen",
          "condicion" =>"senSer.idSenorita = sen.idSenorita",
          "tipo" => "inner",
          "campos" => "sen.apodoSenorita, sen.idSenoritaCategoria"
        ),
        array(
          "tabla" => "senoritaCategoria as senCat",
          "condicion" =>"sen.idSenoritaCategoria = senCat.idSenoritaCategoria",
          "tipo" => "inner",
          "campos" => "senCat.nombreSenoritaCategoria"
        ),
        array(
          "tabla" => "servicio as s",
          "condicion" =>"s.idServicio = facturaDetalle.idServicio",
          "tipo" => "inner",
          "campos" => "s.tiempoServicio, s.idServicioCategoria"
        ),
        array(
          "tabla" => "servicioDetalle as sd",
          "condicion" =>"s.idServicio = sd.idServicio AND sd.idSenoritaCategoriaServicioDetalle = senCat.idSenoritaCategoria",
          "tipo" => "inner",
          "campos" => "sd.porcentajeSenoritaServicioDetalle"
        ),
        array(
          "tabla" => "servicioCategoria as sc",
          "condicion" =>"s.idServicioCategoria = sc.idServicioCategoria",
          "tipo" => "inner",
          "campos" => "sc.nombreServicioCategoria, sc.idServicioCategoria"
        ),
      );

      $condicionFacDet = array("facturaDetalle.idFactura" => $idFactura,
      "estadoServicioDetalle "=>"Activo");
      $facturaDetalle = TraerDatosJoin("facturaDetalle",$condicionFacDet,'',$joinFacDet);

      $datos = array(
        "idFactura" => $idFactura,
        "idCliente" => $factura->idCliente,
        "cliente"   => $factura->nombreFactura,
        "idCaja"    => $factura->idCajaFactura,
        "tipoDoc"   => $factura->tipoDocumentoFactura,
        "correlativo"=> $factura->numeroDocumentoFactura,
        "total"     => $factura->totalFactura,
        "vuelto"    => $factura->vueltoFactura,
        "efectivo"  => $factura->efectivoFactura,
      );

      $servicios =  array();

      $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
      $linea = str_pad("",42,"_",STR_PAD_BOTH);
      $divisor = "|";
      $salto = "\n";
      $espacioCant = 5;
      $espacioDes = 32;
      $espacioTot = 10;
      for ($i=0; $i < 3; $i++) {
        $espacios = 19;
        $relleno = $espacios - strlen("COMPROBANTE DE SERVICIO");
        $servicio = $salto.$espacioInicio.str_pad("COMPROBANTE DE SERVICIO",$relleno," ",STR_PAD_BOTH).$salto;
        $persona = ["CLIENTE","CASA","SEÑORITA"];
        $relleno = $espacios - strlen($persona[$i]);
        $servicio .= $salto.$espacioInicio.str_pad($persona[$i],$relleno," ",STR_PAD_BOTH).$salto.$divisor;
        $espacios = 35;
        $servicio .= $linea.$salto;
        $servicio .= $espacioInicio."CANT. DETALLE                   SUBTOTAL".$salto;
        $servicio .= $linea.$salto;

        foreach($facturaDetalle  as $det){
          $relleno = "";
          $cantidad = $det->cantidadFacturaDetalle;
          $porcentaje = $det->porcentajeSenoritaServicioDetalle;
          if($i == 0){
            $descripcion = $det->nombreServicioCategoria." (".$det->tiempoServicio."MIN ".$det->nombreSenoritaCategoria.") ".$det->apodoSenorita;
            $monto = $det->subTotalFacturaDetalle;
          }
          if($i == 1){
            $descripcion = $det->tiempoServicio."MIN ".$det->nombreSenoritaCategoria." ".$det->apodoSenorita." (".number_format((100-$porcentaje),2)."%)";
            $monto = $det->subTotalFacturaDetalle;
            $monto = number_format(($monto * (100-$porcentaje) /100),2);
          }
          if($i == 2){
            $descripcion = $det->tiempoServicio."MIN ".$det->nombreSenoritaCategoria.") ".$det->apodoSenorita." (".number_format(($porcentaje),2)."%)";
            $monto = $det->subTotalFacturaDetalle;
            $monto = number_format(($monto * ($porcentaje) /100),2);
          }
          $relleno = $espacioCant - strlen($cantidad);
          $servicio .= $espacioInicio.str_pad($cantidad,$relleno," ",STR_PAD_RIGHT);

          $relleno = $espacioDes - strlen($descripcion);
          $servicio .= $espacioInicio.str_pad($descripcion,$relleno," ",STR_PAD_RIGHT);

          $relleno = $espacioTot - strlen($monto);
          $servicio .= $espacioInicio.str_pad($monto,$relleno," ",STR_PAD_LEFT).$salto;
        }
        if($i == 0){
          $servicio .=  $linea.$salto;

          $relleno = $espacios - strlen("TOTAL: $".number_format($factura->totalFactura,2));
          $servicio .= $espacioInicio.str_pad("TOTAL: $".number_format($factura->totalFactura,2),$relleno," ",STR_PAD_RIGHT).$salto;

          $relleno = $espacios - strlen("EFECTIVO: $".number_format($factura->efectivoFactura,2));
          $servicio .= $espacioInicio.str_pad("EFECTIVO: $".number_format($factura->efectivoFactura,2),$relleno," ",STR_PAD_RIGHT).$salto;

          $relleno = $espacios - strlen("CAMBIO: $".number_format($factura->vueltoFactura,2));
          $servicio .= $espacioInicio.str_pad("CAMBIO: $".number_format($factura->vueltoFactura,2),$relleno," ",STR_PAD_RIGHT).$salto;
        }
        array_push($servicios,$servicio);
      }


      $datos['servicio'] = json_encode($servicios);

      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = urlencode($datos);
    }

    echo json_encode($datosRespuesta);
  }
  function ImprimirCorteFiscal(){
    if ($this->input->method(TRUE) == "POST") {
      $idCorte = $this->input->post("idCorte");
      $tipo = $this->input->post("tipo");
      $impresora = $this->input->post("impresora");
      $join = array(
        array(
          "tabla" => "usuario",
          "condicion" =>"corteHistorial.idUsuarioCorteHistorial = usuario.idUsuario",
          "tipo" => "inner",
          "campos" => "usuario.nombreUsuario"
        ),
        array(
          "tabla" => "corteTurno",
          "condicion" =>"corteHistorial.idTurnoCorteHistorial = corteTurno.idTurno",
          "tipo" => "inner",
          "campos" => "corteTurno.corteTurno,corteTurno.idTurno"
        ),
        array(
          "tabla" => "corteCaja",
          "condicion" =>"corteCaja.idCorteCaja = corteTurno.idCorte",
          "tipo" => "inner",
          "campos" => "corteCaja.idCorteCaja, idTurnoVigente"
        ),
        array(
          "tabla" => "caja",
          "condicion" =>"corteCaja.idCaja = caja.idCaja",
          "tipo" => "inner",
          "campos" => "caja.nombreCaja, caja.impresoraCaja"
        ),
        array(
          "tabla" => "documento",
          "condicion" => "documento.aliasDocumento = 'TIK'",
          "tipo" => "left",
          "campos" => "idDocumento"
        ),
        array(
          "tabla" => "cajaDocumento",
          "condicion" => "cajaDocumento.idDocumentoCajaDocumento = documento.idDocumento AND cajaDocumento.idCajaCajaDocumento = caja.idCaja",
          "tipo" => "left",
          "campos" => "numeroResolucionCajaDocumento,inicioCajaDocumento,finalCajaDocumento,fechaResolucionCajaDocumento,numeroResolucionCajaDocumento,serieCajaDocumento",
        ),
      );
      $condicion = array("md5(idCorteHistorial)" => $idCorte);
      $corte = TraerUnDatoJoin("corteHistorial",$condicion, $join);
      // print_r($corte);
      $impresoraCaja = $corte->impresoraCaja;
      $totalEfectivo = $corte->totalCorteEfectivo;
      $totalTarjeta = $corte->totalCorteTarjeta;
      $totalBitcoin = $corte->totalCorteBitcoin;
	  $totalTransferencia = $corte->totalCorteTransferencia;
      $totalPedidosYa = $corte->totalCortePedidosYa;
      $nombre = $corte->nombreUsuario;
      $tipoCorte = $corte->tipoCorteHistorial;
      $montoApertura = $corte->montoAperturaTurnoCorteHistorial;
      $total = $corte->totalCorteHistorial;
      $diferencia = $corte->diferenciaTurnoCorteHistorial;
      $fecha = fecha_d_m_a(substr($corte->fechaCorteHistorial, 0, -9));
      $hora = hora(substr($corte->fechaCorteHistorial, 10, 8));
      $turno = $corte->corteTurno;
      $caja = $corte->nombreCaja;

      $movimientos = TraerDatos("cajaMovimiento",array("idTurnoCajaMovimiento"=> $corte->idTurnoVigente));
      $entrada = 0;
      $salida = 0;
      if($movimientos){
        foreach($movimientos as $m){
          if($m->tipoCajaMovimiento == "Entrada"){
            $entrada +=$m->montoCajaMovimiento;
          }
          if($m->tipoCajaMovimiento == "Salida"){
            $salida +=$m->montoCajaMovimiento;
          }
        }
      }

      ///////////////////////////////////
      //encabezado
      ///////////////////////////////////
      $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
      $linea = str_pad("",42,"_",STR_PAD_BOTH);
      $salto = "\n";
      $divisor = "|";
      $espacios = 19;
      if($tipoCorte == "C"){
        $relleno = $espacios - strlen("CORTE ".$tipoCorte);
        $corteFormato = $salto.$espacioInicio.str_pad("CORTE ".$tipoCorte,$relleno," ",STR_PAD_BOTH).$salto.$divisor;
      }else{

        $nombreEmpresa = GblTraerConfiguracion("nombreEmpresa");
        $nombrePatrono = GblTraerConfiguracion("razonSocialEmpresa");
        $direccionEmpresa = GblTraerConfiguracion("direccionEmpresa");
        $telefonoEmpresa = GblTraerConfiguracion("telefonoEmpresa");
        $nitEmpresa = GblTraerConfiguracion("nitEmpresa");
        $nrcEmpresa = GblTraerConfiguracion("nrcEmpresa");
        $giroEmpresa = GblTraerConfiguracion("giroEmpresa");

        // $condicionDoc = array();
        // $joinDoc = array(
        //   array(

        //   ),
        // );
        // $factura = TraerUnDatoJoin("cajaDocumento",$condicionDoc,$joinDoc);
        $direccion = wordwrap($direccionEmpresa,38,$salto." ");
        $espacios = 35;
        $relleno = $espacios - strlen($nombreEmpresa);
        $corteFormato = $espacioInicio.str_pad($nombreEmpresa,$relleno," ",STR_PAD_BOTH);
        //$relleno = $espacios - strlen($nombrePatrono);
        //$corteFormato .= $salto.$espacioInicio.str_pad($nombrePatrono,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen($direccion);
        $corteFormato .= $salto.$espacioInicio.str_pad($direccion,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("TEL: ".$telefonoEmpresa);
        $corteFormato .= $salto.$espacioInicio.str_pad("TEL: ".$telefonoEmpresa,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("NIT: ".$nitEmpresa." NRC: ".$nrcEmpresa);
        $corteFormato .= $salto.$espacioInicio.str_pad("NIT: ".$nitEmpresa." NRC: ".$nrcEmpresa,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("GIRO: ".$giroEmpresa);
        $corteFormato .= $salto.$espacioInicio.str_pad("GIRO: ".$giroEmpresa,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("RESOLUCION N. ".$corte->numeroResolucionCajaDocumento);
        $corteFormato .= $salto.$espacioInicio.str_pad("RESOLUCION N. ".$corte->numeroResolucionCajaDocumento,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("FECHA RESOLUCION. ".Fecha_D_M_A($corte->fechaResolucionCajaDocumento));
        $corteFormato .= $salto.$espacioInicio.str_pad("FECHA RESOLUCION. ".Fecha_D_M_A($corte->fechaResolucionCajaDocumento),$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("SERIE. ".$corte->serieCajaDocumento);
        $corteFormato .= $salto.$espacioInicio.str_pad("SERIE. ".$corte->serieCajaDocumento,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("DE: ".$corte->inicioCajaDocumento." AL: ".$corte->finalCajaDocumento);
        $corteFormato .= $salto.$espacioInicio.str_pad("DE: ".$corte->inicioCajaDocumento." AL: ".$corte->finalCajaDocumento,$relleno," ",STR_PAD_BOTH);
        $relleno = $espacios - strlen("CORTE ".$tipoCorte);
        $corteFormato .= $salto.$espacioInicio.str_pad("CORTE ".$tipoCorte,$relleno," ",STR_PAD_BOTH);

        $corteFormato .= $salto.$linea.$salto;

        $corteFormato .= $divisor;
      }


      $espacios = 35;
      $relleno = $espacios - strlen("CAJERO: ".$nombre);
      $corteFormato .= $espacioInicio.str_pad("CAJERO: ".$nombre, $relleno," ",STR_PAD_RIGHT).$salto;
      $relleno = $espacios - strlen("FECHA: ".$fecha."    HORA: ".$hora);
      $corteFormato .= $espacioInicio.str_pad("FECHA : ".$fecha."    HORA : ".$hora,$relleno," ",STR_PAD_RIGHT).$salto;
      $relleno = $espacios - strlen("CAJA: ".str_pad($caja,11,' ',STR_PAD_RIGHT)."    TURNO: ".$turno);
      $corteFormato .= $espacioInicio.str_pad("CAJA  : ".str_pad($caja,10,' ',STR_PAD_RIGHT)."    TURNO: ".$turno,$relleno," ",STR_PAD_RIGHT).$salto;
      $corteFormato .= $linea.$salto;
      ///////////////////////////////////
      //Formato
      ///////////////////////////////////
      if($tipoCorte == "C"){

        // $corteFormato .= $salto;
        $descuentos = 0;
        $movimientos = TraerDatos("factura",array("idTurno"=> $corte->idTurnoVigente,"estadoFactura"=>"Cobrado"));
        if($movimientos){
          foreach($movimientos as $m){
            $descuentos +=$m->descuentoDolarFactura;
          }
        }
        $venta = $total - $montoApertura - $entrada + $salida;
        $relleno = $espacios - strlen("VENTA: ");
        $corteFormato .= $espacioInicio."VENTA: ".str_pad("$".number_format($venta + $descuentos,2),$relleno," ",STR_PAD_LEFT).$salto;
        // $corteFormato .= $salto;
        $total = $venta + $entrada - $salida;

        $relleno = $espacios - strlen("- DESCUENTOS: ");
        $corteFormato .= $espacioInicio."- DESCUENTOS: ".str_pad("$".number_format($descuentos,2),$relleno," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $linea.$salto;
        $relleno = $espacios - strlen("TOTAL VENTAS: ");
        $corteFormato .= $espacioInicio."TOTAL VENTAS: ".str_pad("$".number_format($venta ,2),$relleno," ",STR_PAD_LEFT).$salto;
        $relleno = $espacios - strlen("+ ENTRADAS: ");
        $corteFormato .= $espacioInicio."+ ENTRADAS: ".str_pad("$".number_format($entrada,2),$relleno," ",STR_PAD_LEFT).$salto;
        $relleno = $espacios - strlen("- SALIDAS: ");
        $corteFormato .= $espacioInicio."- SALIDAS: ".str_pad("$".number_format($salida,2),$relleno," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $linea.$salto;
        $relleno = $espacios - strlen("TOTAL GANANCIA: ");
        $corteFormato .= $espacioInicio."TOTAL GANANCIA: ".str_pad("$".number_format($total,2),$relleno," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $salto;
        $corteFormato .= $salto;

        $corteFormato .= $linea.$salto;
        $relleno = $espacios - strlen("TOTAL GANANCIA: ");
        $corteFormato .= $espacioInicio."TOTAL GANANCIA: ".str_pad("$".number_format($total,2),$relleno," ",STR_PAD_LEFT).$salto;
        $relleno = $espacios - strlen("+ MONTO APERTURA: ");
        $corteFormato .= $espacioInicio."+ MONTO APERTURA: ".str_pad("$".number_format($montoApertura,2),$relleno," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $linea.$salto;
        $relleno = $espacios - strlen("TOTAL CAJA: ");
        $corteFormato .= $espacioInicio."TOTAL CAJA: ".str_pad("$".number_format($total + $montoApertura,2),$relleno," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $salto;
        $corteFormato .= $salto;
        $relleno = $espacios - strlen("EFECTIVO EN CAJA: ");
        $corteFormato .= $espacioInicio."EFECTIVO EN CAJA: ".str_pad("$".number_format($total + $montoApertura + $diferencia,2),$relleno," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $linea.$salto;

        $relleno = $espacios - strlen("DIFERENCIA: ");
        $corteFormato .= $espacioInicio."DIFERENCIA: ".str_pad("$".number_format($diferencia ,2),$relleno," ",STR_PAD_LEFT).$salto;
        // $corteFormato .= $salto;
        $corteFormato .= $linea.$salto;

        $corteFormato .= $salto;
        $corteFormato .= $salto;
      }else{
        $totalT = $corte->ticketTotalCorteHistorial;
        $totalF = $corte->facturaTotalCorteHistorial;
        $totalC = $corte->facturaTotalCorteHistorial;

        $tini = $corte->ticketInicioCorteHistorial;
        $tfin = $corte->ticketFinalCorteHistorial;
        $fini = $corte->facturaInicioCorteHistorial;
        $ffin = $corte->facturaFinalCorteHistorial;
        $cini = $corte->creditoFiscalInicioCorteHistorial;
        $cfin = $corte->creditoFiscalFinalCorteHistorial;
        $totalTC = $corte->ticketCorteHistorial;
        $totalFC = $corte->facturaCorteHistorial;
        $totalCC = $corte->creditoFiscalCorteHistorial;


        $condicion = array("md5(idCorteHistorial)" => $idCorte, 'tipoCorteDocumento' => 'FAC');
        $fac = TraerUnDato("corteHistorialDocumento",$condicion);
        if($fac){
          $totalFC = $fac->totalNumeroDocumento;
          $fini = $fac->inicioDocumento;
          $ffin = $fac->finDocumento;
          $totalF = $fac->totalDocumento;
          $totalDF = $fac->totalDescuentoDocumento;
        } else {
          $totalFC = "0";
          $fini = "0";
          $ffin = "0";
          $totalF = 0;
          $totalDF = 0;
        }


        $condicion = array("md5(idCorteHistorial)" => $idCorte, 'tipoCorteDocumento' => 'CCF');
        $ccf = TraerUnDato("corteHistorialDocumento",$condicion);
        if($ccf){
          $totalCC = $ccf->totalNumeroDocumento;
          $cini = $ccf->inicioDocumento;
          $cfin = $ccf->finDocumento;
          $totalC = $ccf->totalDocumento;
          $totalDC = $ccf->totalDescuentoDocumento;
        } else {
          $totalCC = "0";
          $cini = "0";
          $cfin = "0";
          $totalC = 0;
          $totalDC = 0;
        }

        $condicion = array("md5(idCorteHistorial)" => $idCorte, 'tipoCorteDocumento' => 'TIK');
        $tik = TraerUnDato("corteHistorialDocumento",$condicion);
        if($tik){
          $totalTC = $tik->totalNumeroDocumento;
          $tini = $tik->inicioDocumento;
          $tfin = $tik->finDocumento;
          $totalT = $tik->totalDocumento;
          $totalDT = $tik->totalDescuentoDocumento;
        } else {
          $totalTC = "0";
          $tini = "0";
          $tfin = "0";
          $totalT = 0;
          $totalDT = 0;
        }

        $corteFormato .= $espacioInicio."              INICIO    FINAL    TOTAL".$salto;
        $corteFormato .= $espacioInicio.str_pad("TIQUETES: ",15," ",STR_PAD_RIGHT).str_pad($tini,5," ",STR_PAD_LEFT).str_pad("",3," ",STR_PAD_RIGHT).str_pad($tfin,6," ",STR_PAD_LEFT).str_pad("",4," ",STR_PAD_RIGHT).str_pad($totalTC,5," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $espacioInicio.str_pad("FACTURAS: ",15," ",STR_PAD_RIGHT).str_pad($fini,5," ",STR_PAD_LEFT).str_pad("",3," ",STR_PAD_RIGHT).str_pad($ffin,6," ",STR_PAD_LEFT).str_pad("",4," ",STR_PAD_RIGHT).str_pad($totalFC,5," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $espacioInicio.str_pad("FISCALES: ",15," ",STR_PAD_RIGHT).str_pad($cini,5," ",STR_PAD_LEFT).str_pad("",3," ",STR_PAD_RIGHT).str_pad($cfin,6," ",STR_PAD_LEFT).str_pad("",4," ",STR_PAD_RIGHT).str_pad($totalCC,5," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $linea.$salto;
        $corteFormato .= $espacioInicio."TOTAL: ".str_pad($totalCC+$totalFC+$totalTC,31," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $salto;
        $corteFormato .= $salto;

        $corteFormato .= $espacioInicio."           EXENTO      GRAVADO      TOTAL".$salto;
        $corteFormato .= $espacioInicio.str_pad("TIQUETES: ",10," ",STR_PAD_RIGHT).str_pad("$0.00",7," ",STR_PAD_LEFT).str_pad("",5," ",STR_PAD_RIGHT).str_pad("$".number_format($totalT - $totalDT,2),8," ",STR_PAD_LEFT).str_pad("",4," ",STR_PAD_RIGHT).str_pad("$".number_format($totalT ,2),7," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $espacioInicio.str_pad("FACTURAS: ",10," ",STR_PAD_RIGHT).str_pad("$0.00",7," ",STR_PAD_LEFT).str_pad("",5," ",STR_PAD_RIGHT).str_pad("$".number_format($totalF - $totalDF,2),8," ",STR_PAD_LEFT).str_pad("",4," ",STR_PAD_RIGHT).str_pad("$".number_format($totalF ,2),7," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $espacioInicio.str_pad("FISCALES: ",10," ",STR_PAD_RIGHT).str_pad("$0.00",7," ",STR_PAD_LEFT).str_pad("",5," ",STR_PAD_RIGHT).str_pad("$".number_format($totalC - $totalDC,2),8," ",STR_PAD_LEFT).str_pad("",4," ",STR_PAD_RIGHT).str_pad("$".number_format($totalC ,2),7," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $linea.$salto;
        $corteFormato .= $espacioInicio.str_pad("TOTAL: ",10," ",STR_PAD_RIGHT).str_pad("$0.00",7," ",STR_PAD_LEFT).str_pad("",5," ",STR_PAD_RIGHT).str_pad("$".number_format(($totalC + $totalF + $totalT ) - ($totalDC + $totalDF + $totalDT) ,2),8," ",STR_PAD_LEFT).str_pad("",4," ",STR_PAD_RIGHT).str_pad("$".number_format(($totalC + $totalF + $totalT ),2),7," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $salto;
        $corteFormato .= $salto;

        $movimientos = TraerDatos("factura",array("idTurno"=> $corte->idTurnoVigente,"estadoFactura"=>"Cobrado"));
        $descuentos = 0;
        if($movimientos){
          foreach($movimientos as $m){
            $descuentos +=$m->descuentoDolarFactura;
          }
        }
        $corteFormato .= $linea.$salto;
        $corteFormato .= $espacioInicio.str_pad("TOTAL  DESCUENTO : $ ",31," ",STR_PAD_LEFT);
        $corteFormato .= $espacioInicio.str_pad($descuentos,31," ",STR_PAD_RIGHT).$salto;


        $corteFormato .= $linea.$salto;
        $corteFormato .= $espacioInicio.str_pad("TOTALES SEGUN TIPO DE PAGO",31," ",STR_PAD_BOTH).$salto;
        $corteFormato .= $linea.$salto;

        $espacios = 35;
        $relleno = $espacios - strlen("TOTAL EFECTIVO  : ");
        $corteFormato .= $espacioInicio."TOTAL EFECTIVO  : ".str_pad("$".number_format($totalEfectivo,2),$relleno," ",STR_PAD_LEFT).$salto;
        $relleno = $espacios - strlen("TOTAL TRANSF.   : ");
        $corteFormato .= $espacioInicio."TOTAL TRANSF.   : ".str_pad("$".number_format($totalTransferencia,2),$relleno," ",STR_PAD_LEFT).$salto;
        $relleno = $espacios - strlen("TOTAL TARJETA   : ");
        $corteFormato .= $espacioInicio."TOTAL TARJETA   : ".str_pad("$".number_format($totalTarjeta,2),$relleno," ",STR_PAD_LEFT).$salto;
        $relleno = $espacios - strlen("TOTAL BITCOIN   : ");
        $corteFormato .= $espacioInicio."TOTAL BITCOIN   : ".str_pad("$".number_format($totalBitcoin,2),$relleno," ",STR_PAD_LEFT).$salto;
        $relleno = $espacios - strlen("TOTAL PEDIDOS YA: ");
        $corteFormato .= $espacioInicio."TOTAL PEDIDOS YA: ".str_pad("$".number_format($totalPedidosYa,2),$relleno," ",STR_PAD_LEFT).$salto;
        $corteFormato .= $salto;
        $corteFormato .= $salto;

        //TOTALES DE EFECTIVO, TARJETA, BITCOIN, PEDIDOS YA



      }
      ///////////////////////////////////
      //Eleccion de la ip de la impresora
      ///////////////////////////////////
      if($impresora == ""){
        $impresora = md5($impresoraCaja);
      }
      $condicion = array("md5(idImpresora)" => $impresora);
      $impresion = TraerUnDato("impresora",$condicion);
      $tipoImpresora = $impresion->tipoImpresora;
      $IpImpresora = $impresion->IpImpresora;
      $recursoCompartidoImpresora = $impresion->recursoCompartidoImpresora;
      $corteFormato .= $divisor.$tipoImpresora;
      $corteFormato .= $divisor.$IpImpresora;
      $corteFormato .= $divisor.$recursoCompartidoImpresora;
      $servidor = $impresion->servidorImpresora;

      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = urlencode($corteFormato);
      $datosRespuesta["tipo"] = $tipoCorte;
      $datosRespuesta["servidor"] = $servidor;
    }

    echo json_encode($datosRespuesta);
  }
  function ImprimirCorte(){
    if ($this->input->method(TRUE) == "POST") {
      $idCorteCaja = $this->input->post("idCorteCaja");
      $efectivo = $this->input->post("efectivo");
      $diferencia = $this->input->post("diferencia");
      $total = $this->input->post("total");

      $condicion = array("idCorteCaja" => $idCorteCaja);
      $corte = TraerUnDato("corteCaja",$condicion);
      $local = TraerUnDatoIndividual("pedidoDetalle","SUM(cantidadPedidoDetalle*precioPedidoDetalle) as dato",array("idCorte" => $idCorteCaja, "tipoPedido" => "Producto"))[0]["dato"];
      $senorita = TraerUnDatoIndividual("pedidoDetalle","SUM(cantidadPedidoDetalle*precioPedidoDetalle) as dato",array("idCorte" => $idCorteCaja, "tipoPedido" => "Producto Especial"))[0]["dato"];
      $servicios = TraerUnDatoIndividual("factura","SUM(totalFactura) as dato",array("idCorte" => $idCorteCaja, "tipoFactura" => "Servicio"))[0]["dato"];
      $propina = TraerUnDatoIndividual("factura","SUM(propinaFactura) as dato",array("idCorte" => $idCorteCaja))[0]["dato"];
      $nombre = TraerUnDatoIndividual("usuario","nombreUsuario",array("idUsuario" => $corte->idUsuarioCorte))[0]["nombreUsuario"];

      $montoApertura = $corte->montoApertura;
      $fecha = $corte->fechaCorte;
      $subtotal = $local + $propina;
      $totalventa = $subtotal + $senorita;
      $total = $totalventa + $servicios + $montoApertura;
      $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
      $linea = str_pad("",42,"_",STR_PAD_BOTH);
      $salto = "\n";
      $divisor = "|";
      $espacios = 19;
      $relleno = $espacios - strlen("CORTE DE CAJA");
      $corteFormato = $salto.$espacioInicio.str_pad("CORTE DE CAJA",$relleno," ",STR_PAD_RIGHT).$salto.$divisor;
      //$corteFormato .= $salto;
      $espacios = 35;
      $relleno = $espacios - strlen("CAJERO: ".$nombre);
      $corteFormato .= $espacioInicio.str_pad("CAJERO: ".$nombre, $relleno," ",STR_PAD_RIGHT).$salto;
      $relleno = $espacios - strlen("FECHA: ".$fecha);
      $corteFormato .= $espacioInicio.str_pad("FECHA: ".$fecha,$relleno," ",STR_PAD_RIGHT).$salto;
      $corteFormato .= $linea.$salto;
      $relleno = $espacios - strlen("MONTO APERTURA: ");
      $corteFormato .= $espacioInicio."MONTO APERTURA: ".str_pad("$".number_format($montoApertura,2),$relleno," ",STR_PAD_LEFT).$salto;
      $corteFormato .= $salto;
      $relleno = $espacios - strlen("TOTAL LOCAL: ");
      $corteFormato .= $espacioInicio."TOTAL LOCAL: ".str_pad("$".number_format($local,2),$relleno," ",STR_PAD_LEFT).$salto;
      $corteFormato .= $salto;
      $relleno = $espacios - strlen("PROPINA: ");
      $corteFormato .= $espacioInicio."PROPINA: ".str_pad("$".number_format($propina,2),$relleno," ",STR_PAD_LEFT).$salto;
      $corteFormato .= $salto;
      $relleno = $espacios - strlen("TOTAL SENORITAS: ");
      $corteFormato .= $espacioInicio."TOTAL SEÑORITAS: ".str_pad("$".number_format($senorita,2),$relleno," ",STR_PAD_LEFT).$salto;
      $corteFormato .= $salto;
      $relleno = $espacios - strlen("TOTAL SERVICIOS: ");
      $corteFormato .= $espacioInicio."TOTAL SERVICIOS: ".str_pad("$".number_format($servicios,2),$relleno," ",STR_PAD_LEFT).$salto;
      $corteFormato .= $linea.$salto;
      $relleno = $espacios - strlen("TOTAL: ");
      $corteFormato .= $espacioInicio."TOTAL: ".str_pad("$".number_format($total,2),$relleno," ",STR_PAD_LEFT).$salto;
      $corteFormato .= $linea.$salto;
      $relleno = $espacios - strlen("EFECTIVO EN CAJA: ");
      $corteFormato .= $espacioInicio."EFECTIVO EN CAJA: ".str_pad("$".number_format($efectivo,2),$relleno," ",STR_PAD_LEFT).$salto;
      $relleno = $espacios - strlen("DIFERENCIA: ");
      $corteFormato .= $espacioInicio."DIFERENCIA: ".str_pad("$".number_format($diferencia,2),$relleno," ",STR_PAD_LEFT).$salto;
      $corteFormato .= $linea.$salto;
      $corteFormato .= $salto;
      $corteFormato .= $salto;
      $corteFormato .= $salto;

      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = urlencode($corteFormato);
    }

    echo json_encode($datosRespuesta);
  }
  function ImprimirCortes(){
    if ($this->input->method(TRUE) == "POST") {
      $idCorteCaja = $this->input->post("idCorteCaja");

      $joinFacDet = array(
        array(
          "tabla" => "senoritaServicio as senSer",
          "condicion" =>"facturaDetalle.idFacturaDetalle = senSer.idFacturaSenoritaServicio",
          "tipo" => "inner",
          "campos" => "senSer.idSenorita"
        ),
        array(
          "tabla" => "senorita as sen",
          "condicion" =>"senSer.idSenorita = sen.idSenorita",
          "tipo" => "inner",
          "campos" => "sen.apodoSenorita, sen.idSenoritaCategoria"
        ),
        array(
          "tabla" => "senoritaCategoria as senCat",
          "condicion" =>"sen.idSenoritaCategoria = senCat.idSenoritaCategoria",
          "tipo" => "inner",
          "campos" => "senCat.nombreSenoritaCategoria"
        ),
        array(
          "tabla" => "servicio as s",
          "condicion" =>"s.idServicio = facturaDetalle.idServicio",
          "tipo" => "inner",
          "campos" => "s.tiempoServicio, s.idServicioCategoria"
        ),
        array(
          "tabla" => "servicioDetalle as sd",
          "condicion" =>"s.idServicio = sd.idServicio AND sd.idSenoritaCategoriaServicioDetalle = senCat.idSenoritaCategoria",
          "tipo" => "inner",
          "campos" => "sd.porcentajeSenoritaServicioDetalle"
        ),
        array(
          "tabla" => "factura as fac",
          "condicion" =>"fac.idFactura = facturaDetalle.idFactura",
          "tipo" => "inner",
          "campos" => "fac.totalFactura, fac.tipoDocumentoFactura, fac.numeroDocumentoFactura, fac.frechaFactura, fac. horaFactura"
        ),
      );
      $condicionFacDet = array("idCorte" => $idCorteCaja, "tipoFactura" => "Servicio");
      $group = "facturaDetalle.idFacturaDetalle";
      $facturaDetalle = TraerDatosJoin("facturaDetalle",$condicionFacDet,'',$joinFacDet,$group);


      $relleno = "";
      $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
      $linea = str_pad("",42,"_",STR_PAD_BOTH);
      $salto = "\n";
      $divisor = "|";
      $divisormayor = "@";
      $espacios = 19;
      $espacios = 35;
      $senoritas  = array();
      //var_dump($facturaDetalle);
      if($facturaDetalle){
        foreach($facturaDetalle  as $dte){
          foreach ($dte as $key => $value) {
            if($key == "idSenorita"){
              if(!in_array($value,$senoritas)){
                $senoritas[$value]= array();
              }
            }
          }
        }
      }
      $serv = array();
      if($facturaDetalle){
        foreach($facturaDetalle  as $dte){
          array_push($senoritas[$dte->idSenorita], array('doc' => $dte->tipoDocumentoFactura.' '.$dte->numeroDocumentoFactura, 'apodo' => $dte->apodoSenorita,'tiempo' => $dte->tiempoServicio, 'total' => $dte->totalFactura, "porcentaje" => $dte->porcentajeSenoritaServicioDetalle));
          // $senoritas[$dte->idSenorita] = array('tiempo' => $dte->tiempoServicio, 'total' => $dte->totalFactura, "porcentaje" => $dte->porcentajeSenoritaServicioDetalle);
        }
      }

      // foreach ($senoritas as $key => $value) {
      //   // code...
      // }
      sort($senoritas);
      // print_r($senoritas);
      $senoritai = "";
      $senoritafi = "";
      $corteFormato="";
      $total = 0;
      $cambio = 0;
      if($senoritas !==0){
        foreach ($senoritas as $senorita => $datosa) {
          // print_r($datos);
          if($datosa !==0){
            foreach ($datosa as $datos) {
              if($senoritai != $datos["apodo"]){
                $senoritai = $datos["apodo"];
                $total = 0;
                $cambio =1;
                $espacios = 17;
                $corteFormato .= $divisormayor;
                $relleno = $espacios - strlen("CORTE ".$datos["apodo"]);
                $corteFormato .= $espacioInicio.str_pad("CORTE ".$datos["apodo"],$relleno," ",STR_PAD_RIGHT).$salto.$divisor;
                // $corteFormato .= $linea;
                $corteFormato .= $linea;
                $corteFormato .= $espacioInicio."FECHA: ".date("d-m-Y h:i A").$salto;
                $corteFormato .= $linea;
                $corteFormato .= $espacioInicio.str_pad("DOCUMENTO",25," ",STR_PAD_RIGHT).str_pad("TIEMPO",10," ",STR_PAD_RIGHT).str_pad("TOTAL",12," ",STR_PAD_RIGHT).$salto;
                $corteFormato .= $linea;

              }
              // code...
              $espacios = 35;
              $porcion = $datos["total"] * ($datos["porcentaje"]/100);
              $total+= $porcion;
              $corteFormato .= $espacioInicio.str_pad($datos["doc"],25," ",STR_PAD_RIGHT).str_pad($datos["tiempo"],10," ",STR_PAD_RIGHT).str_pad(number_format($porcion,2),12," ",STR_PAD_RIGHT).$salto;

              // $corteFormato .= $salto;
              // if($senoritai != $datos["apodo"]){
              //   $senoritai = $datos["apodo"];
              //   $corteFormato .= $espacioInicio.str_pad("TOTAL",35," ",STR_PAD_RIGHT).str_pad(number_format($total,2),12," ",STR_PAD_RIGHT);
              //   $total = 0;
              // }
            }
            if($cambio){
              $corteFormato .= $linea;
              $corteFormato .= $espacioInicio.str_pad("TOTAL",35," ",STR_PAD_RIGHT).str_pad(number_format($total,2),12," ",STR_PAD_RIGHT).$salto;
              $corteFormato .= $linea;
              $corteFormato .= $salto;
              $cambio=0;
            }
          }
        }
      }
    }
    // echo $corteFormato;
    $datosRespuesta["codigo"] = 200;
    $datosRespuesta["datos"] = urlencode($corteFormato);

    echo json_encode($datosRespuesta);
  }
  function ImprimirCortes2(){

    if ($this->input->method(TRUE) == "POST") {
      $idCorteCaja = $this->input->post("idCorteCaja");

      $joinFacDet = array(
        array(
          "tabla" => "senorita as sen",
          "condicion" =>"pedidoDetalle.senoritaPedidoDetalle = sen.idSenorita",
          "tipo" => "inner",
          "campos" => "sen.apodoSenorita, sen.idSenorita"
        ),
        array(
          "tabla" => "producto as pr",
          "condicion" =>"pr.idProducto = pedidoDetalle.idProductoPedidoDetalle",
          "tipo" => "inner",
          "campos" => "pr.nombreProducto"
        ),
        array(
          "tabla" => "factura as fac",
          "condicion" =>"fac.idReferenciaFactura = pedidoDetalle.idPedido",
          "tipo" => "left",
          "campos" => "fac.totalFactura, fac.tipoDocumentoFactura, fac.numeroDocumentoFactura"
        ),
      );
      $condicionFacDet = array("pedidoDetalle.idCorte" => $idCorteCaja, "tipoPedido" => "Producto Especial");
      $group = "pedidoDetalle.idPedidoDetalle";
      $facturaDetalle = TraerDatosJoin("pedidoDetalle",$condicionFacDet,'',$joinFacDet,$group);


      $relleno = "";
      $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
      $linea = str_pad("",42,"_",STR_PAD_BOTH);
      $salto = "\n";
      $divisor = "|";
      $divisormayor = "@";
      $espacios = 19;
      $espacios = 35;
      $senoritas  = array();
      //var_dump($facturaDetalle);
      if($facturaDetalle){
        foreach($facturaDetalle  as $dte){
          foreach ($dte as $key => $value) {
            if($key == "idSenorita"){
              if(!in_array($value,$senoritas)){
                $senoritas[$value]= array();
              }
            }
          }
        }
      }
      // print_r($senoritas);
      $serv = array();
      if($facturaDetalle){
        foreach($facturaDetalle  as $dte){
          array_push($senoritas[$dte->idSenorita], array('doc' => $dte->tipoDocumentoFactura.' '.$dte->numeroDocumentoFactura, 'apodo' => $dte->apodoSenorita,'nombre' => $dte->nombreProducto, 'cantidad' => $dte->cantidadPedidoDetalle, 'precio' => $dte->precioPedidoDetalle));
          // $senoritas[$dte->idSenorita] = array('tiempo' => $dte->tiempoServicio, 'total' => $dte->totalFactura, "porcentaje" => $dte->porcentajeSenoritaServicioDetalle);
        }
      }

      // foreach ($senoritas as $key => $value) {
      //   // code...
      // }
      // print_r($senoritas);
      $porcentaje = GblTraerConfiguracion("porcentaje_bebida_senorita");
      sort($senoritas);
      // print_r($senoritas);
      $senoritai = "";
      $senoritafi = "";
      $corteFormato="";
      $total = 0;
      $cambio = 0;
      if($senoritas !==0){
        foreach ($senoritas as $senorita => $datosa) {
          // print_r($datos);
          if($datosa !==0){
            foreach ($datosa as $datos) {
              if($senoritai != $datos["apodo"]){
                $senoritai = $datos["apodo"];
                $total = 0;
                $cambio =1;
                $espacios = 17;
                $corteFormato .= $divisormayor;
                $relleno = $espacios - strlen("CORTE ".$datos["apodo"]);
                $corteFormato .= $espacioInicio.str_pad("CORTE ".$datos["apodo"],$relleno," ",STR_PAD_RIGHT).$salto.$divisor;
                $corteFormato .= $linea;
                $corteFormato .= $espacioInicio."FECHA: ".date("d-m-Y h:i A").$salto;
                $corteFormato .= $linea;
                $corteFormato .= $espacioInicio.str_pad("DOC.",8," ",STR_PAD_RIGHT).str_pad("CANT.",5," ",STR_PAD_RIGHT)."  ".str_pad("PRODUCTO",18," ",STR_PAD_RIGHT).str_pad("P.U",6," ",STR_PAD_RIGHT).str_pad("TOTAL",8," ",STR_PAD_RIGHT).$salto;
                $corteFormato .= $linea;

              }
              // code...
              $espacios = 35;
              $individual = $datos["precio"] * ($porcentaje/100);
              $porcion = $datos["cantidad"] * $individual;
              $total+= $porcion;
              $corteFormato .= $espacioInicio.str_pad($datos["doc"],8," ",STR_PAD_RIGHT).str_pad($datos["cantidad"],5," ",STR_PAD_LEFT)."  ".str_pad(substr($datos["nombre"],0,18),18," ",STR_PAD_RIGHT).str_pad(number_format($individual,2),6," ",STR_PAD_RIGHT).str_pad(number_format($porcion,2),8," ",STR_PAD_RIGHT).$salto;

              // $corteFormato .= $salto;
              // if($senoritai != $datos["apodo"]){
              //   $senoritai = $datos["apodo"];
              //   $corteFormato .= $espacioInicio.str_pad("TOTAL",35," ",STR_PAD_RIGHT).str_pad(number_format($total,2),12," ",STR_PAD_RIGHT);
              //   $total = 0;
              // }
            }
            if($cambio){
              $corteFormato .= $linea;
              $corteFormato .= $espacioInicio.str_pad("TOTAL",39," ",STR_PAD_RIGHT).str_pad(number_format($total,2),8," ",STR_PAD_RIGHT).$salto;
              $corteFormato .= $linea;
              $corteFormato .= $salto;
              $cambio=0;
            }
          }

        }
      }

    }
    // echo $corteFormato;
    $datosRespuesta["codigo"] = 200;
    $datosRespuesta["datos"] = urlencode($corteFormato);

    echo json_encode($datosRespuesta);
  }
  function ImprimirTicketProducto($idFactura = ''){

    if ($this->input->method(TRUE) == "POST") {
      $efectivo = $this->input->post('efectivo');
      $vuelto = $this->input->post('vuelto');
      $descuento = "";
      $extraEspacio = 0;



      /*******************************************************/
      $propina = 0;
      $propinaCalculada = 0;
      $cobroPropina = GblTraerConfiguracion("cobroPropina");
      if($cobroPropina == "Si"){
        $propina = GblTraerConfiguracion("valorPropina");
      }
      /******************************************************/
      $joinFac = array(
        array(
          "tabla" => "caja",
          "condicion" => "caja.idCaja = factura.idCajaFactura",
          "tipo" => "left",
          "campos" => "nombreCaja"
        ),
        array(
          "tabla" => "corteTurno",
          "condicion" => "corteTurno.idTurno = factura.idTurno",
          "tipo" => "left",
          "campos" => "corteTurno"
        ),
        array(
          "tabla" => "cliente",
          "condicion" => "cliente.idCliente = factura.idCliente",
          "tipo" => "left",
          "campos" => "nombreCliente"
        ),
        array(
          "tabla" => "documento",
          "condicion" => "documento.aliasDocumento = factura.tipoDocumentoFactura",
          "tipo" => "left",
          "campos" => "idDocumento"
        ),
        array(
          "tabla" => "cajaDocumento",
          "condicion" => "cajaDocumento.idDocumentoCajaDocumento = documento.idDocumento AND cajaDocumento.idCajaCajaDocumento = factura.idCajaFactura",
          "tipo" => "left",
          "campos" => "numeroResolucionCajaDocumento,inicioCajaDocumento,finalCajaDocumento,fechaResolucionCajaDocumento,numeroResolucionCajaDocumento,serieCajaDocumento",
        ),
      );
      $condicionFac = array("idFactura" => $idFactura,"cajaDocumento.estadoCajaDocumento" => "Activo");
      $factura = TraerUnDatoJoin("factura",$condicionFac,$joinFac);
      ////////////////////////
      $propinaFactura = $factura->propinaFactura;
      ////////////////////////
      $condicionImp = array("idFactura" => $idFactura);
      $joinImp = array(
        array(
          "tabla" => "caja",
          "condicion" => "caja.impresoraCaja = impresora.idImpresora",
        ),
        array(
          "tabla" => "factura",
          "condicion" => "factura.idCajaFactura = caja.idCaja",
        ),
      );
      $impresora = TraerUnDatoJoin("impresora",$condicionImp,$joinImp);
      if($impresora){
        $tipo = $impresora->tipoImpresora;
        $recurso = $impresora->recursoCompartidoImpresora;
        $ip = $impresora->IpImpresora;
        $servidor = $impresora->servidorImpresora;
      } else {
        $tipo = "IP";
        $recurso = "/dev/usb/lp0";
        $ip = "192.168.1.150";
        $servidor = base_url();
      }

      $condicionPedido = array("idPedido"=>$factura->idReferenciaFactura);
      $joinPedido = array(
        array(
          "tabla" => "usuario",
          "condicion" => "pedido.idUsuarioPedido  = usuario.idUsuario",
          "tipo" => "left",
          "campos" => "nombreUsuario"
        ),
        array(
          "tabla" => "zonaMesa",
          "condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
          "tipo" => "left",
          "campos" => "nombreZonaMesa"
        ),
      );
      $pedido = TraerUnDatoJoin("pedido",$condicionPedido,$joinPedido);

      $tipoCargoZona = $pedido->tipoAumentoPedido;
      $cargoZona = $pedido->aumentoPedido;
      $personas = $pedido->personasPedido;
      $cargoZonaMonto = 0;

      // $condicionPedDet = array("idPedido"=>$factura->idReferenciaFactura,'impreso' => '0');
      $condicionPedDet = array("idPedido"=>$factura->idReferenciaFactura,"estadoPedidoDetalle !="=>"Borrado");
      $joinDet = array(
        array(
          "tabla" => "producto",
          "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
          "tipo" => "inner",
          "campos" => "nombreProducto,SUM(cantidadPedidoDetalle) AS cantidad"
        ),
        array(
          "tabla" => "senorita as sen",
          "condicion" =>"sen.idSenorita = pedidoDetalle.senoritaPedidoDetalle",
          "tipo" => "left",
          "campos" => "sen.apodoSenorita"
        ),
      );
      $pedidoDetalle = TraerDatosJoin("pedidoDetalle",$condicionPedDet,"",$joinDet,"pedidoDetalle.idProductoPedidoDetalle, regaliaPedidoDetalle, precioPedidoDetalle");

      //$pedidoDetalle = TraerDatosJoin("pedidoDetalle",$condicionPedDet,"",$joinDet);
      $datos = array(
        "idFactura" => $idFactura,
        "idCliente" => $factura->idCliente,
        "cliente"   => $factura->nombreFactura,
        "idCaja"    => $factura->idCajaFactura,
        "tipoDoc"   => $factura->tipoDocumentoFactura,
        "correlativo"=> $factura->numeroDocumentoFactura,
        "total"     => $factura->totalFactura,
        "vuelto"    => $factura->vueltoFactura,
        "efectivo"  => $factura->efectivoFactura,
      );
      $nombreEmpresa = GblTraerConfiguracion("nombreEmpresa");
      $nombrePatrono = GblTraerConfiguracion("razonSocialEmpresa");
      $direccionEmpresa = GblTraerConfiguracion("direccionEmpresa");
      $telefonoEmpresa = GblTraerConfiguracion("telefonoEmpresa");
      $nitEmpresa = GblTraerConfiguracion("nitEmpresa");
      $nrcEmpresa = GblTraerConfiguracion("nrcEmpresa");
      $giroEmpresa = GblTraerConfiguracion("giroEmpresa");
      if($efectivo == ''){$efectivo = $factura->efectivoFactura + $factura->tarjetaFactura + $factura->bitcoinFactura + $factura->pedidosYaFactura;}
      if($descuento == ''){$descuento = $factura->descuentoDolarFactura;}
      if($vuelto == ''){$vuelto = $factura->vueltoFactura;}
      $porConsumo = $factura->porConsumoFactura;


      $servicios =  array();
      $senoritas =  array();
      $divisormayor = "@";

      $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
      $linea = str_pad("",42 + $extraEspacio,"_",STR_PAD_BOTH);
      $divisor = "|";
      $salto = "\n";
      $espacioCant = 4;
      $espacioPu = 5;
      $espacioDes = 24 + $extraEspacio;
      $espacioTot = 8;
      $espacios = 19;
      $porcentajebeb = GblTraerConfiguracion("porcentaje_bebida_senorita");

      /**************************************/
      /**************************************/
      if($pedidoDetalle){
        foreach($pedidoDetalle  as $dte){
          foreach ($dte as $key => $value) {
            if($key == "senoritaPedidoDetalle"){
              if(!in_array($value,$senoritas)){
                $senoritas[$value]= array();
              }
            }
          }
        }
      }
      // $idImpresora = TraerUnDatoIndividual("caja","impresoraCaja",array("idCaja"=>$factura->idCajaFactura))[0]["impresoraCaja"];
      // $impresora = TraerUnDato("impresora",array("idImpresora"=>$idImpresora));
      // $datos["recursoCompartido"] = $impresora->recursoCompartidoImpresora;
      // $datos["IpImpresora"] = $impresora->IpImpresora;
      // $datos["idImpresora"] = $impresora->idImpresora;
      // $datos["nombreImpresora"] = $impresora->nombreImpresora;
      // $datos["impresoraRed"] =  GblTraerConfiguracion("impresionEnRed");
      // $senoritas[1] = array();
      /**************************************/
      /**************************************/
      $direccion = wordwrap($direccionEmpresa,40 + $extraEspacio,$salto." ");
      $espacios = 35;
      $relleno = $espacios - strlen($nombreEmpresa);
      $servicio = $espacioInicio.str_pad($nombreEmpresa,$relleno," ",STR_PAD_BOTH);
      // $relleno = $espacios - strlen($nombrePatrono);
      // $servicio = $salto.$espacioInicio.str_pad($nombrePatrono,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen($direccion);
      $servicio .= $salto.$espacioInicio.str_pad($direccion,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("TEL: ".$telefonoEmpresa);
      $servicio .= $salto.$espacioInicio.str_pad("TEL: ".$telefonoEmpresa,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("NIT: ".$nitEmpresa." NRC: ".$nrcEmpresa);
      $servicio .= $salto.$espacioInicio.str_pad("NIT: ".$nitEmpresa." NRC: ".$nrcEmpresa,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("GIRO: ".$giroEmpresa);
      $servicio .= $salto.$espacioInicio.str_pad("GIRO: ".$giroEmpresa,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("RESOLUCION N. ".$factura->numeroResolucionCajaDocumento);
      $servicio .= $salto.$espacioInicio.str_pad("RESOLUCION N. ".$factura->numeroResolucionCajaDocumento,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("FECHA RESOLUCION. ".Fecha_D_M_A($factura->fechaResolucionCajaDocumento));
      $servicio .= $salto.$espacioInicio.str_pad("FECHA RESOLUCION. ".Fecha_D_M_A($factura->fechaResolucionCajaDocumento),$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("SERIE. ".$factura->serieCajaDocumento);
      $servicio .= $salto.$espacioInicio.str_pad("SERIE. ".$factura->serieCajaDocumento,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("DE: ".$factura->inicioCajaDocumento." AL: ".$factura->finalCajaDocumento);
      $servicio .= $salto.$espacioInicio.str_pad("DE: ".$factura->inicioCajaDocumento." AL: ".$factura->finalCajaDocumento,$relleno," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("TIQUETE # ".Zfill($factura->numeroDocumentoFactura,10));
      $servicio .= $salto.$espacioInicio.str_pad("TIQUETE # ".Zfill($factura->numeroDocumentoFactura,10),$relleno," ",STR_PAD_BOTH);
      //$servicio .= $salto.$linea.$salto;
      $servicio .= $salto;
      $servicio .= $linea.$salto;

      $servicio .= $divisor;

      $relleno = $espacios - strlen("TIPO DOC: ".$factura->tipoDocumentoFactura."".$factura->numeroDocumentoFactura);
      $servicio .= $espacioInicio.str_pad("TIPO DOC: ".$factura->tipoDocumentoFactura."".$factura->numeroDocumentoFactura,$relleno," ",STR_PAD_RIGHT).$salto;

      $relleno = $espacios - strlen("FECHA: ".Fecha_D_M_A($factura->fechaFactura)." ".Hora($factura->horaFactura));
      $servicio .= $espacioInicio.str_pad("FECHA: ".Fecha_D_M_A($factura->fechaFactura)." ".Hora($factura->horaFactura),$relleno," ",STR_PAD_RIGHT).$salto;

      $relleno = $espacios - strlen("CAJA: ".$factura->nombreCaja." TURNO: ".$factura->corteTurno);
      $servicio .= $espacioInicio.str_pad("CAJA: ".$factura->nombreCaja." TURNO: ".$factura->corteTurno,$relleno," ",STR_PAD_RIGHT).$salto;
      $nombrecli = ($factura->nombreFactura !="") ? $factura->nombreFactura : "CLIENTES VARIOS";
      $relleno = $espacios - strlen("CLIENTE: ".$nombrecli);
      $servicio .= $espacioInicio.str_pad("CLIENTE: ".$nombrecli,$relleno," ",STR_PAD_RIGHT).$salto;
      if($pedido->tipoCuentaPedido == 'local'){
        $relleno = $espacios - strlen("ZONA/MESA: ".$pedido->zonaPedido." - "."MESA # ".$pedido->nombreZonaMesa);
        $servicio .= $espacioInicio.str_pad("ZONA/MESA: ".$pedido->zonaPedido." - "."MESA # ".$pedido->nombreZonaMesa,$relleno," ",STR_PAD_RIGHT).$salto;
      } else {

      }
      if($personas != "" && $personas>0){
        $relleno = $espacios - strlen("No. DE PERSONAS: ".$personas);
        $servicio .= $espacioInicio.str_pad("No. DE PERSONAS: ".$personas,$relleno," ",STR_PAD_RIGHT).$salto;
      }

      $servicio .= $linea.$salto;
      //$servicio .= $espacioInicio."CANT. DETALLE              P.U   SUBTOTAL".$salto;
      $espacioExtra = str_pad("",$extraEspacio," ",STR_PAD_BOTH);
      $relleno = $espacios - strlen("CANT. DETALLE              ".$espacioExtra."P.U   SUBTOTAL");
      $servicio .= $espacioInicio.str_pad("CANT. DETALLE              ".$espacioExtra."P.U   SUBTOTAL",$relleno," ",STR_PAD_RIGHT).$salto;
      $servicio .= $linea.$salto;
      // $ifsenorita = false;
      //$senoritas = array();
      $total = 0;
      if($porConsumo == 0){
        foreach($pedidoDetalle  as $det){
          $relleno = "";
          $cantidad = $det->cantidad;
          $descripcion = substr($det->nombreProducto,0,22 + $extraEspacio);
          $monto = $det->precioPedidoDetalle;
          if($det->regaliaPedidoDetalle == "1"){
            $monto = 0.00;
          }
          if($det->senoritaPedidoDetalle >0){
            // array_push($senoritas[1], array("nombre" => $det->nombreProducto, "cantidad" => $det->cantidadPedidoDetalle,"precio" => $det->precioPedidoDetalle,"apodo" => $det->apodoSenorita));
            array_push($senoritas[$dte->senoritaPedidoDetalle], array("nombre" => $det->nombreProducto, "cantidad" => $det->cantidadPedidoDetalle,"precio" => $monto,"apodo" => $det->apodoSenorita));
          }
          $subtotal = number_format($monto * $cantidad,2);
          $total+= $subtotal;
          $servicio .= $espacioInicio.str_pad($cantidad,$espacioCant," ",STR_PAD_RIGHT);
          $servicio .= str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT);
          $servicio .= str_pad(number_format($monto,2),$espacioPu," ",STR_PAD_LEFT);
          $servicio .= str_pad(number_format($subtotal,2),$espacioTot," ",STR_PAD_LEFT).$salto;
          $modificadores = $this->llamarModificadores($det->idPedidoDetalle,8);
          //$servicio .= str_pad($modificadores,0," ",STR_PAD_RIGHT);
        }
      } else {
        foreach($pedidoDetalle  as $det){
          $relleno = "";
          $cantidad = $det->cantidad;
          $descripcion = substr($det->nombreProducto,0,22 + $extraEspacio);
          $monto = $det->precioPedidoDetalle;
          if($det->regaliaPedidoDetalle == "1"){
            $monto = 0.00;
          }
          if($det->senoritaPedidoDetalle >0){
            // array_push($senoritas[1], array("nombre" => $det->nombreProducto, "cantidad" => $det->cantidadPedidoDetalle,"precio" => $det->precioPedidoDetalle,"apodo" => $det->apodoSenorita));
            array_push($senoritas[$dte->senoritaPedidoDetalle], array("nombre" => $det->nombreProducto, "cantidad" => $det->cantidadPedidoDetalle,"precio" => $monto,"apodo" => $det->apodoSenorita));
          }
          $subtotal = number_format($monto * $cantidad,2);
          $total+= $subtotal;
          //$servicio .= str_pad($modificadores,0," ",STR_PAD_RIGHT);
        }
        $servicio .= $espacioInicio.str_pad("1",$espacioCant," ",STR_PAD_RIGHT);
        $servicio .= str_pad("Cobro Por Consumo",$espacioDes," ",STR_PAD_RIGHT);
        $servicio .= str_pad(number_format($total,2),$espacioPu," ",STR_PAD_LEFT);
        $servicio .= str_pad(number_format($total,2),$espacioTot," ",STR_PAD_LEFT).$salto;
      }
      $servicio .=  $linea.$salto;

      ////////////////////////////////////////////////////////////////////////////////////
      if($propinaFactura > 0){
        $propinaCalculada = $propinaFactura;
        $relleno =15;//strlen("PROPINA: ");
        $servicio .= $espacioInicio.str_pad("SUMAS: ",$relleno," ",STR_PAD_RIGHT);
        $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->propinaFactura,2));
        $servicio .= $espacioInicio.str_pad("$".number_format($total,2),$relleno," ",STR_PAD_LEFT).$salto;
        ////////////////////////////////////////////////////////////////////////////////////
        $relleno =15;//strlen("PROPINA: ");
        $servicio .= $espacioInicio.str_pad("PROPINA (".$propina."%): ",$relleno," ",STR_PAD_RIGHT);
        $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->propinaFactura,2));
        $servicio .= $espacioInicio.str_pad("$".number_format($propinaCalculada,2),$relleno," ",STR_PAD_LEFT).$salto;
        ////////////////////////////////////////////////////////////////////////////////////
      }

      ////////////////////////////////////////////////////////////////////////////////////
      //  if($tipoCargoZona == "Monto"){
      //   $cargoZonaMonto = $cargoZona;
      // }
      // if($tipoCargoZona == "Porcentaje"){
      //   $cargoZonaMonto = $cargoZona * $total / 100;
      // }
      // $relleno =15;//strlen("PROPINA: ");
      // $servicio .= $espacioInicio.str_pad("CARGO ZONA: ",$relleno," ",STR_PAD_RIGHT);
      // $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->propinaFactura,2));
      // $servicio .= $espacioInicio.str_pad("$".number_format($cargoZonaMonto,2),$relleno," ",STR_PAD_LEFT).$salto;
      //  ////////////////////////////////////////////////////////////////////////////////////

      ////////////////////////////////////////////////////////////////////////////////////
      $relleno =15;//strlen("TOTAL: ");
      $servicio .= $espacioInicio.str_pad("TOTAL (G): ",$relleno," ",STR_PAD_RIGHT);
      $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->totalFactura,2));
      $servicio .= $espacioInicio.str_pad("$".number_format(($total + $propinaCalculada +$cargoZonaMonto ) ,2),$relleno," ",STR_PAD_LEFT).$salto;
      ////////////////////////////////////////////////////////////////////////////////////

      ////////////////////////////////////////////////////////////////////////////////////
      $relleno =15;//strlen("TOTAL: ");
      $servicio .= $espacioInicio.str_pad("TOTAL (E): ",$relleno," ",STR_PAD_RIGHT);
      $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->totalFactura,2));
      $servicio .= $espacioInicio.str_pad("$0.00",$relleno," ",STR_PAD_LEFT).$salto;
      ////////////////////////////////////////////////////////////////////////////////////

      ////////////////////////////////////////////////////////////////////////////////////
      $relleno =15;//strlen("EFECTIVO: ");
      $servicio .= $espacioInicio.str_pad("DESCUENTO: ",$relleno," ",STR_PAD_RIGHT);
      $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->efectivoFactura,2));
      $servicio .= $espacioInicio.str_pad("$".number_format($descuento,2),$relleno," ",STR_PAD_LEFT).$salto;
      ////////////////////////////////////////////////////////////////////////////////////


      //envio

      if($factura->envioFactura>0)
      {
        $relleno =15;//strlen("TOTAL: ");
        $servicio .= $espacioInicio.str_pad("+ ENVIO: ",$relleno," ",STR_PAD_RIGHT);
        $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->totalFactura,2));
        $servicio .= $espacioInicio.str_pad("$".number_format(($factura->envioFactura) ,2),$relleno," ",STR_PAD_LEFT).$salto;

      }


      ////////////////////////////////////////////////////////////////////////////////////
      $relleno =15;//strlen("TOTAL: ");
      $servicio .= $espacioInicio.str_pad("A PAGAR: ",$relleno," ",STR_PAD_RIGHT);
      $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->totalFactura,2));
      $servicio .= $espacioInicio.str_pad("$".number_format(($total + $propinaCalculada - $descuento+$factura->envioFactura) ,2),$relleno," ",STR_PAD_LEFT).$salto;



      ////////////////////////////////////////////////////////////////////////////////////

      ////////////////////////////////////////////////////////////////////////////////////
      $relleno =15;//strlen("EFECTIVO: ");
      $servicio .= $espacioInicio.str_pad("EFECTIVO: ",$relleno," ",STR_PAD_RIGHT);
      $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->efectivoFactura,2));
      $servicio .= $espacioInicio.str_pad("$".number_format($efectivo,2),$relleno," ",STR_PAD_LEFT).$salto;



      if($factura->tarjetaFactura>0)
      {
        $relleno =15;//strlen("EFECTIVO: ");
        $servicio .= $espacioInicio.str_pad("TARJETA: ",$relleno," ",STR_PAD_RIGHT);
        $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->efectivoFactura,2));
        $servicio .= $espacioInicio.str_pad("$".number_format($factura->tarjetaFactura,2),$relleno," ",STR_PAD_LEFT).$salto;
      }
      if($factura->bitcoinFactura>0)
      {
        $relleno =15;//strlen("EFECTIVO: ");
        $servicio .= $espacioInicio.str_pad("BITCOIN: ",$relleno," ",STR_PAD_RIGHT);
        $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->efectivoFactura,2));
        $servicio .= $espacioInicio.str_pad("$".number_format($factura->bitcoinFactura,2),$relleno," ",STR_PAD_LEFT).$salto;
      }
      if($factura->pedidosYaFactura>0)
      {
        $relleno =15;//strlen("EFECTIVO: ");
        $servicio .= $espacioInicio.str_pad("PEDIDOS YA: ",$relleno," ",STR_PAD_RIGHT);
        $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->efectivoFactura,2));
        $servicio .= $espacioInicio.str_pad("$".number_format($factura->pedidosYaFactura,2),$relleno," ",STR_PAD_LEFT).$salto;
      }
      if($factura->transferenciaFactura>0)
      {
        $relleno =15;//strlen("EFECTIVO: ");
        $servicio .= $espacioInicio.str_pad("TRANSFERENCIA: ",$relleno," ",STR_PAD_RIGHT);
        $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->efectivoFactura,2));
        $servicio .= $espacioInicio.str_pad("$".number_format($factura->transferenciaFactura,2),$relleno," ",STR_PAD_LEFT).$salto;
      }

      ////////////////////////////////////////////////////////////////////////////////////

      ////////////////////////////////////////////////////////////////////////////////////
      $relleno =15;//strlen("CAMBIO: ");
      $servicio .= $espacioInicio.str_pad("CAMBIO: ",$relleno," ",STR_PAD_RIGHT);
      $relleno =25 + $extraEspacio;//strlen("$".number_format($factura->vueltoFactura,2));
      $servicio .= $espacioInicio.str_pad("$".number_format($vuelto,2),$relleno," ",STR_PAD_LEFT).$salto;
      ////////////////////////////////////////////////////////////////////////////////////
      $servicio .= $divisor;
      $espacios = 35;
      $relleno = $espacios - strlen("E = EXENTO G = GRAVADO");
      $servicio .= $espacioInicio.str_pad("E = EXENTO G = GRAVADO",$relleno," ",STR_PAD_BOTH).$salto;
      // $relleno = $espacios - strlen("RESOLUCION N. ".$factura->numeroResolucionCajaDocumento);
      // $servicio .= $espacioInicio.str_pad("RESOLUCION N. ".$factura->numeroResolucionCajaDocumento,$relleno," ",STR_PAD_BOTH).$salto;
      // $relleno = $espacios - strlen("FECHA RESOLUCION ".Fecha_D_M_A($factura->fechaResolucionCajaDocumento));
      // $servicio .= $espacioInicio.str_pad("FECHA RESOLUCION ".Fecha_D_M_A($factura->fechaResolucionCajaDocumento),$relleno," ",STR_PAD_BOTH).$salto;
      // $relleno = $espacios - strlen("DE: ".$factura->inicioCajaDocumento." AL: ".$factura->finalCajaDocumento);
      // $servicio .= $espacioInicio.str_pad("DE: ".$factura->inicioCajaDocumento." AL: ".$factura->finalCajaDocumento,$relleno," ",STR_PAD_BOTH).$salto;
      $relleno = $espacios - strlen("GRACIAS POR SU COMPRA");
      $servicio .= $espacioInicio.str_pad("GRACIAS POR SU COMPRA",$relleno," ",STR_PAD_BOTH).$salto;

      $datos['servicio'] = urlencode($servicio);

      /**************************************/
      /**************************************/
      if(GblTraerConfiguracion("ServicioSenorita") == "Si" && GblTraerConfiguracion("porcentaje_bebida_senorita") == "Si"){
        if($senoritas !==0){
          $espacios = 19;

          $relleno = $espacios - strlen("TICKET CASA");
          $servicio11 = $salto.$espacioInicio.str_pad("TICKET CASA",$relleno," ",STR_PAD_BOTH).$salto.$divisor;

          $espacios = 35;
          $relleno = $espacios - strlen("TIPO DOC: ".$factura->tipoDocumentoFactura."".$factura->numeroDocumentoFactura);
          $servicio11 .= $espacioInicio.str_pad("TIPO DOC: ".$factura->tipoDocumentoFactura."".$factura->numeroDocumentoFactura,$relleno," ",STR_PAD_RIGHT).$salto;

          $relleno = $espacios - strlen("FECHA: ".$factura->fechaFactura);
          $servicio11 .= $espacioInicio.str_pad("FECHA: ".$factura->fechaFactura,$relleno," ",STR_PAD_RIGHT).$salto;

          $relleno = $espacios - strlen("HORA: ".$factura->horaFactura);
          $servicio11 .= $espacioInicio.str_pad("HORA: ".$factura->horaFactura,$relleno," ",STR_PAD_RIGHT).$salto;

          $servicio11 .= $linea.$salto;
          $servicio11 .= $espacioInicio."CANT. DETALLE            P.U    SUBTOTAL".$salto;
          $servicio11 .= $linea.$salto;
          // $ifsenorita = false;
          //$senoritas = array();
          $total = 0;
          foreach($pedidoDetalle  as $det){
            $relleno = "";
            $cantidad = $det->cantidadPedidoDetalle;
            $descripcion = substr($det->nombreProducto,0,26);
            $monto = $det->precioPedidoDetalle;
            if($det->regaliaPedidoDetalle == "1"){
              $monto = 0.00;
            }
            if($det->senoritaPedidoDetalle >0){
              $monto = $det->precioPedidoDetalle * (1 - ($porcentajebeb/100));
            }
            $subtotal = number_format($monto * $cantidad,2);
            $total += $subtotal;
            $servicio11 .= $espacioInicio.str_pad($cantidad,$espacioCant," ",STR_PAD_RIGHT);
            $servicio11 .= str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT);
            $servicio11 .= str_pad(number_format($monto,2),$espacioPu," ",STR_PAD_LEFT);
            $servicio11 .= str_pad(number_format($subtotal,2),$espacioTot," ",STR_PAD_LEFT).$salto;
            $modificadores = $this->llamarModificadores($det->idPedidoDetalle,8);
            $servicio11 .= str_pad($modificadores,0," ",STR_PAD_RIGHT);
          }
          $servicio11 .=  $linea.$salto;

          ////////////////////////////////////////////////////////////////////////////////////
          $relleno =15;//strlen("TOTAL: ");
          $servicio11 .= $espacioInicio.str_pad("TOTAL: ",$relleno," ",STR_PAD_RIGHT);
          $relleno =29;//strlen("$".number_format($total,2));
          $servicio11 .= $espacioInicio.str_pad("$".number_format($total ,2),$relleno," ",STR_PAD_LEFT).$salto;
          ////////////////////////////////////////////////////////////////////////////////////


          $datos['servicio11'] = urlencode($servicio11);


          /*****************************************************************************************/
          /*****************************************************************************************/
          /*****************************************************************************************/
          /*****************************************************************************************/
          //echo "aca empieza";

          //print_r($senoritas);

          $servicio12 ="";
          $senoritai = "";
          $senoritafi = "";
          $cambio=0;
          $total = 0;
          foreach ($senoritas as $senorita => $datosa) {
            // print_r($datos);
            if($datosa !==0){
              foreach ($datosa as $datossen) {
                if($senoritai != $datossen["apodo"]){
                  $senoritai = $datossen["apodo"];
                  $cambio=1;
                  $total = 0;
                  $espacios = 17;

                  $servicio12 .= $divisormayor;
                  $relleno = $espacios - strlen("TICKET DE PRODUCTO");
                  $servicio12 .= $salto.$espacioInicio.str_pad("TICKET DE PRODUCTO",$relleno," ",STR_PAD_BOTH).$salto.$divisor;
                  $relleno = $espacios - strlen($datossen["apodo"]);
                  $servicio12 .= $salto.$espacioInicio.str_pad($datossen["apodo"],$relleno," ",STR_PAD_BOTH).$salto.$divisor;

                  $espacios = 35;

                  $relleno = $espacios - strlen("TIPO DOC: ".$factura->tipoDocumentoFactura."".$factura->numeroDocumentoFactura);
                  $servicio12 .= $espacioInicio.str_pad("TIPO DOC: ".$factura->tipoDocumentoFactura."".$factura->numeroDocumentoFactura,$relleno," ",STR_PAD_RIGHT).$salto;

                  $relleno = $espacios - strlen("FECHA: ".$factura->fechaFactura);
                  $servicio12 .= $espacioInicio.str_pad("FECHA: ".$factura->fechaFactura,$relleno," ",STR_PAD_RIGHT).$salto;

                  $relleno = $espacios - strlen("HORA: ".$factura->horaFactura);
                  $servicio12 .= $espacioInicio.str_pad("HORA: ".$factura->horaFactura,$relleno," ",STR_PAD_RIGHT).$salto;

                  $servicio12 .= $linea.$salto;
                  $servicio12 .= $espacioInicio."CANT. DETALLE            P.U    SUBTOTAL".$salto;
                  $servicio12 .= $linea.$salto;

                }
                $relleno = "";
                $cantidad = $datossen["cantidad"];
                $descripcion = substr($datossen["nombre"],0,26);
                $monto = $datossen["precio"] * ($porcentajebeb/100);

                $subtotal = number_format($monto * $cantidad,2);
                $total += $subtotal;
                $servicio12 .= $espacioInicio.str_pad($cantidad,$espacioCant," ",STR_PAD_RIGHT);
                $servicio12 .= str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT);
                $servicio12 .= str_pad(number_format($monto,2),$espacioPu," ",STR_PAD_LEFT);
                $servicio12 .= str_pad(number_format($subtotal,2),$espacioTot," ",STR_PAD_LEFT).$salto;
              }
              if($cambio){
                $servicio12 .=  $linea.$salto;
                ////////////////////////////////////////////////////////////////////////////////////
                $relleno = 15;//- strlen("TOTAL: ");
                $servicio12 .= $espacioInicio.str_pad("TOTAL: ",$relleno," ",STR_PAD_RIGHT);
                $relleno = 29;//- strlen("$".number_format($total,2));
                $servicio12 .= $espacioInicio.str_pad("$".number_format($total ,2),$relleno," ",STR_PAD_LEFT).$salto;
                ////////////////////////////////////////////////////////////////////////////////////
                $cambio=0;
              }
            }
          }
        }
        /*****************************************************************************************/
        /*****************************************************************************************/
        /*****************************************************************************************/
        /*****************************************************************************************/
        // echo $servicio12;
        // echo $servicio;
        $datos["servicio12"] = urlencode($servicio12);
      }
      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = $datos;
      $datosRespuesta["tipo"] = $tipo;
      $datosRespuesta["recurso"] = $recurso;
      $datosRespuesta["ip"] = $ip;
      $datosRespuesta["servidor"] = $servidor;


      echo json_encode($datosRespuesta);
    }
  }
  /////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////
  function p_set($linea,$dato,$inicio,$fin,$a)
  {
    //$dato = $this->quitar_spc($dato);
    $linea= str_replace("\n", "", $linea);
    $in = substr($linea,0,$inicio-1);
    $cuerpo =$this->st(substr($dato,0,($fin-$inicio)),($fin-$inicio)," ",$a);
    $complemento = $this->st(" ",strlen($linea)-strlen($in)-strlen($cuerpo));
    return $in.$cuerpo.$complemento."\n";
  }

  function st($input,$lengt,$carac=" ",$di="R")
  {
    // code..
    $r = "";
    switch ($di) {
      case 'L':
      // code...
      $r=str_pad($input, $lengt, $carac, STR_PAD_LEFT);
      break;
      case 'R':
      // code...
      $r=str_pad($input, $lengt, $carac, STR_PAD_RIGHT);
      break;
      case 'B':
      // code...
      $r=str_pad($input, $lengt, $carac, STR_PAD_BOTH);
      break;
      default:
      // code...
      break;
    }
    return $r;
  }

  function dtl( $text, $width = '80', $lines = '10', $break = '\n', $cut = 0 ) {
    $wrappedarr = array();
    $wrappedtext = wordwrap( $text, $width, $break , true );
    $wrappedtext = trim( $wrappedtext );
    $arr = explode( $break, $wrappedtext );
    return $arr;
  }
  function quitar_spc($cadena){
    $no_permitidas= array ("Ñ","ñ","º","á","é","í","ó","ú","Á","É","Í","Ó","Ú","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
    $permitidas=     array("N","n"," ","a","e","i","o","u","A","E","I","O","U","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E",);
    $texto = str_replace($no_permitidas, $permitidas ,$cadena);
    $texto = preg_replace('/[^a-zA-Z0-9ñÑ.|\/\-\_\+#*$:= ]/u',"",($texto));
    return ($texto);
  }
  /////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////
  function ImprimirFacturaProducto($idFactura = ''){

    if ($this->input->method(TRUE) == "POST") {
      $efectivo = $this->input->post('efectivo');
      $vuelto = $this->input->post('vuelto');
      $descuento = "";
      $extraEspacio = 5;



      /*******************************************************/
      $propina = 0;
      $propinaCalculada = 0;
      $cobroPropina = GblTraerConfiguracion("cobroPropina");
      if($cobroPropina == "Si"){
        $propina = GblTraerConfiguracion("valorPropina");
      }
      /******************************************************/
      $joinFac = array(
        array(
          "tabla" => "caja",
          "condicion" => "caja.idCaja = factura.idCajaFactura",
          "tipo" => "left",
          "campos" => "nombreCaja"
        ),
        array(
          "tabla" => "corteTurno",
          "condicion" => "corteTurno.idTurno = factura.idTurno",
          "tipo" => "left",
          "campos" => "corteTurno"
        ),
        array(
          "tabla" => "cliente",
          "condicion" => "cliente.idCliente = factura.idCliente",
          "tipo" => "left",
          "campos" => "nombreCliente"
        ),
        array(
          "tabla" => "documento",
          "condicion" => "documento.aliasDocumento = factura.tipoDocumentoFactura",
          "tipo" => "left",
          "campos" => "idDocumento"
        ),
        array(
          "tabla" => "cajaDocumento",
          "condicion" => "cajaDocumento.idDocumentoCajaDocumento = documento.idDocumento AND cajaDocumento.idCajaCajaDocumento = factura.idCajaFactura",
          "tipo" => "left",
          "campos" => "numeroResolucionCajaDocumento,inicioCajaDocumento,finalCajaDocumento,fechaResolucionCajaDocumento,numeroResolucionCajaDocumento,serieCajaDocumento",
        ),
      );
      $condicionFac = array("md5(idFactura)" => $idFactura,"cajaDocumento.estadoCajaDocumento" => "Activo");
      $factura = TraerUnDatoJoin("factura",$condicionFac,$joinFac);

      $condicionImp = array("md5(idFactura)" => $idFactura);
      $joinImp = array(
        array(
          "tabla" => "caja",
          "condicion" => "caja.impresoraCaja = impresora.idImpresora",
        ),
        array(
          "tabla" => "factura",
          "condicion" => "factura.idCajaFactura = caja.idCaja",
        ),
      );
      $impresora = TraerUnDatoJoin("impresora",$condicionImp,$joinImp);
      if($impresora){
        $tipo = $impresora->tipoImpresora;
        $recurso = $impresora->recursoCompartidoImpresora;
        $ip = $impresora->IpImpresora;
        $servidor = $impresora->servidorImpresora;
      } else {
        $tipo = "IP";
        $recurso = "/dev/usb/lp0";
        $ip = "192.168.1.150";
        $servidor = "127.0.0.1";
      }
      $servidor = "127.0.0.1";

      $condicionPedido = array("idPedido"=>$factura->idReferenciaFactura);
      $pedido = TraerUnDato("pedido",$condicionPedido);

      $tipoCargoZona = $pedido->tipoAumentoPedido;
      $cargoZona = $pedido->aumentoPedido;
      $cargoZonaMonto = 0;

      // $condicionPedDet = array("idPedido"=>$factura->idReferenciaFactura,'impreso' => '0');
      $condicionPedDet = array("idPedido"=>$factura->idReferenciaFactura,"estadoPedidoDetalle !="=>"Borrado");
      $joinDet = array(
        array(
          "tabla" => "producto",
          "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
          "tipo" => "inner",
          "campos" => "nombreProducto,SUM(cantidadPedidoDetalle) AS cantidad"
        ),
        array(
          "tabla" => "senorita as sen",
          "condicion" =>"sen.idSenorita = pedidoDetalle.senoritaPedidoDetalle",
          "tipo" => "left",
          "campos" => "sen.apodoSenorita"
        ),
      );
      $pedidoDetalle = TraerDatosJoin("pedidoDetalle",$condicionPedDet,"",$joinDet,"pedidoDetalle.idProductoPedidoDetalle, regaliaPedidoDetalle");


      if($efectivo == ''){$efectivo = $factura->efectivoFactura + $factura->tarjetaFactura + $factura->bitcoinFactura + $factura->pedidosYaFactura;}
      if($descuento == ''){$descuento = $factura->descuentoDolarFactura;}
      if($vuelto == ''){$vuelto = $factura->vueltoFactura;}
      $porConsumo = $factura->porConsumoFactura;


      //Columnas y posiciones base
      $tipfac=$factura->tipoDocumentoFactura;
      $nit=$factura->nitFactura;

      $nombre_ape=$factura->nombreFactura;//$nombreapecte;
      $fecha=$factura->fechaFactura;//$nombreapecte;
      $direccion=$factura->direccionFactura;//$nombreapecte;
      $nrc=$factura->nrcFactura;//$nombreapecte;
      $propina=$factura->propinaFactura;//$nombreapecte;
      $total_final=0;
      $info_factura ="";
      /*margen derecho*/
      $marginl=$this->st(" ",10);

      /*incializamos el arreglo con las lineas vacias*/
      $logitud_array=50;
      $arrayL= array();
      for ($i=0; $i < $logitud_array; $i++) {
        // code...
        $arrayL[$i]=$this->st("",88)."\n";
      }

      //Datos encabezado factura
      list($diaa,$mess,$anio)=explode("-",$fecha);
      if($tipfac == "FAC"){
        $arrayL[2]= $this->p_set($arrayL[2],fecha_d_m_a($fecha),70,88,"R");
        $arrayL[4]= $this->p_set($arrayL[4],$nombre_ape,19,88,"R");
        $arrayL[5]= $this->p_set($arrayL[5],$this->quitar_spc($direccion),19,88,"R");



        $array_painc = array(
        );
        $ini=11;
        for ($kl=0; $kl < 12; $kl++) {
          // code...
          $array_painc[$kl]=$ini;
          $ini++;
        }


        $total_final=0;
        $cuantos=0;
        $total=0;
        $i=0;
        if($porConsumo == 0){
          foreach($pedidoDetalle  as $det){
            $cantidad = $det->cantidad;
            $descripcion = $det->nombreProducto;
            $monto = $det->precioPedidoDetalle;
            if($det->regaliaPedidoDetalle == "1"){
              $monto = 0.00;
            }
            $subtotal = number_format($monto * $cantidad,2);
            $total += $subtotal;

            //imprimir productos
            if ($i<31) {

              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$cantidad,10,14,"B");
              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$this->quitar_spc($descripcion),15,62,"R");
              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$monto,57,63,"L");
              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$subtotal,72,87,"L");
              $cuantos=$cuantos+1;
            }
            $i++;
          }
        } else {
          foreach($pedidoDetalle  as $det){
            $cantidad = $det->cantidad;
            $descripcion = substr($det->nombreProducto,0,22 + $extraEspacio);
            $monto = $det->precioPedidoDetalle;
            if($det->regaliaPedidoDetalle == "1"){
              $monto = 0.00;
            }
            $subtotal = number_format($monto * $cantidad,2);
            $total+= $subtotal;
          }
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],1,10,14,"B");
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],"Cobro Por Consumo",15,62,"R");
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$total,57,63,"L");
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$total,72,87,"L");
          $cuantos=$cuantos+1;
          $i++;

        }
        $retencion = 0;
        $total_final_format=number_format(($total+$propina),2,".","");
        list($entero,$decimal)=explode('.',$total_final_format);
        $enteros_txt=num2letras($entero);
        if(strlen($decimal)==1){
          $decimales_txt=$decimal."0";
        }
        else{
          $decimales_txt=$decimal;
        }
        $cadena_salida_txt= "".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";

        $array_painc = array(
          0 => 31,
          1 => 32,
        );
        $array_nocon= $this->dtl($cadena_salida_txt,42);

        foreach ($array_nocon as $key => $value) {
          $arrayL[$array_painc[$key]]=$this->p_set($arrayL[$array_painc[$key]],$value,16,60,"R");
        }

        $arrayL[31]= $this->p_set($arrayL[31],number_format($total,2),72,87,"L");
        $arrayL[32]= $this->p_set($arrayL[32],number_format($total,2),72,87,"L");
        // $arrayL[33]= $this->p_set($arrayL[33],$retencion,72,87,"L");
        // $arrayL[35]= $this->p_set($arrayL[35],"0.00",72,87,"L");
        // $arrayL[36]= $this->p_set($arrayL[36],"0.00",72,87,"L");
        // $arrayL[32]= $this->p_set($arrayL[32],number_format(($total-$retencion),2),72,87,"L");
        $arrayL[37]= $this->p_set($arrayL[37],number_format(($propina),2),72,87,"L");
        $arrayL[39]= $this->p_set($arrayL[39],number_format(($total+$propina),2),72,87,"L");
      } else {
        $arrayL[1]= $this->p_set($arrayL[1],$nombre_ape,19,65,"R");
        $arrayL[1]= $this->p_set($arrayL[1],fecha_d_m_a($fecha),70,88,"R");
        $arrayL[2]= $this->p_set($arrayL[2],$this->quitar_spc($direccion),19,65,"R");
        $arrayL[2]= $this->p_set($arrayL[2],$nrc,70,88,"R");
        // $arrayL[6]= $this->p_set($arrayL[6],$this->quitar_spc($direccion),19,65,"R");
        $arrayL[3]= $this->p_set($arrayL[3],$nit,65,88,"R");



        $array_painc = array(
        );
        $ini=11;
        for ($kl=0; $kl < 12; $kl++) {
          // code...
          $array_painc[$kl]=$ini;
          $ini++;
        }


        $total_final=0;
        $cuantos=0;
        $total=0;
        $i=0;
        if($porConsumo == 0){
          foreach($pedidoDetalle  as $det){
            $cantidad = $det->cantidad;
            $descripcion = $det->nombreProducto;
            $monto = $det->precioPedidoDetalle;
            if($det->regaliaPedidoDetalle == "1"){
              $monto = 0.00;
            }
            $subtotal = number_format($monto * $cantidad,2);
            $total += $subtotal;

            //imprimir productos
            if ($i<31) {

              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$cantidad,10,14,"B");
              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$this->quitar_spc($descripcion),15,62,"R");
              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],number_format(($monto/1.13),2),57,63,"L");
              $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],number_format(($subtotal/1.13),2),72,87,"L");
              $cuantos=$cuantos+1;
            }
            $i++;
          }
        } else {
          foreach($pedidoDetalle  as $det){
            $cantidad = $det->cantidad;
            $descripcion = substr($det->nombreProducto,0,22 + $extraEspacio);
            $monto = $det->precioPedidoDetalle;
            if($det->regaliaPedidoDetalle == "1"){
              $monto = 0.00;
            }
            $subtotal = number_format($monto * $cantidad,2);
            $total+= $subtotal;
          }
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],1,10,14,"B");
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],"Cobro Por Consumo",15,62,"R");
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$total,57,63,"L");
          $arrayL[$array_painc[$i]]=$this->p_set($arrayL[$array_painc[$i]],$total,72,87,"L");
          $cuantos=$cuantos+1;

          $i++;
        }
        $retencion = 0;
        $total_final_format=number_format(($total+$propina),2,".","");
        list($entero,$decimal)=explode('.',$total_final_format);
        $enteros_txt=num2letras($entero);
        if(strlen($decimal)==1){
          $decimales_txt=$decimal."0";
        }
        else{
          $decimales_txt=$decimal;
        }
        $cadena_salida_txt= "".$enteros_txt." dolares con ".$decimales_txt."/100 ctvs";

        $array_painc = array(
          0 => 30,
          1 => 31,
        );
        $array_nocon= $this->dtl($cadena_salida_txt,42);

        foreach ($array_nocon as $key => $value) {
          $arrayL[$array_painc[$key]]=$this->p_set($arrayL[$array_painc[$key]],$value,14,60,"R");
        }

        $arrayL[30]= $this->p_set($arrayL[30],number_format(($total/1.13),2),72,87,"L");
        $arrayL[31]= $this->p_set($arrayL[31],number_format(($total - ($total/1.13)),2),72,87,"L");
        $arrayL[32]= $this->p_set($arrayL[32],number_format($total,2),72,87,"L");
        // $arrayL[33]= $this->p_set($arrayL[33],$retencion,72,87,"L");
        // $arrayL[35]= $this->p_set($arrayL[35],"0.00",72,87,"L");
        // $arrayL[36]= $this->p_set($arrayL[36],"0.00",72,87,"L");
        // $arrayL[32]= $this->p_set($arrayL[32],number_format(($total-$retencion),2),72,87,"L");
        $arrayL[37]= $this->p_set($arrayL[37],number_format($propina,2),72,87,"L");
        $arrayL[39]= $this->p_set($arrayL[39],number_format($total+$propina,2),72,87,"L");
      }
      foreach ($arrayL as $key => $value) {
        $info_factura.=$value;
      }

      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = $info_factura;
      $datosRespuesta["tipo"] = $tipo;
      $datosRespuesta["recurso"] = $recurso;
      $datosRespuesta["ip"] = $ip;
      $datosRespuesta["servidor"] = $servidor;


      echo json_encode($datosRespuesta);
    }
  }

  function ImprimirTicketMovimientoCaja($idFactura = ''){
     if (!GblPermisos($this, __FUNCTION__, $this->controlador)) {
       $datosRespuesta["codigo"] = 403;
     } else {
    if ($this->input->method(TRUE) == "POST") {

      $condicionImp = array("idCajaMovimiento" => $idFactura);
      $joinImp = array(
        array(
          "tabla" => "caja",
          "condicion" => "caja.impresoraCaja = impresora.idImpresora",
        ),
        array(
          "tabla" => "cajaMovimiento",
          "condicion" => "cajaMovimiento.idCaja = caja.idCaja",
        ),
      );
      $impresora = TraerUnDatoJoin("impresora",$condicionImp,$joinImp);
      if($impresora){
        $tipo = $impresora->tipoImpresora;
        $recurso = $impresora->recursoCompartidoImpresora;
        $ip = $impresora->IpImpresora;
        $servidor = $impresora->servidorImpresora;
      } else {
        $tipo = "IP";
        $recurso = "/dev/usb/lp0";
        $ip = "192.168.1.150";
        $servidor = base_url();
      }

      $condicion = array("idCajaMovimiento" => $idFactura);
      $movimiento = TraerUnDato("cajaMovimiento",$condicion);

      if($movimiento){


        $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
        $linea = str_pad("",42,"_",STR_PAD_BOTH);
        $divisor = "|";
        $salto = "\n";
        $espacios = 19;

        $relleno = $espacios - strlen("MOVIMIENTO DE CAJA");
        $servicio = $salto.$espacioInicio.str_pad("MOVIMIENTO DE CAJA",$relleno," ",STR_PAD_BOTH).$salto;
        $relleno = $espacios - strlen($movimiento->tipoCajaMovimiento);
        $servicio .= $salto.$espacioInicio.str_pad($movimiento->tipoCajaMovimiento,$relleno," ",STR_PAD_BOTH).$salto.$divisor;
        $servicio .= $linea.$salto;
        $espacios = 35;
        $relleno = $espacios - strlen("Entrega: ".$movimiento->entregaCajaMovimiento);
        $servicio .= $espacioInicio.str_pad("Entrega: ".$movimiento->entregaCajaMovimiento,$relleno," ",STR_PAD_RIGHT).$salto;

        $relleno = $espacios - strlen("Recibe: ".$movimiento->recibeCajaMovimiento);
        $servicio .= $espacioInicio.str_pad("Recibe: ".$movimiento->recibeCajaMovimiento,$relleno," ",STR_PAD_RIGHT).$salto;

        $relleno = $espacios - strlen("Concepto: ".$movimiento->conceptoCajaMovimiento);
        $servicio .= $espacioInicio.str_pad("Concepto: ".$movimiento->conceptoCajaMovimiento,$relleno," ",STR_PAD_RIGHT).$salto;

        $relleno = $espacios - strlen("Monto: ".$movimiento->montoCajaMovimiento);
        $servicio .= $espacioInicio.str_pad("Monto: ".$movimiento->montoCajaMovimiento,$relleno," ",STR_PAD_RIGHT).$salto;

        $servicio .= $linea.$salto;
        $servicio .= $salto;
        $servicio .= $salto;
        $servicio .= $salto;

        $relleno = $espacios - strlen("Firma:_____________________________");
        $servicio .= $espacioInicio.str_pad("Firma:_____________________________",$relleno," ",STR_PAD_RIGHT).$salto;

        $servicio .= $salto;

        $datosRespuesta["codigo"] = 200;
        $datosRespuesta["servicio"] = $servicio;
        //$datosRespuesta["datos"] = $datos;
        $datosRespuesta["tipo"] = $tipo;
        $datosRespuesta["recurso"] = $recurso;
        $datosRespuesta["ip"] = $ip;
        $datosRespuesta["servidor"] = $servidor;
      }
      echo json_encode($datosRespuesta);
    }
    }
  }


  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////
  function ImprimirCuenta(){
    if ($this->input->method(TRUE) == "POST") {
      $extraEspacio = 0;
      $idPedido = $this->input->post("idPedido");
      $prop = $this->input->post("prop");
      $descuento = "";

      /*******************************************************/
      $propina = 0;
      $propinaCalculada = 0;
      $cobroPropina = GblTraerConfiguracion("cobroPropina");
      if($cobroPropina == "Si"){
        $propina = GblTraerConfiguracion("valorPropina");
      }
      /******************************************************/

      /******************************************************/
      $impresoras = TraerDatos("impresora",array("estadoImpresora"=>"Activo","cuentaImpresora"=>"1","idSucursalImpresora" => $this->session->idSucursal));
      $arrayImpresoras = array();
      if($impresoras){
        foreach($impresoras  as $im){
          $datos = array(
            "idImpresora" => $im->idImpresora,
            "nombreImpresora" => $im->nombreImpresora,
            "recursoCompartido" => $im->recursoCompartidoImpresora,
            "IpImpresora" => $im->IpImpresora,
            //"impresoraRed" => GblTraerConfiguracion("impresionEnRed")
            "tipo" => $im->tipoImpresora,
          );
          $servidor = $im->servidorImpresora;
          $arrayImpresoras[$im->idImpresora] = $datos;
        }
      }
      /*******************************************************/



      $condicionPedido = array("idPedido"=>$idPedido);
      $joinPedido = array(
        array(
          "tabla" => "usuario",
          "condicion" => "pedido.idUsuarioPedido  = usuario.idUsuario",
          "tipo" => "left",
          "campos" => "nombreUsuario"
        ),
        array(
          "tabla" => "zonaMesa",
          "condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
          "tipo" => "left",
          "campos" => "nombreZonaMesa"
        ),
      );
      $pedido = TraerUnDatoJoin("pedido",$condicionPedido,$joinPedido);

      $condicionPedDet = array("idPedido"=>$idPedido,"estadoPedidoDetalle !="=>"Borrado");
      $joinDet = array(
        array(
          "tabla" => "producto",
          "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
          "tipo" => "inner",
          "campos" => "nombreProducto,SUM(cantidadPedidoDetalle) AS cantidad"
        ),
      );
      $pedidoDetalle = TraerDatosJoin("pedidoDetalle",$condicionPedDet,"",$joinDet,"pedidoDetalle.idProductoPedidoDetalle, regaliaPedidoDetalle, precioPedidoDetalle");

      /*******************************************************/
      $tipoCargoZona = $pedido->tipoAumentoPedido;
      $cargoZona = $pedido->aumentoPedido;
      $cargoZonaMonto = 0;
      if($cobroPropina == "Si"){
        $propina = GblTraerConfiguracion("valorPropina");
      }
      /******************************************************/

      if($impresoras !== null){
        foreach($impresoras as $i){
          $idImpresora = $i->idImpresora;
          $nombreImpresora = $i->nombreImpresora;

          //-----------------------------------------------------------//
          $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
          $linea = str_pad("",42+$extraEspacio,"_",STR_PAD_BOTH);
          $divisor = "|";
          $salto = "\n";
          $espacios = 19;
          //-----------------------------------------------------------//

          // $relleno = $espacios - strlen("".$nombreImpresora);
          // $cuenta = $salto.$espacioInicio.str_pad("".$nombreImpresora,$relleno," ",STR_PAD_BOTH).$salto;
          $relleno = $espacios - strlen("CUENTA ".str_pad($pedido->idPedido,6,"0",STR_PAD_LEFT));
          $cuenta = $salto.$espacioInicio.str_pad("CUENTA ".str_pad($pedido->idPedido,6,"0",STR_PAD_LEFT),$relleno," ",STR_PAD_BOTH).$salto;



          $espacios = 19;

          // $relleno = $espacios - strlen("FECHA: ".strtoupper($pedido->fechaPedido));
          // $cuenta .= $espacioInicio.str_pad("FECHA: ".strtoupper($pedido->fechaPedido),$relleno," ",STR_PAD_RIGHT).$salto;

          // $relleno = $espacios - strlen("HORA: ".$pedido->horaPedido);
          // $cuenta .= $espacioInicio.str_pad("HORA: ".$pedido->horaPedido,$relleno," ",STR_PAD_RIGHT).$salto;
          $cuenta .= $divisor;

          $relleno = $espacios - strlen("TIPO: ".strtoupper($pedido->tipoCuentaPedido));
          $cuenta .= $espacioInicio.str_pad("TIPO: ".strtoupper($pedido->tipoCuentaPedido),$relleno," ",STR_PAD_BOTH).$salto;

          $relleno = $espacios - strlen("MESERO: ".$pedido->nombreUsuario);
          $cuenta .= $espacioInicio.str_pad("MESERO: ".$pedido->nombreUsuario,$relleno," ",STR_PAD_RIGHT).$salto;

          if($pedido->tipoCuentaPedido == 'local'){
            $relleno = $espacios - strlen("ZONA/MESA: ".$pedido->zonaPedido." - "."MESA # ".$pedido->nombreZonaMesa);
            $cuenta .= $espacioInicio.str_pad("ZONA/MESA: ".$pedido->zonaPedido." - "."MESA # ".$pedido->nombreZonaMesa,$relleno," ",STR_PAD_RIGHT).$salto;
            // $relleno = $espacios - strlen("MESA # ".$pedido->nombreZonaMesa);
            // $cuenta .= $espacioInicio.str_pad("MESA # ".$pedido->nombreZonaMesa,$relleno," ",STR_PAD_RIGHT).$salto;
          } else {

          }
          if($pedido->personasPedido != "" && $pedido->personasPedido>0){
            $relleno = $espacios - strlen("No. DE PERSONAS: ".$pedido->personasPedido);
            $cuenta .= $espacioInicio.str_pad("No. DE PERSONAS: ".$pedido->personasPedido,$relleno," ",STR_PAD_RIGHT).$salto;
          }
          $espacios = 35;

          $cuenta .= $linea.$salto;
          $espacioExtra = str_pad("",$extraEspacio," ",STR_PAD_BOTH);
          $relleno = $espacios - strlen("CANT. DETALLE              ".$espacioExtra."P.U   SUBTOTAL");
          $cuenta .= $espacioInicio.str_pad("CANT. DETALLE              ".$espacioExtra."P.U   SUBTOTAL",$relleno," ",STR_PAD_RIGHT).$salto;
          $cuenta .= $linea.$salto;
          //---------------------//
          $total = 0;
          $espacioCant = 4;
          $espacioDes = 22 + $extraEspacio;
          $espacioPu = 7;
          $espacioTot = 7;
          //----------------------//
          if($pedidoDetalle){
            $productos =  array();
            foreach($pedidoDetalle  as $det){
              if($det->regaliaPedidoDetalle == "1"){
                $monto = 0.00;
              }else{
                $monto = $det->precioPedidoDetalle;
              }

              $relleno = "";
              $cantidad = $det->cantidad;
              $descripcion = substr($det->nombreProducto,0,22+$extraEspacio);
              $monto = $det->precioPedidoDetalle;
              if($det->regaliaPedidoDetalle == "1"){
                $monto = 0.00;
              }
              //$monto = $det->precioPedidoDetalle;
              $subtotal = number_format($monto * $cantidad,2);
              $total+= $subtotal;
              $cuenta .= $espacioInicio.str_pad($cantidad,$espacioCant," ",STR_PAD_RIGHT);
              $cuenta .= str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT);
              $cuenta .= str_pad(number_format($monto,2),$espacioPu," ",STR_PAD_LEFT);
              $cuenta .= str_pad(number_format($subtotal,2),$espacioTot," ",STR_PAD_LEFT).$salto;
              $modificadores = $this->llamarModificadores($det->idPedidoDetalle,8);
              //$cuenta .=  $salto;
              //$cuenta .= str_pad($modificadores,0," ",STR_PAD_RIGHT);
              //$cuenta .=  $salto;
            }
            ////////////////////////////////////////////////////////////////////////////////////
            if($prop == ""){
              if($propina > 0){
                $propinaCalculada = round(($total * ($propina / 100)),2);
                $relleno = 15;//strlen("PROPINA: ");
                $cuenta .= $espacioInicio.str_pad("SUMAS: ",$relleno," ",STR_PAD_RIGHT);
                $relleno = 24 + $extraEspacio;//strlen("$".number_format($factura->propinaFactura,2));
                $cuenta .= $espacioInicio.str_pad("$".number_format($total,2),$relleno," ",STR_PAD_LEFT).$salto;
                ////////////////////////////////////////////////////////////////////////////////////
                $relleno = 15;//strlen("PROPINA: ");
                $cuenta .= $espacioInicio.str_pad("PROPINA (".$propina."%): ",$relleno," ",STR_PAD_RIGHT);
                $relleno = 24 + $extraEspacio;//strlen("$".number_format($factura->propinaFactura,2));
                $cuenta .= $espacioInicio.str_pad("$".number_format($propinaCalculada,2),$relleno," ",STR_PAD_LEFT).$salto;
                ////////////////////////////////////////////////////////////////////////////////////
              }
            }

            ////////////////////////////////////////////////////////////////////////////////////
            if($tipoCargoZona == "Monto"){
              $cargoZonaMonto = $cargoZona;
            }
            if($tipoCargoZona == "Porcentaje"){
              $cargoZonaMonto = $cargoZona * $total / 100;
            }
            $relleno = 15;//strlen("PROPINA: ");
            $cuenta .= $espacioInicio.str_pad("CARGO ZONA: ",$relleno," ",STR_PAD_RIGHT);
            $relleno = 24 + $extraEspacio;//strlen("$".number_format($factura->propinaFactura,2));
            $cuenta .= $espacioInicio.str_pad("$".number_format($cargoZonaMonto,2),$relleno," ",STR_PAD_LEFT).$salto;
            ////////////////////////////////////////////////////////////////////////////////////


            ////////////////////////////////////////////////////////////////////////////////////
            $relleno = 15;//strlen("TOTAL: ");
            $cuenta .= $espacioInicio.str_pad("TOTAL: ",$relleno," ",STR_PAD_RIGHT);
            $relleno = 24 + $extraEspacio;//strlen("$".number_format($total,2));
            $cuenta .= $espacioInicio.str_pad("$".number_format($total + $propinaCalculada + $cargoZonaMonto ,2),$relleno," ",STR_PAD_LEFT).$salto;
            ////////////////////////////////////////////////////////////////////////////////////


            $arrayImpresoras[$idImpresora]["productos"] = urlencode($cuenta);
          }
          else{
            $arrayImpresoras[$idImpresora]["productos"] = "";
            //unset($arrayImpresoras[$idImpresora]);
          }
        }
      } else {
        $datosRespuesta["codigo"] = 200;

      }


      // $idImpresora = TraerUnDatoIndividual("caja","impresoraCaja",array("idCaja"=>$factura->idCajaFactura))[0]["impresoraCaja"];
      // $impresora = TraerUnDato("impresora",array("idImpresora"=>$idImpresora));
      // $datos["recursoCompartido"] = $impresora->recursoCompartidoImpresora;
      // $datos["IpImpresora"] = $impresora->IpImpresora;
      // $datos["idImpresora"] = $impresora->idImpresora;
      // $datos["nombreImpresora"] = $impresora->nombreImpresora;
      // $datos["impresoraRed"] =  GblTraerConfiguracion("impresionEnRed");
      $datos['cuenta'] = urlencode($cuenta);
      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = $arrayImpresoras;
      $datosRespuesta["servidor"] = $servidor;
    }
    echo json_encode($datosRespuesta);
  }

  
  function ImprimirComandaCocina(){
    if ($this->input->method(TRUE) == "POST") {
      $idPedido = $this->input->post("idPedido");
      $impresoras = TraerDatos("impresora",array('cocinaImpresora'=>'1',"estadoImpresora"=>"Activo","idSucursalImpresora" => $this->session->idSucursal));
      $arrayImpresoras = array();
      $arrayServidor = array();
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
          $arrayImpresoras[$im->idImpresora] = $datos;
        }
      }
      //////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////
      // $impresoraDf = TraerUnDato("impresora",array('generalImpresora'=>'1',"estadoImpresora"=>"Activo","idSucursalImpresora" => $this->session->idSucursal));
      // $datos = array(
      //   "idImpresora" => $impresoraDf->idImpresora,
      //   "nombreImpresora" => $impresoraDf->nombreImpresora,
      //   "recursoCompartido" => $impresoraDf->recursoCompartidoImpresora,
      //   "IpImpresora" => $impresoraDf->IpImpresora,
      //   "impresoraTipo" => $impresoraDf->tipoImpresora
      // );
      // $arrayServidor[$impresoraDf->idImpresora]['servidor'] = $impresoraDf->servidorImpresora;
      // $arrayImpresoras[$impresoraDf->idImpresora] = $datos;
      //////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////

      $condicionPedido = array("idPedido"=>$idPedido);
      $join = array(
        array(
          "tabla"=>"zonaMesa",
          "condicion" => "pedido.idMesaPedido = zonaMesa.idZonaMesa",
          "tipo" => "left",
          "campo" => "nombreZonaMesa"
        ),
        array(
          "tabla"=>"usuario",
          "condicion" => "usuario.idUsuario = '".$this->session->idUsuario."'",
          "tipo" => "inner",
          "campo" => "nombreUsuario"
        ),
      );
      $pedido = TraerUnDatoJoin("pedido",$condicionPedido,$join);
      $comentarioPedido = TraerDatos("pedidoComentario",array("idPedido"=>$idPedido,"estadoPedidoComentario"=>"Activo"));

      $cuentaGlobal = '';
      $iter = 0;
      if($impresoras){
        foreach($impresoras as $i){
          $idImpresora = $i->idImpresora;
          $nombreImpresora = $i->nombreImpresora;
          $condicionPedDet = array("idPedido"=>$idPedido,"producto.impresoraProducto"=>$idImpresora,"pedidoDetalle.impreso" => 0);
          $joinDet = array(
            array(
              "tabla" => "producto",
              "condicion" =>"producto.idProducto = pedidoDetalle.idProductoPedidoDetalle",
              "tipo" => "inner",
              "campos" => "nombreProducto"
            )
          );
          $pedidoDetalle = TraerDatosJoin("pedidoDetalle",$condicionPedDet,"pedidoDetalle.grupoPedidoDetalle",$joinDet);
          $condicionPedDetLlevar = array("idPedido"=>$idPedido,"producto.impresoraProducto"=>$idImpresora,"pedidoDetalle.impreso" => 0,"pedidoDetalle.llevarLocalPedidoDetalle" => 1);
          $existeLlevar = TraerDatosJoin("pedidoDetalle",$condicionPedDetLlevar,"pedidoDetalle.grupoPedidoDetalle",$joinDet);

          $impresoraCorrelativo = TraerUnDato("impresora",array("idImpresora"=>$idImpresora));
          //-----------------------------------------------------------//
          $espacioInicio = str_pad(" ",1," ",STR_PAD_BOTH);
          $linea = str_pad("",42,"_",STR_PAD_BOTH);
          $divisor = "|";
          $salto = "\n";
          $cuenta = '';
          //-----------------------------------------------------------//
          // El nombre de la impresora va primero: es lo unico que se imprime
          // centrado/grande (encabezado), todo lo demas va como detalle
          // alineado a la izquierda a tamano normal — por eso debe quedar
          // ANTES del divisor "|" (que separa encabezado de detalle en
          // imprimir/printComanda.php).
          $espacios = 15;
          if($pedido->tipoCuentaPedido != 'local') {
            $relleno = $espacios - strlen("".$nombreImpresora);
            $cuenta .= $salto.$espacioInicio.str_pad("".$nombreImpresora,$relleno," ",STR_PAD_BOTH).$salto;
            if($iter == 0) $cuentaGlobal .= $salto.$espacioInicio.str_pad("".$nombreImpresora,$relleno," ",STR_PAD_BOTH).$salto;
            $cuenta .= $divisor;
            if($iter == 0) $cuentaGlobal .= $divisor;
          } else {
            $relleno = $espacios - strlen("".$nombreImpresora);
            $cuenta .= $salto.$espacioInicio.str_pad("".$nombreImpresora,$relleno," ",STR_PAD_BOTH).$salto;
            if($iter == 0) $cuentaGlobal .= $salto.$espacioInicio.str_pad("".$nombreImpresora,$relleno," ",STR_PAD_BOTH).$salto;

            $cuenta .= $divisor;
            if($iter == 0) $cuentaGlobal .= $divisor;

            $relleno = $espacios - strlen("ZONA/MESA: ".$pedido->zonaPedido." - "."MESA # ".$pedido->nombreZonaMesa);
            $cuenta .= $espacioInicio.str_pad("ZONA/MESA: ".$pedido->zonaPedido." - "."MESA # ".$pedido->nombreZonaMesa,$relleno," ",STR_PAD_RIGHT).$salto;
            if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("ZONA/MESA: ".$pedido->zonaPedido." - "."MESA # ".$pedido->nombreZonaMesa,$relleno," ",STR_PAD_RIGHT).$salto;
          }

          $espacios = 25;
          $relleno = $espacios - strlen("N: ".strtoupper($impresoraCorrelativo->correlativoImpresora + 1));
          $cuenta .= $espacioInicio.str_pad("N: ".strtoupper($impresoraCorrelativo->correlativoImpresora + 1),$relleno," ",STR_PAD_LEFT).$salto;
          $espacios = 5;
          if($existeLlevar){
            if($pedido->tipoCuentaPedido == 'local'){
              $relleno = $espacios - strlen("TIPO: LLEVAR");
              $cuenta .= $espacioInicio.str_pad("TIPO: LLEVAR",$relleno," ",STR_PAD_RIGHT).$salto;
              if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("TIPO: LLEVAR",$relleno," ",STR_PAD_RIGHT).$salto;
            } else {
              $relleno = $espacios - strlen("TIPO: ".strtoupper($pedido->tipoCuentaPedido));
              $cuenta .= $espacioInicio.str_pad("TIPO: ".strtoupper($pedido->tipoCuentaPedido),$relleno," ",STR_PAD_RIGHT).$salto;
              if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("TIPO: ".strtoupper($pedido->tipoCuentaPedido),$relleno," ",STR_PAD_RIGHT).$salto;
            }
          } else {
            $relleno = $espacios - strlen("TIPO: ".strtoupper($pedido->tipoCuentaPedido));
            $cuenta .= $espacioInicio.str_pad("TIPO: ".strtoupper($pedido->tipoCuentaPedido),$relleno," ",STR_PAD_RIGHT).$salto;
            if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("TIPO: ".strtoupper($pedido->tipoCuentaPedido),$relleno," ",STR_PAD_RIGHT).$salto;
          }

          $espacios = 15;
          if($i->pagoImpresora == "1"){
            $estado = ($pedido->estadoPedido == "Finalizado") ? "PAGADO" : "PENDIENTE";
            $relleno = $espacios - strlen("".$estado);
            $cuenta .= $espacioInicio.str_pad("".$estado,$relleno," ",STR_PAD_RIGHT).$salto;
            if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("".$estado,$relleno," ",STR_PAD_RIGHT).$salto;
          }


          $espacios = 35;

          $relleno = $espacios - strlen("FECHA: ".date("d-m-Y"));
          $cuenta .= $espacioInicio.str_pad("FECHA: ".date("d-m-Y"),$relleno," ",STR_PAD_RIGHT).$salto;

          $relleno = $espacios - strlen("HORA: ".date("h:i A"));
          $cuenta .= $espacioInicio.str_pad("HORA: ".date("h:i A"),$relleno," ",STR_PAD_RIGHT).$salto;

          $relleno = $espacios - strlen("MESERO: ".$pedido->nombreUsuario);
          $cuenta .= $espacioInicio.str_pad("MESERO: ".$pedido->nombreUsuario,$relleno," ",STR_PAD_RIGHT).$salto;
          if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("MESERO: ".$pedido->nombreUsuario,$relleno," ",STR_PAD_RIGHT).$salto;

          if($pedido->tipoCuentaPedido == 'llevar'){
            $relleno = $espacios - strlen("CLIENTE: ".$pedido->nombreClientePedido);
            $cuenta .= $salto.$espacioInicio.str_pad("CLIENTE: ".$pedido->nombreClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;
            if($iter == 0) $cuentaGlobal .= $salto.$espacioInicio.str_pad("CLIENTE: ".$pedido->nombreClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;

            $relleno = $espacios - strlen("DIRECCION: ".$pedido->direccionClientePedido);
            $cuenta .= $espacioInicio.str_pad("DIRECCION: ".$pedido->direccionClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;
            if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("DIRECCION: ".$pedido->direccionClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;

            $clientedata = $this->core->TraerUnDato('cliente',['idCliente' => $pedido->idCliente]);
  					if($clientedata)
  					{
              $relleno = $espacios - strlen("TELFONO: ".$clientedata->telefonoCliente);
              $cuenta .= $espacioInicio.str_pad("TELFONO: ".$clientedata->telefonoCliente,$relleno," ",STR_PAD_RIGHT).$salto;
              if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("TELFONO: ".$clientedata->telefonoCliente,$relleno," ",STR_PAD_RIGHT).$salto;
  					}

          }

          if($pedido->tipoCuentaPedido == 'domicilio'){
            $relleno = $espacios - strlen("CLIENTE: ".$pedido->nombreClientePedido);
            $cuenta .= $salto.$espacioInicio.str_pad("CLIENTE: ".$pedido->nombreClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;
            if($iter == 0) $cuentaGlobal .= $salto.$espacioInicio.str_pad("CLIENTE: ".$pedido->nombreClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;

            $relleno = $espacios - strlen("DIRECCION: ".$pedido->direccionClientePedido);
            $cuenta .= $espacioInicio.str_pad("DIRECCION: ".$pedido->direccionClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;
            if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("DIRECCION: ".$pedido->direccionClientePedido,$relleno," ",STR_PAD_RIGHT).$salto;

            $clientedata = $this->core->TraerUnDato('cliente',['idCliente' => $pedido->idCliente]);
            if($clientedata)
  					{
              $relleno = $espacios - strlen("TELFONO: ".$clientedata->telefonoCliente);
              $cuenta .= $espacioInicio.str_pad("TELFONO: ".$clientedata->telefonoCliente,$relleno," ",STR_PAD_RIGHT).$salto;
              if($iter == 0) $cuentaGlobal .= $espacioInicio.str_pad("TELFONO: ".$clientedata->telefonoCliente,$relleno," ",STR_PAD_RIGHT).$salto;
  					}

          }

          if($pedido->tipoCuentaPedido == 'llevar'||$pedido->tipoCuentaPedido == 'domicilio')
          {

          }

          $cuenta .= $linea.$salto;
          $cuenta .= $espacioInicio."CANT. DETALLE                           ".$salto;
          $cuenta .= $linea.$salto;

          if($iter == 0) $cuentaGlobal .= $linea.$salto;
          if($iter == 0) $cuentaGlobal .= $espacioInicio."CANT. DETALLE                           ".$salto;
          if($iter == 0) $cuentaGlobal .= $linea.$salto;
          //---------------------//
          $total = 0;
          $espacioCant = 5;
          $espacioDes = 17;
          $espacioPu = 7;
          $espacioTot = 10;
          //----------------------//
          if($pedidoDetalle){
            $productos =  array();
            $grupoGeneral = 0;
            $grupoActual = 0;
            $cont = 0;
            foreach($pedidoDetalle  as $det){
              $relleno = "";
              $idCocinero = $det->senoritaPedidoDetalle;
              $grupo = $det->grupoPedidoDetalle;
              $cocinero = TraerUnDato("usuario",array("idUsuario" => $idCocinero));
              $cantidad = $det->cantidadPedidoDetalle;
              $descripcion = substr($det->nombreProducto,0,40);
              $comentario = substr($det->comentarioPedidoDetalle,0,26);
              $comentario = wordwrap($det->comentarioPedidoDetalle,38,$salto."");
              // for($k == 0 ; $k < ){}
              $modificadores = $this->llamarModificadores($det->idPedidoDetalle,5);

              if($cont != 0){
                if($grupoActual != $grupo){
                  $cuenta .=  $linea.$salto;
                  $cuentaGlobal .=  $linea.$salto;
                }
              }

              $cuenta .= $espacioInicio.str_pad($cantidad,$espacioCant," ",STR_PAD_RIGHT);
              $cuentaGlobal .= $espacioInicio.str_pad($cantidad,$espacioCant," ",STR_PAD_RIGHT);
              $cuenta .= str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT).$salto;
              $cuentaGlobal .= str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT).$salto;

              if($pedido->tipoCuentaPedido == 'llevar'||$pedido->tipoCuentaPedido == 'domicilio')
              {
                $cuenta .= str_pad("P.U ".$det->precioPedidoDetalle."SUB:  ".number_format($det->precioPedidoDetalle*$cantidad,2),39," ",STR_PAD_RIGHT).$salto;
                $cuentaGlobal .= str_pad("P.U ".$det->precioPedidoDetalle."SUB:  ".number_format($det->precioPedidoDetalle*$cantidad,2),39," ",STR_PAD_RIGHT).$salto;
              }

              $cuenta .= str_pad($modificadores,0," ",STR_PAD_RIGHT);
              $cuentaGlobal .= str_pad($modificadores,0," ",STR_PAD_RIGHT);
              if($cocinero){
                $cuenta .= $espacioInicio.str_pad("COCINERO: ".$cocinero->nombreUsuario,$espacioDes," ",STR_PAD_RIGHT).$salto;
                $cuentaGlobal .= $espacioInicio.str_pad("COCINERO: ".$cocinero->nombreUsuario,$espacioDes," ",STR_PAD_RIGHT).$salto;
              }
              if($det->comentarioPedidoDetalle != ""){
                $cuenta .= $espacioInicio.str_pad("COMENTARIO:",$espacioDes," ",STR_PAD_RIGHT).$salto;
                $cuentaGlobal .= $espacioInicio.str_pad("COMENTARIO:",$espacioDes," ",STR_PAD_RIGHT).$salto;
                $cuenta .= $espacioInicio.str_pad($comentario,$espacioDes," ",STR_PAD_RIGHT).$salto;
                $cuentaGlobal .= $espacioInicio.str_pad($comentario,$espacioDes," ",STR_PAD_RIGHT).$salto;
                $cuenta .=  $salto;
                $cuentaGlobal .=  $salto;
              }

              $grupoActual = $grupo;
              $cont ++ ;
              EditarDatos("pedidoDetalle",array('impreso'=>1),array("idPedidoDetalle" => $det->idPedidoDetalle));
            }

            $arrayImpresoras[$idImpresora]["productos"] = urlencode($cuenta);
            ActualizarCorrelativo("impresora",array("idImpresora"=>$idImpresora),"correlativoImpresora",1);
          }
          else{
            $arrayImpresoras[$idImpresora]["productos"] = "";
          }
          if($comentarioPedido){
            $comGen =  $linea.$salto;
            $comGen .= $espacioInicio.str_pad("COMENTARIO GENERAL",$espacioDes,"-",STR_PAD_BOTH).$salto;
            $comGen .=  $linea.$salto;
            foreach($comentarioPedido  as $com){
              $comentario = substr(" - ".$com->comentarioPedidoComentario,0,26);
              $comentario = wordwrap(" - ".$com->comentarioPedidoComentario,38,$salto." ");

              $comGen .= $espacioInicio.str_pad($comentario,$espacioDes," ",STR_PAD_RIGHT).$salto;
              $comGen .=  $salto;

              EditarDatos("pedidoComentario",array('estadoPedidoComentario'=>'Inactivo'),array("idPedidoComentario" => $com->idPedidoComentario));
            }
            $arrayImpresoras[$idImpresora]["comentario"] = $comGen;
          } else {
            $arrayImpresoras[$idImpresora]["comentario"] = "";
          }
          $arrayServidor[$idImpresora]["datos"][$idImpresora] = $arrayImpresoras[$idImpresora];
          $iter++;
        }
      }
      /************************************************************/
      /************************************************************/
      // $arrayImpresoras[$impresoraDf->idImpresora]["productos"] = urlencode($cuentaGlobal);
      // $arrayImpresoras[$impresoraDf->idImpresora]["comentario"] = "";
      // $arrayServidor[$impresoraDf->idImpresora]["datos"][$impresoraDf->idImpresora] = $arrayImpresoras[$impresoraDf->idImpresora];
      /************************************************************/
      /************************************************************/

      $datosRespuesta["codigo"] = 200;
      $datosRespuesta["datos"] = json_encode($arrayImpresoras);
      $datosRespuesta["servidor"] = json_encode($arrayServidor);

           echo json_encode($datosRespuesta);
    }
  }
  public function llamarModificadores($idPedidoDetalle = 0,$espacio = 0,$idReferencia = 0){

    $espacioInicio = str_pad(" ",$espacio," ",STR_PAD_BOTH);
    $modText = "";
    //$espacio = 5;
    $salto = "\n";
    $espacioDes = 40 - $espacio;
    $mod = array();
    $modificadores = TraerDatos("pedidoSubDetalle",array("idPedidoDetalle"=>$idPedidoDetalle,"idReferenciaPedidoSubDetalle" => $idReferencia));
    if($modificadores){
      $con = 0;
      if($con < 1){
        foreach($modificadores as $m){
          $relleno = "";
          $nombre = ($m->nombrePedidoSubDetalle != "0") ? $m->nombreModTipoPedidoSubDetalle.' - '.$m->nombrePedidoSubDetalle.'' : '';
          $descripcion = substr($nombre,0,$espacioDes);
          $modText .= $espacioInicio.str_pad($descripcion,$espacioDes," ",STR_PAD_RIGHT).$salto;
          if($m->variosPedidoSubDetalle == "1"){
            $modText .= $this->llamarModificadores($idPedidoDetalle , ($espacio + 3) ,$m->idPedidoSubDetalle);
          }
        }
      }
      $con ++;
    }
    return $modText;
    // return $mod;
	}
  ///////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////

}	
/* End of file Cargos.php */
