<?php
/*
Plugin Name: WP Utilities Cache HTML
Description: Simple cache for HTML
Version: 0.1
*/

add_action( 'init', 'wputh_cache_html' );
function wputh_cache_html() {

    $use_cache = true;
    if ( isset( $_SERVER['REQUEST_URI'] ) && !is_admin() && empty( $_POST ) ) {
        $use_cache = false;
    }

    // Disable or not for logged in users
    if ( is_user_logged_in() ) {
        $use_cache = false;
    }

    if ( $use_cache ) {
        $wputhCacheHTML = new wputhCacheHTML( );
        $wputhCacheHTML->verify_cache();
    }
}

function wputh_create_cache_html( $buffer ) {
    $wputhCacheHTML = new wputhCacheHTML( );
    $wputhCacheHTML->create_cache( $buffer );
    return $buffer;
}

class wputhCacheHTML {

    function __construct( ) {
        $this->set_params();
    }

    public function set_params() {

        /* Cache file */
        $this->uri = $_SERVER['REQUEST_URI'] ;
        $this->cache_id = md5( $this->uri );
        $this->cache_dir = WP_CONTENT_DIR.'/wputh-cache-html/';
        $this->cache_file = $this->cache_dir.$this->cache_id;

        /* Config */
        $this->max_age = 3600;
    }

    /* ----------------------------------------------------------
      Steps
    ---------------------------------------------------------- */

    public function verify_cache() {

        $create_cache = true;

        // If cache does not exists or is expired
        if ( file_exists( $this->cache_file ) ) {
            $filetime = time() - filemtime( $this->cache_file );
            if ( $filetime < $this->max_age ) {
                $create_cache = false;
                header( 'x-wputhchtml: HIT/'. $filetime );
            }
        }

        // Create cache
        if ( $create_cache ) {
            ob_start( 'wputh_create_cache_html' );
        }
        else {
            ob_start( 'ob_gzhandler' );
            exit( file_get_contents( $this->cache_file ) );
        }
    }

    public function create_cache( $buffer ) {
        $this->verify_folder( $this->cache_dir );
        file_put_contents( $this->cache_file, $buffer );
    }

    /* ----------------------------------------------------------
      Utilities
    ---------------------------------------------------------- */

    public function verify_folder( $folder ) {
        if ( !is_dir( $folder ) ) {
            mkdir( $folder );
            @chmod( $folder, 0755 );
        }
        return is_dir( $folder );
    }

}
