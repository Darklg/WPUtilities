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
                $msg_errors[$id] = '<span id="error_'. $id .'">'. sprintf( __( 'The field "%s" is not correct', 'wputh' ), $fields[$id]['label'] ) .'</span>';
            }
            else {
                $fields[$id]['value'] = $tmp_value;
            }
        }
        elseif ( $field['required'] ) {
            $msg_errors[$id] = '<span id="error_'. $id .'">'. sprintf( __( 'The field "%s" is required', 'wputh' ), $fields[$id]['label'] ) .'</span>';
        }
    }

    if ( empty( $msg_errors ) ) {

        add_filter( 'wp_title', 'contact_wp_title_error', 10, 1 );
        add_filter( 'wpseo_title', 'contact_wp_title_error', 10, 1 );
        function contact_wp_title_error( $old_title ){
            $output = __('Your message was successfully sent.', 'wputh');
            $output .= ' - '. get_bloginfo( 'name', 'display' );
            return $output;
        }

        // Setting success message
        $content_contact .= '<p>'.__( 'Your message was successfully sent.<br />Thank you for your message!', 'wputh' ).'</p>';

        // Send mail
        $mail_content = '<p>'.__( 'Message from your contact form', 'wputh' ).'</p>';

        foreach ( $fields as $id => $field ) {
            // Emptying values
            $mail_content .= '<hr /><p><strong>'.$field['label'] . '</strong>:<br />'.$field['value'].'</p>';
            $fields[$id]['value'] = '';
        }

        wputh_sendmail( get_option( 'admin_email' ), __( 'Message from your contact form', 'wputh' ), $mail_content );

    }
    else {
        add_filter( 'wp_title', 'contact_wp_title_error', 10, 1 );
        add_filter( 'wpseo_title', 'contact_wp_title_error', 10, 1 );
        function contact_wp_title_error( $old_title){
        global $msg_errors;
            $errors_count = count($msg_errors); // retrieve all error messages
            if($errors_count > 1) {
                $output = sprintf( _n( '1 error found, the form could not be submitted', '%s errors found, the form could not be submitted', $errors_count, 'wputh' ), $errors_count );
            } else {
                $output = strip_tags(array_shift($msg_errors));
            }
            $output .= ' - '. get_bloginfo( 'name', 'display' );
            return $output;
        }

        $content_contact .= '<div class="message message--error"><p class="bold">'. sprintf( _n( 'I found 1 error', 'I found %s errors', count($msg_errors), 'wputh' ), count($msg_errors) ) .'</p><ol class="message__list"><li class="message__item">'.implode( '</li><li class="message__item">', $msg_errors ).'</li></ol></div>';
    }
}

// Showing contact form
$content_contact .= '<form action="" method="post">';
$content_contact .= '<div class="cssc-form float-form">';
foreach ( $fields as $id => $field ) {
    $field_type = isset( $field['type'] ) ? $field['type']:'';
    $field_id_name = 'id="'.$id.'" name="'.$id.'"';
    $field_val = 'value="'.$field['value'].'"';
    $content_contact .= '<p class="box">';
    if ( isset( $field['label'] ) ) {
        $aria_error = (isset($msg_errors) && is_array($msg_errors) && array_key_exists($id, $msg_errors)) ? ' aria-describedby="error_'.$id.'"' : '';
        $content_contact .= '<label for="'.$id.'"'.$aria_error.'>'.$field['label'].'</label>';
    }
    $field_required = ''; // ($field['required'] === 1) ? ' aria-required="true" required="required"' : '';
    $field_class_error = (isset($msg_errors) && is_array($msg_errors) && array_key_exists($id, $msg_errors)) ? ' class="field--error"' : '';

    switch ( $field_type ) {
    case 'email':
        $content_contact .= '<input type="email" '.$field_id_name.' '.$field_val . $field_required . $field_class_error .' />';
        break;
    case 'textarea':
        $content_contact .= '<textarea cols="30" rows="5" '.$field_id_name . $field_required . $field_class_error .'>'.$field['value'].'</textarea>';
        break;
    default :
        $content_contact .= '<input type="text" '.$field_id_name.' '.$field_val . $field_required . $field_class_error .' />';
    }
    $content_contact .= '</p>';
}
$content_contact .= '<p>
<input type="hidden" name="control_stripslashes" value="&quot;" />
<button class="cssc-button" type="submit">'. __( 'Submit your message', 'wputh' ) .'</button>
</p>';
$content_contact .= '</div>';
$content_contact .= '</form>';
