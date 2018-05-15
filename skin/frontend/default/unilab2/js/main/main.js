jQuery(window).load(function() {
	var countClass = jQuery('.gallery__item').length;
	var count = '';
	var windowSize = jQuery(window).width();
	if (windowSize >= 623) {
		count = '4';
	} else
	if (windowSize <= 622 && windowSize >= 463) {
		count = '3';
	} else
	if (windowSize <= 462 && windowSize >= 383) {
		count = '2';
	} else {
		count = '1';
	}
	jQuery('.owl-prev').html('');
	jQuery('.owl-next').html('');
	var totalWidth = 0;
	jQuery(".gallery__item").each(function() {
		totalWidth = totalWidth + jQuery(this).outerWidth(true);
	});
	var maxScrollPosition = totalWidth - jQuery(".gallery-wrap-").outerWidth();

	function toGalleryItem(jQuerytargetItem, count) {
		if (count >= countClass) {
			jQuery('.gallery__controls-next').prop('disabled', true);
			jQuery('.gallery__controls-prev').prop('disabled', false);
		} else if (count < countClass) {
			jQuery('.gallery__controls-next').prop('disabled', false);
		} else if (count == count) {
			jQuery('.gallery__controls-prev').prop('disabled', true);
		}
		if (jQuery('.counter').val() == count) {
			jQuery('.gallery__controls-prev').prop('disabled', true);
			jQuery('.gallery__controls-next').prop('disabled', false);
		} else {
			jQuery('.gallery__controls-prev').prop('disabled', false);
		}
		jQuery('.counter').val(count);
		if (jQuerytargetItem.length) {
			var newPosition = jQuerytargetItem.position().left;
			if (newPosition <= maxScrollPosition) {
				jQuerytargetItem.addClass("gallery__item--active");
				jQuerytargetItem.siblings().removeClass("gallery__item--active");
				jQuery(".gallery").animate({
					left: -newPosition
				});
			} else {
				jQuery(".gallery").animate({
					left: -maxScrollPosition
				});
			};
		};
	};
	jQuery(".gallery").width(totalWidth - 200);
	jQuery(".gallery__item:first").addClass("gallery__item--active");
	jQuery(".gallery__controls-prev").click(function() {
		var jQuerytargetItem = jQuery(".gallery__item--active").prev();
		count--;
		toGalleryItem(jQuerytargetItem, count);
	});
	jQuery(".gallery__controls-next").click(function() {
		var jQuerytargetItem = jQuery(".gallery__item--active").next();
		count++;
		toGalleryItem(jQuerytargetItem, count);
	});
});
jQuery(document).ready(function() {
	jQuery.noConflict();
	jQuery('#loading-icon').hide();
	jQuery('.gallery__img:first').css({
		'border-left': 'initial'
	});
	var countDiv = 0;
	jQuery(".gallery").find(".gallery__item").each(function() {
		countDiv++;
	});
	if (countDiv > 4) {
		jQuery('.gallery__controls').show();
	} else if (countDiv == 0) {
		jQuery('.category-variants').hide();
	} else {
		jQuery('.gallery__controls').hide();
	}
	jQuery('.owl-carousel').removeAttr("style");
	var owl = jQuery('.owl-carousel').owlCarousel({
		loop: false,
		rewind: true,
		nav: true,
		autoWidth: false,
		// margin:10,
		responsive: {
			320: {
				items: 1
			},
			375: {
				items: 1
			},
			414: {
				items: 2
			},
			480: {
				items: 2
			},
			640: {
				items: 3
			},
			960: {
				items: 4
			},
			1024: {
				items: 4,
			},
			1200: {
				items: 5,
			},
			1280: {
				items: 5,
			},
		}
	});
	jQuery('.owl-carousel').on('mousewheel', '.owl-stage', function(e) {
		if (e.deltaY > 0) {
			jQuery('.owl-carousel').trigger('next.owl');
		} else {
			jQuery('.owl-carousel').trigger('prev.owl');
		}
		e.preventDefault();
	});
	jQuery('.owl-carousel').removeAttr("style");

	function setcheckoutLocation(location) {
		jQuery.ajax({
			type: "GET",
			url: location,
			success: function(data) {}
		});
	}
	console.log(owl);
});