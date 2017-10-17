		function setView1(n){
				for(var i=1;i<=5;i++){
					if(i == n){
						$('#tab1_0'+i).removeClass("undis");
						$('#tab1_0'+i).addClass("dis");
						$('#tab1_'+i).removeClass("nonav");
						$('#tab1_'+i).addClass("nav_on6");
					}else{
						$('#tab1_'+i).removeClass("nav_on6");
						$('#tab1_'+i).addClass("nonav");
						$('#tab1_0'+i).removeClass("dis");
						$('#tab1_0'+i).addClass("undis");
					}
				}
			} 