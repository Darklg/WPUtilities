<?php
/*
Plugin Name: WPU Medium Large Image
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Add an admin field for the hidden medium_large image format.
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUMediumLargeImage {
    public function __construct() {
        add_filter('plugins_loaded', array(&$this, 'plugins_loaded'));
    }

    public function plugins_loaded() {
        add_action('admin_init', array(&$this, 'add_settings'));
        add_filter('whitelist_options', array(&$this, 'whitelist_options'));
    }

    public function add_settings() {
        add_settings_field(
            'medium_large_size',
            __('Medium Large'),
            array(&$this, '_settings_field'),
            'media',
            'default',
            array()
        );
    }

    public function _settings_field() {
        echo '<fieldset>';
        echo '<legend class="screen-reader-text">';
        echo '<span>' . __('Medium Large') . '</span>';
        echo '</legend>';
        echo '<label for="medium_large_size_w">' . __('Max Width') . '</label>';
        echo '<input name="medium_large_size_w" type="number" step="1" min="0" id="medium_large_size_w" value="' . esc_attr(get_option('medium_large_size_w')) . '" class="small-text" />';
        echo '<br>';
        echo '<label for="medium_large_size_h">' . __('Max Height') . '</label>';
        echo '<input name="medium_large_size_h" type="number" step="1" min="0" id="medium_large_size_h" value="' . esc_attr(get_option('medium_large_size_h')) . '" class="small-text" />';
        echo '</fieldset>';
    }

    public function whitelist_options($whitelist_options) {
        $whitelist_options['media'][] = 'medium_large_size_w';
        $whitelist_options['media'][] = 'medium_large_size_h';
        return $whitelist_options;
    }
}

$WPUMediumLargeImage = new WPUMediumLargeImage();
