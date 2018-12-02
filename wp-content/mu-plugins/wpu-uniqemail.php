<?php

/*
Plugin Name: WPU Unique Email
Description: Ensure email address is unique.
Version: 0.1.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUUniqEmail {
    public function __construct() {
    }

    public function cleanEmail($email) {

        $email = strtolower($email);

        /* Get email parts */
        $email_parts = explode('@', $email);

        /* Remove alias */
        $email_tmp = explode('+', $email_parts[0]);
        $email_parts[0] = $email_tmp[0];

        /* Unique domain for gmail */
        $email_parts[1] = str_replace('googlemail.com', 'gmail.com', $email_parts[1]);
        if ($email_parts[1] == 'gmail.com') {
            /* Remove dots */
            $email_parts[0] = str_replace('.', '', $email_parts[0]);
        }

        return implode('@', $email_parts);
    }
}

$WPUUniqEmail = new WPUUniqEmail();
