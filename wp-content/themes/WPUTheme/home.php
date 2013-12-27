<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

echo '<main class="main-content" role="main" id="main">';
echo get_the_loop();
echo '</main>';
get_sidebar();
get_footer();
