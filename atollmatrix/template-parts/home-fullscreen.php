<?php
$featured_page = atollmatrix_get_active_fullscreen_post();
if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
	$_type         = get_post_type( $featured_page );
	$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
}
$custom = get_post_custom( $featured_page );
if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
	$fullscreen_type = $custom['pagemeta_fullscreen_type'][0];
}
if ( isset( $fullscreen_type ) ) {
	$fullscreen_file = atollmatrix_get_fullscreen_file( $fullscreen_type );
}
if ( isset( $fullscreen_file ) ) {
	switch ( $fullscreen_file ) {
		case 'kenburns':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'kenburns' );
			break;

		case 'portfolioslideshow':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'portfolioslideshow' );
			break;

		case 'splitslider':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'splitslider' );
			break;

		case 'coverphoto':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'coverphoto' );
			break;

		case 'particles':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'particles' );
			break;

		case 'fotorama':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'fotorama' );
			break;

		case 'carousel':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'carousel' );
			break;

		case 'portfoliocarousel':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'portfoliocarousel' );
			break;

		case 'supersized':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'supersized' );
			break;

		case 'video':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'video' );
			break;

		case 'revslider':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'revslider' );
			break;

		case 'swiperslides':
			get_template_part( '/template-parts/fullscreen/fullscreen', 'swiperslides' );
			break;
		default:
			break;
	}
} else {
	get_header();
	?>
	<div class="container-wrapper container-boxed"> 
		<div class="page-contents-wrap">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-page-wrapper entry-content clearfix">
					<div class="title-container-wrap">
						<div class="title-container clearfix">
							<div class="entry-title">
							<h1>
							<?php
							echo esc_html__( 'Fullscreen post not selected in customizer', 'atollmatrix' );
							?>
							</h1>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	get_footer();
}
