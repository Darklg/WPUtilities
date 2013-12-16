<?php
/*
Plugin Name: WPU disable posts
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable all posts
Version: 0.3
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Remove post type "post" support
---------------------------------------------------------- */

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

/* ----------------------------------------------------------
  Remove edit menu
---------------------------------------------------------- */

add_action( 'admin_menu', 'wputh_disable_posts_pages' );
function wputh_disable_posts_pages() {
    remove_menu_page( 'edit.php' );
}

/* ----------------------------------------------------------
  Remove Posts and Comments RSS feeds
---------------------------------------------------------- */

add_action( 'template_redirect', 'wputh_disable_posts_rss_feeds' );
function wputh_disable_posts_rss_feeds() {
    remove_action( 'wp_head', 'feed_links', 2 );
}

/* ----------------------------------------------------------
  Disable post single view
---------------------------------------------------------- */

add_action( 'template_redirect', 'wputh_disable_posts_check_single' );
function wputh_disable_posts_check_single() {
    if ( is_singular( 'post' ) ) {
        wp_redirect( site_url() );
        die;
    }
}

/* ----------------------------------------------------------
  Disable RSS feed for posts
---------------------------------------------------------- */

add_action( 'do_feed', 'wputh_disable_posts_disable_feed', 1 );
add_action( 'do_feed_rdf', 'wputh_disable_posts_disable_feed', 1 );
add_action( 'do_feed_rss', 'wputh_disable_posts_disable_feed', 1 );
add_action( 'do_feed_rss2', 'wputh_disable_posts_disable_feed', 1 );
add_action( 'do_feed_atom', 'wputh_disable_posts_disable_feed', 1 );

function wputh_disable_posts_disable_feed() {
    global $post;
    if ( isset( $post->post_type ) && $post->post_type == 'post' ) {
        wp_die( __( 'Our RSS feed is disabled. Please <a href="'.site_url().'">visit our homepage</a>.' ) );
    }
}
