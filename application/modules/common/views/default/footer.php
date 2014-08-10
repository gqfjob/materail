    <div class="clear"></div>
    </div>
</div>
<div class="footerwrap">
	<div class="footer">
		<a href="<?php echo base_url('about/guifan');?>" target="_blank">项目规范</a>
		<a href="<?php echo base_url('about/question');?>" target="_blank">常见问题</a>
		<a href="<?php echo base_url('about/us');?>" target="_blank">关于我们</a>
		
		<div id="backtop" class="backtop" style="display: block;">
			<a href="#top"></a>
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
<script>
$(window).ready(function(){

	$("#backtop").hide();
	$(window).scroll(function(){
        var src = 0;
        if(!(("undefined" == typeof index) || index=='' || index==null || index == false)){
        	scr = $(window).scrollTop();//滚动条距离顶端高度
            height = $("div.content").height();//获取内容高
            //alert(scr);
        }
	    if ($(window).scrollTop()>100){
	        $("#backtop").fadeIn(1000);
	    }
	    else
	    {
	        $("#backtop").fadeOut(1000);
	    }
	});

    
	$.get('/welcome/tongji',{'ur':'<?php echo (isset($_SERVER["HTTP_REFERER"]))?urlencode($_SERVER['HTTP_REFERER']):"";?>','t':encodeURI(document.title)},function(data){});
    //取用户信息
    $.post('/user/information',{'<?php echo $this->security->get_csrf_token_name();?>':'<?php echo $this->security->get_csrf_hash();?>'},function(data){
		$("#userInfo").html(data.msg);
    },'json');

});
//当点击跳转链接后，回到页面顶部位置
$("#backtop").click(function(){
    $('body,html').animate({scrollTop:0},500);
    return false;
});

</script>
</body>
</html>