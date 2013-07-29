<?php
/*
Plugin Name: WP Post Metas
Description: Simple admin for post metas
Version: 0.4.2
*/

/* Based on | http://codex.wordpress.org/Function_Reference/add_meta_box */

// Adds meta boxes
add_action( 'add_meta_boxes', 'wputh_post_metas_add_custom_box' );
function wputh_post_metas_add_custom_box() {
    $boxes = apply_filters( 'wputh_post_metas_boxes', array() );
    $fields = apply_filters( 'wputh_post_metas_fields', array() );

    foreach ( $boxes as $id => $box ) {
        $box = wputh_post_metas_control_box_datas( $box );
        $boxfields = wputh_post_metas_fields_from_box( $id, $fields );
        if ( !empty( $boxfields ) ) {
            foreach ( $box['post_type'] as $type ) {
                if ( current_user_can( $box['capability'] ) ) {
                    add_meta_box(
                        'wputh_box_'.$id,
                        $box['name'],
                        'wputh_post_metas_box_content',
                        $type
                    );
                }
            }
        }
    }
}


// Shows meta box fields
function wputh_post_metas_box_content( $post, $details ) {

    $fields = apply_filters( 'wputh_post_metas_fields', array() );
    $fields = wputh_post_metas_control_fields_datas( $fields );
    wp_nonce_field( plugin_basename( __FILE__ ), 'wputh_post_metas_noncename' );
    echo '<table>';
    foreach ( $fields as $id => $field ) {
        if ( 'wputh_box_'.$field['box'] == $details['id'] ) {

            $value = get_post_meta( $post->ID, $id, true );
            $idname = 'id="el_id_'.$id.'" name="'.$id.'"';
            echo '<tr>';
            echo '<td valign="top" style="width: 150px;"><label for="el_id_'.$id.'">'.$field['name'].' :</label></td>';
            echo '<td valign="top" style="width: 450px;">';
            switch ( $field['type'] ) {
            case 'email':
                echo '<input type="email" '.$idname.' value="'.esc_attr( $value ).'" />';
                break;
            case 'select':
                echo '<select '.$idname.'>';
                echo '<option value="" disabled selected style="display:none;">'.__( 'Select a value', 'wputh' ).'</option>';
                foreach ( $field['datas'] as $key => $var ) {
                    echo '<option value="'.$key.'" '.( (string) $key === (string) $value ? 'selected="selected"':'' ).'>'.$var.'</option>';
                }
                echo '</select>';
                break;
            case 'textarea':
                echo '<textarea rows="3" cols="50" '.$idname.'>'. $value .'</textarea>';
                break;
            case 'editor':
                wp_editor( $value, $id );
                break;
            case 'url':
                echo '<input type="url" '.$idname.' value="'.esc_attr( $value ).'" />';
                break;
            default :
                echo '<input type="text" '.$idname.' value="'.esc_attr( $value ).'" />';
            }
            echo '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
}


// Saves meta box content
add_action( 'save_post', 'wputh_post_metas_save_postdata' );

function wputh_post_metas_save_postdata( $post_id ) {

    $boxes = apply_filters( 'wputh_post_metas_boxes', array() );
    $fields = apply_filters( 'wputh_post_metas_fields', array() );
    $fields = wputh_post_metas_control_fields_datas( $fields );
    $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : 'post';

    // First we need to check if the current user is authorised to do this action.
    if ( 'page' == $post_type ) {
        if ( ! current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) )
            return;
    }

    // Secondly we need to check if the user intended to change this value.
    if ( ! isset( $_POST['wputh_post_metas_noncename'] ) || ! wp_verify_nonce( $_POST['wputh_post_metas_noncename'], plugin_basename( __FILE__ ) ) )
        return;

    $post_ID = $_POST['post_ID'];

    foreach ( $boxes as $id => $box ) {
        $box = wputh_post_metas_control_box_datas( $box );
        // If box corresponds to this post type & current user has level to edit
        if ( in_array( $post_type, $box['post_type'] ) && current_user_can( $box['capability'] ) ) {
            $boxfields = wputh_post_metas_fields_from_box( $id, $fields );
            foreach ( $boxfields as $field_id => $field ) {
                $field_value = wputh_check_field_value( $field_id, $field );
                if ( $field_value !== false ) {
                    update_post_meta( $post_ID, $field_id, $field_value );
                }
            }
        }
    }
}


/* ----------------------------------------------------------
  Utilities
---------------------------------------------------------- */

/* Control box datas
-------------------------- */

function wputh_post_metas_control_box_datas( $box ) {
    $default_box = array(
        'name' => 'Box',
        'capability' => 'delete_others_posts', // Default level : editor
        'post_type' => array( 'post' )
    );
    $new_box = array();
    if ( !is_array( $box ) ) {
        $box = array();
    }
    $new_box = array_merge( $default_box, $box );
    if ( !is_array( $new_box['post_type'] ) ) {
        $new_box['post_type'] = $default_box['post_type'];
    }

    return $new_box;
}

/* Control field datas
-------------------------- */

function wputh_post_metas_control_fields_datas( $fields ) {
    $default_field = array(
        'box' => '',
        'name' => 'Field Name',
        'type' => 'text',
        'datas' => array()
    );

    $new_fields = array();

    foreach ( $fields as $id => $field ) {
        $new_fields[$id] = array_merge( $default_field, $field );
        // if incomplete "select" : defaults to txt
        if ( $new_fields[$id]['type'] == 'select' && empty( $new_fields[$id]['datas'] ) ) {
            $new_fields[$id]['type'] = 'text';
        }
    }

    return $new_fields;
}


/* Returns fields for a given box
-------------------------- */

function wputh_post_metas_fields_from_box( $box_id, $fields ) {
    $boxfields = array();
    foreach ( $fields as $id => $field ) {
        if ( isset( $field['box'] ) && $field['box'] == $box_id ) {
            $boxfields[$id] = $field;
        }
    }
    return $boxfields;
}

/* Control fields value
-------------------------- */

function wputh_check_field_value( $id, $field ) {

    if ( !isset( $_POST[$id] ) ) {
        return false;
    }

    $return = false;
    $value = $_POST[$id];
    switch ( $field['type'] ) {
    case 'email':
        $return = ( filter_var( $value, FILTER_VALIDATE_EMAIL ) === false ) ? false : $value;
        break;
    case 'select':
        $return = array_key_exists( $value, $field['datas'] ) ? $value : false;
        break;
    case 'textarea':
        $return = strip_tags( $value );
        break;
    case 'editor':
        $return = trim( $value );
        break;
    case 'url':
        $return = ( filter_var( $value, FILTER_VALIDATE_URL ) === false ) ? false : $value;
        break;
    default :
        $return = sanitize_text_field( $value );
    }

    return $return;
}
