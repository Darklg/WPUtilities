<?php
include dirname( __FILE__ ) . '/z-protect.php';
?><article class="loop">
    <h2><?php the_title(); ?></h2>
    <aside class="lp-metas">
        <time class="lp-time" datetime="<?php echo get_the_time(DATE_W3C); ?>"><?php echo get_the_time(__('F j, Y')); ?></time>
        •
        <?php echo __('By'); ?> <?php echo get_the_author_link(); ?>
    </aside>
    <?php the_content(); ?>
</article>