<?php
/*
Plugin Name: WPU Oembed Cache
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Oembed requests are cached for 24 hours.
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUOembedCache {

    private $cache_prefix = 'oembed_cache_';
    private $cache_expires = 86400;

    public function __construct() {
        add_action('init', array(&$this, 'init'));
    }

    public function init() {
        /* Hook for config */
        $this->cache_expires = apply_filters('wpuoembedcache__cache_expires', $this->cache_expires);

        /* Actions */
        add_filter('pre_oembed_result', array(&$this, 'get_cached_content'), 10, 3);
        add_filter('oembed_result', array(&$this, 'cache_content'), 10, 3);
    }

    public function get_cached_content($content = '', $url = '', $args = array()) {
        $url_hash = $this->cache_prefix . md5($url . json_encode($args));

        // Return result if available
        $cached_content = wp_cache_get($url_hash);
        if (!empty($cached_content) && !is_null($cached_content)) {
            return $cached_content;
        }

        return $content;
    }

    public function cache_content($content = '', $url = '', $args = array()) {
        $url_hash = $this->cache_prefix . md5($url . json_encode($args));

        // Create cache
        wp_cache_set($url_hash, $content, null, $this->cache_expires);

        // Return content
        return $content;
    }

}

$WPUOembedCache = new WPUOembedCache();
