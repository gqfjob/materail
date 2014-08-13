<div class="container container-border">
	<div class="container-header"></div>
	<div class="material-box">
		<h2 class="bottom-line">上传素材</h2>
		    <form id="material-add" class="form-horizontal" method="post" action="<?php echo base_url('material/material_action_add'); ?>">
		    	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" autocomplete="off" />
		    	<div class="filedset bottom-line">
				    <div class="form-group">
					    <label class="col-sm-2 control-label" for=""><span class="color-red">*</span>素材类型</label>
					    <div class="col-xs-8 select-cate">
					    	<?php foreach($material_cate as $value) : ?>
					    	<button class="btn btn-default" type="button" data-cate="<?php echo $value['id']; ?>"><?php echo $value['cname']; ?></button>
					    	<?php endforeach;?>
					    	<input type="hidden" name="material-cate" id="material-cate" value="" autocomplete="off" />
					    </div>
				    </div>
				    <div class="form-group">
					    <label class="col-sm-2 control-label" for=""><span class="color-red">*</span>素材名称</label>
					    <div class="col-xs-8">
					    	<input type="text" class="form-control" id="material-name" name="material-name" placeholder="请填入素材名称" autocomplete="off"/>
					    </div>
				    </div>
				    <div class="form-group">
					    <label class="col-sm-2 control-label" for="">缩略图</label>
					    <div id="thumb-box" class="col-xs-8">
					    	<input type="hidden" id="thumb-path" name="thumb-path" autocomplete="off" />
					    	<input type="hidden" id="thumb-type" name="thumb-type" value="1" autocomplete="off" />
					    	<button class="btn btn-default btn-success" id="select-thumb" type="button">系统自动生成缩略图</button>
					    	<span class="help-line">或</span>
					    	<div class="upload-container"><input id="upload-thumb" type="file" name="material-thumb"/></div>
					    	<span class="help-line">图片支持png、gif、jpg，大小不超过2M</span>
					    	<div id="thumb-msg" class="alert alert-info msg-width">
					    		<span></span>
					   	 	</div>
					    </div>
				    </div>
				     <div class="form-group">
					    <label class="col-sm-2 control-label" for="inputPassword"><span class="color-red">*</span>版本描述</label>
					    <div class="col-xs-8">
					    	<input type="text" class="form-control" id="version-depict" name="version-depict" placeholder="一句话描述一下该版本主要内容" autocomplete="off" />
					    </div>
				    </div>
				     <div class="form-group">
					    <label class="col-sm-2 control-label" for="inputEmail">权限</label>
					    <div id="select-permission" class="col-xs-8 ">
					    	<button class="btn btn-default" data-type="1" type="button" >匿名可下载</button>
					    	<button class="btn btn-default btn-success" data-type="2" type="button" >登录用户可下载</button>
					    	<button class="btn btn-default" data-type="3" type="button" >指定用户可下载</button>
					    	<input type="hidden" name="permission" id="permission" value="2" autocomplete="off" />
					    	<input type="hidden" name="permission-user" id="permission-user" value="1,2" autocomplete="off" />
					    	<ul></ul>
					    </div>
					    
				    </div>
			    </div>
			    <div class="filedset bottom-line">
				    <div class="form-group">
				    	 <label class="col-sm-2 control-label" for=""></label>
					    <div class="col-xs-8">
					    	<input type="hidden" name="attachment-ids" id ="attachment-ids" value="" autocomplete="off" />
					    	<div class="upload-container"><input id="upload-attachment" type="file" name="material-attachment"/></div>
					    	<span class="help-line">&nbsp;素材大小不超过200M,格式支持doc,txt,ppt,zip,jpg,gif,png,ico</span>
					    	<div id="attachment-msg" class="alert alert-info msg-width">
					    		<span></span>
					   	 	</div>
				    	    <ul class="unstyled attachment-list">
				    	    	
						    </ul>
					    </div>
				    </div>
				</div>
				<div class="filedset">
				    <div class="form-group">
				    	<label class="col-sm-2 control-label" for="inputEmail">素材说明</label>
					    <div class="col-xs-8">
					    	<div id="myEditor"></div>
					    	<link href="<?php echo base_url('assets/js/ueditor/themes/default/css/umeditor.css');?>" media="screen" rel="stylesheet" type="text/css">
					    	<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/umeditor.config.js');?>"></script>
							<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/umeditor.min.js');?>"></script>
							<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/lang/zh-cn/zh-cn.js');?>"></script>
							<script type="text/javascript">
							        var editor = UM.getEditor('myEditor',{textarea:'version-content'});
							</script>
					    </div>
				    </div>
				</div>
			    <div class="form-actions">
				    <button type="submit" class="btn btn-success">保存</button>
				    <a href="<?php echo base_url();?>" class="btn btn-default">取消</a>
				    
				    <div id="form-msg" class="alert alert-info msg-width">
			    		<span></span>
			   	 	</div>
				</div>
		    </form>
	</div>
	<!-- Modal -->
	<div class="select-user-box modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
	    	<div class="modal-content">
	      		<div class="modal-header">
			    	<button type="button" class="close cancel-select" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			        <h4 class="modal-title" id="myModalLabel">指定用户</h4>
			    </div>
			    <div class="modal-body">
			        <div class="form-inline">
			        	<div class="checkbox">
						    <label>
						    	查找用户
						    </label>
					 	</div>
			        	<div class="form-group">
						    <label class="sr-only" for="serch-user">查找用户</label>
						    <input type="text" class="form-control col-xs-4" id="search-user" placeholder="请输入用户名称">
						</div>
					</div>
					<ul id="search-user-result" class="typeahead dropdown-menu">
					</ul>
					<div class="clearfix"></div>
					<div id="select-user-result">
						<input id="selected-users" type="hidden" value="" autocomplete="off" />	
						<div class="select-list-text">已选择</div>
						<div class="select-list">
						  	
						</div>
					</div>
			    </div>
			    <div class="modal-footer">
			    	<button type="button" class="btn btn-primary primary-select" data-dismiss="modal">确定</button>
			    	<button type="button" class="btn btn-default cancel-select" data-dismiss="modal">取消</button>
			    </div>
	    	</div>
	  </div>
	</div>
	<!-- End Modal -->
	<div class="container-footer"></div>
