<?php
/*
Plugin Name: WPU disable attachments pages
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable all attachments
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action('template_redirect', 'wpudisableattachments_template_redirect');
function wpudisableattachments_template_redirect() {
    global $post;
    if (!is_object($post) || $post->post_type != 'attachment' || !is_numeric($post->post_parent)) {
        return;
    }
    $post_parent_url = get_permalink($post->post_parent);
    wp_redirect($post_parent_url);
    die;
}
