<?php

/*
Plugin Name: WPU Settings Version
Description: Keep a custom DB version of your website
Version: 0.3.0
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

    /* ----------------------------------------------------------
      Helpers
    ---------------------------------------------------------- */

    public function upload_asset_by_path($imagepath) {

        if (!file_exists($imagepath)) {
            return false;
        }

        /* get image infos */
        $imageinfos = pathinfo($imagepath);

        /* Copy image to a tmp file */
        $wp_upload_dir = wp_upload_dir();
        $newfile = md5($imagepath) . uniqid();
        if (isset($imageinfos['extension'])) {
            $newfile .= '.' . $imageinfos['extension'];
        }
        $image = $wp_upload_dir['path'] . '/' . $newfile;
        copy($imagepath, $image);

        /* Required methods */
        require_once ABSPATH . '/wp-admin/includes/file.php';
        require_once ABSPATH . '/wp-admin/includes/media.php';
        require_once ABSPATH . '/wp-admin/includes/image.php';

        /* Upload image */
        $att = media_handle_sideload(array(
            'name' => basename($image),
            'type' => wp_check_filetype($image),
            'tmp_name' => $image,
            'error' => 0,
            'size' => filesize($image)
        ), 0);

        /* Return upload info */
        if (!is_numeric($att)) {
            $att = false;
        }
        return $att;

    }

    /* ----------------------------------------------------------
      Create menus
    ---------------------------------------------------------- */

    public function set_menus($page_ids = array(), $menus = array(), $theme_id = '') {
        $opt_id = 'theme_mods_' . $theme_id;
        $theme_mods = get_option($opt_id);
        foreach ($menus as $pos => $menu_name) {

            /* Delete menu if it exists */
            $menu_exists = wp_get_nav_menu_object($menu_name);
            if ($menu_exists) {
                wp_delete_nav_menu($menu_name);
            }
            // If it doesn't exist, let's create it.
            $menu_id = wp_create_nav_menu($menu_name);

            foreach ($page_ids as $p) {
                // Set up default menu items
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => get_the_title($p),
                    'menu-item-object-id' => $p,
                    'menu-item-db-id' => 0,
                    'menu-item-object' => 'page',
                    'menu-item-parent-id' => 0,
                    'menu-item-type' => 'post_type',
                    'menu-item-url' => get_page_link($p),
                    'menu-item-status' => 'publish'
                ));
            }

            if (!is_array($theme_mods)) {
                $theme_mods = array();
            }
            if (!isset($theme_mods['nav_menu_locations']) || !is_array($theme_mods['nav_menu_locations'])) {
                $theme_mods['nav_menu_locations'] = array();
            }
            $theme_mods['nav_menu_locations'][$pos] = $menu_id;

        }

        update_option($opt_id, $theme_mods);

    }

}

$wpu_settings_version = new wpu_settings_version();
