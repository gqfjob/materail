<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php if(isset($title) && !empty($title)) echo $title.'-';?>统一素材库管理后台</title>
<meta name="description" content="<?php if(isset($description) && !empty($description)) echo $description.'-';?>">
<meta name="keywords" content="素材库,<?php if(isset($keywords) && !empty($keywords)) echo $keywords;?>">
<link href="<?php echo base_url('assets/css/bootstrap/3.0.0/bootstrap.css');?>" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('assets/css/font-awesome.css');?>" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('assets/js/uploadfy/uploadify.css');?>" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('assets/css/style.css');?>" media="screen" rel="stylesheet" type="text/css">

<link href="<?php echo base_url('assets/css/admin.css');?>" media="screen" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery/1.11.0/jquery.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery-migrate-1.1.1.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.validate.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.form.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.lightmodel.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap/3.0.0/bootstrap.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/uploadfy/jquery.uploadify.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.placeholder.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/bgcommon.js');?>"></script>  <!-- HTML5 Support for IE -->
<!--[if lt IE 9]>
<script src="<?php echo base_url('assets/js/tools/html5.js');?>"></script>
<![endif]-->

</head>
<body>
<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">
  
    <div class="conjtainer">
      <!-- Menu button for smallar screens -->
      <div class="navbar-header">
		  <button class="navbar-toggle btn-navbar" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
			<span>菜单</span>
		  </button>
		  <!-- Site name for smallar screens -->
		  <a href="index.html" class="navbar-brand hidden-lg">首页</a>
		</div>
      
      

      <!-- Navigation starts -->
      <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">         

        <!-- Links -->
        <ul class="nav navbar-nav pull-right">
          <li class="dropdown pull-right">            
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
              <i class="icon-user"></i> 管理员 <b class="caret"></b>              
            </a>
            
            <!-- Dropdown menu -->
            <ul class="dropdown-menu">
              <li><a href="#"><i class="icon-user"></i> 前台</a></li>
              <li><a href="login.html"><i class="icon-off"></i> 退出</a></li>
            </ul>
          </li>
          
        </ul>
      </nav>

    </div>
  </div>


<!-- Header starts -->
  <header>
    <div class="container">
      <div class="row">

        <!-- Logo section -->
        <div class="col-md-4">
          <!-- Logo. -->
          <div class="logo">
            <h1><a href="#">统一素材库<span class="bold"></span></a></h1>
            <p class="meta">后台管理系统</p>
          </div>
          <!-- Logo ends -->
        </div>

        <!-- Data section -->

        <div class="col-md-3 fr">
          <div class="header-data">

            <!-- Traffic data -->
            <div class="hdata">
              <div class="mcol-left">
                <!-- Icon with red background -->
                <i class="icon-signal bred"></i> 
              </div>
              <div class="mcol-right">
                <!-- Number of visitors -->
                <p><a href="#">7000</a> <em>访问</em></p>
              </div>
              <div class="clearfix"></div>
            </div>

            <!-- Members data -->
            <div class="hdata">
              <div class="mcol-left">
                <!-- Icon with blue background -->
                <i class="icon-user bblue"></i> 
              </div>
              <div class="mcol-right">
                <!-- Number of visitors -->
                <p><a href="#">3000</a> <em>用户</em></p>
              </div>
              <div class="clearfix"></div>
            </div>

            <!-- revenue data -->
            <div class="hdata">
              <div class="mcol-left">
                <!-- Icon with green background -->
                <i class="icon-money bgreen"></i> 
              </div>
              <div class="mcol-right">
                <!-- Number of visitors -->
                <p><a href="#">5000</a><em>下载</em></p>
              </div>
              <div class="clearfix"></div>
            </div>                        

          </div>
        </div>

      </div>
    </div>
  </header>

<!-- Header ends -->