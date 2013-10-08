<?php
/**
 * Utilities
 *
 * @package default
 */


/**
 * Get the loop : returns a main loop
 *
 * @param unknown $params (optional)
 * @return unknown
 */
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
        echo '<div class="list-loops">';
        while ( have_posts() ) {
            the_post();
            get_template_part( $parameters['loop'] );
        }
        echo '</div>';
        include get_template_directory() . '/tpl/paginate.php';
    }
    else {
        echo '<p>' . __( 'Sorry, no search results for this query.', 'wputh' ) . '</p>';
    }
    wp_reset_query();

    /* Returns captured content */
    $content = ob_get_clean();
    return $content;
}


/**
 * Get comments title
 *
 * @param unknown $count_comments
 * @param unknown $zero           (optional)
 * @param unknown $one            (optional)
 * @param unknown $more           (optional)
 * @param unknown $closed         (optional)
 * @return unknown
 */
function wputh_get_comments_title( $count_comments, $zero = false, $one = false, $more = false, $closed = false ) {
    global $post;
    $return = '';
    if ( is_array( $count_comments ) ) {
        $count_comments = count( $count_comments );
    }
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


/**
 * Get comment author name with link
 *
 * @param unknown $comment
 * @return unknown
 */
function wputh_get_comment_author_name_link( $comment ) {
    $return = '';
    $comment_author_url = '';
    if ( !empty( $comment->comment_author_url ) ) {
        $comment_author_url = $comment->comment_author_url;
    }
    if ( empty( $comment_author_url ) && $comment->user_id != 0 ) {
        $user_info = get_user_by( 'id', $comment->user_id );
        $comment_author_url = $user_info->user_url;
    }

    $return = $comment->comment_author;

    if ( !empty( $comment_author_url ) ) {
        $return = '<a href="' . $comment_author_url . '" target="_blank">' . $return . '</a>';
    }

    return '<strong class="comment_author_url">' . $return . '</strong>';
}


/**
 * Get Thumbnail URL
 *
 * @param string  $format
 * @return string
 */
function wputh_get_thumbnail_url( $format ) {
    global $post;
    $returnUrl = get_template_directory_uri().'/images/thumbnails/' . $format . '.jpg';
    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $format );
    if ( isset( $image[0] ) ) {
        $returnUrl = $image[0];
    }
    return $returnUrl;
}


/**
 * Get attachments - images
 *
 * @param int     $postID
 * @param string  $format (optional)
 * @return array
 */
function wputh_get_attachments_images( $postID, $format='medium' ) {
    $images = array();
    $attachments = get_posts( array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'post_status' =>'any',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_parent' => $postID
        ) );
    foreach ( $attachments as $attachment ) {
        $image = wp_get_attachment_image_src( $attachment->ID , $format );
        if ( isset( $image[0] ) ) {
            $images[] = $image;
        }
    }
    return $images;
}
