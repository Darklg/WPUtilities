<?php
/*
Plugin Name: WP Utilities admin protect
Description: Restrictive options for WordPress admin
Version: 0.1
*/

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

/* ----------------------------------------------------------
  Levels
---------------------------------------------------------- */

define( 'WPUTH_ADMIN_MAX_LVL', 'update_options' );
define( 'WPUTH_ADMIN_MIN_LVL', 'read_private_pages' );

/* ----------------------------------------------------------
  Block capabilities
---------------------------------------------------------- */

if ( is_admin() ) {

    /* Hide Updates */

    add_action( 'admin_menu', 'wputh_remove_update_nag' );
    function wputh_remove_update_nag() {
        if ( !current_user_can( 'WPUTH_ADMIN_MAX_LVL' ) ) {
            remove_action( 'admin_notices', 'update_nag', 3 );
        }
    }


    /* Hide Theme Editor for all users : http://www.wprecipes.com/how-to-hide-theme-editor-from-wordpress-dashboard */
    function wpr_remove_editor_menu() {
        remove_action( 'admin_menu', '_add_themes_utility_last', 101 );
    }

    add_action( 'init', 'wputh_admin_protect' );
    function wputh_admin_protect() {
        add_action( 'admin_menu', 'wpr_remove_editor_menu', 1 );
    }

}
