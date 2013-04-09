<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();
?>
<div class="main">
<article>
    <h2><?php echo __( '404 Error', 'wputh' ); ?></h2>
    <p><?php echo __( 'Sorry, but this page doesn’t exists.', 'wputh' ); ?></p>
</article>
</div>
<?php
get_sidebar();
get_footer();
