<?php
/*
Plugin Name: WPU TinyMCE Buttons
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Add new buttons to TinyMCE
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/


class WPUTinyMCE {
    function __construct() {
        $this->set_options();
        add_action( 'init', array( &$this, 'set_buttons' ) );
    }

    function set_options() {
        $this->options = array(
            'plugin-id' => 'wpu_tinymce',
            'buttons' => array(
                'insert_table'
            )
        );
    }

    function set_buttons() {
        if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
            add_filter( "mce_external_plugins", array( &$this, 'add_plugins' ) );
            add_filter( 'mce_buttons', array( &$this, 'add_buttons' ) );
        }
    }

    function add_plugins( $plugins = array() ) {
        $plugins[$this->options['plugin-id']] = plugins_url( '/assets/tinymce.js', __FILE__ );
        return $plugins;
    }

    function add_buttons( $buttons = array() ) {
        foreach($this->options['buttons'] as $button){
            $buttons[] = $button;
        }
        return $buttons;
    }

}

new WPUTinyMCE();

