<style>
#page{
	margin-top:20px;
}
</style>
<div id="container">
	<div class="crumb mb10 mt20">
		<div class="fl ico crumb-ico mr5"></div>当前位置： 
        <a href="###" target="_self" title="分类" hidefocus="true">分类列表</a>
		&nbsp;&gt;&nbsp;
        <a href="###" title="" hidefocus="true"><?php echo ($cid == 0) ? '全部' : $cateName;?></a>
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
					<p class="mtitle"><a href="<?php echo base_url('/material/detail/'.$m['id']);?>"><?php echo $m['mname'];?></a></p>
					<ul class="mtime">
						<li style="width:150px">版本数：<?php echo $m['vernum']?></li>
						<li style="width:250px">更新时间：<?php echo date('Y-m-d H:i:s',$m['upat'])?></li>
					</ul>
					<div class="cl"></div>
					<ul class="minfo">
						<li style="width:150px">素材类型：<?php echo $cateName?></li>
						<li style="width:250px">上传时间：<?php echo date('Y-m-d H:i:s',$m['cat'])?></li>
						<li style="width:300px;overflow:hidden">当前版本：<?php echo $m['depict']?></li>
					</ul>
					<div class="cl"></div>
					<div class="mcontent">
					<?php echo $m['nohtml'];?>
					</div>
				</div>
				<div class="cl"></div>
				<div id="snapshot_<?php echo $m['id']."_0";?>" style="display: none;margin-top:10px"></div>
				<script type="text/javascript">
					$.post('/material/getSnapshot',
					{
						'mid':"<?php echo $m['id'];?>",
						'vid':"<?php echo 0;?>",
						'<?php echo $this->config->item('csrf_token_name'); ?>':'<?php echo $this->security->get_csrf_hash();?>'
					},
					function(data){
						var t = $("#snapshot_<?php echo $m['id']."_0";?>");
						var res = data.data;
						if(res.length > 0){
							var len = (10>res.length)?res.length:10;
							var html="";
							for(i=0;i<len;i++){
								html +="<img src='"+res[i]+"' style='width:60px;height:60px;margin-right:20px'>";
							}	
							t.html(html);
							t.show();
						}
					},"json");
				</script>
				<div class="cl"></div>
			</div>
		</div>
		<?php endforeach;?>
		<!-- 分页 -->
		<?php if($showPage):?>
		<div class="cl"></div>
		<ul id="page">
			<?php if($pagePre):?>
			<li class="fl"><a href="<?php echo base_url('material/lists/'.$cid.'/'.$pagePre)?>" class="mbtn mbtn-default">上一页</a></li>
			<?php else:?>
			<li class="fl"><a href="javascript:void(0);" class="mbtn mbtn-default disable">上一页</a></li>
			<?php endif;?>
			<li class="fl" style="margin-left: 350px;">共计<?php echo $total;?>条记录</li>
			<?php if($pageNext):?>
			<li class="fr"><a href="<?php echo base_url('material/lists/'.$cid.'/'.$pageNext)?>" class="mbtn mbtn-default">下一页</a></li>
			<?php else:?>
			<li class="fr"><a href="javascript:void(0);" class="mbtn mbtn-default disable">下一页</a></li>
			<?php endif;?>
		</ul>
		<div class="cl"></div>
		<?php endif;?>
		<?php else:?>
		<div class="oneMaterail">
			<div class="mbasic">
				<div class="center">没有素材</div>
				<div class="cl"></div>
			</div>
		</div>
		<?php endif;?>
	</div>
</div>