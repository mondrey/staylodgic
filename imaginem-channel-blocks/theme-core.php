<?php
class Theme_Core {

    public function __construct() {
        $this->theme_actions();
        $this->include_shortcodes();
        $this->themecore_load_demoimporter();
        $this->themecore_load_custom_posts();
        $this->themecore_load_availablity_calendar();
        $this->themecore_load_theme_widgets();
    }

    private function theme_actions() {
        add_action( 'wp_enqueue_scripts', array( $this, 'mtheme_load_front_end_scripts_styles') );
        add_action( 'admin_enqueue_scripts', array( $this, 'mtheme_load_admin_styles') );
        add_action( 'wp_ajax_set-menu-item-thumbnail', array( $this, 'wp_ajax_set_menu_item_thumbnail' ) );
        add_action( 'admin_head-nav-menus.php', array( $this, 'mthememenu_image_admin_head_nav_menus_action' ) );

        add_action( 'admin_init', array( $this, 'themecore_add_post_boxes' ) );
        add_action( 'admin_init', array( $this, 'themecore_add_page_box' ) );
        // add_action( 'admin_init', array( $this, 'themecore_clientitemmetabox_init' ) );
        add_action( 'admin_init', array( $this, 'themecore_reservationsitemmetabox_init' ) );
        // add_action( 'admin_init', array( $this, 'themecore_fullscreenitemmetabox_init' ) );
        // add_action( "admin_init", array( $this, 'themecore_portfolioitemmetabox_init' ) );
        // add_action( 'admin_init', array( $this, 'themecore_proofingitemmetabox_init' ) );
        // add_action( 'admin_init', array( $this, 'themecore_photostoryitemmetabox_init' ) );
        add_action( 'admin_init', array( $this, 'themecore_roomitemmetabox_init' ) );

        add_action( 'init', array( $this, 'themecore_load_textdomain' ) );
        add_action( 'init', array( $this, 'themecore_load_metaboxes' ) );
        add_action( 'admin_init', array( $this, 'themecore_woocommerceitemmetabox_init' ) );
    }

