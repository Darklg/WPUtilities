<?php
/*
Plugin Name: WPU Meta tags & title
Description: Adds meta tags & better page title
Version: 0.4.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Contributor: @boiteaweb
Last Update: 07 dec. 2013
*/

/* ----------------------------------------------------------
  Page Title
---------------------------------------------------------- */

add_filter( 'wp_title', 'wputh_wp_title', 10, 2 );
function wputh_wp_title( $title, $sep ) {
    $spaced_sep = ' ' . $sep . ' ';
    $new_title = '';
    // Home : Exception for order
    if ( is_home() ) {
        return get_bloginfo( 'name' ) . $spaced_sep . get_bloginfo( 'description' );
    }
    $new_title = wputh_get_displayed_title();

    // Return new title with site name at the end
    return $new_title . $spaced_sep . get_bloginfo( 'name' );
}

function wputh_get_displayed_title() {
    global $post;
    if ( is_singular() ) {
        $displayed_title = get_the_title();
    }
    if ( is_tax() ) {
        $displayed_title = single_cat_title( "", false );
    }
    if ( is_search() ) {
        $displayed_title = sprintf( __( 'Search results for "%s"', 'wputh' ),  get_search_query() );
    }
    if ( is_404() ) {
        $displayed_title =  __( '404 Error', 'wputh' );
    }
    if ( is_archive() ) {
        $displayed_title = __( 'Archive', 'wputh' );
    }
    if ( is_tag() ) {
        $displayed_title = __( 'Tag:', 'wputh' ) . ' ' . single_tag_title( "", false );
    }
    if ( is_category() ) {
        $displayed_title = __( 'Category:', 'wputh' ) . ' ' . single_cat_title( "", false );
    }
    if ( is_author() ) {
        $curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author ) );
        $displayed_title = __( 'Author:', 'wputh' ) . ' ' . $curauth->nickname;
    }
    if ( is_year() ) {
        $displayed_title = __( 'Year:', 'wputh' ) . ' ' . get_the_time( __( 'Y', 'wputh' ) );
    }
    if ( is_month() ) {
        $displayed_title = __( 'Month:', 'wputh' ) . ' ' . get_the_time( __( 'F Y', 'wputh' ) );
    }
    if ( is_day() ) {
        $displayed_title = __( 'Day:', 'wputh' ) . ' ' . get_the_time( __( 'F j, Y', 'wputh' ) );
    }
    return $displayed_title;
}

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
        $metas['og_type']['content'] = 'article';

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

        $home_meta_description = trim( get_bloginfo( 'description' ) );
        $wpu_description = trim( get_option( 'wpu_home_meta_description' ) );
        if ( !empty( $wpu_description ) ) {
            $home_meta_description = $wpu_description;
        }

        $metas['description'] = array(
            'name' => 'description',
            'content' => wpu_user_metas_prepare_text( $home_meta_description, 200 )
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
