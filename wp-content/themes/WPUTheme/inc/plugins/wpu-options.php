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

    // Create pages IDs from list defined in functions.php
    if (defined('PAGES_IDS')) {
        $pages_ids = unserialize(PAGES_IDS);
        if (is_array($pages_ids)) {
            foreach ($pages_ids as $id => $page) {
                $options[$id] = array(
                    'label' => __($page['name'], 'wputh') ,
                    'box' => 'pages_id',
                    'type' => 'page'
                );
            }
        }
    }

    return $options;
}
