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

/* ----------------------------------------------------------
  Prevent invalid characters in file name
---------------------------------------------------------- */

add_filter( 'sanitize_file_name', 'remove_accents' );
add_filter( 'sanitize_file_name', 'strtolower' );

/* ----------------------------------------------------------
  Set media select to uploaded : http://wordpress.stackexchange.com/a/76213
---------------------------------------------------------- */

add_action( 'admin_footer-post-new.php', 'wputh_set_media_select_uploaded' );
add_action( 'admin_footer-post.php', 'wputh_set_media_select_uploaded' );

function wputh_set_media_select_uploaded() { ?><script>
jQuery(function($) {
    var called = 0;
    $('#wpcontent').ajaxStop(function() {
        if (0 === called) {
            $('[value="uploaded"]').attr('selected', true).parent().trigger('change');
            called = 1;
        }
    });
});
</script><?php }
