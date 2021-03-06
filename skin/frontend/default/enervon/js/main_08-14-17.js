/*
@author: leandro talavera
Dated: 7-26-2017

==================
developers Note:
place your function inside of .read/.load
ready() is fired when the DOM is ready for interaction.
load() is called when all assets are done loading, including images.

js beautifier = http://jsbeautifier.org/ (kind maintain psr2 indention thanks! - leandro)
==================

File Log(delete/replace the logs if you done some changes thnks! - leandro):

note: 
this were just a copied js from content.phtml - leandro(7-26-17).
variable baseurl were declared in content phtml - leandro(7-26-17).
*/
jQuery(window).load(function() {
	
	//Gets the highest div (generic-name-box) height and set it as height for all div (dbox)
    var maxHeight = -1;
    $('.generic-name-box').each(function() {
		maxHeight = maxHeight > $(this).outerHeight() ? maxHeight : $(this).outerHeight();
	});

	$('.dbox').each(function() {
		$(this).outerHeight(maxHeight);
	});

    //Adjust .cms-homepage .shopbycategory-content when slider content is <= 5
    var slider_content_cnt = jQuery(".counter").val();

    if(slider_content_cnt < 6){
        jQuery('.gallery__item .item-name-price').attr('style','margin-top: -5px;');
        jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: 130px;');
        jQuery(window).resize(function(){
            
            var width_ = jQuery(window).width();
            
            if(width_ > 1024){
                jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: 130px;');
            }else if(width_ < 1024 && width_ > 1000){
                jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: 130px;');
            } else if(width_ <= 768){
                jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: -100px;');
            } else if(width_ <= 640){
                jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: -100px;');
            } else if(width_ <= 480){
                jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: -100px;');
            } else if(width_ <= 320){
                jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: -100px;');
            }else {
                jQuery('.cms-homepage .shopbycategory-content').attr('style','margin-top: 130px;');
            }

        });

    }else{
        // jQuery('.gallery__item .item-name-price').attr('style','margin-top: -20px;');
    }
	
	jQuery.noConflict();

	function setcheckoutLocation(location)
	{
		jQuery.ajax({
			type:"GET",
			url:location,
			success:function(data){
			 //window.location.href = "<?php echo Mage::getBaseUrl()?>checkout/cart/";
			}
		});
	}

	//-------------------------------------------------------------------------------------------------------------
	var countClass = jQuery('.gallery__item').length;
    var count = '';
    var windowSize = jQuery(window).width();
    if(windowSize >= 623){
        count = '4';
    }else
    if(windowSize <= 622 && windowSize >= 463){
        count = '3';
    }else
    if(windowSize <= 462 && windowSize >=383){
        count = '2';
    }else{
        count = '1';
    }
     
    // Set general variables
    // ====================================================================
    var totalWidth = 0;

    // Total width is calculated by looping through each gallery item and
    // adding up each width and storing that in `totalWidth`
    jQuery(".gallery__item").each(function(){
        totalWidth = totalWidth + jQuery(this).outerWidth(true);
      
    });

    // The maxScrollPosition is the furthest point the items should
    // ever scroll to. We always want the viewport to be full of images.
    var maxScrollPosition = totalWidth - jQuery(".gallery-wrap-").outerWidth(); //gallery-wrap

    // This is the core function that animates to the target item
    // ====================================================================
    function toGalleryItem(jQuerytargetItem,count){

        // Make sure the target item exists, otherwise do nothing
        if ( count >= countClass){
            jQuery('.gallery__controls-next').prop('disabled',true);
            jQuery('.gallery__controls-prev').prop('disabled',false);
        }else if( count < countClass){
            jQuery('.gallery__controls-next').prop('disabled',false);
        }else if (count == count){
            jQuery('.gallery__controls-prev').prop('disabled',true);
        }
        
        if(jQuery('.counter').val() == count){
            jQuery('.gallery__controls-prev').prop('disabled',true);
            jQuery('.gallery__controls-next').prop('disabled',false);
        }else{
            jQuery('.gallery__controls-prev').prop('disabled',false);
        }
            jQuery('.counter').val(count);

            if(jQuerytargetItem.length){
            // The new position is just to the left of the targetItem
            var newPosition = jQuerytargetItem.position().left;

            // If the new position isnt greater than the maximum width
            if(newPosition <= maxScrollPosition){

                // Add active class to the target item
                jQuerytargetItem.addClass("gallery__item--active");

                // Remove the Active class from all other items
                jQuerytargetItem.siblings().removeClass("gallery__item--active");

                // Animate .gallery element to the correct left position.
                jQuery(".gallery").animate({
                    left : - newPosition
                });
            } else {
                // Animate .gallery element to the correct left position.
                jQuery(".gallery").animate({
                    left : - maxScrollPosition
                });
            };
        };

    };

    // Basic HTML manipulation
    // ====================================================================
    // Set the gallery width to the totalWidth. This allows all items to
    // be on one line.
    jQuery(".gallery").width(totalWidth);

    // Add active class to the first gallery item
    jQuery(".gallery__item:first").addClass("gallery__item--active");

    // When the prev button is clicked
    // ====================================================================
    jQuery(".gallery__controls-prev").click(function(){
        // Set target item to the item before the active item
        var jQuerytargetItem = jQuery(".gallery__item--active").prev();
        count--;
        toGalleryItem(jQuerytargetItem,count);       
    });

    // When the next button is clicked
    // ====================================================================
    

    jQuery(".gallery__controls-next").click(function(){
        // Set target item to the item after the active item
        var jQuerytargetItem = jQuery(".gallery__item--active").next();
        count++;
        toGalleryItem(jQuerytargetItem,count);
        
    });
	
	//-------------------------------------------------------------------------------------------------------------
	
	jQuery('#loading-icon').hide();
    

    jQuery('.gallery__img:first').css({'border-left':'initial'});

    var countDiv = 0;

    jQuery(".gallery").find(".gallery__item").each(function(){      

        countDiv++;     

    });

    if(countDiv > 4){

        jQuery('.gallery__controls').show();       
    
    }else if(countDiv == 0){

        jQuery('.category-variants').hide();

    }else{

        jQuery('.gallery__controls').hide();
    }
	
    //-------------------------------------------------------------------------------------------------------------

	var owl = $('.owl-carousel');
	  owl.owlCarousel({
		loop: false,
		rewind: true,
		nav: true,
		margin: 10,
		responsive: {
			320: {
			items: 1
		  },
		 375: {
			items: 2
		  },
		  414: {
			items: 2
		  },
		  480: {
			items: 2
		  },
		  600: {
			items: 3
		  },
		  960: {
			items: 5
		  },
		  1200: {
			items: 5
		  }
		}
	  });
  owl.on('mousewheel', '.owl-stage', function(e) {
    if (e.deltaY > 0) {
      owl.trigger('next.owl');
    } else {
      owl.trigger('prev.owl');
    }
    e.preventDefault();
  });
  
  jQuery( document ).ready(function() {
        jQuery('.owl-prev').html('');
        jQuery('.owl-next').html('');

    
    });
});