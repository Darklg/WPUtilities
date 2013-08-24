<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

echo '<div class="main-content">';
echo get_the_loop(array(
    'loop' => 'loop-small-thumbnail'
));
echo '</div>';
get_footer();
