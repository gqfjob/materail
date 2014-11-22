<!-- Content starts -->
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		<!-- Page heading -->
        <div class="page-head">
        	<h2 class="pull-left"><i class="icon-list-alt"></i> 用户管理</h2>
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
      	 <form class="navbar-form navbar-right" role="form" method="get" action="<?php echo base_url('admin/mgUser');?>">
			  <div class="form-group">
			    <label class="sr-only" for="exampleInputEmail2">用户名</label>
			    <input type="text" autocomplete="off" class="form-control col-lg-9" id="search" name="search" value="<?php echo isset($search) ? $search : '';?>" placeholder="请输入用户名">
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
		                	<div class="pull-left">用户列表</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                    <table class="table table-striped table-bordered table-hover">
		                    	<thead>
			                        <tr>
			                        	<th width="50"><input type="checkbox" id="check-all" value="" autocomplete="off" /></th>
			                            <th width="400">姓名</th>
			                            <th>素材数</th>
			                            <th>登录时间</th>
			                        </tr>
		                      	</thead>
		                      <tbody>
		                         <?php if(empty($users)) : ?>
		                         <tr>
		                         	<td colspan="4" class="text-center"><strong>暂无用户</strong></td>
		                         </tr>
		                         <?php else: ?>       
		                         <?php foreach($users as $user) : ?>
		                         <tr>
		                         	<td><input autocomplete="off" type="checkbox" name="user" data-id="<?php echo $user['id'];?>" value="" /></td>
		                         	<td><a href="<?php echo base_url('admin/userDetail/1?uid=' . $user['id']);?>" target="_blank"><?php echo $user['realname']; ?></a></td>
		                         	<td><?php echo isset($user_material[$user['id']]['num']) ? $user_material[$user['id']]['num'] : 0;?></td>
		                         	<td><?php echo date('Y-m-d H:i:s', $user['last_login_time']);?></td>
		                         </tr>
		                         <?php endforeach;?>
		                         <?php endif;?>                                            
		                      </tbody>
		                    </table>
		
		                    <div class="widget-foot">
								<div class="pull-left" style="padding-top:10px">
									<button id="delete-user" type="button" class="btn btn-danger">删除</button>
									<button id="deny-user" data-status="0" type="button" class="btn btn-primary">禁止登录</button>
									<button id="allow-user" data-status="1" type="button" class="btn btn-success">允许登录</button>
								</div>
		                      	
		                       <?php echo empty($pages) ? '' : $pages; ?>
		                      
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
				$('input[name="user"]').prop('checked', true);
			}else{
				$('input[name="user"]').prop('checked', false);
			}
		});

		$('input[name="user"]').click(function(){
			if($('input[name="user"]').length == $('input[name="user"]:checked').length){
				$('#check-all').prop('checked', true);
			}else{
				$('#check-all').prop('checked', false);
			}
		});

		//禁止\允许登录
		$('#deny-user,#allow-user').click(function(){
			var _this = $(this);
			var user_checked = $('input[name="user"]:checked');
			if( ! user_checked.length){
				notice('请选择操作项', 300);
				return false;
			}
			var uids = new Array();
			user_checked.each(function(){
				uids.push(parseInt($(this).attr('data-id')));
			});
			uids = uids.join();
			var status = parseInt(_this.attr('data-status'));
			var text = (status) ? '启用' : '禁止';
			_this.addClass('disabled');
			$.ajax({
				url:'/admin/set_user_status',
				type:'post',
				dataType:'json',
				data:{uids:uids,status:status,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice(text + '成功', 300);
					}else{
						if(res.msg){
							notice(res.msg, 300);
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

		//删除用户
		$('#delete-user').click(function(){
			var _this = $(this);
			var user_checked = $('input[name="user"]:checked');
			if( ! user_checked.length){
				notice('请选择操作项', 300);
				return false;
			}
			var uids = new Array();
			user_checked.each(function(){
				uids.push(parseInt($(this).attr('data-id')));
			});
			uids = uids.join();
			_this.addClass('disabled');
			$.ajax({
				url:'/admin/delete_user',
				type:'post',
				dataType:'json',
				data:{uids:uids,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.removeClass('disabled');
					if(res.status){
						notice('删除成功', 300);
						user_checked.parents('tr').remove();
					}else{
						if(res.msg){
							notice(res.msg, 300);
						}else{
							notice('操作失败', 300);
						}
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