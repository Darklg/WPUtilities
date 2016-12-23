<?php

/*
Plugin Name: WPU DB Thumbnail
Description: Store a small thumbnail in db
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action('save_post', 'wpudbthumbnail_init');
function wpudbthumbnail_init($post_id = false) {
    // Invalid post
    if (!is_numeric($post_id) || wp_is_post_revision($post_id)) {
        return false;
    }

    // Thumbnail do not exists
    $post_thumbnail_id = get_post_thumbnail_id($post_id);
    if (!is_numeric($post_thumbnail_id)) {
        return false;
    }

    // Same attachment is used
    if (get_post_meta($post_id, 'wpudbthumbnail_base64thumb_id', 1) == $post_thumbnail_id) {
        return false;
    }

    // Retrieve image
    $base_image = get_attached_file($post_thumbnail_id);
    $image = wp_get_image_editor($base_image);
    if (is_wp_error($image)) {
        return;
    }

    /* Generate image */
    $type = pathinfo($base_image, PATHINFO_EXTENSION);
    $upload_dir = wp_upload_dir();
    $tmp_file = $upload_dir['basedir'] . '/tmp-thumb-' . microtime() . '.png';
    $image->resize(3);
    $image->save($tmp_file, 'image/png');
    $data = file_get_contents($tmp_file);
    unlink($tmp_file);

    /* Save as base64 */
    $base64 = 'data:image/png;base64,' . base64_encode($data);
    update_post_meta($post_id, 'wpudbthumbnail_base64thumb', $base64);
    update_post_meta($post_id, 'wpudbthumbnail_base64thumb_id', $post_thumbnail_id);
}
