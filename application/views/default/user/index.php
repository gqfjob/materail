<style>
.container{
	width:500px;
}
#msgModal .modal-dialog{
	width:300px;
	height:100px;
}
</style>



<!-- Form area -->
<div class="admin-form">
  <div class="container">

    <div class="row">
      <div class="col-md-12">
        <!-- Widget starts -->
            <div class="widget worange">
              <!-- Widget head -->
              <div class="widget-head">
                <i class="icon-lock"></i> 登录 
              </div>

              <div class="widget-content">
                <div class="padd">
                  <!-- Login form -->
                  <form class="form-horizontal" id="newSession"  action="/user/loginDo" method="post">
                    <!-- Email -->
                    <div class="form-group">
                      <label class="control-label col-lg-3" for="inputEmail">用户名</label>
                      <div class="col-lg-9">
                        <input type="text" class="form-control" name="loginName" id="inputEmail" placeholder="邮箱或者昵称">
                      </div>
                    </div>
                    <!-- Password -->
                    <div class="form-group">
                      <label class="control-label col-lg-3" for="inputPassword">密码</label>
                      <div class="col-lg-9">
                        <input type="password" class="form-control" name="loginPwd" id="inputPassword" placeholder="Password">
                      </div>
                    </div>
                    <!-- Remember me checkbox and sign in button -->
                     
                    <div class="form-group">
                    <!--
					<div class="col-lg-9 col-lg-offset-3">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox"> Remember me
                        </label>
						</div>
					</div>
					 -->
					</div>
                        <div class="col-lg-9 col-lg-offset-2">
                        	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>"/>
                        	<input type="hidden" name="ajax" value="1"/>
                        	<?php if($callback):?>
                        	<input type="hidden" name="" value="<?php echo $callback;?>"/>
                        	<?php endif;?>
							<button id="loginBtn" class="btn btn-danger">登录</button>
							<button type="reset" class="btn btn-default">重置</button>
						</div>
                    <br />
                  </form>
				  
				</div>
                </div>
              
                <div class="widget-foot">
                  <!-- 没有用户名？ <a href="#">点击注册</a> -->
                </div>
            </div>  
      </div>
    </div>
  </div> 
</div>

<!-- Modal -->
<div class="modal fade" id="msgModal" tabindex="-1" role="dialog" aria-labelledby="msgModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="msgModalLabel">通知</h4>
      </div>
      <div class="modal-body" id="msg" style="text-align: center">
      ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function notice(msg){
	$("#msg").html(msg);
	$("#msgModal").modal("show");
}
$("#loginBtn").click(function(){
    if($.trim($("#inputEmail").val())==""){
        notice("请输入用户名");
        $("#inputEmail").focus();
        return false;
    }
    if($.trim($("#inputPassword").val())==""){
    	notice("请输入密码");
        $("#inputPassword").focus();
        return false;
    }
    var options = {
	    success: function (data) {
		    res = eval('('+data+')');
	    	   if(res.errno == 0){
	    		   location.href=decodeURIComponent(res.data);
	    	   }else{
	    		   notice(res.msg);
	    	   }
	    }
    };

    $("#newSession").ajaxSubmit(options);
    return false;
    
});
$(document).keydown(function(event){
    if(event.keyCode==13){
       $("#loginBtn").click();
       return false;
    }
});
</script>
