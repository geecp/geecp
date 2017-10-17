jQuery.divselect = function(divselectid,inputselectid) {
	$(divselectid+" cite").click(function(){
		var ul = $(divselectid+" ul");
		if(ul.css("display")=="none"){
			ul.slideDown("fast");
		}else{
			ul.slideUp("fast");
		}
		
	});
	$(divselectid+" ul li a").click(function(){
		var txt = $(this).text();
		$(divselectid+" cite").html(txt);
		var value = $(this).attr("selectid");
		 $(divselectid+" ul").hide(); 
	});
	$(divselectid).mouseleave(function(){$(divselectid +" ul").slideUp("fast")});

	
};