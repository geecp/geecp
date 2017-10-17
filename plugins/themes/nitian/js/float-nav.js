// JavaScript Document

$(function(){
var time0;
var off = true;
$('.float-nav-list0').hover(function(){
	var dom0 = $(this);
	if(off == true){	
		if(!dom0.hasClass('hover0')){
			off = false;
			dom0.toggleClass('hover0');
			$('.float-nav-menu-list').stop().slideDown(0,function(){off = true});
		}
	}
},function(){
	var dom0 = $(this);
	if(off == true){	
		if(dom0.hasClass('hover0')){
			off = false;
			dom0.toggleClass('hover0');
			$('.float-nav-menu-list').stop().slideUp(0,function(){off = true});
		}
	}
});
$('.float-nav-menu-list-item').click(function(){
	$(this).toggleClass('float-nav-open');
	if($(this).hasClass('float-nav-open')){	
		$(this).find('.float-nav-menu-list-item-list').stop().slideDown(100);
	}else{
		$(this).find('.float-nav-menu-list-item-list').stop().slideUp(100);
	}
});
});
$(function(){

	var arr = [];
	var nav_fox = $('.float-nav-list');
		for(var i = 0;i<nav_fox.length;i++){
			nav_fox[i].index = i;
			nav_fox[i].ytop = $("div[id$='-getytop']:eq("+i+")").offset().top-40;
			arr.push(nav_fox[i].ytop);
		}
		var nav=$(".ky-sever-nav"); 
		var win=$(window); 
		var scr=$(document);
		var seize = $(".float-nav-seize");
		var eng1 = $("#eng1");
	    var eng2 = $("#eng2");
	    var allheight = $('body').height();
		win.scroll(function(){
			if(scr.scrollTop()>=467){
		  		nav.addClass("float-nav-fixednav"); 
				eng1.addClass("undis");
				eng2.removeClass("undis");
		  		seize.stop().fadeIn(0);
			}else{
				nav.removeClass("float-nav-fixednav");
				eng2.addClass("undis");
				eng1.removeClass("undis");
				seize.stop().fadeOut(0);
			}

			if(scr.scrollTop()<arr[0]){
				$('.float-nav-list').removeClass("float-nav-list-active");
			}else if(scr.scrollTop()>=arr[0] && scr.scrollTop()<arr[1]){
				$('.float-nav-list').removeClass("float-nav-list-active");
				$('.float-nav-list').eq(0).addClass("float-nav-list-active");
			}else if(scr.scrollTop()>=arr[1] && scr.scrollTop()<arr[2]){
				$('.float-nav-list').removeClass("float-nav-list-active");
				$('.float-nav-list').eq(1).addClass("float-nav-list-active");
			}else if(scr.scrollTop()>=arr[2] && scr.scrollTop()<arr[3]){
				$('.float-nav-list').removeClass("float-nav-list-active");
				$('.float-nav-list').eq(2).addClass("float-nav-list-active");
			}else if(scr.scrollTop()>=arr[3] && scr.scrollTop()<(arr[4]?arr[4]:allheight)){
				$('.float-nav-list').removeClass("float-nav-list-active");
				$('.float-nav-list').eq(3).addClass("float-nav-list-active");
			}else if(scr.scrollTop()>=arr[4] && scr.scrollTop() <= (arr[5]?arr[5]:allheight)){
				$('.float-nav-list').removeClass("float-nav-list-active");
				$('.float-nav-list').eq(4).addClass("float-nav-list-active");
			}else if(scr.scrollTop()>=arr[5] && scr.scrollTop() <= (arr[6]?arr[6]:allheight)){
				$('.float-nav-list').removeClass("float-nav-list-active");
				$('.float-nav-list').eq(5).addClass("float-nav-list-active");
			}
		});
	$('.float-nav-list').click(function(){
		var dom = $(this)[0];
		$("html,body").stop().animate({scrollTop:dom.ytop}, 500);
	});
		
});


