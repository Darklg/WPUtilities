<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
the_post();
?>
<div class="main">
    <?php get_template_part( 'loop' ); ?>
</div>
<?php
get_sidebar();
get_footer();
