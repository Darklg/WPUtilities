<?php
/*
Plugin Name: WPU Base Plugin
Plugin URI: http://github.com/Darklg/WPUtilities
Description: A framework for a WordPress plugin
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if ( !class_exists( 'wpuBasePluginUtilities' ) ) {
    include dirname( __FILE__ ).'/inc/wpubasepluginutilities.php';
}

class wpuBasePlugin extends wpuBasePluginUtilities {
    function __construct() {
        $this->set_public_hooks();
        if ( is_admin() ) {
            $this->set_admin_hooks();
        }
    }

    function set_public_hooks() {

    }

    function set_admin_hooks() {

    }
}

$wpuBasePlugin = new wpuBasePlugin();
