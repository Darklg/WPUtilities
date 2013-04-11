<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
the_post();
?>
<div class="main">
<article>
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
</article>
</div>
<?php
get_sidebar();
get_footer();
