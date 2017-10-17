$(function() {
	$('.page-container .main').css('min-height', $('body').height() - $('.page-container .page-header').height() - 105);
	$(window).resize(function() {
		$('.page-container .main').css('min-height', $('body').height() - $('.page-container .page-header').height() - 105);
	})
});