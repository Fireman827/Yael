<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?= base_url()."/".GblTraerConfiguracion("logoEmpresa");?>" rel="icon">

    <title>DigitalsPos | <?=$titulo?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="<?=base_url();?>vendors/fonts/google.css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/ionicons/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/adminlte.min.css">
    <link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/custom_styles.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/summernote/summernote-bs4.min.css">
		<!-- DataTables -->
		<link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
	  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
		<!-- SweetAlert2 -->
		<link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
		<!-- Toastr -->
		<link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/toastr/toastr.min.css">
		<link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/dropzone/min/dropzone.min.css">
	  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/dropify/css/dropify.css">

	  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/select2/css/select2.min.css">
		<link rel="stylesheet" href="<?=base_url();?>/vendors/core/css/keyboard.css">
	  <link rel="stylesheet" href="<?=base_url();?>/vendors/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

	</head>
  <body class="hold-transition  dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-collapse">
    <div class="wrapper">
      <!-- Preloader -->
      <div class="preloader flex-column justify-content-center align-items-center">
      	<img class="animation__shake" src="<?=base_url();?>/vendors/core/img/dms.png" alt="dms" height="100" width="130">
      </div>
