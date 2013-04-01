<?php
get_header();
the_post();
?>
<div class="main">
<article>
    <h2><?php the_title(); ?></h2>
    <?php the_content(); ?>
</article>
</div>
<?php
get_footer();
