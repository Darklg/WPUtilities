<?php
/* Template Name: Sitemap */
include dirname( __FILE__ ) . '/../z-protect.php';

/* ----------------------------------------------------------
  Functions
---------------------------------------------------------- */

function get_pages_sitemap_child_of( $sitemap_pages = array(), $parent = 0 ) {
    $content = '';

    foreach ( $sitemap_pages as $id => $sitemap_page ) {
        if ( $sitemap_page['parent'] == $parent ) {
            $content .= '<li>';
            $content .= '<a href="'.$sitemap_page['permalink'].'">'.$sitemap_page['title'].'</a>';
            $content .= get_pages_sitemap_child_of( $sitemap_pages, $id );
            $content .= '</li>';
        }
    }

    if ( !empty( $content ) ) {
        $content = '<ul>'.$content.'</ul>';
    }
    return $content;
}

/* ----------------------------------------------------------
  Queries
---------------------------------------------------------- */

$args = array(
    'posts_per_page' => -1,
    'post_type' => 'page',
    'post__not_in' => array( get_the_ID() )
);
$sitemap_pages = array();
$wpq_sitemap = new WP_Query( $args );
if ( $wpq_sitemap->have_posts() ) {
    while ( $wpq_sitemap->have_posts() ) {
        $wpq_sitemap->the_post();
        $sitemap_pages[get_the_ID()] = array(
            'permalink' => get_permalink(),
            'title' => get_the_title(),
            'parent' => $post->post_parent
        );
    }
}
wp_reset_postdata();

/* ----------------------------------------------------------
  Content
---------------------------------------------------------- */

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
    echo get_pages_sitemap_child_of( $sitemap_pages, 0 );
}
?>
    </div>
</article>
</div>
<?php
get_sidebar();
get_footer();
