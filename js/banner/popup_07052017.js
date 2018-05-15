	
	// jQuery(".promo_close").click(function (){
		
	// 	jQuery(".promo_banner").fadeOut();
		
	// 	jQuery(".promo_banner2").fadeIn();
		
	// });
	
	// if (document.cookie.indexOf("ulah_popup_banner") >= 0) {
		  
	// 	  jQuery(".promo_banner").hide();
	// 	  jQuery(".promo_banner2").hide();
		  
	// }
	// else {
		
	// 	var now = new Date();
	// 	var time = now.getTime();
	// 	var value = "ulah_popup_banner";
		
	// 	time += 3600 * 1000;
	// 	now.setTime(time);
	// 	document.cookie = 
	// 	'username=' + value + 
	// 	'; expires=' + now.toUTCString() + 
	// 	'; path=/';	
		
	// 	jQuery(".promo_banner").fadeIn();
	  
	// }
		jQuery(".promo_close").click(function (){
		
		jQuery(".promo_banner").fadeOut();
		jQuery(".promo_banner").style.display = "none";
	
		
	});
	jQuery(".promo_close2").click(function (){
		
		jQuery(".promo_banner2").fadeOut();
		jQuery(".promo_banner2").style.display = "none";
		
	});
		jQuery(".promo_close3").click(function (){
		
		jQuery(".promo_banner3").fadeOut();
		jQuery(".promo_banner3").style.display = "none";
	});
		