    public function themecore_load_textdomain() {
        load_plugin_textdomain( 'imaginem-blocks-ii', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
    }

    public function themecore_load_demoimporter() {
        require_once (plugin_dir_path( __FILE__ ) . '/demo-import/class-imaginemcore-demo-importer.php');
    }

    public function themecore_load_custom_posts() {
        // require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-fullscreen-posts.php');
        // require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-portfolio-posts.php');
        require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-reservation-posts.php');
        // require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-proofing-posts.php');
        //require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-photostory-posts.php');
        require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-room-posts.php');
    }

    public function themecore_load_availablity_calendar() {
        require_once (plugin_dir_path( __FILE__ ) . '/calendar/availability-calendar.php');
    }

    public function themecore_load_theme_widgets() {
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/about-me/about-me-widget.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/portfolio-gallery.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/recent.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/popular.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/social.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/flickr.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/address.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/video.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/portfolio-related-list.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/portfolio-type.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/event-list.php');
        require_once ( plugin_dir_path( __FILE__ ) . 'widgetized/event-type.php');
    }
    public function themecore_load_metaboxes() {
        require_once (plugin_dir_path( __FILE__ ) . '/includes/google-fonts.php');
        require_once (plugin_dir_path( __FILE__ ) . '/includes/theme-gens.php');
        require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxgen/metaboxgen.php');
        require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/page-metaboxes.php');
        // require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/client-metaboxes.php');
        // require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/portfolio-metaboxes.php');
        // require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/proofing-metaboxes.php');
        require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/post-metaboxes.php');
        // require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/fullscreen-metaboxes.php');
        require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/reservation-metaboxes.php');
        require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/woocommerce-metaboxes.php');
        //require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/photostory-metaboxes.php');
        require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/room-metaboxes.php');
    }

    public function mthememenu_image_admin_head_nav_menus_action() {
        wp_enqueue_script( 'menu-image-admin', plugins_url( 'menu-image-admin.js', __FILE__ ), array( 'jquery' ) );
        wp_localize_script(
            'menu-image-admin', 'menuImage', array(
                'l10n'     => array(
                    'uploaderTitle'      => __( 'Chose menu image', 'menu-image' ),
                    'uploaderButtonText' => __( 'Select', 'menu-image' ),
                ),
                'settings' => array(
                    'nonce' => wp_create_nonce( 'update-menu-item' ),
                ),
            )
        );
        wp_enqueue_media();
        wp_enqueue_style( 'editor-buttons' );
    }

	public function include_shortcodes() {
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/shortcode-functions.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/general.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/blog.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/slideshow.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/video.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/audio.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/staff.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/portfolio-blocks.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/fullscreen-blocks.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/proofing.php');
		require_once ( plugin_dir_path( __FILE__ ) . '/shortcodes/instagram.php');
	}

    public function mtheme_load_admin_styles() {
		wp_register_style('chosen', plugin_dir_url( __FILE__ ) .'assets/js/chosen/chosen.css', array(), false, 'screen' );
		wp_register_script('chosen', plugin_dir_url( __FILE__ ) .'assets/js/chosen/chosen.jquery.js', array( 'jquery' ),null, true );
		wp_register_style('flatpickr', plugin_dir_url( __FILE__ ) .'assets/js/flatpickr/flatpickr.min.css', array(), '1.0', 'screen' );
		wp_register_script('flatpickr', plugin_dir_url( __FILE__ ) .'assets/js/flatpickr/flatpickr.js', array( 'jquery' ),'1.0', true );
		wp_register_script('admin-post-meta', plugin_dir_url( __FILE__ ) .'admin/js/admin-post-meta.js', array( 'jquery','wp-api','wp-data'),null, true );
		wp_register_script('menu-image-admin', plugin_dir_url( __FILE__ ) .'admin/js/menu-image-admin.js', array( 'jquery' ),null, true );
		wp_register_style('menu-image-css', plugin_dir_url( __FILE__ ) .'admin/js/menu-image-admin.css', array(), false, 'screen' );
		wp_register_style('themecore-admin-styles', plugin_dir_url( __FILE__ ) .'admin/css/style.css',false, 'screen' );

        wp_enqueue_style( 'room-reservation-plugin-availability-styles', plugin_dir_url( __FILE__ ) .'admin/css/availability-calendar.css',false, 'screen' );
        wp_enqueue_script( 'room-reservation-plugin-availability-scripts', plugin_dir_url( __FILE__ ) .'admin/js/availability-calendar.js', array( 'jquery' ),null, true );

        if ( function_exists('get_current_screen') ) {
            $current_admin_screen = get_current_screen();
        }
        if (isSet($current_admin_screen)) {
            if ($current_admin_screen->base == "nav-menus") {
				wp_enqueue_media();
				wp_enqueue_script('menu-image-admin');
				wp_enqueue_style('menu-image-css');
            }
            if ( $current_admin_screen->base == 'post') {
				wp_enqueue_style( 'wp-color-picker');
				wp_enqueue_script('wp-color-picker');
				wp_register_script('wp-color-picker-alpha', plugin_dir_url( __FILE__ ) .'admin/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ),null, true );
                wp_enqueue_script('wp-color-picker-alpha');

				wp_enqueue_media();

				wp_enqueue_style('themecore-admin-styles');

				wp_enqueue_script('chosen');
				wp_enqueue_style('chosen');
				wp_enqueue_style('flatpickr');
				wp_enqueue_script('flatpickr');
				wp_enqueue_script('admin-post-meta');
            }

            if ( $current_admin_screen->base == 'toplevel_page_room-availability' ) {
				wp_localize_script(
					'jquery',
					'themecore_admin_vars',
					array(
						'post_id'          => get_the_ID(),
						'nonce'            => wp_create_nonce( 'themecore-nonce-metagallery' ),
					)
				);
				wp_enqueue_style( 'wp-color-picker');
				wp_enqueue_script('wp-color-picker');
				wp_register_script('wp-color-picker-alpha', plugin_dir_url( __FILE__ ) .'admin/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ),null, true );
                wp_enqueue_script('wp-color-picker-alpha');

				wp_enqueue_media();

				wp_enqueue_style('themecore-admin-styles');

				wp_enqueue_script('chosen');
				wp_enqueue_style('chosen');
				wp_enqueue_style('flatpickr');
				wp_enqueue_script('flatpickr');
				wp_enqueue_script('admin-post-meta');
            }
        }
    }

    public function mtheme_load_front_end_scripts_styles() {

		// Modernizer
		wp_register_script( 'modernizr', plugin_dir_url( __FILE__ ) .'assets/js/modernizr.custom.47002.js', array( 'jquery' ),null, true );
		wp_register_script( 'jquery-debouncedresize', plugins_url( '/assets/js/smartresize/jquery.debouncedresize.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );
		wp_register_script( 'easing', plugin_dir_url( __FILE__ ) .'assets/js/jquery.easing.min.js', array( 'jquery' ),null, true );

		wp_register_script( 'rellax', plugin_dir_url( __FILE__ ) .'assets/js/rellax.min.js', array( 'jquery' ),null, true );

		wp_register_script( 'gomap', plugin_dir_url( __FILE__ ) .'assets/js/jquery.gomap.js', array( 'jquery' ), null,true );

		// Tubular
		wp_register_script( 'tubular', plugin_dir_url( __FILE__ ) .'assets/js/jquery.tubular.1.0.js', array( 'jquery' ), null,true );

		wp_register_script( 'video-js', plugin_dir_url( __FILE__ ) .'assets/js/videojs/video.js', array( 'jquery' ),null, true );
		wp_register_style( 'video-js', plugin_dir_url( __FILE__ ) .'assets/js/videojs/video-js.css', array( 'superlens-mainstyle' ), false, 'screen' );
		
        wp_enqueue_style( 'simpleicons', plugin_dir_url( __FILE__ ) .'assets/fonts/simple-line-icons/simple-line-icons.css', array( 'superlens-mainstyle' ), false, 'screen' );

		// PhotoWall INIT
		wp_register_script( 'superlens-photowall_init', plugin_dir_url( __FILE__ ) .'assets/js/photowall.js', array( 'jquery' ), null,true );

		// Kenburns
		wp_register_script( 'slideshowify', plugin_dir_url( __FILE__ ) .'assets/js/kenburns/jquery.slideshowify.js', array( 'jquery' ), null,true );
		wp_register_script( 'superlens-kenburns-init', plugin_dir_url( __FILE__ ) .'assets/js/kenburns/kenburns.init.js', array( 'jquery' ), null,true );
		wp_register_script( 'transit', plugin_dir_url( __FILE__ ) .'assets/js/kenburns/jquery.transit.min.js', array( 'jquery' ), null,true );

		// Supersized
		wp_register_script( 'supersized', plugin_dir_url( __FILE__ ) .'assets/js/supersized/supersized.3.2.7.min.js', array( 'jquery' ), null,true );
		wp_register_script( 'supersized-shutter', plugin_dir_url( __FILE__ ) .'assets/js/supersized/supersized.shutter.js', array( 'jquery' ), null,true );
		wp_register_style( 'supersized', plugin_dir_url( __FILE__ ) .'assets/js/supersized/supersized.css',array( 'superlens-mainstyle' ),false, 'screen' );

		wp_register_script( 'superlens-carousel', plugin_dir_url( __FILE__ ) .'assets/js/hcarousel.js', array( 'jquery' ), null,true );

		wp_register_script( 'tilt', plugin_dir_url( __FILE__ ) .'assets/js/tilt.jquery.js', array( 'jquery' ), null,true );
		
        wp_register_script( 'swiper', plugin_dir_url( __FILE__ ) .'assets/js/swiper/swiper.min.js', array( 'jquery' ), null,true );
		// Grid Rotator
		wp_register_script( 'gridrotator', plugin_dir_url( __FILE__ ) .'assets/js/jquery.gridrotator.js', array( 'modernizr' ), null,true );
		wp_register_script( 'classie', plugin_dir_url( __FILE__ ) .'assets/js/classie.js', array( 'jquery' ),null, true );

		// Vivus
		wp_register_script( 'vivus', plugin_dir_url( __FILE__ ) .'assets/js/vivus.min.js', array( 'jquery' ),null, true );

		wp_register_script( 'odometer', plugin_dir_url( __FILE__ ) .'assets/js/odometer.min.js', array( 'jquery' ),null, true );

		// Donut Chart
		wp_register_script( 'easypiechart', plugin_dir_url( __FILE__ ) .'assets/js/jquery.easypiechart.min', array( 'jquery' ),null, true );

		wp_register_script( 'jarallax', plugin_dir_url( __FILE__ ) .'assets/js/jarallax/jarallax.js', array( 'jquery' ), '1.5',true );

		wp_register_script( 'jplayer', plugin_dir_url( __FILE__ ) .'assets/js/html5player/jquery.jplayer.min.js', array( 'jquery' ),null, true );
		wp_register_style( 'jplayer', plugin_dir_url( __FILE__ ) .'assets/js/html5player/jplayer.dark.css', array( 'superlens-mainstyle' ), false, 'screen' );

		wp_register_script( 'fitvids', plugin_dir_url( __FILE__ ) .'assets/js/jquery.fitvids.js', array( 'jquery' ), null,true );

		wp_register_style('chosen', plugin_dir_url( __FILE__ ) .'assets/js/chosen/chosen.css', array( 'superlens-mainstyle' ), false, 'screen' );
		wp_register_script('chosen', plugin_dir_url( __FILE__ ) .'assets/js/chosen/chosen.jquery.js', array( 'jquery' ),null, true );

		wp_register_script( 'modulobox', plugin_dir_url( __FILE__ ) .'assets/js/modulobox/js/modulobox.js', array( 'jquery' ),null, true );
		wp_register_style( 'modulobox', plugin_dir_url( __FILE__ ) .'assets/js/modulobox/css/modulobox.css', array( 'superlens-mainstyle' ), false, 'screen' );
		
		wp_register_style( 'swiper', plugin_dir_url( __FILE__ ) .'assets/js/swiper/swiper.css', array( 'superlens-mainstyle' ), false, 'screen' );

		wp_register_script( 'touchswipe', plugin_dir_url( __FILE__ ) .'assets/js/jquery.touchSwipe.min.js', array( 'jquery' ),null, true );

		wp_register_script( 'ls-unveilhooks', plugin_dir_url( __FILE__ ) .'assets/js/ls.unveilhooks.min.js', array( 'lazysizes' ), null,true );

		wp_register_script( 'fotorama', plugin_dir_url( __FILE__ ) .'assets/js/fotorama/fotorama.js', array( 'jquery' ),null, true );
        wp_register_style( 'fotorama', plugin_dir_url( __FILE__ ) .'assets/js/fotorama/fotorama.css', array( 'superlens-mainstyle' ), false, 'screen' );

		wp_register_script( 'multiscroll', plugin_dir_url( __FILE__ ) .'assets/js/multiscroll/jquery.multiscroll.js', array( 'jquery' ),null, true );
		wp_register_style( 'multiscroll', plugin_dir_url( __FILE__ ) .'assets/js/multiscroll/jquery.multiscroll.css', array( 'superlens-mainstyle' ), false, 'screen' );

		// Particles
		wp_register_script( 'particles', plugin_dir_url( __FILE__ ) .'assets/js/particles/particles.min.js', array( 'jquery' ), null,true );
		wp_register_script( 'superlens-particles-draw-default', plugin_dir_url( __FILE__ ) .'assets/js/particles/draw-default.js', array( 'jquery' ), null,true );
		wp_register_script( 'superlens-particles-draw-stars', plugin_dir_url( __FILE__ ) .'assets/js/particles/draw-stars.js', array( 'jquery' ), null,true );
		wp_register_script( 'superlens-particles-draw-snow', plugin_dir_url( __FILE__ ) .'assets/js/particles/draw-snow.js', array( 'jquery' ), null,true );
		wp_register_script( 'superlens-particles-draw-grab', plugin_dir_url( __FILE__ ) .'assets/js/particles/draw-grab.js', array( 'jquery' ), null,true );
		wp_register_script( 'superlens-particles-draw-move', plugin_dir_url( __FILE__ ) .'assets/js/particles/draw-move.js', array( 'jquery' ), null,true );

    }

        // Proofing Metabox
		public function themecore_proofingitemmetabox_init(){
			add_meta_box("proofingInfo-meta", esc_html__("Proofing Options","imaginem-blocks"), "themecore_proofingitem_metaoptions", "proofing", "normal", "low");
		}
		// Page Metabox
		public function themecore_add_page_box() {
			add_meta_box('common-pagemeta-box', esc_html__('General Page Metabox','imaginem-blocks-ii'), 'themecore_common_show_pagebox', 'page', 'normal', 'core');
		}
		// Client Metabox
		public function themecore_clientitemmetabox_init(){
			add_meta_box('mtheme_clientInfo-meta', esc_html__('Client Options','imaginem-blocks-ii'), 'themecore_clientitem_metaoptions', 'clients', 'normal', 'low');
		}
		// Events Metabox
		public function themecore_reservationsitemmetabox_init(){
			add_meta_box('reservationsInfo-meta', esc_html__('Events Options','imaginem-blocks-ii'), 'themecore_reservationsitem_metaoptions', 'reservations', 'normal', 'low');
		}
		// Fullscreen Metabox
		public function themecore_fullscreenitemmetabox_init(){
			add_meta_box('fullscreen-meta', esc_html__('Featured Options','imaginem-blocks-ii'), 'themecore_featured_options', 'fullscreen', 'normal', 'low');
		}
		// Portfolio Metabox
		public function themecore_portfolioitemmetabox_init(){
			add_meta_box("portfolioInfo-meta", esc_html__("Portfolio Options","imaginem-blocks"), "themecore_portfolioitem_metaoptions", "portfolio", "normal", "low");
		}
		// Portfolio Metabox
		public function themecore_photostoryitemmetabox_init(){
			add_meta_box("photostory-meta", esc_html__("Photo Story Options","imaginem-blocks"), "themecore_photostoryitem_metaoptions", "photostory", "normal", "low");
		}
		// Portfolio Metabox
		public function themecore_roomitemmetabox_init(){
			add_meta_box("room-meta", esc_html__("Room Options","imaginem-blocks"), "themecore_roomitem_metaoptions", "room", "normal", "low");
		}
        // Post Metabox
        public function themecore_add_post_boxes() {

            $mtheme_post_metapack=array();
            $mtheme_post_metapack['main'] = array(
                'id' => 'common-pagemeta-box',
                'title' => esc_html__('General Page Metabox','imaginem-blocks'),
                'page' => 'post',
                'context' => 'normal',
                'priority' => 'core' );
            $mtheme_post_metapack['video'] = array(
                'id' => 'video-meta-box',
                'title' => esc_html__('Video Metabox','imaginem-blocks'),
                'page' => 'post',
                'context' => 'normal',
                'priority' => 'high' );
            $mtheme_post_metapack['audio'] = array(
                'id' => 'audio-meta-box',
                'title' => esc_html__('Audio Metabox','imaginem-blocks'),
                'page' => 'post',
                'context' => 'normal',
                'priority' => 'high' );
            $mtheme_post_metapack['link'] = array(
                'id' => 'link-meta-box',
                'title' => esc_html__('Link Metabox','imaginem-blocks'),
                'page' => 'post',
                'context' => 'normal',
                'priority' => 'high' );
            $mtheme_post_metapack['image'] = array(
                'id' => 'image-meta-box',
                'title' => esc_html__('Image Metabox','imaginem-blocks'),
                'page' => 'post',
                'context' => 'normal',
                'priority' => 'high' );

            $mtheme_post_metapack['quote'] = array(
                'id' => 'quote-meta-box',
                'title' => esc_html__('Quote Metabox','imaginem-blocks'),
                'page' => 'post',
                'context' => 'normal',
                'priority' => 'high' );


            add_meta_box($mtheme_post_metapack['main']['id'], $mtheme_post_metapack['main']['title'], 'themecore_common_show_box', $mtheme_post_metapack['main']['page'], $mtheme_post_metapack['main']['context'], $mtheme_post_metapack['main']['priority']);
            add_meta_box($mtheme_post_metapack['video']['id'], $mtheme_post_metapack['video']['title'], 'themecore_video_show_box', $mtheme_post_metapack['video']['page'], $mtheme_post_metapack['video']['context'], $mtheme_post_metapack['video']['priority']);
            add_meta_box($mtheme_post_metapack['link']['id'], $mtheme_post_metapack['link']['title'], 'themecore_link_show_box', $mtheme_post_metapack['link']['page'], $mtheme_post_metapack['link']['context'], $mtheme_post_metapack['link']['priority']);
            add_meta_box($mtheme_post_metapack['image']['id'], $mtheme_post_metapack['image']['title'], 'themecore_image_show_box', $mtheme_post_metapack['image']['page'], $mtheme_post_metapack['image']['context'], $mtheme_post_metapack['image']['priority']);
            add_meta_box($mtheme_post_metapack['quote']['id'], $mtheme_post_metapack['quote']['title'], 'themecore_quote_show_box', $mtheme_post_metapack['quote']['page'], $mtheme_post_metapack['quote']['context'], $mtheme_post_metapack['quote']['priority']);
            add_meta_box($mtheme_post_metapack['audio']['id'], $mtheme_post_metapack['audio']['title'], 'themecore_audio_show_box', $mtheme_post_metapack['audio']['page'], $mtheme_post_metapack['audio']['context'], $mtheme_post_metapack['audio']['priority']);
        }
        // WooCommerce Metaboxes
        public function themecore_woocommerceitemmetabox_init(){
            add_meta_box('mtheme_woocommerceInfo-meta', esc_html__('WooCommerce Options','imaginem-blocks'), 'themecore_woocommerceitem_metaoptions', 'product', 'normal', 'low');
        }

}
new Theme_Core();