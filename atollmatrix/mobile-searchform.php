<form method="get" id="mobile-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php
		$search_placeholder = atollmatrix_get_option_data( 'search_placeholder' );
	?>
	<input placeholder="<?php echo esc_attr( $search_placeholder ); ?>" type="text" value="" name="s" id="ms" class="right" />
	<button id="mobile-searchbutton" title="<?php echo esc_attr( $search_placeholder ); ?>" type="submit"><i class="feather-icon-search"></i></button>
</form>
