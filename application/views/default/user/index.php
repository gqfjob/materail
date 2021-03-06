<style>
.container{
	width:500px;
	background:none;
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
                        	<input type="hidden" name="callback" value="<?php echo $callback;?>"/>
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


<script type="text/javascript">

$("#loginBtn").click(function(){
    if($.trim($("#inputEmail").val())==""){
        notice("请输入用户名",300,100);
        $("#inputEmail").focus();
        return false;
    }
    if($.trim($("#inputPassword").val())==""){
    	notice("请输入密码",300,100);
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
