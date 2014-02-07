<?php
/*
Plugin Name: WPU Post types & taxonomies
Description: Load custom post types & taxonomies
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Post types
---------------------------------------------------------- */

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
        // Taxonomies
        if ( !isset( $post_type['taxonomies'] ) ) {
            $post_type['taxonomies'] = array();
        }
        // Menu icon
        if ( !isset( $post_type['menu_icon'] ) ) {
            $post_type['menu_icon'] = '';
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
            'menu_icon' => $post_type['menu_icon'],
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



/* ----------------------------------------------------------
  Register taxonomies
---------------------------------------------------------- */

add_action( 'init', 'wputh_add_taxonomies' );
function wputh_add_taxonomies() {
    $taxonomies = apply_filters( 'wputh_get_taxonomies', array() );
    $taxonomies = wputh_verify_taxonomies( $taxonomies );
    foreach ( $taxonomies as $slug => $taxo ) {
        register_taxonomy(
            $slug,
            $taxo['post_type'],
            array(
                'label' => $taxo['name'],
                'rewrite' => array( 'slug' => $slug ),
                'hierarchical' => $taxo['hierarchical']
            )
        );
    }
}

/* ----------------------------------------------------------
  Verify taxonomies
---------------------------------------------------------- */

function wputh_verify_taxonomies( $taxonomies ) {
    foreach ( $taxonomies as $slug => $taxo ) {
        $post_type = ( isset( $taxo['post_type'] ) ? $taxo['post_type'] : 'post' );
        if ( !is_array( $post_type ) ) {
            $post_type = array( $post_type );
        }
        $taxonomies[$slug]['post_type'] = $post_type;
        $taxonomies[$slug]['hierarchical'] = isset( $taxo['hierarchical'] ) ? $taxo['hierarchical'] : true;
        $taxonomies[$slug]['admin_column'] = isset( $taxo['admin_column'] ) ? $taxo['admin_column'] : false;
    }
    return $taxonomies;
}

/* ----------------------------------------------------------
  Add taxonomy columns
---------------------------------------------------------- */

add_filter( 'manage_posts_columns', 'wputh_columns_head_taxo', 10 );
function wputh_columns_head_taxo( $defaults ) {
    global $post;
    // Isolate latest value
    $last_key = key( array_slice( $defaults, -1, 1, TRUE ) );
    $last_value = $defaults[ $last_key ];
    unset( $defaults[ $last_key ] );

    $taxonomies = apply_filters( 'wputh_get_taxonomies', array() );
    $taxonomies = wputh_verify_taxonomies( $taxonomies );

    foreach ( $taxonomies as $slug => $taxo ) {
        // Add keys
        if ( $taxo['admin_column'] && isset( $post->post_type ) && in_array( $post->post_type, $taxo['post_type'] ) ) {
            $defaults[$slug] = $taxo['name'];
        }
    }

    // Add latest value
    $defaults[$last_key] = $last_value;
    return $defaults;
}

add_action( 'manage_posts_custom_column', 'wputh_columns_content_taxo', 10, 2 );
function wputh_columns_content_taxo( $column_name, $post_id ) {
    global $post;
    if ( !isset( $post->post_type ) ) {
        return;
    }
    $taxonomies = apply_filters( 'wputh_get_taxonomies', array() );
    $taxonomies = wputh_verify_taxonomies( $taxonomies );

    foreach ( $taxonomies as $slug => $taxo ) {
        if ( $column_name == $slug && in_array( $post->post_type, $taxo['post_type'] ) ) {
            $terms = wp_get_post_terms( $post_id, $slug );
            $content_term = array();
            if ( is_array( $terms ) ) {
                foreach ( $terms as $term ) {
                    $content_term[] = '<a href="'.admin_url( 'edit.php?post_type='.$post->post_type.'&'.$slug.'='.$term->slug ) . '">'.$term->name.'</a>';
                }
            }
            if ( empty( $content_term ) ) {
                $content_term = array( '-' );
            }
            echo implode( ', ', $content_term );
        }
    }
}
