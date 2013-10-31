<?php
/*
Plugin Name: WPU Base Plugin
Plugin URI: http://github.com/Darklg/WPUtilities
Description: A framework for a WordPress plugin
Version: 0.2
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
        $this->set_options();
        $this->set_public_hooks();
        if ( is_admin() ) {
            $this->set_admin_hooks();
        }
    }

    /* ----------------------------------------------------------
      Options
    ---------------------------------------------------------- */

    function set_options() {
        $this->options = array(
            'id' => 'wpubaseplugin',
            'name' => 'Base Plugin',
            'level' => 'manage_options'
        );
    }

    /* ----------------------------------------------------------
      Hooks
    ---------------------------------------------------------- */

    function set_public_hooks() {

    }

    function set_admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'set_admin_menu' ) );
    }


    /* ----------------------------------------------------------
      Admin
    ---------------------------------------------------------- */

    function set_admin_menu() {
        add_menu_page(
            $this->options['name'],
            $this->options['name'],
            $this->options['level'],
            $this->options['id'],
            array( &$this, 'set_admin_page_main' )
        );
    }

    function set_admin_page_main() {
        echo $this->get_wrapper_start( $this->options['name'] );
        echo '<p>Content</p>';
        echo $this->get_wrapper_end();
    }

    /* ----------------------------------------------------------
      Activation / Desactivation
    ---------------------------------------------------------- */

    function activate() {
    }

    function deactivate() {
    }
}

$wpuBasePlugin = new wpuBasePlugin();

/* External activation hook */

register_activation_hook( __FILE__, array( &$wpuBasePlugin, 'activate' ) );
register_deactivation_hook( __FILE__, array( &$wpuBasePlugin, 'deactivate' ) );
