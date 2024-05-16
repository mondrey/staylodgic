<div class="fullpage-item">
<div class="entry-content author-info">
	<h2 class="author-heading">
	<?php
	$author_publishedby = atollmatrix_get_option_data( 'author_publishedby' );
	if ( '' !== $author_publishedby ) {
		echo esc_html( $author_publishedby );
	} else {
		esc_html_e( 'Published by', 'atollmatrix' );
	}
	?>
	</h2>
	<div class="author-avatar">
		<?php
		$author_bio_avatar_size = apply_filters( 'mtheme_author_bio_avatar_size', 60 );
		echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
		?>
	</div><!-- .author-avatar -->
	<div class="author-description">
		<h3 class="vcard author author-title"><span class="fn"><?php echo get_the_author(); ?></span></h3>

		<div class="author-bio">
			<?php the_author_meta( 'description' ); ?>
			<div class="autho-linked-button">
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
					<div class="mtheme-button">
						<?php
						$author_postsby = atollmatrix_get_option_data( 'author_postsby' );
						if ( '' !== $author_postsby ) {
							echo esc_html( $author_postsby ) . ' ' . get_the_author();
						} else {
							/* translators: %s: The username. */
							printf( esc_html__( 'Posts by %s', 'atollmatrix' ), get_the_author() );
						}
						?>
					</div>
				</a>
			</div>
		</div><!-- .author-bio -->
	</div><!-- .author-description -->
</div><!-- .author-info -->
</div><!-- .fullpage-item -->
