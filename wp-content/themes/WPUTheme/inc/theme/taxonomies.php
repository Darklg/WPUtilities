<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

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
