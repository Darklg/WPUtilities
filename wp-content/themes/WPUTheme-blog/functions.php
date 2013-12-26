<?php

/* ----------------------------------------------------------
  Add stylesheets
---------------------------------------------------------- */

if ( !is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'wputh_addsub_stylesheets', 99 );
}

// Base values
define( 'WPUSUB_CSS_DIR', get_stylesheet_directory() . '/css/' );
define( 'WPUSUB_CSS_URL', get_stylesheet_directory_uri() . '/css/' );

function wputh_addsub_stylesheets() {
    $css_files = parse_path( WPUSUB_CSS_DIR );
    foreach ( $css_files as $file ) {
        wpu_add_css_file( $file, WPUSUB_CSS_DIR, WPUSUB_CSS_URL );
    }
}

/* ----------------------------------------------------------
  Add a body class to help styling this plugin
---------------------------------------------------------- */

add_filter( 'body_class', 'wputh_blog_body_classes' );
function wputh_blog_body_classes( $classes ) {
    $classes[] = 'theme-has-sidebar';
    return $classes;
}
