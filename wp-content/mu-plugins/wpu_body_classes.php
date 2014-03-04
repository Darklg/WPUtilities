<?php
/*
Plugin Name: WPU Body Classes
Description: Add more body classes to WordPress
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpuBodyClasses {
    function __construct() {
        add_filter( 'body_class', array( &$this, 'wpu_body_classes' ) );
    }

    function wpu_body_classes( $classes ) {
        global $post;
        if ( is_single() ) {
            if ( has_post_thumbnail( $post->ID ) ) {
                $classes[] = 'has-post-thumbnail';
            }
            // Categories
            $classes = array_merge( $classes, $this->add_taxonomies_classes( $post, 'category' ) );
            // Tags
            $classes = array_merge( $classes, $this->add_taxonomies_classes( $post, 'post_tag', 'tag' ) );
        }
        return $classes;
    }

    function add_taxonomies_classes( $post, $taxonomy, $taxonomy_slug = false ) {
        $classes = array();
        if ( is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
            if ( $taxonomy_slug == false ) {
                $taxonomy_slug = $taxonomy;
            }
            $categories = get_the_category( $post->ID );
            foreach ( $categories as $cat ) {
                if ( !empty( $cat->slug ) ) {
                    $classes[] = sanitize_html_class( $taxonomy_slug . '-' . $cat->slug, $cat->term_id );
                }
            }
        }
        return $classes;
    }
}

$wpuBodyClasses = new wpuBodyClasses();
