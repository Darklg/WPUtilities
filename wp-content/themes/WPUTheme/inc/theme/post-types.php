<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

add_action( 'init', 'wputh_add_post_types' );

function wputh_add_post_types() {

    $post_types = apply_filters( 'wputh_get_posttypes', array() );

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
        // Plural
        if ( !isset( $post_type['taxonomies'] ) ) {
            $post_type['taxonomies'] = array();
        }
        // Female
        $context = 'female';
        if ( !isset( $post_type['female'] ) || $post_type['female'] != 1 ) {
            $post_type['female'] = 0;
            $context = 'male';
        }

        $args = array(
            'public' => true,
            'publicly_queryable' => true,
            'has_archive' => true,
            'taxonomies' => $post_type['taxonomies'],
            'with_front' => true,
            'show_ui' => true,
            'rewrite' => true,
            'name' => $post_type['name'],
            'supports' => $post_type['supports'],
            'labels' => array(
                'name' => ucfirst( $post_type['plural'] ),
                'singular_name' => ucfirst( $post_type['name'] ),
                'add_new' => __( 'Add New', 'wputh' ),
                'add_new_item' => sprintf( _x( 'Add New %s', 'male', 'wputh' ), $post_type['name'] ),
                'edit_item' => sprintf( _x( 'Edit %s', 'male', 'wputh' ), $post_type['name'] ),
                'new_item' => sprintf( _x( 'New %s', 'male', 'wputh' ), $post_type['name'] ),
                'all_items' => sprintf( _x( 'All %s', 'male', 'wputh' ), $post_type['plural'] ),
                'view_item' => sprintf( _x( 'View %s', 'male', 'wputh' ), $post_type['name'] ),
                'search_items' => sprintf( _x( 'Search %s', 'male', 'wputh' ), $post_type['name'] ),
                'not_found' => sprintf( _x( 'No %s found', 'male', 'wputh' ), $post_type['name'] ),
                'not_found_in_trash' => sprintf( _x( 'No %s found in Trash', 'male', 'wputh' ), $post_type['name'] ),
                'parent_item_colon' => '',
                'menu_name' => ucfirst( $post_type['plural'] )
            )
        );

        // I couldn't use the content of $context var inside of _x() calls because of Poedit :(
        if ( $context == 'female' ) {
            $args['labels']['add_new_item'] = sprintf( _x( 'Add New %s', 'female', 'wputh' ), $post_type['name'] );
            $args['labels']['edit_item'] = sprintf( _x( 'Edit %s', 'female', 'wputh' ), $post_type['name'] );
            $args['labels']['new_item'] = sprintf( _x( 'New %s', 'female', 'wputh' ), $post_type['name'] );
            $args['labels']['all_items'] = sprintf( _x( 'All %s', 'female', 'wputh' ), $post_type['plural'] );
            $args['labels']['view_item'] =sprintf( _x( 'View %s', 'female', 'wputh' ), $post_type['name'] );
            $args['labels']['search_items'] = sprintf( _x( 'Search %s', 'female', 'wputh' ), $post_type['name'] );
            $args['labels']['not_found'] = sprintf( _x( 'No %s found', 'female', 'wputh' ), $post_type['name'] );
            $args['labels']['not_found_in_trash'] = sprintf( _x( 'No %s found in Trash', 'female', 'wputh' ), $post_type['name'] );
        }


        register_post_type( $slug, $args );
    }
}
