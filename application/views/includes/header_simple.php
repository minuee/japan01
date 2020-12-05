<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $pageTitle; ?></title>

    <meta name="csrf-token" content="<?=$csrf_token?>">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="<?php echo base_url(); ?>assets/favicon.ico" type="image/x-icon" />
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/common.css?v=<?=time()?>" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!--<link href="<?php /*echo base_url(); */?>assets/dist/css/common.css" rel="stylesheet" type="text/css" />-->
    <style>
        .error{color:red;font-weight: normal;}
    </style>
    <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript">
        var baseURL = "<?php echo base_url(); ?>";
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>assets/js/html5shiv.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/util.js" charset="utf-8"></script>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/dist/css/datatables.min.css"/>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/dist/js/datatables.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/dist/js/dataTables.bootstrap.min.js"></script>

    <!--<script src="<?php /*echo base_url(); */?>assets/plugins/morris/fastclick.js"></script>
    <script src="<?php /*echo base_url(); */?>assets/plugins/morris/adminlte.min.js"></script>-->
    <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
</head>
<body class="hold-transition skin-purple sidebar-mini">
    <div class="wrapper">
 