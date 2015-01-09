<?php
/*
Plugin Name: WP Utilities Debug toolbar
Description: Display a debug toolbar for developers.
Version: 0.5
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

if ( !is_admin() ) {
    add_action( 'wp_head', 'wputh_debug_display_style' );
    add_action( 'wp_footer', 'wputh_debug_launch_bar', 999 );
    add_action( 'wp_footer', 'wputh_debug_display_script', 1000 );
}

function wputh_debug_display_script() { ?>
<script>
(function(){
    var $ = function(id){return document.getElementById(id);},
        toolbar = $('wputh-debug-toolbar');
    $('wputh-debug-display-queries').onclick=function(){toolbar.setAttribute('data-show-queries','1');}
    $('wputh-debug-hide-queries').onclick=function(){toolbar.setAttribute('data-show-queries','');}
}());
</script>
<?php
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

.wputh-debug-toolbar-content {
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
}

.wputh-debug-toolbar em {
    color: #999;
}

#wputh-debug-hide-queries,
#wputh-debug-display-queries {
    cursor: pointer;
    border-bottom: 1px solid;
}

*[data-show-queries="1"] #wputh-debug-hide-queries,
#wputh-debug-display-queries {
    display: inline;
}

*[data-show-queries="1"] #wputh-debug-display-queries,
#wputh-debug-hide-queries {
    display: none;
}

#wputh-debug-queries {
    display: none;
    margin: 0 auto 5px;
    max-height: 200px;
    max-width: 90%;
    overflow: auto;
    text-align: left;
}

*[data-show-queries="1"] #wputh-debug-queries{
    display: block;
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
    echo '<div data-show-queries="" id="wputh-debug-toolbar" class="wputh-debug-toolbar">';

    if(SAVEQUERIES && current_user_can('administrator')){
        global $wpdb;
        echo '<div id="wputh-debug-queries">';
            echo "<pre>";
            print_r($wpdb->queries);
            echo "</pre>";
        echo '</div>';
    }

    echo '<div class="wputh-debug-toolbar-content">';
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

    if(SAVEQUERIES){
        echo ' <em>&bull;</em> ';
        echo '<span id="wputh-debug-display-queries">Display queries</span>';
        echo '<span id="wputh-debug-hide-queries">Hide queries</span>';
    }
    echo '</div>';

    echo '</div>';
}
