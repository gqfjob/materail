<!-- Content starts -->
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		<!-- Page heading -->
        <div class="page-head">
        	<h2 class="pull-left"><i class="icon-bar-chart"></i> 访问日志</h2>
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
      	 <script type="text/javascript" src="<?php echo base_url('assets/js/My97DatePicker/WdatePicker.js'); ?>"></script>
      	 <form class="navbar-form navbar-right" role="form" method="get" action="<?php echo base_url('admin/mgVisitor');?>">
      	 	 <div class="checkbox">
			    <label>开始时间 </label>
			  </div>
			  <div class="form-group">
			    <label class="sr-only" for="exampleInputEmail2">开始时间</label>
			    <input type="text" autocomplete="off" class="form-control col-lg-9" readonly="readonly" id="start" name="start" value="<?php echo isset($start) ? $start : '';?>" placeholder="请选择开始时间">
			  </div>
			  <div class="checkbox">
			    <label><img onclick="WdatePicker({el:'start',dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'%y-%M-%d %H:%i:%s',startDate:'%y-%M-%d 00:00:00',alwaysUseStartDate:true,autoPickDate:true})" src="<?php echo base_url('assets/js/My97DatePicker/skin/datePicker.gif') ; ?>" width="16" height="22" align="absmiddle" title="点击选择开始时间"> </label>
			  </div>
			   <div class="checkbox">
			    <label>结束时间 </label>
			  </div>
			  <div class="form-group">
			    <label class="sr-only" for="exampleInputEmail2">结束时间</label>
			    <input type="text" autocomplete="off" class="form-control col-lg-9" readonly="readonly" id="end" name="end" value="<?php echo isset($end) ? $end : '';?>" placeholder="请选择结束时间">
			  </div>
			  <div class="checkbox">
			  	<label><img onclick="WdatePicker({el:'end',dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'%y-%M-%d %H:%i:%s',autoPickDate:true})" src="<?php echo base_url('assets/js/My97DatePicker/skin/datePicker.gif') ; ?>" width="16" height="22" align="absmiddle" title="点击选择结束时间"></label>
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
		                	<div class="pull-left">访问列表</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                    <table class="table table-striped table-bordered table-hover">
		                    	<thead>
			                        <tr>
			                        	<th width="100">用户</th>
			                            <th width="200">ip</th>
			                            <th width="200">时间</th>
			                            <th width="100">操作类型</th>
			                            <th>页面地址</th>
			                        </tr>
		                      	</thead>
		                      <tbody>
		                         <?php if(empty($lists)) : ?>
		                         <tr>
		                         	<td colspan="5" class="text-center"><strong>暂无访问记录</strong></td>
		                         </tr>
		                         <?php else: ?>       
		                         <?php foreach($lists as $list) : ?>
		                         <tr>
		                         	<td>
		                         	<?php if(empty($users[$list['uid']])) : ?>
		                         	<span>匿名</span>
		                         	<?php else : ?>
		                         	<a href="<?php echo base_url('admin/userDetail/1?uid=' . $list['uid']); ?>" target="_blank"><?php echo empty($users[$list['uid']]['nickname']) ? $users[$list['uid']]['realname'] : $users[$list['uid']]['realname']; ?></a>
		                         	<?php endif; ?>
		                         	</td>
		                         	<td><?php echo $list['ip']?></td>
		                         	<td><?php echo date('Y-m-d H:i:s', $list['time']); ?></td>
		                         	<td>
		                         		<?php 
		                         			switch($list['type'])
		                         			{
		                         				case 1:
		                         					echo '浏览';
		                         					break;
		                         				case 2:
		                         					echo '上传';
		                         					break;
		                         				case 3:
		                         					echo '下载';
		                         					break;
		                         				default :
		                         					echo '浏览';
		                         					break;
		                         			}
		                         		?>
		                         	</td>
		                         	<td><?php echo $list['curl']; ?></td>
		                         </tr>
		                         <?php endforeach;?>
		                         <?php endif;?>                                            
		                      </tbody>
		                    </table>
		
		                    <div class="widget-foot">
		                       <?php echo empty($pages) ? '&nbsp;' : $pages; ?>
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