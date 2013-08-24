<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

function wputh_add_javascripts() {
    $JScripts = array(
        'mootools' => array(
            'uri' => '/js/lib/mootools-core-1.4.5-full-nocompat-yc.js'
        ),
        'mootools-more' => array(
            'uri' => '/js/lib/mootools-more-1.4.0.1.js'
        ),
        'dk-smooth-scroll' => array(
            'uri' => '/js/classes/dk-smooth-scroll.js'
        ),
        'functions' => array(
            'uri' => '/js/functions.js'
        ),
        'events' => array(
            'uri' => '/js/events.js'
        )
    );

    foreach ( $JScripts as $id => $details ) {
        wp_register_script( $id, get_template_directory_uri() . $details['uri'] );
        wp_enqueue_script( $id );
    }
}
add_action( 'wp_enqueue_scripts', 'wputh_add_javascripts' );
