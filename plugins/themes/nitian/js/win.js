$(function(){
var timer;
var off = true;
$('.win-card').mousemove(function(){
	var dom = $(this);
	if(off == true){	
		if(!dom.hasClass('win-active')){
			off = false;
			dom.addClass('win-active').stop().animate({width:419,height:626,'top':-36},250,'swing').find('.win-card-title').stop().animate({height:185},250,'swing').parent().siblings().removeClass('win-active').stop().animate({width:258.8,height:546,'top':0},250,'swing').find('.win-card-title').stop().animate({height:170},250,'swing');
			$('.win-card').find('.win-body-details').stop().fadeOut(100).prev('.win-body-list').stop().delay(150).fadeIn(100);
			dom.find('.win-body-list').stop().fadeOut(100).next('.win-body-details').stop().delay(150).fadeIn(100,function(){off = true});	
		}
	}
})

})
