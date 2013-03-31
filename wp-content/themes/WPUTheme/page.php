<?php
get_header();
the_post();
?>
<article>
    <h2><?php the_title(); ?></h2>
    <?php the_content(); ?>
</article>
<?php
get_footer();
