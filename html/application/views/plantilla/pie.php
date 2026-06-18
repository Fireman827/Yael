<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
	<strong>Copyright &copy; <?=date("Y")?> <a target="_blank" href="https://digitalsmindssystems.com">DigitalsMindsSystems</a>.</strong>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
	<!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?=base_url();?>/vendors/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=base_url();?>/vendors/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<!-- <script>
$.widget.bridge('uibutton', $.ui.button)
</script> -->
<!--notificaciones-->
<!--script src="<//?=base_url('web/html/scripts/notificaciones.js');?>"></script>-->
<!-- Bootstrap 4 -->
<script src="<?=base_url();?>/vendors/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?=base_url();?>/vendors/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?=base_url();?>/vendors/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<!-- <script src="<?=base_url();?>/vendors/plugins/jqvmap/jquery.vmap.min.js"></script> -->
<!-- <script src="<?=base_url();?>/vendors/plugins/jqvmap/maps/jquery.vmap.usa.js"></script> -->
<!-- jQuery Knob Chart -->
<script src="<?=base_url();?>/vendors/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?=base_url();?>/vendors/plugins/moment/moment.min.js"></script>
<!-- <script src="<?=base_url();?>/vendors/plugins/daterangepicker/daterangepicker.js"></script> -->
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?=base_url();?>/vendors/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?=base_url();?>/vendors/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=base_url();?>/vendors/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url();?>/vendors/core/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="<?=base_url();?>/vendors/core/js/demo.js"></script> -->
<!-- DataTables  & Plugins -->
<script src="<?=base_url();?>/vendors/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/jszip/jszip.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="<?=base_url();?>/vendors/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/jquery-validation/additional-methods.min.js"></script>

<script src="<?=base_url();?>/vendors/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/toastr/toastr.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/numeric/jquery.numeric.js"></script>
<script src="<?=base_url();?>/vendors/plugins/mask/jquery.mask.min.js"></script>
<script src="<?=base_url();?>/vendors/plugins/select2/js/select2.full.min.js"></script>
<script src='<?=base_url();?>/vendors/plugins/floatThead/floatThead/src/jquery.floatThead.js'></script>
<script src='<?=base_url();?>/vendors/plugins/TypeAhead/typeahead.jquery.min.js'></script>
<script src='<?=base_url();?>/vendors/core/js/jquery.keyboard.js'></script>
<script src="<?=base_url();?>/vendors/core/js/main.js"></script>

<!-- BASE_URL (ANTES de notificaciones) -->
<script>const base_url = "<?= base_url(); ?>";</script>

<!-- notificaciones -->
<script src="<?= base_url('scripts/notificaciones.js') ?>"></script>

<script src="<?=base_url()?>/vendors/plugins/dropzone/min/dropzone.min.js"></script>
<script src="<?=base_url()?>/vendors/plugins/dropify/js/dropify.min.js"></script>
<script src="<?=base_url()?>/vendors/plugins/StickyTable/jquery.stickytable.js"></script>

<?php if ($numeroDias<=-5): ?>
	<script type="text/javascript">
	var url = window.location.origin;
	Swal.fire({
		title: 'Información',
		text: 'Fecha de pago  vencida, realice el pago para seguir usando el sistema',
		type: 'error',
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Continuar",
		allowOutsideClick: false,
	}).then((result) =>{
		if (result.value){
			location.href = url + "/PagoAgregar";
		}
	});
	</script>
<?php endif; ?>


<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="<?=base_url();?>/vendors/core/js/pages/dashboard.js"></script> -->
