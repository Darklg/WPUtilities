<?php
/*
Plugin Name: WPU Base Plugin
Plugin URI: http://github.com/Darklg/WPUtilities
Description: A framework for a WordPress plugin
Version: 1.2
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
        global $wpdb;
        $this->options = array(
            'id' => 'wpubaseplugin',
            'level' => 'manage_options'
        );
        $this->data_table = $wpdb->prefix.$this->options['id']."_table";
        load_plugin_textdomain( $this->options['id'], false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
        // Allow translation for plugin name
        $this->options['name'] = __( 'Base Plugin', $this->options['id'] );
        $this->options['menu_name'] = __( 'Base', $this->options['id'] );
    }

    /* ----------------------------------------------------------
      Construct
    ---------------------------------------------------------- */

    function __construct() {
        $this->set_options();
        if ( $this->version < 1.0 ) {
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
        add_action( 'admin_bar_menu', array( &$this, 'set_adminbar_menu' ), 100 );
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
            $this->options['menu_name'],
            $this->options['level'],
            $this->options['id'],
            array( &$this, 'set_admin_page_main' )
        );
    }

    function set_adminbar_menu( $admin_bar ) {
        $admin_bar->add_menu( array(
                'id' => $this->options['id'],
                'title' => $this->options['menu_name'],
                'href' => admin_url( 'admin.php?page='.$this->options['id'] ),
                'meta' => array(
                    'title' => $this->options['menu_name'],
                ),
            ) );
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
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // Create or update table search
        dbDelta( "CREATE TABLE ".$this->data_table." (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `value` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`id`)
        );" );
    }

    function deactivate() {
    }

    function uninstall() {
        global $wpdb;
        $wpdb->query( 'DROP TABLE ' . $this->data_table );
    }
}

$wpuBasePlugin = new wpuBasePlugin();

/* External activation hook */

register_activation_hook( __FILE__, array( &$wpuBasePlugin, 'activate' ) );
register_deactivation_hook( __FILE__, array( &$wpuBasePlugin, 'deactivate' ) );
register_uninstall_hook( __FILE__, array( &$wpuBasePlugin, 'uninstall' ) );
