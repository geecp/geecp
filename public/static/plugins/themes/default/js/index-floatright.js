$(function(){
var win=$(window); 
var scr=$(document);
win.scroll(function(){
  	if(scr.scrollTop() != 0){
	  	$('.fr-kong').eq(5).fadeIn('slow');
	}else{
		$('.fr-kong').eq(5).fadeOut('slow');
	}
})
$('.fr-kong:eq(1)').hover(function(){
	$('.hot-line-tb:eq(0)').fadeIn(0);
},function(){
	$('.hot-line-tb:eq(0)').fadeOut(0);
})

$('.fr-kong:eq(2)').hover(function(){
	$('.hot-line-tb:eq(1)').fadeIn(0);
},function(){
	$('.hot-line-tb:eq(1)').fadeOut(0);
})

$('.fr-kong:eq(3)').hover(function(){
	$('.fr-2wm-bg:eq(0)').fadeIn(0);
},function(){
	$('.fr-2wm-bg:eq(0)').fadeOut(0);
})

$('.fr-kong:eq(5)').click(function(){
	$("html,body").animate({scrollTop:0}, 500);
})

}) 