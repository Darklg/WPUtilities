<?php
$number_results = (int) $wp_query->found_posts;
$search_results = __( '<strong>no</strong> search results', 'wputh' );
if ( $number_results == 1 ) {
    $search_results = __( '<strong>1</strong> search result', 'wputh' );
}
if ( $number_results > 1 ) {
    $search_results = sprintf( __( '<strong>%s</strong> search result', 'wputh' ), $number_results );
}