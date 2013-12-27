<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
?>
<main class="main-content" role="main" id="main">
<article>
    <h1><?php echo __( '404 Error', 'wputh' ); ?></h1>
    <p><?php echo __( 'Sorry, but this page doesn&rsquo;t exists.', 'wputh' ); ?></p>
</article>
</main>
<?php
get_sidebar();
get_footer();
