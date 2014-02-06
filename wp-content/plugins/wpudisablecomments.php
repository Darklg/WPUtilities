<?php
/*
Plugin Name: WPU disable comments
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable all comments
Version: 1.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

// Thx to : http://wordpress.stackexchange.com/a/17936

/* ----------------------------------------------------------
  Remove from main widget
---------------------------------------------------------- */

function wputh_disable_comments_css() {
    echo "<style>#dashboard_right_now .table_discussion {display:none !important;}</style>";
}

add_action( 'admin_head', 'wputh_disable_comments_css' );

/* ----------------------------------------------------------
  Remove dashboard widget
---------------------------------------------------------- */

function wputh_disable_comments_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] ) ) {
        unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
    }
}

add_action( 'wp_dashboard_setup', 'wputh_disable_comments_remove_dashboard_widgets' );

/* ----------------------------------------------------------
  Hide from admin menu
---------------------------------------------------------- */

function wputh_disable_comments_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
    remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}

add_action( 'admin_menu', 'wputh_disable_comments_admin_menus' );

/* ----------------------------------------------------------
  Removes from all post types
---------------------------------------------------------- */

function wputh_disable_comments_support() {
    $post_types = get_post_types(  array(
            'public'   => true
        ), 'names' );
    foreach ( $post_types as $post_type ) {
        remove_post_type_support( $post_type, 'comments' );
    }
}

add_action( 'init', 'wputh_disable_comments_support', 100 );

/* ----------------------------------------------------------
  Removes from admin bar
---------------------------------------------------------- */

function wputh_disable_comments_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}

add_action( 'wp_before_admin_bar_render', 'wputh_disable_comments_admin_bar_render' );

/* ----------------------------------------------------------
  Send every comment to spam
---------------------------------------------------------- */

function wputh_disable_comments_send_spam( $approved , $commentdata ) {
    return 'spam';
}

add_filter( 'pre_comment_approved' , 'wputh_disable_comments_send_spam' , '99', 2 );

/* ----------------------------------------------------------
  Force options
---------------------------------------------------------- */

/* Disable new comments */

function wputh_disable_comments_option_default_comment_status( $value ) {
    return 'closed';
}

add_filter( 'pre_option_default_comment_status', 'wputh_disable_comments_option_default_comment_status' );

/* Disable new pings */

function wputh_disable_comments_option_default_ping_status( $value ) {
    return 'closed';
}

add_filter( 'pre_option_default_ping_status', 'wputh_disable_comments_option_default_ping_status' );

/* ----------------------------------------------------------
  Disable pings
---------------------------------------------------------- */

function wputh_disable_comments_disable_ping( &$links ) {
    $links = array();
}

add_action( 'pre_ping', 'wputh_disable_comments_disable_ping' );

/* ----------------------------------------------------------
  Disable comments RSS feed
---------------------------------------------------------- */

function wputh_disable_comments_feed_links() {
    if ( function_exists( 'remove_theme_support' ) ) {
        remove_theme_support( 'automatic-feed-links' );
    }
}

add_action( 'init', 'wputh_disable_comments_feed_links' );
