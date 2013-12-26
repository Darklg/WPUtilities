<?php
include dirname( __FILE__ ) . '/z-protect.php';

$number_results = (int) $wp_query->found_posts;
$search_results = __( '<strong>no</strong> search results', 'wputh' );
if ( $number_results == 1 ) {
    $search_results = __( '<strong>1</strong> search result', 'wputh' );
}
if ( $number_results > 1 ) {
    $search_results = sprintf( __( '<strong>%s</strong> search result', 'wputh' ), $number_results );
}

get_header();
echo '<div class="main-content">';
echo '<h1>'.sprintf( __( '%s for &ldquo;%s&rdquo;', 'wputh' ), $search_results, get_search_query() ).'</h1>';
echo get_the_loop();
echo '</div>';
get_sidebar();
get_footer();
