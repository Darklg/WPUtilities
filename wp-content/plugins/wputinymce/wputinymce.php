<?php

/*
Plugin Name: WPU TinyMCE Buttons
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Add new buttons to TinyMCE
Version: 0.2
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
        $this->plugin_dir = dirname(__FILE__);

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
        $this->buttons = apply_filters('wputinymce_buttons', array());

        // Check version
        $buttons_version = md5(serialize($this->buttons));
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
            $js.= "wpu_tinymce_items.push({
        id: '".$button_id."',
        title: '".$button['title']."',
        html: '".$button['html']."'
    });\n";
        }

        $js.= file_get_contents($this->plugin_dir."/assets/tinymce-create.js") . "\n";
        $js.= "}());";

        file_put_contents($this->up_dir . '/cache.js', $js);

        // Copy image
        copy($this->plugin_dir."/assets/icon-list.png",$this->up_dir.'/icon-list.png');

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

