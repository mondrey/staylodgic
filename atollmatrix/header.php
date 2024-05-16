<?php
/*
* @ Header
*/
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php
	wp_head();
	?>
</head>
<body <?php body_class(); ?>>
<?php
	wp_body_open();
?>
<?php
if ( ! wp_is_mobile() ) {
	//do_action( 'atollmatrix_display_elementloader' );
	//do_action( 'atollmatrix_preloader' );
}
if ( is_page_template( 'template-blank.php' ) ) {

	$site_layout_width = 'fullwidth';

} else {
	get_template_part( 'template-parts/menu/mobile', 'menu' );
	//Header Navigation elements with Elementor header location check
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
		get_template_part( 'template-parts/header', 'navigation' );
	}
}
do_action( 'atollmatrix_header_woocommerce_shopping_cart_counter' );
echo '<div id="home" class="container-wrapper container-fullwidth entry-content">';
if ( ! atollmatrix_is_fullscreen_post() ) {
	if ( atollmatrix_menu_is_vertical() ) {
		echo '<div class="vertical-menu-body-container">';
	} else {
		echo '<div class="horizontal-menu-body-container">';
	}
	echo '<div class="container-outer">';
}
if ( ! is_page_template( 'template-blank.php' ) && ! atollmatrix_is_fullscreen_post() ) {
	get_template_part( 'template-parts/header', 'title' );
}
if ( ! atollmatrix_is_fullscreen_post() ) {
	echo '<div class="container clearfix">';
}
?>
