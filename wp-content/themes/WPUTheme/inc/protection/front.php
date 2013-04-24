<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Add copyright to content in RSS feed
---------------------------------------------------------- */
// src : http://www.catswhocode.com/blog/useful-snippets-to-protect-your-wordpress-blog-against-scrapers

add_filter( 'the_content', 'add_copyright_feed' );
function add_copyright_feed( $content ) {
    if ( is_feed() ) {
        $content .= '<hr /><p>&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) . ' - <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
    }
    return $content;
}
