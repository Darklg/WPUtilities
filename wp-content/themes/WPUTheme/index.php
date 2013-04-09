<?php
include dirname(__FILE__).'/z-protect.php';
get_header();

echo '<div class="main">';
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        get_template_part('loop', 'small');
    }
}
wp_reset_query();
echo '</div>';
get_sidebar();
get_footer();
