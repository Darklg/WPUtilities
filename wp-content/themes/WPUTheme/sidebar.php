<?php
include dirname( __FILE__ ) . '/z-protect.php';
if(!isset($_POST['ajax'])){
?><aside class="main-sidebar">
    <ul><?php dynamic_sidebar( 'wputh-sidebar' );?></ul>
</aside>
<?php }