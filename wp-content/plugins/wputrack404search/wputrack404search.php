<?php
/*
Plugin Name: WPU Track 404 & Search
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Logs & analyze search queries & 404 Errors
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

/* ----------------------------------------------------------
  Tracking Hooks
---------------------------------------------------------- */

/* Add Hook search
-------------------------- */

add_action( 'template_redirect', 'wputrack404search_search' );
function wputrack404search_search() {
    /* - Log time, request, nb results */
    if ( !is_search() ) {
        return;
    }
    global $wpdb, $wp_query;
    $nb_results = 0;
    if ( isset( $wp_query->found_posts ) ) {
        $nb_results = $wp_query->found_posts;
    }
    $wpdb->insert(
        $wpdb->prefix."wputrack404search_search",
        array(
            'request' => get_search_query(),
            'nb_results' => $nb_results
        )
    );
}

/* Add Hook 404
-------------------------- */

add_action( 'template_redirect', 'wputrack404search_404' );
function wputrack404search_404() {
    /* - Log time, request */
    if ( !is_404() || !isset( $_SERVER['REQUEST_URI'] ) ) {
        return;
    }
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix."wputrack404search_404",
        array(
            'request' => $_SERVER['REQUEST_URI']
        )
    );
}

/* Add Admin & Menu */
/* Admin : Page list 404. Sort by nb, name */
/* Admin : Page search. Sort by most requested, nb results, name */

/* ----------------------------------------------------------
  Activation : Create or update tables
---------------------------------------------------------- */

register_activation_hook( __FILE__, 'wputrack404search_activate' );
function wputrack404search_activate() {
    global $wpdb;
    $base_table_name = $wpdb->prefix."wputrack404search_";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    // Create or update table search
    dbDelta( "CREATE TABLE ".$base_table_name."search (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `request` varchar(2083) DEFAULT NULL,
        `nb_results` BIGINT unsigned,
        PRIMARY KEY (`id`)
    );" );
    // Create or update table 404
    dbDelta( "CREATE TABLE ".$base_table_name."404 (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `request` varchar(2083) DEFAULT NULL,
        PRIMARY KEY (`id`)
    );" );
}
