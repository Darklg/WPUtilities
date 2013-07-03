<?php
/*
Plugin Name: WP Utilities Minify HTML
Description: Minify HTML
Version: 0.1
*/

if ( WP_DEBUG ) {
    add_action( 'init', 'wputh_minify_html' );
}

function wputh_minify_html() {
    ob_start( 'wputh_return_minify_html' );
}

function wputh_return_minify_html( $html ) {
    // Removing multiple spaces
    $html = preg_replace( '/(\s{2,})/', ' ', $html );
    // Removing spaces between tags
    $html = preg_replace( '/>(\s+)</', '><', $html );
    return $html;
}
