function notice(msg,width,height){
	var str ="<div class=\"modal fade\" id=\"msgModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"msgModalLabel\" aria-hidden=\"true\">";
	str +="<div class=\"modal-dialog\" style=\"width:"+width+"px;height:"+height+"px\">";
	str +="<div class=\"modal-content\">";
	str +="<div class=\"modal-header\">";
	str +="<button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>";
	str +="<h4 class=\"modal-title\" id=\"msgModalLabel\">通知</h4>";
	str +="</div>";
	str +="<div class=\"modal-body\" id=\"msg\" style=\"text-align: center\">";
	str +="</div>";
	str +="<div class=\"modal-footer\">";
	str +="<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">关闭</button>";
	str +="</div>";
	str +="</div>";
	str +="</div>";
	str +="</div>";
	$('#msgModal').remove();
	$('body').append(str);
	$("#msg").html(msg);
	$("#msgModal").modal("show");
}