<?php

/*
Plugin Name: WPU User
Plugin URI: https://github.com/WordPressUtilities/wpuvalidateform
Description: Handle users
Version: 0.3.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUUser
{
    private $user = array();
    private $user_data = array();
    private $messages = array();
    public $fields_login = array();
    public $fields_register = array();
    private $validateForm = array();

    /**
     * @param mixed $user_id  numeric if loading a user, current if current user
     */
    function __construct($user_id = false) {

        if (!class_exists('WPUValidateForm')) {
            exit('WPUValidateForm is not enabled.');
        }

        $this->validateForm = new WPUValidateForm();

        $this->fields_login = array(
            'user_login' => array(
                'label' => 'Username',
                'required' => 1,
            ) ,
            'user_password' => array(
                'label' => 'Password',
                'type' => 'password',
                'required' => 1,
            ) ,
            'remember' => array(
                'label' => 'Remember me',
                'type' => 'checkbox',
                'required' => false,
            )
        );

        $this->fields_register = array(
            'user_login' => array(
                'label' => 'Username',
                'required' => 1,
                'islogin' => 1
            ) ,
            'user_email' => array(
                'label' => 'Email',
                'type' => 'email',
                'required' => 1,
                'isemail' => 1
            ) ,
            'user_pass' => array(
                'label' => 'Password',
                'type' => 'password',
                'required' => 1,
            ) ,
            'user_pass2' => array(
                'label' => 'Password 2',
                'type' => 'password',
                'required' => 1,
            )
        );

        if ($user_id == 'current') {
            $user_id = get_current_user_id();
        }
        if (is_numeric($user_id)) {
            $this->user = $this->get_user_by($user_id);
        }

        return true;
    }

    /* ----------------------------------------------------------
      User methods
    ---------------------------------------------------------- */

    /**
     * Login a user from its credentials
     * @param  array  $creds User credentials
     * @return mixed         False if error, Object if success
     */
    function login($creds = array()) {
        if (is_user_logged_in()) {
            $this->add_message('User is already logged in');
            return false;
        }

        if (!is_array($creds) || empty($creds)) {
            $this->add_message('Missing user informations');
            return false;
        }

        $form_valid = $this->validateForm->validate_values_from($this->fields_login, $creds);
        if ($form_valid['has_errors']) {
            $this->add_messages($form_valid['messages']);
            return false;
        }

        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            $this->add_message($user->get_error_message());
            return false;
        }
        $this->user = $user;
        $this->add_message('Success !', 'success');

        return $user;
    }

    function register($creds = array()) {
        if (!is_array($creds) || empty($creds)) {
            $this->add_message('Missing user informations');
            return false;
        }

        // Form validity
        $form_valid = $this->validateForm->validate_values_from($this->fields_register, $creds);
        if ($form_valid['has_errors']) {
            $this->add_messages($form_valid['messages']);
            return false;
        }
        $valid_creds = $form_valid['values'];

        // Email exists
        if (email_exists($valid_creds['user_email'])) {
            $this->add_message('Email is already used.');
            return false;
        }

        // Same password
        if ($valid_creds['user_pass'] != $valid_creds['user_pass2']) {
            $this->add_message('The two password are not identicals.');
            return false;
        }

        // Username exists
        if (username_exists($valid_creds['user_login'])) {
            $this->add_message('Username is already used.');
            return false;
        }

        $user_details = array(
            'role' => 'subscriber'
        );

        $user_full_details = array_merge($valid_creds, $user_details);

        // Create user
        $user_id = wp_insert_user($user_full_details);

        // Check for a WordPress error
        if (is_wp_error($user_id)) {
            foreach ($user_id->errors as $error) {
                foreach ($error as $error_message) {
                    $this->add_message($error_message);
                }
            }
            return false;
        }

        // Set user
        $this->user = $this->get_user_by($user_id);

        $this->add_message('Welcome ' . $this->get_username() . ' !', 'success');
    }

    /**
     * Get user by key and value
     * @param  mixed  $key_value   Key value
     * @param  string $key_type    Key type ( Default : id )
     * @return mixed               False if error, Object if success
     */
    function get_user_by($key_value, $key_type = 'id') {

        // Check for empty key value
        if (empty($key_value)) {
            $this->add_message('Missing key value');
            return false;
        }

        // Check for invalid key type
        if (!in_array($key_type, array(
            'id',
            'slug',
            'email',
            'login'
        ))) {
            $this->add_message('Invalid key type');
            return false;
        }

        $user = get_user_by('id', $key_value);

        if ($user === false) {
            $this->add_message('This user does not exists');
            return false;
        }

        return $user;
    }

    /* ----------------------------------------------------------
      Getters
    ---------------------------------------------------------- */

    public function has_user() {
        return is_object($this->user);
    }

    public function get_username() {
        return $this->user->data->user_login;
    }

    public function get_userID() {
        return $this->user->data->ID;
    }

    /* ----------------------------------------------------------
      Utilities
    ---------------------------------------------------------- */

    /**
     * Add a message array to the message queue
     * @param array $messages Message lists
     */
    function add_messages($messages) {
        foreach ($messages as $message) {
            $this->add_message($message['content'], $message['type']);
        }
    }

    /**
     * Add a message to the message queue
     * @param string $content  Message content
     * @param string $type     (Optional) Message type
     */
    function add_message($content, $type = 'error') {
        $this->messages[] = array(
            'content' => $content,
            'type' => $type
        );
    }

    /**
     * Empty the message queue and return HTML for display
     * @return string  HTML for the message queue
     */
    function display_messages() {
        $messages = $this->messages;
        $message_html = array();

        foreach ($messages as $message) {
            $message_html[] = $message['type'] . ' - ' . $message['content'];
        }

        // Empty messages
        $this->messages = array();

        return implode('<br />', $message_html);
    }
}

/*
### LOGIN ###
$wpu_user = new WPUUser();
if(!empty($_POST)){
    $wpu_user->login($_POST);
}
echo $wpu_user->display_messages();

### REGISTER ###
$wpu_user = new WPUUser();
if(!empty($_POST)){
    $wpu_user->register($_POST);
}
echo $wpu_user->display_messages();
*/
