jQuery(document).ready(function($) {
	"use strict";


	var stickyNavTop = 550;
	var stickyOutofSight = 300;
	var menuHeight = $('.stickymenu-zone').outerHeight();

	var stickyNav = function(){
		var scrollTop = $(window).scrollTop();
					
		if (scrollTop > stickyNavTop) { 
			$('body').addClass('stickymenu-active');
			if ( ! $( 'body' ).hasClass( 'header-type-overlay' ) && ! $( 'body' ).hasClass( 'header-type-inverse-overlay' ) ) {
				$('#home').css('margin-top',menuHeight);
			}
		}

		if (scrollTop > stickyOutofSight) { 
				$('body').addClass('menu-outofsight');
		} else {
				$('body').removeClass('menu-outofsight');
				$('body').removeClass('stickymenu-active');
				$('#home').css('margin-top','0');
		}
	};

	if( ! $( '.responsive-menu-wrap' ).is( ':visible' ) ) {
		if ($('body').hasClass('stickymenu-enabled-sitewide')) {
			stickyNav();

			$(window).scroll(function() {
				stickyNav();
			});
		}
	}

    var touchmoved;
    $('.gridblock-grid-element').on('touchend', function(e){
        if(touchmoved != true){
            $( '.gridblock-background-hover,.gridblock-links-wrap' ).velocity( { opacity:0 }, 50 );
            if( $(this).find('.gridblock-links-wrap').css('opacity') == '0' ) {
                $(this).find( '.gridblock-background-hover,.gridblock-links-wrap' ).velocity( { opacity:1 }, 100 );
                return false;
            }
        }
    }).on('touchmove', function(e){
        touchmoved = true;
    }).on('touchstart', function(){
        touchmoved = false;
    });
	
	$(document).on('lazybeforeunveil lazyloaded', function(e){
		if(e.type === 'lazyloaded') {
			$(e.target).parents().closest('.gridblock-element').removeClass('grid-element-lazy-processing').addClass('grid-element-lazy-complete');
		  } else {
			$(e.target).parents().closest('.gridblock-element').addClass('grid-element-lazy-processing');
		  }
	});

function onepageNavigation() {
		// Cache selectors
		var lastId,
		topMenu = $('.homemenu .sf-menu'),
		topMenuHeight = topMenu.outerHeight()+1,
		// All list items
		menuItems = topMenu.find('a'),
		// Anchors corresponding to menu items
		scrollItems = menuItems.map(function(){
			var indexItm = $(this).attr('href').indexOf('#');
			if (indexItm > 0) {
				var str = $(this).attr('href').substring(indexItm);
				var item = $(str);
				if (item.length) { return item; }
			}
		});

		var thebody = $('html, body');
		$('.responsive-mobile-menu .menu-item a[href*=\\#]').click(function(){
			var onepage_url = $(this).attr('href');
			var onepage_hash = '#' + onepage_url.substring(onepage_url.indexOf("#")+1);
			console.log(onepage_hash);
			thebody.animate({
					scrollTop: $( onepage_hash ).offset().top
			},{
					duration: 1700,
					easing: "easeInOutExpo"
			});
			if ($('body').hasClass('menu-is-onscreen')) {
					MobileMenuAction('resized');
			}
			return false;
		});

		// Bind to scroll
		$(window).scroll(function(){
			// Get container scroll position
			var fromTop = $(this).scrollTop()+topMenuHeight;
			
			// Get id of current scroll item
			var cur = scrollItems.map(function(){
				if ($(this).offset().top < fromTop)
					return this;
			});
			// Get the id of the current element
			cur = cur[cur.length-1];
			var id = cur && cur.length ? cur[0].id : '';
			
			if (lastId !== id) {
					lastId = id;
					// Set/remove active class
					menuItems
						.parent().removeClass('active')
						.end().filter('[href="#'+id+'"]').parent().addClass('active');
			}
		});
	}
	onepageNavigation();

	function centerLogo() {
		var countMenuParents = $(".homemenu ul.sf-menu > li").length;
		if (countMenuParents != 0) {
			if (countMenuParents>1) {
				var centerChild = Math.floor(countMenuParents / 2);
			} else {
				centerChild = 1;
			}
			var center_logo = $('body');
			if ( center_logo.length ) {
				$( ".header-site-title-section" ).detach().insertAfter('.homemenu ul.sf-menu > li:nth-child('+centerChild+')');
				$( ".header-site-title-section" ).wrap( '<li id="header-logo"></li>' );
				
				$( ".header-logo-section" ).detach().insertAfter('.homemenu ul.sf-menu > li:nth-child('+centerChild+')');
				$( ".header-logo-section" ).wrap( '<li id="header-logo"></li>' );
			}
		}
	}
	if ( $( 'body' ).hasClass( 'split-menu' ) ) {
		centerLogo();
	}

	if ( $( 'body' ).hasClass( 'page-is-fullscreen' ) ) {
		$( 'body' ).addClass( 'spinning-done' );
		if ( $( 'body' ).hasClass( 'page-is-fullscreen' ) ) {
			$( 'body' ).addClass('secondary-spinning');
		}
	} else {
		$( '.loading-spinner-detect' ).velocity('fadeOut', {
			duration: 350,
			complete: function() {
				$( 'body' ).addClass( 'spinning-done' );
			}
		});		
	}

	function stickymenu() {
		// Hide header on scroll down
		var didScroll;
		var lastScrollTop = 0;
		var delta = 100;
		var stickymenuzone = $('.stickymenu-zone');
		var stickymenuHide = 600;
		var navbarHeight = 0;

		$(window).scroll(function(event){
			didScroll = true;
		});

		setInterval(function() {
			if (didScroll) {
				hasScrolled();
				didScroll = false;
			}
		}, 250);

		function hasScrolled() {
			var st = $('html').scrollTop();
			// Make scroll more than delta
			if(Math.abs(lastScrollTop - st) <= delta)
				return;
			
				
			console.log(st);
			// If scrolled down and past the navbar, add class .nav-up.
			if (st > lastScrollTop && st > navbarHeight){
				// Scroll Down
				stickymenuzone.removeClass('stickymenu-active-show').addClass('stickymenu-active-hide');
			} else {
				// Scroll Up
				if ( st > stickymenuHide ) {
					if(st + $(window).height() < $(document).height()) {
						stickymenuzone.removeClass('stickymenu-active-hide').addClass('stickymenu-active-show');
					}
				} else {
					stickymenuzone.removeClass('stickymenu-active-show').addClass('stickymenu-active-hide');
				}

			}
		
			lastScrollTop = st;
		}
	}

	

	function isMobileMenuActive() {

		if( $( '.responsive-menu-wrap' ).is( ':visible' ) ) {
			$( 'body' ).addClass( 'mobile-mode-active' );
		} else {
			$( 'body' ).removeClass( 'mobile-mode-active' );
			if ( $( 'body' ).hasClass( 'menu-is-onscreen' ) ) {
				MobileMenuAction( 'resized' );
				SimpleMenuAction( 'resized' );
			}
		}

	}
	isMobileMenuActive();

	$( '.preloader-cover-logo' ).velocity( 'transition.slideUpOut', {
		duration: 1500
	});

	$( 'body' ).addClass( 'pace-done' );

	if ( $( 'body' ).hasClass( 'rightclick-block' ) ) {
		$( window ).on( 'contextmenu', function( b ) {
			if ( 3 === b.which ) {
				showCopyright();
				return false;
			}
		});
	}

	if ( $.fn.tilt ) {
		$( '.has-effect-tilt .gridblock-grid-element' ).tilt({
			maxTilt: 20,
			perspective: 550,
			easing: 'cubic-bezier(.03,.98,.52,.99)',
			speed: 800,
			glare: false,
			scale: 1.01
		});
	}

	function pageOwlcarouselsInit() {
		if ($('.post-format-media .owl-carousel-detect').length) {
			$('.post-format-media .owl-carousel-detect').each(function() {
				var thisID = $(this).data('id');
				var thisAutoplay = $(this).data('autoplay');
				var thisLazyload = $(this).data('lazyload');
				var thisSmartspeed = $(this).data('smartspeed');
				var thisType = $(this).data('type');
				var thisAutoplayTimeout = $(this).data('autoplaytimeout');
				thisAutoplay = typeof thisAutoplay !== 'undefined' ? thisAutoplay : 'false';
				thisLazyload = typeof thisLazyload !== 'undefined' ? thisLazyload : 'false';
				thisSmartspeed = typeof thisSmartspeed !== 'undefined' ? thisSmartspeed : '1000';
				thisAutoplayTimeout = typeof thisAutoplayTimeout !== 'undefined' ? thisAutoplayTimeout : '5000';
				thisType = typeof thisType !== 'undefined' ? thisType : 'slideshow';
				thisID = typeof thisID !== 'undefined' ? thisID : 'false';
				if (thisType=="testimony") {
					 $('#'+thisID).owlCarousel({
						items: 1,
						singleItem : true,
						scrollPerPage : false,
						pagination: true,
						autoplay: thisAutoplay,
						autoplayTimeout: thisAutoplayTimeout,
						autoplayHoverPause:true,
						autoHeight:true,
						animateOut: "animation-action fadeOut",
						animateIn: "animation-action fadeIn",
						nav : false,
						loop: true
					});
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
						navText : ["",""],
						singleItem : true
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
						margin:10,
						stagePadding: 10,
						smartSpeed: thisSmartspeed,
						autoplay: thisAutoplay,
						autoplayTimeout: thisAutoplayTimeout,
						lazyLoad: thisLazyload,
						nav: true,
						autoHeight : true,
						navText : ["",""],
						singleItem : true
					});
				}
				if (thisType !== "centercarousel" || thisType !== "flatcarousel" || thisType !== "testimony") {
					 $('#'+thisID).owlCarousel({
						items:1,
						loop:true,
						autoplay: thisAutoplay,
						smartSpeed: thisSmartspeed,
						autoplayTimeout: thisAutoplayTimeout,
						lazyLoad: thisLazyload,
						nav: true,
						autoHeight: true,
						navText : ["",""],
						singleItem : true
					});          
				}

			});
		}
	}

	if ( $.fn.imagesLoaded ) {
		$('.post-format-media .owl-carousel-detect').imagesLoaded( function() {
			pageOwlcarouselsInit();
		});

		$('.recently-owl-works-detect').imagesLoaded( function() {
			if ($('.recently-owl-works-detect').length) {
				$('.recently-owl-works-detect').each(function() {
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
						loop: false
					});

				});
			}
		});
	}

	function html5videoautoplay() {

		if ( $( '#videocontainer' ).length) {
			$( '#videocontainer' )[0].onplay = function () {
				$( '.fullscreen-video-play' ).hide();
			};

			$( document ).on( 'click', '.fullscreen-video-play', function (e) {
				var video = $( '#videocontainer' ).get(0);
				if (video.paused === false) {
					video.pause();
					video.muted = false;
				} else {
					video.play();
					video.muted = false;
				}

				return false;
			});

			$( document ).on( 'click', '.fullscreen-video-audio', function (e) {
				var video = $( '#videocontainer' ).get(0);
				if (video.muted === false) {
					video.muted = true;
				} else {
					video.muted = false;
				}

				return false;
			});
			
			$( document ).on( 'click', '#videocontainer', function (e) {
				var video = $(this).get(0);
				if (video.paused === false) {
					video.pause();
				} else {
					video.play();
				}

				return false;
			});
		}
	}
	html5videoautoplay();

	displayspecificBackgrounds();
	function displayspecificBackgrounds() {

	   /*
		 * BG Loaded
		 * 
		 *
		 * Copyright (c) 2014 Jonathan Catmull
		 * Licensed under the MIT license.
		 */
		$.fn.bgLoaded = function( custom ) {

			var self = this;

			// Default plugin settings
			var defaults = {
				afterLoaded : function(){
					this.addClass( 'bg-loaded' );
				}
			};

			// Merge default and user settings
			var settings = $.extend({}, defaults, custom);

			// Loop through element
			self.each( function() {
				var $this = $( this ),
					bgImgs = $this.css( 'background-image' ).split(', ');
				$this.data( 'loaded-count',0 );

				$.each( bgImgs, function( key, value ){
					var img = value.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
					if ( img !== 'none' ) {
						$('<img/>').attr( 'src', img ).on( 'load', function() {
							$(this).remove();
							$this.data( 'loaded-count', $this.data('loaded-count' ) +1 );
							if ( $this.data( 'loaded-count' ) >= bgImgs.length ) {
								settings.afterLoaded.call( $this );
							}
						});
					}
				});

			});
		};

		$( '.vertical-parallax-image' ).bgLoaded();
		$( '.column-has-backround-image' ).bgLoaded();
		$( '.site-back-cover' ).bgLoaded();
		$( '#heroimage-background' ).bgLoaded({
			afterLoaded : function(){
				this.parent( '#heroimage' ).addClass( 'bg-loaded' );
			}
		});
		$( '.photocard-image-container').bgLoaded();
		$( '.photocard-image-container').bgLoaded({
			afterLoaded : function(){
				this.parent( '.photocard-image-wrap' ).addClass( 'bg-loaded' );
			}
		});
		$( '.photocard-wrap-type-two' ).bgLoaded({
			afterLoaded : function(){
				this.parent('.photocard-image-two-wrap').addClass( 'bg-loaded' );
			}
		});
	}

	function fullscreenswiperSlides() {
		if (typeof Swiper != 'undefined') {
			if ( $( '.fullscreen-swiper-container' ).length) {

				var autoplaydata = [];
				var widescreenswiper_columns = 3;
				var mediumscreenswiper_columns = 2;
				var swiperID = '#' + $( '.fullscreen-swiper-container' ).data( 'id' );
				var columns = $( '.fullscreen-swiper-container' ).data( 'columns' );
				var getautoplay = $( '.fullscreen-swiper-container' ).data( 'autoplay' );
				console.log( columns );
				columns = typeof columns !== 'undefined' ? columns : '4';
				getautoplay = typeof getautoplay !== 'undefined' ? getautoplay : '5000';
				if ( getautoplay == '0' ) {
					autoplaydata = false;
				} else {
					autoplaydata.delay = getautoplay;
				}

				if ( columns == '1' ) {
					widescreenswiper_columns = 1;
					mediumscreenswiper_columns = 1;
				}

				if ( columns == '2' ) {
					widescreenswiper_columns = 2;
					mediumscreenswiper_columns = 2;
				}

				if ( columns == '3' ) {
					widescreenswiper_columns = 3;
					mediumscreenswiper_columns = 2;
				}

				if ( columns == '4' ) {
					widescreenswiper_columns = 4;
					mediumscreenswiper_columns = 3;
				}
				
				var swiper = new Swiper(swiperID, {
					pagination: {
						el: '.swiper-pagination',
						type: 'bullets',
						clickable: true,
					},
					paginationClickable: true,
					disableOnInteraction: true,
					loop: false,
					autoplay: autoplaydata,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					slidesPerView: columns,
					spaceBetween: 0,
					breakpoints: {
						1300: {
							slidesPerView: widescreenswiper_columns,
							spaceBetween: 0
						},
						1024: {
							slidesPerView: mediumscreenswiper_columns,
							spaceBetween: 0
						},
						768: {
							slidesPerView: mediumscreenswiper_columns,
							spaceBetween: 0
						},
						640: {
							slidesPerView: 1,
							spaceBetween: 0
						},
						320: {
							slidesPerView: 1,
							spaceBetween: 0
						}
					}
				});
			}
		}
	}
	fullscreenswiperSlides();

	function mtheme_findaccordions() {
		if ( $( '.mtheme-accordion-detect' ).length) {
			$( '.mtheme-accordion-detect' ).each(function() {
				var accordionID = $(this).data('accordionid');
				var accordionActive = $(this).data('activetab');
				accordionActive = typeof accordionActive !== 'undefined' ? accordionActive : '-1';
				if ( accordionActive == '-1' ) {
					accordionActive = 'false';
				}
				$( '#' + accordionID ).accordion({
					active: accordionActive,
					heightStyle: 'content',
					animate: {
						duration: 300,
						easing: 'easeInExpo'
					}
				});
			});
		}
	}
	mtheme_findaccordions();


	if ( $('body').hasClass('fullscreen-particles') ) {
		(function() {
		  var lastTime = 0;
		  var vendors = ['ms', 'moz', 'webkit', 'o'];
		  for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
			  window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
			  window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame'] || window[vendors[x]+'CancelRequestAnimationFrame'];
		  }

		  if (!window.requestAnimationFrame)
			  window.requestAnimationFrame = function(callback, element) {
				  var currTime = new Date().getTime();
				  var timeToCall = Math.max(0, 16 - ( currTime - lastTime ) );
				  var id = window.setTimeout(function() { callback( currTime + timeToCall ); }, 
					timeToCall);
				  lastTime = currTime + timeToCall;
				  return id;
			  };

		  if (!window.cancelAnimationFrame)
			  window.cancelAnimationFrame = function(id) {
				  clearTimeout(id);
			  };
		}());

		var update;
		update = function() {
		requestAnimationFrame(update);
		};
		requestAnimationFrame(update);
	}

	$( '.single-image-block' ).each(function() {
		var singleImage = $( this );
		singleImage.imagesLoaded( function() {
			singleImage.addClass( 'single-image-loaded' );
		});
	});

	$( '.social-sharing-toggle,.mobile-sharing-toggle' ).on( 'click', function() {
		$( 'body' ).addClass( 'social-sharing-on' );
	});
	$("#social-modal").on( 'click', function() {
		$( 'body' ).removeClass( 'social-sharing-on' );
	});

	function fullscreenYoutube() {
		if ( $.fn.tubular ) {
			if ( $( '.youtube-fullscreen-player' ).length) {
				var youtubeID = $( '#backgroundvideo' ).data( 'id' );
				var options = { videoId: youtubeID, wrapperZIndex: -1, start: 0, mute: false, repeat: true, ratio: 16/9 };
				$( '#backgroundvideo' ).tubular(options);
			}
		}
	}
	fullscreenYoutube();

	function displayWooProducts() {
		$.Velocity.RegisterEffect( 'woofadeinsteps', {
			calls: [ 
			  [ { opacity: [ 1, 0 ] } ]
			]
		});
		$( '.woocommerce .products li' ).velocity( 'woofadeinsteps', { stagger: 100 } );
	}
	displayWooProducts();

	function showCopyright() {
		$( '#dimmer' ).fadeIn( 'normal' , function() {
			$( 'body' ).addClass( 'dimmer-displayed' );
		});
		$( '#dimmer' ).on( 'click' , function() {
			$( this ).fadeOut( 'normal' , function() {
				$( 'body' ).removeClass( 'dimmer-displayed' );
			});
		});
	}

	$( 'body #static_slidecaption' ).addClass( 'display-content' );


	if ($( '#toggle-menu' ).length) {
 
		$( '#toggle-menu' ).on( 'click', function() {
			$( '#toggle-menu' ).toggleClass( 'toggle-menu-open' );
			$( 'body' ).toggleClass( 'minimal-menu-fadein sticky-menu-off' );
		});

	}

	function MobileMenuReverse() {
		$( '.mtree .display-menu-item-image' ).stop( true,true ).velocity( 'reverse' );
		$( 'ul.mtree > li' ).stop( true,true ).velocity( 'reverse' );
	}

	if ( $( '#mobile-toggle-menu' ).length ) {
		$( '#mobile-toggle-menu' ).on( 'click', function() {
			$( 'body' ).removeClass( 'cart-on' );
			if ( $( 'body' ).hasClass( 'menu-is-onscreen' ) ) {
				if ( ! $( 'body' ).hasClass( 'menu-is-closing' ) ) {
					MobileMenuAction('close');
				}
			} else {
				if ( !$('body').hasClass('menu-is-opening') ) {
					MobileMenuAction('open');
				}
			}
		});
		$(".responsive-menu-overlay").on( 'click', function() {
			MobileMenuAction('close');
			MobileMenuReverse();
		});

	}
	function MobileMenuAction(action) {

		if (action == "resized") {
			$( '#mobile-toggle-menu' ).removeClass( 'mobile-toggle-menu-open' );
			$( 'body' ).removeClass( 'body-dashboard-push-left' );
			$( '.responsive-mobile-menu' ).removeClass( 'menu-push-onscreen' );
			$( 'body' ).removeClass( 'menu-is-onscreen' );
			MobileMenuReverse();
		} else {
			$( '#mobile-toggle-menu' ).toggleClass( 'mobile-toggle-menu-open' );
			$( 'body' ).toggleClass( 'body-dashboard-push-left' );
		}

		if ( action == 'close' ) {
			$( 'body' ).addClass( 'menu-is-closing' );
			$( '.dashboard-columns' ).stop( true,true ).velocity( 'transition.slideUpOut', {
				stagger: 120,
				duration: 800,
				complete: function() {
					$( '.responsive-mobile-menu' ).toggleClass( 'menu-push-onscreen' );
					$( 'body' ).toggleClass( 'menu-is-onscreen' );

					MobileMenuReverse();
					$( 'body' ).removeClass( 'menu-is-closing' );
				}
			});


		}
		
		if ( action == 'open' ) {

			$( 'body' ).addClass( 'menu-is-opening' );
			$( '.responsive-mobile-menu' ).stop( true,true ).velocity( 'fadeIn', {
				complete: function() {
					$( '.responsive-mobile-menu' ).toggleClass( 'menu-push-onscreen' );
					$( 'body' ).toggleClass( 'menu-is-onscreen' );

					$( '.dashboard-columns' ).stop( true,true ).velocity( 'transition.slideUpIn', {
						stagger: 120,
						duration: 800,
						complete: function() {
							$( '.dashboard-columns' ).find( '.lazyload-after' ).addClass( 'lazyload' );
						}
					});

					animateDisplayMenuItems();
					$( 'body' ).removeClass( 'menu-is-opening' );
				}
			});
		}
	}

	function animateDisplayMenuItems() {

		$.Velocity.RegisterEffect( 'menuParentItems', {
			calls: [ 
			  [ { opacity: [ 1, 0 ] , bottom: [ 0, -5 ] } ]
			]
		});
		$( '.responsive-mobile-menu ul.mtree > li' ).css({'opacity':0 , 'bottom': -5}).stop(true,true).velocity('menuParentItems', {
			stagger: 100,
			complete: function() {
				$(this).addClass('menu-item-is-visible');
			}
		});
	}

	function SimpleMenuAction( action ) {

		if ( action == 'resized' ) {
			$( '#minimal-toggle-menu' ).removeClass( 'mobile-toggle-menu-open' );
			$( 'body' ).removeClass( 'body-dashboard-push-left' );
			$( '.simple-menu' ).removeClass( 'menu-push-onscreen' );
			$( 'body' ).removeClass( 'menu-is-onscreen' );
		} else {
			$( '#minimal-toggle-menu' ).toggleClass( 'mobile-toggle-menu-open' );
			$( 'body' ).toggleClass( 'body-dashboard-push-left' );
		}

		if ( action == 'open' ) {
			$( '.simple-menu' ).fadeOut( 'normal', function() {
				$( '.minimal-menu-overlay' ).fadeOut();
				$( '.simple-menu' ).toggleClass( 'menu-push-onscreen' );
				$( 'body' ).toggleClass( 'menu-is-onscreen' );
			});
		}
		
		if ( action == 'close') {
			$( '.simple-menu' ).fadeIn( 'normal', function() {
				$( '.minimal-menu-overlay' ).fadeIn();
				$( '.simple-menu' ).toggleClass( 'menu-push-onscreen' );
				$( 'body' ).toggleClass( 'menu-is-onscreen' );
			});
		}
	}

	function SidebarMenuAction( action ) {

		$( '#sidebarinfo-toggle-menu' ).toggleClass( 'sidebar-toggle-menu-open' );
		$( 'body' ).toggleClass( 'body-dashboard-push-left' );

		if ( action == 'close' ) {

			$( '.dashboard-columns' ).velocity( 'transition.slideUpOut', {
				stagger: 120,
				display: "flex",
				duration: 800
			});

			$( '.sidebarinfo-menu' ).velocity( 'fadeOut', {
				duration: 800,
				complete: function() {
					$( '.sidebarinfo-menu' ).toggleClass( 'sidebar-push-onscreen' );
					$( 'body' ).toggleClass( 'sidebar-is-onscreen' );
				}
			});



		}

		if ( action == 'open' ) {

			$( '.sidebarinfo-menu' ).toggleClass( 'sidebar-push-onscreen' );
			$( 'body' ).toggleClass( 'sidebar-is-onscreen' );
			$( '.dashboard-columns' ).find( '.lazyload-after' ).addClass( 'lazyload' );

			$( '.sidebarinfo-menu' ).velocity( 'fadeIn', {
				complete: function() {
					$( '.dashboard-columns' ).velocity( 'transition.fadeIn', {
						stagger: 120,
						display: "flex",
						duration: 800,
						complete: function() {}
					});
				}
			});
		}
	}

	if ( $( '#minimal-toggle-menu' ).length ) {
		$( '#minimal-toggle-menu' ).on( 'click', function() {
			if ( $( 'body' ).hasClass( 'menu-push-onscreen' ) ) {
				SimpleMenuAction( 'open' );
			} else {
				SimpleMenuAction( 'close' );
			}
		});
		$( '.minimal-menu-overlay' ).on( 'click', function() {
			SimpleMenuAction( 'close' );
		});
	}
	if ( $( '#sidebarinfo-toggle-menu' ).length ) {
		$( '#sidebarinfo-toggle-menu' ).on( 'click', function() {
			if ( $( 'body' ).hasClass( 'sidebar-is-onscreen' ) ) {
				SidebarMenuAction( 'close' );
			} else {
				SidebarMenuAction( 'open' );
			}
		});
		$( '.sidebar-menu-overlay' ).on( 'click', function() {
			SidebarMenuAction( 'close' );
		});
	}

	$( window ).resize(function() {
		$( 'body' ).removeClass( 'cart-on' );
		isMobileMenuActive();
	});

	function fotoramaResizer() {
		if ( $.fn.fotorama ) {
			var fotorama_window_width = $( window ).width();

			if ( $( 'body' ).hasClass( 'menu-is-vertical' ) ) {
				if (fotorama_window_width < 1025) {
					$( '#fotorama-container-wrap' ).addClass( 'fotorama-fullwidth' );
				} else {
					$( '#fotorama-container-wrap' ).removeClass( 'fotorama-fullwidth' );
				}
			}

			var fotorama_width = fotorama_window_width;
			var fotorama_header_height = 0;
			fotorama_header_height = $( '.outer-wrap' ).outerHeight();

			if ( $( 'body' ).hasClass( 'top-header-present' ) ) {
				fotorama_header_height = fotorama_header_height + 35;
			}
			if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
				fotorama_header_height = fotorama_header_height + 32;
			}
			if ( $( 'body' ).hasClass( 'compact-menu' ) ) {
				fotorama_header_height = $('.outer-wrap').outerHeight();
			}
			var fotorama_footer_height = $( '.fullscreen-footer-wrap' ).outerHeight();
			var fotorama_outer = fotorama_header_height + fotorama_footer_height;
			var fotorama_height = $( window ).height() - fotorama_outer;

			if ( $( 'body' ).hasClass( 'fotorama-style-contain' ) ) {
				if ( $( 'body' ).hasClass( 'boxed-site-layout' ) ) {
					fotorama_width = fotorama_window_width - 100;
					$( '#fotorama-container-wrap' ).css( 'left', '50px' );
				}
				if ( fotorama_window_width < 1025 ) {
					fotorama_header_height = $( '.mobile-menu-toggle' ).outerHeight();
					fotorama_outer = fotorama_header_height + fotorama_footer_height;

					fotorama_height = $( window ).height() - fotorama_outer;
					$( '#fotorama-container-wrap' ).css( 'left', '0' );
					fotorama_width = '100%';
				}
			} else {
				fotorama_height = '100%';
				fotorama_header_height = 0;
				fotorama_width = '100%';
			}

			if ( $('body').hasClass( 'fullscreen-mode-on' ) ) {
				fotorama_height = '100%';
				fotorama_header_height = 0;
				fotorama_width = '100%';
				$( '#fotorama-container-wrap' ).css( 'left', '0' );
			}

			var adminbar_adjuster = 0;
			if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
				adminbar_adjuster = 32;
			}
			fotorama_height = $( window ).height();
			if ( $( 'body' ).hasClass( 'menu-is-horizontal' ) ) {
				fotorama_height = $(window).height() - adminbar_adjuster;
			}
			if ( $( 'body' ).hasClass( 'fotorama-style-contain' ) ) {
				fotorama_height = $(window).height() - 200;
				if ( $( 'body' ).hasClass( 'fotorama-thumbnails-disable' ) ) {
					fotorama_height = $(window).height() - 120;
				}
				if ( $( 'body' ).hasClass( 'centered-logo' ) ) {
					fotorama_height = $(window).height() - 280;
					if ( $( 'body' ).hasClass( 'fotorama-thumbnails-disable' ) ) {
						fotorama_height = $(window).height() - 220;
					}
				}
			}
			if (fotorama_window_width < 1051) {
				fotorama_height = $(window).height() - 65;
			}
			if ( $( 'body' ).hasClass('menu-is-vertical') ) {
				fotorama_height = $(window).height() - adminbar_adjuster;
			}

			if ( $( 'body' ).hasClass( 'fotorama-style-cover' ) ) {
				fotorama_height = $(window).height();
			}

			if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
				fotorama_height = fotorama_height - 32;
				if (fotorama_window_width < 1051) {
					fotorama_height = $(window).height() - 100;
				}
			}
			$( '.fotorama' ).fotorama({
				height: fotorama_height,
				width: fotorama_width
			});
		}
	}

	function fotoramaToggleHeader() {

		if ($.fn.fotorama) {
			var slide_color = $( '#slideshow-data .slide-0' ).data( 'color' );
			var slide_header = $( '#slideshow-data .slide-0' ).data( 'header' );
			$( 'body' ).removeClass( 'fullscreen-slide-bright' ).removeClass( 'fullscreen-slide-dark' );
			$( 'body' ).removeClass( 'fullscreen-header-bright' ).removeClass( 'fullscreen-header-dark' );
			if ( slide_color != undefined) {
				$( 'body' ).addClass( 'fullscreen-slide-'+ slide_color  );
				$( 'body' ).addClass( 'fullscreen-header-'+ slide_header  );
			} else {
				$( 'body' ).addClass( 'fullscreen-slide-bright' );
				$( 'body' ).addClass( 'fullscreen-header-bright' );
			}

			var proceed_change_ui = true;
			$( '.fotorama' )
			  .on( 'fotorama:show', function ( e, fotorama ) {

				var slide_color = $( '#slideshow-data .slide-' + fotorama.activeIndex ).data( 'color' );
				var slide_header = $( '#slideshow-data .slide-' + fotorama.activeIndex ).data( 'header' );
				if ( slide_color != undefined) {
					if ( proceed_change_ui ) {
						$( 'body' ).removeClass( 'fullscreen-slide-bright' ).removeClass( 'fullscreen-slide-dark' );
						$( 'body' ).removeClass( 'fullscreen-header-bright' ).removeClass( 'fullscreen-header-dark' );
						$( 'body' ).addClass( 'fullscreen-slide-'+ slide_color  ).addClass( 'fullscreen-header-'+ slide_header  );
						$( 'body' ).addClass( 'fullscreen-slide-'+ slide_color  ).addClass( 'fullscreen-header-'+ slide_header  );
					}
					$( '#slideshow-data li' ).removeClass( 'data-active-slide' );
					$( '#slideshow-data .slide-' + fotorama.activeIndex ).addClass( 'data-active-slide' );
				}
			});
		} 
	}

	$(window).resize(function() {
		fotoramaResizer();
	});

	$(window).on("debouncedresize", function( event ) {
		if ($.fn.isotope) {
			isotopeReady();
		}
	});

	fotoramaResizer();
	fotoramaToggleHeader();

	function isotopeReady() {
		// initialize isotope
		if ( $.fn.isotope ) {

			$isotopeContainer.imagesLoaded( function() {

				$isotopeContainer.parent().addClass( 'isotope-container-displayed' );

				var itemReveal = Isotope.Item.prototype.reveal;
				Isotope.Item.prototype.reveal = function() {
					itemReveal.apply(this, arguments);
					$(this.element).removeClass( 'isotope-hidden' );
					$(this.element).addClass( 'isotope-displayed' );
				};

				var itemHide = Isotope.Item.prototype.hide;
				Isotope.Item.prototype.hide = function() {
					itemHide.apply(this, arguments);
					$(this.element).addClass( 'isotope-hidden' );
					$(this.element).removeClass( 'isotope-displayed' );
				};

				if ( $( $isotopeContainer ).hasClass( 'gridblock-masonary' ) ) {

					var photow_window_width = $( '.container' ).width();
					if ( photow_window_width === null ) {
						photow_window_width = $( '.container-edge-to-edge' ).width();
					}
					var wallContainer_w = $( $isotopeContainer ).width() - 0.5;

					var number_of_columns = $( $isotopeContainer ).attr( 'data-columns' );

					var fourcolumn = '25%',
						threecolumn = '33.3333%',
						twocolumn = '50%',
						onecolumn = '100%';

					if ($($isotopeContainer).hasClass('thumnails-gutter-active')) {
						fourcolumn = '25%';
						threecolumn = '33.3333%';
						twocolumn = '50%';
						onecolumn = '100%';
						wallContainer_w = $( $isotopeContainer ).width() - 0.5;
					}

					if (number_of_columns == 4) {
						$($isotopeContainer).find( '.gridblock-element' ).css( 'width', fourcolumn );
					}
					if (number_of_columns == 3) {
						$($isotopeContainer).find('.gridblock-element').css( 'width', threecolumn );
					}
					if (number_of_columns == 2) {
						$($isotopeContainer).find( '.gridblock-element' ).css( 'width', twocolumn );
					}
					if (number_of_columns == 1) {
						$($isotopeContainer).find( '.gridblock-element' ).css( 'width', onecolumn );
					}

					if (photow_window_width < 1035) {
						number_of_columns = 3;
						$($isotopeContainer).find( '.gridblock-element' ).css( 'width', threecolumn );
					}
					if (photow_window_width < 800) {
						number_of_columns = 2;
						$($isotopeContainer).find( '.gridblock-element' ).css( 'width', twocolumn );
					}
					if (photow_window_width < 500) {
						number_of_columns = 2;
						$($isotopeContainer).find( '.gridblock-element' ).css( 'width', onecolumn );
					}

					if ( $( 'body.rtl' ).length == 1 ) {
							$isotopeContainer.isotope({
								isOriginLeft: false,
								resizable: false, // disable normal resizing
								masonry: {
									gutterWidth: 0,
									columnWidth: wallContainer_w / number_of_columns
								}
							});
					} else {
							$isotopeContainer.isotope({
								resizable: false, // disable normal resizing
								masonry: {
									gutterWidth: 0,
									columnWidth: wallContainer_w / number_of_columns
								}
							});
					}

				} else {
					if ( $( 'body.rtl' ).length == 1 ) {
							$isotopeContainer.isotope({
								isOriginLeft: false,
								layoutMode: 'fitRows',
								transitionDuration: '0.8s',
								masonry: {
									gutterWidth: 0
								}
							});
					} else {
						
							$isotopeContainer.isotope({
								layoutMode: 'fitRows',
								transitionDuration: '0.8s',
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

				if ( $( $isotopeContainer ).hasClass( 'relayout-on-image-load' ) ) {
					$isotopeContainer.each( function(){
						var $curr_module = $( this );

						var layoutupdate = ( function(){
							$curr_module.isotope( 'layout' );
						});

						this.addEventListener( 'load', layoutupdate, true );   
					});
				}
			});
		}
	}
	isotopeReady();

	if ( $.fn.hoverIntent ) {
		$( '.outer-wrap' ).hoverIntent({
			over: mainMenuOn,
			out: mainMenuOff
		});
	} else {
		$( '.outer-wrap' ).mouseenter(function() {
			mainMenuOn();
		})
		.mouseleave(function() {
			mainMenuOff();
		});
	}
	var hoverOutMenu;
	function mainMenuOn() {
		clearTimeout(hoverOutMenu);
		$( 'body' ).addClass( 'main-menu-on' );
	}
	function mainMenuOff() {
		hoverOutMenu = setTimeout(function() {
			$( 'body' ).removeClass( 'main-menu-on' );
		}, 600 );
	}

	$('#sidebar').find('.lazyload-after').addClass('lazyload');

	$( '.side-dashboard-toggle' ).on( 'click', function() {
		$( 'body' ).toggleClass( 'body-dashboard-push-right' );
		$( '.side-dashboard-wrap' ).toggleClass( 'dashboard-push-onscreen' );
	});

	function displayMenuItems() {
		var duration = 400;
		var easing = 'easeInOutQuad';
		$.Velocity.Redirects.menuitemlist = function ( element, options, index, size ) {
		  $.Velocity.animate( element, { 
			opacity: [ 1,0 ]
		  }, {
			delay: index * ( duration/size/2 ),
			duration: duration,
			easing: easing,
			complete: function() {
				$( 'body' ).addClass( 'display-menu-done' );
			}
		  });
		};
		$( '.sf-menu > li > a' ).velocity( 'menuitemlist' );
	}

	$( '.toggle-shortcode' ).on( 'click', function() {
		$( this ).toggleClass( 'active' ).next().slideToggle( 'fast' );
		return false;
	});

	$( '#popularposts_list li:even,#recentposts_list li:even' ).addClass( 'even' );
	$( '#popularposts_list li:odd,#recentposts_list li:odd' ).addClass( 'odd' );

	$( '.service-column .service-item:even' ).addClass( 'service-order-even' );
	$( '.service-column .service-item:odd' ).addClass( 'service-order-odd' );

	// fade in #back-top
	$( function() {
		$( window ).scroll( function() {
			if ( $( this ).scrollTop() > 500 ) {
				$( 'body' ).addClass( 'goto-top-active' );
			} else {
				$( 'body' ).removeClass( 'goto-top-active' );
			}
			if ( $( this ).scrollTop() > 158 ) {
				$( 'body' ).addClass( 'sticky-nav-active' );
			} else {
				$( 'body' ).removeClass( 'sticky-nav-active' );
			}
		});
	});
	$( '#goto-top' ).on( 'click', function() {
		$( 'body' ).velocity( 'scroll', 1000 );
		return false;
	});
	$( '.pricing-column ul' ).each(function( e ) {
		$( this ).find( 'li:even' ).addClass( 'even' );
		$( this ).find( 'li:odd' ).addClass( 'odd' );
	});


	var manualmode = false;
	if ($.fn.multiscroll) {
		
		function fullscreenMultiscroll() {
			$('#multiscroll').multiscroll({
				verticalCentered : true,
				scrollingSpeed: 700,
				easing: 'easeInQuart',
				menu: false,
				sectionsColor: [],
				navigation: true,
				navigationPosition: 'right',
				navigationColor: '#000',
				navigationTooltips: [],
				loopBottom: true,
				loopTop: true,
				css3: true,
				paddingTop: 0,
				paddingBottom: 0,
				normalScrollElements: null,
				keyboardScrolling: true,
				touchSensitivity: 5,

				//responsive
				responsiveWidth: 1000,
				responsiveHeight: 0,
				responsiveExpand: false,

				// Custom selectors
				sectionSelector: '.ms-section',
				leftSelector: '.ms-left',
				rightSelector: '.ms-right',

				//events
				onLeave: function(index, nextIndex, direction){},
				afterLoad: function(anchorLink, index){},
				afterRender: function(){},
				afterResize: function(){},
			});
		}
		if ($('#multiscroll').length) {
			fullscreenMultiscroll();
			$('html').addClass('multislider-active');

			if ($(window).width() < 768) {
				$.fn.multiscroll.destroy();
			} else {
				$.fn.multiscroll.destroy();
				$.fn.multiscroll.build();
				$(window).resize(function() {
					$('body,html').scrollTop( 0 );
				});
			}
		}

		function scrollMultiscroll() {
			if (!manualmode) {
				$.fn.multiscroll.moveSectionDown(); 
			}
		}
		if($('#fullscreen-multiscroll').is(':visible')){
			setInterval(scrollMultiscroll, 5000);
		}
		
	}

	// For Chrome
	window.addEventListener('mousewheel', mouseWheelEvent, {passive: true});

	// For Firefox
	window.addEventListener('DOMMouseScroll', mouseWheelEvent, {passive: true});

	function mouseWheelEvent(e) {
	    manualmode = true;
	}
	$( "body" ).mousedown(function() {
		manualmode = true;
	});

	// WooCommerce Codes
	// Thumnail display for secondary image

	var header_cart_toggle = $( '.header-cart-toggle' );

	$( 'body' ).on( 'click', '.header-cart-toggle', function() {
		$( 'body' ).toggleClass('cart-on');
		if ($('body').hasClass('menu-is-onscreen')) {
			MobileMenuAction('resized');
		}
	});
	$( 'body' ).on( 'click', '.header-cart-close', function() {
		$( 'body' ).removeClass( 'cart-on' );
	});
	$( '.container-wrapper' ).on( 'click', function() {
		$( 'body' ).removeClass( 'cart-on' );
	});

	var woocommerce_active = $( 'body.woocommerce' );
	if (woocommerce_active.length) {
		$( 'ul.products li.mtheme-hover-thumbnail' ).mouseenter(function() {
			var woo_secondary_thumnail = $(this).find( '.mtheme-secondary-thumbnail-image' ).attr( 'src' );
			if ( woo_secondary_thumnail !== undefined ) {
				$( this ).find( '.wp-post-image' ).removeClass( 'woo-thumbnail-fadeInDown' ).addClass( 'woo-thumbnail-fadeOutUp' );
				$( this ).find( '.mtheme-secondary-thumbnail-image' ).removeClass( 'woo-thumbnail-fadeOutUp' ).addClass( 'woo-thumbnail-fadeInDown' );
			}
		})
		.mouseleave(function() {
			var woo_secondary_thumnail = $(this).find( '.mtheme-secondary-thumbnail-image' ).attr( 'src' );
			if ( woo_secondary_thumnail !== undefined ) {
				$( this ).find( '.wp-post-image' ).removeClass( 'woo-thumbnail-fadeOutUp' ).addClass( 'woo-thumbnail-fadeInDown' );
				$( this ).find( '.mtheme-secondary-thumbnail-image' ).removeClass( 'woo-thumbnail-fadeInDown' ).addClass( 'woo-thumbnail-fadeOutUp' );
			}
		});


		var woocommerce_ordering = $( '.woocommerce-page .woocommerce-ordering select' );
		if ((woocommerce_ordering).length) {
			var woocommerce_ordering_curr = $( '.woocommerce-ordering select option:selected' ).text();
			var woocommerce_ordering_to_ul = woocommerce_ordering
				.clone()
				.wrap( '<div></div>' )
				.parent().html()
				.replace(/select/g, "ul")
				.replace(/option/g, "li")
				.replace(/value/g, "data-value");

			$( '.woocommerce-ordering' )
				.prepend( '<div class="mtheme-woo-order-selection-wrap"><div class="mtheme-woo-order-selected-wrap"><span class="mtheme-woo-order-selected">' + woocommerce_ordering_curr + '</span><i class="woo-sorter-dropdown ion-ios-settings"></i></div><div class="mtheme-woo-order-list">' + woocommerce_ordering_to_ul + '</div></div>' );
		}

		$(function() {
			$( '.mtheme-woo-order-selected-wrap' ).on( 'click', function() {
				$( '.mtheme-woo-order-list ul' ).slideToggle( 'fast' );
				$( '.woo-sorter-dropdown' ).toggleClass( 'ion-ios-settings-strong' ).toggleClass( 'ion-ios-close-empty' );
			});
			$('.mtheme-woo-order-list ul li').on( 'click', function() {
				//Set value
				var selected_option = $( this ).data( 'value' );
				$( '.woocommerce-page .woocommerce-ordering select' ).val(selected_option).trigger( 'change' );

				$( '.mtheme-woo-order-selected' ).text( $( this ).text() );
				$( '.mtheme-woo-order-list' ).slideUp( 'fast' );
				$( this ).addClass( 'current' );
				e.preventDefault();
			});
		});
	}

});

//
(function($) {
	"use strict";

	$(window).on('load',function () {

		$( 'body' ).addClass( 'page-has-loaded' );
		if ( ! $( 'body' ).hasClass( 'mobile-detected' ) ) {
			setTimeout( function() {
				$( 'body' ).addClass( 'preloader-done' );
				$( '.loading-spinner-detect' ).velocity( 'fadeOut', {
					duration: 350
				});
			}, 700 );
		}

		setTimeout(function() {
			$( 'body' ).addClass( 'reveal-specific-bg' );
		}, 800 );

		setTimeout(function() { rareHeaderElements(); }, 825);

		setTimeout(function() {
			$( 'body' ).addClass( 'reveal-single-image' );
		}, 850 );

		setTimeout(function() { revealspecificElements(); }, 1000);

		function rareHeaderElements() {
			if ( $.fn.waypoint ) {
				$( '.proofing-header-is-animated' ).waypoint(function() {
					$(this).removeClass( 'proofing-header-is-animated' ).addClass( 'animation-action' );
				}, {
					offset: '90%'
				});
			}
		}

		function revealspecificElements() {

			// reveal all items after init
			$( '#gridblock-container' ).addClass( 'is-showing-items' );

			var i = 0;
			$( '#gridblock-container,.thumbnails-grid-container,.gridblock-metro' ).each(function() {
				$( this ).find( '.grid-animate-display-all' ).each( function( counter ) {
					$( this )
						.velocity( { opacity:1 }, 500 );
				});
			});
			$( '.fotorama__nav__shaft' ).each(function() {
				$( this ).find( '.fotorama__thumb' ).each(function( counter ) {
					$( this )
						.delay( ++i * 20 + Math.random() * 1000 )
						.velocity( { opacity:1 }, 500 );
				}).promise().done( function() { $( '.fotorama__nav__shaft .fotorama__thumb-border' ).velocity( { opacity:1 }, 500 ); } );
			});

		}

		function jPlayerSeek() {
			if ( $.fn.jPlayer ) {
				$( '.single-jplay-video-postformat' ).each(function() {
					var jplay_video_m4v = $( this ).data( 'm4v' );
					var jplay_video_ogv = $( this ).data( 'ogv' );
					var jplay_video_poster = $( this ).data( 'poster' );
					var jplay_video_swfpath = $( this ).data( 'swfpath' );
					var jplay_video_id = $( this ).data( 'id' );
					var jplay_video_videofiles = $( this ).data( 'videofiles' );
					$( '#jquery_jplayer_'+jplay_video_id ).jPlayer({
						ready: function () {
							$( this ).jPlayer( 'setMedia', {
								m4v: jplay_video_m4v,
								ogv: jplay_video_ogv,
								poster: jplay_video_poster
							}).jPlayer( 'stop' );
						},
						play: function() {
							$(this).jPlayer( 'pauseOthers' );
						},
						swfPath: jplay_video_swfpath,
						supplied: jplay_video_videofiles,
						size: {
							width: '100%',
							height: 'auto',
							cssClass: 'jp-video-360p'
						},
						cssSelectorAncestor: '#jp_container_' + jplay_video_id
					});
				});
				$( '.single-jplay-audio-postformat' ).each(function() {
					var jplay_audio_mp3 = $( this ).data( 'mp3' );
					var jplay_audio_m4a = $( this ).data( 'm4a' );
					var jplay_audio_oga = $( this ).data( 'ogv' );
					var jplay_audio_swfpath = $( this ).data( 'swfpath' );
					var jplay_audio_id = $( this ).data('id');
					var jplay_audio_audiofiles = $( this ).data( 'audiofiles' );
					$( '#jquery_jplayer_'+jplay_audio_id ).jPlayer({
						ready: function () {
							$( this ).jPlayer( 'setMedia', {
								mp3: jplay_audio_mp3,
								m4a: jplay_audio_m4a,
								oga: jplay_audio_oga,
								end: ''
							}).jPlayer( 'stop' );
						},
						play: function() {
							$(this).jPlayer( 'pauseOthers' );
						},
						swfPath: jplay_audio_swfpath,
						supplied: jplay_audio_audiofiles,
						cssSelectorAncestor: '#jp_interface_' + jplay_audio_id
					});
				});
				if ( $( '.fullscreenslideshow-audio-player' ).length ) {
					var jplay_audio_mp3 = $( '.fullscreenslideshow-audio-player' ).data( 'mp3' );
					var jplay_audio_m4a = $( '.fullscreenslideshow-audio-player' ).data( 'm4a' );
					var jplay_audio_oga = $( '.fullscreenslideshow-audio-player' ).data( 'ogv' );
					var jplay_audio_swfpath = $( '.fullscreenslideshow-audio-player' ).data( 'swfpath' );
					var jplay_audio_id = $( '.fullscreenslideshow-audio-player' ).data( 'id' );
					var jplay_audio_audiofiles = $( '.fullscreenslideshow-audio-player' ).data( 'audiofiles' );
					var jplay_audio_volume = $( '.fullscreenslideshow-audio-player' ).data( 'volume' );
					$( '#jquery_jplayer_' + jplay_audio_id ).jPlayer({
						ready: function () {
							$( this ).jPlayer( 'setMedia', {
								mp3: jplay_audio_mp3,
								m4a: jplay_audio_m4a,
								oga: jplay_audio_oga,
								end: ''
							}).jPlayer( 'volume', jplay_audio_volume );
						},
						play: function() {
							$( this ).jPlayer( 'pauseOthers' );
						},
						ended: function() {
							$( this ).jPlayer( 'play' );
						},
						swfPath: jplay_audio_swfpath,
						supplied: jplay_audio_audiofiles,
						cssSelectorAncestor: '#jp_interface_' + jplay_audio_id
					});
				}
			}
		}
		jPlayerSeek();

		function gridRotatorUnit() {
			if ( $.fn.gridrotator ) {
				if ( $( '.ri-grid' ).length ) {
					var gridSelect = ( '.ri-grid' );
					var gridID = '#' + $( gridSelect ).data( 'id' );
					var gridTransition = $( gridSelect ).data( 'transition' );
					var slideshowstatus = $( gridSelect ).data( 'slideshow' );
					var gridColumns = $( gridSelect ).data( 'columns' );
					var gridRows = $( gridSelect ).data( 'rows' );
					var responsivegridColumns = $( gridSelect ).data( 'responsivecolumns' );

					gridColumns = typeof gridColumns !== 'undefined' ? gridColumns : '8';
					gridRows = typeof gridRows !== 'undefined' ? gridRows : '2';
					responsivegridColumns = typeof gridColumns !== 'undefined' ? gridColumns : '8';
					gridTransition = typeof gridTransition !== 'undefined' ? gridTransition : 'random';
					slideshowstatus = typeof slideshowstatus !== 'undefined' ? slideshowstatus : false;

					$( gridID ).gridrotator({
						rows : gridRows,
						columns : gridColumns,
						maxStep : 4,
						animType : gridTransition,
						preventClick : false,
						slideshow : slideshowstatus,
						interval : 4000,
						onhover : false,
						w1024 : {
							rows : gridRows,
							columns : gridColumns
						},
						w768 : {
							rows : gridRows,
							columns : responsivegridColumns
						},
						w480 : {
							rows : gridRows,
							columns : responsivegridColumns
						},
						w320 : {
							rows : gridRows,
							columns : responsivegridColumns
						},
						w240 : {
							rows : gridRows,
							columns : responsivegridColumns
						},
					});
				}
			}
		}
		if ( ! $("body").hasClass("elementor-editor-active") ) {
			if ( $('#instagram-grid-gen').length ) {
				var populateimages = function() {
		
					var r = $.Deferred();
				
					var imageset_arr = new Array();
					var linkset_arr = new Array();

					$( '#insta-grid-id-detect #sbi_images img' ).each( function() {
						if ( !$(this).hasClass('emoji') ) {
							var instaimage = $(this).attr('src');
							imageset_arr.push( instaimage );
						}
					});
					$( '#insta-grid-id-detect #sbi_images a.sbi_photo' ).each( function() {
						var instalink = $(this).attr('href');
						linkset_arr.push( instalink );
					});
					var totalimages = imageset_arr.length;
					var imagecount = 0;
					var linkcount = 0;

					$( '.insta-grid-wrap ul img' ).each( function() {
						if ( imagecount <= totalimages ) {
							$( this ).attr( 'data-src', imageset_arr[ imagecount ] );
						}
						imagecount++;
					} );
					$( '.insta-grid-wrap ul .instagram-photos-link' ).each( function() {
						if ( linkcount < totalimages ) {
							$( this ).attr( 'href', linkset_arr[ linkcount ] );
							$( this ).parent( '.gridblock-grid-element' ).addClass( 'insta-image-present' ).removeClass( 'insta-image-absent' );
						}
						linkcount++;
					} );
					$('.insta-grid-wrap ul li.insta-image-absent').remove();
					$('#instagram-grid-gen').remove();
					return r;
				
				};
				window.sbi_custom_js = function(){
					populateimages().done( gridRotatorUnit() );
				}
				setTimeout(function(){
					if ( $('#instagram-grid-gen').parent().length) {
						populateimages().done( gridRotatorUnit() );
					}
				}, 1000);
			} else {
				gridRotatorUnit();
			}
		}
	});
})(jQuery);
