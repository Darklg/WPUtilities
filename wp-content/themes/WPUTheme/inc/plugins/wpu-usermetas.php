<?php

include dirname( __FILE__ ) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Options for the plugin "WPU User Metas"
---------------------------------------------------------- */

/* Sections
-------------------------- */

add_filter( 'wpu_usermetas_sections', 'set_wpu_usermetas_sections', 10, 3 );
function set_wpu_usermetas_sections( $sections ) {
    $sections['test-section'] = array(
        'name' => 'Test Section'
    );
    return $sections;
}

/* Field
-------------------------- */

add_filter( 'wpu_usermetas_fields', 'set_wpu_usermetas_fields', 10, 3 );
function set_wpu_usermetas_fields( $fields ) {
    $fields['wpu_user_height'] = array(
        'name' => 'User height',
        'section' => 'test-section'
    );
    return $fields;
}
