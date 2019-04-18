<?php

/*
Plugin Name: WPU Email Debugger
Description: Send an email content to the debug log instead of sending it.
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action('phpmailer_init', 'phpmailer_init__wpu_email_debugger');
function phpmailer_init__wpu_email_debugger($phpmailer) {
    $_debug_content = '';
    $_fields = array('FromName','From','Subject', 'Body');
    foreach ($_fields as $_field) {
        $_debug_content .= $_field . ': ' . $phpmailer->$_field . "\n";
    }
    error_log("Email Debug : \n" . $_debug_content);
    $phpmailer->ClearAllRecipients();
}
