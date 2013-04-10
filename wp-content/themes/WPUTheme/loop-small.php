<?php
include dirname( __FILE__ ) . '/z-protect.php';
?><article class="loop-small">
    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    <aside class="lpsm-metas">
        <?php the_category(', '); ?>
        •
        <time class="lpsm-time" datetime="<?php echo get_the_time( DATE_W3C ); ?>"><?php echo get_the_time( __( 'F j, Y', 'wputh' ) ); ?></time>
        •
        <?php echo __( 'By', 'wputh' ); ?> <?php echo get_the_author_link(); ?>
    </aside>
    <?php the_excerpt(); ?>
</article>
