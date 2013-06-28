<?php
/*
Plugin Name: WP Utilities Minify CSS
Description: Minify and concatenate CSS
Version: 0.1
*/

function wputh_mincss_is_local( $filename ) {
    $return = 1;
    return $return;
}

add_action( 'wp_head', 'wputh_minify_css', 1 );
function wputh_minify_css() {
    global $wp_styles;

    if ( WP_DEBUG ) {
        return;
    }

    // Setting vars
    $force_regenerate = 1;
    $work_queue = array();
    $option_hash_cache = get_option( 'wputh_minify_css_hash_cache' );
    $cache_dir = '/wputh-minify-cache/';
    $cache_file = $cache_dir.'cache.css';
    $cache_file_dir = TEMPLATEPATH.$cache_file;
    $cache_file_url = get_template_directory_uri().$cache_file;

    // Empty CSS queue
    $queue = $wp_styles->queue;

    // Verify total
    foreach ( $queue as $id ) {
        if ( isset( $wp_styles->registered[$id] ) ) {
            $file_dir = str_replace( site_url(), '', $wp_styles->registered[$id]->src );
            // If local url
            if ( wputh_mincss_is_local( $file_dir ) ) {
                $file_dir = str_replace( '//', '/', ABSPATH.$file_dir );

                // - getting filemtime
                $work_queue[] = array( $file_dir, filemtime( $file_dir ) );

                // - remove from queue
                unset( $queue[array_search( $id, $queue )] );
            }
        }
    }
    $wp_styles->queue = $queue;
    $hash_cache = md5( json_encode( $work_queue ).'0.1' );

    // If total hash is different or cache file does not exists
    if ( !file_exists( $cache_file_dir ) || $hash_cache != $option_hash_cache || $force_regenerate) {
        // - update hash
        update_option( 'wputh_minify_css_hash_cache', $hash_cache );
        // - regenerate cache file
        $cache_content = '';
        foreach ( $work_queue as $file ) {
            $cache_content .= file_get_contents( $file[0] );
        }
        if ( !is_dir( TEMPLATEPATH.$cache_dir ) ) {
            mkdir( TEMPLATEPATH.$cache_dir );
            @chmod( TEMPLATEPATH.$cache_dir, 0755 );
        }
        file_put_contents( $cache_file_dir, $cache_content );
    }

    // Enqueue cache file
    wp_register_style( 'wputh-minify-cache-css', $cache_file_url, NULL, $hash_cache );
    wp_enqueue_style( 'wputh-minify-cache-css' );


}
