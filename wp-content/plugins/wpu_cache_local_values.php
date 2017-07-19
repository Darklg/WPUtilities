<?php

/*
Plugin Name: WPU Cache Local Values
Description: Add Cache to some local values
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* @TODO : longer cache & clear at hook save_post */

/* ----------------------------------------------------------
  Cache url_to_postid
---------------------------------------------------------- */

$global_cache_wputhcacheurltopostid = array();
add_action('url_to_postid', 'wputhcacheurltopostid', 10, 1);
function wputhcacheurltopostid($url) {
    global $global_cache_wputhcacheurltopostid;

    $url_key = md5($url);
    $cache_id = 'wputhcacheurltopostid_' . $url_key;

    // GET CACHED VALUE
    $result = wp_cache_get($cache_id);
    if ($result !== false) {
        return $result;
    }

    // GET NON PERSISTENT CACHED VALUE
    if (array_key_exists($url_key, $global_cache_wputhcacheurltopostid)) {
        return $global_cache_wputhcacheurltopostid[$url_key];
    }

    // COMPUTE RESULT
    remove_action('url_to_postid', 'wputhcacheurltopostid', 10);
    $_id = url_to_postid($url);
    add_action('url_to_postid', 'wputhcacheurltopostid', 10, 1);

    if (is_numeric($_id)) {
        $global_cache_wputhcacheurltopostid[$url_key] = $result;
        wp_cache_set($cache_id, $result, '', 60);
    }
    return $url;
}
