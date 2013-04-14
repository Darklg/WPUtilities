<?php
global $post;
$metas = array();

if ( is_single() ) {
    // Meta description
    $meta_description = str_replace( array( "\n", "\t", '   ', '  ' ), ' ', trim( strip_tags( $post->post_content ) ) );
    $metas['description'] = array(
        'content' => substr( $meta_description, 0, 200 ) . ' ...'
    );
}

foreach ( $metas as $name => $values ) {
    echo '<meta name="' . $name . '"';
    if ( isset( $values['content'] ) ) {
        echo ' content="' . $values['content'] . '"';
    }
    echo ' />';
}
