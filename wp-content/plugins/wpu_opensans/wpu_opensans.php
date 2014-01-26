<?php
/*
Plugin Name: WP Utilities Open Sans
Description: Use an embedded version of the font Open Sans, and bypasses Google Fonts in WP Admin
Version: 0.1.3
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action( 'wp_enqueue_scripts', 'wpu_opensans_enqueue_style' );
add_action( 'admin_enqueue_scripts', 'wpu_opensans_enqueue_style' );
function wpu_opensans_enqueue_style() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', plugins_url( '/assets/css/open-sans.css', __FILE__ ) );
}
