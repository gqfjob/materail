<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php if(isset($title) && !empty($title)) echo $title.'-';?>统一素材库</title>
<meta name="description" content="<?php if(isset($description) && !empty($description)) echo $description.'-';?>">
<meta name="keywords" content="素材,<?php if(isset($keywords) && !empty($keywords)) echo $keywords;?>">
<link href="<?php echo base_url('assets/css/bootstrap/3.0.0/bootstrap.css');?>" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('assets/css/font-awesome.css');?>" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('assets/js/uploadfy/uploadify.css');?>" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url('assets/css/style.css');?>" media="screen" rel="stylesheet" type="text/css">

<link href="<?php echo base_url('assets/css/common.css');?>" media="screen" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery/1.11.0/jquery.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery-migrate-1.1.1.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.validate.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.form.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.lightmodel.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap/3.0.0/bootstrap.js');?>"></script>
<script type="text/javascript">
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
</script>

<script type="text/javascript" src="<?php echo base_url('assets/js/uploadfy/jquery.uploadify.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tools/jquery.placeholder.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/common.js');?>"></script>

<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/plugins/ueditor/editor_config.js');?>"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/plugins/ueditor/editor_all.js');?>"></script>

<!--[if lt IE 9]>
<script src="<?php echo base_url('assets/js/tools/html5.js');?>"></script>
<![endif]-->
</head>
<body>

<div id="headwrap">
	<header>
		<div class="navbar navbar-static-top navbar-shutterstock clearfix">
		    <div class="navbar-inner-left pull-left container-fluid">
		        <ul class="nav pull-left">
		            <li class="<?php if($cur == 0){echo "active ";}?>fl b16">
		                <a href="/">首页</a>
		            </li >
		            <?php foreach($cate as $c):?>
		            <li class="<?php if($cur == $c['id']){echo "active ";}?>fl b16">
		                <a href="/material/lists/<?php echo $c['id'];?>"><?php echo $c['cname']?></a>
		            </li>
		            <?php endforeach;?>
		        </ul>
		    </div>
		
		    <div id="userInfo" class="pull-right container-fluid nav-horiz navbar-inner-right">
				<div style="padding-top:15px;">
					<img src="/assets/img/long-loading.gif"/>
				</div>
			</div>
		</div>
	</header>
</div>
<div class="content">
    <div id="mainpage">