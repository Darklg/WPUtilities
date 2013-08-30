<?php
$att_image = '';
$image_pager = '';
$isImage = wp_attachment_is_image( $post->ID );
// Image URL
$att_image = wp_get_attachment_image_src( $post->ID, "medium" );

// Previous & next attachment
$args = array(
    'post_type' => 'attachment',
    'posts_per_page' => -1,
    'post_status' =>'any',
    'post_parent' => $post->post_parent
);

$attachments = get_posts( $args );
$current_attachment = -1;
if ( !empty($attachments ) &&  count($attachments) > 2) {
    $nb_attachments = count($attachments);

    // Searching for attachment index
    foreach ( $attachments as $i => $attachment ) {
        if ($attachment->ID == $post->ID) {
            $current_attachment = $i;
        }
    }

    if($current_attachment != -1){

        // Setting Previous & Next
        $previous_attachment = $current_attachment - 1;
        $next_attachment = $current_attachment + 1;
        if ($previous_attachment < 0) {
            $previous_attachment = $nb_attachments-1;
        }
        if ($next_attachment >= $nb_attachments) {
            $next_attachment = 0;
        }

        // Previous
        $previous = $attachments[$previous_attachment];
        $previous_content = '';
        if (wp_attachment_is_image( $previous->ID )) {
            $previous_content = wp_get_attachment_image( $previous->ID, "thumbnail" );
        }
        else {
            $previous_content = apply_filters( 'the_title', $previous->post_title );
        }

        // Next
        $next = $attachments[$next_attachment];
        $next_content = '';
        if (wp_attachment_is_image( $next->ID )) {
            $next_content = wp_get_attachment_image( $next->ID, "thumbnail" );
        }
        else {
            $next_content = apply_filters( 'the_title', $next->post_title );
        }
    }
}