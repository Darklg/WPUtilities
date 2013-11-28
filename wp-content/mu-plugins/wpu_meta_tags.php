<?php
/*
Plugin Name: WPU Meta tags
Description: Adds meta tags to the theme header
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Meta content & open graph
---------------------------------------------------------- */

add_action( 'wp_head', 'wpu_user_metas', 0 , 0 );

function wpu_user_metas() {
    global $post;
    $metas = array();

    $metas['og_sitename'] = array(
        'property' => 'og:site_name',
        'content' => get_bloginfo( 'name' )
    );

    $metas['og_type'] = array(
        'property' => 'og:type',
        'content' => 'blog'
    );

    if ( is_single() ) {

        // Meta description
        $meta_description = str_replace( array( "\n", "\t", '   ', '  ' ), ' ', trim( strip_tags( $post->post_content ) ) );
        if ( strlen( $meta_description ) > 195 ) {
            $meta_description = substr( $meta_description, 0, 190 ) . ' ...';
        }

        $metas['description'] = array(
            'name' => 'description',
            'content' => $meta_description
        );
        $metas['og_title'] = array(
            'property' => 'og:title',
            'content' => get_the_title()
        );
        $metas['og_url'] = array(
            'property' => 'og:url',
            'content' => get_permalink()
        );
        $metas['og_image'] = array(
            'property' => 'og:image',
            'content' => wputh_get_thumbnail_url( 'thumbnail' )
        );
    }

    if ( is_home() ) {
        $meta_description = get_bloginfo( 'description' );
        $metas['description'] = array(
            'name' => 'description',
            'content' => substr( $meta_description, 0, 200 ) . ' ...'
        );
        $metas['og_title'] = array(
            'property' => 'og:title',
            'content' => get_bloginfo( 'name' )
        );
        $metas['og_url'] = array(
            'property' => 'og:url',
            'content' => site_url()
        );
        $metas['og_image'] = array(
            'property' => 'og:image',
            'content' => get_template_directory_uri() . '/screenshot.png'
        );
    }

    foreach ( $metas as $values ) {
        echo '<meta';
        foreach ( $values as $name => $value ) {
            echo ' '.$name.'="' . $value . '"';
        }
        echo ' />';
    }
}

/* ----------------------------------------------------------
  Robots tag
---------------------------------------------------------- */

add_action( 'wp_head', 'wpu_user_metas_robots', 1 , 0 );

function wpu_user_metas_robots() {
    global $post;
    $metas = array();

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    // Disable indexation for archives pages after page 1 OR 404 page
    if ( ( ( is_category() || is_tag() || is_author() || is_tax() ) && $paged > 1 ) || is_404() ) {
        $metas['robots'] = array(
            'name' => 'robots',
            'content' => 'noindex, follow'
        );
    }

    foreach ( $metas as $values ) {
        echo '<meta';
        foreach ( $values as $name => $value ) {
            echo ' '.$name.'="' . $value . '"';
        }
        echo ' />';
    }
}
