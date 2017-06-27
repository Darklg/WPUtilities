<?php

/*
Plugin Name: WPU MU Plugins Autoloader
Description: Load MU-Plugins in subfolders
Version: 0.1
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
