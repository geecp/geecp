function contentinfo(b,c,d) {
	var a = $('[data-toggle="continfo"]');
	a.find('.content').before('<div class="info-box">' + a.find('p.info').text() + '</div>');
	if(a.data('imgmix')) {
		var temp = '<div class="swiper-container continfo"><div class="swiper-wrapper"><div class="swiper-pagination"></div></div></div>';
		var btn = '<div class="swiper-button-next"></div><div class="swiper-button-prev"></div>';
		var slider = '';
		var caption='';
		a.find('.content img').each(function() {
			$(this).remove();
			slider += '<div class="swiper-slide"><img src="' + $(this).attr('src') + '" alt="" /><div class="caption"><h3>'+$(this).attr('title')+'</h3><p>'+$(this).attr('alt')+'</p></div></div>';
		});
		a.find('.content').before(temp);
		$('.swiper-wrapper').append(slider);
		if(a.data('spctl')) {
			$('.swiper-container').append(btn);
		}
	}
	var swiper = new Swiper('.swiper-container', {
		pagination: '.swiper-pagination',
		paginationClickable: true,
		paginationType: 'progress',
		loop: true,
		autoHeight: true,
		nextButton: '.swiper-button-next',
		prevButton: '.swiper-button-prev',
	});
}
$('[data-toggle="dropdown"]').click(function() {
	var a = $(this).css('background-color');
	$(this).next().find('li>a').hover(function() {
		$(this).css({
			'background-color': a,
			'color': 'white'
		});
	}, function() {
		$(this).css({
			'background-color': 'inherit',
			'color': 'inherit'
		});
	})
	if($(this).data('sel') && $(this).prev().size() == 0) {
		$(this).next().find('a').click(function() {
			var a = $(this).text();
			var temp = '<span class="caret"></span>';
			$(this).parents('.dropdown-menu').prev().html(a + temp);
		})
	} else if($(this).data('sel') && $(this).prev().size() != 0) {
		$(this).next().find('a').click(function() {
			var a = $(this).text();
			$(this).parents('.dropdown-menu').prev().prev().text(a);
		})
	}
})
$('input[type="file"]').on('change', function() {
	$this = $(this);
	var size = $this.data('size');
	var formData = new FormData();
	var a = document.querySelector('#' + $(this).attr('id')).files;
	var msg = '';
	var b = a.length;
	var temp = '<div class="col-xs-4"></div>'
	$.each(a, function(i, v) {
		formData.append(v.name, v);
		if(v.size > size * 1024) {
			mAlert(2, '您所上传的图片' + v.name + '大于规定的尺寸' + size + 'kb', '警告');
			return false;
		} else {
			if(a.length > 1) {
				msg = "共有" + a.length + "个文件被上传！";
			} else {
				msg = v.name
			}
		}
	})
	$(this).parents('.input-group').find('.form-control').val(msg);
});
$('[data-toggle="edites"]').on('click', ".input-group-btn .bold", function() {
	if($(this).hasClass('btn-info')) {
		$(this).removeClass('btn-info')
		$(this).parent().next('.form-control').css('font-weight', '');
	} else {
		$(this).addClass('btn-info');
		$(this).parent().next('.form-control').css('font-weight', 'bolder');
	}
})
$('[data-toggle="edites"]').on('click', ".input-group-btn .italic", function() {
	if($(this).hasClass('btn-info')) {
		$(this).removeClass('btn-info')
		$(this).parent().next('.form-control').css('font-style', '');
	} else {
		$(this).addClass('btn-info');
		$(this).parent().next('.form-control').css('font-style', 'italic');
	}
})

function mAlert(c, a, b, u) {
	$('.modal').remove();
	var m = '',
		temp = '';
	var mclass, micon;
	var $md = $('#alertModal');
	if(c == 1) {
		mclass = ' warning';
		micon = 'icon-war';
	} else if(c == 2) {
		mclass = ' danger';
		micon = 'icon-erro';
	} else if(c == 3) {
		mclass = ' success';
		micon = 'icon-suc';
	} else if(c == undefined || c == 0) {
		micon = 'icon-info'
	};
	temp += '<div class="modal fade' + mclass + '" id="alertModal" data-keyboard="false" tabindex="-1" role="dialog">';
	temp += '<div class="modal-dialog modal-sm"><div class="modal-content">';
	temp += '<div class="modal-body"><a class="close"></a><div class="media"><div class="media-left"><span class="iconplus ' + micon + '"></span></div>';
	temp += '<div class="media-body  media-middle"><h4>' + b + '</h4><p class="text-justify">' + a + '</p></div></div></div>';
	temp += '<div class="modal-footer"><p><a class="btn btn-default btn-sm pull-right" data-dismiss="modal">确定</a></p></div>';
	temp += '</div></div></div>';
	$('body').append(temp);
	$('#alertModal').modal({
		show: 'true',
		backdrop: 'static',
	});
	if(u != undefined) {
		$('#alertModal').on('hidden.bs.modal', u)
	}
}