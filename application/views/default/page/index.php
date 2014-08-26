<style>
#infoLists {
	width: 800px;
	margin: 400px auto 20px;
}

#infoTab {
	float: left;
	width: 17%;
	text-align: right;
	margin-right: 10px;
}

#infoContain {
	margin-left: 20px;
	float: left;
	height: 400px;
	width: 79%;
	background: #fff;
	border-radius: 0px 5px 5px 0px;
	border: 1px solid #ccc;
}

#infoTab>li {
	width: 160px;
}

#infoTab li a {
	display: inline-block;
	width: 160px;
	height: 45px;
	text-align: center;
	line-height: 45px;
	font-weight: bold;
	border: 1px solid #ccc;
	border-top: 0px;
}

#infoTab .selected {
	width: 160px;
	background: #fff;
}

#infoTab .first {
	border-top: 1px solid #ccc;
	border-radius: 5px 0px 0px 0px;
}

#infoTab .last {
	border-bottom: 1px solid #ccc;
	border-radius: 0px 0px 0px 5px;
}

/*search-bar*/
.form-search{
	width: 540px; 
}
/* input-group exists in bootstrap 3+ */
.form-search .input-group {
	position: relative;
	display: table;
	border-collapse: separate;
}
/* form-control exists in bootstrap 3+ */
.form-search .input-group .form-control {
	display: table-cell;
}
/* input-group-addon only exists in bootstrap 3+ */
.form-search .input-group-addon {
	padding: 6px 12px;
	font-size: 14px;
	font-weight: 400;
	line-height: 1;
	color: #555;
	text-align: center;
	background-color: #EEE;
	border: 1px solid #CCC;
	border-radius: 4px;
}
/* rounded corners are applied outside to the input-group */
.form-search .input-group-addon:not(:first-child):not(:last-child), 
.form-search .input-group-btn:not(:first-child):not(:last-child), 
.form-search .input-group input:not(:first-child):not(:last-child) {
	border-radius: 0;
}
/* remove rounded corners and left border from button and dropdown */
.form-search .input-group input:last-child, 
.form-search .input-group-addon:last-child, 
.form-search .input-group-btn:last-child > .btn, .form-search .input-group-btn:last-child > .btn-group > .btn, 
.form-search .input-group-btn:last-child > .dropdown-toggle, 
.form-search .input-group-btn:first-child > .btn:not(:first-child), 
.form-search .input-group-btn:first-child > .btn-group:not(:first-child) > .btn {
	border-bottom-left-radius: 0;
	border-top-left-radius: 0;
}
/* input-group-btn is new in bootstrap 3+, you cannot have display: table-cell on a button */
.form-search .input-group-addon, 
.form-search .input-group-btn {
	width: 1%;
	white-space: nowrap;
	vertical-align: middle;
}
.form-search .input-group-addon, 
.form-search .input-group-btn, 
.form-search .input-group input {
	display: table-cell;
}

/* 3.1 style dropdown */
.form-search .dropdown-menu {
	position: absolute;
	top: 100%;
	z-index: 1000;
	display: none;
	float: left;
	padding: 5px 0;
	margin: 2px 0 0;
	list-style: none;
	font-size: 14px;
	background-color: #fff;
	border: 1px solid #ccc;
	border: 1px solid rgba(0,0,0,.15);
	border-radius: 4px;
	box-shadow: 0 6px 12px rgba(0,0,0,.175);
	background-clip: padding-box;
}
.open > .dropdown-menu {
	display: block;
}

.form-search  .sAction{
	height:24px;
	line-height:24px;
}

.form-search .line{
	height:10px;
}

