<?php

/*
Plugin Name: WPU UX Tweaks
Description: Adds UX enhancement & tweaks to WordPress
Version: 0.15
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
  CONTENT TWEAKS
---------------------------------------------------------- */

/* Prevent bad formed links
 -------------------------- */

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

/* Clean up text from PDF
 -------------------------- */

add_filter('the_content', 'wpuux_cleanup_pdf_text');
add_filter('the_excerpt', 'wpuux_cleanup_pdf_text');

function wpuux_cleanup_pdf_text($co) {
    $letters = array(
        'a',
        'A',
        'c',
        'C',
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
        $co = str_replace($letter . '¸', '&' . $letter . 'cedil;', $co);
    }
    return $co;
}

/* Specials smileys
 -------------------------- */

add_filter('the_title', 'wpuux_special_smileys');
add_filter('the_content', 'wpuux_special_smileys');
add_filter('the_excerpt', 'wpuux_special_smileys');

function wpuux_special_smileys($content) {
    $content = str_replace(' <3', ' &hearts;', $content);
    $content = str_replace('<3 ', '&hearts; ', $content);
    return $content;
}

/* Add content classes
 -------------------------- */

add_filter('the_content', 'wpuux_content_classes', 99, 1);
function wpuux_content_classes($content) {

    /* Add a class to P tags containing only a A>IMG */
    $content = preg_replace('/<p>([\s]?)<a([^>]*)>([\s]?)<img([^>]*)>([\s]?)<\/a>([\s]?)<\/p>/isU', "<p class=\"only-child-link \">$1<a$2><img$4></a></p>", $content);

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
  Add copyright to content in RSS feed
---------------------------------------------------------- */

// src : http://www.catswhocode.com/blog/useful-snippets-to-protect-your-wordpress-blog-against-scrapers

add_filter('the_excerpt_rss', 'wpuux_add_copyright_feed');
add_filter('the_content', 'wpuux_add_copyright_feed');

function wpuux_add_copyright_feed($content) {
    if (is_feed()) {
        $content .= '<hr /><p>&copy; ' . date('Y') . ' ' . get_bloginfo('name') . ' - <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
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
        'bmp',
        'css',
        'doc',
        'gif',
        'ico',
        'jpg',
        'js',
        'png',
        'rar',
        'tar',
        'txt',
        'xml',
        'zip'
    );
    if (!empty($_SERVER['REQUEST_URI'])) {
        $fileExtension = strtolower(pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION));
    }
    $badFileTypes = apply_filters('wpuux_preventheavy404_filestype', $badFileTypes);
    if (in_array($fileExtension, $badFileTypes)) {
        exit('error');
    }
}

/* ----------------------------------------------------------
  Check "Remember me" by default on login
---------------------------------------------------------- */

add_action('login_form', 'wpuux_check_rememberme');
function wpuux_check_rememberme() {
    global $rememberme;
    $rememberme = 1;
}

/* ----------------------------------------------------------
  Images
---------------------------------------------------------- */

/* Clean default image title
 -------------------------- */

add_action('add_attachment', 'wpuux_clean_default_image_title');
function wpuux_clean_default_image_title($post_ID) {
    $post = get_post($post_ID);
    $post->post_title = str_replace(array(
        '-',
        '_'
    ), ' ', $post->post_title);
    $post->post_title = ucwords($post->post_title);
    wp_update_post(array(
        'ID' => $post_ID,
        'post_title' => $post->post_title
    ));
    return $post_ID;
}

/* Set default image link to none
 -------------------------- */

add_action('init', 'wpuux_default_link_type');
function wpuux_default_link_type() {
    $image_default_link_type = get_option('image_default_link_type');
    if ($image_default_link_type != 'none') {
        update_option('image_default_link_type', 'none');
    }
}

/* Thumbnails for post columns
 -------------------------- */

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
        if (isset($image[0])) {
            echo '<img style="height:70px;width:70px;" src="' . $image[0] . '" alt="" />';
        }
    }
}

/* Set media select to uploaded
 -------------------------- */

/* Thx http://wordpress.stackexchange.com/a/76213 */

add_action('admin_footer-post-new.php', 'wpuux_set_media_select_uploaded');
add_action('admin_footer-post.php', 'wpuux_set_media_select_uploaded');

function wpuux_set_media_select_uploaded() {?><script>
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
  Disable WP Emoji
---------------------------------------------------------- */

add_action('init', 'wpuux_disable_newemojis');
function wpuux_disable_newemojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
}

/* ----------------------------------------------------------
  Fix admin
---------------------------------------------------------- */

add_action('admin_head', 'wpuux_fixchromebug_admin');
function wpuux_fixchromebug_admin() {
    echo '<style>#adminmenu{-webkit-transform:translateZ(0);transform:translateZ(0);}</style>';
}

/* ----------------------------------------------------------
  Disable heartbeat API on new post
---------------------------------------------------------- */

add_action('init', 'wpuux_stop_heartbeat', 1);
function wpuux_stop_heartbeat() {
    global $pagenow;
    if ($pagenow == 'post-new.php') {
        wp_deregister_script('heartbeat');
    }
}

/* ----------------------------------------------------------
  Editor can use menus
---------------------------------------------------------- */

add_action('init', 'wpuux_add_menus_editor');
function wpuux_add_menus_editor() {
    $roleObject = get_role('editor');
    if (!$roleObject->has_cap('edit_theme_options')) {
        $roleObject->add_cap('edit_theme_options');
    }
}

add_action('admin_head', 'wpuux_add_menus__hide_menu_full');
function wpuux_add_menus__hide_menu_full() {
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    if (in_array('editor', $roles)) {
        remove_submenu_page('themes.php', 'themes.php');
        remove_submenu_page('themes.php', 'widgets.php');
        remove_submenu_page('themes.php', 'custom-header');
        remove_submenu_page('themes.php', 'custom-background');
    }
}

add_action('admin_head', 'wpuux_add_menus__hide_menuhead_full');
function wpuux_add_menus__hide_menuhead_full() {
    global $submenu;
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    if (in_array('editor', $roles)) {
        unset($submenu['themes.php'][6]);
        unset($submenu['themes.php'][15]);
        unset($submenu['themes.php'][20]);
    }
}
