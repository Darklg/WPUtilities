<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

$main_tag = 'div';
if(is_home()){
    $main_tag = 'h1';
}

?><<?php echo $main_tag; ?> class="h1"><a href="<?php echo site_url(); ?>"><?php bloginfo('name'); ?></a></<?php echo $main_tag; ?>>
