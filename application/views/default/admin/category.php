<!-- Content starts -->
<style type="text/css">
#upload-clogo{float:left;margin-top:10px;}
#upload-clogo object{
	width:86px;
	height:30px;
	cursor: pointer;
}
#upload-clogo-queue{display:none;}
.uploadify-button {
    padding: 0 12px;
}
#clogo-image{
	float:left;
	padding:0 5px;
	margin-top:10px;
}
#clogo-image img{
	width:32px;
	height:32px;
}
</style>
<div class="content">
	<?php echo $bg_left;?>
	<!-- Main bar -->
	<div class="mainbar">
		<!-- Page heading -->
        <div class="page-head">
        	<h2 class="pull-left"><i class="icon-tasks"></i> 分类管理</h2>
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
      	 <form id="cate-form" class="navbar-form navbar-right" role="form" method="post" action="<?php echo base_url('admin/create_cate');?>">
			  <div class="form-group">
			    <label class="sr-only" for="exampleInputEmail2">分类名</label>
			    <input type="text" autocomplete="off" class="form-control col-lg-9" id="cate-name" name="cate-name" value="" placeholder="请输入分类名">
			  </div>
			  <div class="form-group">
			  	<button type="submit" class="btn btn-default">新增分类</button>
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
		                	<div class="pull-left">分类列表</div>
		                    <div class="clearfix"></div>
		                </div>
		                <div class="widget-content">
		                    <table class="table table-striped table-bordered table-hover">
		                    	<thead>
			                        <tr>
			                            <th>分类名</th>
			                            <th width="200">操作</th>
			                        </tr>
		                      	</thead>
		                      <tbody class="cate-box">
		                         <?php if(empty($cates)) : ?>
		                         <tr class="no-cate">
		                         	<td colspan="2" class="text-center"><strong>暂无分类</strong></td>
		                         </tr>
		                         <?php else: ?>       
		                         <?php foreach($cates as $cate) : ?>
		                         <tr>
		                         	<td class="show-cname"><?php echo $cate['cname'];?></td>
		                         	<td>
		                         		<?php if($cate['cname'] != '其他') : ?>
		                         		<a href="#" class="edit-cate" data-cid="<?php echo $cate['id']; ?>" data-name="<?php echo $cate['cname']; ?>" data-clogo="<?php echo $cate['clogo']; ?>">修改</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" class="delete-cate" data-cid="<?php echo $cate['id']; ?>">删除</a>
		                         		<?php endif;?>
		                         	</td>
		                         </tr>
		                         <?php endforeach;?>
		                         <?php endif;?>                                            
		                      </tbody>
		                    </table>
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
	<div class="edit-cate-box modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:450px">
	    	<div class="modal-content">
	      		<div class="modal-header">
			    	<button type="button" class="close cancel-edit" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			        <h4 class="modal-title" id="myModalLabel">修改分类</h4>
			    </div>
			    <div class="modal-body">
			        <div class="form-inline">
			        	<div class="checkbox">
							<label>分类名</label>
						</div>
			        	<div class="form-group">
						    <label class="sr-only" for="serch-user">分类名</label>
						    <input type="text" class="form-control col-xs-4" id="edit-cate-name" placeholder="请输入分类名">
						    <input type="hidden" value="" id="selected-material"  autocomplete="off" />
						</div>
					</div>
					<br />
					<div class="form-inline">
						<input type="hidden" id="cate-id" value="" autocomplete="off" />
						<input type="hidden" id="clogo-path" value="" autocomplete="off" />
						<div class="checkbox">
							<label>分类图片</label>
						</div>
						<div class="form-group">
							<div id="clogo-box">
								<input id="upload-clogo" type="file" name="clogo" />
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
			    </div>
			    <div class="modal-footer">
			    	<button type="button" class="btn btn-primary primary-edit">确定</button>
			    	<button type="button" class="btn btn-default cancel-edit" data-dismiss="modal">取消</button>
			    </div>
	    	</div>
	  </div>
	</div>
	<!-- End Modal -->
