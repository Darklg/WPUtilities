<?php
/*
Plugin Name: WPU Options
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Options admin
Author: Darklg
Version: 1
Author URI: http://darklg.me
*/


class WPUOptions {

    private $options = array();

    function __construct() {
        if ( is_admin() ) {
            $this->set_options();
            $this->admin_hooks();
        }
    }

    private function set_options() {
        $this->options = array(
            'plugin_name' => 'WPU Options',
            'plugin_userlevel' => 'manage_options',
            'plugin_menutype' => 'index.php',
            'plugin_pageslug' => 'wpuoptions-settings',
            'plugin_dir' => str_replace( ABSPATH, ( site_url() . '/' ), dirname( __FILE__ ) ),
            'plugin_basename' => str_replace( ABSPATH . 'wp-content/plugins/', '', __FILE__ )
        );
    }

    private function admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
    }

    function admin_menu() {
        add_submenu_page(
            $this->options['plugin_menutype'], $this->options['plugin_name'] . ' Settings',
            $this->options['plugin_name'],
            $this->options['plugin_userlevel'],
            $this->options['plugin_pageslug'],
            array( &$this, 'admin_settings' )
        );
    }

    function admin_settings() {
        $fields = apply_filters( 'wpu_options_fields', array() );
        $content = '<div class="wrap">';
        $content .= '<h2>' . $this->options['plugin_name'] . '</h2>';
        if ( !empty( $fields ) ) {
            $content .= $this->admin_update( $fields );
            $content .= $this->admin_form( $fields );
        }
        else {
            $content .= '<p>' . __( 'No fields for the moment' ) . '</p>';
        }
        $content .= '</div>';
        echo $content;
    }

    private function admin_update( $fields = array() ) {
        $content = '';
        if ( !isset( $_POST['plugin_ok'] ) ) {
            return;
        }
        if ( !wp_verify_nonce( $_POST['wpuoptions-noncefield'], 'wpuoptions-nonceaction' ) ) {
            $content .= '<p>' . __( "Error in the form." ) . '</p>';
        }
        else {
            $updated_options = array();
            foreach ( $fields as $id => $field ) {
                $idf = $this->get_field_id( $id );
                if ( isset( $_POST[$idf] ) ) {
                    $field = $this->get_correct_field( $id, $field );
                    $old_option = get_option( $id );
                    $new_option = stripslashes( $_POST[$idf] );
                    if ( $old_option != $new_option ) {
                        update_option( $id, $new_option );
                        $updated_options[] = sprintf( __( 'The field "%s" has been updated.' ), $field['label'] );
                    }
                }
            }
            if ( !empty( $updated_options ) ) {
                $content .= '<p><strong>' . __( 'Success!' ) . '</strong><br />' . implode( '<br />', $updated_options ) . '</p>';
            }
        }
        return $content;
    }

    private function admin_form( $fields = array() ) {
        $content = '<form action="" method="post"><ul>';
        foreach ( $fields as $id => $field ) {
            $content .= $this->admin_field( $id, $field );
        }
        $content .= '<li><input class="button-primary" name="plugin_ok" value="' . __( 'Update' ) . '" type="submit" /></li></ul>';
        $content .= wp_nonce_field( 'wpuoptions-nonceaction', 'wpuoptions-noncefield', 1, 0 );
        $content .= '</form>';
        return $content;
    }

    private function admin_field( $id, $field = array() ) {
        $idf = $this->get_field_id( $id );


        $field = $this->get_correct_field( $id, $field );
        $content = '<li>';
        $content .= '<label for="' . $idf . '">' . $field['label'] . ' : </label><br />';
        switch ( $field['type'] ) {
        case 'textarea':
            $content .= '<textarea id="' . $idf . '" name="' . $idf . '" rows="5" cols="30">' . get_option( $id ) . '</textarea>';
            break;
        default :
            $content .= '<input type="text" id="' . $idf . '" name="' . $idf . '" value="' . get_option( $id ) . '" />';
        }
        $content .= '</li>';
        return $content;
    }

    private function get_correct_field( $id, $field ) {

        $default_values = array(
            'label' => $id,
            'type' => 'text'
        );
        foreach ( $default_values as $name => $value ) {
            if ( empty( $field[$name] ) || !isset( $field[$name] ) ) {
                $field[$name] = $value;
            }
        }

        return $field;
    }

    private function get_field_id( $id ) {
        return 'wpu_admin_id_' . $id;
    }
}

$WPUOptions = new WPUOptions();
