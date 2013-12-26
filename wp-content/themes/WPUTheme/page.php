<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
the_post();
?>
<div class="main-content">
<article <?php post_class(); ?>>
    <h1><?php the_title(); ?></h1>
    <div class="cssc-content cssc-block">
<?php
    the_content();
    /* If a nextpage tag is used */
    wp_link_pages();
    /* Displaying child pages */
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'page',
        'orderby' => 'title',
        'order' => 'ASC',
        'post_parent' => get_the_ID()
    );
    $wpq_child_pages = new WP_Query( $args );
    if ( $wpq_child_pages->have_posts() ) {
        echo '<ul>';
        while ( $wpq_child_pages->have_posts() ) {
            $wpq_child_pages->the_post();
            echo '<li>';
            echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
            echo '</li>';
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