/* Shutterstock spcific styles */
.form-search.form-emphasis .input-group {
	border: 4px solid #e6e6e6;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
}
/* needed for FF because form-control is table-cell */
.form-search .input-group .placeholder_parent{
	position: relative;
}
.lte11 .form-search .input-group .form-control input, .lte10 .form-search .input-group .form-control input {
	padding-bottom: 9px;
}
.lte9 .form-search .input-group .form-control input{
	padding-right: 5px;
}
.lte8 .form-search .input-group .form-control input{
	padding-top: 1px;
	padding-bottom: 3px;
}
/* has to be quite specific to beat out another selector */
.form-search.form-emphasis .input-group input[type=text], 
.form-search.form-emphasis .input-group input[type=text]:focus {
	-webkit-border-radius: 0;
	-moz-border-radius: 0;
	border-radius: 0;
	border: 1px solid #b4b4b4;
	border-right: 0;
	-webkit-box-shadow: none;
	-moz-box-shadow: none;
	box-shadow: none;
	-webkit-transition: none;
	-moz-transition: none;
	-o-transition: none;
	transition: none;
	width: 100%;
}
.lte9 .form-search.form-emphasis .input-group input[type=text]{
	width: 101%;
	display: block;
}
.form-search .input-group-addon {
	color: #64676b;
	border-radius: 0;
	background-color: #fff;
	border: 1px solid #b4b4b4;
	cursor: pointer;
}
/* this fixes borders on input-group-addon ie8 and doesnt break everyone else */
.form-search .dropdown{
	position: static;
}
/* added by me to match design */
.form-search .dropdown-wrapper{
	position: relative;
}
/* remove dup borders */
.form-search .dropdown.input-group-addon {
	border-left: 0;	
	padding: 0;
}
/* the left border is not 100% of the height, so we cant put it on the parent.  In this case it will be as tall as the text */
.form-search .input-group-addon .dropdown-trigger{
	padding: 16px 16px 16px 0;
	font-size: 13px;
}
.form-search .input-group-addon .dropdown-trigger:before {
	border-left: 1px solid #CCC;
	padding: 3px 13px 5px 0;
	content: "";
}
.form-search .input-group-addon .dropdown-trigger:after {
	font-size: 8px;
	color: #797979;
	content: "\25bc"; /* Unicode for the down arrow */
	position: relative;
	bottom: 1px;
	left: 4px;
}
/* fill in the background so you dont see white around rounded corners */
.form-search .input-group-btn {
	background: #e6e6e6;
}
.form-search .input-group-btn .btn {
	padding-top: 6px;
	padding-bottom: 6px;
}
.lte9 .form-search .input-group-btn .btn {
	background-image: none; /* this dones not belong here, should be in base.css' */
}
.form-search .dropdown-menu {
	/* get the drop to be flush against the right side and expand left */
	left: 0;
	right: auto;
}
.form-search .dropdown-menu li{
	color: #64676b;
	text-align: left;
	line-height: 15px;
	padding: 0 15px;
	min-width: 80px;
	font-size: 13px;
	cursor: pointer;
	border-bottom: 0;
}
.form-search .dropdown-menu li:hover{
	background: #a5b2b9;
	color: #fff;
}
.form-search .dropdown-menu li.indent {
	padding-left: 22px;
}
.form-search .dropdown-menu li.line {
	border-top: 1px solid #E5E5E5;
	margin-top: 4px;
	margin-bottom: 4px;
}
.form-search .dropdown-menu li label, .form-search .dropdown-menu li input{
	cursor: pointer;
}
.form-search .dropdown-menu input{
	display: none;
}

