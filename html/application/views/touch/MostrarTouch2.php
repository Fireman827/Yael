<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-5 left-side" >
						<div class="row-horizon" id="list_orden">
							<span class="holdList">
								<span class="Hold pl selectedGat"  id="home_re"><i class="fa fa-home"></i></span>

									<span class="orden Hold" id="">mesa #1
										<input type="hidden" id="id_orden" name="id_orden" value="" />
										<input type="hidden" id="id_tip_ord" name="id_tip_ord" value="" />
									</span>
							</span>
						</div>
					<div class="row" id="tipo_orden">
						<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
							<br>
							<button type="button" class="btn btn-danger col-md-12 col-lg-12 col-sm-4 col-xs-4 flat-box-btn mov_izq" id="pcomer" style="margin-top:2%;"><h5 class="text-bold">PARA COMER</h5></button>
							<button type="button" class="btn btn-success col-md-12 col-lg-12 col-sm-4 col-xs-4 flat-box-btn mov_izq" id="pllevar"  style="margin-top:2%;"><h5 class="text-bold">PARA LLEVAR</h5></button>
							<button type="button" class="btn btn-info col-md-12 col-lg-12 col-sm-4 col-xs-4 flat-box-btn " id="pdomicilio"  style="margin-top:2%;"><h5 class="text-bold">A DOMICILIO</h5></button>
						</div>
					</div>
					<div id="cliente_varios">

					</div>
					<div id="para_cobrar" hidden>
						<div class="row" id="para_llevar" >
							<div class="col-xs-6">
								<h2>Cliente</h2>
							</div>
							<div class="col-xs-6 client-add">

							</div>
						</div>
						<div class="row" id="para_domicilio" >
							<div class="col-xs-6">
								<br>
								<h2>Cliente</h2>
							</div>
							<div class="col-xs-6 client-add">
								<a href="cliente_nuevo.php" data-toggle="modal" data-target="#AddCustomer">
									<span class="fa-stack fa-lg" data-toggle="tooltip" data-placement="top" title="Agregar Cliente">
										<i class="fa fa-square fa-stack-2x grey"></i>
										<i class="fa fa-user-plus fa-stack-1x fa-inverse dark-blue"></i>
									</span>
								</a>
							</div>
						</div>
						<div class="row" id="para_comer" >
							<div class="col-xs-6">
								<h2>Cliente</h2>
							</div>
							<div class="col-xs-6 client-add">

							</div>
						</div>

						<div class="col-xs-6">
							<div class="form-group">
								<label for="CustomerName">Nombre</label>
								<input type="text" placeholder="" class="form-control" id="auto_cliente" name="auto_cliente" data-provide="typeahead" autocomplete="off" readonly>
								<input type="text" placeholder="" class="form-control" id="cliente" name="cliente" autocomplete="off" readonly>
								<input type="hidden" name="id_cliente" id="id_cliente" value="">
							</div>
						</div>
						<div class="col-xs-2" id="verdir" hidden>
							<br>
							<a href="datos_cliente.php" id="verdira" data-toggle="modal" data-target="#modalSeleccliente">
								<span class="fa-stack fa-lg" data-toggle="tooltip" data-placement="top" title="Datos de Cliente">
									<i class="fa fa-square fa-stack-2x grey"></i>
									<i class="fa fa-address-book-o fa-stack-1x fa-inverse dark-blue"></i>
								</span>
							</a>
						</div>
						<div class="col-xs-3">
							<br>
							<button type="button" class="btn btn-danger col-xs-10" id="mesa" href="mesas.php" data-toggle="modal" data-target="#modalSelecMesa"><h5 class="text-bold">MESA</h5></button>
							<input type="hidden" name="id_me" id="id_me" value="">
							<input type="hidden" name="tipo_ord" id="tipo_ord" value="">
							<input type="hidden" name="orden_id" id="orden_id" value="0">

						</div>
						<div class="col-xs-1" id="envio" hidden>
							<label>Envio</label>
							<input type="checkbox" name="sienv" id="sienv" value="0" checked>
						</div>
						<div class="col-sm-12 col-xs-12">

							<div class="col-xs-1 table-header ">
							</div>
							<div class="col-xs-4 table-header ">
								<h3>Producto</h3>
							</div>
							<div class="col-xs-2 table-header">
								<h3 class="text-left">Prec</h3>
							</div>
							<div class="col-xs-1 table-header ">
								<h3 class="text-left">Cant</h3>
							</div>
							<div class="col-xs-4 table-header ">
								<h3 class="text-right">Accion</h3>
							</div>
						</div>

						<div id="productList">

						</div>
						<div class="footer-section">
							<div class="table-responsive col-sm-12 totalTab">
								<table class="table">
									<tr>
										<td class="active" width="40%">SubTotal</td>
										<td class="whiteBg" width="60%">
											<span id="Subtot"></span>
											<span class="float-right"><b id="ItemsNum"><span>
											</span> items</b></span>
										</td>
									</tr>
									<tr id="mostrar_total">
										<td class="active">Total</td>
										<td class="whiteBg light-blue text-bold"><span id="total"></span></td>
									</tr>
									<tr id="cargo_domicilio" class="hidden">
										<td class="active">Cargo Domicilio</td>
										<td class="whiteBg light-blue text-bold"><span id="cargo_dom"></span> <span class="float-right whiteBg"> Cambio <input type="text" name="cambdom" class="form-control cambdom float-right" id="cambdom" value="" style="width:40%;"><span></td>

									</tr>

								</table>
							</div>
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="fila2" id="fila2" value="">
									<input type="hidden" name="id_name_mod" id="id_name_mod" value="">
									<input type="hidden" name="id_price_mod" id="id_price_mod" value="">
									<input type="hidden" name="id_producto_mod" id="id_producto_mod" value="">
									<input type="hidden" name="category_mod" id="category_mod" value="">
									<input type="hidden" name="descuento_mod" id="descuento_mod" value="">
									<input type="hidden" name="datos5" id="datos5" value="">

							<div id="btnfin">
								<button type="button" class="btn btn-danger col-md-12 col-xs-12 flat-box-btn" id="btnfin1"><h5 class="text-bold">FINALIZAR ORDEN</h5></button>
							</div>
							<div id="btnini2">
								<button type="button" class="btn btn-info col-md-6 col-xs-6 flat-box-btn" id="btnadi" ><h5 class="text-bold">ADICIONAR</h5></button>
								<input type="hidden" name="orden_tipolocal" id="orden_tipolocal" value="0">
								<button type="button" class="btn btn-danger col-md-6 col-xs-6 flat-box-btn" id="btnanu" ><h5 class="text-bold">ANULAR</h5></button>
							</div>
						</div>
						</div>

						</div>
					</div>
				</div>
				<div class="col-md-7 right-side ">
					<div class="row-horizon">
						<span class="categories selectedall selectedGat" id=""><i class="fa fa-home"></i></span>
							<span class="categories" id="">categoria #1</span>
							<span class="categories" id="">categoria #2</span>
							<span class="categories" id="">categoria #3</span>
							<span class="categories" id="">categoria #4</span>
							<span class="categories" id="">categoria #5</span>
					</div>
					<!-- lista de productos  -->
					<div class="row-vertical">
						<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
							<div class="col-sm-12 col-xs-12 col-md-6 col-lg-6">
								<input type="text" class="form-control" autocomplete="off" placeholder="Buscar Producto" name="buscar_producto" id="buscar_producto" value="" style="border-radius: 18px;color:#fff;margin-top: 9px;">
							</div>
						</div>
						<div class="col-lg-12 productList3" id="productList2">
								<div class=" col-sm-3 col-xs-4 col-md-3 col-lg-3 ">
									<a  class="addPct" id="product-" >
										<div class="product  flat-box" id="addProd">
											<h3 > <img src="" alt="No Found" height="2px" width="2px"></h3>
											<h3 id="proname">prueba</h3>
											<input type="hidden" id="idname" name="name" value="" />
											<input type="hidden" id="idprice" name="price" value=""/>
											<input type="hidden" id="category" name="category" value="" />
											<input type="hidden" id="cat_des" name="cat_des" value="" />
											<input type="hidden" id="id_producto" name="id_producto" value="" />
											<div class="mask">
												<h3>5.0</h3>
												<p></p>
											</div>
										</div>
									</a>
								</div>

						</div>
					</div>
				</div>
			</div>
			<!-- /.row -->
		</div>
		<!-- /.container-fluid -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_id">
<?php if(isset($proceso)){ ?>
	<input type="hidden" value="<?php echo $proceso; ?>" id="proceso">
<?php } ?>
