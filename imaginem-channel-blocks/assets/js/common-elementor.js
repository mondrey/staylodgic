( function( $ ) {

	var mobx;
	var lightgalleryTransition = mtheme_vars.lightbox_transition;
	var lightgalleryThumbnails = mtheme_vars.lightbox_thumbnails;
	if (lightgalleryThumbnails=="false") {
		lightgalleryThumbnails = false;
	} else {
		lightgalleryThumbnails = true;
	}
	function lightgallery_activate( thumbnailSelector ) {
		
		// if not in elementor edit mode
		if ( ! $("body").hasClass("elementor-editor-active") ) {
			
			var gridblock_lightbox = $(".lightgallery-container");
			if (typeof ModuloBox != 'undefined') {
				
				mobx = new ModuloBox({
					mediaSelector      : '.lightgallery-container .isotope-displayed .lightbox-active,.lightgallery-detect-container .lightbox-active,.post-format-media .lightbox-image,.sidebar-widget .lightbox-image,.swiper-wrapper .lightbox-image,.lightgallery-status-true .lightbox-active,.vertical-image-wrap .lightbox-active,.gridblock-slideshow-element .lightbox-active',
					loop               : 3,
					history            : false,
					controls           : ['zoom', 'play', 'fullScreen', 'download', 'close'],
					timeToIdle         : 0,
					shareButtons       : false,
					videoMaxWidth      : 1440,
					minZoom            : 1,
					zoomTo             : 1.8,
					mouseWheel         : false,
					contextMenu        : false,
					scrollToZoom       : true,
					captionSmallDevice : true,
					thumbnails         : lightgalleryThumbnails,
					videoThumbnail     : true,
					videoAutoPlay      : true,
					thumbnailsNav      : 'centered',
					thumbnailSizes     : {
						1920 : {
							width  : 110,
							height : 80,
							gutter : 10
						},
						1280 : {
							width  : 90,
							height : 65,
							gutter : 10
						},
						680 : {
							width  : 0,
							height : 0,
							gutter : 0
						}
					}
				});
				mobx.on( 'beforeOpen.modulobox', function( gallery, index ) {

					if ( window.cubeRAF ) {

						cancelAnimationFrame(window.cubeRAF);
						window.cubeRAF = false;

					}

				});

				mobx.on( 'afterClose.modulobox', function( gallery, index ) {

					if ( typeof Event === 'function' && !window.cubeRAF ) {
						window.dispatchEvent( new Event( 'scroll' ) );
					}

				});

				var image_id;
				mobx.on( 'updateMedia.modulobox', function( gallery, index ) {

					image_id = $('.lightbox-like-holder').data('lightboxlike');
					// console.log('votegot' + image_id);
					var voted = false;

					//var proofing_item = $('.mtheme-proofing-choice[data-image_id="'+ image_id +'"]').find('.proofing-icon-status');
					var proofing_item = $('#mtheme-proofing-item-'+ image_id).find('.proofing-icon-status');
					
					if ( proofing_item.hasClass('ion-ios-heart') ) {
						voted = true;
					}

					if ( voted ) {
						$('.lightbox-like-holder').find('.proofing-lightbox-like').removeClass('ion-ios-heart-outline').addClass('ion-ios-heart');
					} else {
						$('.lightbox-like-holder').find('.proofing-lightbox-like').removeClass('ion-ios-heart').addClass('ion-ios-heart-outline');
					}
			
			});

			$(document).on('click', ".proofing-lightbox-like.proofing-lightbox-clickready", function(event) {	
					$('#mtheme-proofing-item-'+ image_id).find('.mtheme-proofing-choice.mtheme-proofing-active').trigger( 'click' );
					// console.log('voter' + image_id);
					event.stopImmediatePropagation();
				});

				mobx.init();
			}
		}

	}

	function masonryIsotope( container ) {
		var currContainer = container;
		var photow_window_width = $('.container').width();
		if (photow_window_width === null) {
			photow_window_width = $('.container-edge-to-edge').width();
		}
		var wallContainer_w = $(currContainer).width() - 0.5;

		number_of_columns = $(currContainer).attr('data-columns');

		var fivecolumn = '20%',fourcolumn = '25%',threecolumn = '33.3333%',twocolumn = '50%',onecolumn = '100%';

		if ($(currContainer).hasClass('thumnails-gutter-active')) {
			fivecolumn = '20%';
			fourcolumn = '25%';
			threecolumn = '33.3333%';
			twocolumn = '50%';
			onecolumn = '100%';
			wallContainer_w = $(currContainer).width() - 0.5;
		}
		if (number_of_columns == 5) {
			$(currContainer).find('.gridblock-element').css('width', fivecolumn);
		}
		if (number_of_columns == 4) {
			$(currContainer).find('.gridblock-element').css('width', fourcolumn);
		}
		if (number_of_columns == 3) {
			$(currContainer).find('.gridblock-element').css('width', threecolumn);
		}
		if (number_of_columns == 2) {
			$(currContainer).find('.gridblock-element').css('width', twocolumn);
		}
		if (number_of_columns == 1) {
			$(currContainer).find('.gridblock-element').css('width', onecolumn);
		}

		if (photow_window_width < 1035) {
			if ( number_of_columns > 3 ) {
				number_of_columns = 3;
				$(currContainer).find('.gridblock-element').css('width', threecolumn);
			}
		}
		if (photow_window_width < 800) {
			if ( number_of_columns > 2 ) {
				number_of_columns = 2;
				$(currContainer).find('.gridblock-element').css('width', twocolumn);
			}
		}
		if (photow_window_width < 500) {
			number_of_columns = 1;
			$(currContainer).find('.gridblock-element').css('width', onecolumn);
		}

		if ($('body.rtl').length == 1) {
				currContainer.isotope({
					isOriginLeft: false,
					resizable: false, // disable normal resizing
					masonry: {
						gutterWidth: 0,
						columnWidth: wallContainer_w / number_of_columns
					}
				});
		} else {
				currContainer.isotope({
					resizable: false, // disable normal resizing
					masonry: {
						gutterWidth: 0,
						columnWidth: wallContainer_w / number_of_columns
					}
				});
		}
	}

	function isotopeInit( container ) {
		var currContainer = container;
		// initialize isotope
		if ($.fn.isotope) {

				currContainer.imagesLoaded( function() {

					currContainer.parent().addClass('isotope-container-displayed');

					var itemReveal = Isotope.Item.prototype.reveal;
					Isotope.Item.prototype.reveal = function() {
						itemReveal.apply(this, arguments);
						$(this.element).removeClass('isotope-hidden');
						$(this.element).addClass('isotope-displayed');
					};

					var itemHide = Isotope.Item.prototype.hide;
					Isotope.Item.prototype.hide = function() {
						itemHide.apply(this, arguments);
						$(this.element).addClass('isotope-hidden');
						$(this.element).removeClass('isotope-displayed');
					};

					if ( $(currContainer).hasClass('gridblock-masonary') ) {

						masonryIsotope( currContainer );

					} else {
						if ($('body.rtl').length == 1) {
								currContainer.isotope({
									isOriginLeft: false,
									layoutMode: 'fitRows',
									transitionDuration: '0.8s',
									masonry: {
										gutterWidth: 0
									}
								});
						} else {
							
								currContainer.isotope({
									layoutMode: 'fitRows',
									transitionDuration: '0.5s',
									stagger: 20,
									hiddenStyle: {
										opacity: 0,
										transform: 'scale(0.9)'
									},
									visibleStyle: {
										opacity: 1,
										transform: 'scale(1)'
									},
									masonry: {
										gutterWidth: 0
									}
								});
						}
					}

					if ($(currContainer).hasClass('relayout-on-image-load')) {
						// refresh after each picture lazyloading
						currContainer.each(function(){
							var $curr_module = $(this);

							var layoutupdate = (function(){
								$curr_module.isotope('layout');
							});

							this.addEventListener('load', layoutupdate, true);   
						});
					}
				});



				var isotopeIsFiltering = false;
				var isotopeParentWrap = currContainer.parents( '.isotope-grid-outer-wrap' );
				var gridFilters = isotopeParentWrap.find( '#gridblock-filters a ');

				gridFilters.first().addClass('is-active');

				function isotopeFilter( gridFilters, selector, currContainer ) {
					currContainer.isotope({
						filter: selector
					});
		
					if ($.fn.isotope) {
						if ( isotopeIsFiltering ) {
							currContainer.imagesLoaded( function() {
								currContainer.one( 'arrangeComplete', function() {
									if ( $( '.lightgallery-container' )[0] ) {
										if ( ! $( 'body' ).hasClass( 'elementor-editor-active' ) ) {
											if (typeof ModuloBox != 'undefined') {
												mobx.destroy();
												mobx.init();
												console.log('arrange done, just this one time');
											}
										}
										isotopeIsFiltering = false;
									}
								});
							});
						}
					}
		
					gridFilters.removeClass('is-active');
					if ( selector !== '*' ) {
						isotopeParentWrap.find( '#gridblock-filters '+selector+ ' a' ).addClass( 'is-active' );
					}
				}
		
				function isotopeClicks( gridFilters, currContainer ) {
					// filter items when filter link is clicked
					gridFilters.click(function() {
		
						isotopeIsFiltering = true;
						$( '.gridblock-element' ).removeClass( 'animated animation-standby-portfolio animation-action' );
						$( '.gridblock-element' ).removeClass( 'grid-animate-display-all' );
		
						var selector = $(this).attr('data-filter');
						isotopeFilter( gridFilters, selector , currContainer );
						
						return false;
					});
				}
				isotopeClicks( gridFilters, currContainer );

				$(window).on( 'debouncedresize', function( event ) {
						
						if ( $(currContainer).hasClass('gridblock-masonary') ) {

							masonryIsotope( currContainer );
	
						} else {
							currContainer.isotope().isotope('layout');
						}
				});
				
		}

		var ajaxLoading = 0;
		var SlideStarted = false;

		//variables to confirm window height and width
		var lastWindowHeight = $(window).height();
		var lastWindowWidth = $(window).width();

		$(window).resize(function() {

			$('.thumbnails-grid-container').each(function() {
				$(this).find('.gridblock-element').removeClass('animation-action animated flipInX');
				$(this).find('.gridblock-element').removeClass('grid-animate-display-all');
			});
			$('.animation-standby-portfolio').removeClass('animation-standby-portfolio').addClass('animation-action');
			$('.gridblock-element').removeClass('animated animation-standby-portfolio animation-action');

		});

	}
	document.addEventListener( 'DOMContentLoaded', function() {
		lightgallery_activate();
	});

	/**
	 * @param $scope The Widget wrapper element as a jQuery element
	 * @param $ The jQuery alias
	 */ 
	var WidgetImaginemBlocksHandler = function( $scope, $ ) {


		// cache container
		var $filterContainer = $('#gridblock-container,#gridblock-container-blog,.thumbnails-grid-container');
		var AjaxPortfolio;
		var portfolio_height;
		var portfolio_width;
		var half_width;
		var image_height;
		var slideshow_active;
		var AutoStart;
		var ajax_image_height;
		var ajax_window_height;
		var $data;

		if ( elementorFrontend.isEditMode() ) {
			elementor.channels.editor.on('change',function( view ) {
				var changed = view.container.settings.changed;

				if ( changed.carousel_height ) {
					if ( $scope.find( '.elementor-widget-slideshow-carousel' ).length ) {
						var slideshow = $scope.find( '.flickity-carousel-detect' );
						slideshow.flickity('resize');
					}
				}
			});
			elementor.channels.editor.on( 'change', function( newValue ) {
				// For isotope layout
				//var changed = view.elementSettingsModel.changed;
				if ( $scope.find( '.elementor-widget-portfolio-grid' ).length ) {
					$(window).trigger('resize');
				}
				if ( $scope.find( '.elementor-widget-events-grid' ).length ) {
					$(window).trigger('resize');
				}
				if ( $scope.find( '.elementor-widget-thumbnails-grid' ).length ) {
					$(window).trigger('resize');
				}
				if ( $scope.find( '.elementor-widget-proofing-grid' ).length ) {
					$(window).trigger('resize');
				}
				if ( $scope.find( '.elementor-widget-worktype-albums' ).length ) {
					$(window).trigger('resize');
				}
			});
		}

		// Split Headlines
		elementorFrontend.waypoint( $scope.find( '.elementor-widget-split-headlines' ), function() {
			var headlineContainer = $( this ).find('.multi-headlines');
			headlineContainer.find('.split-header-wrap').each(function( index ) {
				var headline = $(this);
				setTimeout(function() {
					headline.addClass('reveal-split');
				}, 200 * index  );
				
			});
		} );

		elementorFrontend.waypoint( $scope.find( '.elementor-widget-blog-parallax .vertical-parallax-image' ), function() {
			var headline = $(this);
			headline.addClass('reveal-blog-parallax');
		},
		{
			offset: '60%',
			triggerOnce: true
		} );

		// Progress Bar
		elementorFrontend.waypoint( $scope.find( '.elementor-widget-progress-bar' ), function() {
			var progressbar = $( this );
			var skill_indicate = progressbar.find('.skillbar-bar');
			skill_indicate.css( 'width', skill_indicate.data( 'percentage' ) + '%' );
		} );

		// Section Heading Reveal
		elementorFrontend.waypoint( $scope.find( '.elementor-widget-section-heading' ), function() {
			$( this ).addClass( 'theme-reveal-animation' );
		},
		{
			offset: '60%',
			triggerOnce: true
		} );

		// Counter
		elementorFrontend.waypoint( $scope.find( '.elementor-widget-service-box' ), function() {
			var counterbox = $(this).find('.time-count-data');
			var countTo = counterbox.data('to');
			counterbox.numerator({ duration: 3000 , toValue: countTo});
		},
		{
			offset: '60%',
			triggerOnce: true
		} );

		// Image Carousel
		if ($('.owl-works-detect').length) {
			$('.owl-works-detect').each(function() {
				var thisID = $(this).data('id');
				var thisAutoplay = $(this).data('autoplay');
				var thisLazyload = $(this).data('lazyload');
				var thisPagination = $(this).data('pagination');
				var thisColumns = $(this).data('columns');
				var thisType = $(this).data('type');
				var thisAutoplayTimeout = $(this).data('autoplaytimeout');
				thisAutoplay = typeof thisAutoplay !== 'undefined' ? thisAutoplay : 'false';
				thisAutoplayTimeout = typeof thisAutoplayTimeout !== 'undefined' ? thisAutoplayTimeout : '10000';
				thisLazyload = typeof thisLazyload !== 'undefined' ? thisLazyload : 'false';
				thisPagination = typeof thisPagination !== 'undefined' ? thisPagination : 'false';
				thisColumns = typeof thisColumns !== 'undefined' ? thisColumns : '4';
				thisID = typeof thisID !== 'undefined' ? thisID : 'false';

				 $('#'+thisID).owlCarousel({
					responsiveClass:true,
					responsive:{
						0:{
							items:1,
							nav:true
						},
						480:{
							items:2,
							nav:true
						},
						800:{
							items: thisColumns,
							nav:true
						}
					},
					autoplay: thisAutoplay,
					autoplayTimeout: thisAutoplayTimeout,
					autoplayHoverPause:true,
					lazyLoad: thisLazyload,
					dots: thisPagination,
					items: thisColumns,
					nav : true,
					navText : ["",""],
					loop: false,
					onResize : reAdjustJarallax
				});

			});
		}

		$('.is-animation-group').each(function() {
			
			var self = $(this);

			// Counter
			elementorFrontend.waypoint( $scope.find( '.animated-group-element' ), function() {
				var animGroupElement = $(this);
				var animGroupElementDelay = animGroupElement.data('animationdelay');
				animGroupElementDelay = typeof animGroupElementDelay !== 'undefined' ? animGroupElementDelay : '0';
				setTimeout(function() {
					animGroupElement.removeClass('animation-standby-group-item').addClass('animation-action');
				}, animGroupElementDelay );
			} );

		});


		// Edit Mode
		if ( elementorFrontend.isEditMode() ) {
			$('.elementor-widget-progress-bar').each(function() {
				var progressbar = $( this );
				var skill_indicate = progressbar.find('.skillbar-bar');
				skill_indicate.css( 'width', skill_indicate.data( 'percentage' ) + '%' );
			});
			$('.elementor-widget-service-box').each(function() {
				var counterbox = $(this).find('.time-count-data');
				var countTo = counterbox.data('to');
				counterbox.numerator({ duration: 2000 , toValue: countTo});
			});
		}

		if ($.fn.tilt) {
			$(".has-effect-tilt .gridblock-grid-element").tilt({
				maxTilt: 20,
				perspective: 550,
				easing: "cubic-bezier(.03,.98,.52,.99)",
				speed: 800,
				glare: false,
				scale: 1.01
			});
		}

		function reAdjustJarallax () {
			if ($.fn.jarallax) {
				setTimeout(function() {
					$('.jarallax-parent').jarallax('clipContainer');
					$('.jarallax-parent').jarallax('coverImage');
				}, 600 );
			}
		}


		function editorChoice() {

			jQuery(".editor-mode-on .mtheme-editor-choice.mtheme-editor-active").click(function() {

				var proofing_item = jQuery(this);
				var image_id = proofing_item.data("image_id");
				var editor_choice = $('#mtheme-proofing-item-' + image_id + '').data('editor_choice');

				jQuery.ajax({
					type: "post",
					url: ajax_var.url,
					data: "action=superlens_editor_recommended_checker&nonce=" + ajax_var.nonce + "&mtheme_editor_choice=" + editor_choice + "&image_id=" + image_id,
					beforeSend: function() {
						$('#mtheme-proofing-item-' + image_id + '').addClass("proofing-item-inprogress");
						$("#proofing-status-count").removeClass('pulse');
					},
					success: function(data) {

						// Split and Get the values in data varaible that has been given as COUNT:POSTID
						var substr = data.split(':');
						var checked = substr[0];
						var image_id = substr[1];

						if (checked == "checked") {
							$('#mtheme-proofing-item-' + image_id + '').removeClass("proofing-item-inprogress").removeClass("editor-item-unchecked").addClass("editor-item-selected");
							$('#mtheme-proofing-item-' + image_id + '').find('.editor-icon-status').removeClass("ion-android-radio-button-off").addClass("ion-android-radio-button-on");
							$('#mtheme-proofing-item-' + image_id + '').data('editor_choice', 'editorselected');
							$('#mtheme-proofing-item-' + image_id + '').removeClass("filter-editorunchecked").addClass('filter-editorselected');
						} else {
							$('#mtheme-proofing-item-' + image_id + '').removeClass("proofing-item-inprogress").addClass("editor-item-unchecked").removeClass("editor-item-selected");
							$('#mtheme-proofing-item-' + image_id + '').find('.editor-icon-status').addClass("ion-android-radio-button-off").removeClass("ion-android-radio-button-on");
							$('#mtheme-proofing-item-' + image_id + '').data('editor_choice', 'editorunchecked');
							$('#mtheme-proofing-item-' + image_id + '').removeClass("filter-editorselected").addClass('filter-editorunchecked');
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});

				return false;
			});

		}
		editorChoice();

		function AjaxProofing() {

			var proofing_count_total = $(".proofing-item-wrap > .mtheme-proofing-item").length;
			var proofing_count_selected = $(".proofing-item-wrap > .proofing-item-selected").length;

			$(".proofing-count-total").html(proofing_count_total);
			$(".proofing-count-selected").html(proofing_count_selected);

			jQuery(".mtheme-proofing-choice.mtheme-proofing-active").click(function( event ) {

				var proofing_item = jQuery(this);
				var image_id = proofing_item.data("image_id");
				var proofing_status = $('#mtheme-proofing-item-' + image_id + '').data('proofing_status');

				jQuery.ajax({
					type: "post",
					url: ajax_var.url,
					data: "action=superlens_proofing_checker&nonce=" + ajax_var.nonce + "&proofing_status=" + proofing_status + "&image_id=" + image_id,
					beforeSend: function() {
						$('#mtheme-proofing-item-' + image_id + '').addClass("proofing-item-inprogress");

						$('#lightbox-like-id-' + image_id ).addClass("proofing-lightbox-inprogress").removeClass('bounceIn').addClass('voteprogress');
						$('#lightbox-like-id-' + image_id ).find('.proofing-lightbox-like').removeClass("proofing-lightbox-clickready");

						$("#proofing-status-count").removeClass('pulse');
						// console.log('voting' + image_id );
					},
					success: function(data) {

						// Split and Get the values in data varaible that has been given as COUNT:POSTID
						var substr = data.split(':');
						var checked = substr[0];
						var image_id = substr[1];

						// console.log('----------------voted' + image_id );

						if (checked == "checked") {
							$('#mtheme-proofing-item-' + image_id + '').removeClass("proofing-item-inprogress").removeClass("proofing-item-unchecked").addClass("proofing-item-selected");
							$('#mtheme-proofing-item-' + image_id + '').find('.proofing-icon-status').removeClass("ion-ios-heart-outline").addClass("ion-ios-heart");
							$('#mtheme-proofing-item-' + image_id + '').data('proofing_status', 'selected');
							$('#mtheme-proofing-item-' + image_id + '').removeClass("filter-unchecked").addClass('filter-selected');

							$('#lightbox-like-id-' + image_id ).removeClass("proofing-lightbox-inprogress").addClass('bounceIn').removeClass('voteprogress');
							$('#lightbox-like-id-' + image_id + '').find('.proofing-lightbox-like').removeClass("ion-ios-heart-outline").addClass("ion-ios-heart");
							$('#lightbox-like-id-' + image_id ).find('.proofing-lightbox-like').addClass("proofing-lightbox-clickready");
							
						} else {
							$('#mtheme-proofing-item-' + image_id + '').removeClass("proofing-item-inprogress").addClass("proofing-item-unchecked").removeClass("proofing-item-selected");
							$('#mtheme-proofing-item-' + image_id + '').find('.proofing-icon-status').addClass("ion-ios-heart-outline").removeClass("ion-ios-heart");
							$('#mtheme-proofing-item-' + image_id + '').data('proofing_status', 'unchecked');
							$('#mtheme-proofing-item-' + image_id + '').removeClass("filter-selected").addClass('filter-unchecked');

							$('#lightbox-like-id-' + image_id ).removeClass("proofing-lightbox-inprogress").addClass('bounceIn').removeClass('voteprogress');
							$('#lightbox-like-id-' + image_id + '').find('.proofing-lightbox-like').addClass("ion-ios-heart-outline").removeClass("ion-ios-heart");
							$('#lightbox-like-id-' + image_id ).find('.proofing-lightbox-like').addClass("proofing-lightbox-clickready");
						}
						var proofing_count_total = $(".proofing-item-wrap > .mtheme-proofing-item").length;
						var proofing_count_selected = $(".proofing-item-wrap > .proofing-item-selected").length;
						$(".proofing-count-total").html(proofing_count_total);
						$(".proofing-count-selected").html(proofing_count_selected);
						$("#proofing-status-count").addClass('pulse');
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});
				event.stopImmediatePropagation();
				return false;
			});

		}
		AjaxProofing();

		function swiperPortfolioSlider(){
			if ( typeof Swiper != 'undefined' ) {
				if ($('.blog-slider').length) {
					$('.blog-slider').each(function() {
						var swipercontainer = $(this);
						var swiperID = '#' + swipercontainer.data('id');
						var blogslider = new Swiper('.blog-slider', {
							spaceBetween: 30,
							effect: 'fade',
							mousewheel: {
							invert: false,
							},
							navigation: {
								nextEl: '.swiper-button-next',
								prevEl: '.swiper-button-prev',
							},
							pagination: {
							el: '.blog-slider__pagination',
							clickable: true,
							}
						});
					});
				}
			}
		}
		swiperPortfolioSlider();

		function owlcarouselsInit() {
			if ($('.owl-carousel-detect').length) {
				$('.owl-carousel-detect').each(function() {
					var thisID = $(this).data('id');
					var thisAutoplay = $(this).data('autoplay');
					var thisLazyload = $(this).data('lazyload');
					var thisColumns = $(this).data('columns');
					var thisSmartspeed = $(this).data('smartspeed');
					var thisType = $(this).data('type');
					var thisAutoplayTimeout = $(this).data('autoplaytimeout');
					thisAutoplay = typeof thisAutoplay !== 'undefined' ? thisAutoplay : 'false';
					thisLazyload = typeof thisLazyload !== 'undefined' ? thisLazyload : 'false';
					thisColumns = typeof thisColumns !== 'undefined' ? thisColumns : '4';
					thisColumnMid = 1;
					thisSmartspeed = typeof thisSmartspeed !== 'undefined' ? thisSmartspeed : '1000';
					thisAutoplayTimeout = typeof thisAutoplayTimeout !== 'undefined' ? thisAutoplayTimeout : '5000';
					thisType = typeof thisType !== 'undefined' ? thisType : 'slideshow';
					thisID = typeof thisID !== 'undefined' ? thisID : 'false';

					if (thisType !== "centercarousel" && thisType !== "flatcarousel" && thisType !== "testimony") {
						 $('#'+thisID).owlCarousel({
							items:1,
							loop:true,
							autoplay: thisAutoplay,
							smartSpeed: thisSmartspeed,
							autoplayTimeout: thisAutoplayTimeout,
							lazyLoad: thisLazyload,
							nav: true,
							autoHeight: true,
							loop: true,
							navText : ["",""],
							singleItem : true,
							onResize : reAdjustJarallax
						});          
					}

				});
			}
		}

		if ( $.fn.imagesLoaded ) {
			$('.owl-carousel-detect').imagesLoaded( function() {
				owlcarouselsInit();
			});
		}
		function beforeafter() {
			if ($('.before-after-detect').length) {
				var thisID = $(this).data('id');
				$('.before-after-detect').each(function() {
					$(this).twentytwenty({default_offset_pct: 0.5});
				});
			} 
		}
		if ( $.fn.imagesLoaded ) {
			$('.before-after-detect').imagesLoaded( function() {
				beforeafter();
			});
		}

	};

	function ThumbnailLikes() {

		$(document).on('click', ".mtheme-post-like .vote-ready", function() {

			var heart = jQuery(this);

			var post_id = heart.data("post_id");

			jQuery.ajax({
				type: "post",
				url: ajax_var.url,
				data: "action=superlens_post_like_vote&nonce=" + ajax_var.nonce + "&post_id=" + post_id,
				beforeSend: function() {

					if (!heart.hasClass('voted')) {
						heart.children("span.mtheme-like").removeClass("bounceIn");
						heart.children("span.mtheme-like").addClass("voteprogress");
					}
				},
				success: function(data) {
					// Split and Get the values in data varaible that has been given as COUNT:POSTID

					var substr = data.split(':');
					var count = substr[0];
					var post_id = substr[1];
					if (count != "already") {

						jQuery('.mtheme-post-like span[data-post_id="' + post_id + '"]').removeClass("vote-ready").addClass("vote-disabled");
						jQuery('.mtheme-post-like span[data-post_id="' + post_id + '"]').find(".mtheme-like").removeClass("like-notvoted").addClass("voted").removeClass("voteprogress");
						jQuery('.mtheme-post-like span[data-post_id="' + post_id + '"]').find(".vote-like-icon").removeClass("ion-ios-heart-outline").addClass("ion-ios-heart");
						jQuery('.post-link-count-wrap[data-count_id="' + post_id + '"]').find("span.post-like-count").text(count);
					}
				},
				complete: function(){
					heart.children("span.mtheme-like").addClass("bounceIn");
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
				}
			});

			return false;
		});

	}
	ThumbnailLikes();

	$('.elementor-widget-split-headlines').each(function() {
		var headlineContainer = $( this ).find('.multi-headlines');
		headlineContainer.find(':header').each(function() {
			$(this).wrap( "<div class='split-header-wrap'><div class='split-header-inner'></div></div>" );
		});
	});
	
	// Make sure you run this code under Elementor.
	$( window ).on( 'elementor/frontend/init', function() {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', WidgetImaginemBlocksHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/imaginem-animated-headings.default', function( $scope ) {
			var headlinecontainer = $scope.find( '.fliptitles-intro' );
			var headlines = headlinecontainer.find( '.fliptitles-headline' );
			//var animDelay = 3500;
			var animDelay = headlinecontainer.data('delay');
			var nextWord = '';
			var currWord = '';
		
			animDelay = typeof animDelay !== 'undefined' ? animDelay : '3500';
		
			headlines.each( function() {
				var allWords = $(this);
				var displayedWord = allWords.find('.word-visible').eq(0);
				setTimeout( function() {
					wordHeart( displayedWord );
				}, animDelay );
			});
		
			function wordHeart( currWord ) {
				
				if ( !currWord.is(':last-child') ) {
					nextWord = currWord.next();
				} else {
					nextWord = currWord.parent().children().eq(0);
				}
		
				currWord.removeClass('word-visible').addClass('word-hidden');
				nextWord.removeClass('word-hidden').addClass('word-visible');
		
				setTimeout(function(){ wordHeart(nextWord) }, animDelay );
			}
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/proofing-archive.default', function( $scope ) {
			var container = $scope.find( '#gridblock-container' );
			isotopeInit( container );
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/portfolio-grid.default', function( $scope ) {
			var container = $scope.find( '#gridblock-container' );
			isotopeInit( container );
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/thumbnails-grid.default', function( $scope ) {
			var container = $scope.find( '.thumbnails-grid-container' );
			isotopeInit( container );
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/blog-grid.default', function( $scope ) {
			var container = $scope.find( '#gridblock-container' );
			isotopeInit( container );
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/proofing-grid.default', function( $scope ) {
			var container = $scope.find( '#gridblock-container' );
			isotopeInit( container );
		} );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/circular-counter.default', function( $scope ) {
			var circularchart = $scope.find( '.radial-chart' );
			elementorFrontend.waypoint( $scope.find( '.radial-chart' ), function() {
				circularchart.easyPieChart({
					easing: 'cubic-bezier(.03,.98,.2,.99)',
					animate: {
						duration: 2500,
						enabled: true
					},
				} ).addClass('radial-animation-on');
			},
			{
				offset: '60%',
				triggerOnce: true
			} );
			
		} );

		// For a widget without a skin
		elementorFrontend.hooks.addAction( 'frontend/element_ready/slideshow-carousel.default', function( $scope ) {
			var slideshow = $scope.find( '.owl-carousel-detect' );
			var thisID = slideshow.data('id');
			var thisAutoplay = slideshow.data('autoplay');
			var thisLazyload = slideshow.data('lazyload');
			var thisColumns = slideshow.data('columns');
			var thisSmartspeed = slideshow.data('smartspeed');
			var thisType = slideshow.data('type');
			var thisAutoplayTimeout = slideshow.data('autoplaytimeout');
			thisAutoplay = typeof thisAutoplay !== 'undefined' ? thisAutoplay : 'false';
			thisLazyload = typeof thisLazyload !== 'undefined' ? thisLazyload : 'false';
			thisColumns = typeof thisColumns !== 'undefined' ? thisColumns : '4';
			thisColumnMid = 1;
			thisSmartspeed = typeof thisSmartspeed !== 'undefined' ? thisSmartspeed : '1000';
			thisAutoplayTimeout = typeof thisAutoplayTimeout !== 'undefined' ? thisAutoplayTimeout : '5000';
			thisType = typeof thisType !== 'undefined' ? thisType : 'slideshow';
			thisID = typeof thisID !== 'undefined' ? thisID : 'false';

			function SlideshowReAdjustJarallax () {
				if ($.fn.jarallax) {
					setTimeout(function() {
						$('.jarallax-parent').jarallax('clipContainer');
						$('.jarallax-parent').jarallax('coverImage');
					}, 600 );
				}
			}

			if (thisType=="centercarousel") {
				$('#'+thisID).owlCarousel({
					responsiveClass:true,
					responsive:{
						0:{
							items:1,
							nav:true
						},
						600:{
							items:1,
							nav:true
						},
						1000:{
							items:1,
							nav:true
						},
						1350:{
							items:2,
							nav:true
						}
					},
					center: true,
					items:2,
					loop:true,
					margin:10,
					stagePadding: 10,
					autoplay: thisAutoplay,
					autoplayTimeout: thisAutoplayTimeout,
					lazyLoad: thisLazyload,
					nav: true,
					autoHeight : true,
					loop: true,
					navText : ["",""],
					singleItem : true,
					onResize : SlideshowReAdjustJarallax
				});
			}
			if (thisType=="flatcarousel") {
					$('#'+thisID).owlCarousel({
					responsiveClass:true,
					responsive:{
						0:{
							items:1,
							nav:true
						},
						600:{
							items:1,
							nav:true
						},
						1000:{
							items:1,
							nav:true
						},
						1350:{
							items:2,
							nav:true
						}
					},
					center: true,
					items:2,
					loop:true,
					margin:20,
					stagePadding: 10,
					smartSpeed: thisSmartspeed,
					autoplay: thisAutoplay,
					autoplayTimeout: thisAutoplayTimeout,
					lazyLoad: thisLazyload,
					nav: true,
					autoHeight : true,
					loop: true,
					navText : ["",""],
					singleItem : true,
					onResize : SlideshowReAdjustJarallax
				});
			}
			$('#'+thisID).on('click',function(){
				$('#'+thisID).trigger('stop.owl.autoplay');
			})
		} );
		// For a widget without a skin
		elementorFrontend.hooks.addAction( 'frontend/element_ready/testimonials.default', function( $scope ) {
			var testimonial = $scope.find( '.owl-carousel-detect' );
			var thisID = testimonial.data('id');
			var thisAutoplay = testimonial.data('autoplay');
			var thisLazyload = testimonial.data('lazyload');
			var thisColumns = testimonial.data('columns');
			var thisSmartspeed = testimonial.data('smartspeed');
			var thisType = testimonial.data('type');
			var thisAutoplayTimeout = testimonial.data('autoplaytimeout');
			thisAutoplay = typeof thisAutoplay !== 'undefined' ? thisAutoplay : 'false';
			thisLazyload = typeof thisLazyload !== 'undefined' ? thisLazyload : 'false';
			thisColumns = typeof thisColumns !== 'undefined' ? thisColumns : '4';
			thisColumnMid = 1;
			thisSmartspeed = typeof thisSmartspeed !== 'undefined' ? thisSmartspeed : '1000';
			thisAutoplayTimeout = typeof thisAutoplayTimeout !== 'undefined' ? thisAutoplayTimeout : '5000';
			thisType = typeof thisType !== 'undefined' ? thisType : 'slideshow';
			thisID = typeof thisID !== 'undefined' ? thisID : 'false';

			function TestimonyReAdjustJarallax () {
				if ($.fn.jarallax) {
					setTimeout(function() {
						$('.jarallax-parent').jarallax('clipContainer');
						$('.jarallax-parent').jarallax('coverImage');
					}, 600 );
				}
			}

			if ( thisColumns > 1 ) {
				thisColumnMid = 2;
			}
			
			$('#'+thisID).owlCarousel({
				items: 3,
				responsiveClass:true,
				responsive:{
					0:{
						items:1,
						nav:true
					},
					600:{
						items: thisColumnMid,
						nav:true
					},
					1000:{
						items: thisColumns,
						nav:true
					},
				},
				singleItem : true,
				margin:10,
				scrollPerPage : false,
				pagination: true,
				autoplay: thisAutoplay,
				autoplayTimeout: thisAutoplayTimeout,
				autoplayHoverPause:true,
				autoHeight:true,
				animateOut: "animation-action fadeOut",
				animateIn: "animation-action fadeIn",
				nav: true,
				loop: true,
				navText : ["",""],
				onResize : TestimonyReAdjustJarallax
			});
		} );

		// For a widget without a skin
		elementorFrontend.hooks.addAction( 'frontend/element_ready/timeline-contents.default', function( $scope ) {
			if (typeof Swiper != "undefined") {
				var container = $scope.find( '.swiperslide-timeline-contents-container' );
				var containerContents = $scope.find( '.swiper-slide-contents' );
				var containerDates = $scope.find( '.timeline-dates' );

				var classID = container.data('id');
				var swiperID = '#' + container.data('id');
				var getautoplay = container.data('autoplay');

				getautoplay = typeof getautoplay !== 'undefined' ? getautoplay : '6000';
				var autoplaydata = [];
				if (getautoplay=="0") {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
					autoplaydata.disableOnInteraction = true;
				}
				if ( $("body").hasClass("elementor-editor-active") ) {
					autoplaydata = false;
				}

				var timelineContents = new Swiper(containerContents, {
					navigation: {
						nextEl: '.timeline-button-next',
						prevEl: '.timeline-button-prev',
					},
					grabCursor: true,
					spaceBetween: 10,
					autoplay: false,
					speed: 1000,
				});
				var timelineDates = new Swiper(containerDates, {
					spaceBetween: 10,
					centeredSlides: true,
					autoplay: false,
					slidesPerView: 'auto',
					touchRatio: 0.2,
					speed: 1000,
					slideToClickedSlide: true
				});
				timelineContents.controller.control = timelineDates;
				timelineDates.controller.control = timelineContents;	
			}
		} );
		// For a widget without a skin
		elementorFrontend.hooks.addAction( 'frontend/element_ready/timeline.default', function( $scope ) {
			if (typeof Swiper != "undefined") {
				var swipercontainer = $scope.find( '.swiperslide-timeline-container' );
				var classID = swipercontainer.data('id');
				var swiperID = '#' + swipercontainer.data('id');
				var getautoplay = swipercontainer.data('autoplay');

				getautoplay = typeof getautoplay !== 'undefined' ? getautoplay : '6000';

				var autoplaydata = [];
				if (getautoplay=="0") {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
				}
				if ( $("body").hasClass("elementor-editor-active") ) {
					autoplaydata = false;
				}
				
				var timelineSwiper = new Swiper(swipercontainer, {
				direction: "vertical",
				lazy: {
					loadPrevNext: true,
					loadOnTransitionStart: true,
				},
				loop: false,
				speed: 1000,
				autoplay: autoplaydata,
				pagination: {
					el: '.swiper-pagination',
					type: 'bullets',
					renderBullet: function (index, className) {
						var year = document.querySelectorAll( swiperID + ' .swiper-slide')[index].getAttribute('data-year');
						return '<span class="' + className + '">' + year + '</span>';;
					},
					clickable: true
					},
					navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
					}
				});		
			}
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/fotorama.default', function( $scope ) {
			if ( $.fn.fotorama ) {

				var fotramaContainer = $scope.find( '.fotorama' );

				var adjustheight = fotramaContainer.data('adjustheight');
				var desktopoffset = fotramaContainer.data('desktopoffset');
				var mobileoffset = fotramaContainer.data('mobileoffset');

				var heightval = 75;

				adjustheight = typeof adjustheight !== 'undefined' ? adjustheight : '75';
				desktopoffset = typeof desktopoffset !== 'undefined' ? desktopoffset : 'no';
				mobileoffset = typeof mobileoffset !== 'undefined' ? mobileoffset : 'no';

				heightval = ( $(window).height() / 100 ) * adjustheight;

				if( $( '.responsive-menu-wrap' ).is( ':visible' ) ) {
					if ( mobileoffset == 'yes' ) {
						mobile_header_height = $( '.responsive-menu-wrap' ).outerHeight();
						heightval = heightval - mobile_header_height;
					}
				} else {
					if ( desktopoffset == 'yes' ) {
						desktop_header_height = $( '.outer-wrap' ).outerHeight();
						heightval = heightval - desktop_header_height;
					}
				}

				fotramaContainer.fotorama({
					height: heightval
				});
				$('.mtheme-fotorama').addClass('fotorama-initized');
				var i = 0;
				fotramaContainer.find( '.fotorama__nav__shaft' ).each(function() {
					$( this ).find( '.fotorama__thumb' ).each(function( counter ) {
						$( this )
							.delay( ++i * 20 + Math.random() * 1000 )
							.velocity( { opacity:1 }, 500 );
					}).promise().done( function() { $( '.fotorama__nav__shaft .fotorama__thumb-border' ).velocity( { opacity:1 }, 500 ); } );
				});

				$(window).on( 'debouncedresize', function( event ) {
					heightval = ( $(window).height() / 100 ) * adjustheight;

					if( $( '.responsive-menu-wrap' ).is( ':visible' ) ) {
						if ( mobileoffset == 'yes' ) {
							mobile_header_height = $( '.responsive-menu-wrap' ).outerHeight();
							heightval = heightval - mobile_header_height;
						}
					} else {
						if ( desktopoffset == 'yes' ) {
							desktop_header_height = $( '.outer-wrap' ).outerHeight();
							heightval = heightval - desktop_header_height;
						}
					}
					
					fotramaContainer.fotorama({
						height: heightval
					});
				});
			}
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/swiper-slides.default', function( $scope ) {
			if (typeof Swiper != "undefined") {
				var swipercontainer = $scope.find( '.shortcode-swiper-container' );
				var autoplaydata = [];
				var lesscolumns = 1;
				var paginaitonType = 'bullets';
				var midlesscolumns = 1;
				var geteffect = [];
				var effectslide = [];
				var fadeslide = [];
				var slidepagination = [];
				var setloop = false;
				var initStatus = true;
				var swiperID = '#' + swipercontainer.data('id');
				var columns = swipercontainer.data('columns');
				var getpagination = swipercontainer.data('swiperpagination');
				var getautoplay = swipercontainer.data('autoplay');
				var geteffect = swipercontainer.data('slidestyle');
				columns = typeof columns !== 'undefined' ? columns : '4';
				getautoplay = typeof getautoplay !== 'undefined' ? getautoplay : '5000';
				geteffect = typeof geteffect !== 'undefined' ? geteffect : 'slide';
				getpagination = typeof getpagination !== 'undefined' ? getpagination : 'yes';
				if ( getautoplay == '0' ) {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
				}
				effectslide = 'slide';
				setloop = true;
				if ( $("body").hasClass("elementor-editor-active") ) {
					autoplaydata = false;
				}
					
				if ( getpagination == 'yes' ) {
					paginaitonType = 'bullets';
				}
				if ( getpagination == 'fraction' ) {
					paginaitonType = 'fraction';
				}

				if ( getautoplay == '0' ) {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
					autoplaydata.disableOnInteraction = true;
				}
				effectslide = 'slide';
				if ( columns !== 1 ) {
					geteffect = 'slide';
					setloop = false;
				}
				if ( geteffect == 'fade' ) {
					effectslide = 'fade';
					fadeslide.crossFade = false;
					setloop = true;
				}

				if ( columns == 2 ) {
					lesscolumns = 2;
					midlesscolumns = 2;
				}
				if ( columns == 3 ) {
					lesscolumns = 2;
					midlesscolumns = 3;
				}
				if ( columns > 3 ) {
					lesscolumns = 2;
					midlesscolumns = 4;
				}

				if ( columns == 1 ) {
					initStatus = false;
				}

				var multiswiper = new Swiper(swipercontainer, {

					pagination: {
						el: '.swiper-pagination',
						type: paginaitonType,
						clickable: true,
					},
					lazy: {
						loadPrevNext: true,
						loadOnTransitionStart: true,
					},
					keyboard: {
						enabled: true,
						onlyInViewport: true,
					},
					init: initStatus,
					watchSlidesVisibility: true,
					loop: setloop,
					effect: effectslide,
					fadeEffect: fadeslide,
					autoplay: autoplaydata,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					slidesPerView: columns,
					spaceBetween: 0,
					speed: 1000,
					breakpoints: {
						320: {
							slidesPerView: 1,
							spaceBetween: 0
						},
						640: {
							slidesPerView: 1,
							spaceBetween: 0
						},
						1000: {
							slidesPerView: lesscolumns,
							spaceBetween: 0
						},
						1300: {
							slidesPerView: midlesscolumns,
							spaceBetween: 0
						}
					},
					on: {
						transitionStart: function () {
							swipercontainer.removeClass('transition-done');
							swipercontainer.addClass('transition-progress');
						},
						transitionEnd: function () {
							swipercontainer.removeClass('transition-progress');
							swipercontainer.addClass('transition-done');
						},
					  }
				});
				if ( columns == 1 ) {
					$( document ).ready(function() {
						setTimeout(function() {
							multiswiper.init();
					}, 300);
					});
				}
				

			}
		} );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/image-reel.default', function( $scope ) {
			if (typeof Swiper != "undefined") {
				var swipercontainer = $scope.find( '.shortcode-multislider-container' );
				var autoplaydata = [];
				var lesscolumns = 1;
				var paginaitonType = 'bullets';
				var midlesscolumns = 1;
				var geteffect = [];
				var effectslide = [];
				var fadeslide = [];
				var slidepagination = [];
				var setloop = false;
				var swiperID = '#' + swipercontainer.data('id');
				var columns = swipercontainer.data('columns');
				var slidetotal = swipercontainer.data('slidetotal');
				var getpagination = swipercontainer.data('swiperpagination');
				var getautoplay = swipercontainer.data('autoplay');
				var geteffect = swipercontainer.data('slidestyle');
				columns = typeof columns !== 'undefined' ? columns : '4';
				getautoplay = typeof getautoplay !== 'undefined' ? getautoplay : '5000';
				geteffect = typeof geteffect !== 'undefined' ? geteffect : 'slide';
				getpagination = typeof getpagination !== 'undefined' ? getpagination : 'yes';
				if ( getautoplay == '0' ) {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
				}
				effectslide = 'slide';
				setloop = true;
				if ( $("body").hasClass("elementor-editor-active") ) {
					autoplaydata = false;
				}
					
				if ( getpagination == 'yes' ) {
					paginaitonType = 'bullets';
				}
				if ( getpagination == 'fraction' ) {
					paginaitonType = 'fraction';
				}

				if ( getautoplay == '0' ) {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
					autoplaydata.disableOnInteraction = true;
				}
				effectslide = 'slide';
				if ( columns !== 1 ) {
					geteffect = 'slide';
					setloop = false;
				}
				if ( geteffect == 'fade' ) {
					effectslide = 'fade';
					fadeslide.crossFade = false;
					setloop = true;
				}

				if ( columns == 2 ) {
					lesscolumns = 2;
					midlesscolumns = 2;
				}
				if ( columns == 3 ) {
					lesscolumns = 2;
					midlesscolumns = 3;
				}
				if ( columns > 3 ) {
					lesscolumns = 2;
					midlesscolumns = 4;
				}
				
				var heroswiper = new Swiper(swipercontainer, {

					pagination: {
						el: '.swiper-pagination',
						type: paginaitonType,
						clickable: true,
					},
					loop: true,
					effect: effectslide,
					fadeEffect: fadeslide,
					autoplay: autoplaydata,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					slidesPerView: 'auto',
					spaceBetween: 12,
					loopedSlides: slidetotal,
					speed: 1000,
					on: {
						transitionStart: function () {
							swipercontainer.removeClass('transition-done');
							swipercontainer.addClass('transition-progress');
						},
						transitionEnd: function () {
							swipercontainer.removeClass('transition-progress');
							swipercontainer.addClass('transition-done');
						},
					  }
				});
			}
		} );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/imaginem-countdown.default', function( $scope ) {

			var countdown = $scope.find( '.theme-countdown' );
			var finalDate = countdown.data('finaldate');
			var countdownID = $( '#' + countdown.data('countid') );
			var wordDay = countdown.data('day');
			var wordDays = countdown.data('days');
			var wordWeek = countdown.data('week');
			var wordWeeks = countdown.data('weeks');
			var wordYear = countdown.data('year');
			var wordYears = countdown.data('years');
			var wordEnded = countdown.data('ended');
			//finaldate = finaldate + ':00';
			//console.log( countdownID );
			//countdown.countdown(finaldate);
			//finalDate = new Date().getTime() + 5000;
			countdownID.countdown(finalDate, function(event) {
				var format = '<span class="countdown-time">%H:%M:%S</span>';
				  if(event.offset.days > 0) {
					if( event.offset.days == 1 ) {
						format = '<span class="countdown-day">%-d ' + wordDay + '</span> ' + format;
					} else {
						format = '<span class="countdown-day">%-d ' + wordDays + '</span> ' + format;
					}
				  }
				  if(event.offset.weeks > 0) {
					if(event.offset.weeks == 1) {
						format = '<span class="countdown-week">%-w ' + wordWeek + '</span> ' + format;
					} else {
						format = '<span class="countdown-week">%-w ' + wordWeeks + '</span> ' + format;
					}
				  }
				  if(event.offset.years > 0) {
					if(event.offset.years == 1) {
						format = '<span class="countdown-year">%-Y ' + wordYear + '</span> ' + format;
					} else {
						format = '<span class="countdown-year">%-Y ' + wordYears + '</span> ' + format;
					}
				  }
				  if (event.elapsed) {
					countdownID.html(event.strftime( wordEnded ));
				  } else {
					countdownID.html(event.strftime(format));
				  }
				  
			});
		} );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/card-slider.default', function( $scope ) {
			if (typeof Swiper != "undefined") {
				var swipercontainer = $scope.find( '.shortcode-swiper-container' );
				var autoplaydata = [];
				var lesscolumns = 1;
				var paginaitonType = 'bullets';
				var midlesscolumns = 1;
				var geteffect = [];
				var effectslide = [];
				var fadeslide = [];
				var slidepagination = [];
				var setloop = false;
				var initStatus = true;
				var swiperID = '#' + swipercontainer.data('id');
				var columns = swipercontainer.data('columns');
				var getpagination = swipercontainer.data('swiperpagination');
				var getautoplay = swipercontainer.data('autoplay');
				var geteffect = swipercontainer.data('slidestyle');
				columns = typeof columns !== 'undefined' ? columns : '4';
				getautoplay = typeof getautoplay !== 'undefined' ? getautoplay : '5000';
				geteffect = typeof geteffect !== 'undefined' ? geteffect : 'slide';
				getpagination = typeof getpagination !== 'undefined' ? getpagination : 'yes';
				if ( getautoplay == '0' ) {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
				}
				effectslide = 'slide';
				setloop = true;
				if ( $("body").hasClass("elementor-editor-active") ) {
					autoplaydata = false;
				}
					
				if ( getpagination == 'yes' ) {
					paginaitonType = 'bullets';
				}
				if ( getpagination == 'fraction' ) {
					paginaitonType = 'fraction';
				}

				if ( getautoplay == '0' ) {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
					autoplaydata.disableOnInteraction = true;
				}
				effectslide = 'slide';
				if ( columns !== 1 ) {
					geteffect = 'slide';
					setloop = false;
				}
				if ( geteffect == 'fade' ) {
					effectslide = 'fade';
					fadeslide.crossFade = false;
					setloop = true;
				}

				if ( columns == 2 ) {
					lesscolumns = 2;
					midlesscolumns = 2;
				}
				if ( columns == 3 ) {
					lesscolumns = 2;
					midlesscolumns = 3;
				}
				if ( columns > 3 ) {
					lesscolumns = 2;
					midlesscolumns = 4;
				}

				if ( columns == 1 ) {
					initStatus = false;
				}

				var multiswiper = new Swiper(swipercontainer, {

					pagination: {
						el: '.swiper-pagination',
						type: paginaitonType,
						clickable: true,
					},
					lazy: {
						loadPrevNext: true,
						loadOnTransitionStart: true,
					},
					keyboard: {
						enabled: true,
						onlyInViewport: true,
					},
					init: initStatus,
					watchSlidesVisibility: true,
					loop: setloop,
					effect: effectslide,
					fadeEffect: fadeslide,
					autoplay: autoplaydata,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					slidesPerView: columns,
					spaceBetween: 0,
					speed: 1000,
					breakpoints: {
						320: {
							slidesPerView: 1,
							spaceBetween: 0
						},
						640: {
							slidesPerView: 1,
							spaceBetween: 0
						},
						1000: {
							slidesPerView: lesscolumns,
							spaceBetween: 0
						},
						1300: {
							slidesPerView: midlesscolumns,
							spaceBetween: 0
						}
					},
					on: {
						transitionStart: function () {
							swipercontainer.removeClass('transition-done');
							swipercontainer.addClass('transition-progress');
						},
						transitionEnd: function () {
							swipercontainer.removeClass('transition-progress');
							swipercontainer.addClass('transition-done');
						},
					  }
				});
				if ( columns == 1 ) {
					$( document ).ready(function() {
						setTimeout(function() {
							multiswiper.init();
					}, 300);
					});
				}
				

			}
		} );

	} );
} )( jQuery );
/*
 * BG Loaded
 * 
 *
 * Copyright (c) 2014 Jonathan Catmull
 * Licensed under the MIT license.
 */
 
