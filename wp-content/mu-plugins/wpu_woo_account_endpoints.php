<?php

/*
Plugin Name: WPU Woo Account Endpoints
Description: Add a new endpoint on Woocommerce account
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Based on : https://iconicwp.com/add-custom-page-account-area-woocommerce/
*/

class WPUWooAccountEndpoint {
    private $endpoints = array();

    public function __construct() {
        add_filter('woocommerce_account_menu_items', array(&$this, 'add_menu_items'));
        add_filter('template_redirect', array(&$this, 'launch_postaction'));
        add_action('init', array(&$this, 'init'));
    }

    public function init() {

        /* Get endpoints */
        $this->endpoints = apply_filters('wpu_woo_account_endpoints__list', $this->endpoints);

        foreach ($this->endpoints as $id => &$endpoint) {
            /* Filter endpoint content */
            if (!is_array($endpoint)) {
                $endpoint = array();
            }

            if (!isset($endpoint['name'])) {
                $endpoint['name'] = ucfirst($id);
            }

            if (!isset($endpoint['callback_content'])) {
                $endpoint['callback_content'] = array(&$this, 'default_content');
            }

            /* Load endpoint */
            add_rewrite_endpoint($id, EP_PAGES);
            add_action('woocommerce_account_' . $id . '_endpoint', $endpoint['callback_content']);
        }

        /* Check if rewrite rules are correct */
        $opt_id = 'wpu_woo_account_endpoints__rules';
        $rules_version = md5(json_encode($this->endpoints));
        if (get_option($opt_id) != $rules_version) {
            update_option($opt_id, $rules_version);
            flush_rewrite_rules();
        }

    }

    /* Load item into the WP menu */
    public function add_menu_items($items) {
        foreach ($this->endpoints as $id => $endpoint) {
            $items[$id] = $endpoint['name'];
        }
        return $items;
    }

    /* Default content for the page */
    public function default_content() {
        foreach ($this->endpoints as $id => $endpoint) {
            if ($this->is_endpoint($id)) {
                echo '<p>' . sprintf('Default content for <strong>%s</strong>', $endpoint['name']) . '</p>';
                break;
            }
        }
    }

    /* Launch user action */
    public function launch_postaction() {
        foreach ($this->endpoints as $id => $endpoint) {
            if ($this->is_endpoint($id) && isset($endpoint['callback_postaction'])) {
                call_user_func_array($endpoint['callback_postaction'], array($id));
                error_log($id);
                break;
            }
        }
    }

    /* ----------------------------------------------------------
      Tools
    ---------------------------------------------------------- */

    public function is_endpoint($endpoint = false) {
        global $wp_query;

        if (!$wp_query) {
            return false;
        }

        return isset($wp_query->query[$endpoint]);
    }

}

$WPUWooAccountEndpoint = new WPUWooAccountEndpoint();


/* ADD A FIELD
add_filter('wpu_woo_account_endpoints__list', 'test___wpu_woo_account_endpoints__list', 10, 1);
function test___wpu_woo_account_endpoints__list($items) {
    $items['info'] = array(
        'name' => __('Information')
    );
    return $items;
}
*/
