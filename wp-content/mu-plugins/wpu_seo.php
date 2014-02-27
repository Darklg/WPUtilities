<?php
/*
Plugin Name: WPU SEO
Description: Enhance SEO : Clean title, nice metas.
Version: 0.10
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Contributor: @boiteaweb
Last Update: 07 dec. 2013
*/


class WPUSEO {
    function init() {
        // Filter
        add_filter( 'wp_title', array( &$this, 'wp_title' ), 10, 2 );

        // Actions
        add_action( 'wp_head', array( &$this, 'add_metas' ), 0 );
        add_action( 'wp_head', array( &$this, 'add_metas_robots' ), 1 , 0 );
        add_action( 'wp_footer', array( &$this, 'display_google_analytics_code' ) );

        // Clean WP Head
        add_action( 'template_redirect', array( &$this, 'clean_wordpress_head' ) );

        // Admin boxes
        add_filter( 'wpu_options_boxes', array( &$this, 'add_boxes' ), 99, 1 );
        add_filter( 'wpu_options_fields', array( &$this, 'add_fields' ), 99, 1 );

        // User boxes
        add_filter( 'wpu_usermetas_sections', array( &$this, 'add_user_sections' ), 10, 3 );
        add_filter( 'wpu_usermetas_fields', array( &$this, 'add_user_fields' ), 10, 3 );

    }

    /* ----------------------------------------------------------
      Clean WordPress head
    ---------------------------------------------------------- */

    function clean_wordpress_head() {
        if ( !is_single() ) {
            remove_action( 'wp_head', 'wp_shortlink_wp_head' );
            remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
        }
    }

    /* ----------------------------------------------------------
      Admin Options
    ---------------------------------------------------------- */

    function add_boxes( $boxes ) {
        $boxes['wpu_seo'] = array( 'name' => 'WPU SEO : Main' );
        $boxes['wpu_seo_google'] = array( 'name' => 'WPU SEO : Google' );
        $boxes['wpu_seo_facebook'] = array( 'name' => 'WPU SEO : Facebook' );
        $boxes['wpu_seo_twitter'] = array( 'name' => 'WPU SEO : Twitter' );
        return $boxes;
    }

    function add_fields( $options ) {
        // Various
        $options['wpu_home_meta_description'] = array( 'label' => __( 'Main meta description', 'wputh' ), 'type' => 'textarea', 'box' => 'wpu_seo' );
        $options['wpu_home_meta_keywords'] = array( 'label' => __( 'Main meta keywords', 'wputh' ), 'type' => 'textarea', 'box' => 'wpu_seo' );

        // Google
        $options['wpu_google_site_verification'] = array( 'label' => __( 'Google verification ID', 'wputh' ), 'box' => 'wpu_seo_google' );
        $options['wputh_ua_analytics'] = array( 'label' => __( 'Google Analytics ID', 'wputh' ), 'box' => 'wpu_seo_google' );

        // Facebook
        $options['wputh_fb_admins'] = array( 'label' => __( 'FB:Admins ID', 'wputh' ), 'box' => 'wpu_seo_facebook' );
        $options['wputh_fb_app'] = array( 'label' => __( 'FB:App ID', 'wputh' ), 'box' => 'wpu_seo_facebook' );
        $options['wputh_fb_image'] = array( 'label' => __( 'FB:Image', 'wputh' ), 'box' => 'wpu_seo_facebook', 'type' => 'media' );

        // Twitter
        $options['wpu_seo_user_twitter_site_username'] = array( 'label' => __( 'Twitter site @username', 'wputh' ), 'box' => 'wpu_seo_twitter' );
        $options['wpu_seo_user_twitter_site_creator'] = array( 'label' => __( 'Twitter site @creator', 'wputh' ), 'box' => 'wpu_seo_twitter' );
        $options['wpu_seo_user_twitter_account_id'] = array( 'label' => __( 'Twitter ads account ID', 'wputh' ), 'box' => 'wpu_seo_twitter' );

        return $options;
    }

    /* ----------------------------------------------------------
      User Options
    ---------------------------------------------------------- */

    function add_user_sections( $sections ) {
        $sections['wpu-seo'] = array(
            'name' => 'WPU SEO'
        );
        return $sections;
    }

