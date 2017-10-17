$(function() {
	$(".search-box a").click(function() {
		var i = $(this);
		$(this).toggleClass("active");
		i.next().find(".close").click(function() {
			i.removeClass("active");
			if(!$(this).hasClass("active")) {
				return false;
			}
		})
		if($(this).hasClass("on")) {
		}
	})
	$(".J-showMnavbar").click(function() {
		var len = $(".nav-list").find(".cus-mobile");
		console.log(len.length + ","+ len.eq(0).height()+15)
		$(this).toggleClass("on");
		if($(this).hasClass("on")) {
			$(this).parents(".nav-list").css("height", len.length * (len.eq(0).height()+11)+"px");
		} else{
			$(this).parents(".nav-list").css("height", "50px");
			
		}
	})
	
	//历史模块中li每隔一个增加灰色背景
	$(".history-list > li:odd, .fans-list > li:odd").addClass("gray");
	
})