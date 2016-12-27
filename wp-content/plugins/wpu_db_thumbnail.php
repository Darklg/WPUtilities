<?php

/*
Plugin Name: WPU DB Thumbnail
Description: Store a small thumbnail in db
Version: 0.3
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action('save_post', 'wpudbthumbnail_init');
function wpudbthumbnail_init($post_id = false) {
    $jpeg_quality = apply_filters('wpudbthumbnail_jpegquality', 30);
    $image_size = apply_filters('wpudbthumbnail_imagesize', 10);

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
    $mime_type = 'image/' . $type;
    if ($type == 'jpg' || $type == 'jpeg') {
        $mime_type = 'image/jpeg';
        $type = 'jpg';
    }
    $upload_dir = wp_upload_dir();
    $image->resize($image_size, $image_size, false);
    if ($type == 'jpg') {
        $image->set_quality($jpeg_quality);
    }
    $tmp_file = $image->generate_filename('tmp-thumb', $upload_dir['basedir'], $type);
    $image->save($tmp_file, $mime_type);
    $data = file_get_contents($tmp_file);
    unlink($tmp_file);

    /* Save as base64 */
    $base64 = 'data:' . $mime_type . ';base64,' . base64_encode($data);
    update_post_meta($post_id, 'wpudbthumbnail_base64thumb', $base64);
    update_post_meta($post_id, 'wpudbthumbnail_base64thumb_id', $post_thumbnail_id);
}
