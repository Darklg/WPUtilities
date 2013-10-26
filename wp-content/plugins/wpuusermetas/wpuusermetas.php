<?php
/*
Plugin Name: WPU User Metas
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Simple admin for user metas
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Based On: http://blog.ftwr.co.uk/archives/2009/07/19/adding-extra-user-meta-fields/
*/



class WPUUserMetas {
    private $sections = array();
    private $fields = array();

    function __construct() {
        // Admin init
        if ( is_admin() ) {
            $this->admin_hooks();
        }
    }

    function admin_hooks() {
        add_action( 'show_user_profile', array( &$this, 'display_form' ) );
        add_action( 'edit_user_profile', array( &$this, 'display_form' ) );
        add_action( 'personal_options_update', array( &$this, 'update_user_meta' ) );
        add_action( 'edit_user_profile_update', array( &$this, 'update_user_meta' ) );
    }

    /* Datas */

    function get_datas() {
        $fields = apply_filters( 'wpu_usermetas_fields', array() );
        $this->fields = array();
        foreach ( $fields as $id_field => $field ) {
            if ( !isset( $field['name'] ) || empty( $field['name'] ) ) {
                $field['name'] = $id_field;
            }
            if ( !isset( $field['type'] )|| empty( $field['type'] ) ) {
                $field['type'] = 'text';
            }
            $this->fields[$id_field] = $field;
        }
        $this->sections = apply_filters( 'wpu_usermetas_sections', array() );
    }

    function get_section_fields( $section_id ) {
        $fields = array();
        foreach ( $this->fields as $id_field => $field ) {
            if ( isset( $field['section'] ) && $field['section'] == $section_id ) {
                $fields[$id_field] = $field;
            }
        }
        return $fields;
    }

    /* Update */

    function update_user_meta( $user_id ) {
        $this->get_datas();
        foreach ( $this->fields as $id_field => $field ) {
            $new_value = '';
            if ( isset( $_POST[$id_field] ) ) {
                $new_value = esc_attr( $_POST[$id_field] );
                update_usermeta( $user_id, $id_field, $new_value );
            }
        }
    }

    /* Display */

    function display_form( $user ) {
        $this->get_datas();
        foreach ( $this->sections as $id => $section ) {
            echo $this->display_section( $user, $id, $section['name'] );
        }
    }

    function display_section( $user, $id, $name ) {
        $content = '';
        $fields = $this->get_section_fields( $id );
        if ( !empty( $fields ) ) {
            $content .= '<h3>' . $name . '</h3>';
            $content .= '<table class="form-table">';
            foreach ( $this->fields as $id_field => $field ) {
                $content .= $this->display_field( $user, $id_field, $field['name'], $field['type'] );
            }
            $content .= '</table>';
        }
        return $content;
    }

    function display_field( $user, $id_field, $label, $type = 'text' ) {
        $content = '';
        $content .= '<tr>';
        $content .= '<th><label for="'.$id_field.'">'.$label.'</label></th>';
        $content .= '<td><input type="text" name="'.$id_field.'" id="'.$id_field.'" placeholder="'.$label.'" value="'. esc_attr( get_the_author_meta( $id_field, $user->ID ) ) .'" /></td>';
        $content .= '</tr>';
        return $content;
    }

}

new WPUUserMetas();
