<?php

/*
Plugin Name: WPU Cache Post Types
Description: Cache all values of a post type
Version: 0.2.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUCachePostTypes {
    private $cache_key = 'wpucacheposttypes_pt_';
    private $post_types = array();

    public function __construct() {
        add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
        add_action('save_post', array(&$this, 'save_post'));
    }

    public function plugins_loaded() {
        $this->post_types = apply_filters('wpucacheposttypes__list', $this->post_types);
    }

    public function get_post_type($post_type, $nocache = false) {
        $cache_id = $this->cache_key . $post_type;
        $items = array();

        if (!array_key_exists($post_type, $this->post_types)) {
            return $items;
        }

        $pt = $this->post_types[$post_type];
        if (!is_array($pt)) {
            $pt = array();
        }
        if (!isset($pt['fields'])) {
            $pt['fields'] = array();
        }

        $items = wp_cache_get($cache_id);
        if ($items === false || $nocache) {
            // Load results
            $args = apply_filters('wpucacheposttypes__pt_args', array(
                'post_type' => $post_type,
                'orderby' => 'date',
                'order' => 'ASC',
                'posts_per_page' => -1
            ), $post_type);
            $posts = get_posts($args);
            $items = array();
            foreach ($posts as $item_post) {
                $item = array();
                $item['post'] = $item_post;
                foreach ($pt['fields'] as $key => $meta_name) {
                    $item[$key] = get_post_meta($item_post->ID, $meta_name, 1);
                }
                $items[] = $item;
            }

            wp_cache_set($cache_id, $items, '', 0);
        }

        return $items;

    }

    public function save_post($post_id) {
        $post_type = get_post_type($post_id);
        if (!array_key_exists($post_type, $this->post_types)) {
            return;
        }
        /* Reload post type */
        $this->get_post_type($post_type, 1);
    }
}

$WPUCachePostTypes = new WPUCachePostTypes();
