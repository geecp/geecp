$(function() {
	//首页团队留言内容传递
	$(".show-note").click(function() {
		var html = $(this).prev().html();
		$("#note-modal").find(".modal-body").html(html);
	})
	var i = $("div[data-showbox]").find("input");
	i.click(function() {
		if(i.eq(0).is(":checked")) {
			$(this).parents("div[data-showbox]").find(".col-md-12").addClass("hidden");
		} else {
			$(this).parents("div[data-showbox]").find(".col-md-12").removeClass("hidden");
		}
	})
})
$(function() {
	$(".J-reqChecked").click(function() {
		if($(this).parents(".tab-pane").find("input[type='checkbox']").is(":checked")) {
			return true;
		} else {
			mAlert(2, '请选择要操作的用户！', '错误操作');
			return false;
		}
	})
})
$(function() {
	var i = $(".table-checked-color").find("input");
	i.click(function() {
		if($(this).is(":checked")) {
			$(this).parents("tr").addClass("bg-lblue");
		} else {
			$(this).parents("tr").removeClass("bg-lblue");
		}
	})
})
$(function() {
	$('[data-nice-radio="true"]').click(function() {
		niceRadioCheck($(this));
	})
	$('[data-nice-checkbox="1"]').click(function() {
		niceCheckboxCheck($(this));
	})
	$(".btn-show-next-active").click(function() {
		$(this).next().toggle();
	})
})
$(function() {
	var f = $('#login');
	f.find('[href="#reg"]').click(function() {
		$(this).parents('.row').css('margin-top', '-260px');
		console.log(1)
	})
	f.find('[href="#log"]').click(function() {
		$(this).parents('.row').css('margin-top', '-190px');
	})
})
$(function() {
	//公用全选checkbox  判断是否禁用
	$('#checkedAll-disabled').click(function() {
		if($(this).prop('checked') == 1) {
			$('input[name="apiAccredit"]').each(function() {
				if($(this).prop('disabled') != 1) {
					$(this).prop('checked', true);
				}
			})
		} else {
			$('input[name="apiAccredit"]').each(function() {
				if($(this).prop('disabled') != 1) {
					$(this).prop('checked', false);
				}
			})
		}
	})
})
$(function() {
	//编辑资料表单内容生成
	var i = j = '';
	$('.J-subitem').click(function() {
		i = $(this).parents('.additem-box').find('.form-control').eq(0);
		j = $(this).parents('.additem-box').find('.form-control').eq(1);
		if(i.val() == '' || j.val() == '') {
			mAlert(2, '请输入内容！', '表单错误');
			return false;
		}
		$(this).parents('.additem-box').next('.additem-list').find('ul').append('<li><span class="item-icon pull-left">搜</span><div class="text-max-hide pull-left"><span>' + i.val() + '</span> · <span>' + j.val() + '</span></div><a href="javascript:;" class=" pull-right" onclick="removeItemLi($(this))"><span class="glyphicon glyphicon-trash mr5"></span>删除</a></li>');
		$(this).parents('.item-hidden').addClass('hidden');
		$(this).parents('.item-hidden').prev().removeClass('hidden');
		i.val('');
		j.val('');

	})
	$('.J-subitem').next().click(function() {
		i = $(this).parents('.additem-box').find('.form-control').eq(0);
		j = $(this).parents('.additem-box').find('.form-control').eq(1);
		i.val('');
		j.val('');
	})
})

function removeItemLi(i) {
	i.parents('li').remove();
}

$(function() {
	$('.J-showHideBox a').click(function() {
		var f = $(this).parent();
		var i = j = l = '';
		if($(this).parents('.J-showHideBox').hasClass('edit')) {
			l = $(this).parents('.J-showHideBox');
			$('#verify').modal('show');
			$('#verify').find('[type="submit"]').click(function() {
				verify1 = ({
					beforeSend: function() {
						if($('#verify').find('[type="text"]').val() == '') {
							console.log(1)
							mAlert(2, '请输入验证码', '表单提交错误');
							return false;
						}
					},
					success: function(data) {
						if(data.status == 1) {
							l.next().removeClass('hidden');
							l.addClass('hidden');
							$('#verify').modal('hide');
						} else {
							mAlert(2, "error info : " + data.info, '表单提交失败');
						}
					}
				});
				$(".verify-pin").ajaxForm(verify1);

			})
		} else {
			$(this).parent().next().removeClass('hidden');
			$(this).parent().addClass('hidden');
			$(this).parent().next().find('.btn').click(function() {
				if($(this).hasClass('J-subitem')) {
					i = $(this).parents('.additem-box').find('.form-control').eq(0);
					j = $(this).parents('.additem-box').find('.form-control').eq(1);
					if(i.val() == '' || j.val() == '') {
						return false;
					}
				} else {
					$(this).parents('.item-hidden').addClass('hidden');
					$(this).parents('.item-hidden').prev().removeClass('hidden');
				}
				//以下是原设定
				//$(this).parents('.item-hidden').addClass('hidden');
				//$(this).parents('.item-hidden').prev().removeClass('hidden');
			})
		}

	})
})

