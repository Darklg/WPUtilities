<?php
/* Template Name: Sitemap */
include dirname( __FILE__ ) . '/../../WPUTheme/z-protect.php';
include get_template_directory() . '/tpl/sitemap/header-action.php';
get_header();
the_post();
?>
<div class="main">
<article>
    <h1><?php the_title(); ?></h1>
    <div class="le-content">
    <?php the_content(); ?>
    <hr />
    <?php
if ( !empty( $sitemap_pages ) ) {
    echo '<h3>'.__( 'Pages', 'wputh' ).'</h3>';
    echo get_pages_sitemap_child_of( $sitemap_pages, 0 );
}

if ( $wpq_sitemap_posts->have_posts() ) {
    echo '<h3>'.__( 'Posts', 'wputh' ).'</h3>';
    echo '<ul>';
    while ( $wpq_sitemap_posts->have_posts() ) {
        $wpq_sitemap_posts->the_post();
        echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
    }
    echo '</ul>';
}
wp_reset_postdata();

?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
