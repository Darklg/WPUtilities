<?php
/*
Plugin Name: WPU Fishpig Redirect
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Redirect fishpig URls
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/


class wpufishpigredirect {
    public function __construct() {
        add_action('wp', array(&$this, 'wp'));
    }

    public function wp() {
        global $wp;
        if (is_admin()) {
            return;
        }

        /* Get Redirects */
        $redirects = apply_filters('wpufishpigredirect_list', array(
            /* before , after  */
            /* array('/wp/fr', '/fr/blog')  */
        ));

        /* Set urls */
        $_currentUrl = home_url(add_query_arg(array(), $wp->request));
        $_urlDetails = parse_url($_currentUrl);

        /* Parse redirections */
        $_newUrl = $_currentUrl;
        $_baseUrl = $_urlDetails['scheme'] . '://' . $_urlDetails['host'];
        foreach ($redirects as $_redirect) {
            $_newUrl = str_replace($_baseUrl . $_redirect[0], $_baseUrl . $_redirect[1], $_newUrl);
        }

        /* If redirection is needed */
        if ($_currentUrl != $_newUrl) {
            wp_redirect($_newUrl);
            die;
        }
    }
}

$wpufishpigredirect = new wpufishpigredirect();
