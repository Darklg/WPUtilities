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
            load_plugin_textdomain( 'wpuoptions', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
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
            $content .= '<p>' . __( 'No fields for the moment', 'wpuoptions' ) . '</p>';
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
            $content .= '<p>' . __( "Error in the form.", 'wpuoptions' ) . '</p>';
        }
        else {
            $updated_options = array();
            $errors = array();
            foreach ( $fields as $id => $field ) {
                $idf = $this->get_field_id( $id );
                if ( isset( $_POST[$idf] ) ) {
                    $field = $this->get_field_datas( $id, $field );
                    $old_option = get_option( $id );
                    $new_option = trim( stripslashes( $_POST[$idf] ) );

                    $test_field = $this->test_field_value( $field, $new_option );

                    // Field is required and have been emptied
                    if ( $new_option == '' && isset( $field['required'] ) ) {
                        $errors[] = sprintf( __( 'The field "%s" must not be empty', 'wpuoptions' ), $field['label'] );
                    }
                    // If test is ok OR the field is not required
                    elseif ( $test_field || ( $new_option == '' && !isset( $field['required'] ) ) ) {
                        if ( $old_option != $new_option ) {
                            update_option( $id, $new_option );
                            $updated_options[] = sprintf( __( 'The field "%s" has been updated.', 'wpuoptions' ), $field['label'] );
                        }
                    } else {
                        $errors[] = sprintf( __( 'The field "%s" has not been updated, because it’s not valid.', 'wpuoptions' ), $field['label'] );
                    }
                }
            }
            if ( !empty( $updated_options ) ) {
                $content .= '<p><strong>' . __( 'Success!', 'wpuoptions' ) . '</strong><br />' . implode( '<br />', $updated_options ) . '</p>';
            }
            if ( !empty( $errors ) ) {
                $content .= '<p><strong>' . __( 'Fail!', 'wpuoptions' ) . '</strong><br />' . implode( '<br />', $errors ) . '</p>';
            }
        }
        return $content;
    }

    private function admin_form( $fields = array() ) {
        $content = '<form action="" method="post"><ul>';
        foreach ( $fields as $id => $field ) {
            $content .= $this->admin_field( $id, $field );
        }
        $content .= '<li><input class="button-primary" name="plugin_ok" value="' . __( 'Update', 'wpuoptions' ) . '" type="submit" /></li></ul>';
        $content .= wp_nonce_field( 'wpuoptions-nonceaction', 'wpuoptions-noncefield', 1, 0 );
        $content .= '</form>';
        return $content;
    }

    private function admin_field( $id, $field = array() ) {
        $idf = $this->get_field_id( $id );
        $field = $this->get_field_datas( $id, $field );
        $content = '<li>';
        $content .= '<label for="' . $idf . '">' . $field['label'] . ' : </label><br />';
        $idname = ' id="' . $idf . '" name="' . $idf . '" ';
        switch ( $field['type'] ) {
        case 'textarea':
            $content .= '<textarea ' . $idname . ' rows="5" cols="30">' . get_option( $id ) . '</textarea>';
            break;
        case 'email':
            $content .= '<input type="email" ' . $idname . ' value="' . get_option( $id ) . '" />';
            break;
        case 'url':
            $content .= '<input type="url" ' . $idname . ' value="' . get_option( $id ) . '" />';
            break;
        default :
            $content .= '<input type="text" ' . $idname . ' value="' . get_option( $id ) . '" />';
        }
        $content .= '</li>';
        return $content;
    }

    /* Getting all datas for a field, with default values for undefined params  */
    private function get_field_datas( $id, $field ) {

        $default_values = array(
            'label' => $id,
            'type' => 'text',
            'test' => ''
        );
        foreach ( $default_values as $name => $value ) {
            if ( empty( $field[$name] ) || !isset( $field[$name] ) ) {
                $field[$name] = $value;
            }
        }

        return $field;
    }

    private function test_field_value( $field, $value ) {
        $return = true;
        switch ( $field['test'] ) {
        case 'email':
            if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) === false ) {
                $return = false;
            }
            break;
        case 'url':
            if ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) {
                $return = false;
            }
            break;
        default:
        }

        return $return;
    }

    private function get_field_id( $id ) {
        return 'wpu_admin_id_' . $id;
    }
}

$WPUOptions = new WPUOptions();
