<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

add_action( 'init', 'wputh_add_post_types' );

function wputh_add_post_types() {
    $post_types = array(
        'task' => array(
            'name' => __('Task')
        )
    );

    foreach ( $post_types as $slug => $post_type ) {
        $args = array(
            'public' => true,
            'label' => $post_type['name']
        );
        register_post_type( $slug, $args );
    }
}
