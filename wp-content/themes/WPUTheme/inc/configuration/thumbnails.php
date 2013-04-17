<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

// Default thumbnail size
if ( function_exists( 'set_post_thumbnail_size' ) ) {
    set_post_thumbnail_size( 1200, 1200 );
}

if ( function_exists( 'add_image_size' ) ) {
    add_image_size( 'content-thumb', 300, 9999 );
}
