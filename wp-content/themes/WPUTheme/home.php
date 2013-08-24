<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

echo '<div class="main">';
echo get_the_loop(array(
    'loop' => 'loop-small-thumbnail'
));
echo '</div>';
get_sidebar();
get_footer();
