<?php

/*
Plugin Name: WPU Custom Avatar
Description: Override gravatar with a custom image.
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action('init', 'wpucustomavatar_init', 50, 0);
function wpucustomavatar_init() {
    add_filter('get_avatar', 'wpucustomavatar_get_custom', 1, 5);
    add_filter('user_profile_picture_description', '__return_empty_string');
}

function wpucustomavatar_get_custom($avatar, $id_or_email, $size, $default, $alt) {
    $user = false;
    if (is_numeric($id_or_email)) {
        $id = (int) $id_or_email;
        $user = get_user_by('id', $id);
    } elseif (is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by('id', $id);
        }
    } else {
        $user = get_user_by('email', $id_or_email);
    }
    if ($user && is_object($user)) {
        $user_img = get_user_meta($user->data->ID, 'user_custom_avatar', 1);
        if (is_numeric($user_img)) {
            $avatar_arr = wp_get_attachment_image_src($user_img, $size);
            $avatar = "<img alt='{$alt}' src='{$avatar_arr[0]}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }
    }
    return $avatar;
}