/* LOHP Spcific CSS, these differ from the swig module  */
.form-search{
	width: 640px;
	top: 50%;
	left: 50%;
	position: absolute;
	margin: -55px 0 0 -320px;
}
.form-search.form-emphasis .input-group {
	border: 4px solid #595959;
	border: 6px solid rgba(218, 218, 209, 0.6);           
	background: #595959;
	background: rgba(189, 189, 89, 0.6);           
	background-clip: content-box;
}
#search_label{
	position: absolute;
	top: 14px;
	left: 18px;
	font: 18px/26px Arial;
	color: #B3B5B9;
}
.language_pl #search_label, .language_fr #search_label{
	font-size: 16px;
}
.form-search.form-emphasis .input-group input[type=text], 
.form-search.form-emphasis .input-group input[type=text]:focus{
	height: 58px;
	line-height: 26px;
	padding: 12px 0 12px 17px;
	font-size: 18px;
	box-shadow: inset 2px 2px 2px rgba(0, 0, 0, 0.2);
	border-top-left-radius: 2px;
	border-bottom-left-radius: 2px;
	background: #fff;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
}
.form-search .dropdown.input-group-addon{
	box-shadow: inset 0 4px 2px -2px rgba(0, 0, 0, 0.2);
}
.form-search .input-group-addon .dropdown-trigger{
	font-size: 15px;
}
.form-search .input-group-btn{
	background: #595959;
	background: rgba(89, 89, 89, 0.6);
}
.form-search .input-group-btn .btn {
	padding-top: 12px;
	padding-bottom: 13px;
}
#search-shutterstock .form-search .input-group-btn .btn img{
	left: auto;
	position: static;
	top: auto;
	height: 25px;
	width: 25px;
	max-width: inherit;
}
.form-search .dropdown-menu{
	top: 35px;
	padding-bottom: 8px;
	padding-top: 8px;
}
/* webstack has this same problem */
.form-search .dropdown-menu li label {
	margin-bottom: 0;
	font-size: 13px;
}
.visuallyhidden {
	border: 0;
	clip: rect(0 0 0 0);
	height: 1px;
	margin: -1px;
	overflow: hidden;
	padding: 0;
	width: 1px;
	position: absolute;
}
.form-search .input-group-btn .btn img {
	left: auto;
	position: static;
	top: auto;
	height: 25px;
	width: 25px;
	max-width: inherit;
}
.dropdown-menu{
	min-width:100px;
}
.form-control{
	border:0px;
	padding:0px;
}
/*覆盖btn样式*/
.btn {
display: inline-block;
padding: 4px 12px;
margin-bottom: 0;
font-size: 14px;
line-height: 20px;
color: #333;
text-align: center;
text-shadow: 0 1px 1px rgba(255,255,255,0.75);
vertical-align: middle;
cursor: pointer;
background-color: #f5f5f5;
background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
background-image: -o-linear-gradient(top,#fff,#e6e6e6);
background-image: linear-gradient(to bottom,#fff,#e6e6e6);
background-repeat: repeat-x;
border: 1px solid #ccc;
border-color: #e6e6e6 #e6e6e6 #bfbfbf;
border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
border-bottom-color: #b3b3b3;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff',endColorstr='#ffe6e6e6',GradientType=0);
filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
-webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
-moz-box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
box-shadow: inset 0 1px 0 rgba(255,255,255,0.2),0 1px 2px rgba(0,0,0,0.05);
}
.btn-primary {

text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
background-color: #006dcc;
background-image: -moz-linear-gradient(top,#08c,#04c);
background-image: -webkit-gradient(linear,0 0,0 100%,from(#08c),to(#04c));
background-image: -webkit-linear-gradient(top,#08c,#04c);
background-image: -o-linear-gradient(top,#08c,#04c);
background-image: linear-gradient(to bottom,#08c,#04c);
background-repeat: repeat-x;
border-color: #04c #04c #002a80;
border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc',endColorstr='#ff0044cc',GradientType=0);
filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
}

</style>

<script type="text/javascript" src="<?php echo base_url('assets/js/tools/idTabs.js');?>"></script>

<div id="container">
	<div>
		<!-- 搜索 -->
		<form class="form-search form-emphasis" id="site-search" method="post" action="/search/all"  onsubmit="return checkData();">
			<div class="input-group">
				<div class="form-control">
					<div class="placeholder_parent">
						<span id="search_label" class="placeholder">请输入关键词</span>
					</div>
					<input id="index_keyword_input" name="searchterm" type="text" autocomplete="off" class="">
				</div>
				<div class="dropdown input-group-addon">
					<div class="dropdown-wrapper">
						<span class="dropdown-trigger" role="button" data-toggle="dropdown" href="#" id="mateNow">全部素材</span>
						<ul class="dropdown-menu" role="menu" aria-labelledby="Content Type Dropdown">
							<li role="presentation" data-action="/material/search" data-media-name="allMate" clas>
								<label><input type="radio" name="media_type" value="allMate" class="visuallyhidden">全部素材</label>
							</li>
							<li class="line"></li>
							<?php foreach ($cate as $c):?>
							<li class="indent sAction" role="presentation" data-action="/material/search/<?php echo $c['id'];?>" data-media-name="<?php echo $c['id'];?>">
								<label><input type="radio" name="media_type" value="<?php echo $c['id'];?>" class="visuallyhidden"><?php echo $c['cname'];?></label>
							</li>
							<?php endforeach;?>							
							<!-- 
							<li role="presentation" data-action="/search/app" data-media-name="app">
								<label> <input type="radio" name="app" class="visuallyhidden">应用</label>
							</li>
							<li role="presentation" data-action="/search/other" data-media-name="other">
								<label> <input type="radio" name="other" class="visuallyhidden">其他</label>
							</li>
							 -->
						</ul>
					</div>
				</div>
				<span class="input-group-btn">
					<button class="btn btn-primary">
						<span class="visuallyhidden">search</span> <img src="/assets/img/site/search-icon.png" alt="icon-search">
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>"/>
					</button>
				</span>
			</div>
		</form>
		<div class="cl"></div>
	</div>
	<div class="cl"></div>
	<?php //var_dump($cate);?>
	<!-- 切换 -->
	<div id="infoLists">
		<div id="info" class="usual">
		<?php if( ! empty($cate)) : ?>
			<ul id="infoTab">
				<?php $i = 0;?>
				<?php foreach($cate as $c) : $i++;?>
				<?php if($i == 1) : ?>
				<li><a href="#tabs<?php echo $c['id'];?>" class="selected first"><?php echo $c['cname'];?></a></li>
				<?php elseif($i == count($cate)) : ?>
				<li><a href="#tabs<?php echo $c['id'];?>" class="last"><?php echo $c['cname'];?></a></li>
				<?php else: ?>
				<li><a href="#tabs<?php echo $c['id'];?>"><?php echo $c['cname'];?></a></li>
				<?php endif;?>
				<?php endforeach;?>
			</ul>
			<div id="infoContain" style="padding:15px 7px;">
				<?php foreach($cate_hot_material as $cid => $materials) : ?>
				<div id="tabs<?php echo $cid;?>" class="padd">
					<div class="row">
					<?php foreach($materials as $material) : ?>
						<div class="col-xs-3" style="padding-bottom:25px">
							<a href="<?php echo base_url('material/detail/' . $material['id']);?>" target="_blank"><img src="<?php echo $material['logo']; ?>" alt="" class="img-thumbnail" /> </a>
							<div class="text-center text-overflow"><a href="<?php echo base_url('material/detail/' . $material['id']);?>" target="_blank" title="<?php echo $material['mname'];?>"><?php echo $material['mname'];?></a></div>
						</div>
					<?php endforeach;?>
					</div>
				</div>
				<?php endforeach; ?>
				
			</div>
		<?php endif;?>
		</div>
		<div class="cl"></div>
	</div>
</div>

<script type="text/javascript"> 
	$("#info ul").idTabs("tabs1");
	
	$(".sAction").click(function(){
		var node = $(this);
		$("#mateNow").html(node.html());
		$("#site-search").attr("action",node.attr("data-action"));
	}); 
	//placeholer
	$("#site-search").on('keyup',"#index_keyword_input",function(){
		var node = $(this);
		if(''!=node.val()){
			$("#search_label").html("");
		}else{
			$("#search_label").html("请输入关键词");
		}
	});
	function checkData(){
		var res = $.trim($('#index_keyword_input').val());
		if(res == ''){
			notice("请输入关键词",300,150);
			return false;
		}
	}
</script>
