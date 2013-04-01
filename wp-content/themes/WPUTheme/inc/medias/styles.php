<?php

if ( !is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'wputh_add_stylesheets' );
}

function wputh_add_stylesheets() {
    // Base values
    $css_dir = TEMPLATEPATH . '/css/';
    $css_url = THEME_URL . '/css/';
    // Retrieving CSS files
    $css_files = glob( $css_dir . '*.css' );
    // Ordering by name
    asort($css_files);
    foreach ( $css_files as $file ) {
        // Adding each file to the WordPress stylesheet queue
        $css_file_url = str_replace( $css_dir, $css_url, $file );
        $css_file_slug = 'wputh' . strtolower( str_replace( array( $css_dir, '.css' ), '', $file ) );
        wp_register_style( $css_file_slug, $css_file_url, NULL, '2.0' );
        wp_enqueue_style( $css_file_slug );
    }
}
