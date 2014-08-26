<!-- Content starts -->
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		<!-- Page heading -->
        <div class="page-head">
        	<h2 class="pull-left"><i class="icon-file-alt"></i> 素材管理</h2>
        	<!-- Breadcrumb -->
	        <div class="bread-crumb pull-right">
	        	<a href="<?php echo base_url('admin/index'); ?>"><i class="icon-home"></i> 首页</a> 
	            <!-- Divider -->
	          	<span class="divider">/</span> 
	          	<a href="<?php echo base_url('admin/mgMaterial');?>" class="bread-current">素材管理</a>
	          	<span class="divider">/</span> 
	          	<span class="divider"><?php echo $material['mname']; ?></span> 
	        </div>
        	<div class="clearfix"></div>
        </div>
        
        <div class="container">
        	<div class="row">
        		<div class="col-md-12">
        			<div class="widget">
						 <div class="widget-content">
						 	<div class="padd">
						 		<div class="row">
						 			<div class="col-md-2" style="width:148px;">
						 				<img src="<?php echo base_url($material['logo']);?>" />
						 			</div>
						 			<div class="col-md-8">
						 				<a href="#" class="h3"><?php echo $material['mname'];?></a>
						 				<div class="row">
						 					<div class="col-md-4">上传时间:<?php echo date('Y-m-d H:i:s', $material['create_at'])?></div>
						 					<div class="col-md-4">素材类型:<?php echo $material['cname']?></div>
						 				</div>
						 				<div class="row">
						 					<div class="col-md-4">更新时间:<?php echo date('Y-m-d H:i:s', $material['update_at'])?></div>
						 					<div class="col-md-4">版本数:<span class="vnum"><?php echo $material['vernum']?></span></div>
						 				</div>
						 				<div class="row">
						 					<div class="col-md-8">
						 						简介:<?php echo isset($material_versions[$material['cversion']]['nohtml']) ? $material_versions[$material['cversion']]['nohtml'] : ''; ?>
						 					</div>
						 				</div>
						 			</div>
						 		</div>
						 	</div>
						 </div>
        			</div>
        		</div>
        	</div>
        </div>
        
        <div class="container">
        	<div class="row">
        		<div class="col-md-12">
        			<div class="widget">
        				 <div class="widget-head">
		                	<div class="pull-left">详细说明</div>
		                    <div class="clearfix"></div>
		                </div>
						 <div class="widget-content">
						 	<div class="padd">
							 	<div id="myEditor" style="height:200px"></div>
						    	<link href="<?php echo base_url('assets/js/ueditor/themes/default/css/umeditor.css');?>" media="screen" rel="stylesheet" type="text/css">
						    	<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/umeditor.config.js');?>"></script>
								<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/umeditor.min.js');?>"></script>
								<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/lang/zh-cn/zh-cn.js');?>"></script>
								<?php if(empty($material_versions[$material['cversion']]['content'])) : ?>
								<script type="text/javascript">
								        var editor = UM.getEditor('myEditor',{textarea:'version-content'});
								</script>
								<?php else:?>
								<script type="text/javascript">
								        var editor = UM.getEditor('myEditor',{textarea:'version-content', initialContent:'<?php echo $material_versions[$material['cversion']]['content'];?>'});
								</script>
								<?php endif;?>
							</div>
						 </div>
						 <div class="widget-foot">
							<div class="pull-right" style="padding-top:10px">
								<button id="edit-version-content" data-mid="<?php echo $material['id']?>" data-vid="<?php echo $material['cversion'];?>" type="button" class="btn btn-success">修改</button>
							</div>
	                      <div class="clearfix"></div> 
	
	                    </div>
        			</div>
        		</div>
        	</div>
        </div>
        
        <div class="container">
        	<div class="row">
        		<div class="col-md-12">
        			<div class="widget">
						 <div class="widget-content">
						 	<table class="table table-striped table-bordered table-hover">
						 		<thead>
							   		<tr>
							   			<th>版本</th>
							   			<th class="text-center">上传时间</th>
							   			<th class="text-center">操作</th>
							   			<th class="text-center">默认版本</th>
							   		</tr>
						   		</thead>
						   		<?php if(empty($material_versions)):?>
						   		<tr><td colspan="4" class="text-center">无版本信息</td></tr>
						   		<?php else:?>
						   		<?php foreach($material_versions as $version) : ?>
						   		<tr>
						   			<td><a href="<?php echo base_url('/material/edit_version/' . $material['id'] . '/' .$version['id']);?>" title=""><?php echo $version['depict']?></a></td>
						   			<td class="text-center"><?php echo date('Y-m-d H:i:s',$version['cat']);?></td>
						   			<td class="text-center"><a href="###" class="delete-version" data-vid="<?php echo  $version['id'];?>" data-mid="<?php echo $material['id'];?>">删除</a></td>
						   			<td class="text-center">
						   				<?php if($material['cversion'] == $version['id']):?>
						   				<input type="radio" name="current-version" autocomplete="off" checked="checked" value="<?php echo  $version['id'];?>" data-default="1" data-mid="<?php echo $material['id'];?>" />
						   				<?php else:?>
						   				<input type="radio" name="current-version" autocomplete="off" value="<?php echo  $version['id'];?> " data-default="0" data-mid="<?php echo $material['id'];?>" />
						   				<?php endif;?>
						   			</td>
						   		</tr>
						   		<?php endforeach;?>
						   		<?php endif;?>
						    </table>
						 </div>
						 <div class="widget-foot">
							<div class="pull-right" style="padding-top:10px">
								<button id="delete-material" data-id="<?php echo $material['id']?>" type="button" class="btn btn-danger">删除</button>
								<button id="set-draft" data-id="<?php echo $material['id']?>" data-status="0" type="button" class="btn btn-primary">转为草稿</button>
								<button id="set-publish" data-id="<?php echo $material['id']?>" data-status="1" type="button" class="btn btn-success">发布</button>
							</div>
	                      <div class="clearfix"></div> 
	
	                    </div>
        			</div>
        		</div>
        	</div>
        </div>
	</div>
	<!-- Main bar end-->
	<div class="clearfix"></div>
