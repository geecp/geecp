// JavaScript Document

function setView5(n){
		for(var i=1;i<=5;i++){
			if(i == n){
				$('#tab5_0'+i).removeClass("undis");
				$('#tab5_0'+i).addClass("dis");
				$('#tab5_'+i).removeClass("nonav");
				$('#tab5_'+i).addClass("nav_on6");
			}else{
				$('#tab5_'+i).removeClass("nav_on6");
				$('#tab5_'+i).addClass("nonav");
				$('#tab5_0'+i).removeClass("dis");
				$('#tab5_0'+i).addClass("undis");
			}
		}
	} 



$(".xt-radio").click(function(){
	$(".xt-radio.checked").removeClass("checked");
	$(this).addClass("checked");
});

$(".os_btn_1").click(function() {
	$(".os_btn_2").removeClass('v-selected');
	$(".os_btn_1").addClass('v-selected');
	$("#czxt").val(1);
});
$(".os_btn_2").click(function() {
	$(".os_btn_1").removeClass('v-selected');
	$(".os_btn_2").addClass('v-selected');
	$("#czxt").val(0);
});





