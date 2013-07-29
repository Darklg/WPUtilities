<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

add_action( 'init', 'wputh_add_taxonomies' );

function wputh_add_taxonomies() {
    $taxonomies = array(
        'importance' => array(
            'name' => __( 'Importance', 'wputh' )
        )
    );

    foreach ( $taxonomies as $slug => $taxo ) {
        register_taxonomy(
            $slug,
            (isset($taxo['post_type']) ? $taxo['post_type'] : 'post'),
            array(
                'label' => $taxo['name'],
                'rewrite' => array( 'slug' => $slug ),
                'hierarchical' => (isset($taxo['hierarchical']) ? $taxo['hierarchical'] : true)
            )
        );
    }
}
