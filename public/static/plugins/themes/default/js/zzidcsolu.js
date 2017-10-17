

$(function(){
	var solu_width = $('.solu-item-warp').width();
	var prev_btn = $('.solu-left');
	var next_btn = $('.solu-right');
	var solu_num = $('.solu-item-warp').length-1;
	var margin_left = (solu_num+1)/3*solu_width;
	//$('.solu-item-box').css('margin-left',-margin_left);
	$('.solu-item-box').css('margin-left',0);
	next_btn.click(function(){	 
		$('.solu-item-box').css('left',solu_width).append($('.solu-item-warp').first()).stop().animate({
			left:0
		}).find('.solu-item-active').removeClass('solu-item-active').next().addClass('solu-item-active');
		$('.solu-item-active').children().stop().animate({"height":380,"marginTop":0}).parent().prev().children().stop().animate({"height":340,"marginTop":20});
		$('.solu-item-active').find('.solu-item-details').stop().fadeIn(0).parent().parent().prev().find('.solu-item-details').stop().fadeOut(0);
	})
	prev_btn.click(function(){
		$('.solu-item-box').css('left',-solu_width).prepend($('.solu-item-warp').last()).stop().animate({
			left:0
		}).find('.solu-item-active').removeClass('solu-item-active').prev().addClass('solu-item-active');
		$('.solu-item-active').children().stop().animate({"height":380,"marginTop":0}).parent().next().children().stop().animate({"height":340,"marginTop":20});
		$('.solu-item-active').find('.solu-item-details').stop().fadeIn(0).parent().parent().next().find('.solu-item-details').stop().fadeOut(0);
	})

	$(window).resize(function(){
		var inWidth = $(this).innerWidth();
		var xx = -((1567-inWidth)/2) +'px';
		if(inWidth < 1200){
			$('.solu-body-child').width(1159);
			$('.solu-item-box').css('marginLeft',-180);
			$('.solu-left-right').width(1200).css('marginLeft',-600);
		}else if(inWidth < 1567 && inWidth >= 1200){
			$('.solu-body-child').width(inWidth - 41);
			$('.solu-item-box').css('marginLeft',xx);
			$('.solu-left-right').width(inWidth).css('marginLeft',-inWidth/2);
		}else if(inWidth >= 1567){
			$('.solu-body-child').width(1526);
			$('.solu-item-box').css('marginLeft',0);
			$('.solu-left-right').width(1567).css('marginLeft',-1567/2);
		}
	})
})

window.onload=function(){
	var inWidth = $(this).innerWidth();
		var xx = -((1567-inWidth)/2) +'px';
		// var xxx = -(1526 + xx) + 'px';
		if(inWidth < 1200){
			$('.solu-body-child').width(1159);
			$('.solu-item-box').css('marginLeft',-180);
			$('.solu-left-right').width(1200).css('marginLeft',-600);
		}else if(inWidth < 1567 && inWidth >= 1200){
			$('.solu-body-child').width(inWidth - 41);
			$('.solu-item-box').css('marginLeft',xx);
			$('.solu-left-right').width(inWidth).css('marginLeft',-inWidth/2);
		}else if(inWidth >= 1567){
			$('.solu-body-child').width(1526);
			$('.solu-item-box').css('marginLeft',0);
			$('.solu-left-right').width(1567).css('marginLeft',-1567/2);
	}
}
