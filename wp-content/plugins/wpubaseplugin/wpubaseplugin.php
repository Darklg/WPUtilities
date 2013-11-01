<?php
/*
Plugin Name: WPU Base Plugin
Plugin URI: http://github.com/Darklg/WPUtilities
Description: A framework for a WordPress plugin
Version: 1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

// Load Base Plugin Utilities
if ( !class_exists( 'wpuBasePluginUtilities' ) ) {
    include dirname( __FILE__ ).'/inc/wpubasepluginutilities.php';
}

class wpuBasePlugin extends wpuBasePluginUtilities {

    /* ----------------------------------------------------------
      Options
    ---------------------------------------------------------- */

    function set_options() {
        $this->options = array(
            'id' => 'wpubaseplugin',
            'name' => 'Base Plugin',
            'level' => 'manage_options'
        );
        load_plugin_textdomain( $this->options['id'], false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }

    /* ----------------------------------------------------------
      Construct
    ---------------------------------------------------------- */

    function __construct() {
        $this->set_options();
        if ( $this->version < 2.0 ) {
            // Detect outdated version of utilities
            $error_message = htmlentities( __( 'Error: Your version of %s is outdated!', $this->options['id'] ) );
            exit(  sprintf( $error_message , '<strong>Base Plugin Utilities</strong>' ) );
        }
        $this->set_public_hooks();
        if ( is_admin() ) {
            $this->set_admin_hooks();
        }
    }

    /* ----------------------------------------------------------
      Hooks
    ---------------------------------------------------------- */

    function set_public_hooks() {

    }

    function set_admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'set_admin_menu' ) );
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->options['id'] ) {
            add_action( 'admin_print_styles', array( &$this, 'load_assets_css' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'load_assets_js' ) );
        }
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
        echo '<p>'.__( 'Content', $this->options['id'] ).'</p>';
        echo $this->get_wrapper_end();
    }

    /* ----------------------------------------------------------
      Assets
    ---------------------------------------------------------- */

    function load_assets_js() {
        wp_enqueue_script(  $this->options['id'] . '_scripts', plugin_dir_url( __FILE__ ) . '/assets/js/script.js' );
    }

    function load_assets_css() {
        wp_register_style( $this->options['id'] . '_style', plugins_url( 'assets/css/style.css', __FILE__ ) );
        wp_enqueue_style( $this->options['id'] . '_style' );
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
