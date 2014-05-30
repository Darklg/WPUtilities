<?php

include dirname( __FILE__ ) . '/../../z-protect.php';

/* ----------------------------------------------------------
   Options for the plugin "WPU Options"
   ------------------------------------------------------- */

add_filter( 'wpu_options_boxes', 'set_wpu_options_boxes', 10, 3 );

function set_wpu_options_boxes( $boxes ) {
    $boxes['virtual_contacts'] = array( 'name' => 'Virtual contacts' );
    $boxes['social_networks'] = array( 'name' => 'Réseaux sociaux' );
    $boxes['pages_id'] = array( 'name' => 'Pages IDs' );
    return $boxes;
}


add_filter( 'wpu_options_fields', 'set_wputh_options_fields', 10, 3 );

function set_wputh_options_fields( $options ) {

    // Virtual contacts
    $options['wpu_opt_email'] = array(
        'label' => __( 'Email address', 'wputh' ),
        'box' => 'virtual_contacts',
        'type' => 'email',
        'test' => 'email'
    );

    // Social networks
    $wpu_social_links = unserialize(WPU_SOCIAL_LINKS);
    foreach ( $wpu_social_links as $id => $name ) {
        $options['social_'.$id.'_url'] = array( 'label' => $name . ' URL', 'box' => 'social_networks' );
    }

    // Pages IDs
    $options['about__page_id'] = array( 'label' => __( 'About', 'wputh' ), 'box' => 'pages_id', 'type' => 'page' );
    $options['mentions__page_id'] = array( 'label' => __( 'Mentions', 'wputh' ), 'box' => 'pages_id', 'type' => 'page' );
    return $options;
}
