<?php

/*
Plugin Name: WPU Woo Ask Invoice
Description: Add an invoice by email
Version: 0.2.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Based on : https://iconicwp.com/add-custom-page-account-area-woocommerce/
*/

class WPUWooAskInvoice {

    public $text_ask = '';
    public $text_ask_multiple = '';
    public $text_success_ask = '';
    public $text_email_title = '';
    public $text_email_content = '';
    public $email_to = '';
    public $switch_meta_key = 'wpuwooaskinvoice_asked';
    public $enable_multiple_ask = true;
    public $authorized_statuses = array(
        'processing',
        'on-hold'
    );

    public function __construct() {
        if (!function_exists('is_view_order_page')) {
            return;
        }
        add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
        add_action('template_redirect', array(&$this, 'template_redirect'));
        add_action('woocommerce_order_details_after_order_table', array(&$this, 'woocommerce_order_details_after_order_table'));
    }

    public function plugins_loaded() {
        /* Text */
        $this->text_ask = apply_filters('wpuwooaskinvoice__text_ask', __('Ask Invoice', 'wpuwooaskinvoice'));
        $this->text_ask_multiple = apply_filters('wpuwooaskinvoice__text_ask_multiple', __('You already asked for an invoice', 'wpuwooaskinvoice'));
        $this->text_success_ask = apply_filters('wpuwooaskinvoice__text_success_ask', __('Invoice has been asked', 'wpuwooaskinvoice'));
        $this->text_email_title = apply_filters('wpuwooaskinvoice__text_email_title', __('Order #%s - Invoice', 'wpuwooaskinvoice'));
        $this->text_email_content = apply_filters('wpuwooaskinvoice__text_email_content', __('Please send the invoice for the order #%s', 'wpuwooaskinvoice'));
        /* Values */
        $this->authorized_statuses = apply_filters('wpuwooaskinvoice__authorized_statuses', $this->authorized_statuses);
        $this->switch_meta_key = apply_filters('wpuwooaskinvoice__switch_meta_key', $this->switch_meta_key);
        $this->enable_multiple_ask = apply_filters('wpuwooaskinvoice__enable_multiple_ask', $this->enable_multiple_ask);
        $this->email_to = apply_filters('wpuwooaskinvoice__email_to', get_option('admin_email'));
    }

    public function template_redirect() {
        global $wp;
        if (!is_view_order_page() || empty($_POST) || !isset($_POST['wpuwooaskinvoice_ask'])) {
            return false;
        }
        $order_id = $wp->query_vars['view-order'];
        $wpuwooaskinvoice_asked = get_post_meta($order_id, $this->switch_meta_key, 1);
        if ($wpuwooaskinvoice_asked == 'ok' && !$this->enable_multiple_ask) {
            return false;
        }
        update_post_meta($order_id, $this->switch_meta_key, 'ok');
        wc_add_notice($this->text_success_ask);
        $order = wc_get_order($order_id);
        $order->add_order_note($this->text_success_ask);

        $text_email_title = sprintf($this->text_email_title, $order_id);
        $text_email_content = sprintf($this->text_email_content, $order_id);
        wp_mail($this->email_to, $text_email_title, $text_email_content);
    }

    public function woocommerce_order_details_after_order_table($order) {
        if (!$order || !in_array($order->get_status(), $this->authorized_statuses)) {
            return false;
        }
        $wpuwooaskinvoice_asked = get_post_meta($order->get_id(), $this->switch_meta_key, 1);
        if ($wpuwooaskinvoice_asked == 'ok' && !$this->enable_multiple_ask) {
            return false;
        }

        echo '<div class="wpuwooaskinvoice-form-container">';
        if ($wpuwooaskinvoice_asked == 'ok' && $this->enable_multiple_ask) {
            echo '<p>' . $this->text_ask_multiple . '</p>';
        }
        echo '<form class="wpuwooaskinvoice-form" action="" method="post"><p><button type="submit" name="wpuwooaskinvoice_ask">' . $this->text_ask . '</button></p></form>';
        echo '</div>';
    }
}

$WPUWooAskInvoice = new WPUWooAskInvoice();
