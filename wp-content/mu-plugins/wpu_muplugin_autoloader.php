<?php

/*
Plugin Name: WPU MU Plugins Autoloader
Description: Load MU-Plugins in subfolders
Version: 0.3
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Utilities
---------------------------------------------------------- */

/* Recursive GLOB */
/* Thx to https://stackoverflow.com/a/17161106 */
function wpu_muplugin_autoloader_rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);

    /* If a folder contains a file with the same name, ignore other files */
    foreach ($files as $_file) {
        $_f = str_replace('.php', '', basename($_file));
        $_dir = basename(dirname($_file));
        if ($_f == $_dir) {
            return array($_file);
        }
    }

    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, wpu_muplugin_autoloader_rglob($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}

/* Searching for PHP files in all subfolders */
function wpu_muplugin_autoloader_list() {
    $wpu_muplugin_autoloader_list = wpu_muplugin_autoloader_rglob(dirname(__FILE__) . '/*/*.php');
    /* Sorting alphanumerically */
    asort($wpu_muplugin_autoloader_list);
    return $wpu_muplugin_autoloader_list;
}

/* ----------------------------------------------------------
  Loading plugins
---------------------------------------------------------- */

$wpu_muplugin_autoloader_list = wpu_muplugin_autoloader_list();
/* Include each file */
foreach ($wpu_muplugin_autoloader_list as $wpu_muplugin_autoloader_plugin) {
    wp_register_plugin_realpath($wpu_muplugin_autoloader_plugin);
    require_once $wpu_muplugin_autoloader_plugin;
}

/* ----------------------------------------------------------
  Display in admin
---------------------------------------------------------- */

add_action('show_advanced_plugins', 'wpu_muplugin_autoloader__show_advanced_plugins', 10, 2);
function wpu_muplugin_autoloader__show_advanced_plugins($show, $type) {

    /* Check correct page */
    if (!is_admin()) {
        return;
    }
    $screen = get_current_screen();
    $current = is_multisite() ? 'plugins-network' : 'plugins';
    if ($screen->base != $current || $type != 'mustuse' || !current_user_can('activate_plugins')) {
        return $show;
    }

    /* Retrieve base plugins */
    $mu_plugins = get_mu_plugins();

    /* Add new plugins */
    global $wpu_muplugin_autoloader_list;
    foreach ($wpu_muplugin_autoloader_list as $plugin) {
        $plugin_id = str_replace(WPMU_PLUGIN_DIR . '/', '', $plugin);
        $plugin_id = str_replace('/', '___dir___', $plugin_id);
        $mu_plugins[$plugin_id] = get_plugin_data($plugin, 0, 0);
        if (!$mu_plugins[$plugin_id]['Name']) {
            $mu_plugins[$plugin_id]['Name'] = basename($plugin);
        }
        $mu_plugins[$plugin_id]['Description'] = '(Autoloaded) ' . $mu_plugins[$plugin_id]['Description'];
    }

    /* Sort list */
    uasort($mu_plugins, '_sort_uname_callback');

    /* Override plugin list */
    $GLOBALS['plugins']['mustuse'] = $mu_plugins;
}
