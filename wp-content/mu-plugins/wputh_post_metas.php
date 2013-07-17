<?php
/*
Plugin Name: WP Post Metas
Description: Simple admin for post metas
Version: 0.2
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
            foreach ( $box['type'] as $type ) {
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


// Shows meta box fields
function wputh_post_metas_box_content( $post, $details ) {

    $fields = apply_filters( 'wputh_post_metas_fields', array() );
    wp_nonce_field( plugin_basename( __FILE__ ), 'wputh_post_metas_noncename' );
    echo '<table>';
    foreach ( $fields as $id => $field ) {
        if ( isset( $field['box'] ) && 'wputh_box_'.$field['box'] == $details['id'] ) {
            if ( !isset( $field['name'] ) || empty( $field['name'] ) ) {
                $field['name'] = 'Field name';
            }
            if ( !isset( $field['type'] ) || empty( $field['type'] ) ) {
                $field['type'] = 'text';
            }
            $value = get_post_meta( $post->ID, $id, true );
            $idname = 'id="el_id_'.$id.'" name="'.$id.'"';
            echo '<tr>';
            echo '<td valign="top" style="width: 150px;"><label for="el_id_'.$id.'">'.$field['name'].' :</label></td>';
            echo '<td valign="top" style="width: 450px;">';
            switch ( $field['type'] ) {
            case 'email':
                echo '<input type="email" '.$idname.' value="'.esc_attr( $value ).'" />';
                break;
            case 'textarea':
                echo '<textarea rows="3" cols="50" '.$idname.'>'.esc_attr( $value ).'</textarea>';
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

    // First we need to check if the current user is authorised to do this action.
    if ( 'page' == $_POST['post_type'] ) {
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
        // If box corresponds to this post type
        if ( in_array( $_POST['post_type'], $box['type'] ) ) {
            $boxfields = wputh_post_metas_fields_from_box( $id, $fields );
            foreach ( $boxfields as $field_id => $field ) {
                $mydata = sanitize_text_field( $_POST[$field_id] );
                update_post_meta( $post_ID, $field_id, $mydata );
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
    if ( !is_array( $box ) ) {
        $box = array();
    }
    if ( !isset( $box['type'] ) || empty( $box['type'] ) || !is_array( $box['type'] ) ) {
        $box['type'] = array( 'post' );
    }
    if ( !isset( $box['name'] ) || empty( $box['name'] ) ) {
        $box['name'] = 'Box name';
    }
    return $box;
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
