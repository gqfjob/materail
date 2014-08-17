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
	          	<a href="#" class="bread-current">控制台</a>
	        </div>
        	<div class="clearfix"></div>
      </div>
      <div class="container">
      	 <form class="navbar-form navbar-right" role="form" method="get" action="<?php echo base_url('admin/mgMaterial');?>">
			  <div class="form-group">
			    <label class="sr-only" for="exampleInputEmail2">搜索素材</label>
			    <input type="text" class="form-control col-lg-9" id="search" name="search" value="<?php echo isset($search) ? $search : '';?>" placeholder="搜索素材名">
			  </div>
			  <div class="form-group">
			  	<button type="submit" class="btn btn-default">查找</button>
			  </div>
	 	 </form>
		 	
	  </div>
	  <div class="clearfix"></div>
      <!-- Page heading ends -->
		<div class="container">
          <!-- Table -->
        	<div class="row">
            	<div class="col-md-12">
	                <div class="widget">
		            	<div class="widget-head">
		                	<div class="pull-left">素材列表</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                    <table class="table table-striped table-bordered table-hover">
		                    	<thead>
			                        <tr>
			                        	<th><input type="checkbox" id="check-all" value="" autocomplete="off" /></th>
			                            <th>素材名</th>
			                            <th>作者</th>
			                            <th>类型</th>
			                            <th>附件数</th>
			                            <th>版本数</th>
			                            <th>当前版本描述</th>
			                            <th>上传时间</th>
			                        </tr>
		                      	</thead>
		                      <tbody>
		                         <?php if(empty($materials)) : ?>
		                         <tr>
		                         	<td colspan="8" class="text-center"><strong>暂无素材</strong></td>
		                         </tr>
		                         <?php else: ?>       
		                         <?php foreach($materials as $material) : ?>
		                         <tr>
		                         	<td><input autocomplete="off" type="checkbox" name="material" data-id="<?php echo $material['id'];?>" value="" /></td>
		                         	<td><a href="<?php echo base_url('admin/mgVersion/' . $material['id']);?>"><?php echo $material['mname']; ?></a></td>
		                         	<td><a href=""><?php echo isset($users[$material['uid']]['nickname']) ? $users[$material['uid']]['nickname'] : '';?></a></td>
		                         	<td><?php echo $material['cname']; ?></td>
		                         	<td><?php echo isset($attachment_num[$material['id']]) ? $attachment_num[$material['id']] : '';?></td>
		                         	<td><?php echo $material['vernum']?></td>
		                         	<td><?php echo isset($versions[$material['cversion']]['depict']) ? $versions[$material['cversion']]['depict'] : ''; ?></td>
		                         	<td><?php echo date('Y-m-d H:i:s', $material['create_at'])?></td>
		                         </tr>
		                         <?php endforeach;?>
		                         <?php endif;?>                                            
		                      </tbody>
		                    </table>
		
		                    <div class="widget-foot">
								<div class="pull-left" style="padding-top:10px">
									<button id="set-draft" data-status="0" type="button" class="btn btn-primary">转为草稿</button>
									<button id="set-publish" data-status="1" type="button" class="btn btn-success">发布</button>
									<button id="delete-material" type="button" class="btn btn-danger">批量删除</button>
								</div>
		                      	
		                       <?php echo $pages; ?>
		                      
		                      <div class="clearfix"></div> 
		
		                    </div>
		
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
		//全选
		$('#check-all').click(function(){
			if($('input[id="check-all"]:checked').length){
				$('input[name="material"]').prop('checked', true);
			}else{
				$('input[name="material"]').prop('checked', false);
			}
		});

		$('input[name="material"]').click(function(){
			if($('input[name="material"]').length == $('input[name="material"]:checked').length){
				$('#check-all').prop('checked', true);
			}else{
				$('#check-all').prop('checked', false);
			}
		});

		//设置\发布草稿
		$('#set-draft,#set-publish').click(function(){
			var _this = $(this);
			var material_checked = $('input[name="material"]:checked');
			if( ! material_checked.length){
				notice('请选择操作项', 300);
				return false;
			}
			var mids = new Array();
			material_checked.each(function(){
				mids.push(parseInt($(this).attr('data-id')));
			});
			mids = mids.join();
			var status = parseInt(_this.attr('data-status'));
			var text = (status) ? '发布' : '转为草稿';
			_this.addClass('disabled');
			$.ajax({
				url:'/admin/set_material_status',
				type:'post',
				dataType:'json',
				data:{mids:mids,status:status,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
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
			var _this = $(this);
			var material_checked = $('input[name="material"]:checked');
			if( ! material_checked.length){
				notice('请选择操作项', 300);
				return false;
			}
			var mids = new Array();
			material_checked.each(function(){
				mids.push(parseInt($(this).attr('data-id')));
			});
			mids = mids.join();
			_this.addClass('disabled');
			$.ajax({
				url:'/admin/delete_material',
				type:'post',
				dataType:'json',
				data:{mids:mids,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice('删除成功', 300);
						material_checked.parents('tr').remove();
					}else{
						notice('删除失败', 300);
					}
				},
				error:function(){
					_this.removeClass('disabled');
					notice('出错了', 300);
				},
				
			});
		});
	});
</script>