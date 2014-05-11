<?php
/*
Plugin Name: WPU UX Tweaks
Description: Adds UX enhancement & tweaks to WordPress
Version: 0.4
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
add_filter( 'sanitize_file_name', 'wputh_uxt_clean_filename' );

function wputh_uxt_clean_filename( $string ) {
    $string = strtolower( $string );
    $string = preg_replace( '/[^a-z0-9-_\.]+/', '', $string );
    return $string;
}

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
            wp_redirect( get_permalink( $wp_query->post ) );
        }
    }
}

/* ----------------------------------------------------------
  Configure mail from & name
---------------------------------------------------------- */

add_filter( 'wp_mail_from', 'wpu_new_mail_from' );
function wpu_new_mail_from( $email ) {
    $new_email = get_option( 'wpu_opt_email' );
    if ( !empty( $new_email ) && $new_email !== false ) {
        $email = $new_email;
    }

    return $email;
}

add_filter( 'wp_mail_from_name', 'wpu_new_mail_from_name' );
function wpu_new_mail_from_name( $name ) {
    $new_email_name = get_option( 'wpu_opt_email_name' );
    if ( !empty( $new_email_name ) && $new_email_name !== false ) {
        $name = $new_email_name;
    }
    return $name;
}

/* ----------------------------------------------------------
  Clean up text from PDF
---------------------------------------------------------- */

function wputh_cleanup_pdf_text($co) {
    $letters = array(
        'a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U'
    );
    foreach ($letters as $letter) {
        $co = str_replace($letter . '̀', '&' . $letter . 'grave;', $co);
        $co = str_replace($letter . '́', '&' . $letter . 'acute;', $co);
        $co = str_replace($letter . '̂', '&' . $letter . 'circ;', $co);
        $co = str_replace($letter . '̈', '&' . $letter . 'uml;', $co);
    }
    $co = str_replace('ç', '&ccedil;', $co);
    return $co;
}
add_filter('the_content', 'wputh_cleanup_pdf_text');
add_filter('the_excerpt', 'wputh_cleanup_pdf_text');
