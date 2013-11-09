<?php
/*
Plugin Name: WPU UX Tweaks
Description: Add UX enhancement & tweaks to WordPress
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if ( !defined( 'ABSPATH' ) ) {
    exit();
}


/* ----------------------------------------------------------
  Clean head
---------------------------------------------------------- */

add_action( 'init', 'wpu_clean_head' );
function wpu_clean_head() {
    global $wp_widget_factory;
    // Hardcoded recent comments style
    if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
        remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
    }
    // Meta generator
    remove_action( 'wp_head', 'wp_generator' );
}

/* ----------------------------------------------------------
  Prevent bad formed link
---------------------------------------------------------- */

add_action( 'the_content', 'wpu_bad_formed_links' );
function wpu_bad_formed_links( $content ) {
    $badform = array();
    $goodform = array();

    $badform[] = 'href="www.';
    $goodform[] = 'href="http://www.';

    $badform[] = 'href="http//';
    $goodform[] = 'href="http://';

    $badform[] = 'href=" http://';
    $goodform[] = 'href="http://';

    $content = str_replace( $badform, $goodform, $content );
    return $content;
}

/* ----------------------------------------------------------
  Prevent invalid characters in file name
---------------------------------------------------------- */

add_filter( 'sanitize_file_name', 'remove_accents' );
add_filter( 'sanitize_file_name', 'strtolower' );

/* ----------------------------------------------------------
  Set media select to uploaded : http://wordpress.stackexchange.com/a/76213
---------------------------------------------------------- */

add_action( 'admin_footer-post-new.php', 'wpu_set_media_select_uploaded' );
add_action( 'admin_footer-post.php', 'wpu_set_media_select_uploaded' );

function wpu_set_media_select_uploaded() { ?><script>
jQuery(function($) {
    var called = 0;
    $('#wpcontent').ajaxStop(function() {
        if (0 === called) {
            $('[value="uploaded"]').attr('selected', true).parent().trigger('change');
            called = 1;
        }
    });
});
</script><?php }

/* ----------------------------------------------------------
  Add copyright to content in RSS feed
---------------------------------------------------------- */
// src : http://www.catswhocode.com/blog/useful-snippets-to-protect-your-wordpress-blog-against-scrapers

add_filter( 'the_excerpt_rss', 'wpu_add_copyright_feed' );
add_filter( 'the_content', 'wpu_add_copyright_feed' );
function wpu_add_copyright_feed( $content ) {
    if ( is_feed() ) {
        $content .= '<hr /><p>&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) . ' - <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
    }
    return $content;
}

/* ----------------------------------------------------------
  Redirect to the only search result.
---------------------------------------------------------- */

add_action( 'template_redirect', 'wpu_redirect_only_result_search' );
function wpu_redirect_only_result_search() {
    if ( is_search() ) {
        global $wp_query;
        if ( $wp_query->post_count == 1 ) {
            wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
        }
    }
}