(function($){
    $.fn.bgLoaded = function(custom) {

        var self = this;

    // Default plugin settings
    var defaults = {
        afterLoaded : function(){
            this.addClass('bg-loaded');
        }
    };

        // Merge default and user settings
        var settings = $.extend({}, defaults, custom);

        // Loop through element
        self.each(function(){
            var $this = $(this),
                bgImgs = $this.css('background-image').split(', ');
            $this.data('loaded-count',0);

            $.each( bgImgs, function(key, value){
                var img = value.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
                if (img !== "none") {
                    $('<img/>').attr('src', img).on( 'load', function() {
                        $(this).remove(); // prevent memory leaks
                        $this.data('loaded-count',$this.data('loaded-count')+1);
                        if ($this.data('loaded-count') >= bgImgs.length) {
                            settings.afterLoaded.call($this);
                        }
                    });
                }
            });

        });
    };
})(jQuery);
jQuery(document).ready(function($) {
	"use strict";

    if ($('.instagram-username').length) {
        var insta_username_halfwidth = ( $('.instagram-username').outerWidth() / 2 ) * -1;
        var insta_username_halfheight = ( $('.instagram-username').outerHeight() /2 ) * -1;
        $('.instagram-username').css('margin-left', insta_username_halfwidth + 'px');
        $('.instagram-username').css('margin-top', insta_username_halfheight + 'px');
	}
	
	function swiperSlideProductSlider() {
		if ( typeof Swiper != 'undefined' ) {
			var swiper = new Swiper('.portfolio-slider-wrapper .product-slider', {
				spaceBetween: 30,
				// initialSlide: 2,
				loop: false,
				navigation: {
					nextEl: '.next',
					prevEl: '.prev'
				},
				// mousewheel: {
				//     // invert: false
				// },
				on: {
					init: function(){
						var index = this.activeIndex;
		
						var target = $('.product-slider__item').eq(index).data('target');
		
						$('.product-img__item').removeClass('active');
						$('.product-img__item#'+ target).addClass('active');
					}
				}
		
			});
		
			swiper.on('slideChange', function () {
				var index = this.activeIndex;
		
				var target = $('.product-slider__item').eq(index).data('target');
		
				$('.product-img__item').removeClass('active');
				$('.product-img__item#'+ target).addClass('active');
		
				if(swiper.isEnd) {
					$('.prev').removeClass('disabled');
					$('.next').addClass('disabled');
				} else {
					$('.next').removeClass('disabled');
				}
		
				if(swiper.isBeginning) {
					$('.prev').addClass('disabled');
				} else {
					$('.prev').removeClass('disabled');
				}
			});
		
			$(".js-fav").on("click", function() {
				$(this).find('.heart').toggleClass("is-active");
			});
		}
	}
	swiperSlideProductSlider();

	if (typeof Rellax != "undefined") {
		if( $('.elementor-element').hasClass('rellax') ){
			var rellax = new Rellax('.rellax');
		}
	}
	$('.site-back-cover').bgLoaded();
	$('.gridblock-element-card').bgLoaded();
    $('.photocard-image-container').bgLoaded();
    $('.photocard-image-container').bgLoaded({
      afterLoaded : function(){
       this.parent('.photocard-image-wrap').addClass('bg-loaded');
      }
	});
	$( '.lazyload-container' ).each(function() {
		var lazyloadContainer = $( this );
		$(lazyloadContainer).find('img').each(function(){
			$(this).attr("data-src",$(this).attr("src"));
			$(this).attr("data-srcset",$(this).attr("srcset"));
			$(this).removeAttr("src").removeAttr("srcset").addClass('lazyload');
		});
	});
	$(document).on('lazybeforeunveil', function( e ){
		if( $(e.target).hasClass('lazyload-after') ){
			$('.dashboard-inner').addClass('dashboardimage-loaded');
		}

	});
	$(document).on('lazybeforeunveil lazyloaded', function(e){
		if( $(e.target).hasClass('lazyload-after') ){
			$(e.target).closest('.vertical-image-list').addClass('container-lazyimage-loaded');
		}
	});

	$(document).on('lazybeforeunveil lazyloaded', function(e){
		if(e.type === 'lazyloaded') {
			$(e.target).parents().closest('.gridblock-element').removeClass('grid-element-lazy-processing').addClass('grid-element-lazy-complete');
		} else {
			$(e.target).parents().closest('.gridblock-element').addClass('grid-element-lazy-processing');
		}
	});

	if ($('.progress-wrap').length) {
		$('body').addClass('goto-top-enabled');
		var progressPath = document.querySelector('.progress-wrap path');
		var pathLength = progressPath.getTotalLength();
		progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
		progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
		progressPath.style.strokeDashoffset = pathLength;
		progressPath.getBoundingClientRect();
		progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';		
		var updateProgress = function () {
			var scroll = $(window).scrollTop();
			var height = $(document).height() - $(window).height();
			var progress = pathLength - (scroll * pathLength / height);
			progressPath.style.strokeDashoffset = progress;
		}
		updateProgress();
		$(window).scroll(updateProgress);	
		var offset = 50;
		var duration = 550;
		jQuery(window).on('scroll', function() {
			if (jQuery(this).scrollTop() > offset) {
				jQuery('.progress-wrap').addClass('active-progress');
			} else {
				jQuery('.progress-wrap').removeClass('active-progress');
			}
		});
		jQuery('.progress-wrap').on('click', function(event) {
			event.preventDefault();
			$('#home').css('margin-top','0');
			$('html, body').velocity( 'scroll', 1000 );
			return false;
		});
	}

	if ($("body").hasClass("animated-cursor-active")) {
		var lerp = function lerp(a, b, n) {
		  return (1 - n) * a + n * b;
		};
	  
		var body = document.body;
	  
		var getMousePos = function getMousePos(e) {
		  var posx = 0;
		  var posy = 0;
		  if (!e) e = window.event;
	  
		  if (e.pageX || e.pageY) {
			posx = e.pageX;
			posy = e.pageY;
		  } else if (e.clientX || e.clientY) {
			posx = e.clientX + body.scrollLeft + document.documentElement.scrollLeft;
			posy = e.clientY + body.scrollTop + document.documentElement.scrollTop;
		  }
	  
		  return {
			x: posx,
			y: posy
		  };
		};
	  
		var Cursor =
		/*#__PURE__*/
		function () {
			function Cursor(el) {
				var _this = this;
		
				this.DOM = {
					el: el
				};
				this.DOM.dot = this.DOM.el.querySelector('.cursor__inner--dot');
				this.DOM.circle = this.DOM.el.querySelector('.cursor__inner--circle');
				this.bounds = {
				dot: this.DOM.dot.getBoundingClientRect(),
				circle: this.DOM.circle.getBoundingClientRect()
				};
				this.scale = 1;
				this.opacity = 1;
				this.mousePos = {
				x: 0,
				y: 0
				};
				this.lastMousePos = {
				dot: {
					x: 0,
					y: 0
				},
				circle: {
					x: 0,
					y: 0
				}
				};
				this.lastScale = 1;
				this.initEvents();
				requestAnimationFrame(function () {
					return _this.render();
				});
			}
		
			var _proto = Cursor.prototype;
		
			_proto.initEvents = function initEvents() {
				var _this2 = this;
		
				window.addEventListener('mousemove', function (ev) {
				return _this2.mousePos = getMousePos(ev);
				});
			};
	  
			_proto.render = function render() {
				var _this3 = this;
		
				this.lastMousePos.dot.x = lerp(this.lastMousePos.dot.x, this.mousePos.x - this.bounds.dot.width / 2, 1);
				this.lastMousePos.dot.y = lerp(this.lastMousePos.dot.y, this.mousePos.y - this.bounds.dot.height / 2, 1);
				this.lastMousePos.circle.x = lerp(this.lastMousePos.circle.x, this.mousePos.x - this.bounds.circle.width / 2, 0.15);
				this.lastMousePos.circle.y = lerp(this.lastMousePos.circle.y, this.mousePos.y - this.bounds.circle.height / 2, 0.15);
				this.lastScale = lerp(this.lastScale, this.scale, 0.15);
				this.DOM.dot.style.transform = "translateX(" + this.lastMousePos.dot.x + "px) translateY(" + this.lastMousePos.dot.y + "px)";
				this.DOM.circle.style.transform = "translateX(" + this.lastMousePos.circle.x + "px) translateY(" + this.lastMousePos.circle.y + "px) scale(" + this.lastScale + ")";
				requestAnimationFrame(function () {
				return _this3.render();
				});
			};
		
			_proto.enter = function enter() {
				this.scale = 1.5;
				this.DOM.dot.style.display = 'none';
			};
		
			_proto.leave = function leave() {
				this.scale = 1;
				this.DOM.dot.style.display = '';
			};
		
			return Cursor;
		}();
	  
			var cursor = new Cursor(document.querySelector('.cursor'));
			var mouse_over_element = false;
			window.addEventListener("mousemove", function (ev) {
			mouse_over_element = false;
	  
			if ($('.elementor-custom-embed-image-overlay:hover,.progress-wrap:hover,#mobile-toggle-menu:hover,a:hover,.owl-nav div:hover,#goto-top:hover,.owl-dot span:hover,.vote-ready:hover,.prev-hcarousel:hover,.next-hcarousel:hover,.fotorama__nav__shaft:hover,.fotorama__arr:hover,.swiper-button-prev:hover,.swiper-button-next:hover').length != 0) {
				mouse_over_element = true;
			}

			if ($('#supersized a:hover').length != 0) {
				mouse_over_element = false;
			}

			if ($('form input:hover, form select:hover').length != 0) {
				mouse_over_element = true;
			}

			if (mouse_over_element) {
				$('body').addClass('cursor-on-element');
			} else {
				$('body').removeClass('cursor-on-element');
			}
		});
	}

});
jQuery(document).ready(function($) {
	"use strict";

	$('.theme-elementor-menu-toggle').on('click', function(){
		$(this).toggleClass('elementor-editor-menu-on');
		$('body').toggleClass('elementor-editor-menu-active');
	});

});