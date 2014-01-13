<?php
/*
Plugin Name: WPU SEO
Description: Enhance SEO : Clean title, nice metas.
Version: 0.5
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Contributor: @boiteaweb
Last Update: 07 dec. 2013
*/


class WPUSEO {
    function init() {
        add_filter( 'wp_title', array( &$this, 'wp_title' ), 10, 2 );
        add_action( 'wp_head', array( &$this, 'add_metas' ), 0 );
        add_action( 'wp_head', array( &$this, 'add_metas_robots' ), 1 , 0 );
        add_action( 'wp_footer', array( &$this, 'display_google_analytics_code' ) );
        // Admin
        add_filter( 'wpu_options_boxes', array( &$this, 'add_boxes' ), 99, 1);
        add_filter( 'wpu_options_fields', array( &$this, 'add_fields' ), 99, 1);

    }

    /* ----------------------------------------------------------
      Admin
    ---------------------------------------------------------- */

    function add_boxes( $boxes ) {
        $boxes['wpu_seo'] = array( 'name' => 'WPU SEO' );
        return $boxes;
    }
    function add_fields( $options ) {
        // Various fields
        $options['wputh_ua_analytics'] = array( 'label' => __( 'Analytics code', 'wputh' ), 'box' => 'wpu_seo' );
        $options['wpu_home_meta_description'] = array( 'label' => __( 'Main meta description', 'wputh' ), 'type' => 'textarea', 'box' => 'wpu_seo' );
        return $options;

    }
    /* ----------------------------------------------------------
      Page Title
    ---------------------------------------------------------- */

    function wp_title( $title, $sep ) {
        $spaced_sep = ' ' . $sep . ' ';
        $new_title = '';
        // Home : Exception for order
        if ( is_home() ) {
            return get_bloginfo( 'name' ) . $spaced_sep . get_bloginfo( 'description' );
        }
        $new_title = $this->get_displayed_title();

        // Return new title with site name at the end
        return $new_title . $spaced_sep . get_bloginfo( 'name' );
    }

    function get_displayed_title() {
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
            global $author;
            $author_name = get_query_var( 'author_name' );
            $curauth = !empty( $author_name ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author ) );
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

    function add_metas() {
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
                'content' => $this->prepare_text( $post->post_content )
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
                'content' => $this->prepare_text( $home_meta_description, 200 )
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

        echo $this->metas_convert_array_html( $metas );
    }

    /* ----------------------------------------------------------
      Robots tag
    ---------------------------------------------------------- */

    function add_metas_robots() {
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

        echo $this->metas_convert_array_html( $metas );
    }

    /* ----------------------------------------------------------
      Google Analytics
    ---------------------------------------------------------- */

    function display_google_analytics_code() {
        $ua_analytics = get_option( 'wputh_ua_analytics' );
        if ( $ua_analytics !== false && !empty( $ua_analytics ) && !in_array( $ua_analytics, array( 'UA-XXXXX-X' ) ) ) {
            // List of vars to send to Google Analytics
            $gaq = array();
            // Analytics code used
            $gaq['account'] = "['_setAccount','".$ua_analytics."']";
            // Default action : page viewed
            $gaq['trackpageview'] = "['_trackPageview']";
            if ( is_404() ) {
                // Tracking 404 errors | http://www.joshstauffer.com/track-404s-with-google-analytics/
                $gaq['trackpageview'] = "['_trackPageview', '/404?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]";
            }

            echo '<script type="text/javascript">';
            echo 'var _gaq=_gaq||[];';
            foreach ( $gaq as $_gaq ) {
                echo '_gaq.push('.$_gaq.');';
            }
            echo "(function(){".
                "var ga=document.createElement('script');".
                "ga.type='text/javascript';".
                "ga.async=true;".
                "ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';".
                "var s=document.getElementsByTagName('script')[0];".
                "s.parentNode.insertBefore(ga,s);".
                "})();";
            echo '</script>';
        }
    }

    /* ----------------------------------------------------------
      Utilities
    ---------------------------------------------------------- */

    /* Prepare meta description
    -------------------------- */

    function prepare_text( $text, $max_length = 200 ) {
        $text = strip_shortcodes( $text );
        $text = strip_tags( $text );
        $text = preg_replace( "/\s+/", ' ', $text );
        $text = trim( $text );
        if ( strlen( $text ) > $max_length ) {
            $text = substr( $text, 0, $max_length - 5 ) . ' ...';
        }
        return $text;
    }

    /* Convert an array of metas to HTML
    -------------------------- */

    function metas_convert_array_html( $metas ) {
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

}

$WPUSEO = new WPUSEO();
$WPUSEO->init();