    function add_user_fields( $fields ) {
        $fields['wpu_seo_user_google_profile'] = array(
            'name' => 'Google+ URL',
            'section' => 'wpu-seo'
        );
        return $fields;
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
        $links = array();

        $metas['og_sitename'] = array(
            'property' => 'og:site_name',
            'content' => get_bloginfo( 'name' )
        );

        $metas['og_type'] = array(
            'property' => 'og:type',
            'content' => 'website'
        );

        $wpu_seo_user_twitter_site_username = trim( get_option( 'wpu_seo_user_twitter_site_username' ) );
        if ( !empty( $wpu_seo_user_twitter_site_username ) && $this->testTwitterUsername( $wpu_seo_user_twitter_site_username ) ) {
            $metas['twitter_site'] = array(
                'name' => 'twitter:site',
                'content' => $wpu_seo_user_twitter_site_username
            );
        }
        $wpu_seo_user_twitter_site_creator = trim( get_option( 'wpu_seo_user_twitter_site_creator' ) );
        if ( !empty( $wpu_seo_user_twitter_site_creator ) && $this->testTwitterUsername( $wpu_seo_user_twitter_site_creator ) ) {
            $metas['twitter_creator'] = array(
                'name' => 'twitter:creator',
                'content' => $wpu_seo_user_twitter_site_creator
            );
        }

        $wpu_seo_user_twitter_account_id = trim( get_option( 'wpu_seo_user_twitter_account_id' ) );
        if ( !empty( $wpu_seo_user_twitter_account_id ) ) {
            $metas['twitter_account_id'] = array(
                'property' => 'twitter:account_id',
                'content' => $wpu_seo_user_twitter_account_id
            );
        }

        if ( is_single() || is_page() ) {

            $description = $this->prepare_text( $post->post_content );

            /* Twitter : Summary card */
            $metas['twitter_card'] = array(
                'name' => 'twitter:card',
                'content' => 'summary'
            );
            $metas['twitter_title'] = array(
                'name' => 'twitter:title',
                'content' => get_the_title()
            );
            $metas['twitter_description'] = array(
                'name' => 'twitter:description',
                'content' => $description
            );

            /* Facebook : Open Graph */
            $metas['og_type']['content'] = 'article';

            // Description
            $metas['description'] = array(
                'name' => 'description',
                'content' => $description
            );

            $keywords = $this->get_post_keywords( get_the_ID() );
            if ( !empty( $keywords ) ) {
                $keywords_txt = implode( ', ', $keywords );
                $metas['keywords'] = array(
                    'name' => 'keywords',
                    'content' => $this->prepare_text( $keywords_txt )
                );
            }

            $metas['og_title'] = array(
                'property' => 'og:title',
                'content' => get_the_title()
            );
            $metas['og_url'] = array(
                'property' => 'og:url',
                'content' => get_permalink()
            );
            $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium', true );
            if ( isset( $thumb_url[0] ) ) {
                $metas['og_image'] = array(
                    'property' => 'og:image',
                    'content' => $thumb_url[0]
                );
                $metas['twitter_image'] = array(
                    'name' => 'twitter:image',
                    'content' => $thumb_url[0]
                );
            }

            // Author informations
            $wpu_seo_user_google_profile = get_user_meta( $post->post_author, 'wpu_seo_user_google_profile', 1 );
            if ( filter_var( $wpu_seo_user_google_profile, FILTER_VALIDATE_URL ) ) {
                $links['google_author'] = array(
                    'rel' => 'author',
                    'href' => $wpu_seo_user_google_profile
                );
            }
        }

        if ( is_home() || is_front_page() ) {

            // Meta description
            $home_meta_description = trim( get_bloginfo( 'description' ) );
            $wpu_description = trim( get_option( 'wpu_home_meta_description' ) );
            if ( !empty( $wpu_description ) ) {
                $home_meta_description = $wpu_description;
            }
            $metas['description'] = array(
                'name' => 'description',
                'content' => $this->prepare_text( $home_meta_description, 200 )
            );

            // Meta keywords
            $wpu_keywords = trim( get_option( 'wpu_home_meta_keywords' ) );
            if ( !empty( $wpu_keywords ) ) {
                $metas['keywords'] = array(
                    'name' => 'keywords',
                    'content' => $this->prepare_text( $wpu_keywords, 200 )
                );
            }

            $metas['og_title'] = array(
                'property' => 'og:title',
                'content' => get_bloginfo( 'name' )
            );
            $metas['og_url'] = array(
                'property' => 'og:url',
                'content' => home_url()
            );

            $og_image = get_template_directory_uri() . '/screenshot.png';
            $opt_wputh_fb_image = get_option( 'wputh_fb_image' );
            $wputh_fb_image = wp_get_attachment_image_src( $opt_wputh_fb_image, 'medium', true );
            if ( $opt_wputh_fb_image != false && isset( $wputh_fb_image[0] ) ) {
                $og_image = $wputh_fb_image[0];
            }
            $metas['og_image'] = array(
                'property' => 'og:image',
                'content' => $og_image
            );
        }

        // Google Site
        $wpu_google_site_verification = trim( get_option( 'wpu_google_site_verification' ) );
        if ( !empty( $wpu_google_site_verification ) ) {
            $metas['google_site_verification'] = array(
                'name' => 'google-site-verification',
                'content' => $wpu_google_site_verification
            );
        }

        // FB Admins
        $wputh_fb_admins = trim( get_option( 'wputh_fb_admins' ) );
        if ( !empty( $wputh_fb_admins ) ) {
            $metas['fb_admins'] = array(
                'property' => 'fb:admins',
                'content' => $wputh_fb_admins
            );
        }

        // FB App
        $wputh_fb_app = trim( get_option( 'wputh_fb_app' ) );
        if ( !empty( $wputh_fb_admins ) ) {
            $metas['fb_app'] = array(
                'property' => 'fb:app',
                'content' => $wputh_fb_admins
            );
        }

        echo $this->special_convert_array_html( $metas );
        echo $this->special_convert_array_html( $links, 'link' );
    }

