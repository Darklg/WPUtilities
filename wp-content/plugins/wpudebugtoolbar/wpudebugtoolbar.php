<?php
/*
Plugin Name: WP Utilities Debug toolbar
Description: Debug toolbar
Version: 0.4
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if ( !is_admin() ) {
    add_action( 'wp_head', 'wputh_debug_display_style' );
    add_action( 'shutdown', 'wputh_debug_launch_bar', 999 );
}

/* ----------------------------------------------------------
  Style
---------------------------------------------------------- */

function wputh_debug_display_style() { ?>
<style>
body {
    padding-bottom: 40px;
}

.wputh-debug-toolbar {
    z-index: 9999;
    position: fixed;
    right: 0;
    bottom: 0;
    left: 0;
    padding: 3px 5px;
    border-top: 1px solid #999;
    text-align: center;
    font-size: 11px;
    line-height: 14px;
    background-color: #ccc;
}

.wputh-debug-toolbar em {
    color: #999;
}
</style>
<?php
}

/* ----------------------------------------------------------
  Bar
---------------------------------------------------------- */

function wputh_debug_launch_bar() {
    global $template, $pagenow;
    if ( $pagenow == 'wp-login.php' ) {
        die;
    }
    echo '<div class="wputh-debug-toolbar">';
    // Theme
    echo 'Theme : <strong>' . wp_get_theme().'</strong>';
    echo ' <em>&bull;</em> ';
    // Template
    echo 'File : <strong>' . basename( $template ).'</strong>';
    echo ' <em>&bull;</em> ';
    // Current language
    echo 'Lang : <strong>' . get_bloginfo( 'language' ).'</strong>';
    echo ' <em>&bull;</em> ';
    // Memory used
    echo 'Memory : <strong>'.round( memory_get_peak_usage()/( 1024*1024 ), 3 ). '</strong> mb';
    echo ' <em>&bull;</em> ';
    // Queries
    echo 'Queries : <strong>' .get_num_queries().'</strong>';
    echo ' <em>&bull;</em> ';
    // Execution time
    echo 'Time : <strong>' . timer_stop( 0 ).'</strong> sec';
    echo '</div>';
}
