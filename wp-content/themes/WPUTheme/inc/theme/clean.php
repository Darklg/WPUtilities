<?php

/* ----------------------------------------------------------
  Clean head
---------------------------------------------------------- */

add_action( 'init', 'wpu_clean_head' );
function wpu_clean_head() {
    global $wp_widget_factory;
    // Hardcoded recent comments style
    if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
        remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
    }
    // Meta generator
    remove_action( 'wp_head', 'wp_generator' );
}

/* ----------------------------------------------------------
  Prevent bad formed link
---------------------------------------------------------- */

add_action( 'the_content', 'wputh_bad_formed_links' );
function wputh_bad_formed_links( $content ) {
    $badform = array();
    $goodform = array();

    $badform[] = 'href="www.';
    $goodform[] = 'href="http://www.';

    $badform[] = 'href="http//';
    $goodform[] = 'href="http://';

    $badform[] = 'href=" http://';
    $goodform[] = 'href="http://';

    $content = str_replace( $badform, $goodform, $content );
    return $content;
}
