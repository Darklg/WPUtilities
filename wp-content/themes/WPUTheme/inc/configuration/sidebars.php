<?php
include dirname( __FILE__ ) . '/../../z-protect.php';
register_sidebar( array(
        'name' => __( 'Default Sidebar', 'wputh' ),
        'id' => 'wputh-sidebar',
        'description' => __( 'Default theme sidebar', 'wputh' ),
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ) );
