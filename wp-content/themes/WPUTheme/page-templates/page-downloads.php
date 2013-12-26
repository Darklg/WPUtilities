<?php
/* Template Name: Downloads */
include dirname( __FILE__ ) . '/../z-protect.php';

get_header();
the_post();

$attachments = get_posts( array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' =>'any',
        'post_parent' => $post->ID )
);
?>

<div class="main-content">
<article>
    <h1><?php the_title(); ?></h1>
<?php
the_content();
if ( !empty( $attachments ) ) {
    echo '<ul>';
    foreach ( $attachments as $attachment ) {
        $attachment_url = wp_get_attachment_url( $attachment->ID );
        $attachment_path = get_attached_file( $attachment->ID );
        $filesize = size_format( filesize( $attachment_path ) );
        echo '<li>';
        echo '<h4><a target="_blank" download href="' . $attachment_url . '">' . apply_filters( 'the_title' , $attachment->post_title ) . '</a></h4>';
        echo '<small>' . __( 'Size:', 'wputh' ) . ' ' . $filesize . '</small>';
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
