<?php
/* Template Name: Sitemap */
include dirname( __FILE__ ) . '/../z-protect.php';

/* ----------------------------------------------------------
  Functions
---------------------------------------------------------- */

function get_pages_sitemap_child_of( $sitemap_pages = array(), $parent = 0 ) {
    $content = '';

    if ( $parent == 0 ) {
        $content .= '<li><a href="'.site_url().'">'.__( 'Home page', 'wputh' ).'</a></li>';
    }

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

$args = array(
    'posts_per_page' => 100
);
$wpq_sitemap_posts = new WP_Query( $args );

/* ----------------------------------------------------------
  Page content
---------------------------------------------------------- */

get_header();
the_post();
?>
<div class="main-content">
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
