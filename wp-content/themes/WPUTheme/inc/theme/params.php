<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Supported features
---------------------------------------------------------- */

if ( function_exists( 'add_theme_support' ) ) {

    // Supporting thumbnails
    add_theme_support( 'post-thumbnails' );

    // Supporting RSS Links
    add_theme_support( 'automatic-feed-links' );
}

/* ----------------------------------------------------------
  Excerpt
---------------------------------------------------------- */

add_filter( 'excerpt_length', 'wputh_excerpt_length', 999 );
function wputh_excerpt_length( ) {
    return 15;
}

add_filter( 'excerpt_more', 'wputh_excerpt_more' );
function wputh_excerpt_more( ) {
    return ' &hellip; ';
}
