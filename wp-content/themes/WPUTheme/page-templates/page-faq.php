<?php
/* Template Name: FAQ */
include dirname( __FILE__ ) . '/../../WPUTheme/z-protect.php';
the_post();
include get_template_directory() . '/tpl/faq/header-action.php';
get_header();
?>
<div class="main-content">
<article>
    <h1><?php the_title(); ?></h1>
    <div id="faq-content">
        <?php echo $content_faq; ?>
    </div>
</article>
</div>
<?php
get_footer();
