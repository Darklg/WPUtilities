<?php

/*
Plugin Name: WP Utilities Admin Protect
Description: Restrictive options for WordPress admin
Version: 0.7
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if (!defined('ABSPATH')) {
    exit();
}

/* ----------------------------------------------------------
  Levels
---------------------------------------------------------- */

define('WPUTH_ADMIN_MAX_LVL', 'manage_options');
define('WPUTH_ADMIN_MIN_LVL', 'manage_categories');

/* ----------------------------------------------------------
  Block capabilities
---------------------------------------------------------- */

if (is_admin()) {

    /* if the user is not an administrator, kill WordPress execution and provide a message */

    add_action('admin_init', 'wputh_block_admin', 1);
    function wputh_block_admin() {
        if (!current_user_can(WPUTH_ADMIN_MIN_LVL) && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php') {
            wp_die(__('You are not allowed to access this part of the site'));
        }
    }

    /* Hide Updates */

    add_action('admin_menu', 'wputh_remove_update_nag');
    function wputh_remove_update_nag() {
        if (!current_user_can(WPUTH_ADMIN_MAX_LVL)) {
            remove_action('admin_notices', 'update_nag', 3);
        }
    }

    /* Hide Errors for non admins */
    add_action('init', 'wputh_hide_errors');
    function wputh_hide_errors() {
        if (!current_user_can(WPUTH_ADMIN_MIN_LVL)) {
            @error_reporting(0);
            @ini_set('display_errors', 0);
        }
    }
}

/* ----------------------------------------------------------
  Constants
---------------------------------------------------------- */

if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}
if (!defined('DISALLOW_FILE_MODS')) {
    define('DISALLOW_FILE_MODS', true);
}

/* ----------------------------------------------------------
  Users settings
---------------------------------------------------------- */

/* Thanks to http://blog.secupress.fr/ajoutez-point-securite-facilement-astuce-156.html */

/* Disable user registration */
add_filter('pre_option_users_can_register', 'wputh_admin_option_users_can_register');
function wputh_admin_option_users_can_register($value) {
    return '0';
}

/* Default role : subscriber */
add_filter('pre_option_default_role', 'wputh_admin_option_default_role');
function wputh_admin_option_default_role($value) {
    return 'subscriber';
}

/* ----------------------------------------------------------
  Block WordPress version info
---------------------------------------------------------- */

// remove wp version param from any enqueued scripts
function wputh_admin_protect__remove_ver($src) {
    $ver = get_bloginfo('version');
    $new_ver = substr(md5(NONCE_SALT . $ver) , 0, 6);
    if (strpos($src, 'ver=' . $ver)) {
        $src = remove_query_arg('ver', $src);
        $src = add_query_arg('ver', $new_ver, $src);
    }
    return $src;
}

add_action('init', 'wputh_admin_protect_remove_versions');
function wputh_admin_protect_remove_versions() {
    wputh_admin_protect__set_htaccess("0.7");
    remove_action('wp_head', 'wp_generator');
    add_filter('the_generator', '__return_empty_string', 9999);
    add_filter('style_loader_src', 'wputh_admin_protect__remove_ver', 9999);
    add_filter('script_loader_src', 'wputh_admin_protect__remove_ver', 9999);
}

function wputh_admin_protect__set_htaccess($opt_ver = '0.0') {
    $opt = 'wputh_admin_protect__has_htaccess';
    $ver = get_option($opt);
    if ($ver == $opt_ver) {
        return;
    }
    $htaccess_file = ABSPATH . '/.htaccess';
    $htaccess_content = file_get_contents($htaccess_file);
    $htaccess_content = preg_replace("/\n\#\ STARTWPUADMINPROTECT(.*)\#\ ENDWPUADMINPROTECT\n/isU", "", $htaccess_content);
    $htaccess_content = "\n# STARTWPUADMINPROTECT
# WP Utilities Admin Protect - v ${opt_ver}
# - Security requirements
RewriteEngine On
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
RewriteRule ^(.*)$ index.php [F,L]
# - Disable directory browsing
Options All -Indexes
# - Protect files
<FilesMatch (^wp-config\.php|^timthumb\.php|^readme\.html|^README\.md|^license\.html|^debug\.log)>
Deny from all
</FilesMatch>
# - Disallow PHP Easter Egg
RewriteCond %{QUERY_STRING} \=PHP[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12} [NC]
RewriteRule .* - [F,L]
# Remove Server Signature
Header unset Server
ServerSignature Off
# ENDWPUADMINPROTECT\n" . $htaccess_content;
    @file_put_contents($htaccess_file, $htaccess_content);
    update_option($opt, $opt_ver);
}

