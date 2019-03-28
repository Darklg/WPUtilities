<?php

/*
Plugin Name: WPU Custom Avatar
Description: Override gravatar with a custom image.
Version: 0.5.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpuCustomAvatar {

    private $meta_id;

    public function __construct() {

        $this->meta_id = apply_filters('wpucustomavatar_metaname', 'user_custom_avatar');

        // Retrieve custom avatar
        add_filter('get_avatar_data', array(&$this,
            'get_avatar_data'
        ), 1, 2);

        // Hide default avatar field
        add_filter('admin_notices', array(&$this,
            'hide_default_avatar_field'
        ));

        // Add user metas fields
        add_filter('wpu_usermetas_sections', array(&$this,
            'set_usermetas_sections'
        ), 10, 3);
        add_filter('wpu_usermetas_fields', array(&$this,
            'set_usermetas_fields'
        ), 10, 3);
    }

    /* Getter */
    public function get_avatar_data($args, $id_or_email) {
        $user = false;

        /* Get user details */
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

        /* Get user avatar */
        if ($user && is_object($user)) {
            $user_img = get_user_meta($user->data->ID, $this->meta_id, 1);
            if (is_numeric($user_img)) {
                $avatar_arr = wp_get_attachment_image_src($user_img, array($args['width'], $args['height']));
                if (is_array($avatar_arr)) {
                    $args['url'] = $avatar_arr[0];
                    $args['width'] = $avatar_arr[1];
                    $args['height'] = $avatar_arr[2];
                }
            }
        }
        return $args;
    }

    /* Admin */
    public function hide_default_avatar_field() {
        $screen = get_current_screen();

        // Not profile
        if (!is_object($screen) || ($screen->base != 'profile' && $screen->base != 'user-edit')) {
            return false;
        }
        global $user_id;
        if (!is_numeric($user_id)) {
            return false;
        }

        // Get user custom avatar
        $user_img = get_user_meta($user_id, $this->meta_id, 1);
        if (!is_numeric($user_img)) {
            return false;
        }

        // Disable avatar preview in content
        add_filter('option_show_avatars', '__return_false');
    }

    /* Fields */
    public function set_usermetas_sections($sections) {
        $sections['wpu_custom_avatar'] = array(
            'name' => __('Profile Picture')
        );
        return $sections;
    }

    public function set_usermetas_fields($fields) {
        $fields[$this->meta_id] = array(
            'name' => __('Profile Picture'),
            'type' => 'image',
            'section' => 'wpu_custom_avatar'
        );
        return $fields;
    }

}

add_action('init', 'wpucustomavatar_init', 50, 0);
function wpucustomavatar_init() {
    $wpuCustomAvatar = new wpuCustomAvatar();
}
