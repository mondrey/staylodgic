<?php
$search_buttontext = atollmatrix_get_option_data( 'search_buttontext' );
$search_placeholder = atollmatrix_get_option_data( 'search_placeholder' );
?>
<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
<input placeholder="<?php echo esc_attr( $search_placeholder ); ?>" type="text" value="" name="s" id="s" class="right" />
<button class="ntips" id="searchbutton" title="<?php echo esc_html( $search_buttontext ); ?>" type="submit"><i class="fa fa-search"></i></button>
</form>
