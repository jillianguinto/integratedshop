	
	jQuery(".promo_close").click(function (){
		jQuery(".promo_banner").fadeOut();
	});

	
	if (document.cookie.indexOf("ulah_popup_banner") >= 0) {
		  
		  jQuery(".promo_banner").hide();
		  
	}
	else {
		
		var now = new Date();
		var time = now.getTime();
		var value = "ulah_popup_banner";
		
		time += 3600 * 1000;
		now.setTime(time);
		document.cookie = 
		'username=' + value + 
		'; expires=' + now.toUTCString() + 
		'; path=/';	
		
		jQuery(".promo_banner").fadeIn();
	  
	}