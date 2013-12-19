<?php
/*
Plugin Name: WPU Base Plugin
Plugin URI: http://github.com/Darklg/WPUtilities
Description: A framework for a WordPress plugin
Version: 1.6.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpuBasePlugin {

    /* ----------------------------------------------------------
      Options
    ---------------------------------------------------------- */

    function set_options() {
        global $wpdb;
        $this->options = array(
            'id' => 'wpubaseplugin',
            'level' => 'manage_options'
        );
        $this->messages = array();
        $this->data_table = $wpdb->prefix.$this->options['id']."_table";
        load_plugin_textdomain( $this->options['id'], false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
        // Allow translation for plugin name
        $this->options['name'] = $this->__( 'Base Plugin' );
        $this->options['menu_name'] = $this->__( 'Base' );
    }

    /* ----------------------------------------------------------
      Construct
    ---------------------------------------------------------- */

    function __construct() {
        $this->set_options();
    }

    function init() {
        $this->set_global_hooks();
        if ( is_admin() ) {
            $this->set_admin_hooks();
        }
        else {
            $this->set_public_hooks();
        }
    }

    /* ----------------------------------------------------------
      Hooks
    ---------------------------------------------------------- */

    private function set_global_hooks() {

    }


    private function set_public_hooks() {

    }

    private function set_admin_hooks() {
        add_action( 'admin_menu', array( &$this, 'set_admin_menu' ) );
        add_action( 'admin_bar_menu', array( &$this, 'set_adminbar_menu' ), 100 );
        add_action( 'wp_dashboard_setup', array( &$this, 'add_dashboard_widget' ) );
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->options['id'] ) {
            add_action( 'wp_loaded', array( &$this, 'set_admin_page_main_postAction' ) );
            add_action( 'admin_print_styles', array( &$this, 'load_assets_css' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'load_assets_js' ) );
            add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
        }
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
        echo '<p>'.$this->__( 'Content' ).'</p>';

        // Default Form
        echo '<form action="" method="post"><div>';
        wp_nonce_field( 'action-main-form', 'action-main-form-'.$this->options['id'] );
        echo '<button class="button-primary" type="submit">'.$this->__( 'Submit' ).'</button>';
        echo '</div></form>';

        echo $this->get_wrapper_end();
    }

    function set_admin_page_main_postAction() {
        if ( empty( $_POST ) || !isset( $_POST['action-main-form-'.$this->options['id']] ) || !wp_verify_nonce( $_POST['action-main-form-'.$this->options['id']], 'action-main-form' ) ) {
            return;
        }
        $this->messages[] = 'Success !';
    }

    /* Widget Dashboard */

    function add_dashboard_widget() {
        wp_add_dashboard_widget(
            $this->options['id'] . '_dashboard_widget',
            $this->options['name'],
            array( &$this, 'content_dashboard_widget' )
        );
    }

    function content_dashboard_widget() {
        echo '<p>Hello World !</p>';
    }

    /* ----------------------------------------------------------
      Assets & Notices
    ---------------------------------------------------------- */

    /* Display notices */
    private function admin_notices() {
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

    function load_assets_js() {
        wp_enqueue_script(  $this->options['id'] . '_scripts', plugin_dir_url( __FILE__ ) . '/assets/js/script.js' );
    }

    function load_assets_css() {
        wp_register_style( $this->options['id'] . '_style', plugins_url( 'assets/css/style.css', __FILE__ ) );
        wp_enqueue_style( $this->options['id'] . '_style' );
    }

    /* ----------------------------------------------------------
      Activation / Desactivation
    ---------------------------------------------------------- */

    function activate() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // Create or update table search
        dbDelta( "CREATE TABLE ".$this->data_table." (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `value` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;" );
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

    private function get_pager_limit( $perpage, $tablename = '' ) {
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

    private function export_array_to_csv( $array, $name ) {
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
      Utilities : Public
    ---------------------------------------------------------- */

    private function public_message( $message = '' ) {
        get_header();
        echo '<div class="'.$this->options['id'].'-message">'.$message.'</div>';
        get_footer();
        exit();
    }

    /* ----------------------------------------------------------
      Utilities : Translate
    ---------------------------------------------------------- */

    function __( $string ) {
        return __( $string, $this->options['id'] );
    }

    /* ----------------------------------------------------------
      Utilities : Display
    ---------------------------------------------------------- */

    private function get_wrapper_start( $title ) {
        return '<div class="wrap"><div id="icon-options-general" class="icon32"></div><h2 class="title">'.$title.'</h2><br />';
    }

    private function get_wrapper_end() {
        return '</div>';
    }

    private function get_admin_table( $values, $args = array() ) {
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

$wpuBasePlugin = false;
add_action( 'init', 'init_wpuBasePlugin' );
function init_wpuBasePlugin() {
    global $wpuBasePlugin;
    $wpuBasePlugin = new wpuBasePlugin();
    $wpuBasePlugin->init();
}

/* Limited launch for activation/deactivation hook */
$temp_wpuBasePlugin = new wpuBasePlugin();
register_activation_hook( __FILE__, array( &$temp_wpuBasePlugin, 'activate' ) );
register_deactivation_hook( __FILE__, array( &$temp_wpuBasePlugin, 'deactivate' ) );
