<?php
if ( is_singular() ) {
	$pagetitle_status = get_post_meta( get_the_id(), 'pagemeta_page_title', true );
}
if ( is_archive() ) {
	$pagetitle_status = 'show';
}
if ( ! isset( $pagetitle_status ) ) {
	$pagetitle_status = '';
}
if ( is_singular( 'proofing' ) ) {
	$pagetitle_status = 'hide';
}
if ( is_singular( 'clients' ) ) {
	$pagetitle_status = 'hide';
}
if ( post_password_required() ) {
	$pagetitle_status = 'hide';
}
if ( 'hide' !== $pagetitle_status ) {
	?>
<div class="title-container-outer-wrap">
	<div class="title-container-wrap">
	<div class="title-container clearfix">
		<?php
		do_action( 'mtheme_before_header_title' );
		?>
		<?php
		$mtheme_pagestyle = '';
		if ( isset( $post->ID ) ) {
			$custom = get_post_custom( $post->ID );
		}
		$mtheme_pagestyle = atollmatrix_get_pagestyle( get_the_id() );
		if ( is_home() ) {
			$mtheme_pagestyle = 'rightsidebar';
		}
		if ( is_post_type_archive() ) {
			$mtheme_pagestyle = 'fullwidth';
		}
		if ( is_tax() ) {
			$mtheme_pagestyle = 'fullwidth';
		}
		if ( 'fullwidth' === $mtheme_pagestyle || 'edge-to-edge' === $mtheme_pagestyle ) {
			$floatside = '';
		}
		if ( 'rightsidebar' === $mtheme_pagestyle ) {
			$floatside = 'float-left';
		}
		if ( 'leftsidebar' === $mtheme_pagestyle ) {
			$floatside = 'float-right';
		}

		if ( isset( $custom['pagemeta_pagetitle_style'][0] ) ) {
			$mtheme_pagetitle_style = $custom['pagemeta_pagetitle_style'][0];
		}
		if ( isset( $mtheme_pagetitle_style ) ) {
			$mtheme_pagetitle_style = ' ' . $mtheme_pagetitle_style;
		} else {
			$mtheme_pagetitle_style = '';
		}
		$hide_pagetitle = atollmatrix_get_option_data( 'hide_pagetitle' );
		if ( true !== $hide_pagetitle ) {
			?>
			<div class="entry-title-wrap<?php echo esc_attr( $mtheme_pagetitle_style ); ?>">
				<h1 class="entry-title">
				<?php
				if ( is_day() ) :
					$archive_daily_titleprefix = atollmatrix_get_option_data( 'archive_daily_titleprefix' );
					echo esc_html( $archive_daily_titleprefix ) . get_the_date();
				elseif ( is_month() ) :
					$archive_monthly_titleprefix = atollmatrix_get_option_data( 'archive_monthly_titleprefix' );
					echo esc_html( $archive_monthly_titleprefix ) . get_the_date( 'F Y' );
				elseif ( is_year() ) :
					$archive_year_titleprefix = atollmatrix_get_option_data( 'archive_year_titleprefix' );
					echo esc_html( $archive_category_titleprefix ) . get_the_date( 'Y' );
				elseif ( is_author() ) :
					$archive_author_titleprefix = atollmatrix_get_option_data( 'archive_author_titleprefix' );
					echo esc_html( $archive_author_titleprefix );
					echo esc_html( get_query_var( 'author_name' ) );
				elseif ( is_category() ) :
					$archive_category_titleprefix = atollmatrix_get_option_data( 'archive_category_titleprefix' );
					echo esc_html( $archive_category_titleprefix ) . '<span>' . single_cat_title( '', false ) . '</span>';
				elseif ( is_tag() ) :
					$archive_tag_titleprefix = atollmatrix_get_option_data( 'archive_tag_titleprefix' );
					echo esc_html( $archive_tag_titleprefix ) . '<span>' . single_cat_title( '', false ) . '</span>';
				elseif ( is_search() ) :
					$archive_search_notfoundtitleprefix = atollmatrix_get_option_data( 'archive_search_notfoundtitleprefix' );
					echo esc_html( $archive_search_notfoundtitleprefix ) . '<span>' . get_search_query() . '</span>';
				elseif ( is_404() ) :
					$pagenoutfound_title = atollmatrix_get_option_data( 'pagenoutfound_title' );
					echo esc_html( $pagenoutfound_title );
				elseif ( is_front_page() && ! is_home() ) :
					the_title( '' );
				elseif ( is_front_page() ) :
					bloginfo( 'name' );
				elseif ( is_home() ) :
					$frontpage_id = get_option( 'page_for_posts' );
					echo get_the_title( $frontpage_id );
				elseif ( is_post_type_archive( 'portfolio' ) ) :
					echo esc_html( atollmatrix_get_option_data( 'portfolio_archive_title' ) );
				elseif ( is_post_type_archive( 'events' ) ) :
					echo esc_html( atollmatrix_get_option_data( 'event_gallery_title' ) );
				elseif ( is_post_type_archive( 'proofing' ) ) :
					echo esc_html( atollmatrix_get_option_data( 'proofing_archive_title' ) );
				elseif ( is_post_type_archive( 'product' ) && class_exists( 'woocommerce' ) && is_woocommerce() ) :
					echo esc_html( woocommerce_page_title() );
				elseif ( is_tax() ) :
					$tax_term = get_queried_object();
					if ( ! isset( $tax_term->name ) ) {
						$worktype = atollmatrix_get_option_data( 'portfolio_singular_refer' );
					} else {
						$worktype = $tax_term->name;
					}
					echo esc_html( $worktype );
				else :
					the_title( '' );
				endif;
				?>
				</h1>
			</div>
			<?php
		}
		do_action( 'mtheme_after_header_title' );
		?>
	</div>
</div>
</div>
	<?php
}
