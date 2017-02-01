<?php

/*
Plugin Name: WPU Settings Version
Description: Keep a custom DB version of your website
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpu_settings_version {

    public function __construct() {
        add_action('wp_loaded', array(&$this, 'wp_loaded'));
    }

    public function wp_loaded() {
        $new_version = 0;
        $current_version = $this->get_current_version();

        /* Retrieve actions, sorted by version */
        $actions = $this->get_actions();
        foreach ($actions as $v => $action) {
            /* Action has already been launched */
            if ($v <= $current_version) {
                continue;
            }
            /* Launch action */
            call_user_func($action);
            /* Save new version */
            if ($v > $new_version) {
                $new_version = $v;
            }
        }

        /* Save new version */
        if ($new_version > $current_version) {
            update_option('wpusettingsversion_version', $new_version);
        }

    }

    /**
     * Get current version
     * @return int Version number
     */
    public function get_current_version() {
        $current_version = get_option('wpusettingsversion_version');
        if (!$current_version) {
            $current_version = 0;
        }
        return intval($current_version, 10);
    }

    /**
     * Get actions
     * @return array Array of function callbacks
     */
    public function get_actions() {
        $actions = apply_filters('wpusettingsversion_actions', array());
        arsort($actions);
        if (!is_array($actions)) {
            $actions = array();
        }
        return $actions;
    }
}

$wpu_settings_version = new wpu_settings_version();
