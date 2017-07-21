<?php
/*
Plugin Name: WPU Woo Custom Order Status
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Get an order summary for the latest user order
Version: 0.1.2
Author: Darklg
Author URI: http://darklg.me/
Thanks to: https://www.sellwithwp.com/woocommerce-custom-order-status-2/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUWooCustomOrderStatus {
    public function __construct() {
        add_action('plugins_loaded', array(&$this,
            'plugins_loaded'
        ));
        add_action('init', array(&$this,
            'register_awaiting_shipment_order_status'
        ));
        add_filter('wc_order_statuses', array(&$this,
            'add_awaiting_shipment_to_order_statuses'
        ));
    }

    public function plugins_loaded() {
        $this->statuses = $this->load_statuses(apply_filters('wpuwoocustomorderstatus_statuses', array()));
    }

    public function load_statuses($statuses = array()) {
        $new_statuses = array();
        foreach ($statuses as $id => $status) {
            if (!isset($status['name'])) {
                $status['name'] = ucfirst($id);
            }
            if (!isset($status['public'])) {
                $status['public'] = true;
            }
            if (!isset($status['exclude_from_search'])) {
                $status['exclude_from_search'] = true;
            }
            if (!isset($status['show_in_admin_all_list'])) {
                $status['show_in_admin_all_list'] = true;
            }
            if (!isset($status['show_in_admin_status_list'])) {
                $status['show_in_admin_status_list'] = true;
            }
            if (!isset($status['label_count'])) {
                $status['label_count'] = _n_noop($status['name'] . ' <span class="count">(%s)</span>', $status['name'] . ' <span class="count">(%s)</span>');
            }

            $new_statuses[$id] = $status;
        }
        return $new_statuses;
    }

    public function register_awaiting_shipment_order_status() {
        foreach ($this->statuses as $id => $status) {
            register_post_status($id, $status);
        }
    }

    // Add to list of WC Order statuses
    public function add_awaiting_shipment_to_order_statuses($order_statuses) {
        $new_order_statuses = array();
        // add new order status after processing
        foreach ($order_statuses as $key => $status) {
            foreach ($this->statuses as $id => $new_status) {
                if (isset($new_status['insert_before']) && $new_status['insert_before'] === $key) {
                    $new_order_statuses[$id] = $new_status['name'];
                }
            }
            $new_order_statuses[$key] = $status;
            foreach ($this->statuses as $id => $new_status) {
                if (isset($new_status['insert_after']) && $new_status['insert_after'] === $key) {
                    $new_order_statuses[$id] = $new_status['name'];
                }
            }
        }

        /* Insert status after */
        foreach ($this->statuses as $id => $new_status) {
            if (!isset($new_status['insert_before'],$new_status['insert_after'])) {
                $new_order_statuses[$id] = $new_status['name'];
            }
        }

        return $new_order_statuses;
    }

}

$WPUWooCustomOrderStatus = new WPUWooCustomOrderStatus();
