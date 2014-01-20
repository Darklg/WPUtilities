<?php
include dirname( __FILE__ ) . '/../WPUTheme/z-protect.php';
if ( !isset( $_GET['is_ajax'] ) ) {
    ?><aside class="main-sidebar">
    <ul class="main-sidebar--widgets"><?php dynamic_sidebar( 'wputh-sidebar' );?></ul>
</aside>
<?php }
