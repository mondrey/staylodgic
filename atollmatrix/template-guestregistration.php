<?php
/*
Template Name: Room Search Page
*/
?>
<?php get_header(); ?>
<?php
if ( post_password_required() ) {
    echo '<div class="entry-content" id="password-protected">';
    atollmatrix_display_password_form_action();
    echo '</div>';
} else {
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-page-wrapper entry-content clearfix">
                    <?php
                    // Debugging: Check if the shortcode function exists
                    if ( shortcode_exists('guest_registration') ) {
                        echo do_shortcode('[guest_registration]');
                    } else {
                        echo '<p>Something went wrong.</p>';
                    }
                    ?>
                </div>            
            </div><!-- .entry-content -->
            <?php
        endwhile;
    endif;
}
?>
<?php get_footer(); ?>
