<?php
/*
Plugin Name: WPU no HTTP calls
Description: Disable updates & callbacks to WordPress. Please use as a MU-Plugin.
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Disable updates
  Thanks to https://gist.github.com/willmot/2830722
---------------------------------------------------------- */

add_action('muplugins_loaded', 'wpunohttpcalls__muplugins_loaded');
function wpunohttpcalls__muplugins_loaded() {

    // Disable core update checking
    add_filter('pre_site_transient_update_core', '__return_null');
    remove_action('admin_init', '_maybe_update_core');
    remove_action('wp_version_check', 'wp_version_check');
    add_filter('admin_menu', 'wpunohttpcalls__remove_update_menu');

    // Disable plugin update checking
    remove_action('load-plugins.php', 'wp_update_plugins');
    remove_action('load-update.php', 'wp_update_plugins');
    remove_action('load-update-core.php', 'wp_update_plugins');
    remove_action('admin_init', '_maybe_update_plugins');
    remove_action('wp_update_plugins', 'wp_update_plugins');
    add_filter('pre_site_transient_update_plugins', '__return_null');

    // Disable theme update checking
    remove_action('load-themes.php', 'wp_update_themes');
    remove_action('load-update.php', 'wp_update_themes');
    remove_action('load-update-core.php', 'wp_update_themes');
    remove_action('admin_init', '_maybe_update_themes');
    remove_action('wp_update_themes', 'wp_update_themes');
    add_filter('pre_site_transient_update_themes', '__return_null');

}

function wpunohttpcalls__remove_update_menu() {
    remove_submenu_page('index.php', 'update-core.php');
}

/* ----------------------------------------------------------
  Disable browser nag
  Thanks to https://wordpress.org/plugins/no-browser-nag/
---------------------------------------------------------- */

add_action('admin_init', 'wpunohttpcalls__disable_browser_nag');
function wpunohttpcalls__disable_browser_nag() {
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        add_filter('pre_site_transient_browser_' . md5($_SERVER['HTTP_USER_AGENT']), '__return_null');
    }
}
