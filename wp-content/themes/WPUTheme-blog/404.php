<?php
include dirname( __FILE__ ) . '/../WPUTheme/z-protect.php';
get_header();
?>
<div class="main">
<article>
    <h1><?php echo __( '404 Error', 'wputh' ); ?></h1>
    <p><?php echo __( 'Sorry, but this page doesn&rsquo;t exists.', 'wputh' ); ?></p>
</article>
</div>
<?php
get_sidebar();
get_footer();
