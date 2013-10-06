<?php
/* Template Name: Webservice */

include dirname( __FILE__ ) . '/../z-protect.php';

$mode = '';
if ( isset( $_GET['mode'] ) ) {
    $mode = $_GET['mode'];
}

switch ( $_GET['mode'] ) {
case 'ajax_content':
    the_post();
    the_content();
    break;
default:
    header( $_SERVER["SERVER_PROTOCOL"]." 404 Not Found" );
    include get_template_directory() . '/404.php';
    die;
}
