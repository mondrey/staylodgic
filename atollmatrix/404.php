<?php
/*
404 Page
*/
?>
<?php get_header(); ?>
<div id="vertical-center-wrap">
	<div class="vertical-center-outer">
		<div class="vertical-center-inner">
			<div class="entry-page-wrapper entry-content clearfix">
				<div class="mtheme-404-wrap">
					<div class="mtheme-404-icon">
						<i class="et-icon-caution"></i>
					</div>
					<?php
					$error_msg  = atollmatrix_get_option_data( 'pagenoutfound_title' );
					$search_msg = atollmatrix_get_option_data( 'pagenoutfound_search' );
					if ( '' === $error_msg ) {
						$error_msg = esc_html__( '404 Page not Found!', 'atollmatrix' );
					}
					if ( '' === $search_msg ) {
						$search_msg = esc_html__( 'Would you like to search for the page', 'atollmatrix' );
					}
					?>
					<div class="mtheme-404-error-message1"><?php echo esc_html( $error_msg ); ?></div>
					<h4><?php echo esc_html( $search_msg ); ?></h4>
					<?php get_search_form(); ?>
				</div>
			</div><!-- .entry-content -->
		</div>
	</div>
</div>
<?php get_footer(); ?>
