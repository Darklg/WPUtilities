<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
the_post();

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
if ( !empty( $attachments ) &&  count( $attachments ) > 2 ) {
    $nb_attachments = count( $attachments );

    // Searching for attachment index
    foreach ( $attachments as $i => $attachment ) {
        if ( $attachment->ID == $post->ID ) {
            $current_attachment = $i;
        }
    }

    if ( $current_attachment != -1 ) {

        // Setting Previous & Next
        $previous_attachment = $current_attachment - 1;
        $next_attachment = $current_attachment + 1;
        if ( $previous_attachment < 0 ) {
            $previous_attachment = $nb_attachments-1;
        }
        if ( $next_attachment >= $nb_attachments ) {
            $next_attachment = 0;
        }

        // Previous
        $previous = $attachments[$previous_attachment];
        $previous_content = '';
        if ( wp_attachment_is_image( $previous->ID ) ) {
            $previous_content = wp_get_attachment_image( $previous->ID, "thumbnail" );
        }
        else {
            $previous_content = apply_filters( 'the_title', $previous->post_title );
        }

        // Next
        $next = $attachments[$next_attachment];
        $next_content = '';
        if ( wp_attachment_is_image( $next->ID ) ) {
            $next_content = wp_get_attachment_image( $next->ID, "thumbnail" );
        }
        else {
            $next_content = apply_filters( 'the_title', $next->post_title );
        }
    }
}

?>
<main class="main-content" role="main" id="main">
<article class="loop">
    <h1><?php the_title(); ?></h1>
    <div>
    <?php if ( $isImage ) : ?>
    <p><img src="<?php echo $att_image[0];?>" alt="<?php $post->post_excerpt; ?>" /></p>
    <?php else : ?>
    <a href="<?php echo wp_get_attachment_url( $post->ID ) ?>" title="<?php echo esc_html( get_the_title( $post->ID ), 1 ) ?>" rel="attachment">
        <?php echo basename( $post->guid ) ?>
    </a>
    <?php endif; ?>
    <?php
    // Display attachment navigation
    if ( $current_attachment != -1 ) {
        echo '<div>';
        echo '<a class="prev" href="'.get_attachment_link( $previous->ID ).'">'.$previous_content.'</a>';
        echo '<a class="next" href="'.get_attachment_link( $next->ID ).'">'.$next_content.'</a>';
        echo '</div>';
    }
    ?>
    </div>
</article>
</main>
<?php
get_sidebar();
get_footer();
