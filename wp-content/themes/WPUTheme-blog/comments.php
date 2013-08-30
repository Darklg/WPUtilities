<?php
include dirname( __FILE__ ) . '/../WPUTheme/z-protect.php';
if ( post_password_required() ) {
    echo '<p>' . __( 'This post requires a password to read the comments', 'wputh' ) . '</p>';
    return;
}

/* Obtaining datas */
include get_stylesheet_directory() . '/tpl/comments/datas.php';

/* Comment section title */

echo '<header>';
echo '<h3>' . wputh_get_comments_title( count( $comments ) ) . '</h3>';
if ( count( $comments ) > 0 && comments_open() ) {
    // We don’t show the link if there are no comments between the title & the comment form
    // echo '<a href="#form-comments">' . __( 'Add yours', 'wputh' ) . '</a>';
}
echo '<header>';

/* List comments */
if ( count( $comments ) > 0 ) {
    echo '<div class="list-comments">';
    wputh_loop_comment( $comments );
    echo '</div>';
}

/* Comments form */
include get_stylesheet_directory() . '/tpl/comments/form.php';

/* Trackbacks */
if ( count( $trackbacks ) > 0 ) {
    echo '<h3>' . __( 'Trackbacks', 'wputh' ) . '</h3>';
    echo '<ul class="list-trackbacks">';
    foreach ( $trackbacks as $trackback ) {
        echo '<li><a href="' . $trackback->comment_author_url . '" target="_blank" rel="external">' . strip_tags( $trackback->comment_content ) . '</a></li>';
    }
    echo '</ul>';
}


