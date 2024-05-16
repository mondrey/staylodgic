<?php
/**
*  Sidebar
 */
$mtheme_pagestyle = 'rightsidebar';
if ( is_singular() ) {
	$mtheme_pagestyle = atollmatrix_get_pagestyle( get_the_id() );
	$floatside        = 'float-left';
	if ( 'nosidebar' === $mtheme_pagestyle ) {
		$floatside = '';
	}
	if ( 'rightsidebar' === $mtheme_pagestyle ) {
		$floatside = 'float-left';
	}
	if ( 'leftsidebar' === $mtheme_pagestyle ) {
		$floatside = 'float-right';
	}
	if ( ! isset( $mtheme_pagestyle ) || '' === $mtheme_pagestyle ) {
		$mtheme_pagestyle = 'rightsidebar';
		$floatside        = 'float-left';
	}
	if ( 'edge-to-edge' === $mtheme_pagestyle ) {
		$floatside        = '';
		$mtheme_pagestyle = 'nosidebar';
	}
	$mtheme_sidebar_choice = get_post_meta( get_the_id(), 'pagemeta_sidebar_choice', true );
}
if ( atollmatrix_page_is_woo_shop() ) {
	$woo_shop_post_id      = get_option( 'woocommerce_shop_page_id' );
	$mtheme_sidebar_choice = get_post_meta( $woo_shop_post_id, 'pagemeta_sidebar_choice', true );
}
if ( ! is_singular() ) {
	if ( ! atollmatrix_page_is_woo_shop() ) {
		unset( $mtheme_sidebar_choice );
	}
}
if ( class_exists( 'woocommerce' ) ) {
	if ( is_shop() ) {
		$mtheme_pagestyle = atollmatrix_get_pagestyle( get_the_ID() );
	}

	if ( is_product_category() || is_product_tag() ) {
		$mtheme_pagestyle = atollmatrix_get_pagestyle( get_the_ID() );
	}
}
$sidebar_position = 'sidebar-float-right';
if ( 'rightsidebar' === $mtheme_pagestyle ) {
	$sidebar_position = 'sidebar-float-right';
}
if ( 'leftsidebar' === $mtheme_pagestyle ) {
	$sidebar_position = 'sidebar-float-left';
}
$sidebar_wrap_class = 'sidebar-wrap';
if ( is_single() || is_page() ) {
	$sidebar_wrap_class = $sidebar_wrap_class . '-single';
}
if ( ! isset( $mtheme_sidebar_choice ) || empty( $mtheme_sidebar_choice ) ) {
	$mtheme_sidebar_choice = 'default_sidebar';
	if ( class_exists( 'woocommerce' ) ) {
		if ( is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url() || is_shop() || is_product() || is_account_page() ) {
			$mtheme_sidebar_choice = 'woocommerce_sidebar';
		}
	}
}
if ( class_exists( 'woocommerce' ) ) {
	if ( is_shop() ) {
		$mtheme_sidebar_choice = 'woocommerce_sidebar';
	}
}
if ( class_exists( 'woocommerce' ) ) {
	if ( is_product_category() ) {
		$mtheme_sidebar_choice = 'woocommerce_sidebar';
	}
	if ( is_product_tag() ) {
		$mtheme_sidebar_choice = 'woocommerce_sidebar';
	}
}
if ( is_active_sidebar( $mtheme_sidebar_choice ) ) {
	?>
	<div id="sidebar" class="sidebar-wrap-column-outer <?php echo esc_attr( $sidebar_wrap_class ); ?> <?php echo esc_attr( $sidebar_position ); ?>">
		<div class="sidebar clearfix">
	<?php
	dynamic_sidebar( $mtheme_sidebar_choice );
	?>
		</div>
	</div>
	<?php
}
?>
