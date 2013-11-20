<?php
/*
Plugin Name: Display Instagram
Description: Displays the latest image for an Instagram account
Version: 0.4
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

function wpu_get_instagram() {
    $api_key = trim( get_option( 'wpu_get_instagram__api_key' ) );
    $user_id = trim( get_option( 'wpu_get_instagram__user_id' ) );
    $transient_id = 'json_instagram_' . $user_id;
    $latest_id = 'latest_id_instagram_' . $user_id;
    $att_id = 'att_id_instagram_' . $user_id;

    $return = array(
        'image' => '',
        'link' => '#',
        'created_time' => '0',
        'caption' => '',
        'id' => get_option( $latest_id ),
        'att_id' => get_option( $att_id ),
    );

    // Get cached JSON
    $json_instagram = get_transient( $transient_id );
    if ( empty( $json_instagram ) ) {
        $json_instagram = file_get_contents( 'https://api.instagram.com/v1/users/'.$user_id.'/media/recent/?count=1&access_token='. $api_key );
        set_transient( $transient_id, $json_instagram, HOUR_IN_SECONDS );
    }

    // Extract and return informations
    $imginsta = json_decode( $json_instagram );

    if ( isset( $imginsta->data[0] ) ) {
        $details = $imginsta->data[0];
        // Image
        if ( isset( $imginsta->data[0]->id ) ) {
            $return['id'] = $imginsta->data[0]->id;
        }
        // Image
        if ( isset( $details->images->standard_resolution->url ) ) {
            $return['image'] = $details->images->standard_resolution->url;
        }
        // Link
        if ( isset( $details->link ) ) {
            $return['link'] = $details->link;
        }
        // Created time
        if ( isset( $details->created_time ) ) {
            $return['created_time'] = $details->created_time;
        }
        // Caption
        if ( isset( $details->caption->text ) ) {
            $return['caption'] = $details->caption->text;
        }
    }

    // Cache Image if necessary
    if ( !empty( $return['image'] ) && get_option( $latest_id ) != $return['id'] ) {

        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        global $wpu_get_instagram_att_id;

        add_action( 'add_attachment', 'wpu_get_instagram_catch_att_id' );
        media_sideload_image( $return['image'], 0 );
        remove_action( 'add_attachment', 'wpu_get_instagram_catch_att_id' );

        update_option( $latest_id, $return['id'] );
        update_option( $att_id, $wpu_get_instagram_att_id );
        $return['att_id'] = $wpu_get_instagram_att_id;

    }
    return $return;
}

/* ----------------------------------------------------------
  Catches the attribute id
---------------------------------------------------------- */

$wpu_get_instagram_att_id = false;
function wpu_get_instagram_catch_att_id( $att_id ) {
    global $wpu_get_instagram_att_id;
    $wpu_get_instagram_att_id = $att_id;
}

/* ----------------------------------------------------------
  Add administration with WPU Options plugin
---------------------------------------------------------- */

add_filter( 'wpu_options_boxes', 'wpu_get_instagram_options_boxes', 12, 3 );
function wpu_get_instagram_options_boxes( $boxes ) {
    $boxes['instagram_config'] = array( 'name' => 'Plugin: Display Instagram' );
    return $boxes;
}

add_filter( 'wpu_options_fields', 'wpu_get_instagram_options_fields', 12, 3 );
function wpu_get_instagram_options_fields( $options ) {
    $options['wpu_get_instagram__api_key'] = array( 'label' => 'API Key', 'box' => 'instagram_config' );
    $options['wpu_get_instagram__user_id'] = array( 'label' => 'User ID' , 'box' => 'instagram_config' );
    return $options;
}
