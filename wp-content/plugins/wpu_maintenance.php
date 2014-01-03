<?php
/*
Plugin Name: WPU Maintenance page
Description: Adds a maintenance page for non logged-in users
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUWaitingPage {
    function __construct() {
        // Kill if in admin, or if user is logged in
        if ( is_admin() || is_user_logged_in() ) {
            return;
        }
        // Try to include a HTML file
        $maintenanceFilenames = array( 'index.html', 'maintenance.html' );
        foreach ( $maintenanceFilenames as $filename ) {
            $filepath = ABSPATH . '/' . $filename;
            if ( file_exists( $filepath ) ) {
                include $filepath;
                die;
            }
        }
        // Display default page
        echo '<!DOCTYPE HTML><html' . get_bloginfo( 'language' ) . '>';
        echo '<head><meta charset="UTF-8" /><title>' . get_bloginfo( 'name' ) . '</title></head>';
        echo '<body><h1>' . get_bloginfo( 'name' ) . '</h1><p><strong>' . get_bloginfo( 'name' ) . '</strong> ' . __( 'is in maintenance mode', 'wputh' ) . '</p></body>';
        echo '</html>';
        die;
    }
}

add_action( 'init', 'init_wpuwaitingpage' );
function init_wpuwaitingpage() {
    $WPUWaitingPage = new WPUWaitingPage();
}
