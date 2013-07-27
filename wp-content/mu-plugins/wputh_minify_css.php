<?php
/*
Plugin Name: WP Utilities Minify CSS
Description: Minify and concatenate CSS
Version: 0.3
*/

function wputh_minify_css() {
    new wputhMinifyCSS();
}

if ( !WP_DEBUG || isset($_GET['regenerate_css']) ) {
    add_action( 'wp_head', 'wputh_minify_css', 1 );
}


class wputhMinifyCSS {

    function __construct() {
        global $wp_styles;

        // Setting vars

        $cache_base_dir = get_template_directory();
        $cache_base_url = get_template_directory_uri();
        $cache_folder = '/wputh-minify-cache/';
        $cache_file = 'cache.css';

        $cache_path = $cache_base_dir.$cache_folder.$cache_file;
        $cache_url = $cache_base_url.$cache_folder.$cache_file;

        $work_queue = $this->construct_work_queue( $wp_styles );

        $this->opt_hash_cache = get_option( 'wputh_minify_css_hash_cache' );
        $hash_cache = $this->generate_hash( $work_queue, $cache_path );

        // If total hash is different or cache file does not exists
        if ( $this->should_regenerate($hash_cache, $cache_path)) {
            $this->generate_cache( $work_queue, $cache_path );
        }

        $this->enqueue_css( 'wputh-minify-cache-css', $cache_url, $hash_cache );
    }

    /* ----------------------------------------------------------
      Steps
    ---------------------------------------------------------- */

    private function construct_work_queue( $wp_styles ) {
        $work_queue = array();
        foreach ( $wp_styles->queue as $id ) {
            if ( isset( $wp_styles->registered[$id] ) ) {
                $file_dir = trim( str_replace( site_url(), ABSPATH, $wp_styles->registered[$id]->src ) );

                // If local file
                if ( file_exists( $file_dir ) ) {

                    // - getting filemtime
                    $work_queue[] = array( $file_dir, filemtime( $file_dir ) );

                    // - remove from queue
                    wp_dequeue_style( $id );
                }
            }
        }
        return $work_queue;
    }

    private function generate_cache( $work_queue, $cache_file ) {
        $return = false;
        $hash_cache = $this->generate_hash( $work_queue, $cache_file );
        $cache_folder = dirname( $cache_file );

        // - update hash
        update_option( 'wputh_minify_css_hash_cache', $hash_cache );

        // - regenerate cache file
        $cache_content = '';
        foreach ( $work_queue as $file ) {
            $css = trim(file_get_contents( $file[0] ));
            $css = $this->compress_css( $css );
            $css = $this->convert_url_base64( $css, $file[0] );
            $cache_content .= $css;
        }


        if ( $this->verify_folder( $cache_folder ) ) {
            $return = ( file_put_contents( $cache_file, $cache_content ) !== false );
        }

    }

    /* ----------------------------------------------------------
      Utilities
    ---------------------------------------------------------- */

    private function compress_css( $css ) {
        // Suppression des commentaires
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

        // Suppression des tabulations, espaces multiples, retours à la ligne, etc.
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

        // Suppression des derniers espaces inutiles
        $css = str_replace(array(' { ',' {','{ '), '{', $css);
        $css = str_replace(array(' } ',' }','} '), '}', $css);
        $css = str_replace(array(' : ',' :',': '), ':', $css);

        return $css;
    }

    private function convert_url_base64($css, $file){
        $dir_file = dirname($file) . '/';
        preg_match_all('/url\((.*)\)/',$css, $matches);
        if(!empty($matches[1])){
            foreach($matches[1] as $match){
                $match = str_replace(array('"',"'"), '',$match);
                $base64 = $this->convert_file_base64($dir_file.$match);
                if(!empty($base64)){
                    $css = str_replace($match, $base64, $css);
                }
            }
        }
        return $css;
    }

    private function convert_file_base64($file){
        $base64 = '';
        if(file_exists($file) && filesize($file) < 1000){
            $base64 = 'data:'.mime_content_type($file).';base64,'.base64_encode(file_get_contents($file));
        }
        return $base64;
    }

    private function should_regenerate($hash_cache, $cache_path){
        return
            0 != 0
            || isset($_GET['regenerate_css'])
            || !file_exists( $cache_path )
            || $hash_cache != $this->opt_hash_cache;
    }

    private function generate_hash( $work_queue,$cache_path ) {
        $return = '';
        if ( !isset( $this->hash_cache ) ) {
            $this->hash_cache = md5( json_encode( $work_queue ).'0.3' );
        }
        if(file_exists($cache_path)){
            $this->hash_cache .= '-'.filemtime($cache_path);
        }
        return $this->hash_cache;
    }

    private function verify_folder( $folder ) {
        if ( !is_dir( $folder ) ) {
            mkdir( $folder );
            @chmod( $folder, 0755 );
        }
        return is_dir( $folder );
    }

    private function enqueue_css( $file_id, $file_url, $version ) {
        wp_register_style( $file_id, $file_url, NULL, $version );
        wp_enqueue_style( $file_id );
    }
}
