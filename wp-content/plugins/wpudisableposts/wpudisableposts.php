<?php
/*
Plugin Name: WPU disable posts
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable all posts
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if ( ! function_exists( 'unregister_post_type' ) ) :
    function unregister_post_type( $post_type ) {
        global $wp_post_types;
        if ( isset( $wp_post_types[ $post_type ] ) ) {
            unset( $wp_post_types[ $post_type ] );
            return true;
        }
        return false;
    }
endif;

add_action( 'init', 'wputh_disable_posts' );
function wputh_disable_posts() {
    unregister_post_type( 'post' );
}

add_action( 'admin_menu', 'wputh_disable_posts_pages' );
function wputh_disable_posts_pages() {
    remove_menu_page( 'edit.php' );
}
