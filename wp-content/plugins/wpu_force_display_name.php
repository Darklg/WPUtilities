<?php

/*
Plugin Name: WPU Force Display Name
Description: Force WordPress Display Name
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUForceDisplayName {
    public function __construct() {
        add_action('woocommerce_checkout_update_user_meta', array(&$this, 'update_profile'));
        add_action('woocommerce_save_account_details', array(&$this, 'update_profile'));
        add_action('personal_options_update', array(&$this, 'update_profile'));
        add_action('edit_user_profile_update', array(&$this, 'update_profile'));
        add_action('user_register', array(&$this, 'update_profile'));
    }

    public function update_profile($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        $this->set_display_name($user_id);
    }

    public function set_display_name($user_id) {
        $info = get_userdata($user_id);
        if (!is_object($info)) {
            return false;
        }
        $display_name = trim($info->first_name . ' ' . $info->last_name);
        if (!$display_name) {
            $display_name = $info->user_login;
        }
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $display_name
        ));
    }

}

$WPUForceDisplayName = new WPUForceDisplayName();
