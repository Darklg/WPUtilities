<?php
/*
Plugin Name: WPU disable attachments pages
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable all attachments
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUDisableAttachments {
    public function __construct() {
        add_action('template_redirect', array(&$this, 'template_redirect'));
        add_action('admin_head', array(&$this, 'admin_head'));
    }

    public function template_redirect() {
        global $post;
        if (!is_object($post) || $post->post_type != 'attachment' || !is_numeric($post->post_parent)) {
            return;
        }
        $post_parent_url = $post->post_parent == 0 ? home_url() : get_permalink($post->post_parent);
        wp_redirect($post_parent_url);
        die;
    }

    public function admin_head() {
        echo '<style>#post_type[value="attachment"] ~ #poststuff #edit-slug-box,.attachment-info .actions a.view-attachment {display:none;}</style>';
    }
}

$WPUDisableAttachments = new WPUDisableAttachments();