<!-- Content ends -->
<script type="text/javascript">
	$(function(){
		//新增分类
		$('#cate-form').submit(function(){
			var _this = $(this);
			var cate_name = $.trim($('#cate-name').val());
			if(cate_name.length == 0){
				notice('请输入分类名',300);
				return false;
			}
			_this.find('button[type="submit"]').addClass('disabled');
			$.ajax({
				url:_this.attr('action'),
				type:'post',
				dataType:'json',
				data:{cate_name:cate_name,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					_this.find('button[type="submit"]').removeClass('disabled');
					if(res.status){
						var html = '<tr>';
						    html += '<td>' + cate_name + '</td>';
						    html += '<td><a href="#" class="edit-cate" data-cid="' + res.cid + '" data-name="' + cate_name + '" data-clogo="">修改</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" class="delete-cate" data-cid="' + res.cid + '">删除</a></td>';
						    html += '</tr>';
						if($('.cate-box .no-cate').length){
							$('.cate-box .no-cate').remove();
						}
						$('.cate-box').append(html);
						
					}else{
						notice(res.msg,300);
					}
				} 
			});
			return false;
		});

		//修改分类
		$('.cate-box').on('click','.edit-cate',function(){
			var _this = $(this);
			var clogo = $.trim(_this.attr('data-clogo'));
			var cname = $.trim(_this.attr('data-name'));
			var cid = parseInt(_this.attr('data-cid'));
			if(clogo.length){
				$('#clogo-path').val(clogo);
				if($('#clogo-image').length){
					$('#clogo-image img').attr('src', '/' +clogo);
				}else{
					$('#clogo-box').prepend('<div id="clogo-image"><img src="/' + clogo + '" /></div>');
				}
			}else{
				$('#clogo-image').remove();
				$('#clogo-path').val('');
			}
			$('#edit-cate-name').val(cname);
			$('#cate-id').val(cid);
			$('#myModal').modal();
			return false;
		});
		$('.primary-edit').click(function(){
			var _this = $(this);
			var cid = $('#cate-id').val();
			var cname = $('#edit-cate-name').val();
			var clogo = $('#clogo-path').val();
			$.ajax({
				url:'/admin/edit_cate',
				type:'post',
				dataType:'json',
				data:{cid:cid,cname:cname,clogo:clogo,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(res){
					if(res.status){
						$('.edit-cate[data-cid="' + cid + '"]').attr({'data-name':cname,'data-clogo':clogo}).parents('tr').find('.show-cname').text(cname);
						$('.cancel-edit').click();
					}else{
						alert(res.msg);
					}
				}
			});
		});
		//删除分类
		$('.cate-box').on('click','.delete-cate',function(){
			if(confirm('确定删除此分类?')){
				var _this = $(this);
				var cid = _this.attr('data-cid');
				var del = false;
				$.ajax({
					url:'/admin/check_cate',
					type:'post',
					async:false,
					dataType:'json',
					data:{cid:cid,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
					success:function(res){
						if(res.status){
							if(res.has){
								if(confirm('该分类下存在素材，是否仍然删除',300)){
									del = true;
								}
							}else{
								del = true;
							}
						}else{
							notice(res.msg,300);
						}
					}
				});
				if(del){
					$.ajax({
						url:'/admin/delete_cate',
						type:'post',
						dataType:'json',
						data:{cid:cid,<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
						success:function(res){
							if(res.status){
								_this.parents('tr').remove();
							}else{
								notice(res.msg,300);
							}
						}
					});
				}
			}
			return false;
		});

		//上传分类图片
		$("#upload-clogo").uploadify({
			'formData'     : {
				'<?php echo $this->config->item('csrf_token_name'); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
				'cookie':'<?php echo json_encode(array($this->config->item('user_login_cookie') => get_cookie($this->config->item('user_login_cookie')), $this->config->item('csrf_cookie_name') => $this->security->get_csrf_hash())); ?>'
			},
			'fileObjName' : 'upload_file',
	        'swf'      : '/assets/js/uploadfy/uploadify.swf',
	        'uploader' : '/file/upload_clogo',
	        'buttonClass' : 'btn btn-info',
			'buttonText' : '上传缩略图',
			'width' : 'auto',
			'height' : 30,
			'multi'    : false,
			'progressData' : 'speed', 
			'fileSizeLimit' : '2MB',
			'fileTypeExts' : '*.jpg; *.png; *.gif',
			'fileTypeDesc' : '图片仅支持png、gif、jpg',
			'overrideEvents' : ['onSelectError'],
			'onSelectError' : function(file,errorCode,errorMsg){
				var settings = this.settings;
				switch(errorCode){
					case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
						$('#clogo-msg').show().find('span').text('<?php echo $lang->line('upload_queue_limit_exceeded');?>');
						break;
					case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
						$('#clogo-msg').show().find('span').text('<?php echo $lang->line('upload_invalid_filesize');?>');
						break;
					case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
						$('#clogo-msg').show().find('span').text('<?php echo $lang->line('upload_zero_byit_file');?>');
						break;
					case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
						$('#clogo-msg').show().find('span').text('<?php echo $lang->line('upload_stopped_by_extension');?>');
						break;
				}
				if (errorCode != SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
					delete this.queueData.files[file.id];
				}
			},
			'debug' : false,
			'onDialogClose' : function(){},
			'onUploadError' : function(){},
			'onUploadStart' : function(){},
			'onUploadSuccess' : function(file, data, response){
				data = eval('(' + data + ')');
				if(data.status){
					$('#clogo-path').val(data.result['file_path']);
					if($('#clogo-image').length){
						$('#clogo-image img').attr('src', '/' + data.result['file_path']);
					}else{
						$('#clogo-box').prepend('<div id="clogo-image"><img width="118" height="118" src="/' + data.result['file_path'] + '" /></div>');
					}
					$('#clogo-image').show();
				}else{
					alert('上传失败');
				}
				delete this.queueData.files[file.id];
			}
	    }); 
	});
</script>