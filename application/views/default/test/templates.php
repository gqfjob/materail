<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php if(isset($title) && !empty($title)) echo $title.'-';?>代码测试</title>
<meta name="description" content="<?php if(isset($description) && !empty($description)) echo $description.'-';?>">
<meta name="keywords" content="爱创新,<?php if(isset($keywords) && !empty($keywords)) echo $keywords;?>">
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

<link href="<?php echo createStaticPath('css');?>/bootstrap.css" rel="stylesheet">
<link href="<?php echo createStaticPath('css');?>/bootstrap-theme.css" rel="stylesheet">
<link href="<?php echo createStaticPath('css');?>/common.css" rel="stylesheet">

<script src="<?php echo createStaticPath('js');?>/seajs/seajs/2.2.0/sea.js"></script>
<script src="<?php echo createStaticPath('js');?>/jquery/1.10.1/jquery.js"></script>
<script src="<?php echo createStaticPath('js');?>/bootstrap/3.0.3/bootstrap.js"></script>
</head>
<body>
<header id="header">
        <div class="top">
            <section class="logo">
                <hgroup>
                    <h1>
                        <a href="http://www.36ria.com/" title="ria之家–RIA三部曲：jquery、ext、flex" rel="home">ria之家–RIA三部曲：jquery、ext、flex</a>
                    </h1>
                    <h2>
                        RIA三部曲：jquery、ext、flex                    </h2>
                </hgroup>

                <a class="logo-link" href="/"><img src="http://www.36ria.com/wp-content/themes/36ria3.0/images/LOGO-min.png" width="311" height="99" alt="'"> </a>
            </section>
            <section class="search">
                <form method="get" action="###">
                    <div class="area"><input type="text" value="" name="q" id="q"></div>
                    <input type="hidden" value="UTF-8" name="ie">
                    <button class="search-btn"><span>搜索</span></button>
                </form>
            </section>
        </div>
</header>

<div id="main" class="grid">


<div id="container" style="width:50px;height:50px;background:#eee000"></div>

</div>

<footer id="footer">
	<i class="hd"></i>
	<div class="ft"></div>
</footer>
<script>
seajs.config({
	  base: "<?php echo createStaticPath('js',true);?>/",
	  alias: {
	    "jquery": "jquery/jquery/1.10.1/jquery.js"
	  }
});
seajs.use("<?php echo createStaticPath('js',true);?>/temp/src/main")
</script>

</body>
</html>