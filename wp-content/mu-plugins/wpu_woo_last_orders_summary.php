<?php
/*
Plugin Name: WPU Woo Last Orders Summary
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Get an order summary for the latest user order
Version: 0.1.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUWooLastOrdersSummary {
    public function __construct() {
        add_action('woocommerce_account_dashboard', array(&$this, 'woocommerce_account_dashboard'));
    }

    public function woocommerce_account_dashboard() {
        /* Get orders summary */
        $orders = $this->get_orders();

        /* Trigger a new hook */
        do_action('wpuwoolastorderssummary_orders', $orders);
    }

    public function get_orders($customer_id = false, $statuses = false) {

        $orders = array(
            'summary' => array(
                'total' => 0,
                'processing' => 0
            ),
            'latest' => false
        );

        if (!is_numeric($customer_id)) {
            $customer_id = get_current_user_id();
        }
        /* Excluded cancelled by default */
        if (!is_array($statuses)) {
            $statuses = array('cancelled');
        }

        $customer_orders = wc_get_orders(apply_filters('woocommerce_my_account_my_orders_query', array('customer' => $customer_id)));
        foreach ($customer_orders as $order) {
            if (is_bool($order)) {
                continue;
            }
            $status = $order->get_status();
            if (in_array($status, $statuses)) {
                continue;
            }
            /* Save latest order */
            if ($orders['latest'] === false) {
                $orders['latest'] = $order;
            }
            /* Count each order status */
            if (!isset($orders['summary'][$status])) {
                $orders['summary'][$status] = 0;
            }
            $orders['summary'][$status]++;
            $orders['summary']['total']++;
        }

        return $orders;
    }
}

$WPUWooLastOrdersSummary = new WPUWooLastOrdersSummary();
