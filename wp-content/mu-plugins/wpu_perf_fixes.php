<?php

/*
Plugin Name: WPU Perf fixes
Plugin URI: https://github.com/Darklg/WPUtilities
Description: Performance Fixes in WordPress
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Hide Audio/Video Library in medias
---------------------------------------------------------- */

add_filter('media_library_show_audio_playlist', '__return_false', 1);
add_filter('media_library_show_video_playlist', '__return_false', 1);

/* ----------------------------------------------------------
  Disable custom fields box autocomplete
---------------------------------------------------------- */

add_filter('postmeta_form_keys', 'wpu_perf_fixes__postmeta_form_keys', 999, 1);
function wpu_perf_fixes__postmeta_form_keys($content) {
    return array();
}
