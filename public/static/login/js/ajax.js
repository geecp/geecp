$(function() {
	$.ajax({
		type: "get",
		url: "http://static.gensee.com/webcast/static/sdk/flash/GenseeEasyLive.swf?160426",
		async: true,
		dataType: "jsonp",
		success: function(data) {
			
		},
		error: function() {
			alert('fuck');
		}
	});
})