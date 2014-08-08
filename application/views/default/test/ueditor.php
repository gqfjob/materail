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
<?php  $this->load->module("common/header/getUeditor");?>
</head>
<body>
<header id="header">
        <div class="top"></div>

</header>

<div id="main" class="grid">
<div id="container">

	<div id="myEditor" style="width:600px;height:200px"></div>
	<script type="text/javascript">
	        var editor = UM.getEditor('myEditor');
	</script>

	<div class="text-center">
	    <button class="demo btn btn-primary btn-large" data-toggle="modal" href="#notlong">View Demo</button>
	</div>


</div>
</div>
<div id="modals">

<div id="notlong" class="modal hide fade" tabindex="-1" data-replace="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Not That Long</h3>
  </div>
  <div class="modal-body">
    <button class="btn" data-toggle="modal" href="#verylong" style="position: absolute; top: 50%; right: 12px">Very Long Modal</button>
    <div style="height: 400px; overflow: hidden;">
      <img style="height: 800px" src="http://i.imgur.com/KwPYo.jpg" />
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">Close</button>
  </div>
</div>
</div>
<footer id="footer">
	<i class="hd"></i>
	<div class="ft"></div>
</footer>
<script>
$(document).ready(function(){
    setTimeout(function() {
        $.bootstrapGrowl("测试提示框,2s自动隐藏", {
            type: 'danger',
            align: 'center',
            width: 'auto',
            allow_dismiss: true
        });
    }, 2000);
    
});
</script>

</body>
</html>