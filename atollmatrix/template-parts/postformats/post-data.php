<?php
if ( atollmatrix_get_option_data( 'postsingle_tags' ) || atollmatrix_get_option_data( 'postsingle_author' ) || atollmatrix_get_option_data( 'postsingle_date' ) || atollmatrix_get_option_data( 'postsingle_categories' ) || atollmatrix_get_option_data( 'postsingle_comment' ) ) {
	?>
	<div class="postsummarywrap fullpage-item">
	<?php
	$postformat      = atollmatrix_get_postformat();
	$postformat_icon = atollmatrix_get_postformat_icon( $postformat );
	?>
		<div class="datecomment clearfix">
		<?php
		if ( is_single() ) {
			if ( atollmatrix_get_option_data( 'postsingle_tags' ) ) {
				the_tags( '<div class="post-single-tags"><i class="feather-icon-tag"></i>', ' ', '</div>' );
			}
		}
		?>
			<div class="post-single-meta-group-one">
				<?php
				if ( atollmatrix_get_option_data( 'postsingle_author' ) ) {
					?>
					<span class="post-single-meta post-single-meta-author">
					<i class="feather-icon-head"></i>
					<span class="post-meta-author">
						<?php
						the_author();
						?>
					</span>
					</span>
					<?php
				}
				if ( atollmatrix_get_option_data( 'postsingle_date' ) ) {
					?>
					<span class="post-single-meta post-single-meta-date">
						<span class="post-meta-time">
						<i class="feather-icon-clock"></i>
						<?php
							echo '<span class="date updated">';
							echo get_the_date();
							echo '</span>';
						?>
						</span>
					</span>
					<?php
				}
				?>
			</div>
			<div class="post-single-meta-group-two">
				<?php
				if ( atollmatrix_get_option_data( 'postsingle_categories' ) ) {
					?>
					<span class="post-single-meta post-meta-category post-single-meta-category">
					<i class="feather-icon-grid"></i>
					<?php
					the_category( ' / ' );
					?>
					</span>
					<?php
				}
				if ( atollmatrix_get_option_data( 'postsingle_comment' ) ) {
					$num_comments = get_comments_number();
					if ( comments_open() || $num_comments > 0 ) {
						?>
						<span class="post-single-meta post-single-meta-comment">
							<span class="post-meta-comment">
							<i class="feather-icon-speech-bubble"></i>
							<?php
							comments_popup_link( '0', '1', '%' );
							?>
							</span>
						</span>
						<?php
					}
				}
				?>
			</div>
		</div>
	</div>
	<?php
}
?>
