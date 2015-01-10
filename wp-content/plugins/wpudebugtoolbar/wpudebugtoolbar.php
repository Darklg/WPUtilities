<?php

/*
Plugin Name: WP Utilities Debug toolbar
Description: Display a debug toolbar for developers.
Version: 0.6
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUDebugToolbar
{
    function __construct() {
        add_action('init', array(&$this,
            'init'
        ));
    }

    public function init() {
        if (current_user_can('administrator') && !is_admin()) {
            $this->assets_dir = 'assets/';
            $this->assets_url = 'assets/';
            $this->load_hooks();
        }
    }

    public function load_hooks() {
        add_action('wp_footer', array(&$this,
            'launch_bar'
        ) , 999);
        add_action('wp_enqueue_scripts', array(&$this,
            'enqueue_assets'
        ) , 1000);
    }

    function enqueue_assets() {
        wp_enqueue_script('wpudebugtoolbar_scripts', plugins_url('assets/script.js', __FILE__) , array() , '0.6', 1);
        wp_register_style('wpudebugtoolbar_style', plugins_url('assets/style.css', __FILE__));
        wp_enqueue_style('wpudebugtoolbar_style');
    }

    /* ----------------------------------------------------------
      Bar
    ---------------------------------------------------------- */

    function launch_bar() {
        global $template, $pagenow;
        if ($pagenow == 'wp-login.php') {
            return;
        }
        echo '<div data-show-queries="" id="wputh-debug-toolbar" class="wputh-debug-toolbar">';

        if (SAVEQUERIES) {
            global $wpdb;
            echo '<div id="wputh-debug-queries">';
            echo "<pre>";
            print_r($wpdb->queries);
            echo "</pre>";
            echo '</div>';
        }

        echo '<div class="wputh-debug-toolbar-content">';

        // Theme
        echo 'Theme : <strong>' . wp_get_theme() . '</strong>';
        echo ' <em>&bull;</em> ';

        // Template
        echo 'File : <strong>' . basename($template) . '</strong>';
        echo ' <em>&bull;</em> ';

        // Current language
        echo 'Lang : <strong>' . get_bloginfo('language') . '</strong>';
        echo ' <em>&bull;</em> ';

        // Memory used
        echo 'Memory : <strong>' . round(memory_get_peak_usage() / (1024 * 1024) , 3) . '</strong> mb';
        echo ' <em>&bull;</em> ';

        // Queries
        echo 'Queries : <strong>' . get_num_queries() . '</strong>';
        echo ' <em>&bull;</em> ';

        // Execution time
        echo 'Time : <strong>' . timer_stop(0) . '</strong> sec';

        if (SAVEQUERIES) {
            echo ' <em>&bull;</em> ';
            echo '<span id="wputh-debug-display-queries">Display queries</span>';
            echo '<span id="wputh-debug-hide-queries">Hide queries</span>';
        }
        echo '</div>';

        echo '</div>';
    }
}

$WPUDebugToolbar = new WPUDebugToolbar();

