<?php

/*
Plugin Name: WPU User
Plugin URI: https://github.com/WordPressUtilities/wpuvalidateform
Description: Handle users
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUUser
{
    private $user = array();
    private $messages = array();
    private $fields_create = array();

    function __construct() {
        $fields_create = array(
            'user_login' => array(
                'required' => 1,
                'label' => 'Username',
            ) ,
            'password' => array(
                'required' => 1,
                'label' => 'Password',
            ) ,
            'remember' => array()
        );
    }

    function login($creds = array()) {
        if (!is_array($creds) || empty($creds) || !isset($creds['user_login']) || !isset($creds['user_password']) || empty($creds['user_login']) || empty($creds['user_password'])) {
            $this->add_message('Missing user informations');
            return false;
        }

        if (!isset($creds['remember']) || !is_bool($creds['remember'])) {
            $creds['remember'] = false;
        }

        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            $this->add_message($user->get_error_message());
            return false;
        }
        $this->user = $user;
        $this->add_message('Success !');

        return $user;
    }

    function add_message($message) {
        $this->messages[] = $message;
    }

    function display_messages() {
        $messages = $this->messages;
        $this->messages = array();
        return implode('<br />', $messages);
    }
}

