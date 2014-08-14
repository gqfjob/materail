<div class="container crumb mb20 mt20">
	<div class="fl ico crumb-ico mr5"></div>当前位置：
    <a href="<?php echo base_url();?>" title="" hidefocus="true">首页</a>&nbsp;&gt;&nbsp;<span hidefocus="true">管理素材</span>
</div>
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
	   		<?php $disabled = ( ! $manager_material) ? 'disabled="disabled"' : '';?>
	   		<?php foreach($material_versions as $version) : ?>
	   		<tr>
	   			<td><a href="<?php echo base_url('/material/edit_version/' . $material['id'] . '/' .$version['id']);?>" title=""><?php echo $version['depict']?></a></td>
	   			<td class="text-center"><?php echo date('Y-m-d H:i:s',$version['cat']);?></td>
	   			<td class="text-center">
	   				<?php if($manager_material || $version['uid'] == $user['id']) : ?>
		   			<a href="<?php echo base_url('/material/edit_version/' . $material['id'] . '/' .$version['id']);?>" title="" >修改</a>
		   			<span class="separator">|</span>
		   			<a href="###" class="delete-version" data-vid="<?php echo  $version['id'];?>" data-mid="<?php echo $material['id'];?>">删除</a>
		   			<?php endif;?>
	   			</td>
	   			<td class="text-center">
	   				<?php if($material['cversion'] == $version['id']):?>
	   				<input type="radio" name="current-version" autocomplete="off" checked="checked" value="<?php echo  $version['id'];?>" data-default="1" data-mid="<?php echo $material['id'];?>"  <?php echo $disabled; ?> />
	   				<?php else:?>
	   				<input type="radio" name="current-version" autocomplete="off" value="<?php echo  $version['id'];?> " data-default="0" data-mid="<?php echo $material['id'];?>" <?php echo $disabled; ?> />
	   				<?php endif;?>
	   			</td>
	   		</tr>
	   		<?php endforeach;?>
	   		<?php endif;?>
	    </table>
	</div>
	<div id="material-op" class="container">
		<?php if($manager_material) :?>
		<a id="set-draft" class="btn btn-default btn-primary" data-id="<?php echo $material['id']?>" data-status="0">转为草稿</a>
		<a id="set-publish" class="btn btn-default btn-success" data-id="<?php echo $material['id']?>" data-status="1">发布</a>
		<?php endif;?>
		<a href="<?php echo base_url('material/add_version/' . $material['id'])?>" class="btn btn-default btn-info">上传新版本</a>
	</div>
</div>
<?php if($manager_material) :?>
<script type="text/javascript">
	$(function(){
		//设置状态
		$('#set-draft,#set-publish').click(function(){
			var _this = $(this);
			var mid = parseInt(_this.attr('data-id'));
			var status = parseInt(_this.attr('data-status'));
			var text = (status) ? '发布' : '转为草稿';
			_this.addClass('disabled');
			$.ajax({
				url      : '/material/set_material_status',
				type     : 'post',
				dataType : 'json',
				data     : {mid:mid,status:status,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success  : function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice(text + '成功', 300);
					}else{
						if(res.msg){
							notice(msg, 300);
						}else{
							notice(text + '失败', 300);
						}
					}
				},
				error    : function(){
					_this.removeClass('disabled');
					notice('出错了', 300);
				}
			});
		});

		//设置默认版本
		$('input[name="current-version"]').click(function(){
			var _this = $(this);
			if(_this.attr('data-default') == '1'){
				return false;
			}
			_this.addClass('disabled');
			$.ajax({
				url      : '/material/set_default_version',
				type     : 'post',
				dataType : 'json',
				data     : {vid:parseInt(_this.val()),mid:parseInt(_this.attr('data-mid')),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success  : function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice('操作成功',300);
						$('#msgModal button[data-dismiss="modal"],.modal-open').click(function(){window.location.reload();});
					}else{
						notice(res.msg,300);
					}
				},
				error    : function(){
					_this.removeClass('disabled');
					notice('出错了', 300);
				}
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
							notice(res.msg,300);
						}
					},
					error:function(){}
				});
		    }
			return false;
		});
	});
</script>
<?php endif;?>