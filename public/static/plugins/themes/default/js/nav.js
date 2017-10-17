$(function(){
if($('div').hasClass('vps')){		
		$('.regi').addClass('bg6');
		$('.hd').addClass('bd');
	}
$('.hd').hover(function(){
	if($('.regi').hasClass('bg6')){
		setTimeout(function(){
			if($('.hd').hasClass('hover')){
				$('.regi').removeClass('bg6');
				$('.hd').removeClass('bd');	
			}			
		},150)
	}
},function(){
	if($('div').hasClass('vps')){
		setTimeout(function(){
			if(!$('.hd').hasClass('hover')){
				$('.regi').addClass('bg6');
				$('.hd').addClass('bd');
			}			
		},350)
	}
})

	
if(!$('.hd-floor').hasClass('index-page')){
	$('.hd-floor').css({'display':'block','border':'none',height:64});
}

 
//控制导航页面背景
$(function(){
	var trigger =null;
	$(".hd").hover(function(){
		clearTimeout(trigger);
		trigger = setTimeout(function(){
			if(!$(".hd").hasClass("hover")){
				$(".hd").toggleClass("hover",true);
				if($('.hd-floor').hasClass('index-page')){
					$('.hd-floor').stop().slideDown(100);
				}
			}
		},100);
	},function(){
		// 如果快速划过，则终止之前的浮动事件
		clearTimeout(trigger);
		trigger =setTimeout(function(){
			if($(".hd").hasClass("hover")){
				$(".hd").toggleClass("hover",false);
				if($('.hd-floor').hasClass('index-page')){
					$('.hd-floor').stop().slideUp(100);
				}
			}
	     }, 300);
	});
})

});

$(function(){
	var time; 
	var leng = $('.nav>li').length;
	
$('.nav>li').hover(function(){
		var hoverNow=$(this);
	clearTimeout(time);
	time = setTimeout(function(){
		if(!hoverNow.hasClass('hover')){
			hoverNow.toggleClass('hover');
			var navlifirst = $('.nav>li:eq(0)');
			if(hoverNow.children().hasClass('nav2')){
				hoverNow.find('.nav2').stop().slideDown(100)
			}else{
				hoverNow.find('.nav-solu').stop().slideDown(80);
			}
		}
	},100);
},function(){
	clearTimeout(time);
	// time = setTimeout(function(){
		for(var i = 0;i<leng;i++){
			(function(n){
				if($('.nav>li').eq(n).hasClass('hover')){
					$('.nav>li').eq(n).toggleClass('hover');
					if($('.nav>li').eq(n).find('.nav2')){
						$('.nav>li').eq(n).find('.nav2').stop().slideUp(100);
					}
					$('.nav>li').eq(n).find('.nav-solu').stop().slideUp(80);
				}
			})(i)
		}
		
	// },0)
});
})


// 二级菜单显示
$(function(){
	var time;
	$('.pro>li>div:eq(0)').hover(function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if(!$('.pro>li>div:eq(0)').hasClass('hover') && !$('.pro>li>div:eq(0)').hasClass('pro-active')){
				$('.pro>li>div:eq(0)').toggleClass('hover').addClass('pro-active').parent('li').siblings('li').children('div').removeClass('pro-active');
				// $('.pro>li>div').removeClass('pro-active');
				// $('.pro>li>div:eq(0)').addClass('pro-active');
				$('.allpro').stop().fadeOut(0);
				$('.allpro:eq(0)').stop().fadeIn(0);
			}
		// },10);
	},function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if($('.pro>li>div:eq(0)').hasClass('hover')){
				$('.pro>li>div:eq(0)').toggleClass('hover');
			}
		// },10)
	})

$('.pro>li>div:eq(1)').hover(function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if(!$('.pro>li>div:eq(1)').hasClass('hover') && !$('.pro>li>div:eq(1)').hasClass('pro-active')){
				$('.pro>li>div:eq(1)').toggleClass('hover').addClass('pro-active').parent('li').siblings('li').children('div').removeClass('pro-active');
				// $('.pro>li>div').removeClass('pro-active');
				// $('.pro>li>div:eq(1)').addClass('pro-active');
				$('.allpro').stop().fadeOut(0);
				$('.allpro:eq(1)').stop().fadeIn(0);
			}
		// },10);
	},function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if($('.pro>li>div:eq(1)').hasClass('hover')){
				$('.pro>li>div:eq(1)').toggleClass('hover');
			}
		// },10)
	})

$('.pro>li>div:eq(2)').hover(function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if(!$('.pro>li>div:eq(2)').hasClass('hover') && !$('.pro>li>div:eq(2)').hasClass('pro-active')){
				$('.pro>li>div:eq(2)').toggleClass('hover').addClass('pro-active').parent('li').siblings('li').children('div').removeClass('pro-active');
				// $('.pro>li>div').removeClass('pro-active');
				// $('.pro>li>div:eq(2)').addClass('pro-active');
				$('.allpro').stop().fadeOut(0);
				$('.allpro:eq(2)').stop().fadeIn(0);
			}
		// },10);
	},function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if($('.pro>li>div:eq(2)').hasClass('hover')){
				$('.pro>li>div:eq(2)').toggleClass('hover');
			}
		// },10)
	})

$('.pro>li>div:eq(3)').hover(function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if(!$('.pro>li>div:eq(3)').hasClass('hover') && !$('.pro>li>div:eq(3)').hasClass('pro-active')){
				$('.pro>li>div:eq(3)').toggleClass('hover').addClass('pro-active').parent('li').siblings('li').children('div').removeClass('pro-active');
				// $('.pro>li>div').removeClass('pro-active');
				// $('.pro>li>div:eq(3)').addClass('pro-active');
				$('.allpro').stop().fadeOut(0);
				$('.allpro:eq(3)').stop().fadeIn(0);
			}
		// },10);
	},function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if($('.pro>li>div:eq(3)').hasClass('hover')){
				$('.pro>li>div:eq(3)').toggleClass('hover');
			}
		// },10)
	})

$('.pro>li>div:eq(4)').hover(function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if(!$('.pro>li>div:eq(4)').hasClass('hover') && !$('.pro>li>div:eq(4)').hasClass('pro-active')){
				$('.pro>li>div:eq(4)').toggleClass('hover').addClass('pro-active').parent('li').siblings('li').children('div').removeClass('pro-active');
				// $('.pro>li>div').removeClass('pro-active');
				// $('.pro>li>div:eq(4)').addClass('pro-active');
				$('.allpro').stop().fadeOut(0);
				$('.allpro:eq(4)').stop().fadeIn(0);
			}
		// },10);
	},function(){
		clearTimeout(time);
		// time = setTimeout(function(){
			if($('.pro>li>div:eq(4)').hasClass('hover')){
				$('.pro>li>div:eq(4)').toggleClass('hover');
			}
		// },10)
	})

$('.nav-arrow-up').click(function(){
		$('.nav2').stop().slideUp(100);
	})


})


