<?php
/*
Plugin Name: WP Utilities admin protect
Description: Restrictive options for WordPress admin
Version: 0.2.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

/* ----------------------------------------------------------
  Levels
---------------------------------------------------------- */

define( 'WPUTH_ADMIN_MAX_LVL', 'manage_options' );
define( 'WPUTH_ADMIN_MIN_LVL', 'manage_categories' );

/* ----------------------------------------------------------
  Block capabilities
---------------------------------------------------------- */

if ( is_admin() ) {

    add_action( 'admin_init', 'wputh_block_admin', 1 );
    function wputh_block_admin() {
        // if the user is not an administrator, kill WordPress execution and provide a message
        if ( ! current_user_can( WPUTH_ADMIN_MIN_LVL ) && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php' ) {
            wp_die( __( 'You are not allowed to access this part of the site' ) );
        }
    }

    /* Hide Updates */

    add_action( 'admin_menu', 'wputh_remove_update_nag' );
    function wputh_remove_update_nag() {
        if ( !current_user_can( WPUTH_ADMIN_MAX_LVL ) ) {
            remove_action( 'admin_notices', 'update_nag', 3 );
        }
    }

}

/* ----------------------------------------------------------
  Constants
---------------------------------------------------------- */

if ( !defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}
if ( !defined( 'DISALLOW_FILE_MODS' ) ) {
    define( 'DISALLOW_FILE_MODS', true );
}