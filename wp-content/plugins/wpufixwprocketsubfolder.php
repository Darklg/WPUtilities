<?php

/*
Plugin Name: WPU Fix WP Rocket Subfolder
Description: Ensure WP Rocket Minify works good when WordPress is installed in a separate subfolder.
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUFixWPRocketSubFolder {

    private $wp_install_path = '/wp-cms';

    public function __construct() {
        add_action('init', array(&$this, 'init'));
    }

    public function init() {
        $this->wp_install_path = apply_filters('wpufixwprocketsubfolder_installpath', $this->wp_install_path);
        add_filter('rocket_override_min_documentRoot', '__return_true', 10, 1);
        add_filter('rocket_min_documentRoot', array(&$this, 'ignore_wp_install_folder'), 10, 1);
        add_filter('rocket_pre_minify_path', array(&$this, 'add_wp_install_folder'), 10, 1);

    }

    /**
     * Ensure WP native files are loaded from the correct path
     * @param string $filePath path of the file to be included
     * @return string          Updated file path
     */
    public function add_wp_install_folder($filePath) {
        if (substr($filePath, 0, 6) == '/wp-in' || substr($filePath, 0, 6) == '/wp-ad') {
            $filePath = $this->wp_install_path . $filePath;
        }
        return $filePath;
    }

    /**
     * Use the parent path as relative path
     * @param  string $content Base Document Root
     * @return string          Updated Document Root
     */
    public function ignore_wp_install_folder($documentRoot) {
        $documentRoot = str_replace($this->wp_install_path, '', $documentRoot);
        return $documentRoot;
    }

}

$WPUFixWPRocketSubFolder = new WPUFixWPRocketSubFolder();
