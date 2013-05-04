<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
the_post();
?>
<div class="main">
<article class="loop">
    <h1><?php the_title(); ?></h1>
    <div>
    <?php if ( wp_attachment_is_image( $post->id ) ) :
    $att_image = wp_get_attachment_image_src( $post->id, "medium" ); ?>
    <p><img src="<?php echo $att_image[0];?>" alt="<?php $post->post_excerpt; ?>" /></p>
    <?php else : ?>
    <a href="<?php echo wp_get_attachment_url( $post->ID ) ?>" title="<?php echo wp_specialchars( get_the_title( $post->ID ), 1 ) ?>" rel="attachment">
        <?php echo basename( $post->guid ) ?>
    </a>
    <?php endif; ?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
