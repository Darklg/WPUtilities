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
    if ( is_archive() ) {
        $new_title = wputh_get_shown_title();
    }
    if ( is_tax() ) {
        $new_title = single_cat_title( "", false );
    }

    // Return new title with site name at the end
    return $new_title . $spaced_sep . get_bloginfo( 'name' );
}
add_filter( 'wp_title', 'wputh_wp_title', 10, 2 );


function wputh_get_shown_title() {
    global $post;
    $shown_title = __( 'Archive', 'wputh' );
    if ( is_tag() ) {
        $shown_title = __( 'Tag:', 'wputh' ) . ' ' . single_tag_title( "", false );
    }
    if ( is_category() ) {
        $shown_title = __( 'Category:', 'wputh' ) . ' ' . single_cat_title( "", false );
    }
    if ( is_author() ) {
        $curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author ) );
        $shown_title = __( 'Author:', 'wputh' ) . ' ' . $curauth->nickname;
    }
    if ( is_year() ) {
        $shown_title = __( 'Year:', 'wputh' ) . ' ' . get_the_time( __( 'Y', 'wputh' ) );
    }
    if ( is_month() ) {
        $shown_title = __( 'Month:', 'wputh' ) . ' ' . get_the_time( __( 'F Y', 'wputh' ) );
    }
    if ( is_day() ) {
        $shown_title = __( 'Day:', 'wputh' ) . ' ' . get_the_time( __( 'F j, Y', 'wputh' ) );
    }
    return $shown_title;
}
