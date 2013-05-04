<?php
/* Template Name: Gallery */

include dirname( __FILE__ ) . '/../z-protect.php';
get_header();
the_post();
?>
<div class="main">
<article>
    <h1><?php the_title(); ?></h1>
<?php
the_content();
$attachments = get_posts( array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' =>'any',
        'post_parent' => $post->ID )
);
if ( !empty( $attachments ) ) {
    echo '<ul class="gallery-list subfloat sf_150_20">';
    foreach ( $attachments as $attachment ) {
        $attachment_url = get_attachment_link( $attachment->ID );
        $attachment_thumb = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
        echo '<li><a style="background-image:url(' . $attachment_thumb[0] . ');" href="' . $attachment_url . '">';
        echo '<strong class="trans-opa">' . apply_filters( 'the_title' , $attachment->post_title ) . '</strong>';
        echo '</a></li>';
    }
    echo '</ul>';
}

?>


</article>
</div>
<?php
get_sidebar();
get_footer();
