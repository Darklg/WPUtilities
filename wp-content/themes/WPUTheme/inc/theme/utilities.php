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

/* ----------------------------------------------------------
  Comments title
---------------------------------------------------------- */

function wputh_get_comments_title( $comments = array(), $zero = false, $one = false, $more = false, $closed = false ) {
    global $post;
    $return = '';
    if ( $zero === false ) {
        $zero = __( '<strong>no</strong> comments', 'wputh' );
    }
    if ( $one === false ) {
        $one = __( '<strong>1</strong> comment', 'wputh' );
    }
    if ( $more === false ) {
        $more = __( '<strong>%s</strong> comments', 'wputh' );
    }
    if ( $closed === false ) {
        $closed = __( 'Comments are closed', 'wputh' );
    }
    if ( !comments_open() ) {
        $return = $closed;
    }
    else {
        $count_comments = count( $comments );
        switch ( $count_comments ) {
        case 0:
            $return = $zero;
            break;
        case 1:
            $return = $one;
            break;
        default :
            $return = sprintf( $more, $count_comments );
        }
    }

    return $return;
}
