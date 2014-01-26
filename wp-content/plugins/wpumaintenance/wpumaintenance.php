<?php
/*
Plugin Name: WPU Maintenance page
Description: Adds a maintenance page for non logged-in users
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Contributors: @ScreenFeedFr
*/

class WPUWaitingPage {
    function __construct() {
        load_plugin_textdomain( 'wpumaintenance', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

        // Dont launch if in admin, or if user is logged in
        global $pagenow;
        if ( !is_admin() && !is_user_logged_in() && $pagenow != 'wp-login.php' ) {
            $this->launch_maintenance();
        }
    }

    function launch_maintenance() {
        // Try to include a HTML file
        $maintenanceFilenames = array( 'maintenance.html', 'index.html' );
        foreach ( $maintenanceFilenames as $filename ) {
            $filepath = ABSPATH . '/' . $filename;
            if ( file_exists( $filepath ) ) {
                include $filepath;
                die;
            }
        }
        // Or include the default maintenance page
        include dirname( __FILE__ ) . '/includes/maintenance.php';
        die;
    }
}

add_action( 'init', 'init_wpuwaitingpage' );
function init_wpuwaitingpage() {
    $WPUWaitingPage = new WPUWaitingPage();
}
