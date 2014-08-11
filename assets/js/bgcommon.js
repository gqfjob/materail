/* Navigation */

$(document).ready(function() {

	$(window).resize(function() {
		if ($(window).width() >= 765) {
			$(".sidebar #nav").slideDown(350);
		} else {
			$(".sidebar #nav").slideUp(350);
		}
	});

	$("#nav > li > a").on('click', function(e) {
		if ($(this).parent().hasClass("has_sub")) {
			e.preventDefault();
		}

		if (!$(this).hasClass("subdrop")) {
			// hide any open menus and remove all other classes
			$("#nav li ul").slideUp(350);
			$("#nav li a").removeClass("subdrop");

			// open our new menu and add the open class
			$(this).next("ul").slideDown(350);
			$(this).addClass("subdrop");
		}

		else if ($(this).hasClass("subdrop")) {
			$(this).removeClass("subdrop");
			$(this).next("ul").slideUp(350);
		}

	});

	$(".sidebar-dropdown a").on('click', function(e) {
		e.preventDefault();

		if (!$(this).hasClass("open")) {
			// hide any open menus and remove all other classes
			$(".sidebar #nav").slideUp(350);
			$(".sidebar-dropdown a").removeClass("open");

			// open our new menu and add the open class
			$(".sidebar #nav").slideDown(350);
			$(this).addClass("open");
		}

		else if ($(this).hasClass("open")) {
			$(this).removeClass("open");
			$(".sidebar #nav").slideUp(350);
		}
	});

	/* Scroll to Top */
	$(".totop").hide();

	$(window).scroll(function() {
		if ($(this).scrollTop() > 300) {
			$('.totop').slideDown();
		} else {
			$('.totop').slideUp();
		}
	});

	$('.totop a').click(function(e) {
		e.preventDefault();
		$('body,html').animate({
			scrollTop : 0
		}, 500);
	});

});
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