<div class="box">
	<div id="material-detail" class="container">
		<div class="material-thumb-box pull-left">
	    	<div class="material-thumb">
	    		<img width="118" height="118" src="<?php echo base_url($material['logo']);?>" />
	    		<div class="material-alt-bg"></div>
	    		<div class="material-alt text-overflow"><span class="text-overflow" title="<?php echo isset($material_versions[$material['cversion']]['depict']) ? $material_versions[$material['cversion']]['depict'] : ''; ?>"><?php echo isset($material_versions[$material['cversion']]['depict']) ? $material_versions[$material['cversion']]['depict'] : ''; ?></span></div>
	    	</div>
	    </div>
	    <div class="material-content-box pull-right">
	    	<div class="material-cate text-overflow pull-left" title="<?php echo $material['cname']?>"><?php echo $material['cname']?></div>
	    	<div class="material-name pull-right"><?php echo $material['mname']?></div>
	    	<div class="clearfix"></div>
	    	<div class="material-content">
				<?php echo isset($material_versions[$material['cversion']]['nohtml']) ? $material_versions[$material['cversion']]['nohtml'] : ''; ?>
			</div>
	   	    <div class="material-vnum pull-left">版本数:<?php echo $material['vernum']?></div>
	   	    <div class="material-ctime pull-left">创建时间:<?php echo date('Y-m-d H:i:s',$material['create_at']);?></div>
	   	    <div class="material-utime pull-right">更新时间:<?php echo date('Y-m-d H:i:s',$material['update_at']);?></div>
	    </div>
	</div>
	<div id="material-versions" class="container">
		<table class="table table-hover">
	   		<tr>
	   			<th width="50%">版本</th>
	   			<th width="20%" class="text-center">上传时间</th>
	   			<th width="15%" class="text-center">操作</th>
	   			<th width="15%" class="text-center">默认版本</th>
	   		</tr>
	   		<?php if(empty($material_versions)):?>
	   		<tr><td colspan="4" class="text-center">无版本信息</td></tr>
	   		<?php else:?>
	   		<?php foreach($material_versions as $version) : ?>
	   		<tr>
	   			<td><a href="<?php echo base_url('/material/edit_version/' . $material['id'] . '/' .$version['id']);?>" title=""><?php echo $version['depict']?></a></td>
	   			<td class="text-center"><?php echo date('Y-m-d H:i:s',$version['cat']);?></td>
	   			<td class="text-center"><a href="<?php echo base_url('/material/edit_version/' . $material['id'] . '/' .$version['id']);?>" title="" >修改</a><span class="separator">|</span><a href="###" class="delete-version" data-vid="<?php echo  $version['id'];?>" data-mid="<?php echo $material['id'];?>">删除</a></td>
	   			<td class="text-center">
	   				<?php if($material['cversion'] == $version['id']):?>
	   				<input type="radio" name="current-version" autocomplete="off" checked="checked" value="<?php echo  $version['id'];?>" data-mid="<?php echo $material['id'];?>" />
	   				<?php else:?>
	   				<input type="radio" name="current-version" autocomplete="off" value="<?php echo  $version['id'];?> " data-mid="<?php echo $material['id'];?>" />
	   				<?php endif;?>
	   			</td>
	   		</tr>
	   		<?php endforeach;?>
	   		<?php endif;?>
	    </table>
	</div>
	<div id="material-op" class="container">
		<a id="set-draft" class="btn" data-id="<?php echo $material['id']?>" data-status="0">设为草稿</a>
		<a id="set-publish" class="btn" data-id="<?php echo $material['id']?>" data-status="1">发布</a>
		<a href="<?php echo base_url('material/add_version/' . $material['id'])?>" class="btn">上传新版本</a>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		//设置状态
		$('#set-draft,#set-publish').click(function(){
			var _this = $(this);
			$.ajax({
				url      : '/material/set_material_status',
				type     : 'post',
				dataType : 'json',
				data     : {mid:_this.attr('data-id'),status:parseInt(_this.attr('data-status')),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success  : function(res){
					if(res.status){
						alert('操作成功');
					}else{
						alert('操作失败');
					}
				},
				error    : function(){}
			});
		});

		//设置默认版本
		$('input[name="current-version"]').click(function(){
			var _this = $(this);
			$.ajax({
				url      : '/material/set_default_version',
				type     : 'post',
				dataType : 'json',
				data     : {vid:parseInt(_this.val()),mid:parseInt(_this.attr('data-mid')),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success  : function(res){
					if(res.status){
						alert('操作成功');
						window.location.reload();
					}else{
						alert('操作失败');
					}
				},
				error    : function(){}
			});
		});

		//删除版本
		$('.delete-version').click(function(){
			if(confirm('确定删除此版本吗？')){
			    var _this = $(this);
				$.ajax({
					url : '/material/delete_version',
					type : 'post',
					dataType : 'json',
					data : {mid:parseInt(_this.attr('data-mid')),vid:parseInt(_this.attr('data-vid')),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
					success : function(res){
						if(res.status){
							_this.parents('tr').remove();
						}else{
							alert(res.msg);
						}
					},
					error:function(){}
				});
		    }
			return false;
		});
	});
</script>