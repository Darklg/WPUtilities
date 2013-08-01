<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

add_action( 'init', 'wputh_add_post_types' );

function wputh_add_post_types() {
    $post_types = array(
        'task' => array(
            'name' => __( 'Task', 'wputh' )
        )
    );

    foreach ( $post_types as $slug => $post_type ) {
        // Default label: slug
        if ( !isset( $post_type['name'] ) ) {
            $post_type['name'] = ucfirst( $slug );
        }
        // Default capabilities
        if ( !isset( $post_type['supports'] ) ) {
            $post_type['supports'] = array( 'title', 'editor', 'thumbnail' );
        }
        // Plural
        if ( !isset( $post_type['plural'] ) ) {
            $post_type['plural'] = $post_type['name'];
        }

        $args = array(
            'public' => true,
            'name' => $post_type['name'],
            'singular_name' => $post_type['name'],
            'label' => $post_type['plural'],
            'supports' => $post_type['supports']
        );
        register_post_type( $slug, $args );
    }
}
