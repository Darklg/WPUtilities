<?php
/*
Plugin Name: WPU disable posts
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable all posts
Version: 0.6
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Remove edit menu
---------------------------------------------------------- */

add_action('admin_menu', 'wputh_disable_posts_pages');
function wputh_disable_posts_pages() {
    remove_menu_page('edit.php');
    global $menu;
    unset($menu[5]);
}

/* ----------------------------------------------------------
  Remove Posts and Comments RSS feeds
---------------------------------------------------------- */

add_action('template_redirect', 'wputh_disable_posts_rss_feeds');
function wputh_disable_posts_rss_feeds() {
    remove_action('wp_head', 'feed_links', 2);
}

/* ----------------------------------------------------------
  Disable post single view
---------------------------------------------------------- */

add_action('template_redirect', 'wputh_disable_posts_check_single');
function wputh_disable_posts_check_single() {
    if (is_singular('post')) {
        wp_redirect(site_url());
        die;
    }
}

/* ----------------------------------------------------------
  Disable RSS feed for posts
---------------------------------------------------------- */

add_action('do_feed', 'wputh_disable_posts_disable_feed', 1);
add_action('do_feed_rdf', 'wputh_disable_posts_disable_feed', 1);
add_action('do_feed_rss', 'wputh_disable_posts_disable_feed', 1);
add_action('do_feed_rss2', 'wputh_disable_posts_disable_feed', 1);
add_action('do_feed_atom', 'wputh_disable_posts_disable_feed', 1);

function wputh_disable_posts_disable_feed() {
    global $post;
    if (isset($post->post_type) && $post->post_type == 'post') {
        wp_die(sprintf(__('Our RSS feed is disabled. Please <a href="%s">visit our homepage</a>.', 'wputh'), home_url()));
    }
}

/* ----------------------------------------------------------
  Remove dashboard widget
---------------------------------------------------------- */

function wputh_disable_posts_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    if (isset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'])) {
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    }
}

add_action('wp_dashboard_setup', 'wputh_disable_posts_remove_dashboard_widgets');

/* ----------------------------------------------------------
  Remove count
---------------------------------------------------------- */

add_filter('wp_count_posts', 'wputh_disable_posts_remove_count', 10, 3);
function wputh_disable_posts_remove_count($counts, $type, $perm) {
    if ($type == 'post') {
        return 0;
    }
    return $counts;
}
