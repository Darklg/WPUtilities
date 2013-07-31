<?php
/*
Plugin Name: WPU Options
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Options admin
Author: Darklg
Version: 2.0
Author URI: http://darklg.me
*/


class WPUOptions {

    private $options = array();

    private $default_box = array(
        'default' => array(
            'name' => ''
        )
    );

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
        $boxes = apply_filters( 'wpu_options_boxes', $this->default_box );
        $content = '<div class="wrap">';
        $content .= '<h2>' . $this->options['plugin_name'] . '</h2>';
        if ( !empty( $fields ) ) {
            $content .= $this->admin_update( $fields );
            $content .= $this->admin_form( $fields, $boxes );
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
            $languages = $this->get_languages();
            $updated_options = array();
            $errors = array();
            $testfields = array();
            foreach ($fields as $id=> $field) {
                $testfields[$id] = $field;
                if (isset($field['lang']) && !empty($languages)) {
                    foreach ($languages as $lang => $name) {
                        $newfield = $field;
                        $newfield['label'] = '['.$lang.'] '.$newfield['label'];
                        $testfields[$lang.'___'.$id] = $newfield;
                    }
                }
            }

            foreach ( $testfields as $id => $field ) {
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
                        $errors[] = sprintf( __( 'The field "%s" has not been updated, because it\'s not valid.', 'wpuoptions' ), $field['label'] );
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

    private function admin_form( $fields = array(), $boxes = array() ) {
        $base_content = '<form action="" method="post" class="wpu-options-form">';
        $content = $base_content;
        foreach ($boxes as $idbox => $box) {
            $content_tmp = '';
            foreach ( $fields as $id => $field ) {
                if ((isset($field['box']) && $field['box'] == $idbox) || ($idbox == 'default' && !isset($field['box']))) {
                    $content_tmp .= $this->admin_field( $id, $field );
                }
            }
            if (!empty($content_tmp)) {
                // Adding box name if available
                if (!empty($box['name'])) {
                    $content .= '<h3>'.$box['name'].'</h3>';
                }
                $content .= '<table style="width: 500px;">'.$content_tmp.'</table>';
            }
        }
        $content .= '<ul><li><input class="button-primary" name="plugin_ok" value="' . __( 'Update', 'wpuoptions' ) . '" type="submit" /></li></ul>';
        $content .= wp_nonce_field( 'wpuoptions-nonceaction', 'wpuoptions-noncefield', 1, 0 );
        $content .= '</form>';
        return $content;
    }

    private function admin_field( $id, $field = array() ) {
        $languages = $this->get_languages();
        $fields_versions = array();

        if(empty($languages) || !isset($field['lang'])){
            $fields_versions[] = array(
                'id' => $id,
                'field' => $field,
                'prefix_label' => '',
                'prefix_opt' => '',
            );
        }
        else {
            foreach($languages as $idlang => $lang){
                $fields_versions[] = array(
                    'id' => $id,
                    'field' => $field,
                    'prefix_label' => '['.$idlang.'] ',
                    'prefix_opt' => $idlang . '___',
                );
            }
        }
        $content = '';
        foreach($fields_versions as $field_version){
            $idf = $this->get_field_id( $field_version['prefix_opt'] . $field_version['id'] );
            $field = $this->get_field_datas( $field_version['id'], $field_version['field'] );
            $idname = ' id="' . $idf . '" name="' . $idf . '" ';
            $value = get_option( $field_version['prefix_opt'] . $field_version['id'] );

            $content .= '<tr class="wpu-options-box">';
            $content .= '<td style="width: 150px;"><label for="' . $field_version['prefix_opt'] . $idf . '">' . $field_version['prefix_label'] . $field['label'] . ' : </label></td>';
            $content .= '<td>';
            switch ( $field['type'] ) {
            case 'editor':
                ob_start();
                wp_editor( $value, $idf );
                $content .= ob_get_clean();
                break;
            case 'email':
                $content .= '<input type="email" ' . $idname . ' value="' . $value . '" />';
                break;
            case 'page':
                $content .= wp_dropdown_pages( array(
                        'name' => $idf,
                        'selected' => $value,
                        'echo' => 0,
                    ) );
                break;
            case 'textarea':
                $content .= '<textarea ' . $idname . ' rows="5" cols="30">' . $value . '</textarea>';
                break;
            case 'url':
                $content .= '<input type="url" ' . $idname . ' value="' . $value . '" />';
                break;
            default :
                $content .= '<input type="text" ' . $idname . ' value="' . $value . '" />';
            }
            $content .= '</td>';
            $content .= '</tr>';
        }
        return $content;
    }

    /* Getting all datas for a field, with default values for undefined params  */
    private function get_field_datas( $id, $field ) {

        $default_values = array(
            'box' => 'default',
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
        case 'page':
            if ( !ctype_digit( $value ) ) {
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


    private function get_languages() {
        global $q_config;
        $languages = array();
        // Obtaining from Qtranslate
        if (isset($q_config['enabled_languages'])) {
            foreach ($q_config['enabled_languages'] as $lang) {
                if (!in_array($lang, $languages) && isset($q_config['language_name'][$lang])) {
                    $languages[$lang] = $q_config['language_name'][$lang];
                }
            }
        }
        return $languages;
    }

}

$WPUOptions = new WPUOptions();

function wputh_l18n_get_option($name){
    global $q_config;

    if(isset($q_config['language'])){
        $name = $q_config['language'] . '___' . $name;
    }

    return get_option($name);
}