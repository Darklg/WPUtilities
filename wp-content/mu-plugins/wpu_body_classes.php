<?php

/*
Plugin Name: WPU Body Classes
Description: Add more body classes to WordPress
Version: 0.3
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpuBodyClasses {

    public function __construct() {
        add_filter('body_class', array(&$this,
            'wpu_body_classes'
        ));
    }

    public function wpu_body_classes($classes) {
        global $post;
        if (is_singular()) {

            // Post thumbnail
            if (has_post_thumbnail($post->ID)) {
                $classes[] = 'has-post-thumbnail';
            }

            // Post slug
            $classes[] = 'post-name_' . $post->post_name;

            $add_taxonomies = apply_filters('wpu_body_classes__taxonomies', array(
                'category' => 'category',
                'post_tag' => 'tag'
            ));
            foreach ($add_taxonomies as $taxo_id => $taxo_slug) {
                $classes = array_merge($classes, $this->add_taxonomies_classes($post, $taxo_id, $taxo_slug));
            }
        }
        return $classes;
    }

    public function add_taxonomies_classes($post, $taxonomy, $taxonomy_slug = false) {
        $classes = array();

        if (is_object_in_taxonomy($post->post_type, $taxonomy)) {
            if ($taxonomy_slug == false) {
                $taxonomy_slug = $taxonomy;
            }
            $categories = get_the_terms($post->ID,$taxonomy);
            foreach ($categories as $cat) {
                if (!empty($cat->slug)) {
                    $classes[] = sanitize_html_class($taxonomy_slug . '-' . $cat->slug, $cat->term_id);
                }
            }
        }
        return $classes;
    }
}

$wpuBodyClasses = new wpuBodyClasses();
