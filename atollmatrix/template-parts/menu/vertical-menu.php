<?php
$open_status_class = '';
if ( atollmatrix_get_option_data( 'vertical_menu_keep_open' ) ) {
	$open_status_class = ' show-current-menu-open';
}
?>
<div class="vertical-menu-outer<?php echo esc_attr( $open_status_class ); ?>">
	<div class="vertical-menu-wrap">
		<div class="vertical-menu">
<?php
$verticalmenu_logo = atollmatrix_get_option_data( 'verticalmenu_logo' );
$custom_logo_url   = atollmatrix_get_option_data( 'custom_logo_url' );
$home_url_path     = home_url( '/' );

if ( '' !== $custom_logo_url ) {
	$home_url_path = $custom_logo_url;
}

if ( '' !== $verticalmenu_logo ) {
	echo '<div class="vertical-logo-wrap">';
	$menu_logo          = '<img class="vertical-logoimage" src="' . esc_url( $verticalmenu_logo ) . '" alt="Logo" />';
	$vertical_menu_logo = '<a href="' . esc_url( $home_url_path ) . '">' . $menu_logo . '</a>';
	echo wp_kses( $vertical_menu_logo, atollmatrix_get_allowed_tags() );
} else {
	$site_description_tag = atollmatrix_get_site_description();
	$textlogo_tag         = atollmatrix_get_option_data('textlogo_tag');

	if ( '' == $textlogo_tag || ! isset( $textlogo_tag ) ) {
		$textlogo_tag = 'h1';
	}

	echo '<div class="vertical-text-logo-wrap">';
	$vertical_text_logo  = '<div class="vertical-site-title-section">';
	$vertical_text_logo .= '<' . esc_attr( $textlogo_tag ) . ' class="site-title"><a href="' . esc_url( $home_url_path ) . '" rel="home">' . get_bloginfo( 'name' ) . '</a></' . esc_attr( $textlogo_tag ) . '>';
	$vertical_text_logo .= $site_description_tag;
	$vertical_text_logo .= '</div>';
	echo wp_kses( $vertical_text_logo, atollmatrix_get_allowed_tags() );
}
?>
		</div>
<?php
do_action( 'atollmatrix_add_mobile_menu_wpml' );
?>
			<nav>
<?php
$custom_menu_call = '';
if ( is_singular() ) {
	$user_choice_of_menu = get_post_meta( get_the_id(), 'pagemeta_menu_choice', true );
	if ( isset( $user_choice_of_menu ) && 'deault' !== $user_choice_of_menu ) {
		$custom_menu_call = $user_choice_of_menu;
	}
}
if ( atollmatrix_page_is_woo_shop() ) {
	$woo_shop_post_id    = get_option( 'woocommerce_shop_page_id' );
	$user_choice_of_menu = get_post_meta( $woo_shop_post_id, 'pagemeta_menu_choice', true );
	if ( isset( $user_choice_of_menu ) && 'deault' !== $user_choice_of_menu ) {
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
// Responsive menu conversion to drop down list
if ( function_exists( 'wp_nav_menu' ) ) {
	wp_nav_menu(
		array(
			'container'      => false,
			'theme_location' => 'main_menu',
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
}
?>
			</nav>
		</div>
<?php
if ( true === get_theme_mod( 'instagram_footer', true ) ) {
	$display_instagram = true;
} else {
	$display_instagram = false;
}
if ( $display_instagram ) {
	// Disable instgram on footer and will be displayed for Vertical menu
	if ( 'instagram-verticalmenu' === atollmatrix_get_option_data( 'instagram_location' ) ) {
		$display_instagram = true;
	} else {
		$display_instagram = false;
	}
}
$vertical_footer_copyright = atollmatrix_get_option_data( 'vertical_footer_copyright' );
$vertical_footer_copyright = atollmatrix_convert_breaks_to_list( $vertical_footer_copyright );
$vertical_footer_copyright = do_shortcode( $vertical_footer_copyright );
?>
	<div class="vertical-footer-wrap">
	<?php
	if ( $display_instagram ) {
		get_template_part( 'template-parts/grid', 'instagram' );
	}
	?>
		<div class="vertical-footer-wrap-inner">
		<?php
		if ( is_active_sidebar( 'social_header' ) ) {
			?>
			<div class="login-socials-wrap clearfix">
			<?php
			dynamic_sidebar( 'social_header' );
			?>
			</div>
			<?php
		}
		?>
				<div class="vertical-footer-copyright"><?php echo wp_kses( $vertical_footer_copyright, atollmatrix_get_allowed_tags() ); ?></div>
			</div>
		</div>
	</div>
</div>
