<?php

include dirname( __FILE__ ) . '/../../z-protect.php';

/* ----------------------------------------------------------
   Options for the plugin "WPU Options"
   ------------------------------------------------------- */

add_filter( 'wpu_options_boxes', 'set_wpu_options_boxes', 10, 3 );

function set_wpu_options_boxes( $boxes ) {
    $boxes['virtual_contacts'] = array(
        'name' => 'Virtual contacts'
    );
    $boxes['pages_id'] = array(
        'name' => 'Pages IDs'
    );
    return $boxes;
}


add_filter( 'wpu_options_fields', 'set_wputh_options_fields', 10, 3 );

function set_wputh_options_fields( $options ) {
    $options['wputh_ua_analytics'] = array(
        'label' => __( 'Analytics code', 'wputh' )
    );
    $options['wpu_opt_email'] = array(
        'label' => __( 'Email address', 'wputh' ),
        'box' => 'virtual_contacts',
        'type' => 'email',
        'test' => 'email'
    );

    // Pages IDs
    $options['about__page_id'] = array( 'label' => __( 'About', 'wputh' ), 'box' => 'pages_id', 'type' => 'page' );
    $options['mentions__page_id'] = array( 'label' => __( 'Mentions', 'wputh' ), 'box' => 'pages_id', 'type' => 'page' );
    return $options;
}
