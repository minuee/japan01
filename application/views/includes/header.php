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
        <header class="main-header">
            <!-- Logo -->
            <span  class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>HP</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>Hackers</b> Projects</span>
            </span>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"><span class="sr-only">Toggle navigation</span></a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!--<li class="dropdown tasks-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <i class="fa fa-history"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"> Last Login : <i class="fa fa-clock-o"></i> <?/*= empty($last_login) ? "First Time Login" : $last_login; */?></li>
                        </ul>
                        </li>-->
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?=$global_face?>" class="user-image" alt="<?=$name?>"/>
                                <input type="hidden" id="global_userId" value="<?=$global_userId?>">
                                <span class="hidden-xs"><?php echo $name; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="<?=$global_face?>" class="img-circle" alt="<?=$name?>" />
                                    <p>
                                        <?php echo $name; ?>
                                        <small><?php echo $role_text; ?></small>
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?php echo base_url(); ?>profile" class="btn btn-warning btn-flat"><i class="fa fa-user-circle"></i> Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo base_url(); ?>logout" class="btn btn-default btn-flat"><i class="fa fa-sign-out"></i> Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">ToDo관리</li>
                    <?php
                        if($role == ROLE_ADMIN || $global_hackersid == "hanacody"  )
                        {
                    ?>
                            <li>
                                <a href="<?php echo base_url(); ?>statics">
                                    <i class="fa  fa-area-chart"></i>
                                    <span>관리 DashBoard</span>
                                </a>
                            </li>
                    <?
                        }
                    ?>
                    <?php
                        if($role !== ROLE_ADMIN || ( $role == ROLE_ADMIN  && $global_userId == 1 ) )
                        {
                    ?>
                            <li>
                                <a href="<?php echo base_url(); ?>myjobs">
                                    <i class="fa fa-dashboard"></i> <span>MY JOBS</span>
                                    <span class="pull-right-container">
                                        <small class="label pull-right bg-done" id="left_nav_done"><?=isset($MyJobsCount[0]['done_cnt'])?$MyJobsCount[0]['done_cnt']:0?></small>
                                        <small class="label pull-right bg-doing"  id="left_nav_doing"><?=isset($MyJobsCount[0]['doing_cnt'])?$MyJobsCount[0]['doing_cnt']:0?></small>
                                        <small class="label pull-right bg-todo"  id="left_nav_todo"><?=isset($MyJobsCount[0]['todo_cnt'])?$MyJobsCount[0]['todo_cnt']:0?></small>
                                    </span>
                                </a>
                            </li>
                            <?
                        } ?>
                    <li>
                        <a href="<?php echo base_url(); ?>manager/kanban">
                            <i class="fa fa-sitemap"></i>
                            <span>Team KANBAN</span>
                        </a>
                    </li>
                    <li class="<?=( $MyTeamViewPermission > 0 ?'':'display_none')?>">
                        <a href="<?php echo base_url(); ?>manager/monitor">
                            <i class="fa fa-sitemap"></i>
                            <span>타팀 KANBAN</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo base_url(); ?>manager/project">
                            <i class="fa fa-bars"></i>
                            <span>프로젝트관리</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo base_url(); ?>manager/schedule">
                            <i class="fa fa-calendar"></i> <span>일정관리</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo base_url(); ?>manager/report">
                            <i class="fa fa-files-o"></i>
                            <span>업무리포트</span>
                        </a>
                    </li>
                    <?php
                        if(  $role == ROLE_ADMIN  || $global_hackersid == "hanacody" )
                        {
                        ?>
                        <li class="treeview">
                            <a href="<?php echo base_url(); ?>manager/group">
                                <i class="fa fa-users"></i>
                                조직도
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?=($role == ROLE_ADMIN  && $global_userId == 1)?"":"display_none"?>">
                                    <a href="<?php echo base_url(); ?>manager/group/index">
                                        <i class="fa fa-users"></i><span>기획본부조직</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url(); ?>manager/group/develope">
                                        <i class="fa fa-users"></i><span>개발팀</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url(); ?>manager/group/design">
                                        <i class="fa fa-users"></i><span>디자인팀</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url(); ?>manager/group/depth3?mode=<?=BASE_REALTOR_PARENT_CODE?>">
                                        <i class="fa fa-users"></i><span>공인중개사기획</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo base_url(); ?>manager/group/depth3?mode=<?=BASE_BASEENGLISH_PARENT_CODE?>">
                                        <i class="fa fa-users"></i><span>기초영어사업</span>
                                    </a>
                                </li>
                                <li>
                                <a href="<?php echo base_url(); ?>manager/group/depth3?mode=<?=BASE_PLANNING_PARENT_CODE?>">
                                <i class="fa fa-users"></i><span>서비스인프라혁신</span>
                                </a>
                                </li>
                            </ul>
                        </li>
                            <?php
                        }
                        ?>
                    <li class="<?=($global_parentCode == BASE_DEVELOPE_PARENT_CODE  || $global_parentCode == BASE_SEC_PARENT_CODE ) ?'':'display_none'?>">
                        <a href="<?php echo base_url(); ?>manager/group/develope">
                            <i class="fa fa-users"></i>
                            <span>개발팀조직도</span>
                        </a>
                    </li>
                    <li class="<?=($global_parentCode == BASE_DESIGN_PARENT_CODE ) ?'':'display_none'?>">
                        <a href="<?php echo base_url(); ?>manager/group/design">
                            <i class="fa fa-users"></i>
                            <span>디자인팀조직도</span>
                        </a>
                    </li>
                    <li class="<?=($global_parentCode == BASE_REALTOR_PARENT_CODE ) ?'':'display_none'?>">
                        <a href="<?php echo base_url(); ?>manager/group/depth3?mode=<?=BASE_REALTOR_PARENT_CODE?>">
                            <i class="fa fa-users"></i><span>공인중개사기획</span>
                        </a>
                    </li>
                    <li class="<?=($global_parentCode == BASE_BASEENGLISH_PARENT_CODE ) ?'':'display_none'?>">
                        <a href="<?php echo base_url(); ?>manager/group/depth3?mode=<?=BASE_BASEENGLISH_PARENT_CODE?>">
                            <i class="fa fa-users"></i><span>기초영어사업</span>
                        </a>
                    </li>
                    <li class="<?=($global_parentCode == BASE_PLANNING_PARENT_CODE  ) ?'':'display_none'?>">
                        <a href="<?php echo base_url(); ?>manager/group/depth3?mode=<?=BASE_PLANNING_PARENT_CODE?>">
                            <i class="fa fa-users"></i><span>서비스인프라혁신</span>
                        </a>
                    </li>
                    <?php
                    if($global_hackersid == "hanacody" || ( $role == ROLE_ADMIN && $global_userId == 1 ))
                    {
                        ?>
                        <li>
                            <a href="<?php echo base_url(); ?>userListing">
                                <i class="fa fa-users"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <!--<li>
                        <a href="<?php /*echo base_url(); */?>manager/schedule">
                        <i class="fa fa-calendar"></i> <span>Calendar</span>

                        </a>
                        </li>-->
                        <!--<li >
                        <a href="<?php /*echo base_url(); */?>manager/report" >
                        <i class="fa fa-files-o"></i>
                        <span>Reports</span>
                        </a>
                        </li>-->
                        <?php
                    }
                    ?>
                    <li>
                        <a href="<?php echo base_url(); ?>manual">
                            <i class="fa fa-info-circle"></i>
                            <span>Manual</span>
                        </a>
                    </li>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>
