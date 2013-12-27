<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

$shown_title = wp_title( "", false );
if ( function_exists( 'wputh_get_displayed_title' ) ) {
    $shown_title = wputh_get_displayed_title();
}

echo '<main class="main-content" role="main" id="main">';
echo '<h1>'.$shown_title.'</h1>';
if ( is_year() || is_month() || is_day() ) {
    include get_template_directory() . '/tpl/archive/dates-navigation.php';
}
echo get_the_loop();
echo '</main>';
get_sidebar();
get_footer();