var imgbase = '';
$(function() {
	var title,
		author,
		cover,
		abstract,
		contHtml,
		f;
	$(".J-preview").click(function() {
		if($("#addtitletext").val() == '') {
			mAlert(2, '请输入标题！', '预览失败');
			return false;
		} else if($("#addauthortext").val() == '') {
			mAlert(2, '请输入作者名字！', '预览失败');
			return false;
		} else if($("#addcovertext").val() == '') {
			mAlert(2, '请选择封面！', '预览失败');
			return false;
		} else if($("#addabstract").val() == '') {
			mAlert(2, '请输入摘要！', '预览失败');
			return false;
		} else if($(".note-editable").html() == '<p><br></p>') {
			mAlert(2, '请输入正文！', '预览失败');
			return false;
		} else {
			title = $("#addtitletext").val();
			author = $("#addauthortext").val();
			cover = imgbase;
			abstract = $("#addabstract").val();
			contHtml = $(".note-editable").html();
			//			cover = readFile($("#addcovertext"));
			f = $(".preview-box");
			f.find(".title").html(title);
			f.find(".cover").attr('src', cover);
			f.find(".abstract").html(abstract);
			f.find(".author").html(author);
			f.find(".preview-content").html(contHtml);
		}
	})

})

function readFile(obj) {
	console.log(obj)
	var file = obj.files[0];
	//判断类型是不是图片  
	if(!/image\/\w+/.test(file.type)) {
		alert("请确保文件为图像类型");
		return false;
	}
	var reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onload = function(e) {
		//		$(this).val(this.result);
		//		console.log(this.result); //就是base64  
		imgbase = this.result;
		return imgbase;
	}
}

function checkedAll(i) {
	var n = i.attr("name");
	i.click(function() {
		if($(this).is(":checked")) {
			$("input[name=" + n + "]").each(function() {
				$(this).prop("checked", true);
				$(this).parents("tr").addClass("bg-lblue");
			});
		} else {
			$("input[name=" + n + "]").each(function() {
				$(this).prop("checked", false);
				$(this).parents("tr").removeClass("bg-lblue");
			});
		}
	});
}

function niceRadioCheck(i) {
	//i为该元素  f为顶级父元素 t为类型 c为需要指定的容器  b为被指定的容器
	//a为判断是否为子元素选择 d为子元素被指定的容器
	var c, b, t, f, d, g, l;
	f = i.parents('[data-nice-radioFather="1"]');
	t = i.attr('data-nice-radioType');
	c = i.attr('data-nice-radioCon');
	b = f.find('[data-nice-radioBox]');
	d = f.find('div[data-nice-radioChiBox]').attr('data-nice-radioChiBox');
	console.log(i + ',' + t + ',' + c + ',' + b.attr('data-nice-radioBox') + ',' + f + ',' + d);
	if(b.attr('data-nice-radioBox') == c) {
		if(t == 1) {
			i.attr('checked', true);
			if(i.attr('checked')) {
				b.removeClass('hidden');
				i.attr('checked', true);
			} else {
				b.addClass('hidden');
				i.removeAttr('checked');
			}
		} else if(t == 3) {
			console.log(3)

			if(b.attr('data-nice-radioBox') == c) {
				console.log(3)
				b.find('input').attr('disabled', true);
			} else {
				b.find('input').removeAttr('disabled');
			}
		} else if(t == 4) {
			//						console.log(4);
			g = f.find('[data-nice-radioFourShow="1"]');
			if(b.attr('data-nice-radioBox') == c) {
				b.addClass('hidden');
				i.addClass('hidden');
				g.removeClass("hidden");
				g.find('[data-nice-radio="true"]').click(function() {
					b.removeClass('hidden');
					i.removeClass('hidden');
					g.addClass("hidden");
				})
			} else {
				console.log('error')
			}
		}
	} else if(b.attr('data-nice-radioBox') == c) {

	} else if(t == 3) {
		return true;
	} else if(t == 4) {
		return true;
	} else if(t == 5) {
		return true;
	} else {
		b.addClass('hidden');
	}
}

function niceCheckboxCheck(i) {
	var a;
	if(i.attr('data-nice-checkboxAll') == 1) {
		a = i.parents('div[data-nice-checkboxCon="1"]');
		i.parents('div').eq(0)
	} else {
		console.log("error");
	}
}