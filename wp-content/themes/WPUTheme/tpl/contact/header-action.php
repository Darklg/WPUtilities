<?php

$content_contact = '';
$fields = array(
    'contact_name' => array(
        'label' => __( 'Name', 'wputh' ),
        'required' => 1
    ),
    'contact_email' => array(
        'label' => __( 'Email', 'wputh' ),
        'type' => 'email',
        'required' => 1
    ),
    'contact_message' => array(
        'label' => __( 'Message', 'wputh' ),
        'type' => 'textarea',
        'required' => 1
    ),
);

function wpu_set_html_content_type() {
    return 'text/html';
}

// Testing missing values
foreach ( $fields as $id => $field ) {
    if ( !isset( $field['value'] ) ) {
        $fields[$id]['value'] = '';
    }
    if ( !isset( $field['type'] ) ) {
        $fields[$id]['type'] = '';
    }
    if ( !isset( $field['required'] ) ) {
        $fields[$id]['required'] = 0;
    }
}

// Checking before post
if ( !empty( $_POST ) ) {
    // Initial settings
    $msg_errors = array();
    $msg_success = '';

    // Checking for PHP Conf
    if ( isset( $_POST['control_stripslashes'] ) && $_POST['control_stripslashes'] == '\"' ) {
        foreach ( $_POST as $id => $field ) {
            $_POST[$id] = stripslashes( $field );
        }
    }

    foreach ( $fields as $id => $field ) {
        if ( isset( $_POST[$id] ) && !empty( $_POST[$id] ) ) {
            $tmp_value = htmlentities( strip_tags( $_POST[$id] ) );
            $field_ok = true;
            // Testing fields
            switch ( $field['type'] ) {
            case 'email':
                $field_ok = filter_var( $tmp_value, FILTER_VALIDATE_EMAIL ) !== false;
                break;
            default :
            }

            if ( !$field_ok ) {
                $msg_errors[] = sprintf( __( 'The field "%s" is not correct', 'wputh' ), $id );
            }
            else {
                $fields[$id]['value'] = $tmp_value;
            }
        }
        elseif ( $field['required'] ) {
            $msg_errors[] = sprintf( __( 'The field "%s" is required', 'wputh' ), $id );
        }
    }

    if ( empty( $msg_errors ) ) {

        // Setting success message
        $content_contact .= '<p>'.__( 'Thank you for your message!', 'wputh' ).'</p>';

        // Send mail
        $mail_content = '<p>'.__( 'Message from your contact form', 'wputh' ).'</p>';

        foreach ( $fields as $id => $field ) {
            // Emptying values
            $mail_content .= '<hr /><p><strong>'.$field['label'] . '</strong>:<br />'.$field['value'].'</p>';
            $fields[$id]['value'] = '';
        }

        add_filter( 'wp_mail_content_type', 'wpu_set_html_content_type' );
        wp_mail( get_option( 'admin_email' ), __( 'Message from your contact form', 'wputh' ), $mail_content );
        remove_filter( 'wp_mail_content_type', 'wpu_set_html_content_type' ); // reset content-type to to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578


    }
    else {
        $content_contact .= '<p><strong>'.__( 'Error:', 'wputh' ).'</strong><br />'.implode( '<br />', $msg_errors ).'</p>';
    }
}

// Showing contact form
$content_contact .= '<form action="" method="post"><ul class="cssc-form float-form">';
foreach ( $fields as $id => $field ) {
    $field_type = isset( $field['type'] ) ? $field['type']:'';
    $field_id_name = 'id="'.$id.'" name="'.$id.'"';
    $field_val = 'value="'.$field['value'].'"';
    $content_contact .= '<li class="box">';
    if ( isset( $field['label'] ) ) {
        $content_contact .= '<label for="'.$id.'">'.$field['label'].'</label>';
    }
    switch ( $field_type ) {
    case 'email':
        $content_contact .= '<input type="email" '.$field_id_name.' '.$field_val.' />';
        break;
    case 'textarea':
        $content_contact .= '<textarea cols="30" rows="5" '.$field_id_name.'>'.$field['value'].'</textarea>';
        break;
    default :
        $content_contact .= '<input type="text" '.$field_id_name.' '.$field_val.' />';
    }
    $content_contact .= '</li>';
}
$content_contact .= '<li>
<input type="hidden" name="control_stripslashes" value="&quot;" />
<button class="cssc-button" type="submit">'.__( 'Submit', 'wputh' ).'</button>
</li>';
$content_contact .= '</ul></form>';
