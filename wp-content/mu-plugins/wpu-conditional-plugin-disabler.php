<?php
/*
Plugin Name: WPU conditional plugin disabler
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable plugins on some URL schemes
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if (defined('WPUCONDITIONALPLUGINDISABLER_DESACTIVATE_PLUGINS') && WPUCONDITIONALPLUGINDISABLER_DESACTIVATE_PLUGINS) {
    add_filter('option_active_plugins', 'wpuconditionalplugindisabler_disable_plugins');
}

function wpuconditionalplugindisabler_disable_plugins($plugins) {

    $url_formats = array(
    );

    $excl_plugins = array(
    );

    if (!is_array($_SERVER) || !isset($_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'])) {
        return $plugins;
    }

    /* Disable for WP admin and AJAX requests */
    $file = $_SERVER["SCRIPT_NAME"];
    $admin_ajax_url = '/wp-admin/admin-ajax.php';
    if (is_admin() && $file != $admin_ajax_url) {
        return $plugins;
    }

    /* Enable only when URL starts with a certain text */
    $pattern_detected = $_SERVER['REQUEST_URI'] == '/';
    foreach ($url_formats as $url_format) {
        if (wpuconditionalplugindisabler_startwith($_SERVER['REQUEST_URI'], $url_format)) {
            $pattern_detected = true;
        }
    }
    if (!$pattern_detected) {
        return $plugins;
    }

    foreach ($excl_plugins as $excl_plugin) {
        $key = array_search($excl_plugin, $plugins);
        if (false !== $key) {
            unset($plugins[$key]);
        }
    }
    return $plugins;
}

function wpuconditionalplugindisabler_startwith($haystack, $needle) {
    return (substr($haystack, 0, strlen($needle)) === $needle);
}
