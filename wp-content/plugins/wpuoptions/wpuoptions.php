<?php
/*
Plugin Name: WPU Options
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Friendly interface for website options
Author: Darklg
Version: 2.1.4
Author URI: http://darklg.me
*/

class WPUOptions {

    private $options = array();

    private $default_box = array(
        'default' => array(
            'name' => ''
        )
    );


    /**
     * Init plugin
     */
    function __construct() {
        if ( is_admin() ) {
            load_plugin_textdomain( 'wpuoptions', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
            $this->set_options();
            $this->admin_hooks();
        }
    }


    /**
     * Set Options
     */
    private function set_options() {
        $this->options = array(
            'plugin_name' => 'WPU Options',
            'plugin_userlevel' => 'manage_categories',
            'plugin_menutype' => 'index.php',
            'plugin_pageslug' => 'wpuoptions-settings',
            'plugin_dir' => str_replace( ABSPATH, ( site_url() . '/' ), dirname( __FILE__ ) ),
            'plugin_basename' => str_replace( ABSPATH . 'wp-content/plugins/', '', __FILE__ )
        );
    }


    /**
     * Set admin hooks
     */
    private function admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action('admin_bar_menu', array( &$this, 'add_toolbar_menu_items' ), 100);

    }


    /**
     * Set admin menu
     */
    function admin_menu() {
        add_submenu_page(
            $this->options['plugin_menutype'], $this->options['plugin_name'] . ' Settings',
            $this->options['plugin_name'],
            $this->options['plugin_userlevel'],
            $this->options['plugin_pageslug'],
            array( &$this, 'admin_settings' )
        );
    }


    /**
     * Add menu items to toolbar
     *
     * @param unknown $admin_bar
     */
    function add_toolbar_menu_items($admin_bar) {
        $admin_bar->add_menu( array(
                'id' => 'wpu-options-menubar-link',
                'title' => $this->options['plugin_name'] ,
                'href' => admin_url( $this->options['plugin_menutype'] . '?page='.$this->options['plugin_pageslug'] ),
                'meta' => array(
                    'title' => $this->options['plugin_name'],
                ),
            ));
    }


    /**
     * Set admin page
     */
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


    /**
     * Save new values
     *
     * @param unknown $fields (optional)
     * @return unknown
     */
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


    /**
     * Returns admin form
     *
     * @param unknown $fields (optional)
     * @param unknown $boxes  (optional)
     * @return unknown
     */
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
                $content .= '<table style="table-layout:fixed;width: 650px;">'.$content_tmp.'</table>';
            }
        }
        $content .= '<ul><li><input class="button-primary" name="plugin_ok" value="' . __( 'Update', 'wpuoptions' ) . '" type="submit" /></li></ul>';
        $content .= wp_nonce_field( 'wpuoptions-nonceaction', 'wpuoptions-noncefield', 1, 0 );
        $content .= '</form>';
        return $content;
    }


    /**
     * Return an admin field
     *
     * @param unknown $id
     * @param unknown $field (optional)
     * @return unknown
     */
    private function admin_field( $id, $field = array() ) {
        $languages = $this->get_languages();
        $fields_versions = array();

        if (empty($languages) || !isset($field['lang'])) {
            $fields_versions[] = array(
                'id' => $id,
                'field' => $field,
                'prefix_label' => '',
                'prefix_opt' => '',
            );
        }
        else {
            foreach ($languages as $idlang => $lang) {
                $fields_versions[] = array(
                    'id' => $id,
                    'field' => $field,
                    'prefix_label' => '['.$idlang.'] ',
                    'prefix_opt' => $idlang . '___',
                );
            }
        }
        $content = '';
        foreach ($fields_versions as $field_version) {
            $idf = $this->get_field_id( $field_version['prefix_opt'] . $field_version['id'] );
            $field = $this->get_field_datas( $field_version['id'], $field_version['field'] );
            $idname = ' id="' . $idf . '" name="' . $idf . '" ';
            $originalvalue = get_option( $field_version['prefix_opt'] . $field_version['id'] );
            $value = htmlentities($originalvalue);

            $content .= '<tr class="wpu-options-box">';
            $content .= '<td style="vertical-align:top;width: 150px;"><label for="' . $field_version['prefix_opt'] . $idf . '">' . $field_version['prefix_label'] . $field['label'] . ' : </label></td>';
            $content .= '<td>';
            switch ( $field['type'] ) {
            case 'editor':
                ob_start();
                wp_editor( $originalvalue, $idf );
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
            case 'select':
                $content .= '<select ' . $idname . '"><option value="" disabled selected style="display:none;">'.__('Select a value', 'wputh').'</option>';
                foreach ($field['datas'] as $key => $var) {
                    $content .= '<option value="'.htmlentities($key).'" '.($key == $value ? 'selected="selected"' : '').'>'.htmlentities($var).'</option>';
                }
                $content .= '</select>';
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


    /**
     * Getting all datas for a field, with default values for undefined params
     *
     * @param int     $id
     * @param unknown $field
     * @return unknown
     */
    private function get_field_datas( $id, $field ) {

        $default_values = array(
            'box' => 'default',
            'label' => $id,
            'type' => 'text',
            'test' => '',
            'datas' => array(__('No', 'wputh'), __('Yes', 'wputh'))
        );
        foreach ( $default_values as $name => $value ) {
            if ( empty( $field[$name] ) || !isset( $field[$name] ) ) {
                $field[$name] = $value;
            }
        }

        return $field;
    }


    /**
     * Validate a field value
     *
     * @param string  $field
     * @param unknown $value
     * @return boolean
     */
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
        case 'select' :
            if ( !array_key_exists($value, $field['datas'])) {
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


    /**
     * Optain an admin field id
     *
     * @param string  $id
     * @return string
     */
    private function get_field_id( $id ) {
        return 'wpu_admin_id_' . $id;
    }


    /**
     * Obtain a list of languages
     *
     * @return array
     */
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


/**
 * Get an option value with l18n
 *
 * @param string  $name
 * @return string
 */
function wputh_l18n_get_option($name) {
    global $q_config;

    if (isset($q_config['language'])) {
        $name = $q_config['language'] . '___' . $name;
    }

    return get_option($name);
}