    /* ----------------------------------------------------------
      Robots tag
    ---------------------------------------------------------- */

    function add_metas_robots() {
        $metas = array();

        // Disable indexation for archives pages after page 1 OR 404 page OR paginated comments
        if ( ( is_paged() && ( is_category() || is_tag() || is_author() || is_tax() ) ) ||
            is_404() ||
            ( is_single() && comments_open() && (int) get_query_var( 'cpage' ) > 0 )
        ) {
            $metas['robots'] = array(
                'name' => 'robots',
                'content' => 'noindex, follow'
            );
        }

        echo $this->special_convert_array_html( $metas );
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
      Get post keywords
    ---------------------------------------------------------- */

    function get_post_keywords( $id ) {
        global $post;

        // Keywords
        $keywords_raw = array();

        $title = explode( ' ', strtolower( get_the_title( $id ) ) );
        foreach ( $title as $word ) {
            if ( strlen( $word ) > 3 ) {
                $keywords_raw = $this->check_keywords_value( sanitize_title( $word ), $word, $keywords_raw );
            }
        }

        $keywords_raw = $this->add_terms_to_keywords( get_the_category( $id ), $keywords_raw );
        $keywords_raw = $this->add_terms_to_keywords( get_the_tags( $id ), $keywords_raw );

        // Sort keywords by score
        usort( $keywords_raw, array( 'WPUSEO', 'order_keywords_values' ) );

        // Set keywords
        $keywords = array();
        foreach ( $keywords_raw as $keyword ) {
            $keywords[] = $keyword[1];
        }
        return $keywords;
    }

    function add_terms_to_keywords( $terms, $keywords_raw ) {
        if ( is_array( $terms ) ) {
            foreach ( $terms as $term ) {
                $keywords_raw = $this->check_keywords_value( $term->slug, $term->name, $keywords_raw );
            }
        }

        return $keywords_raw;
    }

    function check_keywords_value( $slug, $word, $keywords_raw ) {
        $word = str_replace( array( ',' ), ' ', $word );
        if ( array_key_exists( $slug, $keywords_raw ) ) {
            $keywords_raw[$slug][0]++;
        }
        else {
            $keywords_raw[$slug] = array( 1, $word );
        }
        return $keywords_raw;
    }

    function order_keywords_values( $a, $b ) {
        return $a[0] < $b[0];
    }

    /* ----------------------------------------------------------
      Utilities
    ---------------------------------------------------------- */

    /* Test a twitter username
    -------------------------- */

    public function testTwitterUsername( $username ) {
        return preg_match( '/^\@([a-zA_Z_0-9]+)$/', $username ) !== false;
    }

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

    function special_convert_array_html( $metas, $tag='meta' ) {
        $html = '';
        foreach ( $metas as $values ) {
            $html .= '<'.$tag;
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
