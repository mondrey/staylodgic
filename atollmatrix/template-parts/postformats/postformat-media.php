<?php
$postformat = atollmatrix_get_postformat();
if ( ! post_password_required() ) {
	get_template_part( 'template-parts/postformats/' . $postformat );
}
