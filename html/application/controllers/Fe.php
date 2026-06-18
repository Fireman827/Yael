<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fe extends CI_Controller
{

  private $controlador = "Fe";
  function __construct()
  {
    parent::__construct();
    $this->load->add_package_path(APPPATH . 'third_party/fpdf');
    $this->load->library('venta');
    $this->load->add_package_path(APPPATH . 'third_party/phpqrcode');
    $this->load->library('Qr');
  }
  public function crearDTE($idFactura){
    $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
    if($fe_enabled == "Si"){
      /**********************************************************************/
      $condicionDatos = array('idFactura' => $idFactura);
      $datosVenta = TraerUnDato("factura", $condicionDatos);
      /****************************************************************/
      /****************************************************************/
      $idReferenciaFactura = $datosVenta->idReferenciaFactura;
      $selloRecibido = $datosVenta->selloRecibido;
      $numeroControl = $datosVenta->numeroControl;
      $codigoGeneracion = $datosVenta->codigoGeneracion;
      $tipoDescuento = "Porcentaje";//$datosVenta->tipoDescuentoFactura;
      $ultimoCorrelativo = $datosVenta->numeroDocumentoFactura;
      $fecha = $datosVenta->fechaFactura;
      $hora = $datosVenta->horaFactura;
      $tipoPago = $datosVenta->tipoPagoFactura;
      $condicionPago = 3;
      if($tipoPago == 'Contado'){
        $condicionPago = 1;
      } else if($tipoPago == 'Credito'){
        $condicionPago = 2;
      }
      /****************************************************************/
      /****************************************************************/
      $condicionDatos = array('idCliente' => $datosVenta->idCliente);
      $joinDatosCliente = array(
        array(
          'tabla' => 'FE_CAT_019_CodigodeActividadEco as giros',
          'tipo' => 'left',
          'condicion' => 'giros.codigo=cliente.giroCliente',
          'campos' => 'giros.valores as giro'
        ),
      );
      $datosCliente = TraerUnDatoJoin("cliente", $condicionDatos,$joinDatosCliente);
      $documento = str_replace("-","",$datosCliente->nitCliente);
      $tipoDocumentoCliente = "36";
      if($datosCliente->facturarConCliente == "DUI"){
        $tipoDocumentoCliente = "13";
        $documento = $datosCliente->duiCliente;
      }
      $nombreComercialCliente = $datosCliente->nombreComercialCliente;
      if($datosCliente->nombreComercialCliente == ""){
        $nombreComercialCliente = $datosCliente->nombreCliente;
      }
      $tipoDocumento = $datosVenta->tipoDocumentoFactura;
      if($tipoDocumento == "FAC"){
        $codigoDocumento = "01";
      } else if($tipoDocumento == "CCF") {
        $codigoDocumento = "03";
      }
      $condicionDatosDocumento = array('codigo' => $codigoDocumento);
      $datosDocumento = TraerUnDato("FE_CAT_002_TipodeDocumento", $condicionDatosDocumento);
      $versionDocumento = $datosDocumento->version;

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

      /**********************************************************************/
      /**********************************************************************/
      if($selloRecibido == ""){
        /************************************************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        $productos = array();
        $gravado = 0;
        $descuentos = 0;
        $totalIva = 0;
        $numItem=1;
        $condicionDatosDetalle = array('idPedido' => $idReferenciaFactura,"estadoPedidoDetalle !="=>"Borrado");
        $datosPedidoDetalle = TraerDatos("pedidoDetalle", $condicionDatosDetalle);
        if($datosPedidoDetalle !== null){
          foreach ($datosPedidoDetalle as $pedidoDetalle) {
            $descuento=0;
            $subt_exento=0;
            $subt_nosujeto=0;

            $cantidad = $pedidoDetalle->cantidadPedidoDetalle;
            $descuento = 0;//$pedidoDetalle->descuentoPedidoDetalle;
            if($pedidoDetalle->idProductoPedidoDetalle !=""){
              $condicionDatosProducto = array('producto.idProducto' => $pedidoDetalle->idProductoPedidoDetalle);
              $datosProducto = TraerUnDato("producto", $condicionDatosProducto);
              $descripcion = $datosProducto->nombreProducto;
              $tipoProdServ = 3;
              $uniMedida = 99;
            } else {
              $tipoProdServ = 3;
              $uniMedida = 99;
              $descripcion = $pedidoDetalle->observacion;
            }
            if($cantidad == 0){
              $cantidad =1;
            }
            switch ($codigoDocumento) {
              case '03':
              $precio = $pedidoDetalle->precioPedidoDetalle  / (1 + GblTraerConfiguracionFe("iva"));
              // if($tipoDescuento == "Porcentaje"){
              //   $descuento = ($pedidoDetalle->descuentoPedidoDetalle/100) * $precio;
              // }
              if($pedidoDetalle->regaliaPedidoDetalle){
                $precio=0;
              }
              $subTotal = $cantidad * $precio; //($pedidoDetalle->subTotalIvaPedidoDetalle / (1 + GblTraerConfiguracionFe("iva")));// - ($descuento * $cantidad);
              $subTotalD = 0;//$descuento * $cantidad;

              $tributos = ["20"];
              $productos[] = array(
                'numItem' =>$numItem,
                'tipoItem' =>$tipoProdServ,
                'numeroDocumento' => null,
                'codigo' => null,
                'codTributo' => null,
                'descripcion' => $descripcion,
                'cantidad' => round($cantidad,4),
                'uniMedida' => $uniMedida,
                'precioUni' => round($precio,4),
                'montoDescu' => round($subTotalD,4),
                'ventaNoSuj' => round($subt_nosujeto,4),
                'ventaExenta' => round($subt_exento,4),
                'ventaGravada' => round($subTotal,4),
                'tributos' => $tributos,
                'psv' => 0,
                'noGravado' => 0,
              );
              break;
              case "01":
              $precio = $pedidoDetalle->precioPedidoDetalle;
              $descuento = 0;//$pedidoDetalle->descuentoPedidoDetalle;
              // if($tipoDescuento == "Porcentaje"){
              //   $descuento = ($pedidoDetalle->descuentoPedidoDetalle/100) * $precio;
              // }
              if($pedidoDetalle->regaliaPedidoDetalle){
                $precio=0;
              }
              $subTotal = round(($cantidad * $precio),4);
              $subTotalD = 0;//$descuento * $cantidad;
              $ivaItem = round($subTotal - ($subTotal / ( 1 + GblTraerConfiguracionFe("iva"))),4);

              $tributos = null;
              $productos[] = array(
                'numItem' =>$numItem,
                'tipoItem' =>$tipoProdServ,
                'numeroDocumento' => null,
                'codigo' => null,
                'codTributo' => null,
                'descripcion' => $descripcion,
                'cantidad' => round($cantidad,2),
                'uniMedida' => $uniMedida,
                'precioUni' => round($precio,4),
                'montoDescu' => round($subTotalD,4),
                'ventaNoSuj' => round($subt_nosujeto,4),
                'ventaExenta' => round($subt_exento,4),
                'ventaGravada' => round($subTotal,4),
                'tributos' => $tributos,
                'psv' => 0,
                'noGravado' => 0,
                'ivaItem' => $ivaItem,
              );
              $totalIva += $ivaItem;
              break;
              case "04":
              $precio = $pedidoDetalle->precioPedidoDetalle;
              // if($tipoDescuento == "Porcentaje"){
              //   $descuento = ($pedidoDetalle->descuentoPedidoDetalle/100) * $precio;
              // }
              if($pedidoDetalle->regaliaPedidoDetalle){
                $precio=0;
              }
              $subTotal = $cantidad * $precio;//$pedidoDetalle->subTotalIvaPedidoDetalle-($descuento * $cantidad);
              $tributos = null;
              $subTotalD = 0;//$descuento * $cantidad;

              $ivaItem = round($subTotal - ($subTotal / (1 + GblTraerConfiguracionFe("iva"))),4);
              $productos[] = array(
                'numItem' =>$numItem,
                'tipoItem' =>$tipoProdServ,
                'numeroDocumento' => null,
                'codigo' => null,
                'codTributo' => null,
                'descripcion' => $descripcion,
                'cantidad' => round($cantidad,2),
                'uniMedida' => $uniMedida,
                'precioUni' => round($precio,4),
                'montoDescu' => round($subTotalD,4),
                'ventaNoSuj' => round($subt_nosujeto,4),
                'ventaExenta' => round($subt_exento,4),
                'ventaGravada' => round($subTotal,4),
                'tributos' => $tributos,
              );
              $totalIva += $ivaItem;
              break;
              case "11":
              $precio = $pedidoDetalle->precioPedidoDetalle;
              // if($tipoDescuento == "Porcentaje"){
              //   $descuento = ($pedidoDetalle->descuentoPedidoDetalle/100) * $precio;
              // }
              if($pedidoDetalle->regaliaPedidoDetalle){
                $precio=0;
              }
              $subTotal = $cantidad * $precio;//$pedidoDetalle->subTotalPedidoDetalle-($descuento * $cantidad);
              $subTotalD = 0;//$descuento * $cantidad;
              $tributos = null;
              $productos[] = array(
                'numItem' =>$numItem,
                'codigo' => null,
                'descripcion' => $descripcion,
                'cantidad' => round($cantidad,2),
                'uniMedida' => $uniMedida,
                'precioUni' => round($precio,4),
                'montoDescu' => round($subTotalD,4),
                'ventaGravada' => round($subTotal,4),
                'tributos' => $tributos,
                'noGravado' => 0,
              );
              break;
            }
            $gravado += $subTotal;
            $descuentos += $descuento;
            $numItem ++;
            /*////////////////////////////////////////////////////////*/
          }
        }
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        $nosujeto=0;
        $exento=0;
        $sumas = $nosujeto+$exento+$gravado;
        $desc_nosujeto = round(0,2);
        $desc_exento = round(0,2);
        $desc_gravado = round(0,2);
        $porc_descuento = round(0,2);
        $total_descuento = round($descuentos,2);
        $iva = round($gravado * GblTraerConfiguracionFe("iva"),2);
        $subtotal = round($gravado,2);
        $iva_percibido = round(0,2);
        $iva_retenido = round(0,2);
        $ivaRet = 0;
        if($datosCliente->retieneIvaCliente){
          if($codigoDocumento == "03"){
            $iva_retenido = $gravado * GblTraerConfiguracionFe("ivaRet");
          } else {
            $iva_retenido = ($gravado/(1+GblTraerConfiguracionFe("iva"))) * GblTraerConfiguracionFe("ivaRet");
          }
        }
        $retencion = round(0,2);
        if($datosCliente->retieneRentaCliente){
          if($codigoDocumento == "03"){
            $retencion = $gravado * GblTraerConfiguracionFe("ivaRent");
          } else {
            $retencion = ($gravado/(1+GblTraerConfiguracionFe("iva"))) * GblTraerConfiguracionFe("ivaRent");
          }
        }
        // $retencion = round(0,2);
        $total = round($gravado,2);
        if($codigoDocumento == "03"){
          $total = round($gravado+$iva,2);
        }
        $montoTotal=$total;
        $total = round($total-$iva_retenido,2);
        $total = round($total-$retencion,2);
        //$total = round($gravado-$iva_retenido,2);
        $total_nogravado = round($exento+$nosujeto,2);
        $total_pagar = round($total,2);
        list($entero,$decimal) = explode(".",number_format($total_pagar,2,".",""));
        if($entero > 0){
          $total_letras = num2letras($entero)." ".$decimal."/100";
        } else {
          $total_letras = "CERO ".$decimal."/100";
        }
        $saldo_favor = round(0,2);

        if($numeroControl == ""){
          $numeroControl = generarNumeroControl($codigoDocumento,$ultimoCorrelativo);
        }
        if($codigoGeneracion == ""){
          $codigoGeneracion = generarUuid();
        }
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////

        ////////////////////////////////////////////////////
        if($codigoDocumento == "11"){
          $identificacion = array (
            'version' => round($versionDocumento,0),
            'ambiente' => GblTraerConfiguracionFe("ambiente"),
            'tipoDte' => $codigoDocumento,
            'numeroControl' => $numeroControl,
            'codigoGeneracion' => $codigoGeneracion,
            'tipoModelo' => round(GblTraerConfiguracionFe("modeloFacturacion"),0),
            'tipoOperacion' => 1,
            'tipoContingencia' => null,
            'motivoContigencia' => null,
            'fecEmi' => $fecha,
            'horEmi' => $hora,
            'tipoMoneda' => 'USD',
          );
        } else {
          $identificacion = array (
            'version' => round($versionDocumento,0),
            'ambiente' => GblTraerConfiguracionFe("ambiente"),
            'tipoDte' => $codigoDocumento,
            'numeroControl' => $numeroControl,
            'codigoGeneracion' => $codigoGeneracion,
            'tipoModelo' => round(GblTraerConfiguracionFe("modeloFacturacion"),0),
            'tipoOperacion' => 1,
            'tipoContingencia' => null,
            'motivoContin' => null,
            'fecEmi' => $fecha,
            'horEmi' => $hora,
            'tipoMoneda' => 'USD',
          );
        }
        $emisor = array(
          'nit' =>GblTraerConfiguracionFe('nitEmisor'),
          'nrc' => GblTraerConfiguracionFe('nrcEmisor'),
          'nombre' =>GblTraerConfiguracionFe('nombreEmisor'),
          'codActividad' =>GblTraerConfiguracionFe('codGiroEmisor'),
          'descActividad' => GblTraerConfiguracionFe('giroEmisor'),
          'nombreComercial' =>  GblTraerConfiguracionFe('nombreComercialEmisor'),
          'tipoEstablecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
          'direccion' =>
          array (
            'departamento' => GblTraerConfiguracionFe('departamentoEmisor'),
            'municipio' => GblTraerConfiguracionFe('municipioEmisor'),
            'complemento' => GblTraerConfiguracionFe('direccionEmisor'),
          ),
          'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
          'correo' => GblTraerConfiguracionFe('correoEmisor'),
        );
        if($codigoDocumento == "03"){
          $receptor = array (
            'nit' => str_replace("-","",$documento),
            'nrc' => str_replace("-","",$datosCliente->nrcCliente),
            'nombre' => $datosCliente->nombreCliente,
            'codActividad' => $datosCliente->giroCliente,
            'descActividad' => $datosCliente->giro,
            'nombreComercial' => $nombreComercialCliente,
            'direccion' =>
            array (
              'departamento' => $datosCliente->departamentoCliente,
              'municipio' => $datosCliente->municipioCliente,
              'complemento' => $datosCliente->direccionCliente,
            ),
            'telefono' => $datosCliente->telefonoCliente,
            'correo' => $datosCliente->emailCliente,
          );
        } else if ($codigoDocumento == "01"){
          // if($total > 200){
          $giroCliente = $datosCliente->giroCliente;
          $giroTxt = $datosCliente->giro;
          if($documento == ""){
            $tipoDocumentoCliente = "37";
            $documento = "000000000";
          }
          if($datosCliente->giro == ""){
            $giroTxt=null;
          }
          if($datosCliente->giroCliente == ""){
            $giroCliente=null;
          }
          $telefonoCliente = $datosCliente->telefonoCliente;
          if($datosCliente->telefonoCliente == ""){
            $telefonoCliente = "0000-0000";
          }
          $emailCliente = $datosCliente->emailCliente;
          if($datosCliente->emailCliente == ""){
            $emailCliente = GblTraerConfiguracionFe("correoEmisor");
          }
          // $nrcCliente = str_replace("-","",$datosCliente->nrcCliente);
          // if($datosCliente->nrcCliente == ""){
          $nrcCliente = null;
          // }
          $receptor = array (
            'tipoDocumento' => $tipoDocumentoCliente,
            'numDocumento' => $documento,
            'nrc' => $nrcCliente,
            'nombre' => $datosCliente->nombreCliente,
            'codActividad' => $giroCliente,
            'descActividad' => $giroTxt,
            'direccion' =>
            array (
              'departamento' => $datosCliente->departamentoCliente,
              'municipio' => $datosCliente->municipioCliente,
              'complemento' => $datosCliente->direccionCliente,
            ),
            'telefono' => $telefonoCliente,
            'correo' => $emailCliente,
          );
          // } else {
          //   $receptor = null;
          // }
        } else if($codigoDocumento == "04"){
          $receptor = array (
            'tipoDocumento' => $tipoDocumentoCliente,
            'numDocumento' => $documento,
            'nrc' => str_replace("-","",$datosCliente->nrcCliente),
            'nombre' => $datosCliente->nombreCliente,
            'bienTitulo' => "",
            'codActividad' => $datosCliente->giroCliente,
            'descActividad' => $datosCliente->giro,
            'nombreComercial' => $datosCliente->nombreComercialCliente,
            'direccion' =>
            array (
              'departamento' => $datosCliente->departamentoCliente,
              'municipio' => $datosCliente->municipioCliente,
              'complemento' => $datosCliente->direccionCliente,
            ),
            'telefono' => $datosCliente->telefonoCliente,
            'correo' => $datosCliente->emailCliente,
          );
        } else if($codigoDocumento == "11"){
          $receptor = array (
            'tipoDocumento' => $tipoDocumentoCliente,
            'numDocumento' => str_replace("-","",$documento),
            'nombreComercial' => $datosCliente->nombreComercialCliente,
            'nombre' => $datosCliente->nombreCliente,
            'descActividad' => $datosCliente->giro,
            'nombreComercial' => $datosCliente->nombreComercialCliente,
            'complemento' => $datosCliente->direccionCliente,
            'telefono' => $datosCliente->telefonoCliente,
            'tipoPersona' => 1,
            'correo' => $datosCliente->emailCliente,
            'codPais' => "9333",
            'nombrePais' => "AUSTRALIA",
          );
        }
        if($codigoDocumento == "03" || $codigoDocumento == "01" ||  $codigoDocumento == "04" ||  $codigoDocumento == "11"){
          $emisor['codEstableMH'] = GblTraerConfiguracionFe("codEstableMH");
          $emisor['codEstable'] = GblTraerConfiguracionFe("codEstableMH");
          $emisor['codPuntoVentaMH'] = GblTraerConfiguracionFe("codPuntoVentaMH");
          $emisor['codPuntoVenta'] = GblTraerConfiguracionFe("codPuntoVentaMH");
          if($codigoDocumento == "11"){
            $emisor['tipoItemExpor'] = 1;
            $emisor['recintoFiscal'] = null;
            $emisor['regimen'] = null;
          }
        }
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        switch($codigoDocumento) {
          case "04":
          $dte = array (
            'identificacion' => $identificacion,
            'documentoRelacionado' => null,
            'emisor' => $emisor,
            'receptor' => $receptor,
            'ventaTercero' => null,
            'cuerpoDocumento' => $productos,
            'resumen' => array(
              'totalNoSuj' => round($nosujeto,2),
              'totalExenta' => round($exento,2),
              'totalGravada' => round($gravado,2),
              'subTotalVentas' => round($sumas,2),
              'descuNoSuj' => $desc_nosujeto,
              'descuExenta' => $desc_exento,
              'descuGravada' => $desc_gravado,
              'porcentajeDescuento' => $porc_descuento,
              'totalDescu' => $total_descuento,
              'tributos' => null,
              'subTotal' => $subtotal,
              'montoTotalOperacion' => $montoTotal,
              'totalLetras' => $total_letras,
            ),
            'extension' => null,
            'apendice' => null,
          );
          break;
          case "03":
          $dte = array (
            'identificacion' => $identificacion,
            'documentoRelacionado' => null,
            'emisor' => $emisor,
            'receptor' => $receptor,
            'otrosDocumentos' => null,
            'ventaTercero' => null,
            'cuerpoDocumento' => $productos,
            'resumen' => array(
              'totalNoSuj' => round($nosujeto,2),
              'totalExenta' => round($exento,2),
              'totalGravada' => round($gravado,2),
              'subTotalVentas' => round($sumas,2),
              'descuNoSuj' => $desc_nosujeto,
              'descuExenta' => $desc_exento,
              'descuGravada' => $desc_gravado,
              'porcentajeDescuento' => $porc_descuento,
              'totalDescu' => $total_descuento,
              'tributos' => array(["codigo" => "20", "descripcion" => "Impuesto al Valor Agregado 13%", "valor" => $iva]),
              'subTotal' => $subtotal,
              'ivaPerci1' => $iva_percibido,
              'ivaRete1' => round($iva_retenido,2),
              'reteRenta' => $retencion,
              'montoTotalOperacion' => $montoTotal,
              'totalNoGravado' => $total_nogravado,
              'totalPagar' => $total_pagar,
              'totalLetras' => $total_letras,
              'saldoFavor' => $saldo_favor,
              'condicionOperacion' => round($condicionPago,0),
              'pagos' => null,
              'numPagoElectronico' => ''
            ),
            'extension' => null,
            'apendice' => null,
          );
          break;
          case "01":
          $dte = array (
            'identificacion' => $identificacion,
            'documentoRelacionado' => null,
            'emisor' => $emisor,
            'receptor' => $receptor,
            'otrosDocumentos' => null,
            'ventaTercero' => null,
            'cuerpoDocumento' => $productos,
            'resumen' => array(
              'totalNoSuj' => round($nosujeto,2),
              'totalExenta' => round($exento,2),
              'totalGravada' => round($gravado,2),
              'subTotalVentas' => round($sumas,2),
              'descuNoSuj' => round($desc_nosujeto,2),
              'descuExenta' => round($desc_exento,2),
              'descuGravada' => round($desc_gravado,2),
              'porcentajeDescuento' => round($porc_descuento,2),
              'totalDescu' => round($total_descuento,2),
              'tributos' => null,
              'subTotal' => round($subtotal,2),
              'ivaRete1' => round($iva_retenido,2),
              'reteRenta' => round($retencion,2),
              'montoTotalOperacion' => round($montoTotal,2),
              //'montoTotalOperacion' => round($total,2),
              'totalNoGravado' => round($total_nogravado,2),
              'totalPagar' => round($total_pagar,2),
              'totalLetras' => $total_letras,
              'totalIva' => round($totalIva,2),
              'saldoFavor' => round($saldo_favor,2),
              'condicionOperacion' => round($condicionPago,0),
              'pagos' => null,
              'numPagoElectronico' => ''
            ),
            'extension' => null,
            'apendice' => null,
          );
          break;
          case "11":
          $dte = array (
            'identificacion' => $identificacion,
            'emisor' => $emisor,
            'receptor' => $receptor,
            'otrosDocumentos' => null,
            'ventaTercero' => null,
            'cuerpoDocumento' => $productos,
            'resumen' => array(
              'totalGravada' => round($gravado,2),
              'porcentajeDescuento' => $porc_descuento,
              'descuento' => $total_descuento,
              'totalDescu' => $total_descuento,
              'flete' => null,
              'seguro' => null,
              'montoTotalOperacion' => $montoTotal,
              'totalNoGravado' => $total_nogravado,
              'totalPagar' => $total_pagar,
              'totalLetras' => $total_letras,
              'condicionOperacion' => round($condicionPago,0),
              'pagos' => null,
              'codIncoterms' => null,
              'descIncoterms' => null,
              'numPagoElectronico' => '',
              'observaciones' => ''
            ),
            'apendice' => null,
          );
          break;
        }

        $dteFirmado = firmarDte($dte);
        $doc = enviarDocumento($ultimoCorrelativo,$versionDocumento,$codigoDocumento,$dteFirmado);
        // print_r(json_encode($dte));
        // print_r($doc);

        if($doc["status"] =="OK"){
          $datosArrayMH = array(
            "selloRecibido" => $doc["selloRecibido"],
            "fhProcesamiento" => $doc["fhProcesamiento"],
            "jsonDTE" => json_encode($dte),
            "jsonFirmado" => $dteFirmado,
            "codigoGeneracion" => $codigoGeneracion,
            "numeroControl" => $numeroControl,
            "aleatorioFactura" => uniqid(),
          );
          $datosRespuesta["codigo"]=200;

        } else {
          $datosArrayMH = array(
            "error" => json_encode($doc),
            "jsonDTE" => json_encode($dte),
            "jsonFirmado" => $dteFirmado,
            "codigoGeneracion" => $codigoGeneracion,
            "numeroControl" => $numeroControl,
            "aleatorioFactura" => uniqid()
          );
          $datosRespuesta["idVenta"]=$idFactura;
          $datosRespuesta["codigo"]=500;
        }
        $condicion = array('idFactura' => $idFactura);
        $editar = EditarDatos("factura",$datosArrayMH,$condicion);
        $mail =$this->EnviarCorreo($idFactura,'1');
        if($datosRespuesta["codigo"] == 200){
          if($mail == 500){
            $datosRespuesta["codigo"]=203;
          }
        }
      } else {
        $datosRespuesta["codigo"]=403;
      }
    } else {
      $datosRespuesta["idVenta"]=$idFactura;
      $datosRespuesta["codigo"]=200;
    }
    echo json_encode($datosRespuesta);
  }
  ///////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////
  public function crearDTECompra($idCompra){
    $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
    if($fe_enabled == "Si"){
      /**********************************************************************/
      $condicionDatos = array('idCompra' => $idCompra);
      $datosVenta = TraerUnDato("compras", $condicionDatos);
      /****************************************************************/
      /****************************************************************/
      $selloRecibido = $datosVenta->selloRecibido;
      $numeroControl = $datosVenta->numeroControl;
      $codigoGeneracion = $datosVenta->codigoGeneracion;
      $ultimoCorrelativo = $datosVenta->numero;
      $fecha = $datosVenta->fecha;
      $hora = $datosVenta->hora;
      $tipoPago = $datosVenta->pagoCompra;
      $condicionPago = 3;
      if($tipoPago == 'Contado'){
        $condicionPago = 1;
      } else if($tipoPago == 'Credito'){
        $condicionPago = 2;
      }
      /****************************************************************/
      /****************************************************************/
      $condicionDatos = array('idProveedor' => $datosVenta->idProveedor);
      $joinDatosCliente = array(
        array(
          'tabla' => 'FE_CAT_019_CodigodeActividadEco as giros',
          'tipo' => 'left',
          'condicion' => 'giros.codigo=proveedor.giroProveedor',
          'campos' => 'giros.valores as giro'
        ),
      );
      $datosCliente = TraerUnDatoJoin("proveedor", $condicionDatos,$joinDatosCliente);
      $documento = $datosCliente->nitProveedor;
      $tipoDocumentoProveedor = "36";
      if($datosCliente->facturarConProveedor == "DUI"){
        $tipoDocumentoProveedor = "13";
        $documento = $datosCliente->duiProveedor;
      }
      $condicionDatosDocumento = array('aliasDocumento' => $datosVenta->tipoCompra);
      $joinDatosDocumento = array(
        array(
          'tabla' => 'FE_CAT_002_TipodeDocumento AS fedoc',
          'tipo' => 'inner',
          'condicion' => 'fedoc.codigo = documento.codigo',
          'campos' => 'fedoc.version'
        ),
      );
      $datosDocumento = TraerUnDatoJoin("documento", $condicionDatosDocumento,$joinDatosDocumento);
      $codigoDocumento = $datosDocumento->codigo;
      $versionDocumento = $datosDocumento->version;

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

      /**********************************************************************/
      /**********************************************************************/
      if($selloRecibido == ""){
        /************************************************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        $productos = array();
        $gravado = 0;
        $descuentos = 0;
        $totalIva = 0;
        $numItem=1;
        $condicionDatosDetalle = array('idCompra' => $idCompra);
        $datosPedidoDetalle = TraerDatos("compras_detalles", $condicionDatosDetalle);
        if($datosPedidoDetalle !== null){
          foreach ($datosPedidoDetalle as $pedidoDetalle) {
            $descuento=0;

            $cantidad = $pedidoDetalle->cant;
            $descuento = 0;

            $condicionDatosProducto = array('producto.idProducto' => $pedidoDetalle->idProducto,'productoPresentacion.idProductoPresentacion' => $pedidoDetalle->idProductoPresentacion);
            $joinDatosProducto = array(
              array(
                'tabla' => 'productoPresentacion',
                'tipo' => 'inner',
                'condicion' => 'productoPresentacion.idProducto = producto.idProducto',
                'campos' => 'idUnidadMedida'
              ),
            );
            $datosProducto = TraerUnDatoJoin("producto", $condicionDatosProducto,$joinDatosProducto);
            $descripcion = $datosProducto->nombreProducto;
            $uniMedida = round($datosProducto->idUnidadMedida,0);
            $tipoProdServ = 1;
            if($datosProducto->noStockProducto){
              $tipoProdServ = 2;
            }
            if($cantidad == 0){
              $cantidad =1;
            }
            switch ($codigoDocumento) {
              case "14":
              $precio = $pedidoDetalle->costoConIva;
              $subTotal = $pedidoDetalle->subtotal;
              $productos[] = array(
                'numItem' =>$numItem,
                'tipoItem' =>$tipoProdServ,
                'codigo' => null,
                'descripcion' => $descripcion,
                'cantidad' => round($cantidad,2),
                'uniMedida' => $uniMedida,
                'precioUni' => round($precio,2),
                'montoDescu' => round($descuento,2),
                'compra' => round($subTotal,2),
              );
              break;
            }
            $gravado += $subTotal;
            $descuentos += $descuento;
            $numItem ++;
            /*////////////////////////////////////////////////////////*/
          }
        }
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        $nosujeto=0;
        $exento=0;
        $sumas = $nosujeto+$exento+$gravado;
        $desc_gravado = round(0,2);
        $porc_descuento = round(0,2);
        $total_descuento = round($descuentos,2);
        $iva = round($gravado * GblTraerConfiguracionFe("iva"),2);
        $subtotal = round($gravado,2);
        $iva_percibido = round(0,2);
        $iva_retenido = round(0,2);
        $retencion = round(0,2);
        $total = round($gravado,2);
        // if($datosCliente->retieneIvaCliente){
        //   $iva_retenido = $gravado * GblTraerConfiguracionFe("ivaRet");
        //   $total = round($total-$ivaRet,2);
        // }

        $total_nogravado = round($exento+$nosujeto,2);
        $total_pagar = round($total,2);
        list($entero,$decimal) = explode(".",number_format($total_pagar,2,".",""));
        $total_letras = num2letras($entero)." ".$decimal."/100";
        $saldo_favor = round(0,2);

        if($numeroControl == ""){
          $numeroControl = generarNumeroControl($codigoDocumento,$ultimoCorrelativo);
        }
        if($codigoGeneracion == ""){
          $codigoGeneracion = generarUuid();
        }
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////

        ////////////////////////////////////////////////////
        $identificacion = array (
          'version' => round($versionDocumento,0),
          'ambiente' => GblTraerConfiguracionFe("ambiente"),
          'tipoDte' => $codigoDocumento,
          'numeroControl' => $numeroControl,
          'codigoGeneracion' => $codigoGeneracion,
          'tipoModelo' => round(GblTraerConfiguracionFe("modeloFacturacion"),0),
          'tipoOperacion' => 1,
          'tipoContingencia' => null,
          'motivoContin' => null,
          'fecEmi' => $fecha,
          'horEmi' => $hora,
          'tipoMoneda' => 'USD',
        );
        $emisor = array(
          'nit' =>GblTraerConfiguracionFe('nitEmisor'),
          'nrc' => GblTraerConfiguracionFe('nrcEmisor'),
          'nombre' =>GblTraerConfiguracionFe('nombreEmisor'),
          'codActividad' =>GblTraerConfiguracionFe('codGiroEmisor'),
          'descActividad' => GblTraerConfiguracionFe('giroEmisor'),
          'direccion' =>
          array (
            'departamento' => GblTraerConfiguracionFe('departamentoEmisor'),
            'municipio' => GblTraerConfiguracionFe('municipioEmisor'),
            'complemento' => GblTraerConfiguracionFe('direccionEmisor'),
          ),
          'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
          'correo' => GblTraerConfiguracionFe('correoEmisor'),
        );
        if($codigoDocumento == "14"){
          $receptor = array (
            'tipoDocumento' => $tipoDocumentoProveedor,
            'numDocumento' => str_replace("-","",$documento),
            'nombre' => $datosCliente->nombreProveedor,
            'codActividad' => null,//$datosCliente->giroProveedor,
            'descActividad' => null,//$datosCliente->giro,
            'direccion' =>
            array (
              'departamento' => $datosCliente->departamentoProveedor,
              'municipio' => $datosCliente->municipioProveedor,
              'complemento' => $datosCliente->direccionProveedor,
            ),
            'telefono' => $datosCliente->telefonoProveedor,
            'correo' => $datosCliente->correoProveedor,
          );
        }
        if($codigoDocumento =="14"){
          $emisor['codEstableMH'] = GblTraerConfiguracionFe("codEstableMH");
          $emisor['codEstable'] = GblTraerConfiguracionFe("codEstableMH");
          $emisor['codPuntoVentaMH'] = GblTraerConfiguracionFe("codPuntoVentaMH");
          $emisor['codPuntoVenta'] = GblTraerConfiguracionFe("codPuntoVentaMH");
        }
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        switch($codigoDocumento) {
          case "14":
          $dte = array (
            'identificacion' => $identificacion,
            'emisor' => $emisor,
            'sujetoExcluido' => $receptor,
            'cuerpoDocumento' => $productos,
            'resumen' => array(
              'totalCompra' => round($gravado,2),
              'descu' => $desc_gravado,
              'totalDescu' => $total_descuento,
              'subTotal' => $subtotal,
              'ivaRete1' => $iva_retenido,
              'reteRenta' => $retencion,
              'totalPagar' => $total_pagar,
              'totalLetras' => $total_letras,
              'condicionOperacion' => round($condicionPago,0),
              'pagos' => null,
              'observaciones' => ''
            ),
            'apendice' => null,
          );
          break;
        }

        $dteFirmado = firmarDte($dte);
        $doc = enviarDocumento($ultimoCorrelativo,$versionDocumento,$codigoDocumento,$dteFirmado);
        // print_r(json_encode($dte));
        // print_r($doc);

        if($doc["status"] =="OK"){
          $datosArrayMH = array(
            "codigoGeneracion" => $codigoGeneracion,
            "numeroControl" => $numeroControl,
            "selloRecibido" => $doc["selloRecibido"],
            "fhProcesamiento" => $doc["fhProcesamiento"],
            "jsonDTE" => json_encode($dte),
            "jsonFirmado" => $dteFirmado,
            "aleatorioCompra" => uniqid(),
          );
          $datosRespuesta["codigo"]=200;

        } else {
          $datosArrayMH = array(
            "codigoGeneracion" => $codigoGeneracion,
            "numeroControl" => $numeroControl,
            "error" => json_encode($doc),
            "aleatorioCompra" => uniqid()
          );
          $datosRespuesta["idCompra"]=$idCompra;
          $datosRespuesta["codigo"]=500;
        }
        $condicion = array('idCompra' => $idCompra);
        $editar = EditarDatos("compras",$datosArrayMH,$condicion);
        // $mail =$this->EnviarCorreo($idFactura);
        // if($datosRespuesta["codigo"] == 200){
        //   if($mail == 500){
        //     $datosRespuesta["codigo"]=203;
        //   }
        // }
      } else {
        $datosRespuesta["codigo"]=403;
      }
    } else {
      $datosRespuesta["codigo"]=200;
    }
    echo json_encode($datosRespuesta);
  }
  ///////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////
  public function crearDTENota($idFactura){
    $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
    if($fe_enabled == "Si"){
      /**********************************************************************/
      /**********************************************************************/
      $condicionDatos = array('idFactura' => $idFactura);
      $datosVenta = TraerUnDato("factura", $condicionDatos);
      /****************************************************************/
      /****************************************************************/
      $selloRecibido = $datosVenta->selloRecibido;
      $numeroControl = $datosVenta->numeroControl;
      $codigoGeneracion = $datosVenta->codigoGeneracion;
      $tipoDescuento = $datosVenta->tipoDescuentoFactura;
      $ultimoCorrelativo = $datosVenta->numeroDocumentoFactura;
      $fecha = $datosVenta->fechaFactura;
      $hora = $datosVenta->horaFactura;
      $tipoPago = $datosVenta->tipoPagoFactura;
      $idReferencia = $datosVenta->idAfectaFactura;
      /*******************************************************************/
      /*******************************************************************/
      /****************************************************************/
      $condicionDatosRef = array('idFactura' => $idReferencia);
      $datosVentaRef = TraerUnDato("factura", $condicionDatosRef);
      /****************************************************************/
      $codigoGeneracionRef = $datosVentaRef->numeroDocumentoFactura;
      // $codigoGeneracionRef = $datosVentaRef->codigoGeneracion;
      $fechaFacturaRef = $datosVentaRef->fechaFactura;
      $condicionDatosDocumentoRef = array('idDocumento' => $datosVentaRef->idDocumento);
      $joinDatosDocumentoRef = array(
        array(
          'tabla' => 'FE_CAT_002_TipodeDocumento AS fedoc',
          'tipo' => 'inner',
          'condicion' => 'fedoc.codigo = documento.codigo',
          'campos' => 'fedoc.version'
        ),
      );
      $datosDocumentoRef = TraerUnDatoJoin("documento", $condicionDatosDocumentoRef,$joinDatosDocumentoRef);
      $codigoDocumentoRef = $datosDocumentoRef->codigo;
      $versionDocumentoRef = $datosDocumentoRef->version;

      $documentoRelacionado = array(
        array(
          "tipoDocumento" => $codigoDocumentoRef,
          "tipoGeneracion" => round(GblTraerConfiguracionFe('modeloFacturacion'),0),
          // "numeroDocumento" =>"123",
          "numeroDocumento" =>$codigoGeneracionRef,
          "fechaEmision" => $fechaFacturaRef
        )
      );
      /*******************************************************************/
      /*******************************************************************/
      $condicionPago = 3;
      if($tipoPago == 'Contado'){
        $condicionPago = 1;
      } else if($tipoPago == 'Credito'){
        $condicionPago = 2;
      }
      /****************************************************************/
      /****************************************************************/
      $condicionDatos = array('idCliente' => $datosVenta->idCliente);
      $joinDatosCliente = array(
        array(
          'tabla' => 'FE_CAT_019_CodigodeActividadEco as giros',
          'tipo' => 'left',
          'condicion' => 'giros.codigo=cliente.giroCliente',
          'campos' => 'giros.valores as giro'
        ),
      );
      $datosCliente = TraerUnDatoJoin("cliente", $condicionDatos,$joinDatosCliente);
      $documento = $datosCliente->nitCliente;
      $tipoDocumentoCliente = "36";
      if($datosCliente->facturarConCliente == "DUI"){
        $tipoDocumentoCliente = "13";
        $documento = $datosCliente->duiCliente;
      }
      $condicionDatosDocumento = array('idDocumento' => $datosVenta->idDocumento);
      $joinDatosDocumento = array(
        array(
          'tabla' => 'FE_CAT_002_TipodeDocumento AS fedoc',
          'tipo' => 'inner',
          'condicion' => 'fedoc.codigo = documento.codigo',
          'campos' => 'fedoc.version'
        ),
      );
      $datosDocumento = TraerUnDatoJoin("documento", $condicionDatosDocumento,$joinDatosDocumento);
      $codigoDocumento = $datosDocumento->codigo;
      $versionDocumento = $datosDocumento->version;

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

      /**********************************************************************/
      /**********************************************************************/
      if($selloRecibido == ""){
        /************************************************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        $productos = array();
        $gravado = 0;
        $descuentos = 0;
        $totalIva = 0;
        $numItem=1;
        $condicionDatosDetalle = array('idFactura' => $idFactura,"estadoPedidoDetalle !="=>"Borrado");
        $datosPedidoDetalle = TraerDatos("pedidoDetalle", $condicionDatosDetalle);
        if($datosPedidoDetalle !== null){
          foreach ($datosPedidoDetalle as $pedidoDetalle) {
            $descuento=0;
            $subt_exento=0;
            $subt_nosujeto=0;

            $cantidad = $pedidoDetalle->cantidadPedidoDetalle;
            $descuento = $pedidoDetalle->descuentoPedidoDetalle;

            $condicionDatosProducto = array('producto.idProducto' => $pedidoDetalle->idProducto,'productoPresentacion.idProductoPresentacion' => $pedidoDetalle->idPresentacionProductoPedidoDetalle);
            $joinDatosProducto = array(
              array(
                'tabla' => 'productoPresentacion',
                'tipo' => 'inner',
                'condicion' => 'productoPresentacion.idProducto = producto.idProducto',
                'campos' => 'idUnidadMedida'
              ),
            );
            $datosProducto = TraerUnDatoJoin("producto", $condicionDatosProducto,$joinDatosProducto);
            $descripcion = $datosProducto->nombreProducto;
            $uniMedida = round($datosProducto->idUnidadMedida,0);
            $tipoProdServ = 1;
            if($datosProducto->noStockProducto){
              $tipoProdServ = 2;
            }
            if($cantidad == 0){
              $cantidad =1;
            }

            $precio = $pedidoDetalle->precioIvaUnitarioPedidoDetalle  / (1 + GblTraerConfiguracionFe("iva"));
            if($tipoDescuento == "Porcentaje"){
              $descuento = ($pedidoDetalle->descuentoPedidoDetalle/100) * $precio;
            }
            $subTotal = ($pedidoDetalle->subTotalIvaPedidoDetalle / (1 + GblTraerConfiguracionFe("iva"))) - ($descuento * $cantidad);
            $subTotalD = $descuento * $cantidad;

            $tributos = ["20"];

              $productos[] = array(
                'numItem' =>$numItem,
                'tipoItem' =>$tipoProdServ,
                'numeroDocumento' => $codigoGeneracionRef,
                'codigo' => null,
                'codTributo' => null,
                'descripcion' => $descripcion,
                'cantidad' => round($cantidad,2),
                'uniMedida' => $uniMedida,
                'precioUni' => round($precio,4),
                'montoDescu' => round($subTotalD,4),
                'ventaNoSuj' => round($subt_nosujeto,4),
                'ventaExenta' => round($subt_exento,4),
                'ventaGravada' => round($subTotal,4),
                'tributos' => $tributos,
              );

            $gravado += $subTotal;
            $descuentos += $descuento;
            $numItem ++;
            /*////////////////////////////////////////////////////////*/
          }
        }
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        /**********************************************************************/
        $nosujeto=0;
        $exento=0;
        $sumas = $nosujeto+$exento+$gravado;
        $desc_nosujeto = round(0,2);
        $desc_exento = round(0,2);
        $desc_gravado = round(0,2);
        $porc_descuento = round(0,2);
        $total_descuento = round($descuentos,2);
        $iva = round($gravado * GblTraerConfiguracionFe("iva"),2);
        $subtotal = round($gravado,2);
        $iva_percibido = round(0,2);
        $iva_retenido = round(0,2);
        $ivaRet = 0;
        if($datosCliente->retieneIvaCliente && $datosVenta->retencionFactura>0){
          if($codigoDocumento == "06" || $codigoDocumento == "05"){
            $iva_retenido = $gravado * GblTraerConfiguracionFe("ivaRet");
          } else {
            $iva_retenido = ($gravado/(1+GblTraerConfiguracionFe("iva"))) * GblTraerConfiguracionFe("ivaRet");
          }
        }
        $retencion = round(0,2);
        if($datosCliente->retieneRentaCliente && $datosVenta->rentaFactura>0){
          if($codigoDocumento  == "06" || $codigoDocumento == "05"){
            $retencion = $gravado * GblTraerConfiguracionFe("ivaRent");
          } else {
            $retencion = ($gravado/(1+GblTraerConfiguracionFe("iva"))) * GblTraerConfiguracionFe("ivaRent");
          }
        }
        $total = round($gravado,2);
        if($codigoDocumento == "06" || $codigoDocumento == "05"){
          $total = round($gravado+$iva,2);
        }
        $total = round($total-$iva_retenido,2);
        $total = round($total-$retencion,2);
        $montoTotal = $total;

        // $retencion = round(0,2);
        // $total = round($gravado,2);
        // if($codigoDocumento == "05" || $codigoDocumento == "06"){
        //   $total = round($gravado+$iva,2);
        // }

        $total_nogravado = round($exento+$nosujeto,2);
        $total_pagar = round($total,2);
        list($entero,$decimal) = explode(".",number_format($total_pagar,2,".",""));
        $total_letras = num2letras($entero)." ".$decimal."/100";
        $saldo_favor = round(0,2);

        if($numeroControl == ""){
          $numeroControl = generarNumeroControl($codigoDocumento,$ultimoCorrelativo);
        }
        if($codigoGeneracion == ""){
          $codigoGeneracion = generarUuid();
        }
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////

        ////////////////////////////////////////////////////
        $identificacion = array (
          'version' => round($versionDocumento,0),
          'ambiente' => GblTraerConfiguracionFe("ambiente"),
          'tipoDte' => $codigoDocumento,
          'numeroControl' => $numeroControl,
          'codigoGeneracion' => $codigoGeneracion,
          'tipoModelo' => round(GblTraerConfiguracionFe("modeloFacturacion"),0),
          'tipoOperacion' => 1,
          'tipoContingencia' => null,
          'motivoContin' => null,
          'fecEmi' => $fecha,
          'horEmi' => $hora,
          'tipoMoneda' => 'USD',
        );
        $emisor = array(
          'nit' =>GblTraerConfiguracionFe('nitEmisor'),
          'nrc' => GblTraerConfiguracionFe('nrcEmisor'),
          'nombre' =>GblTraerConfiguracionFe('nombreEmisor'),
          'codActividad' =>GblTraerConfiguracionFe('codGiroEmisor'),
          'descActividad' => GblTraerConfiguracionFe('giroEmisor'),
          'nombreComercial' =>  GblTraerConfiguracionFe('nombreComercialEmisor'),
          'tipoEstablecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
          'direccion' =>
          array (
            'departamento' => GblTraerConfiguracionFe('departamentoEmisor'),
            'municipio' => GblTraerConfiguracionFe('municipioEmisor'),
            'complemento' => GblTraerConfiguracionFe('direccionEmisor'),
          ),
          'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
          'correo' => GblTraerConfiguracionFe('correoEmisor'),
        );
        if($codigoDocumento == "05" || $codigoDocumento == "06"){
          $receptor = array (
            'nit' => str_replace("-","",$documento),
            'nrc' => str_replace("-","",$datosCliente->nrcCliente),
            'nombre' => $datosCliente->nombreCliente,
            'codActividad' => $datosCliente->giroCliente,
            'descActividad' => $datosCliente->giro,
            'nombreComercial' => $datosCliente->nombreComercialCliente,
            'direccion' =>
            array (
              'departamento' => $datosCliente->departamentoCliente,
              'municipio' => $datosCliente->municipioCliente,
              'complemento' => $datosCliente->direccionCliente,
            ),
            'telefono' => $datosCliente->telefonoCliente,
            'correo' => $datosCliente->emailCliente,
          );
        }
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        ////////////////////////////////////////////////////
        switch($codigoDocumento) {
          case "05":
          $dte = array (
            'identificacion' => $identificacion,
            'documentoRelacionado' => $documentoRelacionado,
            'emisor' => $emisor,
            'receptor' => $receptor,
            'ventaTercero' => null,
            'cuerpoDocumento' => $productos,
            'resumen' => array(
              'totalNoSuj' => round($nosujeto,2),
              'totalExenta' => round($exento,2),
              'totalGravada' => round($gravado,2),
              'subTotalVentas' => round($sumas,2),
              'descuNoSuj' => $desc_nosujeto,
              'descuExenta' => $desc_exento,
              'descuGravada' => $desc_gravado,
              'totalDescu' => $total_descuento,
              'tributos' => array(["codigo" => "20", "descripcion" => "Impuesto al Valor Agregado 13%", "valor" => $iva]),
              'subTotal' => $subtotal,
              'ivaPerci1' => $iva_percibido,
              'ivaRete1' => round($iva_retenido,2),
              'reteRenta' => round($retencion,2),
              'montoTotalOperacion' => $montoTotal,
              'totalLetras' => $total_letras,
              'condicionOperacion' => round($condicionPago,0),
            ),
            'extension' => null,
            'apendice' => null,
          );
          break;
          case "06":
          $dte = array (
            'identificacion' => $identificacion,
            'documentoRelacionado' => $documentoRelacionado,
            'emisor' => $emisor,
            'receptor' => $receptor,
            'ventaTercero' => null,
            'cuerpoDocumento' => $productos,
            'resumen' => array(
              'totalNoSuj' => round($nosujeto,2),
              'totalExenta' => round($exento,2),
              'totalGravada' => round($gravado,2),
              'subTotalVentas' => round($sumas,2),
              'descuNoSuj' => $desc_nosujeto,
              'descuExenta' => $desc_exento,
              'descuGravada' => $desc_gravado,
              'totalDescu' => $total_descuento,
              'tributos' => array(["codigo" => "20", "descripcion" => "Impuesto al Valor Agregado 13%", "valor" => $iva]),
              'subTotal' => $subtotal,
              'ivaPerci1' => $iva_percibido,
              'ivaRete1' => round($iva_retenido,2),
              'reteRenta' => round($retencion,2),
              'montoTotalOperacion' => $montoTotal,
              'totalLetras' => $total_letras,
              'numPagoElectronico' => null,
              'condicionOperacion' => round($condicionPago,0),
            ),
            'extension' => null,
            'apendice' => null,
          );
          break;
        }

        $dteFirmado = firmarDte($dte);
        $doc = enviarDocumento($ultimoCorrelativo,$versionDocumento,$codigoDocumento,$dteFirmado);
        // print_r(json_encode($dte));
        // print_r($doc);

        if($doc["status"] =="OK"){
          $datosArrayMH = array(
            "numeroControl" => $numeroControl,
            "codigoGeneracion" => $codigoGeneracion,
            "selloRecibido" => $doc["selloRecibido"],
            "fhProcesamiento" => $doc["fhProcesamiento"],
            "jsonDTE" => json_encode($dte),
            "jsonFirmado" => $dteFirmado,
            "aleatorioFactura" => uniqid(),
          );
          $datosRespuesta["codigo"]=200;

        } else {
          $datosArrayMH = array(
            "error" => json_encode($doc),
            "jsonDTE" => json_encode($dte),
            "jsonFirmado" => $dteFirmado,
            "aleatorioFactura" => uniqid()
          );
          $datosRespuesta["idVenta"]=$idFactura;
          $datosRespuesta["codigo"]=500;
        }
        $condicion = array('idFactura' => $idFactura);
        $editar = EditarDatos("factura",$datosArrayMH,$condicion);
        // $mail =$this->EnviarCorreo($idFactura);
        // if($datosRespuesta["codigo"] == 200){
        //   if($mail == 500){
        //     $datosRespuesta["codigo"]=203;
        //   }
        // }
      } else {
        $datosRespuesta["codigo"]=403;
      }
    } else {
      $datosRespuesta["codigo"]=200;
    }
    echo json_encode($datosRespuesta);
  }
  ///////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////
  function anularDTE($idFactura){
    /**********************************************************************/
    /**********************************************************************/
    $condicionDatos = array('idFactura' => $idFactura);
    $datosVenta = TraerUnDato("factura", $condicionDatos);
    $estadoFactura = $datosVenta->estadoFactura;
    $prodsDev = true;
    if($estadoFactura != "Anulado"){
      $datosArrayMH = array(
        "estadoFactura" => 'Anulado',
        "aleatorioFactura" => uniqid()
      );
      // IniciarTransaccion();
      $condicion = array('idFactura' => $idFactura);
      $editar = EditarDatos("factura",$datosArrayMH,$condicion);
      if($editar){
        // $condicionDatosDetalle = array('idFactura' => $idFactura);
        // $datosPedidoDetalle = TraerDatos("pedidoDetalle", $condicionDatosDetalle);
        // $errorl=false;
        // if($datosPedidoDetalle !== null){
          // foreach ($datosPedidoDetalle as $pedidoDetalle) {
          //   $condicionDatos = array('idProductoPresentacion' => $pedidoDetalle->idPresentacionProductoPedidoDetalle);
          //   $datosProductoPresentacion = TraerUnDato("productoPresentacion", $condicionDatos);
          //   $unidad = $datosProductoPresentacion->unidadProductoPresentacion;
          //   $cantidadreal =$unidad * $pedidoDetalle->cantidadPedidoDetalle;
          //
          //
          //   $dp = TraerUndato('producto',['idProducto'=>$pedidoDetalle->idProducto]);
          //   if($dp->comboProducto==1)
          //   {
          //     $this->db->select('productoPresentacion.idProductoPresentacion,productoPresentacion.costoProductoPresentacion,productoPresentacion.costoIVAProductoPresentacion,productoCombo.idProducto,productoPresentacion.unidadProductoPresentacion,productoCombo.cant');
          //     $this->db->join('productoPresentacion','productoPresentacion.idProductoPresentacion=productoCombo.idProductoPresentacion');
          //     $this->db->where('productoCombo.idProductoPadre',$pedidoDetalle->idProducto);
          //     $query = $this->db->get('productoCombo');
          //     $productoscombo = $query->result();
          //     foreach ($productoscombo as $keyc) {
          //
          //       $cantidadreal = $keyc->cant * $keyc->unidadProductoPresentacion * $pedidoDetalle->cantidadPedidoDetalle;
          //       $response_lotes = $this->lotes->CargaLote(
          //         "Anulacion",
          //         $pedidoDetalle->idPedidoDetalle,
          //         $keyc->idProducto,
          //         $keyc->idProductoPresentacion,
          //         $this->session->idSucursal,
          //         ($keyc->cant * $pedidoDetalle->cantidadPedidoDetalle),
          //         $cantidadreal,
          //         $keyc->unidadProductoPresentacion,
          //         $keyc->costoProductoPresentacion,
          //         "",
          //         "",
          //         "");
          //     }
          //   }
          //   else
          //   {
          //     $condicionLote = array('idProducto' => $pedidoDetalle->idProducto);
          //     $datosLote = TraerUnDato("lote", $condicionLote);
          //     $costoPromedio = $datosLote->costo_promedio;
          //     $costoReal = $costoPromedio * $unidad;
          //
          //     $response_lotes = $this->lotes->CargaLote(
          //       "Anulacion",
          //       $pedidoDetalle->idPedidoDetalle,
          //       $pedidoDetalle->idProducto,
          //       $pedidoDetalle->idPresentacionProductoPedidoDetalle,
          //       $this->session->idSucursal,
          //       $pedidoDetalle->cantidadPedidoDetalle,
          //       $cantidadreal,
          //       $unidad,
          //       $costoReal,
          //       "",
          //       "",
          //       "");
          //
          //   }
          //
          //
          //
          //
          //
          //
          //     if($response_lotes['Error']==true){
          //       $errorl = true;
          //     }
          //   }
          // }
          // if(!$errorl){
          //   //exito
          //   EjecutarTransaccion();
          // } else {
          //   //lotes
          //   DeshacerTransaccion();
          //   $prodsDev = false;
          // }
        } else {
          DeshacerTransaccion();
          $prodsDev = false;
          /// NO ANULA
        }
      }
      $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
      if($fe_enabled == "Si"){
        if($datosVenta->tipoDocumentoFactura != "TIK"){
          $tipoDocumento = $datosVenta->tipoDocumentoFactura;
          if($tipoDocumento == "FAC"){
            $codigoDocumento = "01";
          } else if($tipoDocumento == "CCF") {
            $codigoDocumento = "03";
          }
          $condicionDatosDocumento = array('codigo' => $codigoDocumento);
          $datosDocumento = TraerUnDato("FE_CAT_002_TipodeDocumento", $condicionDatosDocumento);
          $versionDocumento = $datosDocumento->version;
          $versionDocumentoA = 2;
          /****************************************************************/
          /****************************************************************/
          $selloRecibidoAnulacion = $datosVenta->selloRecibidoAnulacion;
          $codigoGeneracionAnulacion = $datosVenta->codigoGeneracionAnulacion;

          $codigoGeneracion = $datosVenta->codigoGeneracion;
          $selloRecibido = $datosVenta->selloRecibido;
          //$selloRecibido = "";
          if($selloRecibido != ""){
            $numeroControl = $datosVenta->numeroControl;
            $total = $datosVenta->totalFactura;
            $iva = $datosVenta->ivaFactura;

            $ultimoCorrelativo = $datosVenta->numeroDocumentoFactura;
            // $codigoGeneracion = $ultimoCorrelativo;
            // $numeroControl = $ultimoCorrelativo;
            $fecha = $datosVenta->fechaFactura;
            $hora = $datosVenta->horaFactura;
            $fechaAnu = date("Y-m-d");
            $horaAnu = date("H:i:s");
            $tipoPago = $datosVenta->tipoPagoFactura;
            /****************************************************************/
            /****************************************************************/
            $condicionDatos = array('idCliente' => $datosVenta->idCliente);
            $joinDatosCliente = array(
              array(
                'tabla' => 'FE_CAT_019_CodigodeActividadEco as giros',
                'tipo' => 'left',
                'condicion' => 'giros.codigo=cliente.giroCliente',
                'campos' => 'giros.valores as giro'
              ),
            );
            $datosCliente = TraerUnDatoJoin("cliente", $condicionDatos,$joinDatosCliente);

            $documentoCliente = str_replace("-","",$datosCliente->nitCliente);
            $tipoDocumentoCliente = "36";
            if($datosCliente->facturarConCliente == "DUI"){
              $tipoDocumentoCliente = "13";
              $documentoCliente = $datosCliente->duiCliente;
            }
            if($documentoCliente == ""){
              $tipoDocumentoCliente = "37";
              $documentoCliente = "000000000";
            }
            $telefonoCliente = $datosCliente->telefonoCliente;
            if($datosCliente->telefonoCliente == ""){
              $telefonoCliente = "0000-0000";
            }
            $emailCliente = $datosCliente->emailCliente;
            if($datosCliente->emailCliente == ""){
              $emailCliente = GblTraerConfiguracionFe("correoEmisor");
            }

            // $condicionDatosDocumento = array('idDocumento' => $datosVenta->idDocumento);
            // $joinDatosDocumento = array(
            //   array(
            //     'tabla' => 'FE_CAT_002_TipodeDocumento AS fedoc',
            //     'tipo' => 'inner',
            //     'condicion' => 'fedoc.codigo = documento.codigo',
            //     'campos' => 'fedoc.version'
            //   ),
            // );
            // $datosDocumento = TraerUnDatoJoin("documento", $condicionDatosDocumento,$joinDatosDocumento);
            // $codigoDocumento = $datosDocumento->codigo;
            // // $versionDocumento = $datosDocumento->version;


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

            /**********************************************************************/
            /**********************************************************************/
            if($selloRecibidoAnulacion == ""){

              if($codigoGeneracionAnulacion == ""){
                $codigoGeneracionAnulacion = generarUuid();
              }
              ////////////////////////////////////////////////////
              ////////////////////////////////////////////////////

              ////////////////////////////////////////////////////
              $identificacion = array (
                'version' => round($versionDocumentoA,0),
                'ambiente' => GblTraerConfiguracionFe("ambiente"),
                'codigoGeneracion' => $codigoGeneracionAnulacion,
                'fecAnula' => $fechaAnu,
                'horAnula' => $horaAnu,
              );
              $emisor = array(
                'nit' =>GblTraerConfiguracionFe('nitEmisor'),
                'nombre' =>GblTraerConfiguracionFe('nombreEmisor'),
                'tipoEstablecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
                'nomEstablecimiento' => GblTraerConfiguracionFe('nombreComercialEmisor'),
                'codEstableMH' => GblTraerConfiguracionFe("codEstableMH"),
                'codEstable' => GblTraerConfiguracionFe("codEstableMH"),
                'codPuntoVentaMH' => GblTraerConfiguracionFe("codPuntoVentaMH"),
                'codPuntoVenta' => GblTraerConfiguracionFe("codPuntoVentaMH"),
                'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
                'correo' => GblTraerConfiguracionFe('correoEmisor'),
              );
              ////////////////////////////////////////////////////
              ////////////////////////////////////////////////////
              $dte = array (
                'identificacion' => $identificacion,
                'emisor' => $emisor,
                // 'receptor' => $receptor,
                'documento' => array(
                  'tipoDte' => $codigoDocumento,
                  'codigoGeneracion' => $codigoGeneracion,
                  'selloRecibido' => $selloRecibido,
                  'numeroControl' => $numeroControl,
                  'fecEmi' => $fecha,
                  'montoIva' => round($total,2),
                  'codigoGeneracionR' => null,
                  'tipoDocumento' => $tipoDocumentoCliente,
                  'numDocumento' => $documentoCliente,
                  'nombre' => $datosCliente->nombreCliente,
                  'telefono' => $telefonoCliente,
                  'correo' => $emailCliente
                ),
                'motivo' => array(
                  "tipoAnulacion" => round(2,0),
                  "motivoAnulacion" => "ANULACION DE LA OPERACION",
                  "nombreResponsable" => GblTraerConfiguracionFe("nombreEmisor"),
                  "tipDocResponsable" => "36",
                  "numDocResponsable" => GblTraerConfiguracionFe("nitEmisor"),
                  "nombreSolicita" => $datosCliente->nombreCliente,
                  "tipDocSolicita" => $tipoDocumentoCliente,
                  "numDocSolicita" => $documentoCliente
                ),
              );

              $dteFirmado = firmarDte($dte);
              $doc = anularDocumento($ultimoCorrelativo,$versionDocumentoA,$dteFirmado);
              // print_r(json_encode($dte));
              // echo "<hr>";
              // print_r($dteFirmado);
              // echo "<hr>";
              // print_r(json_encode($doc));
              // echo "<hr>";

              if($doc["status"] =="OK"){
                $datosArrayMH = array(
                  "selloRecibidoAnulacion" => $doc["selloRecibido"],
                  "fhProcesamientoAnulacion" => $doc["fhProcesamiento"],
                  "codigoGeneracionAnulacion" => $codigoGeneracionAnulacion,
                  "jsonDTEAnulacion" => json_encode($dte),
                  "jsonFirmadoAnulacion" => $dteFirmado,
                  "aleatorioFactura" => uniqid(),
                );
                $datosRespuesta["codigo"]=200;
                $datosRespuesta["devolucion"]=$prodsDev;
                $datosRespuesta["dte"]=true;

              } else {
                $datosArrayMH = array(
                  "codigoGeneracionAnulacion" => $codigoGeneracionAnulacion,
                  "errorAnulacion" => json_encode($doc),
                  "aleatorioFactura" => uniqid()
                );
                $datosRespuesta["idVenta"]=$idFactura;
                $datosRespuesta["codigo"]=500;
                $datosRespuesta["devolucion"]=$prodsDev;
                $datosRespuesta["dte"]=false;
              }
              $condicion = array('idFactura' => $idFactura);
              $editar = EditarDatos("factura",$datosArrayMH,$condicion);
              $mail = $this->EnviarCorreoAnulacion($idFactura);
              if($datosRespuesta["codigo"] == 200){
                if($mail == 500){
                  $datosRespuesta["codigo"]=203;
                }
              }

            } else {
              $datosRespuesta["codigo"]=403;
              $datosRespuesta["devolucion"]=$prodsDev;
              $datosRespuesta["dte"]=false;
            }
          } else {
            $datosRespuesta["codigo"]=200;
            $datosRespuesta["devolucion"]=$prodsDev;
            $datosRespuesta["dte"]=false;
          }
        } else {
          $datosRespuesta["codigo"]=200;
          $datosRespuesta["devolucion"]=$prodsDev;
          $datosRespuesta["dte"]=false;
        }
      } else {
        $datosRespuesta["idVenta"]=$idFactura;
        $datosRespuesta["codigo"]=200;
        $datosRespuesta["devolucion"]=$prodsDev;
        $datosRespuesta["dte"]=false;
      }
      echo json_encode($datosRespuesta);
    }
    function contingenciaDTE($idFactura){
      $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
      if($fe_enabled == "Si"){
        /**********************************************************************/
        /**********************************************************************/
        $condicionDatos = array('idFactura' => $idFactura);
        $datosVenta = TraerUnDato("factura", $condicionDatos);
        $estadoFactura = $datosVenta->estadoFactura;
        $fechaFactura = $datosVenta->fechaFactura;

        if($datosVenta->tipoDocumentoFactura != "TIK"){

          $tipoDocumento = $datosVenta->tipoDocumentoFactura;
          if($tipoDocumento == "FAC"){
            $codigoDocumento = "01";
          } else if($tipoDocumento == "CCF") {
            $codigoDocumento = "03";
          }
          $versionDocumentoA = 3;
          /****************************************************************/
          /****************************************************************/
          $motivoContingencia = $datosVenta->motivoContingencia;
          $tipoContingencia = $datosVenta->tipoContingencia;
          if($motivoContingencia == ""){
            $motivoContingencia = $this->input->post("motivoContingencia");
          }
          if($tipoContingencia == ""){
            $tipoContingencia = $this->input->post("tipoContingencia");
          }
          $selloRecibidoContingencia = $datosVenta->selloRecibidoContingencia;
          $codigoGeneracionContingencia = $datosVenta->codigoGeneracionContingencia;

          $codigoGeneracion = $datosVenta->codigoGeneracion;

          $numeroControl = $datosVenta->numeroControl;
          $fechaContingencia = date("Y-m-d");
          $horaContingencia = date("H:i:s");
          $tipoPago = $datosVenta->tipoPagoFactura;
          /****************************************************************/
          /****************************************************************/
          $condicionDatos = array('idCliente' => $datosVenta->idCliente);
          $joinDatosCliente = array(
            array(
              'tabla' => 'FE_CAT_019_CodigodeActividadEco as giros',
              'tipo' => 'left',
              'condicion' => 'giros.codigo=cliente.giroCliente',
              'campos' => 'giros.valores as giro'
            ),
          );
          $datosCliente = TraerUnDatoJoin("cliente", $condicionDatos,$joinDatosCliente);

          $documentoCliente = str_replace("-","",$datosCliente->nitCliente);
          $tipoDocumentoCliente = "36";
          if($datosCliente->facturarConCliente == "DUI"){
            $tipoDocumentoCliente = "13";
            $documentoCliente = $datosCliente->duiCliente;
          }
          if($documentoCliente == ""){
            $tipoDocumentoCliente = "37";
            $documentoCliente = "000000000";
          }
          $telefonoCliente = $datosCliente->telefonoCliente;
          if($datosCliente->telefonoCliente == ""){
            $telefonoCliente = "0000-0000";
          }
          $emailCliente = $datosCliente->emailCliente;
          if($datosCliente->emailCliente == ""){
            $emailCliente = GblTraerConfiguracionFe("correoEmisor");
          }

          // $condicionDatosDocumento = array('idDocumento' => $datosVenta->idDocumento);
          // $joinDatosDocumento = array(
          //   array(
          //     'tabla' => 'FE_CAT_002_TipodeDocumento AS fedoc',
          //     'tipo' => 'inner',
          //     'condicion' => 'fedoc.codigo = documento.codigo',
          //     'campos' => 'fedoc.version'
          //   ),
          // );
          // $datosDocumento = TraerUnDatoJoin("documento", $condicionDatosDocumento,$joinDatosDocumento);
          // $codigoDocumento = $datosDocumento->codigo;
          // $versionDocumento = $datosDocumento->version;


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

          /**********************************************************************/
          /**********************************************************************/
          $selloRecibidoContingencia = "";
          if($selloRecibidoContingencia == ""){

            if($codigoGeneracionContingencia == ""){
              $codigoGeneracionContingencia = generarUuid();
            }
            ////////////////////////////////////////////////////
            ////////////////////////////////////////////////////

            ////////////////////////////////////////////////////
            $identificacion = array (
              'version' => round($versionDocumentoA,0),
              'ambiente' => GblTraerConfiguracionFe("ambiente"),
              'codigoGeneracion' => $codigoGeneracionContingencia,
              'fTransmision' => $fechaContingencia,
              'hTransmision' => $horaContingencia,
            );
            $emisor = array(
              'nit' => GblTraerConfiguracionFe('nitEmisor'),
              'nombre' =>GblTraerConfiguracionFe('nombreEmisor'),
              'nombreResponsable' =>GblTraerConfiguracionFe('nombreEmisor'),
              'tipoDocResponsable' => "36",
              'numeroDocResponsable' =>GblTraerConfiguracionFe('nitEmisor'),
              'tipoEstablecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
              'codEstableMH' => GblTraerConfiguracionFe("codEstableMH"),
              'codPuntoVenta' => GblTraerConfiguracionFe("codPuntoVentaMH"),
              'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
              'correo' => GblTraerConfiguracionFe('correoEmisor'),
            );
            ////////////////////////////////////////////////////
            ////////////////////////////////////////////////////
            $dte = array (
              'identificacion' => $identificacion,
              'emisor' => $emisor,
              // 'receptor' => $receptor,
              'detalleDTE' => array(
                array(
                  'noItem' => 1,
                  'tipoDoc' => $codigoDocumento,
                  'codigoGeneracion' => $codigoGeneracion,
                ),
              ),
              'motivo' => array(
                'fInicio' => $fechaFactura,
                'fFin' => $fechaFactura,
                'hInicio' => "00:01:00",
                'hFin' => "23:59:59",
                'tipoContingencia' => 3,
                'motivoContingencia' => "FALLA DE INTERNET"
              ),
            );

            $dteFirmado = firmarDte($dte);
            $doc = contingirDocumento($versionDocumentoA,$dteFirmado,GblTraerConfiguracionFe('nitEmisor'));
            // print_r(json_encode($dte));
            // echo "<hr>";
            // // print_r($dteFirmado);
            // // echo "<hr>";
            // print_r(json_encode($doc));
            // echo "<hr>";

            if($doc["status"] =="OK"){
              $datosArrayMH = array(
                "selloRecibidoContingencia" => $doc["selloRecibido"],
                "fhProcesamientoContingencia" => $doc["fechaHora"],
                "codigoGeneracionContingencia" => $codigoGeneracionContingencia,
                "tipoContingencia" => $tipoContingencia,
                "motivoContingencia" => $motivoContingencia,
                "aleatorioFactura" => uniqid(),
              );
              $datosRespuesta["codigo"]=200;
              $datosRespuesta["dte"]=true;

            } else {
              $datosArrayMH = array(
                "tipoContingencia" => $tipoContingencia,
                "motivoContingencia" => $motivoContingencia,
                "codigoGeneracionContingencia" => $codigoGeneracionContingencia,
                "errorContingencia" => json_encode($doc),
                "aleatorioFactura" => uniqid()
              );
              $datosRespuesta["idVenta"]=$idFactura;
              $datosRespuesta["codigo"]=500;
              $datosRespuesta["dte"]=false;
            }
            $condicion = array('idFactura' => $idFactura);
            $editar = EditarDatos("factura",$datosArrayMH,$condicion);
          } else {
            $datosRespuesta["codigo"]=403;
            $datosRespuesta["dte"]=false;
          }
        } else {
          $datosRespuesta["codigo"]=200;
          $datosRespuesta["dte"]=false;
        }
      } else {
        $datosRespuesta["codigo"]=200;
        $datosRespuesta["dte"]=false;
      }
      echo json_encode($datosRespuesta);
    }
    function VentasPdf($id) {
      $condicionDatos = array('idFactura' => $id);
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
      $filename = "vendors/core/pdf/".$id.".pdf";
      $this->fpdf->Output($filename, "F");
      // exit;
      //$this->imprimirReporteComision($fechaInicio,$fechaFinal);
    }
    function ComprasPdf($id) {
      $condicionDatos = array('idCompra' => $id);
      $datosVenta = TraerUnDato("compras", $condicionDatos);

      $condicionDatos = array('idProveedor' => $datosVenta->idProveedor);
      $datosCliente = TraerUnDato("proveedor", $condicionDatos);

      $condicionDatosDocumento = array('aliasDocumento' => $datosVenta->tipoCompra);
      $datosDocumento = TraerUnDato("documento", $condicionDatosDocumento);

      $condicionVersion = array('codigo' => $datosDocumento->codigo);
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
      $path = base_url($emisor["logo"]);
      $this->fpdf->Image($path,$setX,$setY,25,25);

      $this->fpdf->SetFont('Helvetica','B', 9);

      // Salto de línea
      $this->fpdf->Ln(1);
      $this->fpdf->SetY(10);
      $this->fpdf->SetX(98);
      $this->fpdf->Cell(110, 5,utf8_decode("DOCUMENTO TRIBUTARIO ELECTRÓNICO"),'TLR', 1, 'C');
      // $this->fpdf->SetY(15);
      $this->fpdf->SetFont('Helvetica','B', 10);
      $this->fpdf->SetX(98);
      $this->fpdf->Cell(110, 5,utf8_decode($datosDocumento->nombreDocumento),'BLR', 1, 'C');

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
      $this->fpdf->Cell(25, 4,Fecha_D_M_A($datosVenta->fecha),'R', 1, 'L');

      $this->fpdf->SetX(98);
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Cell(30, 4,utf8_decode("Hora emisión: "),'LB', 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(80, 4,Hora($datosVenta->hora),'RB', 1, 'L');

      $this->qr = new QRcode();
      $data = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen=".$datosVenta->codigoGeneracion."&fechaEmi=".$datosVenta->fechaFactura;
      // $data = "https://admin.factura.gob.sv/consultaPublica?ambiente=01&codGen='".$datosVenta->codigoGeneracion."'&fechaEmi='".$datosVenta->fecha."'";
      // $data = $datosVenta->codigoGeneracion;
      $file = "vendors/core/img/qrs/".$id.".png";

      ///QR2
      if ($datosVenta->codigoGeneracion!= "")
      {
        $data1 = $datosVenta->codigoGeneracion;
      }
      else
      {
        $data1 = $id;
      }
      $file1 = "vendors/core/img/qrs/".$id."_codigoGeneracion.png";

      /////QR3
      if ($datosVenta->selloRecibido!= "")
      {
        $data2 = $datosVenta->selloRecibido;
      }
      else
      {
        $data2 = $id;
      }
      $file2 = "vendors/core/img/qrs/".$id."_selloRecibido.png";

      /////QR4

      if ($datosVenta->numeroControl!= "")
      {
        $data3 = $datosVenta->numeroControl;
      }
      else
      {
        $data3 = $id;
      }
      $file3 = "vendors/core/img/qrs/".$id."_numeroControl.png";

      $ecc = 'H';
      $pixel_size = 5;
      $frame_size = 1;
      $this->qr::png($data, $file, $ecc, $pixel_size, $frame_size);
      $this->qr::png($data1, $file1, $ecc, $pixel_size, $frame_size);
      $this->qr::png($data2, $file2, $ecc, $pixel_size, $frame_size);
      $this->qr::png($data3, $file3, $ecc, $pixel_size, $frame_size);



      $setX = 98;
      $setY = 48;
      $this->fpdf->Image($file,$setX,$setY,25,25);

      $setX = 133;
      $setY = 57;
      $this->fpdf->Image($file1,$setX,$setY,15,15);

      $setX = 163;
      $setY = 57;
      $this->fpdf->Image($file2,$setX,$setY,15,15);

      $setX = 193;
      $setY = 57;
      $this->fpdf->Image($file3,$setX,$setY,15,15);



      ///encabezados
      $this->fpdf->SetX(7);
      $this->fpdf->SetY(30);
      $this->fpdf->SetFont('Helvetica','B', 10);
      $this->fpdf->Cell(90, 5,utf8_decode($emisor["nombre_comercial"]),0, 1, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(90, 4,utf8_decode($emisor["giro"]),0, 1, 'L');
      $this->fpdf->Cell(90, 4,utf8_decode($emisor["complemento"]),0, 1, 'L');
      $this->fpdf->Cell(90, 4,utf8_decode($emisor["municipioNombre"].", ".$emisor["departamentoNombre"].", El Salvador"),0, 1, 'L');
      $this->fpdf->Cell(90, 4,utf8_decode("Telefono: ".$emisor["telefono"]),0, 1, 'L');
      $this->fpdf->Cell(90, 4,utf8_decode("Correo: ".$emisor["correo"]),0, 1, 'L');
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

      $this->fpdf->Ln(-3);
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Cell(200, 5,"Informacion del receptor",'B', 1, 'L');

      $this->fpdf->Cell(20, 4,utf8_decode("NOMBRE: "),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(110, 4,utf8_decode($datosCliente->nombreProveedor),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','B', 7);
      if ($datosCliente->facturarConProveedor == "DUI")
      {
        $this->fpdf->Cell(15, 4,utf8_decode("DUI: "),0, 0, 'L');
        $this->fpdf->SetFont('Helvetica','', 7);
        $this->fpdf->Cell(55, 4,utf8_decode($datosCliente->duiProveedor),0, 1, 'L');
      }
      else
      {
        $this->fpdf->Cell(15, 4,utf8_decode("NIT: "),0, 0, 'L');
        $this->fpdf->SetFont('Helvetica','', 7);
        $this->fpdf->Cell(55, 4,utf8_decode($datosCliente->nitProveedor),0, 1, 'L');
      }

      ////////////////////////
      // $this->fpdf->SetFont('Helvetica','B', 7);
      // $this->fpdf->Cell(20, 4,utf8_decode("Actividad: "),0, 0, 'L');
      // $this->fpdf->SetFont('Helvetica','', 7);
      // $this->fpdf->Cell(110, 4,utf8_decode($datosCliente->giroCliente),0, 0, 'L');
      // $this->fpdf->SetFont('Helvetica','B', 7);
      // if ($datosVenta->idDocumento == 2)
      // {
      //   $this->fpdf->Cell(15, 4,utf8_decode(""),0, 0, 'L');
      //   $this->fpdf->SetFont('Helvetica','', 7);
      //   $this->fpdf->Cell(55, 4,utf8_decode(""),0, 1, 'L');
      // }
      // else
      // {
      //   $this->fpdf->Cell(15, 4,utf8_decode("NRC: "),0, 0, 'L');
      //   $this->fpdf->SetFont('Helvetica','', 7);
      //   $this->fpdf->Cell(55, 4,utf8_decode($datosCliente->nrcCliente),0, 1, 'L');
      // }

      /////////////////////////
      $departamento = TraerUnDato('FE_CAT_012_Departamento',array("codigo"=>$datosCliente->departamentoProveedor));
      $departamentoClienteNombre = $departamento->valores;
      $municipio = TraerUnDato('FE_CAT_013_Municipio',array("codigo"=>$datosCliente->municipioProveedor,"departamento" => $datosCliente->departamentoProveedor));
      $municipioClienteNombre = $municipio->valores;
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Cell(20, 5,utf8_decode("Dirección: "),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(110, 5,utf8_decode($datosCliente->direccionCliente.", ".$municipioClienteNombre.", ".$departamentoClienteNombre),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Cell(15, 5,utf8_decode("Telefono: "),0, 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(55, 5,utf8_decode($datosCliente->telefonoProveedor),0, 1, 'L');

      //////////////////////////
      $this->fpdf->Cell(130, 5,'','B', 0, 'L');
      $this->fpdf->SetFont('Helvetica','B', 7);
      $this->fpdf->Cell(15, 5,utf8_decode("Correo: "),'B', 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(55, 5,utf8_decode($datosCliente->correoProveedor),'B', 1, 'L');


      $array_data = array(
        0 => array(utf8_decode("Cant"),15,"C"),
        1 => array(utf8_decode("Codigo"),15,"C"),
        2 => array(utf8_decode("Descripción"),70,"L"),
        3 => array(utf8_decode("Precio unitario"),20,"R"),
        4 => array(utf8_decode("Descuento"),20,"R"),
        5 => array(utf8_decode("Venta no sujeta"),20,"R"),
        6 => array(utf8_decode("Venta exenta"),20,"R"),
        7 => array(utf8_decode("Venta gravada"),20,"R"),
      );
      $this->fpdf->LineWriteB($array_data, 1,5);

      $joinDet = array(
        array(
          "tabla" => "producto",
          "condicion" => "compras_detalles.idProducto = producto.idProducto",
          "campos" => "nombreProducto, codProducto"
        ),
      );
      $datosDetalle = TraerDatosJoin("compras_detalles",array("compras_detalles.idCompra" => $id),"compras_detalles.idCompraDetalle DESC",$joinDet );
      $setYValor = $this->fpdf->GetY();
      $this->fpdf->Line(8, $setYValor, 8, $setYValor+140);
      $this->fpdf->Line(208, $setYValor, 208, $setYValor+140);

      $nDatosPedidoDetalle = 0;
      foreach ($datosDetalle as $fila)
      {
        $precioUnitarioMostrar = $fila->costoConIva;
        $subTotalMostrar = $fila->subtotal;
        $descuentoMostrar =  0;
        $array_data = array(
          0 => array(utf8_decode($fila->cant),15,"C"),
          1 => array(utf8_decode($fila->codProducto),15,"C"),
          2 => array(utf8_decode($fila->nombreProducto),70,"L"),
          3 => array(utf8_decode(number_format($precioUnitarioMostrar,2)),20,"R"),
          4 => array(utf8_decode($descuentoMostrar),20,"R"),
          5 => array(utf8_decode(""),20,"R"),
          6 => array(utf8_decode(""),20,"R"),
          7 => array(utf8_decode(number_format($subTotalMostrar,2)),20,"R"),
        );
        $this->fpdf->LineWriteB($array_data, 0,4);
        $nDatosPedidoDetalle += 1;
      }
      $valor_altura = $nDatosPedidoDetalle*4;
      $alturaReal =135-$valor_altura;
      $this->fpdf->Ln($alturaReal);
      $this->fpdf->SetFont('Helvetica','B', 7);
      $valorVenta = explode(".", number_format($datosVenta->total,2));
      $entero = $valorVenta[0];
      $centavos = $valorVenta[1];
      $this->fpdf->Cell(20, 8,utf8_decode("Valor en letras: "),'LTB', 0, 'L');
      $this->fpdf->SetFont('Helvetica','', 7);
      $this->fpdf->Cell(120, 8,utf8_decode(num2letras($entero)." con ".$centavos."/100"),"TB", 0, 'L');
      $this->fpdf->SetFont('Helvetica','',6);
      // $this->fpdf->Cell(21, 5,utf8_decode("Total operación"),"T", 0, 'L');
      // $this->fpdf->Cell(13, 5,utf8_decode("No sujetas ".$datosVenta->noSujetoFactura),"T", 0, 'L');
      // $this->fpdf->Cell(13, 5,utf8_decode("Exentas ".$datosVenta->excentoFactura),"T", 0, 'L');
      // $this->fpdf->Cell(13, 5,utf8_decode("Gravadas ".$datosVenta->sumasFactura),"TR", 1, 'L');
      $this->fpdf->SetX(148);
      $array_data = array(
        0 => array(utf8_decode("Total operación"),60,"C"),
      );
      $this->fpdf->LineWriteB1($array_data, 1,4);

      $this->fpdf->SetFont('Helvetica','',6);
      $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Sumas"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($datosVenta->total,2)),1, 1, 'R');

      $this->fpdf->Cell(140, 4,utf8_decode(""),"L", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Monto total de la operacion"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($datosVenta->total,2)),1, 1, 'R');

      $this->fpdf->SetFont('Helvetica','',6);
      $this->fpdf->Cell(140, 4,utf8_decode(""),"LB", 0, 'L');
      $this->fpdf->Cell(45, 4,utf8_decode("Total a pagar"),1, 0, 'L');
      $this->fpdf->Cell(15, 4,utf8_decode(number_format($datosVenta->total,2)),1, 1, 'R');
      $filename = "vendors/core/pdf/".$id.".pdf";
      ob_clean();
      $this->fpdf->Output($filename, "F");
      // exit;
    }

    function EnviarCorreo($idFactura = '', $parametro=''){
      $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
      if($fe_enabled == "Si"){
        $email="";
        if ($this->input->method(TRUE) == "POST") {
          $email = $this->input->post("email");
        }
        $condicionDatos = array('idfactura' => $idFactura);
        $join = array(
          array(
            "tabla" => "cliente",
            "condicion" => "cliente.idCliente = factura.idCliente",
          ),
        );
        $datosProducto = TraerUnDatoJoin('factura',$condicionDatos,$join);
        $codigoGeneracion = $datosProducto->codigoGeneracion;
        if($email == ""){
          $email = $datosProducto->emailCliente;
        }
        $nombreCliente = $datosProducto->nombreCliente;
        if($email != ""){
          $file = fopen("vendors/core/pdf/".$idFactura.".json", "w");
          $jsonMal = json_decode($datosProducto->jsonDTE, true);
          $jsonMal["selloRecibido"] = $datosProducto->selloRecibido;
          $jsonMal["jsonFirmado"] = $datosProducto->jsonFirmado;
          $jsonBien = json_encode($jsonMal,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
          fwrite($file,$jsonBien);
          fclose($file);
          ///////////////////////////////////////////
          $this->VentasPdf($idFactura);
          ///////////////////////////////////////////
          $attachment =array(
            array("name" => $idFactura.".pdf", "extension" => "pdf", "url" => "vendors/core/pdf/".$idFactura.".pdf"),
            array("name" => $idFactura.".json", "extension" => "json", "url" => "vendors/core/pdf/".$idFactura.".json"),
          );
          $contenido = $this->load->view("email/comprobante", array('datosProducto' => $datosProducto),TRUE);
          $arrayto = array( array('email' => $email,'name' => $nombreCliente));
          $send=MailSend($arrayto,'Facturación Electrónica',$contenido,$attachment);
          unlink("vendors/core/pdf/".$idFactura.".pdf");
          unlink("vendors/core/pdf/".$idFactura.".json");
          $datosRespuesta["codigo"] = 200;
        } else {
          $datosRespuesta["codigo"] = 404;
        }
      } else {
        $datosRespuesta["codigo"] = 200;
      }
      if ($parametro != "1") {
      // if ($this->input->method(TRUE) == "POST") {
        echo json_encode($datosRespuesta);
      } else {
        return $datosRespuesta["codigo"];
      }
    }
    function EnviarCorreoAnulacion($idFactura = ''){
      // if ($this->input->method(TRUE) == "POST") {
      $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
      if($fe_enabled == "Si"){
        $condicionDatos = array('idfactura' => $idFactura);
        $join = array(
          array(
            "tabla" => "cliente",
            "condicion" => "cliente.idCliente = factura.idCliente",
          )
        );
        $datosProducto = TraerUnDatoJoin('factura',$condicionDatos,$join);
        $codigoGeneracion = $datosProducto->codigoGeneracion;
        $email = $datosProducto->emailCliente;
        $nombreCliente = $datosProducto->nombreCliente;
        if($email != ""){
          ///////////////////////////////////////////
          $this->VentasPdf($idFactura);
          ///////////////////////////////////////////
          $attachment =array(
            array("name" => $idFactura.".pdf", "extension" => "pdf", "url" => "vendors/core/pdf/".$idFactura.".pdf"),
          );
          $contenido = $this->load->view("email/anulacion", array('datosProducto' => $datosProducto),TRUE);
          $arrayto = array( array('email' => $email,'name' => $nombreCliente));
          $send=MailSend($arrayto,'Anulación de Facturación Electrónica',$contenido,$attachment);
          unlink("vendors/core/pdf/".$idFactura.".pdf");
          $datosRespuesta["codigo"] = 200;
        } else {
          $datosRespuesta["codigo"] = 404;
        }
      } else {
        $datosRespuesta["codigo"] = 200;
      }
      // }
      return $datosRespuesta["codigo"];
      // echo json_encode($datosRespuesta);

    }
    function EnviarCorreoCompra($idCompra = ''){
      $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
      if($fe_enabled == "Si"){
        // if ($this->input->method(TRUE) == "POST") {
        $condicionDatos = array('idCompra' => $idCompra);
        $join = array(
          array(
            "tabla" => "proveedor",
            "condicion" => "proveedor.idProveedor = compras.idProveedor",
          ),
          array(
            "tabla" => "documento",
            "condicion" => "documento.aliasDocumento = compras.tipoCompra",
          ),
          array(
            "tabla" => "FE_CAT_002_TipodeDocumento",
            "condicion" => "FE_CAT_002_TipodeDocumento.codigo = documento.codigo",
          ),
        );
        $datosProducto = TraerUnDatoJoin('compras',$condicionDatos,$join);
        $datosProducto->fechaFactura = $datosProducto->fecha;
        $datosProducto->totalFactura = $datosProducto->total;
        $datosProducto->nombreCliente = $datosProducto->nombreProveedor;
        $codigoGeneracion = $datosProducto->codigoGeneracion;
        $email = $datosProducto->correoProveedor;
        $nombreCliente = $datosProducto->nombreProveedor;
        if($email != ""){
          ///////////////////////////////////////////
          $this->ComprasPdf($idCompra);
          ///////////////////////////////////////////
          $attachment =array(
            array("name" => $idCompra.".pdf", "extension" => "pdf", "url" => "vendors/core/pdf/".$idCompra.".pdf"),
          );
          $contenido = $this->load->view("email/comprobante", array('datosProducto' => $datosProducto),TRUE);
          $arrayto = array( array('email' => $email,'name' => $nombreCliente));
          $send=MailSend($arrayto,'Facturación Electrónica',$contenido,$attachment);
          unlink("vendors/core/pdf/".$idCompra.".pdf");
        }
        // }
        $datosRespuesta["codigo"] = 200;
      } else {
        $datosRespuesta["codigo"] = 200;
      }
      echo json_encode($datosRespuesta);

    }

    function anularCompra($idCompra){
      /**********************************************************************/
      /**********************************************************************/
      $condicionDatos = array('idCompra' => $idCompra);
      $datosCompra = TraerUnDato("compras", $condicionDatos);
      $estadoCompra = $datosCompra->estadoCompra;
      $prodsDev = true;
      if($estadoCompra != "Anulado"){
        $datosArrayMH = array(
          "estadoCompra" => 'Anulado',
          "aleatorioCompra" => uniqid()
        );
        IniciarTransaccion();
        $condicion = array('idCompra' => $idCompra);
        $editar = EditarDatos("compras",$datosArrayMH,$condicion);
        if($editar){
          $condicionDatosDetalle = array('idCompra' => $idCompra);
          $datosCompraDetalle = TraerDatos("compras_detalles", $condicionDatosDetalle);
          $errorl=false;
          if($datosCompraDetalle !== null){
            foreach ($datosCompraDetalle as $CompraDetalle) {
              $condicionDatos = array('idProductoPresentacion' => $CompraDetalle->idProductoPresentacion);
              $datosProductoPresentacion = TraerUnDato("productoPresentacion", $condicionDatos);
              $unidad = $datosProductoPresentacion->unidadProductoPresentacion;
              $cantidadreal =$unidad * $CompraDetalle->cant;

              $condicionLote = array('idProducto' => $CompraDetalle->idProducto);
              $datosLote = TraerUnDato("lote", $condicionLote);
              $costoPromedio = $datosLote->costo_promedio;
              $costoReal = $costoPromedio * $unidad;

              $response_lotes = $this->lotes->DescargaLote(
                "AnulacionCompra",
                $CompraDetalle->idCompraDetalle,
                $CompraDetalle->idProducto,
                $CompraDetalle->idProductoPresentacion,
                $this->session->idSucursal,
                $CompraDetalle->cant,
                $cantidadreal,
                $unidad,
                $costoReal,
                "",
                "",
                "");

                if($response_lotes['Error']==true){
                  $errorl = true;
                }
              }
            }
            if(!$errorl){
              //exito
              EjecutarTransaccion();
            } else {
              //lotes
              DeshacerTransaccion();
              $prodsDev = false;
            }
          } else {
            DeshacerTransaccion();
            $prodsDev = false;
            /// NO ANULA
          }
        }
        $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
        if($fe_enabled == "Si"){
          if($datosCompra->tipoCompra == "FSE"){
            $condicionDatosDocumento = array('aliasDocumento' => $datosCompra->tipoCompra);
            $joinDatosDocumento = array(
              array(
                'tabla' => 'FE_CAT_002_TipodeDocumento AS fedoc',
                'tipo' => 'inner',
                'condicion' => 'fedoc.codigo = documento.codigo',
                'campos' => 'fedoc.version'
              ),
            );
            $datosDocumento = TraerUnDatoJoin("documento", $condicionDatosDocumento,$joinDatosDocumento);
            $codigoDocumento = $datosDocumento->codigo;
            $versionDocumentoA = 2;
            /****************************************************************/
            /****************************************************************/
            $selloRecibidoAnulacion = $datosCompra->selloRecibidoAnulacion;
            $codigoGeneracionAnulacion = $datosCompra->codigoGeneracionAnulacion;

            $codigoGeneracion = $datosCompra->codigoGeneracion;
            $selloRecibido = $datosCompra->selloRecibido;
            //$selloRecibido = "";
            if($selloRecibido != ""){
              $numeroControl = $datosCompra->numeroControl;
              $total = $datosCompra->total;
              $iva = $datosCompra->iva;

              $ultimoCorrelativo = $datosCompra->numero;

              $fecha = $datosCompra->fecha;
              $hora = $datosCompra->hora;
              $fechaAnu = date("Y-m-d");
              $horaAnu = date("H:i:s");
              $tipoPago = 'Contado';
              /****************************************************************/
              /****************************************************************/
              $condicionDatos = array('idProveedor' => $datosCompra->idProveedor);
              $joinDatosProveedor = array(
                array(
                  'tabla' => 'FE_CAT_019_CodigodeActividadEco as giros',
                  'tipo' => 'left',
                  'condicion' => 'giros.codigo=proveedor.giroProveedor',
                  'campos' => 'giros.valores as giro'
                ),
              );
              $datosProveedor = TraerUnDatoJoin("proveedor", $condicionDatos,$joinDatosProveedor);

              $documentoProveedor = str_replace("-","",$datosProveedor->nitProveedor);
              $tipoDocumentoProveedor = "36";
              if($datosProveedor->facturarConProveedor == "DUI"){
                $tipoDocumentoProveedor = "13";
                $documentoProveedor = $datosProveedor->duiProveedor;
              }
              if($documentoProveedor == ""){
                $tipoDocumentoProveedor = "37";
                $documentoProveedor = "000000000";
              }
              $telefonoProveedor = $datosProveedor->telefonoProveedor;
              if($datosProveedor->telefonoProveedor == ""){
                $telefonoProveedor = "0000-0000";
              }
              $emailProveedor = $datosProveedor->correoProveedor;
              if($datosProveedor->correoProveedor == ""){
                $emailProveedor = GblTraerConfiguracionFe("correoEmisor");
              }

              $condicionDatosDocumento = array('idDocumento' => $datosDocumento->idDocumento);
              $joinDatosDocumento = array(
                array(
                  'tabla' => 'FE_CAT_002_TipodeDocumento AS fedoc',
                  'tipo' => 'inner',
                  'condicion' => 'fedoc.codigo = documento.codigo',
                  'campos' => 'fedoc.version'
                ),
              );
              $datosDocumento = TraerUnDatoJoin("documento", $condicionDatosDocumento,$joinDatosDocumento);
              $codigoDocumento = $datosDocumento->codigo;

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

              /**********************************************************************/
              /**********************************************************************/
              if($selloRecibidoAnulacion == ""){

                if($codigoGeneracionAnulacion == ""){
                  $codigoGeneracionAnulacion = generarUuid();
                }
                ////////////////////////////////////////////////////
                ////////////////////////////////////////////////////

                ////////////////////////////////////////////////////
                $identificacion = array (
                  'version' => round($versionDocumentoA,0),
                  'ambiente' => GblTraerConfiguracionFe("ambiente"),
                  'codigoGeneracion' => $codigoGeneracionAnulacion,
                  'fecAnula' => $fechaAnu,
                  'horAnula' => $horaAnu,
                );
                $emisor = array(
                  'nit' =>GblTraerConfiguracionFe('nitEmisor'),
                  'nombre' =>GblTraerConfiguracionFe('nombreEmisor'),
                  'tipoEstablecimiento' => GblTraerConfiguracionFe('tipoEstablecimientoEmisor'),
                  'nomEstablecimiento' => GblTraerConfiguracionFe('nombreComercialEmisor'),
                  'codEstableMH' => GblTraerConfiguracionFe("codEstableMH"),
                  'codEstable' => GblTraerConfiguracionFe("codEstableMH"),
                  'codPuntoVentaMH' => GblTraerConfiguracionFe("codPuntoVentaMH"),
                  'codPuntoVenta' => GblTraerConfiguracionFe("codPuntoVentaMH"),
                  'telefono' => GblTraerConfiguracionFe('telefonoEmisor'),
                  'correo' => GblTraerConfiguracionFe('correoEmisor'),
                );
                ////////////////////////////////////////////////////
                ////////////////////////////////////////////////////
                $dte = array (
                  'identificacion' => $identificacion,
                  'emisor' => $emisor,
                  // 'receptor' => $receptor,
                  'documento' => array(
                    'tipoDte' => $codigoDocumento,
                    'codigoGeneracion' => $codigoGeneracion,
                    'selloRecibido' => $selloRecibido,
                    'numeroControl' => $numeroControl,
                    'fecEmi' => $fecha,
                    'montoIva' => round($total,2),
                    'codigoGeneracionR' => null,
                    'tipoDocumento' => $tipoDocumentoProveedor,
                    'numDocumento' => $documentoProveedor,
                    'nombre' => $datosProveedor->nombreProveedor,
                    'telefono' => $telefonoProveedor,
                    'correo' => $emailProveedor
                  ),
                  'motivo' => array(
                    "tipoAnulacion" => round(2,0),
                    "motivoAnulacion" => "ANULACION DE LA OPERACION",
                    "nombreResponsable" => GblTraerConfiguracionFe("nombreEmisor"),
                    "tipDocResponsable" => "36",
                    "numDocResponsable" => GblTraerConfiguracionFe("nitEmisor"),
                    "nombreSolicita" => $datosProveedor->nombreProveedor,
                    "tipDocSolicita" => $tipoDocumentoProveedor,
                    "numDocSolicita" => $documentoProveedor
                  ),
                );

                $dteFirmado = firmarDte($dte);
                $doc = anularDocumento($ultimoCorrelativo,$versionDocumentoA,$dteFirmado);
                // print_r(json_encode($dte));
                // echo "<hr>";
                // print_r($dteFirmado);
                // echo "<hr>";
                // print_r(json_encode($doc));
                // echo "<hr>";

                if($doc["status"] =="OK"){
                  $datosArrayMH = array(
                    "selloRecibidoAnulacion" => $doc["selloRecibido"],
                    "fhProcesamientoAnulacion" => $doc["fhProcesamiento"],
                    "codigoGeneracionAnulacion" => $codigoGeneracionAnulacion,
                    "jsonDTEAnulacion" => json_encode($dte),
                    "jsonFirmadoAnulacion" => $dteFirmado,
                    "aleatorioCompra" => uniqid(),
                  );
                  $datosRespuesta["codigo"]=200;
                  $datosRespuesta["devolucion"]=$prodsDev;
                  $datosRespuesta["dte"]=true;

                } else {
                  $datosArrayMH = array(
                    "codigoGeneracionAnulacion" => $codigoGeneracionAnulacion,
                    "errorAnulacion" => json_encode($doc),
                    "aleatorioCompra" => uniqid()
                  );
                  $datosRespuesta["idCompra"]=$idCompra;
                  $datosRespuesta["codigo"]=500;
                  $datosRespuesta["devolucion"]=$prodsDev;
                  $datosRespuesta["dte"]=false;
                }
                $condicion = array('idCompra' => $idCompra);
                $editar = EditarDatos("compras",$datosArrayMH,$condicion);
                $mail = $this->EnviarCorreoAnulacionCompra($idCompra);
                if($datosRespuesta["codigo"] == 200){
                  if($mail == 500){
                    $datosRespuesta["codigo"]=203;
                  }
                }

              } else {
                $datosRespuesta["codigo"]=403;
                $datosRespuesta["devolucion"]=$prodsDev;
                $datosRespuesta["dte"]=false;
              }
            } else {
              $datosRespuesta["codigo"]=200;
              $datosRespuesta["devolucion"]=$prodsDev;
              $datosRespuesta["dte"]=false;
            }
          } else {
            $datosRespuesta["codigo"]=200;
            $datosRespuesta["devolucion"]=$prodsDev;
            $datosRespuesta["dte"]=false;
          }
        } else {
          $datosRespuesta["idCompra"]=$idCompra;
          $datosRespuesta["codigo"]=200;
          $datosRespuesta["devolucion"]=$prodsDev;
          $datosRespuesta["dte"]=false;
        }
        echo json_encode($datosRespuesta);
      }

      function EnviarCorreoAnulacionCompra($idCompra = ''){
        $fe_enabled = GblTraerConfiguracion("facturacion_electronica");
        if($fe_enabled == "Si"){
          // if ($this->input->method(TRUE) == "POST") {
          $condicionDatos = array('idCompra' => $idCompra);
          $join = array(
            array(
              "tabla" => "proveedor",
              "condicion" => "proveedor.idProveedor = compras.idProveedor",
            ),
            array(
              "tabla" => "documento",
              "condicion" => "documento.aliasDocumento = compras.tipoCompra",
            ),
            array(
              "tabla" => "FE_CAT_002_TipodeDocumento",
              "condicion" => "FE_CAT_002_TipodeDocumento.codigo = documento.codigo",
            ),
          );
          $datosProducto = TraerUnDatoJoin('compras',$condicionDatos,$join);
          $datosProducto->fechaFactura = $datosProducto->fecha;
          $datosProducto->totalFactura = $datosProducto->total;
          $datosProducto->nombreCliente = $datosProducto->nombreProveedor;
          $codigoGeneracion = $datosProducto->codigoGeneracion;
          $email = $datosProducto->correoProveedor;
          $nombreCliente = $datosProducto->nombreProveedor;
          if($email != ""){
            ///////////////////////////////////////////
            $this->ComprasPdf($idCompra);
            ///////////////////////////////////////////
            $attachment =array(
              array("name" => $idCompra.".pdf", "extension" => "pdf", "url" => "vendors/core/pdf/".$idCompra.".pdf"),
            );
            $contenido = $this->load->view("email/anulacion", array('datosProducto' => $datosProducto),TRUE);
            $arrayto = array( array('email' => $email,'name' => $nombreCliente));
            $send=MailSend($arrayto,'Anulación de Facturación Electrónica',$contenido,$attachment);
            unlink("vendors/core/pdf/".$idCompra.".pdf");
          }
          // }
          $datosRespuesta["codigo"] = 200;
        } else {
          $datosRespuesta["codigo"] = 200;
        }
        return $datosRespuesta["codigo"];
      }

  }
  /* End of file Fe.php */
