<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php if(isset($title) && !empty($title)) echo $title.'-';?>创新梦工厂</title>
<meta name="description" content="<?php if(isset($description) && !empty($description)) echo $description.'-';?>">
<meta name="keywords" content="爱创新,<?php if(isset($keywords) && !empty($keywords)) echo $keywords;?>">

<script src="<?php echo createStaticPath('js');?>/seajs/seajs/2.2.0/sea.js"></script>

</head>
<body>
<div id="container">
  <img src="https://i.alipayobjects.com/e/201211/1cqKb32QfE.png" width="44" height="44" alt="H">
  <img src="https://i.alipayobjects.com/e/201211/1cqKb2rJHI.png" width="44" height="44" alt="e">
  <img src="https://i.alipayobjects.com/e/201211/1cqKeZrUpg.png" width="44" height="44" alt="l">
  <img src="https://i.alipayobjects.com/e/201211/1cqM4u3Ejk.png" width="44" height="44" alt="l">
  <img src="https://i.alipayobjects.com/e/201211/1cqKoKV2Sa.png" width="44" height="44" alt="o">
  <img src="https://i.alipayobjects.com/e/201211/1cqKb4JU4K.png" width="44" height="44" alt=",">
  <img src="https://i.alipayobjects.com/e/201211/1cqKojFDLY.png" width="44" height="44" alt="S">
  <img src="https://i.alipayobjects.com/e/201211/1cqKb2sBO8.png" width="44" height="44" alt="e">
  <img src="https://i.alipayobjects.com/e/201211/1cqKb2LmXk.png" width="44" height="44" alt="a">
  <img src="https://i.alipayobjects.com/e/201211/1cqKb1jcWC.png" width="44" height="44" alt="J">
  <img src="https://i.alipayobjects.com/e/201211/1cqKojb72y.png" width="44" height="44" alt="S">
</div>

 
<script>
// Set configuration
seajs.config({
  base: "<?php echo createStaticPath('js',true);?>",
  alias: {
    "jquery": "jquery/jquery/1.10.1/jquery.js"
  }
});
<?php if(defined('ENVIRONMENT') && (ENVIRONMENT=='development')):?>
seajs.use("<?php echo createStaticPath('js',true);?>/hello/src/main");
<?php else:?>
seajs.use("<?php echo createStaticPath('js',true);?>/hello/1.0.0/main");
<?php endif;?>
</script>

</body>
</html>