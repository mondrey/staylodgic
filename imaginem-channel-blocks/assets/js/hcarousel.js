(function($) {
$(window).on( 'load', function() {
    "use strict";

    if (typeof Swiper != "undefined") {
        var swipercontainer = $( '.shortcode-multislider-container' );
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
            observeParents: true,
            observeSlideChildren: true,
            observer: true,
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

})
})(jQuery);