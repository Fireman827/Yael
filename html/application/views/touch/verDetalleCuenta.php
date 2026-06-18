<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
    <span class="modal-title"> <i class="<?= $icono; ?>"></i> <?= $titulo; ?></span>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true"><i class="fa fa-times"></i></span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

        </div>

        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <?php if($pedido->tipoCuentaPedido == "local"):?>
                <a style="margin-left:2%;" class="btn btn-primary btn-lg btn-block UnirCuenta" role="button" idPedido="<?=$pedido->idPedido?>"><i class="fa fa-compress-arrows-alt"></i> UNIR</a>
				<a style="margin-left:2%;" class="btn btn-primary btn-lg btn-block DividirCuenta" role="button" idPedido="<?=$pedido->idPedido?>"><i class="fa fa-arrows-alt"></i> DIVIDIR</a>
            <?php endif;?>
            <a style="margin-left:2%;" class="btn btn-primary btn-lg btn-block AgregarCuenta"   data-dismiss="modal" role="button" idPedido="<?=$pedido->idPedido?>"><i class="fa fa-plus"></i> AGREGAR</a>
            <a style="margin-left:2%;" class="btn btn-success btn-lg btn-block ImprimirCuenta"  data-dismiss="modal" role="button" idPedido="<?=$pedido->idPedido?>"><i class="fa fa-receipt"></i> IMPRIMIR</a>
            <a style="margin-left:2%;" class="btn btn-warning btn-lg btn-block CobrarCuenta"    data-dismiss="modal" role="button" total="<?=$pedido->totalPedido?>" idPedido="<?=$pedido->idPedido?>"><i class="fa fa-dollar-sign"></i> COBRAR</a>
            <?php
                $corte = TraerUnDatoIndividual("corteCajas","idCorteCaja",array("estadoCorte" => "Vigente","idTurnoVigente >"=>1));
                $idCorte = ($corte) ? $corte[0]["idCorteCaja"] : 0;
                $usuarioCorte = TraerUnDatoIndividual("corteCaja","idUsuarioCorte,idTurnoVigente",array("idCorteCaja" => $idCorte));
                $idUsuarioCorte = ($usuarioCorte) ? $usuarioCorte[0]["idUsuarioCorte"]: 0;
            ?>
            <?php if($this->session->idUsuario == $idUsuarioCorte){?>
                <a style="margin-left:2%;" class="btn btn-danger  btn-lg btn-block AnularCuenta"    data-dismiss="modal" role="button" idPedido="<?=$pedido->idPedido?>"><i class="fa fa-times"></i> ANULAR</a>
            <?php }?>
        
        </div>

    </div>
</div>
<div class="modal-footer">
    <!-- <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button> -->
</div>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<!-- <?php //if (isset($proceso)) { 
        ?>
	<input type="hidden" value="<?= $proceso; ?>" id="proceso">
<?php //} 
?> -->
<!-- /.modal-content -->