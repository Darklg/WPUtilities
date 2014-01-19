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
            'uri' => '/js/classes/dk-smooth-scroll.js',
            'footer' => 1
        ),
        'functions' => array(
            'uri' => '/js/functions.js',
            'footer' => 1
        ),
        'wpu-home' => array(
            'uri' => '/js/modules/home.js',
            'footer' => 1
        ),
        'wpu-faq' => array(
            'uri' => '/js/modules/faq.js',
            'footer' => 1
        ),
        'events' => array(
            'uri' => '/js/events.js',
            'footer' => 1
        )
    );

    foreach ( $JScripts as $id => $details ) {
        $url = '';
        if ( isset( $details['url'] ) ) {
            $url = $details['url'];
        }
        if ( isset( $details['uri'] ) ) {
            $url = get_template_directory_uri() . $details['uri'];
        }
        $deps = isset( $details['deps'] ) ? $details['deps'] : false;
        $ver = isset( $details['ver'] ) ? $details['ver'] : false;
        $in_footer = isset( $details['footer'] ) && $details['footer'] == true;
        wp_register_script( $id, $url, $deps, $ver, $in_footer );
        wp_enqueue_script( $id );
    }
}
add_action( 'wp_enqueue_scripts', 'wputh_add_javascripts' );
