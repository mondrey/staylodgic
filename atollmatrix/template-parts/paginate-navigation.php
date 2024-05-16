<div class="clearfix"></div>
<!-- ADD Custom Numbered Pagination code. -->
<?php
if ( isset( $additional_loop ) ) {
	atollmatrix_pagination( $additional_loop->max_num_pages );
} else {
	atollmatrix_pagination();
}