</div>
<script type="text/javascript">
	$(function(){
		//上传缩略图
		$("#upload-thumb").uploadify({
			'formData'     : {
				'<?php echo $this->config->item('csrf_token_name'); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
				'cookie':'<?php echo json_encode(array($this->config->item('user_login_cookie') => get_cookie($this->config->item('user_login_cookie')), $this->config->item('csrf_cookie_name') => $this->security->get_csrf_hash())); ?>'
			},
			'fileObjName' : 'upload_file',
	        'swf'      : '/assets/js/uploadfy/uploadify.swf',
	        'uploader' : '/file/upload_thumb',
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
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_queue_limit_exceeded');?>');
						break;
					case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_invalid_filesize');?>');
						break;
					case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_zero_byit_file');?>');
						break;
					case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_stopped_by_extension');?>');
						break;
				}
				if (errorCode != SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
					delete this.queueData.files[file.id];
				}
			},
			'debug' : false,
			'onDialogClose' : function(){},
			'onUploadError' : function(){$('#thumb-msg').removeClass('alert-info').addClass('alert-warning').find('span').text('上传失败');},
			'onUploadStart' : function(){$('#thumb-msg').removeClass('alert-warning').addClass('alert-info').show().find('span').text('文件上传中...');},
			'onUploadSuccess' : function(file, data, response){
				data = eval('(' + data + ')');
				if(data.status){
					$('#thumb-path').val(data.result['file_path']);
					$('#thumb-msg').hide().find('span').text('');
					if($('#thumb-image').length){
						$('#thumb-image img').attr('src', '/' + data.result['file_path']);
					}else{
						$('#thumb-box').prepend('<div id="thumb-image"><img width="118" height="118" src="/' + data.result['file_path'] + '" /></div>');
					}
					$('#select-thumb').removeClass('btn-success');
					$('#thumb-type').val('');
					$('#thumb-image').show();
				}else{
					$('#thumb-msg').removeClass('alert-info').addClass('alert-warning').find('span').text('上传失败');
				}
				delete this.queueData.files[file.id];
			}
	    }); 

		//上传附件
		$("#upload-attachment").uploadify({
			'formData'     : {
				'<?php echo $this->config->item('csrf_token_name'); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
				'cookie':'<?php echo json_encode(array($this->config->item('user_login_cookie') => get_cookie($this->config->item('user_login_cookie')), $this->config->item('csrf_cookie_name') => $this->security->get_csrf_hash())); ?>'
			},
			'fileObjName' : 'upload_file',
	        'swf'      : '/assets/js/uploadfy/uploadify.swf',
	        'uploader' : '/file/upload_attachment',
	        'buttonClass' : 'btn btn-info',
			'buttonText' : '上传素材',
			'width' : 'auto',
			'height' : 30,
			'multi'    : false,
			'progressData' : 'speed', 
			'fileSizeLimit' : '200MB',
			'fileTypeExts' : '*.doc;*.txt;*.ppt;*.zip;*.jpg;*.gif;*.png;*.ico',
			'fileTypeDesc' : '格式仅支持doc,txt,ppt,zip,jpg,gif,png,ico',
			'overrideEvents' : ['onSelectError'],
			'onSelectError' : function(file,errorCode,errorMsg){
				var settings = this.settings;
				switch(errorCode){
					case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_queue_limit_exceeded');?>');
						break;
					case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_invalid_filesize');?>');
						break;
					case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_zero_byit_file');?>');
						break;
					case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
						$('#thumb-msg').show().find('span').text('<?php echo $lang->line('upload_stopped_by_extension');?>');
						break;
				}
				if (errorCode != SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
					delete this.queueData.files[file.id];
				}
			},
			'debug' : false,
			'onDialogClose' : function(){},
			'onUploadError' : function(){$('#attachment-msg').removeClass('alert-info').addClass('alert-warning').find('span').text('上传失败');},
			'onUploadStart' : function(){$('#attachment-msg').removeClass('alert-warning').addClass('alert-info').show().find('span').text('文件上传中...');},
			'onUploadSuccess' : function(file, data, response){
				data = eval("(" + data + ")");
				if(data.status){
					$('#attachment-msg').hide().find('span').text('');
					var attachment_ids = $.trim($('#attachment-ids').val());
					attachment_ids += (attachment_ids.length) ? (',' + data.result['attachment_id']) : data.result['attachment_id'];
					$('#attachment-ids').val(attachment_ids);
					var attachmet_html = '<li><span class="close pull-left" data-type="new" data-id="' + data.result['attachment_id'] + '">&times;</span>' + data.result['attachment_name'] + '</li>';
					$('.attachment-list').prepend(attachmet_html);
				}else{
					$('#attachment-msg').removeClass('alert-info').addClass('alert-warning').find('span').text('上传失败');
				}
				delete this.queueData.files[file.id];
			}
	    });

	    //删除附件
	    $('.attachment-list').on('click', '.close', function(){
		    if(confirm('确定删除此文件吗？')){
			    var _this = $(this);
				$.ajax({
					url : '/file/delete_attachment',
					type : 'post',
					dataType : 'json',
					data : {attachment_id:_this.attr('data-id'),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
					beforeSend : function(){
						$('#attachment-msg').removeClass('alert-warning').addClass('alert-info').show().find('span').text('正在删除文件...');
					},
					success : function(res){
						var attachment_ids = new Array();
						if(res.status){
							_this.parent('li').remove();
							$('#attachment-msg').hide().find('span').text('');
							$('.attachment-list').find('.close[data-type="new"]').each(function(){
								attachment_ids.push($(this).attr('data-id'));
							})
							if(attachment_ids.length){
								$('#attachment-ids').val(attachment_ids.join());
							}else{
								$('#attachment-ids').val('');
							}
						}else{
							$('#attachment-msg').removeClass('alert-info').addClass('alert-warning').find('span').text(res.msg);
						}
						
					},
					error:function(){

					}
				});
		    }
		}); 

		//选择素材分类
		$('.select-cate .btn').click(function(){
			$(this).addClass('btn-success').siblings('.btn').removeClass('btn-success');
			var cate_id = parseInt($(this).attr('data-cate'));
			$('#material-cate').val(cate_id);
		});

		//选择自动缩略图
		$('#select-thumb').click(function(){
			var thumb_type = $('#thumb-type').val();
			if(thumb_type){
				$(this).removeClass('btn-success');
				$('#thumb-type').val('');
				$('#thumb-image').show();
			}else{
				$(this).addClass('btn-success');
				$('#thumb-type').val(1);
				$('#thumb-image').hide();
			}
		});

		//选择权限
		var selected_user_html = '';
		$('#select-permission .btn').click(function(){
			$(this).addClass('btn-success').siblings('.btn').removeClass('btn-success');
			var type = parseInt($(this).attr('data-type'));
			if(type == 3){
				$('#myModal').addClass('select-user-box').modal();
				$('#search-user').val('');
				selected_user_html = $('#select-user-result .select-list').html();
			}
			$('#permission').val(type);
		});

		//指定用户
		$('body').on('keyup','#search-user',function(){
			var _this = $(this);
			$.ajax({
				url:'/material/search_user',
				type:'post',
				data:{name:$.trim(_this.val()),<?php echo $this->config->item('csrf_token_name'); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
				dataType:'json',
				success:function(res){
					if(res.status){
						var li_html = '';
						for(var i = 0 ; i < res.result.length; i++){
							li_html += '<li><a href="#" data-id="' + res.result[i]['id'] + '" data-name="' + res.result[i]['realname'] + '" >' + res.result[i]['realname'] + '</a></li>'
						}
						if(li_html.length){
							$('#search-user-result').show().html(li_html);
						}else{
							$('#search-user-result').hide().html('');
						}
					}
				}
			});
		});
		$('body').on('click','#search-user-result li a', function(){
			var _this = $(this);
			var selected_users = $('#selected-users').val();
			if(selected_users.length){
				var arr_users = selected_users.split(',');
				for(i = 0; i < arr_users.length; i++){
					if(arr_users[i] == _this.attr('data-id')){
						alert('此用户已选择');
						return false;
					}
				}
				arr_users.push(_this.attr('data-id'));
				$('#selected-users').val(arr_users.join());
			}else{
				$('#selected-users').val(_this.attr('data-id'));
			}
			$('#select-user-result .select-list').prepend('<div class="select-list-item">' + _this.attr('data-name') + '<span class="close" data-id="' + _this.attr('data-id') + '">&times;</span></div>');
			$('#search-user-result').hide().html('');
			
		});
		
		$('body').on('click','#select-user-result .close', function(){
			var _this = $(this);
			var selected_users = $('#selected-users').val();
			if(selected_users.length){
				var arr_users = selected_users.split(',');
				for(i = 0; i < arr_users.length; i++){
					if(arr_users[i] == _this.attr('data-id')){
						arr_users.splice(i,1);
					}
				}
				$('#selected-users').val(arr_users.join());
			}
			_this.parents('.select-list-item').remove();
		});
		$('.cancel-select').click(function(){
			var selected_users = $('#permission-user').val();
			$('#search-user-result').hide().html('');
			$('#selected-users').val(selected_users);
			$('#select-user-result .select-list').html(selected_user_html);
		});
		$('.primary-select').click(function(){
			$('#permission-user').val($('#selected-users').val());
		});
		//表单提交
		$('#material-add').submit(function(){
			if(!$('#material-cate').val()){
				$('#form-msg').removeClass('alert-info').addClass('alert-warning').show().find('span').text('请选择素材类型');
				return false;
			}

			if(!$('#material-name').val()){
				$('#form-msg').removeClass('alert-info').addClass('alert-warning').show().find('span').text('请填入素材名称');
				return false;
			}

			if(!$('#version-depict').val()){
				$('#form-msg').removeClass('alert-info').addClass('alert-warning').show().find('span').text('请填入版本描述');
				return false;
			}
			return true;
		});
	});
</script>