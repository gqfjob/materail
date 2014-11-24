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
		            <li class="<?php if($cur == -1){echo "active ";}?>fl b16">
		                <a href="<?php echo base_url('material/lists');?>">全部</a>
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
<?php if($cur !=0):?>
<div id="search_interface">
	<form name="keyword_form" autocomplete="off" method="post" action="/material/search/<?php echo $tab;?>" style="width:532px;margin:0px auto;">
		<input type="hidden" name="search_source" value="search_form">  
		<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>"/>              
		<!-- main search container -->
		<div class="main_search_container">
			<div class="integrated_search_field">
				<span class="keyword_input">
					<input type="text" name="searchterm" placeholder="输入关键字，查找素材" value="" autocomplete="off">
				</span>
				<!-- 
				<span class="media_types">
					<span class="media_select">
						<span class="media_selected">All Images</span>
						<ul class="media_options hidden_radio_form shadow2 dropdown-menu" style="display: none;">
								<li data-media-type="images">
									All Images
								</li>
								
								<li data-media-type="photos" class="indent">
									Photos
								</li>
								
								<li data-media-type="vectors" class="indent">
									Vectors
								</li>
								
								<li data-media-type="illustrations" class="indent">
									Illustrations
								</li>
								<li class="line"></li>
								<li data-media-type="footage">
									Footage
								</li>
								<li class="line"></li>
								<li data-media-type="music">
									Music
								</li>
						</ul>
					</span>
				</span>
				 -->
				<span class="main_search_button">
					<button class="gray btn-secondary no-max-width" type="submit" value="Search">
						<img alt="Search" src="/assets/img/icn-search-mag-glass-16xp.png" width="16" height="16">
					</button>
				</span>
			</div>
			
		</div>
	<div class="cl"></div>
	</form>
</div>
<?php endif;?>
<div class="content">
    <div id="mainpage">