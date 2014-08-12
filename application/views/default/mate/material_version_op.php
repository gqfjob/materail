<div class="container container-border">
	<div class="container-header"></div>
	<div class="material-box">
		<h2 class="bottom-line">上传新版本</h2>
		    <form id="material-version-op" class="form-horizontal" method="post" action="<?php echo base_url($action_url); ?>">
		    	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" autocomplete="off" />
		    	<input type="hidden" name="mid" value="<?php echo $material['id']; ?>" />
		    	<?php if( ! empty($version)) : ?>
		    	<input type="hidden" name="vid" value="<?php echo $version['id']; ?>" />
		    	<?php endif; ?>
		    	<div id="material-detail-box" class="filedset  bottom-line">
				    <div class="form-group">
					    <label class="col-sm-2 control-label" for="">素材类型</label>
					    <div class="col-xs-8 select-cate">
					    	<button class="btn btn-success" type="button" data-cate="<?php echo $material['id']; ?>"><?php echo $material['cname']; ?></button>
					    </div>
				    </div>
				    <div class="form-group">
					    <label class="col-sm-2 control-label" for="">素材名称</label>
					    <div class="col-xs-8">
					    	<span class="help-line"><?php echo $material['mname']; ?></span>
					    </div>
				    </div>
				    <div class="form-group">
					    <label class="col-sm-2 control-label" for="">缩略图</label>
					    <div id="thumb-box" class="col-xs-8">
					    	<div id="thumb-image">
					    		<img src="/<?php echo $material['logo']; ?>" />
					    	</div>
					    </div>
				    </div>
			    </div>
			    <div id="attachment-box"class="filedset bottom-line">
			    	<div id="up-down" class="up"></div>
			    	<br />
				    <div class="form-group">
				    	<label class="col-sm-2 control-label" for=""></label>
					    <div class="col-xs-8">
					    	<input type="hidden" name="attachment-ids" id ="attachment-ids" value="" autocomplete="off" />
					    	<div class="upload-container"><input id="upload-attachment" type="file" name="material-attachment"/></div>
					    	<span class="help-line">&nbsp;素材大小不超过200M</span>
					    	<div id="attachment-msg" class="alert alert-info msg-width">
					    		<span></span>
					   	 	</div>
					   	 	<ul class="unstyled attachment-list">
					   	 	<?php if( ! empty($version_attachment)) : ?>
						    <?php foreach($version_attachment as $value) : ?>
						    	<li><span data-id="<?php echo $value['id']?>" class="close pull-left">×</span><?php echo $value['sname'];?></li>
						    <?php endforeach;?>
						    <?php endif; ?>
						    </ul>
					    </div>
				    </div>
				</div>
				<div class="filedset">
					<div class="form-group">
					    <label class="col-sm-2 control-label" ><span class="color-red">*</span>版本描述</label>
					    <div class="col-xs-8">
					    	<input value="<?php echo (empty($version['depict'])) ? '' : $version['depict'];?>" type="text" class="form-control" id="version-depict" name="version-depict" placeholder="一句话描述一下该版本主要内容" autocomplete="off" />
					    </div>
				    </div>
				    <div class="form-group">
				    	<label class="col-sm-2 control-label" for="inputEmail">素材说明</label>
					    <div class="col-xs-8">
					    	<div id="myEditor"></div>
					    	<link href="<?php echo base_url('assets/js/ueditor/themes/default/css/umeditor.css');?>" media="screen" rel="stylesheet" type="text/css">
					    	<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/umeditor.config.js');?>"></script>
							<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/umeditor.min.js');?>"></script>
							<script type="text/javascript" charset="utf-8" src="<?php echo base_url('assets/js/ueditor/lang/zh-cn/zh-cn.js');?>"></script>
					    	<?php if(empty($version['content'])) : ?>
							<script type="text/javascript">
							        var editor = UM.getEditor('myEditor',{textarea:'version-content'});
							</script>
							<?php else:?>
							<script type="text/javascript">
							        var editor = UM.getEditor('myEditor',{textarea:'version-content', initialContent:'<?php echo $version['content'];?>'});
							</script>
							<?php endif;?>
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
	
	<div class="container-footer"></div>
</div>
<script type="text/javascript">
	$(function(){
		//上传附件
		$("#upload-attachment").uploadify({
			'formData'     : {
				'<?php echo $this->config->item('csrf_token_name'); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
				'cookie':'<?php echo json_encode(array($this->config->item('csrf_cookie_name') => $this->security->get_csrf_hash())); ?>'
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
			'fileTypeExts' : '',
			'fileTypeDesc' : '',
			'overrideEvents' : ['onSelectError'],
			'onSelectError' : function(file,errorCode,errorMsg){
				var settings = this.settings;
				switch(errorCode){
					case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
						$('#thumb-msg').show().find('span').text('<?php echo $this->lang->line('upload_queue_limit_exceeded');?>');
						break;
					case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
						$('#thumb-msg').show().find('span').text('<?php echo $this->lang->line('upload_invalid_filesize');?>');
						break;
					case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
						$('#thumb-msg').show().find('span').text('<?php echo $this->lang->line('upload_zero_byit_file');?>');
						break;
					case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
						$('#thumb-msg').show().find('span').text('<?php echo $this->lang->line('upload_stopped_by_extension');?>');
						break;
				}
				if (errorCode != SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
					delete this.queueData.files[file.id];
				}
			},
			'debug' : false,
			'onDialogClose' : function(){},
			'onUploadError' : function(){},
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
							$('#attachment-msg').removeClass('alert-info').addClass('alert-warning').find('span').text('删除失败');
						}
						
					},
					error:function(){

					}
				});
		    }
		}); 


		//显示控制
		$("#up-down").toggle(
		  function () {
		    $(this).removeClass('up').addClass('down');
		    $('#material-detail-box').show();
		  },
		  function () {
			  $(this).removeClass('down').addClass('up');
			  $('#material-detail-box').hide();
		  }
		);

		//表单提交
		$('#material-version-op').submit(function(){
			if(!$('#version-depict').val()){
				$('#form-msg').removeClass('alert-info').addClass('alert-warning').show().find('span').text('请填入版本描述');
				return false;
			}
			return true;

		});
	});
</script>