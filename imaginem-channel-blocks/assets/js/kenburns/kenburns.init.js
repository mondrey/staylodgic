jQuery(document).ready(function($){


	//Detect Orientaiton change
	window.onload = orientationchange;
	window.onorientationchange = orientationchange;
	jQuery(window).bind("resize", orientationchange);
	function orientationchange() {
		slideshowify();
	}

	var speedMinValue=12000;
	var speedMaxValue=18000;
	var randomizeValue=false;

	function slideshowify() {
		$('.slideshowifier').remove();
		if ($.fn.slideshowify) {
			jQuery('#kenburns-container img').slideshowify({
				randomize : randomizeValue, // default is false
				aniSpeedMin : speedMinValue, // shortest animation will be 8 seconds
				aniSpeedMax : speedMaxValue // longest animation will be 10 seconds
				});
		}
		
	}

	$(window).on( 'load', function() {
		if ($.fn.slideshowify) {
			jQuery('.kenburns-preloader').remove();
			jQuery('#kenburns-container img').slideshowify({
				randomize : randomizeValue, // default is false
				aniSpeedMin : speedMinValue, // shortest animation will be 8 seconds
				aniSpeedMax : speedMaxValue // longest animation will be 10 seconds
			});
		}
	});
	
});