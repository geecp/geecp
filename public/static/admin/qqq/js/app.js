$(function() {
	$('.page-container .main').css('min-height', $('body').height() - $('.page-container .page-header').height() - 105);
	$(window).resize(function() {
		$('.page-container .main').css('min-height', $('body').height() - $('.page-container .page-header').height() - 105);
	})
});

//mAlert 组件
function mAlert(c, a, b) {
	$('.mAlert').remove();
	var temp = '';
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
	if(b == undefined) {
		b = "";
	}
	temp += '<div class="modal mAlert fade' + mclass + '" id="alertModal" data-keyboard="false" tabindex="-1" role="dialog">';
	temp += '<div class="modal-dialog modal-sm"><div class="modal-content">';
	temp += '<div class="modal-body"><a class="close"></a><div class="media"><div class="media-left"><span class="iconfont ' + micon + '"></span></div>';
	temp += '<div class="media-body  media-middle"><h4>' + b + '</h4><p class="text-justify">' + a + '</p></div></div></div>';
	temp += '<div class="modal-footer"><p><a class="btn btn-default btn-sm pull-right" data-dismiss="modal">确定</a></p></div>';
	temp += '</div></div></div>';
	$('body').append(temp);
	$('#alertModal').modal({
		show: 'true',
		backdrop: 'static',
	});
}