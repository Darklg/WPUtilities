<?php

/*
Plugin Name: WPU TinyMCE Buttons
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Add new buttons to TinyMCE
Version: 0.4.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUTinyMCE
{
    function __construct() {
        if (!is_admin()) {
            return;
        }

        $upload_dir = wp_upload_dir();
        $this->up_dir = $upload_dir['basedir'] . '/wpu_tinymce-cache';
        $this->up_url = $upload_dir['baseurl'] . '/wpu_tinymce-cache';
        $this->plugin_assets_dir = dirname(__FILE__) . '/assets/';
        $this->plugin_version = "0.4.1";

        add_action('init', array(&$this,
            'check_buttons_list'
        ));
        add_action('init', array(&$this,
            'set_options'
        ));
        add_action('init', array(&$this,
            'set_buttons'
        ));
    }

    function check_buttons_list() {

        // Import buttons
        $buttons = apply_filters('wputinymce_buttons', array());
        $this->buttons = array();

        // Check values
        foreach ($buttons as $button_id => $button) {

            $button['id'] = $button_id;

            // Default image
            if (!isset($button['image'])) {
                $button['image'] = $this->up_url . '/icon-list.png';
            }

            // Default title
            if (!isset($button['title']) || empty($button['title'])) {
                $button['title'] = ucwords(str_replace('_', ' ', $button_id));
            }

            if (isset($button['html'])) {
                $this->buttons[$button_id] = $button;
            }
        }

        // Check version
        $buttons_version = md5($this->plugin_version.serialize($this->buttons));
        $buttons_version_option = get_option('wputinymce_buttons_list');

        // Same version : quit
        if ($buttons_version == $buttons_version_option) {
            return;
        }

        // Else : regenerate JS
        $this->regenerate_js_file();

        // Save version
        update_option('wputinymce_buttons_list', $buttons_version);
    }

    function regenerate_js_file() {

        // Check cache directory
        if (!is_dir($this->up_dir)) {
            @mkdir($this->up_dir, 0777);
            @chmod($this->up_dir, 0777);
        }

        // Regenerate JS
        $js = "(function(){\n";
        $js.= "var wpu_tinymce_items = [];\n";

        foreach ($this->buttons as $button_id => $button) {
            $js.= "wpu_tinymce_items.push(" . json_encode($button) . ");\n";
        }

        $js.= file_get_contents($this->plugin_assets_dir . "tinymce-create.js") . "\n";
        $js.= "}());";

        file_put_contents($this->up_dir . '/cache.js', $js);

        // Copy default icon
        if (!file_exists($this->up_dir . '/icon-list.png')) {
            copy($this->plugin_assets_dir . "icon-list.png", $this->up_dir . '/icon-list.png');
        }
    }

    function set_options() {
        $this->options = array(
            'plugin-id' => 'wpu_tinymce',
            'buttons' => array()
        );
        foreach ($this->buttons as $button_id => $button) {
            $this->options['buttons'][] = $button_id;
        }
    }

    function set_buttons() {
        if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
            add_filter("mce_external_plugins", array(&$this,
                'add_plugins'
            ));
            add_filter('mce_buttons', array(&$this,
                'add_buttons'
            ));
        }
    }

    function add_plugins($plugins = array()) {
        $plugins[$this->options['plugin-id']] = $this->up_url . '/cache.js';
        return $plugins;
    }

    function add_buttons($buttons = array()) {
        foreach ($this->options['buttons'] as $button) {
            $buttons[] = $button;
        }
        return $buttons;
    }
}

new WPUTinyMCE();

