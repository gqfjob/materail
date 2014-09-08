<style>
#page{
	margin-top:20px;
}
</style>
<div id="container">
	<div class="crumb mb10 mt20">
		<div class="fl ico crumb-ico mr5"></div>当前位置： 
        <a href="<?php echo base_url();?>" target="_self" title="首页" hidefocus="true">首页</a>
		&nbsp;&gt;&nbsp;
        <a href="javascript:void(0);" title="" hidefocus="true">我的素材</a>
        <a href="/material/material_add" class="fr btn btn-info" style="margin-top: -8px;margin-right:10px">上传素材</a>
	</div>
	<?php //var_dump($materials);?>
	<div id="allMaterial">
		<?php if(sizeof($materials) > 0):?>
		<?php foreach ($materials as $m):?>
		<div class="oneMaterail">
			<div class="mbasic">
				<div class="mlogo">
					<img src="<?php echo base_url($m['logo']);?>"/>
				</div>
				<div class="mdetail">
					<p class="mtitle">
						<a href="<?php echo base_url('/material/detail/'.$m['id']);?>" target="_blank"><?php echo $m['mname'];?></a>
						<a style="color:#fff;font-size:12px  !important;" class="btn btn-primary pull-right" href="<?php echo base_url('material/manager/'.$m['id']); ?>" target="_blank">管理</a>
					</p>
					<ul class="mtime">
						<li style="width:150px">版本数：<?php echo $m['vernum']?></li>
						<li style="width:250px">更新时间：<?php echo date('Y-m-d H:i:s',$m['update_at'])?></li>
					</ul>
					<div class="cl"></div>
					<ul class="minfo">
						<li style="width:150px">素材类型：<?php echo $m['cname']?></li>
						<li style="width:250px">上传时间：<?php echo date('Y-m-d H:i:s',$m['create_at'])?></li>
						<li style="width:300px;overflow:hidden">当前版本：<?php echo isset($versions[$m['cversion']]['depict']) ? $versions[$m['cversion']]['depict'] : '';?></li>
					</ul>
					<div class="cl"></div>
					<div class="mcontent">
					<?php echo isset($versions[$m['cversion']]['nohtml']) ? $versions[$m['cversion']]['nohtml'] : '';?>
					</div>
				</div>
				<div class="cl"></div>
			</div>
		</div>
		<?php endforeach;?>
		<!-- 分页 -->
		<?php if($showPage):?>
		<div class="cl"></div>
		<ul id="page">
			<?php if($pagePre):?>
			<li class="fl"><a href="<?php echo base_url('material/my_material/'.$pagePre)?>" class="mbtn mbtn-default">上一页</a></li>
			<?php else:?>
			<li class="fl"><a href="javascript:void(0);" class="mbtn mbtn-default disable">上一页</a></li>
			<?php endif;?>
			<li class="fl" style="margin-left: 350px;">共计<?php echo $total;?>条记录</li>
			<?php if($pageNext):?>
			<li class="fr"><a href="<?php echo base_url('material/my_material/'.$pageNext)?>" class="mbtn mbtn-default">下一页</a></li>
			<?php else:?>
			<li class="fr"><a href="javascript:void(0);" class="mbtn mbtn-default disable">下一页</a></li>
			<?php endif;?>
		</ul>
		<div class="cl"></div>
		<?php endif;?>
		<?php else:?>
		<?php endif;?>
	</div>
</div>