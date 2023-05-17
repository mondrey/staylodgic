jQuery(document).ready(function($){
	
	var $wallContainer = $('#photowall-container');
	var $wallWrap = $('.photowall-wrap');

    //variables to confirm window height and width
    var wall_lastWindowHeight = $(window).height();
    var wall_lastWindowWidth = $(window).width();

    $(window).resize(function() {

        //confirm window was actually resized
        if($(window).height()!=wall_lastWindowHeight || $(window).width()!=wall_lastWindowWidth){

            //set this windows size
            wall_lastWindowHeight = $(window).height();
            wall_lastWindowWidth = $(window).width();

            //call my function
            photoWallInit();
            $('.photowall-item').each(function(){
            	$(this).removeClass('animation-standby animated fadeInUp animation-action');
            });

        }
    });

	photoWallInit();

	function photoWallInit() {
		// initialize isotope photowall-container
		if ($.fn.isotope) {

			$wallContainer.imagesLoaded( function() {

				var photow_window_width = $(window).width();
				var wallContainer_w = $("#photowall-container").width();

				number_of_columns = 3;
				$('.photowall-item').css('width','33.333333333333333333%');

				if (photow_window_width < 1200 ) {
					number_of_columns = 3;
					$('.photowall-item').css('width','33.333333333333333333%');
				}

				if (photow_window_width < 800 ) {
					number_of_columns = 2;
					$('.photowall-item').css('width','50%');
				}

				if (photow_window_width < 600 ) {
					number_of_columns = 1;
					$('.photowall-item').css('width','100%');
				}

				$wallContainer.isotope({
					layoutMode: 'masonry',
					resizable: false, // disable normal resizing
				  	masonry: {
				    	gutterWidth: 0,
				    	columnWidth: wallContainer_w / number_of_columns
					}
				});

                if ($($wallContainer).hasClass('relayout-on-image-load')) {
                    // refresh after each picture lazyloading
                    $wallContainer.each(function(){
                        var $curr_module = $(this);

                        var wallupdate = (function(){
                            $curr_module.isotope('layout');
                        });

                        this.addEventListener('load', wallupdate, true);   
                    });
                }

				if ($.fn.waypoint) {
					var i = 0;
					$('.photowall-item-presence').each(function(g) {
						var photowallcell = $(this);
				        $(this).waypoint(function() {
                            setTimeout(function() {
					        	photowallcell.removeClass('photowall-item-not-visible').addClass('photowall-item-is-visible photowall-item-displayed').removeClass('photowall-item-presence');
						        photowallcell.find('img')
					                .velocity(
					                	{
					                		opacity:1
					                	},
										{ 
										    duration: 250,
										});
                            }, 120 * g );
				        }, {
				            offset: '90%'
				        });
			        });
				}

			});
		}
	}

	$(window).on( 'load', function() {
	    setTimeout(function() {
	        photoWallInit();
	    }, 800 );
		
	});
	
});