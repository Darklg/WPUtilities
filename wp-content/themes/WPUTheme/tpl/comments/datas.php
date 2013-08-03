<?php include dirname( __FILE__ ) . '/../../z-protect.php';

/* Retrieving comments */

$get_comments = get_comments( array(
        'post_id' => get_the_ID(),
        'status' => 'approve',
        'order' => 'ASC',
    ) );

$comments = array();
$trackbacks = array();

/* Comment template */

function wputh_loop_comment( $comments, $parent = 0, $comment_depth = 0 ) {
    foreach ( $comments as $comment ) {
        if ( $comment->comment_parent == $parent ) {
            include get_template_directory() . '/loop-comment.php';
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