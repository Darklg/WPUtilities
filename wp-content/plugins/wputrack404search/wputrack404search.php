<?php
/*
Plugin Name: WPU Track 404 & Search
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Logs & analyze search queries & 404 Errors
Version: 0.3
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if ( !class_exists( 'wpuBasePluginUtilities' ) ) {
    include dirname( __FILE__ ).'/inc/wpubasepluginutilities.php';
}

class wpuTrack404Search extends wpuBasePluginUtilities {

    function __construct() {
        global $wpdb;
        $this->base_table_name = $wpdb->prefix."wputrack404search_";

        $this->set_public_hooks();
        if ( is_admin() ) {
            $this->set_admin_hooks();
        }
    }

    function set_public_hooks() {
        add_action( 'template_redirect', array( &$this, 'track_search_results' ) );
        add_action( 'template_redirect', array( &$this, 'track_404_errors' ) );
    }

    function set_admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'set_menu_page' ) );
    }

    /* ----------------------------------------------------------
      Admin pages
    ---------------------------------------------------------- */

    /* Add Admin & Menu */
    function set_menu_page() {
        add_menu_page( 'Track 404 & Search', 'Track 404 & Search', 'manage_options', 'wputrack404search', array( &$this, 'page_top_results' ) );
        add_submenu_page( 'wputrack404search', '404 Errors list', '404 Errors list', 'manage_options', 'wputrack404search-404', array( &$this, 'page_errors_list' ) );
        add_submenu_page( 'wputrack404search', 'Search list', 'Search list', 'manage_options', 'wputrack404search-search', array( &$this, 'page_search_list' ) );
    }


    /* Page top results
    -------------------------- */

    function page_top_results() {
        global $wpdb;
        echo $this->get_wrapper_start( 'Top results' );
        echo '<h3>Most searched requests</h3>';
        $list_most_searched = $wpdb->get_results( "SELECT request, count(request), nb_results AS total FROM ".$this->base_table_name."search GROUP BY request ORDER BY total DESC LIMIT 0, 10" );
        if ( empty( $list_most_searched ) ) {
            echo '<p>No results yet</p>';
        }
        else {
            echo $this->get_admin_table( $list_most_searched , array(
                    'columns' => array( 'Request', '# of times', '# of results' )
                ) );
        }
        echo '<h3>Most common errors</h3>';
        $list_common_errors = $wpdb->get_results( "SELECT request, count(request) AS total FROM ".$this->base_table_name."404 GROUP BY request ORDER BY total DESC LIMIT 0, 10;" );
        if ( empty( $list_common_errors ) ) {
            echo '<p>No results yet</p>';
        }
        else {
            echo $this->get_admin_table( $list_common_errors , array(
                    'columns' => array( 'Request', '# of times' )
                ) );
        }
        echo $this->get_wrapper_end();
    }

    /* Page List search
    -------------------------- */

    /* Admin : Page search. Sort by most requested, nb results, name */

    function page_search_list() {
        global $wpdb;

        $pager = $this->get_pager_limit( 20, $this->base_table_name."search" );
        $list = $wpdb->get_results( "SELECT id, date, request, nb_results FROM ".$this->base_table_name."search ". $pager['limit'] );

        echo $this->get_wrapper_start( 'Search list' );
        if ( empty( $list ) ) {
            echo '<p>No results yet</p>';
        }
        else {
            echo $this->get_admin_table( $list , array(
                    'columns' => array( 'id', 'Date', 'Request', '# of results' ),
                    'pagenum' => $pager['pagenum'],
                    'max_pages' => $pager['max_pages']
                ) );
        }
        echo $this->get_wrapper_end();
    }

    /* Page List errors
    -------------------------- */

    /* Admin : Page list 404. Sort by nb, name */

    function page_errors_list() {
        global $wpdb;

        $pager = $this->get_pager_limit( 20, $this->base_table_name."404" );
        $list = $wpdb->get_results( "SELECT id, date, request FROM ".$this->base_table_name."404 ". $pager['limit'] );

        echo $this->get_wrapper_start( '404 Errors list' );
        if ( empty( $list ) ) {
            echo '<p>No results yet</p>';
        }
        else {
            echo $this->get_admin_table( $list , array(
                    'columns' => array( 'id', 'Date', 'Request' ),
                    'pagenum' => $pager['pagenum'],
                    'max_pages' => $pager['max_pages']
                ) );
        }
        echo $this->get_wrapper_end();
    }


    /* ----------------------------------------------------------
      Tracking Hooks
    ---------------------------------------------------------- */

    /* Add Hook search
    -------------------------- */

    function track_search_results() {
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

    function track_404_errors() {
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

    /* ----------------------------------------------------------
      Activation : Create or update tables
    ---------------------------------------------------------- */

    function activate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // Create or update table search
        dbDelta( "CREATE TABLE ".$this->base_table_name."search (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `request` varchar(2083) DEFAULT NULL,
            `nb_results` BIGINT unsigned,
            PRIMARY KEY (`id`)
        );" );
        // Create or update table 404
        dbDelta( "CREATE TABLE ".$this->base_table_name."404 (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `request` varchar(2083) DEFAULT NULL,
            PRIMARY KEY (`id`)
        );" );
    }

}

$wpuTrack404Search = new wpuTrack404Search();

/* External activation hook */

register_activation_hook( __FILE__, array( &$wpuTrack404Search, 'activate' ) );
