<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

if ( !is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'wputh_add_stylesheets' );
}

// Base values
define( 'WPU_CSS_DIR', TEMPLATEPATH . '/css/' );
define( 'WPU_CSS_URL', THEME_URL . '/css/' );

function wputh_add_stylesheets() {
    $css_files = parse_path( WPU_CSS_DIR );
    foreach ( $css_files as $file ) {
        wpu_add_css_file( $file );
    }
}

function parse_path( $dir ) {
    $css_files = array();

    // Retrieving files
    $files = glob( $dir . '*', GLOB_MARK );

    // Ordering by name
    asort( $files );

    foreach ( $files as $file ) {
        // Searching for files inside a folder
        if ( is_dir( $file ) ) {
            $css_files = array_merge( parse_path( $file ), $css_files );
        }
        elseif ( substr( $file, -3, 3 ) == 'css' ) {
            $css_files[] = $file;
        }
    }

    return $css_files;
}


function wpu_add_css_file( $file ) {
    // Adding a file to the WordPress stylesheet queue
    $css_file_url = str_replace( WPU_CSS_DIR, WPU_CSS_URL, $file );
    $css_file_slug = 'wputh' . strtolower( str_replace( array( WPU_CSS_DIR, '.css' ), '', $file ) );
    wp_register_style( $css_file_slug, $css_file_url, NULL, '2.0' );
    wp_enqueue_style( $css_file_slug );
}
