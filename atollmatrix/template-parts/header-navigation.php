<?php
$custom_menu_call = '';
if ( is_singular() ) {
	$user_choice_of_menu = get_post_meta( get_the_id(), 'pagemeta_menu_choice', true );
	if ( atollmatrix_page_is_woo_shop() ) {
		$woo_shop_post_id    = get_option( 'woocommerce_shop_page_id' );
		$user_choice_of_menu = get_post_meta( $woo_shop_post_id, 'pagemeta_menu_choice', true );
	}
	if ( isset( $user_choice_of_menu ) && 'default' !== $user_choice_of_menu ) {
		$custom_menu_call = $user_choice_of_menu;
	}
}
if ( atollmatrix_is_fullscreen_home() ) {
	$featured_page       = atollmatrix_get_active_fullscreen_post();
	$user_choice_of_menu = get_post_meta( $featured_page, 'pagemeta_menu_choice', true );
	if ( isset( $user_choice_of_menu ) && 'default' !== $user_choice_of_menu ) {
		$custom_menu_call = $user_choice_of_menu;
	}
}
$menu_class = 'sf-menu';

$header_menu_type = 'left-logo';
$menu_navigation  = true;

if ( atollmatrix_header_is_minimal() || atollmatrix_header_is_compact() ) {
	$menu_navigation = false;
}

function atollmatrix_main_menu_logo( $header_menu_type = 'left-logo' ) {
	$header_menu_type  = 'left-logo';
	$logo_element      = '';
	$sticky_main_logo  = atollmatrix_get_option_data( 'sticky_main_logo' );
	$sticky_logo_class = '';
	if ( '' !== $sticky_main_logo ) {
		$sticky_logo_class = ' sticky-alt-logo-present';
	}

	$theme_style     = 'light';
	$main_logo       = atollmatrix_get_option_data( 'main_logo' );
	$secondary_logo  = atollmatrix_get_option_data( 'secondary_logo' );
	$custom_logo_url = atollmatrix_get_option_data( 'custom_logo_url' );
	$home_url_path   = home_url( '/' );

	if ( '' !== $custom_logo_url ) {
		$home_url_path = $custom_logo_url;
	}

	if ( '' !== $main_logo ) {
		if ( '' === $secondary_logo ) {
			$secondary_logo = $main_logo;
		}

		$logo_element .= '<div class="header-logo-section">';
		$logo_element .= '<div class="logo' . esc_attr( $sticky_logo_class ) . '">';
		$logo_element .= '<a href="' . esc_url( $home_url_path ) . '">';

		$logo_element .= '<img class="logo-theme-main logo-theme-primary logo-theme-dark logo-theme-custom" src="' . esc_url( $main_logo ) . '" alt="' . esc_attr__( 'logo', 'atollmatrix' ) . '" />';
		$logo_element .= '<img class="logo-theme-main logo-theme-secondary logo-theme-bright logo-theme-custom" src="' . esc_url( $secondary_logo ) . '" alt="' . esc_attr__( 'logo', 'atollmatrix' ) . '" />';

		$logo_element .= '</a>';
		$logo_element .= '</div>';
		$logo_element .= '</div>';
	} else {

		$site_description_tag = atollmatrix_get_site_description();
		$textlogo_tag         = atollmatrix_get_option_data('textlogo_tag');

		if ( '' == $textlogo_tag || ! isset( $textlogo_tag ) ) {
			$textlogo_tag = 'h1';
		}

		$logo_element .= '<div class="header-site-title-section">';
		$logo_element .= '<' . esc_attr( $textlogo_tag ) . ' class="site-title"><a href="' . esc_url( $home_url_path ) . '" rel="home">' . get_bloginfo( 'name' ) . '</a></' . esc_attr( $textlogo_tag ) . '>';
		$logo_element .= $site_description_tag;
		$logo_element .= '</div>';
	}

	return $logo_element;
}
if ( atollmatrix_menu_is_vertical() ) {
	get_template_part( 'template-parts/menu/vertical', 'menu' );
} else {
	?>
<div class="outer-wrap stickymenu-zone">
<div class="outer-wrap-inner-zone">
	<?php
	if ( is_active_sidebar( 'social_header' ) && $menu_navigation ) {
		echo '<div class="menu-social-header">';
			dynamic_sidebar( 'social_header' );
		echo '</div>';
	}
	?>
	<?php
	do_action( 'atollmatrix_add_toggle_menu_cart' );
	?>
	<div class="outer-header-wrap clearfix">
		<nav>
			<?php
			$header_menu_type = 'left-logo';
			$adjustable       = '';
			?>
			<div class="mainmenu-navigation <?php echo esc_attr( $adjustable ); ?> clearfix">
				<?php
				$header_menu_type           = 'left-logo';
				$atollmatrix_main_menu_logo = atollmatrix_main_menu_logo( $header_menu_type );
				echo wp_kses( $atollmatrix_main_menu_logo, atollmatrix_get_allowed_tags() );

				if ( $menu_navigation ) {
					if ( has_nav_menu( 'main_menu' ) ) {

						$submenuindicator = '';
						if ( atollmatrix_get_option_data( 'submenu_indicator' ) ) {
							$submenuindicator = ' has-submenu-indicators';
						}
						?>
						<div class="homemenu<?php echo esc_attr( $submenuindicator ); ?>">
						<?php
						if ( class_exists( 'mtheme_Menu_Megamenu' ) ) {
							echo wp_nav_menu(
								array(
									'container'      => false,
									'menu'           => $custom_menu_call,
									'theme_location' => 'main_menu',
									'menu_class'     => $menu_class,
									'echo'           => false,
									'before'         => '',
									'after'          => '',
									'link_before'    => '',
									'link_after'     => '',
									'depth'          => 0,
									'fallback_cb'    => 'mtheme_nav_fallback',
									'walker'         => new mtheme_Menu_Megamenu(),
								)
							);
						} else {
							echo wp_nav_menu(
								array(
									'container'      => false,
									'menu'           => $custom_menu_call,
									'theme_location' => 'main_menu',
									'menu_class'     => $menu_class,
									'echo'           => false,
									'before'         => '',
									'after'          => '',
									'link_before'    => '',
									'link_after'     => '',
									'depth'          => 0,
									'fallback_cb'    => 'mtheme_nav_fallback',
								)
							);
						}
						?>
						</div>
						<?php
					}
				}
				?>
			</div>
		</nav>
	</div>
</div>
</div>
	<?php
}
