<?php
/*
Plugin Name: Thumbnails
Description: Better for thumbnails
Version: 0.1
*/

if ( !defined( 'ABSPATH' ) ) {
    exit();
}

/* ----------------------------------------------------------
  Thumbnails size
---------------------------------------------------------- */

add_action( 'init', 'wputh_apply_thumbnails_sizes' , 99, 3  );
function wputh_apply_thumbnails_sizes() {
    $default_details = array(
        'width' => 0,
        'height' => 0,
        'crop' => 0
    );
    $sizes = apply_filters( 'wputh_thumbnails_sizes' );
    if ( empty( $sizes ) ) {
        $sizes = array();
    }
    foreach ( $sizes as $id => $details ) {
        $details = array_merge(  $default_details, $details );
        add_image_size( $id , $details['width'], $details['height'], $details['crop'] ); // 220 pixels wide by 180 pixels tall, hard crop mode
    }
}

/* ----------------------------------------------------------
  Getting a thumbnail URL
---------------------------------------------------------- */

function wputh_get_thumbnail( $size, $post_id = false ) {
    global $post;

    $thumbnail_url = get_template_directory_uri() . '/images/thumbnails/' . $size . '.jpg';

    // Obtaining post parent id
    if ( !ctype_digit( $post_id ) ) {
        if ( isset( $post->ID ) ) {
            $post_id = $post->ID;
        }
        else {
            return false;
        }
    }
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    $thumbnail_details = wp_get_attachment_image_src( $thumbnail_id, $size );

    if ( !empty( $thumbnail_details ) ) {
        $thumbnail_url = $thumbnail_details[0];
    }

    return $thumbnail_url;
}