</div>
<!-- Content ends -->
<script type="text/javascript">
	$(function(){
		//设置默认版本
		$('input[name="current-version"]').click(function(){
			var _this = $(this);
			if(_this.attr('data-default') == '1'){
				return false;
			}
			_this.addClass('disabled');
			$.ajax({
				url      : '/admin/set_default_version',
				type     : 'post',
				dataType : 'json',
				data     : {vid:parseInt(_this.val()),mid:parseInt(_this.attr('data-mid')),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success  : function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice('操作成功', 300);
						_this.parents('tr').siblings('tr').find('input[name="current-version"]').prop('checked',false).attr('data-default', 0);
						$('#msgModal button[data-dismiss="modal"],.modal-open').click(function(){window.location.reload();});
					}else{
						notice('失败成功',300);
						_this.prop('checked',false);
						$('input[data-default="1"]').prop('checked',true);
					}
				},
				error    : function(){_this.removeClass('disabled');notice('出错了',300);}
			});
		});

		//设置\发布草稿
		$('#set-draft,#set-publish').click(function(){
			var _this = $(this);
			var mid = parseInt(_this.attr('data-id'));
			var status = parseInt(_this.attr('data-status'));
			var text = (status) ? '发布' : '转为草稿';
			_this.addClass('disabled');
			$.ajax({
				url:'/admin/set_material_status',
				type:'post',
				dataType:'json',
				data:{mids:mid,status:status,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
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
				error:function(){
					_this.removeClass('disabled');
					notice('出错了', 300);
				},
				
			});
		});

		//删除素材
		$('#delete-material').click(function(){
			if(confirm('确定删除此素材')){
				var _this = $(this);
				var mid = parseInt(_this.attr('data-id'));
				_this.addClass('disabled');
				$.ajax({
					url:'/admin/delete_material',
					type:'post',
					dataType:'json',
					data:{mids:mid,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
					success:function(res){
						_this.removeClass('disabled');
						if(res.status){
							notice('删除成功', 300);
							$('#msgModal button[data-dismiss="modal"],.modal-open').click(function(){window.location.href='/admin/mgMaterial';});
						}else{
							notice('删除失败', 300);
						}
					},
					error:function(){
						_this.removeClass('disabled');
						notice('出错了', 300);
					},
					
				});
			}
		});

		//删除版本
		$('.delete-version').click(function(){
			if(confirm('确定删除此版本吗？')){
			    var _this = $(this);
			    _this.addClass('disabled');
				$.ajax({
					url : '/admin/delete_version',
					type : 'post',
					dataType : 'json',
					data : {mid:parseInt(_this.attr('data-mid')),vid:parseInt(_this.attr('data-vid')),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
					success : function(res){
						_this.removeClass('disabled');
						if(res.status){
							_this.parents('tr').remove();
						}else{
							notice('删除失败', 300);
						}
					},
					error:function(){_this.removeClass('disabled');notice('出错了', 300);}
				});
		    }
			return false;
		});

		//修改版本说明
		$('#edit-version-content').click(function(){
			var _this = $(this);
			_this.addClass('disabled');
			$.ajax({
				url : '/admin/edit_version_content',
				type : 'post',
				dataType : 'json',
				data : {mid:parseInt(_this.attr('data-mid')),vid:parseInt(_this.attr('data-vid')),content:editor.getContent(),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success : function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice('修改成功', 300);
						$('#msgModal button[data-dismiss="modal"],.modal-open').click(function(){window.location.reload();});
					}else{
						notice(res.msg, 300);
					}
				},
				error:function(){_this.removeClass('disabled');notice('出错了', 300);}
			});
			
		});
	});
</script>