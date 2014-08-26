<!-- Content starts -->
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		<!-- Page heading -->
        <div class="page-head">
        	<h2 class="pull-left"><i class="icon-file-alt"></i> 用户管理</h2>
        	<!-- Breadcrumb -->
	        <div class="bread-crumb pull-right">
	        	<a href="<?php echo base_url('admin/index'); ?>"><i class="icon-home"></i> 首页</a> 
	            <!-- Divider -->
	          	<span class="divider">/</span> 
	          	<a href="<?php echo base_url('admin/mgUser');?>" class="bread-current">用户管理</a>
	          	<span class="divider">/</span> 
	          	<span class="divider"><?php echo $user['realname']; ?></span> 
	        </div>
        	<div class="clearfix"></div>
      </div>
      
      <!-- Page heading ends -->
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="widget">
						<div class="widget-head">
		                	<div class="pull-left">用户基本信息</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content" style="height:125px">
		                	<div class="padd">
			                	<div class="row checkbox">
			                		<div class="col-md-3 text-right"><strong>姓名：</strong></div>
			                		<div class="col-md-9"><?php echo $user['realname']?></div>
			                	</div>
			                	<div class="row checkbox">
			                		<div class="col-md-3 text-right"><strong>上次登录ip：</strong></div>
			                		<div class="col-md-9"><?php echo $user['last_login_ip']?></div>
			                	</div>
			                	<div class="row checkbox">
			                		<div class="col-md-3 text-right"><strong>上次登录：</strong></div>
			                		<div class="col-md-9"><?php echo empty($user['last_login_time']) ? '' : date('Y-m-d H:i:s', $user['last_login_time']);?></div>
			                	</div>
		                	</div>
		                </div>
	                </div>
				</div>
				<div class="col-md-6">
					<div class="widget">
						<div class="widget-head">
		                	<div class="pull-left">用户基本权限</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content" style="height:125px">
		                	<div class="padd">
			                	<div class="row">
			                		<div class="col-md-4">
										<div class="checkbox">
										    <label>
										      <input id="set-admin" type="checkbox" <?php echo ($user['auth'] == 2) ? 'checked="checked"' : '';?> autocomplete="off"> <strong>后台管理员</strong>
										    </label>
										</div>
									</div>
			                		<div class="col-md-8">
			                			<div class="checkbox ">
										    <label>
										      <input id="set-status" type="checkbox" <?php echo ($user['status'] == 1) ? 'checked="checked"' : '';?> autocomplete="off"> <strong>可访问网站</strong>
										    </label>
										</div>
			                		</div>
			                	</div>
			                	<div class="row">
			                		<div class="col-md-4">
			                			<div class="checkbox">
										    <label>
										      <input id="set-upload" type="checkbox"  <?php echo ($user['upload_auth'] == 1) ? 'checked="checked"' : '';?> autocomplete="off"> <strong>可上传素材</strong>
										    </label>
										</div>
									</div>
			                	</div>
		                	</div>
		                </div>
	                </div>
				</div>
			</div>
          <!-- Table -->
        	<div class="row">
            	<div class="col-md-12">
	                <div class="widget">
		            	<div class="widget-head">
		                	<div class="pull-left">可访问素材列表</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                    <table class="table table-striped table-bordered table-hover">
		                    	<thead>
			                        <tr>
			                        	<th width="50"><input type="checkbox" id="check-view-all" value="" autocomplete="off" /></th>
			                            <th width="300">素材名</th>
			                            <th>作者</th>
			                            <th>类型</th>
			                            <th>附件数</th>
			                            <th>版本数</th>
			                            <th>当前版本</th>
			                            <th>上传时间</th>
			                        </tr>
		                      	</thead>
		                      <tbody class="view-material-box">
		                         <?php if(empty($view_materials)) : ?>
		                         <tr>
		                         	<td colspan="8" class="text-center"><strong>暂无素材</strong></td>
		                         </tr>
		                         <?php else: ?>       
		                         <?php foreach($view_materials as $material) : ?>
		                         <tr>
		                         	<td><input autocomplete="off" type="checkbox" name="view-material" data-id="<?php echo $material['id'];?>" value="" /></td>
		                         	<td><a href="<?php echo base_url('admin/mgVersion/' . $material['id']);?>" target="_blank"  title="<?php echo $material['mname'];?>" ><?php echo $material['mname'];?></a></td>
		                         	<td><a href="<?php echo base_url('admin/userDetail/' . $material['uid']);?>" target="_blank"><?php echo empty($users[$material['uid']]['realname']) ? '' : $users[$material['uid']]['realname'];?></a></td>
		                         	<td><?php echo empty($material['cname']) ? '' : $material['cname'];?></td>
		                         	<td><?php echo empty($attachment_num[$material['id']]['num']) ? 0 : $attachment_num[$material['id']]['num']; ?></td>
		                         	<td><?php echo $material['vernum'];?></td>
		                         	<td><?php echo empty($versions[$material['cversion']]['depict']) ? '' : $versions[$material['cversion']]['depict'];?></td>
		                         	<td><?php echo date('Y-m-d H:i:s', $material['create_at']); ?></td>
		                         </tr>
		                         <?php endforeach;?>
		                         <?php endif;?>                                            
		                      </tbody>
		                    </table>
		
		                    <div class="widget-foot">
								<div class="pull-left" style="padding-top:10px">
									<button id="add-view-material" type="button" class="btn btn-primary">新增访问素材</button>
									<button id="remove-view-material" type="button" class="btn btn-danger">删除访问权限</button>
								</div>
		                      	
		                       <?php echo empty($view_pages) ? '' : '<div class="view-pages">' . $view_pages . '</div>'; ?>
		                      
		                      <div class="clearfix"></div> 
		
		                    </div>
		
		                 </div>
	                </div>
              </div>
            </div>
            <div class="row">
            	<div class="col-md-12">
	                <div class="widget">
		            	<div class="widget-head">
		                	<div class="pull-left">上传素材列表</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                    <table class="table table-striped table-bordered table-hover">
		                    	<thead>
			                        <tr>
			                        	<th width="50"><input type="checkbox" id="check-upload-all" value="" autocomplete="off" /></th>
			                            <th width="300">素材名</th>
			                            <th>类型</th>
			                            <th>附件数</th>
			                            <th>版本数</th>
			                            <th>当前版本</th>
			                            <th>上传时间</th>
			                            <th>作者</th>
			                        </tr>
		                      	</thead>
		                      <tbody class="upload-material-box">
		                         <?php if(empty($upload_materials)) : ?>
		                         <tr>
		                         	<td colspan="8" class="text-center"><strong>暂无用户</strong></td>
		                         </tr>
		                         <?php else: ?>       
		                         <?php foreach($upload_materials as $material) : ?>
		                         <tr>
		                         	<td><input autocomplete="off" type="checkbox" name="upload-material" data-id="<?php echo $material['id'];?>" value="" /></td>
		                         	<td><a href="<?php echo base_url('admin/mgVersion/' . $material['id']);?>" target="_blank"  title="<?php echo $material['mname'];?>" ><?php echo $material['mname'];?></a></td>
		                         	<td><?php echo empty($material['cname']) ? '' : $material['cname'];?></td>
		                         	<td><?php echo empty($attachment_num[$material['id']]['num']) ? 0 : $attachment_num[$material['id']]['num']; ?></td>
		                         	<td><?php echo $material['vernum'];?></td>
		                         	<td><?php echo empty($versions[$material['cversion']]['depict']) ? '' : $versions[$material['cversion']]['depict'];?></td>
		                         	<td><?php echo date('Y-m-d H:i:s', $material['create_at']); ?></td>
		                         	<td><a href="<?php echo base_url('admin/userDetail/' . $material['uid']);?>" target="_blank"><?php echo empty($users[$material['uid']]['realname']) ? '' : $users[$material['uid']]['realname'];?></a></td>
		                         </tr>
		                         <?php endforeach;?>
		                         <?php endif;?>                                            
		                      </tbody>
		                    </table>
		
		                    <div class="widget-foot">
								<div class="pull-left" style="padding-top:10px">
									<button id="delete-material" type="button" class="btn btn-danger">删除</button>
								</div>
		                      	
		                       <?php echo empty($upload_pages) ? '' : '<div class="upload-pages">' . $upload_pages . '</div>'; ?>
		                      
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
<!-- Modal -->
	<div class="select-material-box modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:450px">
	    	<div class="modal-content">
	      		<div class="modal-header">
			    	<button type="button" class="close cancel-select" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			        <h4 class="modal-title" id="myModalLabel">查询素材</h4>
			    </div>
			    <div class="modal-body">
			        <div class="form-inline">
			        	<div class="form-group">
						    <label class="sr-only" for="serch-user">查找素材</label>
						    <input type="text" class="form-control col-xs-4" id="search-material" placeholder="请输入素材名称">
						    <input type="hidden" value="" id="selected-material"  autocomplete="off" />
						</div>
						<div class="checkbox">
							<button type="button" class="btn btn-primary primary-selected">确定</button>
						</div>
					</div>
					<ul id="search-material-result" class="typeahead dropdown-menu" style="position:static">
					</ul>
					<div class="clearfix"></div>
			    </div>
	    	</div>
	  </div>
	</div>
	<!-- End Modal -->
