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
function wputh_excerpt_length( $length ) {
    return 15;
}

add_filter( 'excerpt_more', 'wputh_excerpt_more' );
function wputh_excerpt_more( $more ) {
    return ' &hellip; ';
}

/* ----------------------------------------------------------
  Title
---------------------------------------------------------- */

function wputh_wp_title( $title, $sep ) {
    $spaced_sep = ' ' . $sep . ' ';
    $new_title = '';
    // Home : Exception for order
    if ( is_home() ) {
        return get_bloginfo( 'name' ) . $spaced_sep . get_bloginfo( 'description' );
    }
    if ( is_singular() ) {
        $new_title = get_the_title();
    }
    if ( is_tax() || is_tag() || is_category() ) {
        $new_title = single_cat_title( "", false );
    }

    // Return new title with site name at the end
    return $new_title . $spaced_sep . get_bloginfo( 'name' );
}
add_filter( 'wp_title', 'wputh_wp_title', 10, 2 );
