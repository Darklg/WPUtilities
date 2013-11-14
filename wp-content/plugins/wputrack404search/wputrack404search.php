<?php
/*
Plugin Name: WPU Track 404 & Search
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Logs & analyze search queries & 404 Errors
Version: 0.5.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpuTrack404Search extends wpuTrack404SearchUtilities {

    function __construct() {
        global $wpdb;
        $this->base_table_name = $wpdb->prefix."wputrack404search_";

        $this->set_options();
        $this->set_public_hooks();
        if ( is_admin() ) {
            $this->set_admin_hooks();
        }
    }

    function set_options() {
        $this->options = array(
            'id' => 'wputrack404search',
            'level' => 'manage_options'
        );
        load_plugin_textdomain( $this->options['id'], false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
        $this->options['name'] = __( 'Track 404 & Search', $this->options['id'] );
    }

    function set_public_hooks() {
        add_action( 'template_redirect', array( &$this, 'track_search_results' ) );
        add_action( 'template_redirect', array( &$this, 'track_404_errors' ) );
    }

    function set_admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'set_menu_page' ) );
        add_action( 'admin_init', array( &$this, 'page_export_postAction' ) );
    }

    /* ----------------------------------------------------------
      Admin pages
    ---------------------------------------------------------- */

    /* Add Admin & Menu */
    function set_menu_page() {
        add_menu_page( $this->options['name'], $this->options['name'], $this->options['level'], $this->options['id'], array( &$this, 'page_top_results' ) );
        add_submenu_page( $this->options['id'], __( '404 Errors list', $this->options['id'] ), __( '404 Errors list',  $this->options['id'] ), $this->options['level'], $this->options['id'].'-404', array( &$this, 'page_errors_list' ) );
        add_submenu_page( $this->options['id'], __( 'Search list', $this->options['id'] ), __( 'Search list', $this->options['id'] ), $this->options['level'], $this->options['id'].'-search', array( &$this, 'page_search_list' ) );
        add_submenu_page( $this->options['id'], __( 'Export', $this->options['id'] ), __( 'Export', $this->options['id'] ), $this->options['level'], $this->options['id'].'-export', array( &$this, 'page_export' ) );
    }


    /* Page top results
    -------------------------- */

    function page_top_results() {
        global $wpdb;
        $list_most_searched = $wpdb->get_results( "SELECT request, count(request) as nb_requests, nb_results AS total FROM ".$this->base_table_name."search GROUP BY request ORDER BY nb_requests DESC LIMIT 0, 10" );
        $list_common_errors = $wpdb->get_results( "SELECT request, count(request) AS total FROM ".$this->base_table_name."404 GROUP BY request ORDER BY total DESC LIMIT 0, 10;" );

        echo $this->get_wrapper_start( $this->options['name'] . ' - ' . __( 'Top results', $this->options['id'] ) );

        // Most searched requests
        echo '<h3>'.__( 'Most searched requests', $this->options['id'] ).'</h3>';
        if ( empty( $list_most_searched ) ) {
            echo '<p>'.__( 'No results yet', $this->options['id'] ).'</p>';
        }
        else {
            echo $this->get_admin_table( $list_most_searched , array(
                    'columns' => array( __( 'Request', $this->options['id'] ), __( '# of times', $this->options['id'] ), __( '# of results', $this->options['id'] ) )
                ) );
        }

        // Most common errors
        echo '<h3>'.__( 'Most common errors', $this->options['id'] ).'</h3>';
        if ( empty( $list_common_errors ) ) {
            echo '<p>'.__( 'No results yet', $this->options['id'] ).'</p>';
        }
        else {
            echo $this->get_admin_table( $list_common_errors , array(
                    'columns' => array( __( 'Request', $this->options['id'] ), __( '# of times', $this->options['id'] ) )
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

        echo $this->get_wrapper_start( __( 'Search list', $this->options['id'] ) );
        if ( empty( $list ) ) {
            echo '<p>'.__( 'No results yet', $this->options['id'] ).'</p>';
        }
        else {
            echo $this->get_admin_table( $list , array(
                    'columns' => array( 'id', __( 'Date', $this->options['id'] ), __( 'Request', $this->options['id'] ), __( '# of results', $this->options['id'] ) ),
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

        echo $this->get_wrapper_start( __( '404 Errors list', $this->options['id'] ) );
        if ( empty( $list ) ) {
            echo '<p>'.__( 'No results yet', $this->options['id'] ).'</p>';
        }
        else {
            echo $this->get_admin_table( $list , array(
                    'columns' => array( 'id', __( 'Date', $this->options['id'] ), __( 'Request', $this->options['id'] ) ),
                    'pagenum' => $pager['pagenum'],
                    'max_pages' => $pager['max_pages']
                ) );
        }
        echo $this->get_wrapper_end();
    }

    /* Page Export
    -------------------------- */

    function page_export() {
        echo $this->get_wrapper_start( __( 'Export', $this->options['id'] ) );
        echo '<form action="" method="post">';
        echo '<p><button type="submit" name="'.$this->options['id'].'_export" value="404">' . __( 'Export 404 errors list', $this->options['id'] ) . '</button></p>';
        echo '<p><button type="submit" name="'.$this->options['id'].'_export" value="search">' . __( 'Export search list', $this->options['id'] ) . '</button></p>';
        echo '</form>';
        echo $this->get_wrapper_end();

    }

    function page_export_postAction() {

        if ( isset( $_POST[$this->options['id'].'_export'] ) ) {
            $val = $_POST[$this->options['id'].'_export'];
            if ( in_array( $val, array( '404', 'search' ) ) ) {
                global $wpdb;
                $list = $wpdb->get_results( "SELECT * FROM ".$this->base_table_name.$val." ", ARRAY_A );
                $this->export_array_to_csv( $list, $val );
            }
        }
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
        global $wpdb, $wp_query, $paged;
        // Log only first page results
        if ( $paged > 2 ) {
            return;
        }
        // Retrieve number of results
        $nb_results = 0;
        if ( isset( $wp_query->found_posts ) ) {
            $nb_results = $wp_query->found_posts;
        }
        // Insert in database
        $ins = $wpdb->insert(
            $this->base_table_name."search",
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
        // Insert in database
        global $wpdb;
        $wpdb->insert(
            $this->base_table_name."404",
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


/*
Name: WPU Base Plugin Utilities
Version: 1.3
*/
class wpuTrack404SearchUtilities {

    public $version = 1.3;

    /* ----------------------------------------------------------
      Requests
    ---------------------------------------------------------- */

    function get_pager_limit( $perpage, $tablename = '' ) {
        global $wpdb;

        // Ensure good format for table name
        if ( empty( $tablename ) || !preg_match( '/^([A-Za-z0-9_-]+)$/', $tablename ) ) {
            return array(
                'pagenum' => 0,
                'max_pages' => 0,
                'limit' => '',
            );
        }

        // Ensure good format for perpage
        if ( empty( $perpage ) || !is_numeric( $perpage ) ) {
            $perpage = 20;
        }

        // Get number of elements in table
        $elements_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".$tablename );

        // Get max page number
        $max_pages = ceil( $elements_count / $perpage );

        // Obtain Page Number
        $pagenum = ( isset( $_GET['pagenum'] ) && is_numeric( $_GET['pagenum'] ) ? $_GET['pagenum'] : 1 );
        $pagenum = min( $pagenum, $max_pages );

        // Set SQL limit
        $limit = 'LIMIT ' . ( $pagenum * $perpage - $perpage ) . ', '.$perpage;

        return array(
            'pagenum' => $pagenum,
            'max_pages' => $max_pages,
            'limit' => $limit,
        );

    }

    /* ----------------------------------------------------------
      Export
    ---------------------------------------------------------- */

    function export_array_to_csv( $array, $name ) {
        if ( isset( $array[0] ) ) {
            header( 'Content-Type: application/csv' );
            header( 'Content-Disposition: attachment; filename=export-list-'.$name.'-'.date( 'y-m-d' ).'.csv' );
            header( 'Pragma: no-cache' );
            echo implode( ';', array_keys( $array[0] ) )."\n";
            foreach ( $array as $line ) {
                echo implode( ';', $line )."\n";
            }
            die;
        }
    }

    /* ----------------------------------------------------------
      Display
    ---------------------------------------------------------- */

    function get_wrapper_start( $title ) {
        return '<div class="wrap"><div id="icon-options-general" class="icon32"></div><h2 class="title">'.$title.'</h2><br />';
    }

    function get_wrapper_end() {
        return '</div>';
    }

    function get_admin_table( $values, $args = array() ) {
        $pagination = '';
        if ( isset( $args['pagenum'], $args['max_pages'] ) ) {
            $page_links = paginate_links( array(
                    'base' => add_query_arg( 'pagenum', '%#%' ),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $args['max_pages'],
                    'current' => $args['pagenum']
                ) );

            if ( $page_links ) {
                $pagination = '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
            }
        }

        $content = '<table class="widefat">';
        if ( isset( $args['columns'] ) && is_array( $args['columns'] ) && !empty( $args['columns'] ) ) {
            $labels = '<tr><th>' . implode( '</th><th>', $args['columns'] ).'</th></tr>';
            $content .= '<thead>'.$labels.'</thead>';
            $content .= '<tfoot>'.$labels.'</tfoot>';
        }
        $content .= '<tbody>';
        foreach ( $values as $id => $vals ) {
            $content .= '<tr>';
            foreach ( $vals as $val ) {
                $content .= '<td>'.$val.'</td>';
            }
            $content .= '</tr>';
        }
        $content .= '</tbody>';
        $content .= '</table>';
        $content .= $pagination;

        return $content;
    }
}
