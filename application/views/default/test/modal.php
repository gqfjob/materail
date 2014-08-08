<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php if(isset($title) && !empty($title)) echo $title.'-';?>代码测试</title>
<meta name="description" content="<?php if(isset($description) && !empty($description)) echo $description.'-';?>">
<meta name="keywords" content="爱创新,<?php if(isset($keywords) && !empty($keywords)) echo $keywords;?>">
<?php  $this->load->module("common/header/getLinks");?>
</head>
<body>
<header id="header">
        <div class="top"></div>

</header>

<div id="main" class="grid">
<div id="container">


	<div class="text-center">
	    <button class="demo btn btn-primary btn-large" id="model-demo">View Demo</button>
	</div>


</div>
</div>
<div id="modals">


</div>
<footer id="footer">
	<i class="hd"></i>
	<div class="ft"></div>
</footer>
<script>
$(document).ready(function(){
	$.fn.modalmanager.defaults.resize = true;
});
$('#model-demo').click(function(){
	  var tmpl = [
	    // tabindex is required for focus
	    '<div class="modal fade" >',
	      '<div class="modal-header">',
	        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>',
	        '<h3>Modal header</h3>', 
	      '</div>',
	      '<div class="modal-body">',
	        '<p>Test</p>',
	      '</div>',
	      '<div class="modal-footer">',
	        '<a href="#" data-dismiss="modal" class="btn">Close</a>',
	        '<a href="#" class="btn btn-primary">Save changes</a>',
	      '</div>',
	    '</div>'
	  ].join('');
	  
	  $(tmpl).modal();
});
</script>

</body>
</html>