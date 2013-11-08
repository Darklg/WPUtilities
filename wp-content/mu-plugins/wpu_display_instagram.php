<?php
/*
Plugin Name: Display Instagram
Description: Display Latest Image for Instagram
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

function wpu_get_instagram() {
    $api_key = trim( get_option( 'wpu_get_instagram__api_key' ) );
    $user_id = trim( get_option( 'wpu_get_instagram__user_id' ) );
    $transient_id = 'json_instagram_' . $user_id;

    // Get cached JSON
    $json_instagram = get_transient( $transient_id );
    if ( empty( $json_instagram ) ) {
        $json_instagram = file_get_contents( 'https://api.instagram.com/v1/users/'.$user_id.'/media/recent/?count=1&access_token='. $api_key );
        set_transient( $transient_id, $json_instagram, HOUR_IN_SECONDS );
    }
    // Extract and return informations
    $imginsta = json_decode( $json_instagram );
    $details = $imginsta->data[0];
    return array(
        'image' =>  $details->images->standard_resolution->url,
        'link' =>  $details->link,
        'caption' =>  $details->caption->text,
    );
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
