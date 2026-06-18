<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<table style="height:100%;width:100%;background-color:#ebebeb;font-family:Arial" cellspacing="10">
<tbody>
 <tr style="height:100%" valign="top">
  <td>
   <table style="height:71px;background-color:#ffffff;margin-left:auto;margin-right:auto;max-width:650px;border-radius:10px" width="85%" cellspacing="20">
    <tbody>
     <tr>
      <td>
       <table style="width:100%">
        <tbody>
         <tr>
          <td style="border-radius:5px;background-color:#2990fe">
           <p style="text-align:center;margin:15px">
            <strong>ANULACIÓN DE DOCUMENTO ELECTRÓNICO</strong>
           </p>
          </td>
         </tr>
         <tr>
          <td>
           <p style="margin:15px">
            <strong>Estimado Cliente: <?=$datosProducto->nombreCliente ?></strong>
           </p>
           <p style="margin:15px;text-align:justify">Le saludamos de <?=GblTraerConfiguracionFe('nombreComercialEmisor') ?>, adjunto enviamos Documentos Tributarios Electrónicos, el cual fue aunulado.</p>
           <p style="margin:15px;text-align:justify">Detalle del documento:</p>
          </td>
         </tr>
         <tr>
          <td style="border-radius:5px;background-color:#89c0fc">
           <p style="text-align:left;margin:15px 15px 2px 15px">
            <strong>Numero de Factura Electrónica:</strong> <?=$datosProducto->codigoGeneracion ?></p>
           <p style="text-align:left;margin:2px 15px 2px 15px">
            <strong>Numero de Control:</strong> <?=$datosProducto->numeroControl ?></p>
           <p style="text-align:left;margin:2px 15px 2px 15px">
            <strong>Fecha:</strong><?=Fecha_D_M_A($datosProducto->fechaFactura) ?></p>
           <p style="text-align:left;margin:2px 15px 2px 15px">
            <strong>Tipo de documento tributario:</strong> <?=$datosProducto->tipoDocumentoFactura ?></p>
           <p style="text-align:justify;margin:2px 15px 15px 15px">
            <strong>Monto:</strong> <?=$datosProducto->totalFactura ?></p>
            <p style="text-align:left;margin:15px 15px 2px 15px">
            <strong>Numero de Anulación:</strong> <?=$datosProducto->codigoGeneracionAnulacion ?></p>
            <p style="text-align:left;margin:2px 15px 2px 15px">
            <strong>Sello de recepción de Anulación:</strong> <?=$datosProducto->selloRecibidoAnulacion ?></p>
            <p style="text-align:left;margin:2px 15px 2px 15px">
            <strong>Fecha Anulación:</strong><?=$datosProducto->fhProcesamientoAnulacion ?></p>
          </td>
         </tr>
        </tbody>
       </table>
      </td>
     </tr>
    </tbody>
   </table>
  </td>
 </tr>
</tbody>
</table>
