<?php
include dirname( __FILE__ ) . '/../WPUTheme/z-protect.php';
get_header();
the_post();
?>
<div class="main">
<article <?php post_class(); ?>>
    <h1><?php the_title(); ?></h1>
    <div class="cssc-content cssc-block">
<?php
    the_content();
    /* If a nextpage tag is used */
    wp_link_pages();
    /* Showing child pages */
    include get_template_directory() . '/tpl/page/child-pages.php';
?>
    </div>
</article>

</div>
<?php
get_sidebar();
get_footer();
