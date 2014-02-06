<?php
/*
Plugin Name: WP Utilities admin protect
Description: Restrictive options for WordPress admin
Version: 0.4
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

    /* if the user is not an administrator, kill WordPress execution and provide a message */

    add_action( 'admin_init', 'wputh_block_admin', 1 );
    function wputh_block_admin() {
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


    /* Hide Errors for non admins */
    add_action( 'init', 'wputh_hide_errors' );
    function wputh_hide_errors() {
        if ( ! current_user_can( WPUTH_ADMIN_MIN_LVL ) ) {
            @error_reporting( 0 );
            @ini_set( 'display_errors', 0 );
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

/* ----------------------------------------------------------
  Users settings
---------------------------------------------------------- */

/* Thanks to http://blog.secupress.fr/ajoutez-point-securite-facilement-astuce-156.html */

/* Disable user registration */
add_filter( 'pre_option_users_can_register', 'wputh_admin_option_users_can_register' );
function wputh_admin_option_users_can_register( $value ) {
    return '0';
}

/* Default role : subscriber */
add_filter( 'pre_option_default_role', 'wputh_admin_option_default_role' );
function wputh_admin_option_default_role( $value ) {
    return 'subscriber';
}
