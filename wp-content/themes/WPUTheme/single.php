<?php
get_header();
the_post();
?>
<article>
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php the_content(); ?>
</article>
<?php
get_footer();
