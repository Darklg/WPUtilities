<?php

/*
Plugin Name: WPU viewed posts
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Track most viewed posts
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUViewedPosts {
    function __construct() {
        add_action('wp_enqueue_scripts', array(&$this,
            'js_callback'
        ));
        add_action('wp_ajax_wpuviewedposts_track_view', array(&$this,
            'track_view'
        ));
        add_action('wp_ajax_nopriv_wpuviewedposts_track_view', array(&$this,
            'track_view'
        ));
    }

    function js_callback() {
        if (!is_singular()) {
            return;
        }
        wp_enqueue_script('wpuviewedposts-tracker', plugins_url('/assets/js/tracker.js', __FILE__) , array(
            'jquery'
        ));
        wp_localize_script('wpuviewedposts-tracker', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php') ,
            'post_id' => get_the_ID()
        ));
    }

    function track_view() {
        $post_id = intval($_POST['post_id']);
        if (!is_numeric($post_id)) {
            return;
        }
        $nb_views = intval(get_post_meta($post_id, 'wpuviewedposts_nbviews', 1));
        update_post_meta($post_id, 'wpuviewedposts_nbviews', ++$nb_views);
        wp_die();
    }
}

$WPUViewedPosts = new WPUViewedPosts();


