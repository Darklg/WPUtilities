<?php
get_header();

query_posts( );
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        get_template_part('loop', 'small');
    }
}
wp_reset_query();

get_footer();
