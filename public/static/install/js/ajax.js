$(function() {
	$.ajax({
		type: "get",
		url: "http://web.juhe.cn:8080/finance/exchange/rmbquot?key=7eff5404acc282f10c7ee4f24126b3f2",
		async: true,
		dataType: "jsonp",
		success: function(data) {
			
		},
		error: function() {
			alert('fuck');
		}
	});
})