<!-- Content ends -->
<script type="text/javascript">
	$(function(){
		//全选
		$('#check-view-all').click(function(){
			if($('input[id="check-view-all"]:checked').length){
				$('input[name="view-material"]').prop('checked', true);
			}else{
				$('input[name="view-material"]').prop('checked', false);
			}
		});

		$('body').on('click','input[name="view-material"]',function(){
			if($('input[name="view-material"]').length == $('input[name="view-material"]:checked').length){
				$('#check-view-all').prop('checked', true);
			}else{
				$('#check-view-all').prop('checked', false);
			}
		});

		$('#check-upload-all').click(function(){
			if($('input[id="check-upload-all"]:checked').length){
				$('input[name="upload-material"]').prop('checked', true);
			}else{
				$('input[name="upload-material"]').prop('checked', false);
			}
		});

		$('body').on('click','input[name="upload-material"]',function(){
			if($('input[name="upload-material"]').length == $('input[name="upload-material"]:checked').length){
				$('#check-upload-all').prop('checked', true);
			}else{
				$('#check-upload-all').prop('checked', false);
			}
		});

		//用户权限设置
		$('#set-admin').click(function(){
			var _this = $(this);
			var auth;
			if($('#set-admin:checked').length){
				auth = 2;
			}else{
				auth = 1;
			}
			_this.attr('disabled','disabled');
			$.ajax({
				url:'/admin/set_auth',
				type:'post',
				dataType:'json',
				data:{auth:auth,uid:<?php echo $user['id'];?>,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.removeAttr('disabled');
					if(res.status){
						notice('操作成功',300);
					}else{
						notice(res.msg, 300);
					}
				},
				error:function(){
					_this.removeAttr('disabled');
					notice('出错了', 300);
				},
				
			});
		});

		//用户上传权限设置
		$('#set-upload').click(function(){
			var _this = $(this);
			var upload_auth;
			if($('#set-upload:checked').length){
				upload_auth = 1;
			}else{
				upload_auth = 0;
			}
			_this.attr('disabled','disabled');
			$.ajax({
				url:'/admin/set_upload_auth',
				type:'post',
				dataType:'json',
				data:{upload_auth:upload_auth,uid:<?php echo $user['id'];?>,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.removeAttr('disabled');
					if(res.status){
						notice('操作成功',300);
					}else{
						notice(res.msg, 300);
					}
				},
				error:function(){
					_this.removeAttr('disabled');
					notice('出错了', 300);
				},
				
			});
		});

		//用户状态设置
		$('#set-status').click(function(){
			var _this = $(this);
			var status;
			if($('#set-status:checked').length){
				status = 1;
			}else{
				status = 0;
			}
			_this.attr('disabled','disabled');
			$.ajax({
				url:'/admin/set_user_status',
				type:'post',
				dataType:'json',
				data:{uids:'<?php echo $user['id'];?>',status:status,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.removeAttr('disabled');
					if(res.status){
						notice('操作成功',300);
					}else{
						notice(res.msg, 300);
					}
				},
				error:function(){
					_this.removeAttr('disabled');
					notice('出错了', 300);
				},
				
			});
		});
		
		//获取分页内容
		$('.view-pages').on('click','li a',function(){
			var _this = $(this);
			var d = new Date();
			if(_this.attr('href')){
				$.ajax({
					url:'' + _this.attr('href') + '',
					type:'post',
					dataType:'json',
					data:{d:d.getTime(),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
					success:function(res){
						if(res.status){
							$('.view-material-box').html(res.html);
							$('.view-pages').html(res.pages);
						}else{
							notice(res.msg, 300);
						}
					},
					error:function(){
						notice('出错了', 300);
					},
					
				});
			}
			return false;
		});
		
		$('.upload-pages').on('click','li a',function(){
			var _this = $(this);
			var d = new Date();
			if(_this.attr('href')){
				$.ajax({
					url:'' + _this.attr('href') + '',
					type:'post',
					dataType:'json',
					data:{d:d.getTime(),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
					success:function(res){
						if(res.status){
							$('.upload-material-box').html(res.html);
							$('.upload-pages').html(res.pages);
						}else{
							notice(res.msg, 300);
						}
					},
					error:function(){
						notice('出错了', 300);
					},
					
				});
			}
			return false;
		});

		//增加访问素材
		$('#add-view-material').click(function(){
			$('#myModal').modal();
			$('#search-material').val('');
		});
		$('body').on('keyup','#search-material',function(){
			var _this = $(this);
			$.ajax({
				url:'/admin/search_material',
				type:'post',
				data:{name:$.trim(_this.val()),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				dataType:'json',
				success:function(res){
					if(res.status){
						var li_html = '';
						for(var i = 0 ; i < res.materials.length; i++){
							li_html += '<li><a href="#" data-id="' + res.materials[i]['id'] + '" data-name="' + res.materials[i]['mname'] + '" >' + res.materials[i]['mname'] + '</a></li>'
						}
						if(li_html.length){
							$('#search-material-result').show().html(li_html);
						}else{
							$('#search-material-result').hide().html('');
						}
					}
				}
			});
		});
		$('body').on('click','#search-material-result li a', function(){
			var _this = $(this);
			$('#selected-material').val(_this.attr('data-id'));
			$('#search-material').val(_this.attr('data-name'));
			$('#search-material-result').hide().html('');
			return false;
			
		});
		
		$('.primary-selected').click(function(){
			var mid = parseInt($('#selected-material').val());
			if(!mid){
				alert('请选择素材');
			}
			$.ajax({
				url:'/admin/add_view_material',
				type:'post',
				dataType:'json',
				data:{mid:mid,uid:<?php echo $user['id'];?>,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					if(res.status){
						alert('新增成功');
						window.location.reload();
					}else{
						alert(res.msg);
					}
				}
			});
			
		});

		//删除访问素材
		$('#remove-view-material').click(function(){
			var _this = $(this);
			var material_checked = $('input[name="view-material"]:checked');
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
				url:'/admin/remove_view_material',
				type:'post',
				dataType:'json',
				data:{uid:<?php echo $user['id']; ?>,mids:mids,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice('删除成功', 300);
						material_checked.parents('tr').remove();
					}else{
						notice(res.msg, 300);
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
			var material_checked = $('input[name="upload-material"]:checked');
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