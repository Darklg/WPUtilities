<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

$shown_title = __( 'Archive', 'wputh' );
if ( is_tag() ) {
    $shown_title = __( 'Tag:', 'wputh' ).' '.single_cat_title( "", false );
}
if ( is_category() ) {
    $shown_title = __( 'Category:', 'wputh' ).' '.single_cat_title( "", false );
}

echo '<div class="main">';
echo '<h1>'.$shown_title.'</h1>';
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        get_template_part( 'loop', 'small' );
    }
}
wp_reset_query();
echo '</div>';
get_sidebar();
get_footer();
