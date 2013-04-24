<?php

/* ----------------------------------------------------------
   Get the loop : returns a main loop
   ------------------------------------------------------- */

function get_the_loop( $params = array() ) {
    global $post, $wp_query, $wpdb;

    /* Get params */
    $default_params = array(
        'loop' => 'loop-small'
    );

    if ( !is_array( $params ) ) {
        $params = array( $params );
    };

    $parameters = array_merge( $default_params, $params );

    /* Start the loop */
    ob_start();
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            get_template_part( $parameters['loop'] );
        }
        include TEMPLATEPATH . '/tpl/paginate.php';
    }
    else {
        echo '<p>' . __( 'Sorry, no search results for this query.', 'wputh' ) . '</p>';
    }
    wp_reset_query();

    /* Returns captured content */
    $content = ob_get_clean();
    return $content;
}
