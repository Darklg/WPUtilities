<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

include dirname( __FILE__ ).'/wpubaseplugin.php';

$wpuBasePlugin->uninstall();
