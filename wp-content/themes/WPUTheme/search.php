<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

echo '<div class="main">';
echo '<h1>'.sprintf( __( 'Search results for “%s”', 'wputh' ), get_search_query() ).'</h1>';
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        get_template_part( 'loop', 'small' );
    }
}
else {
    echo '<p>'.__( 'Sorry, no search results for this query.', 'wputh' ).'</p>';
}
wp_reset_query();
echo '</div>';
get_sidebar();
get_footer();
