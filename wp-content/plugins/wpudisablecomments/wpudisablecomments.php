<?php
/*
Plugin Name: WPU disable comments
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable all comments
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

// Thx to : http://wordpress.stackexchange.com/a/17936

/* ----------------------------------------------------------
  Removes from admin menu
---------------------------------------------------------- */

function wputh_disable_comments_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
}

add_action( 'admin_menu', 'wputh_disable_comments_admin_menus' );

/* ----------------------------------------------------------
  Removes from post and pages
---------------------------------------------------------- */

function wputh_disable_comments_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}

add_action('init', 'wputh_disable_comments_support', 100);

/* ----------------------------------------------------------
  Removes from admin bar
---------------------------------------------------------- */

function wputh_disable_comments_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}

add_action( 'wp_before_admin_bar_render', 'wputh_disable_comments_admin_bar_render' );

/* ----------------------------------------------------------
  Send every comment to spam
---------------------------------------------------------- */

function wputh_disable_comments_send_spam( $approved , $commentdata ){
    return 'spam';
}

add_filter( 'pre_comment_approved' , 'wputh_disable_comments_send_spam' , '99', 2 );
