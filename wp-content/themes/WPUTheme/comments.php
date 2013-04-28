<?php include dirname( __FILE__ ) . '/z-protect.php';

if ( post_password_required() ) {
    echo '<p>' . __( 'This post requires a password to read the comments', 'wputh' ) . '</p>';
    return;
}

/* Retrieving comments */

$get_comments = get_comments( array(
        'post_id' => get_the_ID(),
        'comment_approved' => 1
    ) );

$comments = array();
$trackbacks = array();

/* Comment template */
function wputh_loop_comment( $comments, $parent = 0, $comment_depth = 0 ) {
    foreach ( $comments as $comment ) {
        if ( $comment->comment_parent == $parent ) {
            include TEMPLATEPATH . '/loop-comment.php';
            wputh_loop_comment( $comments, $comment->comment_ID, $comment_depth+1 );
        }
    }
}

/* Sorting comments */

foreach ( $get_comments as $comment ) {
    if ( empty( $comment->comment_type ) ) {
        $comments[] = $comment;
    }
    else {
        $trackbacks[] = $comment;
    }
}

/* Comment section title */

echo '<header>';
echo '<h3>' . wputh_get_comments_title( $comments ) . '</h3>';
if ( count( $comments ) > 0 && comments_open() ) {
    // We don’t show the link if there are no comments between the title & the comment form
    // echo '<a href="#form-comments">' . __( 'Add yours', 'wputh' ) . '</a>';
}
echo '<header>';

/* List comments */
if ( count( $comments ) > 0 ) {
    wputh_loop_comment( $comments );
}

/* Comments form */

/* Trackbacks */
