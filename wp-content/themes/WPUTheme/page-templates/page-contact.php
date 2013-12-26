<?php
/* Template Name: Contact */
include dirname( __FILE__ ) . '/../../WPUTheme/z-protect.php';
include get_template_directory() . '/tpl/contact/header-action.php';
get_header();
the_post();
?>
<div class="main-content">
<article>
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
    <?php echo $content_contact; ?>
</article>
</div>
<?php
get_sidebar();
get_footer();
