<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

echo '<div class="main">';
echo '<h1>'.sprintf( __( 'Search results for “%s”', 'wputh' ), get_search_query() ).'</h1>';
echo get_the_loop();
echo '</div>';
get_sidebar();
get_footer();
