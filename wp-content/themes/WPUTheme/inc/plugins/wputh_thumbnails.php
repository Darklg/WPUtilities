<?php

add_action( 'init', 'wputh_thumbnails_sizes_filter', 10, 3  );
function wputh_thumbnails_sizes_filter() {
    add_filter( 'wputh_thumbnails_sizes', 'wputh_set_thumbnails_sizes', 10, 3 );
    return $sizes;
}

function wputh_set_thumbnails_sizes( $sizes ) {
    $sizes['loop-small-thumbnail'] = array(
        'width' => 100,
        'height' => 60,
        'crop' => 1
    );
    return $sizes;
}
