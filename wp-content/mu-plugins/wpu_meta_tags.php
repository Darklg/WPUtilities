<?php
/*
Plugin Name: WPU Meta tags
Description: Adds meta tags to the theme header
Version: 0.3.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Contributor: @boiteaweb
Last Update: 07 dec. 2013
*/

/* ----------------------------------------------------------
  Meta content & open graph
---------------------------------------------------------- */

add_action( 'wp_head', 'wpu_user_metas', 0 );
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

    if ( is_single() || is_page() ) {
        $metas['description'] = array(
            'name' => 'description',
            'content' => wpu_user_metas_prepare_text( $post->post_content )
        );
        $metas['og_title'] = array(
            'property' => 'og:title',
            'content' => get_the_title()
        );
        $metas['og_url'] = array(
            'property' => 'og:url',
            'content' => get_permalink()
        );
        $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail', true );
        if ( isset( $thumb_url[0] ) ) {
            $metas['og_image'] = array(
                'property' => 'og:image',
                'content' => $thumb_url[0]
            );
        }
    }

    if ( is_home() || is_front_page() ) {
        $metas['description'] = array(
            'name' => 'description',
            'content' => wpu_user_metas_prepare_text( get_bloginfo( 'description' ), 200 )
        );
        $metas['og_title'] = array(
            'property' => 'og:title',
            'content' => get_bloginfo( 'name' )
        );
        $metas['og_url'] = array(
            'property' => 'og:url',
            'content' => home_url()
        );
        $metas['og_image'] = array(
            'property' => 'og:image',
            'content' => get_template_directory_uri() . '/screenshot.png'
        );
    }

    echo wpu_user_metas_convert_array_html( $metas );
}

/* ----------------------------------------------------------
  Robots tag
---------------------------------------------------------- */

add_action( 'wp_head', 'wpu_user_metas_robots', 1 , 0 );

function wpu_user_metas_robots() {
    $metas = array();

    // Disable indexation for archives pages after page 1 OR 404 page OR paginated comments
    if ( ( is_paged() && ( is_category() || is_tag() || is_author() || is_tax() ) ) ||
        is_404() ||
        ( comments_open() && (int) get_query_var( 'cpage' ) > 0 )
    ) {
        $metas['robots'] = array(
            'name' => 'robots',
            'content' => 'noindex, follow'
        );
    }

    echo wpu_user_metas_convert_array_html( $metas );
}

/* ----------------------------------------------------------
  Utilities
---------------------------------------------------------- */

/* Prepare meta description
-------------------------- */

function wpu_user_metas_prepare_text( $text, $max_length = 200 ) {
    $text = strip_shortcodes( $text );
    $text = strip_tags( $text );
    $text = preg_replace( "/\s+/", ' ', $text );
    $text = trim( $text );
    if ( strlen( $text ) > $max_length ) {
        $text = substr( $text, 0, $max_length - 5 ) . ' &hellip;';
    }
    return $text;
}

/* Convert an array of metas to HTML
-------------------------- */

function wpu_user_metas_convert_array_html( $metas ) {
    $html = '';
    foreach ( $metas as $values ) {
        $html .= '<meta';
        foreach ( $values as $name => $value ) {
            $html .= sprintf( ' %s="%s"', $name, esc_attr( $value ) );
        }
        $html .= ' />';
    }
    return $html;
}
