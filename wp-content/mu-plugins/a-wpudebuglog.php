<?php

/*
Plugin Name: WPU Error Log
Description: Set a custom path for error log
Version: 0.2.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUErrorLog {
    public function __construct() {
        if (!defined('WPUERRORLOGPATH')) {
            $upload_dir = wp_upload_dir();
            $log_path = apply_filters('beauxartsgli_log_path', $upload_dir['basedir']) . '/debug/';
        } else {
            $log_path = WPUERRORLOGPATH;
        }
        if (!is_dir($log_path)) {
            mkdir($log_path);
        }
        $log_path = $log_path.'/' . date('Y-m');
        if (!is_dir($log_path)) {
            mkdir($log_path);
        }
        $log_file = $log_path . '/debug-' . date('Y-m-d') . '.log';
        if (!file_exists($log_file)) {
            touch($log_file);
        }
        ini_set('log_errors', 1);
        ini_set('error_log', $log_file);
    }
}

try {
    $WPUErrorLog = new WPUErrorLog();
} catch (Exception $e) {}
