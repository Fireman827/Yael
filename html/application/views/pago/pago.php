<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="<?= base_url()."/".GblTraerConfiguracion("logoEmpresa");?>" rel="icon">
  <title>DigitalsPos | Pago Mensual</title>
  <style>
  @font-face { font-family: Arial !important; font-display: swap !important; }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="<?=base_url();?>/vendors/core/js/adminlte.js"></script>
  <link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/pago.css">
  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <script src="<?=base_url();?>/vendors/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?=base_url();?>/vendors/plugins/mask/jquery.mask.min.js"></script>
  <script src="<?=base_url();?>/vendors/plugins/jquery-validation/jquery.validate.min.js"></script>
  <script src="<?=base_url();?>/vendors/plugins/jquery-validation/additional-methods.min.js"></script>
  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/dropify/css/dropify.css">
  <script src="<?=base_url()?>/vendors/plugins/dropify/js/dropify.min.js"></script>
  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/select2/css/select2.css">
  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/select2-bootstrap4-theme/select2-bootstrap4.css">
  <script src="<?=base_url();?>/vendors/plugins/select2/js/select2.full.min.js"></script>
  <style>
  </style>
</head>
<body classname="snippet-body">
  <div class="cargando" style="display: none;">cargando&#8230;</div>
  <div class="container d-flex justify-content-center mt-5 mb-5">
    <?php if ($numeroDias>0): ?>
      <div class="row g-3">
        <div class="col-md-3">
        </div>
        <div class="col-md-6">
          <div class="alert alert-primary mt-3" role="alert" >
            <strong> La cuota esta fuera de vigencia!</strong>
          </div>
        </div>
        <div class="col-md-3">
        </div>
      </div>
    <?php else: ?>
      <div class="row g-3">
        <div class="col-md-6">
          <span>Resumen</span>
          <div class="card">
            <div class="d-flex justify-content-between p-3 filaDetallePago">
              <div class="d-flex flex-column">
                <input type="hidden" class="cantidadDetallePago" value="1">
                <input type="hidden" class="precioDetallePago" value="<?=$alojamientoMensual ?>">
                <input type="hidden" class="subtotalDetallePago" value="<?=$alojamientoMensual ?>">
                <span style="font-weight: bold;" class="descripcionDetallePago">Alojamiento Mensual</span>
              </div>
              <div class="mt-1">
                <sup class="super-price" style="font-weight: bold;">$ <?=number_format($alojamientoMensual,2,".","")?></sup>
              </div>
            </div>
            <?php if ($facturacionElectronica == 'Si'): ?>
              <?php if ($tipoCobroFe == 'Por Documento'): ?>
                <hr class="mt-0 line">
                <div class="p-3">
                  <?php if ($numeroCreditoFiscal>0): ?>
                    <div class="d-flex justify-content-between mb-2 filaDetallePago">
                      <div class="d-flex flex-column">
                        <input type="hidden" class="cantidadDetallePago" value="<?=$numeroCreditoFiscal ?>">
                        <input type="hidden" class="precioDetallePago" value="<?=$valorTransaccion ?>">
                        <input type="hidden" class="subtotalDetallePago" value="<?=$totalPagarCreditoFiscal ?>">
                        <span style="font-weight: bold;" class="descripcionDetallePago">Credito Fiscal</span>
                        <small>Cant: <?=$numeroCreditoFiscal ?></small>
                        <small>Precio: $<?=number_format($valorTransaccion,2,".","")?></small>
                      </div>
                      <span style="font-weight: bold;">$<?=number_format($totalPagarCreditoFiscal,2,".","")?></span>
                    </div>
                  <?php endif; ?>
                  <?php if ($numeroFactura>0): ?>
                    <div class="d-flex justify-content-between mb-2 filaDetallePago">
                      <div class="d-flex flex-column">
                        <input type="hidden" class="cantidadDetallePago" value="<?=$numeroFactura ?>">
                        <input type="hidden" class="precioDetallePago" value="<?=$valorTransaccion ?>">
                        <input type="hidden" class="subtotalDetallePago" value="<?=$totalPagarFactura ?>">
                        <span style="font-weight: bold;" class="descripcionDetallePago">Consumidor Final</span>
                        <small>Cant: <?=$numeroFactura ?></small>
                        <small>Precio: $<?=number_format($valorTransaccion,2,".","")?></small>
                      </div>
                      <span style="font-weight: bold;">$<?=number_format($totalPagarFactura,2,".","")?></span>
                    </div>
                  <?php endif; ?>
                  <?php if ($numeroSujetoExcluido): ?>
                    <div class="d-flex justify-content-between mb-2 filaDetallePago">
                      <div class="d-flex flex-column">
                        <input type="hidden" class="cantidadDetallePago" value="<?=$numeroSujetoExcluido ?>">
                        <input type="hidden" class="precioDetallePago" value="<?=$valorTransaccion ?>">
                        <input type="hidden" class="subtotalDetallePago" value="<?=$totalPagarSujetoExcluido ?>">
                        <span style="font-weight: bold;" class="descripcionDetallePago">Sujeto Excluido</span>
                        <small>Cant: <?=$numeroSujetoExcluido ?></small>
                        <small>Precio: $<?=number_format($valorTransaccion,2,".","")?></small>
                      </div>
                      <span style="font-weight: bold;">$<?=number_format($totalPagarSujetoExcluido,2,".","")?></span>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            <?php endif; ?>
            <hr class="mt-0 line">
            <div class="p-3 d-flex justify-content-between">
              <div class="d-flex flex-column">
                <span style="font-weight: bold;">Total</span>
              </div>
              <span style="font-weight: bold;">$<?=number_format($totalPagar,2,".","")?></span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <span>Método de pago</span>
          <div class="card">
            <div class="accordion" id="accordionExample">
              <div class="card">
                <div class="card-header p-0" id="headingTwo">
                  <h2 class="mb-0">
                    <button class="btn btn-light btn-block text-left p-3 rounded-0 border-bottom-custom collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                      <div class="d-flex align-items-center justify-content-between">
                        <span>Transferencia bancaria</span>
                      </div>
                    </button>
                  </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample" style="">
                  <div class="card-body">
                    <form id="FrmPagoAgregarTransferencia" autocomplete="off">
                      <span class="font-weight-normal card-text">Cuentas Bancarias</span>
                      <select  class="select2" style="width:100%" name="idCuentaBancaria" id="idCuentaBancaria" required>
                        <option value="" numeroCuenta='' nombreDestinatario='' correoElectronicoDestinatario=''>Seleccione</option>

                        <?php if ($datosCuentasBancarias!=false): ?>
                          <?php foreach ($datosCuentasBancarias as $key): ?>
                            <option value="<?=$key->idCuentaBancaria ?>" numeroCuenta='<?=$key->numeroCuenta ?>' nombreDestinatario='<?=$key->nombreDestinatario ?>' correoElectronicoDestinatario='<?=$key->correoElectronicoDestinatario ?>'><?=$key->nombreBanco ?></option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                      <div class="invalid-feedback">
                        Por favor, seleccione una cuenta Bancaria.
                      </div>
                      <div class="alert alert-primary mt-3" role="alert" id='informacionCuentaBancaria' style="display:none">
                        <strong> Banco: </strong><span id="nombreBanco"></span><br>
                        <strong> Número de cuenta: </strong><span id="numeroCuenta"></span><br>
                        <strong> Nombre del destinatario: </strong><span id="nombreDestinatario"></span><br>
                        <strong> Correo electrónico del destinatario: </strong><span id="correoElectronicoDestinatario"></span><br>
                      </div>
                      <span class="font-weight-normal card-text">Nombre del Titular</span>
                      <div class="input">
                        <i class="fa fa-user"></i>
                        <input type="text" class="form-control" placeholder="" name="nombreTransferencia" id="nombreTransferencia" required>
                        <div class="invalid-feedback">
                          Este campo es obligatorio.
                        </div>
                      </div>
                      <span class="font-weight-normal card-text">Captura</span>
                      <input type="file" class="form-control dropify" accept="image/*" data-height="170"  name="capturaTransferencia" id="capturaTransferencia" required aria-label="file example">
                      <div class="invalid-feedback">
                        Este campo es obligatorio.
                      </div>
                      <span class="font-weight-normal card-text">Correo Electronico</span>
                      <div class="input">
                        <i class="fa fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="" name="correoTransferencia" id="correoTransferencia" required>
                        <div class="invalid-feedback">
                          Por favor, escribe una dirección de correo válida.
                        </div>
                      </div>
                      <div class="pf-3">
                        <input type="hidden" id="detallePagoTransferencia" name="detallePagoTransferencia" value="">
                        <input type="hidden" id="idPagoTransferencia" name="idPagoTransferencia" value="<?=$datosPago->idPago ?>">
                        <input type="hidden" id="totalTransferencia" name="totalTransferencia" value="<?=$totalPagar ?>">
                        <button type="submit" class="btn btn-primary btn-block d-flex justify-content-between mt-3">
                          <span class="total_services_text">$ <?=number_format($totalPagar,2,".","")?></span><span>Pagar<i class="fa fa-credit-card ml-1"></i></span>
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="card">
                <div class="card-header p-0">
                  <h2 class="mb-0">
                    <button class="btn btn-light btn-block text-left p-3 rounded-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                      <div class="d-flex align-items-center justify-content-between">
                        <span>Tarjeta de crédito</span>
                      </div>
                    </button>
                  </h2>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample" style="">
                  <div class="card-body payment-card-body">
                    <form id="FrmPagoAgregarTarjeta" autocomplete="off">
                      <span class="font-weight-normal card-text">Nombre del Titular</span>
                      <div class="input">
                        <i class="fa fa-user"></i>
                        <input type="text" class="form-control" placeholder="" name="nombreTarjeta" id="nombreTarjeta" required>
                        <div class="invalid-feedback">
                          Este campo es obligatorio.
                        </div>
                      </div>
                      <span class="font-weight-normal card-text">Correo Electronico</span>
                      <div class="input">
                        <i class="fa fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="" name="correoTarjeta" id="correoTarjeta" required>
                        <div class="invalid-feedback">
                          Por favor, escribe una dirección de correo válida.
                        </div>
                      </div>
                      <span class="font-weight-normal card-text">Número de tarjeta</span>
                      <div class="input">
                        <i class="fa fa-credit-card"></i>
                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" name="numeroTarjeta" id="numeroTarjeta" required>
                        <div class="invalid-feedback">
                          Por favor, escribe un número de tarjeta válido.
                        </div>
                      </div>
                      <div class="row mt-3 mb-3">
                        <div class="col-md-6">
                          <span class="font-weight-normal card-text">Fecha de caducidad</span>
                          <div class="input">
                            <i class="fa fa-calendar"></i>
                            <input type="text" class="form-control" placeholder="MM/YY" id="mmaaTarjeta" name="mmaaTarjeta" required>
                            <div class="invalid-feedback">
                              Este campo es obligatorio.
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <span class="font-weight-normal card-text">CVC/CVV</span>
                          <div class="input">
                            <i class="fa fa-lock"></i>
                            <input type="password" class="form-control" placeholder="000" id="ccvTarjeta" name="ccvTarjeta" required>
                            <div class="invalid-feedback">
                              Este campo es obligatorio.
                            </div>
                          </div>
                        </div>
                        <div class="pf-3">
                          <input type="hidden" id="detallePagoTarjeta" name="detallePagoTarjeta" value="">
                          <input type="hidden" id="idPagoTarjeta" name="idPagoTarjeta" value="<?=$datosPago->idPago ?>">
                          <input type="hidden" id="totalTarjeta" name="totalTarjeta" value="<?=$totalPagar ?>">
                          <button class="btn btn-primary btn-block d-flex justify-content-between mt-3">
                            <span class="total_services_text">$ <?=number_format($totalPagar,2,".","")?></span><span>Pagar<i class="fa fa-credit-card ml-1"></i></span>
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="<?=base_url();?>/scripts/agregarPago.js"></script>
  <script type="text/javascript">
  var myLink = document.querySelectorAll('a[href="#"]');
  myLink.forEach(function(link){
    link.addEventListener('click', function(e) {
      e.preventDefault();
    });
  });
</script>

</body>
</html>
