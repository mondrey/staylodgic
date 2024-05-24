<?php
$mobile_menu_active   = false;
$fallback_menu_active = false;
$mobile_menu_location = 'mobile_menu';
$search_mobileform    = atollmatrix_get_option_data('search_mobileform');
if (has_nav_menu('mobile_menu')) {
	$mobile_menu_active = true;
} else {
	if (has_nav_menu('main_menu')) {
		$mobile_menu_active   = true;
		$fallback_menu_active = true;
		$mobile_menu_location = 'main_menu';
	}
}
$open_status_class = '';
if (atollmatrix_get_option_data('responsive_menu_keep_open')) {
	$open_status_class = ' show-current-menu-open';
}
?>
<?php
if ($mobile_menu_active) {
?>
	<nav id="mobile-toggle-menu" class="mobile-toggle-menu mobile-toggle-menu-close">
		<span class="mobile-toggle-menu-trigger"><span>Menu</span></span>
	</nav>
	<div class="responsive-menu-wrap<?php echo esc_attr($open_status_class); ?>">
		<div class="mobile-alt-toggle">
			<?php
			do_action('atollmatrix_add_mobile_menu_cart');
			do_action('atollmatrix_add_mobile_menu_wpml');
			?>
		</div>
		<div class="mobile-menu-toggle">
			<div class="logo-mobile">
				<?php
				$responsive_logo = atollmatrix_get_option_data('responsive_logo');
				$theme_style     = 'light';
				$custom_logo_url = atollmatrix_get_option_data('custom_logo_url');
				$home_url_path   = home_url('/');

				if (function_exists('staylodgic_get_option')) {
					$responsive_logo_id = staylodgic_get_option('responsive_logo');
					$responsive_logo       = $responsive_logo_id ? wp_get_attachment_image_url($responsive_logo_id, 'full') : '';
				}

				if ('' !== $custom_logo_url) {
					$home_url_path = $custom_logo_url;
				}

				if ('' !== $responsive_logo) {
					$mobile_logo     = '<img class="custom-responsive-logo logoimage" src="' . esc_url($responsive_logo) . '" alt="' . esc_attr__('logo', 'atollmatrix') . '" />';
					$mobile_logo_tag = '<a href="' . esc_url($home_url_path) . '">' . $mobile_logo . '</a>';
					echo wp_kses($mobile_logo_tag, atollmatrix_get_allowed_tags());
				} else {
				}
				?>
			</div>
			<div class="responsive-menu-overlay"></div>
		</div>
	</div>
	<?php
	$header_menu_type = atollmatrix_get_option_data('menu_type');
	if (function_exists('theme_demo_feature_mode')) {
		$header_menu_type = apply_filters('header_style', $header_menu_type);
	}
	$minimal_menu_active = false;
	if (atollmatrix_header_is_toggle_main_menu()) {
		$minimal_menu_active = true;
	}
	if ($mobile_menu_active) {
		if ($minimal_menu_active) {
	?>
			<div class="responsive-mobile-menu-outer">
				<div class="minimal-logo-overlay"></div>
			<?php
		}
			?>
			<div class="responsive-mobile-menu">
				<div class="dashboard-columns">
					<div class="mobile-menu-social">
						<div class="mobile-socials-wrap clearfix">
							<?php
							dynamic_sidebar('mobile_social_header');
							?>
						</div>
					</div>
					<nav>
						<?php
						$custom_menu_call    = '';
						$user_choice_of_menu = get_post_meta(get_the_id(), 'pagemeta_menu_choice', true);
						if (atollmatrix_page_is_woo_shop()) {
							$woo_shop_post_id    = get_option('woocommerce_shop_page_id');
							$user_choice_of_menu = get_post_meta($woo_shop_post_id, 'pagemeta_menu_choice', true);
						}
						if (isset($user_choice_of_menu) && 'default' !== $user_choice_of_menu) {
							$custom_menu_call = $user_choice_of_menu;
						}
						if (atollmatrix_is_fullscreen_home()) {
							$featured_page       = atollmatrix_get_active_fullscreen_post();
							$user_choice_of_menu = get_post_meta($featured_page, 'pagemeta_menu_choice', true);
							if (isset($user_choice_of_menu) && 'default' !== $user_choice_of_menu) {
								$custom_menu_call = $user_choice_of_menu;
							}
						}
						// Responsive menu conversion to drop down list
						echo wp_nav_menu(
							array(
								'container'      => false,
								'theme_location' => $mobile_menu_location,
								'menu'           => $custom_menu_call,
								'menu_class'     => 'mtree',
								'echo'           => true,
								'before'         => '',
								'after'          => '',
								'link_before'    => '',
								'link_after'     => '',
								'depth'          => 0,
								'fallback_cb'    => 'mtheme_nav_fallback',
							)
						);
						?>
					</nav>
					<div class="clearfix"></div>
				</div>
			</div>
			<?php
			if ($minimal_menu_active) {
			?>
			</div>
		<?php
			}
		?>
<?php
	}
}
