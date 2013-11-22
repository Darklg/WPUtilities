<?php
/*
Plugin Name: WPU Download links
Plugin URI: http://github.com/Darklg/WPUtilities
Description: A Generator for download links
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpuDownloadLinks {

    /* ----------------------------------------------------------
      Options
    ---------------------------------------------------------- */

    function set_options() {
        global $wpdb;
        $this->options = array(
            'id' => 'wpudownloadlinks',
            'level' => 'manage_options'
        );
        $this->messages = array();
        $this->data_table = $wpdb->prefix.$this->options['id']."_table";
        load_plugin_textdomain( $this->options['id'], false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

        // Allow translation for plugin name
        $this->options['name'] = __( 'Download links', $this->options['id'] );
        $this->options['menu_name'] = __( 'Download links', $this->options['id'] );
    }

    /* ----------------------------------------------------------
      Construct
    ---------------------------------------------------------- */

    function __construct() {
        $this->set_options();
        $this->set_public_hooks();
        if ( is_admin() ) {
            $this->set_admin_hooks();
        }
    }

    /* ----------------------------------------------------------
      Hooks
    ---------------------------------------------------------- */

    function set_public_hooks() {
        add_action( 'template_redirect', array( &$this, 'intercept_download_link' ) );
    }

    function set_admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'set_admin_menu' ) );
        add_action( 'admin_bar_menu', array( &$this, 'set_adminbar_menu' ), 100 );
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->options['id'] ) {
            add_action( 'wp_loaded', array( &$this, 'set_admin_page_main_postAction' ) );
            add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
        }
    }

    /* ----------------------------------------------------------
      Public
    ---------------------------------------------------------- */

    function intercept_download_link() {

        $max_downloads = 5;

        global $wpdb;

        // Check for invalid code
        if ( !isset( $_GET['wpu-id-code'] ) || !preg_match( '/^([0-9]+)-([a-z0-9]+)$/', $_GET['wpu-id-code'] ) ) {
            return;
        }

        $code_details = explode( '-', $_GET['wpu-id-code'] );

        // Obtain code details
        $code = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM ".$this->data_table . " WHERE id = %s AND code = %s",
                $code_details[0], $code_details[1]
            )
        );

        // If invalid code : exit
        if ( !isset( $code->id ) ) {
            return $this->public_message( __( 'Error: This download code is invalid.',  $this->options['id']  ) );
        }

        // If download number outdated : exit
        if ( $code->downloads >= $max_downloads ) {
            return $this->public_message( __( 'Error: The maximum number of downloads is outdated.',  $this->options['id']  ) );
        }

        // If file doesnt exists : exit
        if ( !file_exists( $code->path ) ) {
            return $this->public_message( __( 'Error: This file doesn\'t exists.',  $this->options['id']  ) );
        }

        // Update download number
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE ".$this->data_table . " SET downloads=downloads+1 WHERE id='%s'",
                $code->id
            )
        );

        // Extract file infos
        $path_info = explode( '/', $code->path );
        $filename = array_pop( $path_info );

        // Send headers & file
        header( "Content-Disposition: attachment; filename=" . urlencode( $filename ) );
        header( "Content-Type: application/force-download" );
        header( "Content-Type: application/octet-stream" );
        header( "Content-Type: application/download" );
        header( "Content-Description: File Transfer" );
        header( "Content-Length: " . filesize( $code->path ) );
        flush(); // this doesn't really matter.

        $fp = fopen( $code->path, "r" );
        while ( !feof( $fp ) ) {
            echo fread( $fp, 65536 );
            flush(); // this is essential for large downloads
        }
        fclose( $fp );
        exit();
    }

    function public_message( $message = '' ) {
        get_header();
        echo '<div class="'.$this->options['id'].'-message">'.$message.'</div>';
        get_footer();
        exit();
    }

    /* ----------------------------------------------------------
      Admin
    ---------------------------------------------------------- */

    function set_admin_menu() {
        add_menu_page(
            $this->options['name'],
            $this->options['menu_name'],
            $this->options['level'],
            $this->options['id'],
            array( &$this, 'set_admin_page_main' )
        );
        add_submenu_page(
            $this->options['id'],
            'Links list',
            'Links list',
            $this->options['level'],
            $this->options['id'] . '-list',
            array( &$this, 'set_admin_page_list' )
        );

    }

    function set_adminbar_menu( $admin_bar ) {
        $admin_bar->add_menu( array(
                'id' => $this->options['id'],
                'title' => $this->options['menu_name'],
                'href' => admin_url( 'admin.php?page='.$this->options['id'] ),
                'meta' => array(
                    'title' => $this->options['menu_name'],
                ),
            ) );
    }

    function set_admin_page_main() {
        echo $this->get_wrapper_start( $this->options['name'] );

        // Content
        echo '<p>'.__( 'Content', $this->options['id'] ).'</p>';

        // Default Form
        echo '<form action="" method="post"><div>';
        wp_nonce_field( 'action-main-form', 'action-main-form-'.$this->options['id'] );
        echo '<button class="button-primary" type="submit">'.__( 'Submit', $this->options['id'] ).'</button>';
        echo '</div></form>';

        echo $this->get_wrapper_end();
    }

    function set_admin_page_main_postAction() {
        if ( empty( $_POST ) || !isset( $_POST['action-main-form-'.$this->options['id']] ) || !wp_verify_nonce( $_POST['action-main-form-'.$this->options['id']], 'action-main-form' ) ) {
            return;
        }
        $this->messages[] = 'Success !';
    }

    function set_admin_page_list() {
        global $wpdb;
        echo $this->get_wrapper_start( 'Links list' );

        $pager = $this->get_pager_limit( 20, $this->data_table );
        $list = $wpdb->get_results( "SELECT id, date, infos, downloads FROM ".$this->data_table . ' '. $pager['limit'] );

        if ( empty( $list ) ) {
            echo '<p>'.__( 'No results yet', $this->options['id'] ).'</p>';
        }
        else {
            echo $this->get_admin_table( $list , array(
                    'columns' => array( 'ID', 'Date', 'Infos', 'Downloads' ),
                    'pagenum' => $pager['pagenum'],
                    'max_pages' => $pager['max_pages']
                ) );
        }

        echo $this->get_wrapper_end();
    }

    /* ----------------------------------------------------------
      Assets & Notices
    ---------------------------------------------------------- */

    /* Display notices */
    function admin_notices() {
        $return = '';
        if ( !empty( $this->messages ) ) {
            foreach ( $this->messages as $message ) {
                $return .= '<div class="updated"><p>'.$message.'</p></div>';
            }
        }
        // Empty messages
        $this->messages = array();
        echo $return;
    }

    /* ----------------------------------------------------------
      Activation / Desactivation
    ---------------------------------------------------------- */

    function activate() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // Create or update table search
        dbDelta( "CREATE TABLE ".$this->data_table." (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `code` varchar(50) DEFAULT NULL,
            `infos` varchar(200) DEFAULT NULL,
            `path` varchar(200) DEFAULT NULL,
            `downloads` int(11) unsigned NOT NULL,
            PRIMARY KEY (`id`)
        );" );
    }

    function deactivate() {
    }

    function uninstall() {
        global $wpdb;
        $wpdb->query( 'DROP TABLE ' . $this->data_table );
    }


    /* ----------------------------------------------------------
      Utilities : Requests
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
      Utilities : Export
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
      Utilities : Display
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

$wpuDownloadLinks = new wpuDownloadLinks();

/* External activation hook */

register_activation_hook( __FILE__, array( &$wpuDownloadLinks, 'activate' ) );
register_deactivation_hook( __FILE__, array( &$wpuDownloadLinks, 'deactivate' ) );
