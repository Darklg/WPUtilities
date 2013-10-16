<?php
include dirname( __FILE__ ) . '/../WPUTheme/z-protect.php';
get_header();
include get_stylesheet_directory() . '/tpl/account/user-toolbar.php';
echo '<div class="main">';
echo get_the_loop();
echo '</div>';
get_footer();
