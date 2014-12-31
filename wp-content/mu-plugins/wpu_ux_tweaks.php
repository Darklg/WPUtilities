<?php

/*
Plugin Name: WPU UX Tweaks
Description: Adds UX enhancement & tweaks to WordPress
Version: 0.8
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if (!defined('ABSPATH')) {
    exit();
}

/* ----------------------------------------------------------
  Clean head
---------------------------------------------------------- */

add_action('init', 'wpuux_clean_head');

function wpuux_clean_head() {
    global $wp_widget_factory;

    // Hardcoded recent comments style
    if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
        remove_action('wp_head', array(
            $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
            'recent_comments_style'
        ));
    }

    // Meta generator
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}

/* ----------------------------------------------------------
  Prevent bad formed link
---------------------------------------------------------- */

add_action('the_content', 'wpuux_bad_formed_links');

function wpuux_bad_formed_links($content) {
    $badform = array();
    $goodform = array();

    $badform[] = 'href="www.';
    $goodform[] = 'href="http://www.';

    $badform[] = 'href="http//';
    $goodform[] = 'href="http://';

    $badform[] = 'href=" http://';
    $goodform[] = 'href="http://';

    $content = str_replace($badform, $goodform, $content);
    return $content;
}

/* ----------------------------------------------------------
  Prevent invalid characters in file name
---------------------------------------------------------- */

add_filter('sanitize_file_name', 'remove_accents');
add_filter('sanitize_file_name', 'wpuux_uxt_clean_filename');

function wpuux_uxt_clean_filename($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-_\.]+/', '', $string);
    return $string;
}

/* ----------------------------------------------------------
  Clean default image title
---------------------------------------------------------- */

add_action('add_attachment', 'wpuux_clean_default_image_title');
function wpuux_clean_default_image_title($post_ID) {
    $post = get_post($post_ID);
    $post->post_title = str_replace(array(
        '-',
        '_'
    ) , ' ', $post->post_title);
    $post->post_title = ucwords($post->post_title);
    wp_update_post(array(
        'ID' => $post_ID,
        'post_title' => $post->post_title
    ));
    return $post_ID;
}

/* ----------------------------------------------------------
  Set media select to uploaded : http://wordpress.stackexchange.com/a/76213
---------------------------------------------------------- */

add_action('admin_footer-post-new.php', 'wpuux_set_media_select_uploaded');
add_action('admin_footer-post.php', 'wpuux_set_media_select_uploaded');

function wpuux_set_media_select_uploaded() { ?><script>
jQuery(function($) {
    var called = 0;
    $('#wpcontent').ajaxStop(function() {
        $('[value="uploaded"]').each(function(){
            var uploaded = $(this),
                uploadedParent = uploaded.parent();
            if (!uploadedParent.hasClass('has-set-uploaded')) {
                uploaded.attr('selected', true).parent().trigger('change');
                uploadedParent.addClass('has-set-uploaded');
            }
        });
    });
});
</script><?php
}

/* ----------------------------------------------------------
  Add copyright to content in RSS feed
---------------------------------------------------------- */

// src : http://www.catswhocode.com/blog/useful-snippets-to-protect-your-wordpress-blog-against-scrapers

add_filter('the_excerpt_rss', 'wpuux_add_copyright_feed');
add_filter('the_content', 'wpuux_add_copyright_feed');

function wpuux_add_copyright_feed($content) {
    if (is_feed()) {
        $content.= '<hr /><p>&copy; ' . date('Y') . ' ' . get_bloginfo('name') . ' - <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
    }
    return $content;
}

/* ----------------------------------------------------------
  Redirect to the only search result.
---------------------------------------------------------- */

add_action('template_redirect', 'wpuux_redirect_only_result_search');

function wpuux_redirect_only_result_search() {
    if (is_search()) {
        global $wp_query;
        if ($wp_query->post_count == 1) {
            wp_redirect(get_permalink($wp_query->post));
        }
    }
}

/* ----------------------------------------------------------
  Configure mail from & name
---------------------------------------------------------- */

add_filter('wp_mail_from', 'wpuux_new_mail_from');

function wpuux_new_mail_from($email) {
    $new_email = get_option('wpuux_opt_email');
    if (!empty($new_email) && $new_email !== false) {
        $email = $new_email;
    }

    return $email;
}

add_filter('wp_mail_from_name', 'wpuux_new_mail_from_name');

function wpuux_new_mail_from_name($name) {
    $new_email_name = get_option('wpuux_opt_email_name');
    if (!empty($new_email_name) && $new_email_name !== false) {
        $name = $new_email_name;
    }
    return $name;
}

/* ----------------------------------------------------------
  Clean up text from PDF
---------------------------------------------------------- */

add_filter('the_content', 'wpuux_cleanup_pdf_text');
add_filter('the_excerpt', 'wpuux_cleanup_pdf_text');

function wpuux_cleanup_pdf_text($co) {
    $letters = array(
        'a',
        'A',
        'e',
        'E',
        'i',
        'I',
        'o',
        'O',
        'u',
        'U'
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

/* ----------------------------------------------------------
  Thumbnails for post columns
---------------------------------------------------------- */

add_filter('manage_posts_columns', 'wpuux_add_column_thumb', 5);

function wpuux_add_column_thumb($defaults) {
    $defaults['wpuux_column_thumb'] = __('Thumbnail');
    return $defaults;
}

add_action('manage_posts_custom_column', 'wpuux_add_column_thumb_content', 5, 2);

function wpuux_add_column_thumb_content($column_name, $id) {
    global $post;
    if ($column_name === 'wpuux_column_thumb' && isset($post->ID)) {
        $thumb_id = get_post_thumbnail_id($post->ID);
        if (!$thumb_id) {
            return;
        }
        $image = wp_get_attachment_image_src($thumb_id, 'thumbnail');
        if (!isset($image[0])) {
            return;
        }
        echo '<img style="height:70px;width:70px;" src="' . $image[0] . '" alt="" />';
    }
}

/* ----------------------------------------------------------
  Specials smileys
---------------------------------------------------------- */

add_filter('the_title', 'wpuux_special_smileys');
add_filter('the_content', 'wpuux_special_smileys');
add_filter('the_excerpt', 'wpuux_special_smileys');

function wpuux_special_smileys($content) {
    $content = str_replace(' <3', ' &hearts;', $content);
    $content = str_replace('<3 ', '&hearts; ', $content);
    return $content;
}

/* ----------------------------------------------------------
  Prevent heavy 404 pages for static files
---------------------------------------------------------- */

/* From http://www.binarymoon.co.uk/2011/04/optimizing-wordpress-404s/ */

add_filter('template_redirect', 'wpuux_preventheavy404');
function wpuux_preventheavy404() {
    if (!is_404()) {
        return;
    }
    header('HTTP/1.1 404 Not Found');
    $fileExtension = '';
    $badFileTypes = array(
        'css',
        'txt',
        'jpg',
        'gif',
        'rar',
        'zip',
        'png',
        'bmp',
        'tar',
        'doc',
        'xml',
        'js',
    );
    if (!empty($_SERVER['REQUEST_URI'])) {
        $fileExtension = strtolower(pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION));
    }
    $badFileTypes = apply_filters('wpuux_preventheavy404_filestype', $badFileTypes);
    if (in_array($fileExtension, $badFileTypes)) {
        exit('error');
    }
}
