<header>
    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    <aside class="lpsm-metas">
        <?php echo __( 'By', 'wputh' ); ?>
        <?php the_author_posts_link(); ?>
        &bull;
        <time class="lpsm-time" datetime="<?php echo get_the_time( DATE_W3C ); ?>">
            <?php echo get_the_time( __( 'F j, Y', 'wputh' ) ); ?>
        </time>
    </aside>
</header>