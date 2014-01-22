<?php
/*
Plugin Name: WP Utilities Open Sans
Description: Use an embedded version of the font Open Sans, and bypasses Google Fonts in WP Admin
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
function load_custom_wp_admin_style() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', plugins_url( '/assets/css/open-sans.css', __FILE__ ) );
}
