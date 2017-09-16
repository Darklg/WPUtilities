<?php
/*
Plugin Name: WPU Woo Custom Order Status
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Get an order summary for the latest user order
Version: 0.4.0
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
            'register_post_statuses'
        ));
        add_filter('wc_order_statuses', array(&$this,
            'wc_order_statuses'
        ));
        add_filter('woocommerce_reports_order_statuses', array(&$this,
            'woocommerce_reports_order_statuses'
        ));
        add_filter('admin_head', array(&$this,
            'admin_head'
        ));
        add_filter('woocommerce_my_account_my_orders_query', array(&$this,
            'woocommerce_my_account_my_orders_query'
        ), 10, 1);
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
            if (!isset($status['wpuwoo_exclude_from_user_account'])) {
                $status['wpuwoo_exclude_from_user_account'] = false;
            }
            if (!isset($status['show_in_admin_all_list'])) {
                $status['show_in_admin_all_list'] = true;
            }
            if (!isset($status['show_in_admin_status_list'])) {
                $status['show_in_admin_status_list'] = true;
            }
            if (!isset($status['load_in_reports'])) {
                $status['load_in_reports'] = false;
            }
            if (!isset($status['icon_color'])) {
                $status['icon_color'] = '#73a724';
            }
            if (!isset($status['icon_mark'])) {
                $status['icon_mark'] = '\e015';
            }
            if (!isset($status['label_count'])) {
                $status['label_count'] = _n_noop($status['name'] . ' <span class="count">(%s)</span>', $status['name'] . ' <span class="count">(%s)</span>');
            }

            $new_statuses[$id] = $status;
        }
        return $new_statuses;
    }

    public function admin_head() {
        $_main_class = array();
        foreach ($this->statuses as $id => $status) {
            $_main_class[] = '.widefat .column-order_status mark.' . str_replace('wc-', '', $id) . '::after';
        }
        if (empty($_main_class)) {
            return;
        }
        echo '<style>';
        echo implode(',', $_main_class) . '{font-family: WooCommerce;speak: none;font-weight: 400;font-variant: normal;text-transform: none;line-height: 1;-webkit-font-smoothing: antialiased;margin: 0;text-indent: 0;position: absolute;top: 0;left: 0;width: 100%;height: 100%;text-align: center}';
        foreach ($this->statuses as $id => $status) {
            echo '.widefat .column-order_status mark.' . str_replace('wc-', '', $id) . '::after{content:"' . $status['icon_mark'] . '";color: ' . $status['icon_color'] . ';}';
        }
        echo '</style>';
    }

    public function register_post_statuses() {
        foreach ($this->statuses as $id => $status) {
            register_post_status($id, $status);
        }
    }

    // Add to list of WC Order statuses
    public function wc_order_statuses($order_statuses) {
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
            if (!isset($new_status['insert_before'], $new_status['insert_after'])) {
                $new_order_statuses[$id] = $new_status['name'];
            }
        }

        return $new_order_statuses;
    }

    public function woocommerce_reports_order_statuses($order_status = array()) {
        if (!is_array($order_status)) {
            return $order_status;
        }
        foreach ($this->statuses as $id_status => $new_status) {
            if (!$new_status['load_in_reports']) {
                continue;
            }
            if (in_array($id_status, $order_status)) {
                continue;
            }
            $order_status[] = str_replace('wc-', '', $id_status);
        }
        return $order_status;
    }

    public function woocommerce_my_account_my_orders_query($args) {
        if (!isset($args['status'])) {
            $statuses = wc_get_order_statuses();
            foreach ($this->statuses as $key => $status) {
                if (isset($status['wpuwoo_exclude_from_user_account'], $statuses[$key]) && $status['wpuwoo_exclude_from_user_account']) {
                    unset($statuses[$key]);
                }
            }
            $args['status'] = array_keys($statuses);
        }
        return $args;
    }

}

$WPUWooCustomOrderStatus = new WPUWooCustomOrderStatus();
