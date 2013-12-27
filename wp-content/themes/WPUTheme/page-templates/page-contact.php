<?php
/* Template Name: Contact */
include dirname( __FILE__ ) . '/../../WPUTheme/z-protect.php';
include get_template_directory() . '/tpl/contact/header-action.php';
get_header();
the_post();
?>
<main class="main-content" role="main" id="main">
<article role="article">
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
    <?php echo $content_contact; ?>
</article>
</main>
<?php
get_sidebar();
get_footer();
