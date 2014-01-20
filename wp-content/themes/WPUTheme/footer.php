<?php
include dirname( __FILE__ ) . '/z-protect.php';
if ( isset( $_GET['is_ajax'] ) ) return;
?>
</div></div>
<div class="main-footer centered-container">
    <footer class="contentinfo" role="contentinfo" id="contentinfo">
        <small class="main-footer--copyright">&copy; <?php echo date( 'Y' ); ?> - <?php bloginfo( 'name' ); ?></small>
        <small class="main-footer--credits"><?php
        echo sprintf( __( 'A %s site using %s', 'wputh' ),
            '<a href="https://wordpress.org" target="_blank">WordPress</a>',
            '<a href="https://github.com/Darklg/WPUtilities" target="_blank">WPUtilities</a>' );
        ?></small>
    </footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
