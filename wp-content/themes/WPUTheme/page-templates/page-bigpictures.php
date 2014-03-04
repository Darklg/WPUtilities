<?php
/* Template Name: Big Pictures */
include dirname( __FILE__ ) . '/../z-protect.php';

get_header();
the_post();
?>
<div class="main-content">
<article>
    <h1><?php the_title(); ?></h1>
<?php
the_content();
$attachments = get_posts( array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' =>'any',
        'post_mime_type' => 'image',
        'post_parent' => $post->ID )
);
if ( !empty( $attachments ) ) {
    echo '<ul class="bigimages-list">';
    foreach ( $attachments as $attachment ) {
        $attachment_title = apply_filters( 'the_title' , $attachment->post_title );
        $attachment_content = apply_filters( 'the_content' , $attachment->post_content );
        $attachment_url = get_attachment_link( $attachment->ID );
        $attachment_thumb = wp_get_attachment_image_src( $attachment->ID, 'big' );
        echo '<li class="bigimage-loop">';
        echo '<div><img src="'.$attachment_thumb[0].'" alt="'.esc_attr( $attachment_title ).'" /></div>';
        echo '<h3>' . $attachment_title . '</h3>' . $attachment_content;
        echo '</li>';
    }
    echo '</ul>';
}

?>
</article>
</div>
<?php
get_sidebar();
get_footer();
