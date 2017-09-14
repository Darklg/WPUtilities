<?php

/*
Plugin Name: WPU Woo Ask Invoice
Description: Add an invoice by email
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Based on : https://iconicwp.com/add-custom-page-account-area-woocommerce/
*/

class WPUWooAskInvoice {

    public $text_ask = '';
    public $text_success_ask = '';
    public $text_email_title = '';
    public $text_email_content = '';

    public function __construct() {
        add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
        add_action('template_redirect', array(&$this, 'template_redirect'));
        add_action('woocommerce_order_details_after_order_table', array(&$this, 'woocommerce_order_details_after_order_table'));
    }

    public function plugins_loaded() {
        $this->text_ask = apply_filters('wpuwooaskinvoice__text_ask', __('Ask Invoice', 'wpuwooaskinvoice'));
        $this->text_success_ask = apply_filters('wpuwooaskinvoice__text_success_ask', __('Invoice has been asked', 'wpuwooaskinvoice'));
        $this->text_email_title = apply_filters('wpuwooaskinvoice__text_email_title', __('Order #%s - Invoice', 'wpuwooaskinvoice'));
        $this->text_email_content = apply_filters('wpuwooaskinvoice__text_email_content', __('Please send the invoice for the order #%s', 'wpuwooaskinvoice'));
    }

    public function template_redirect() {
        global $post;
        if (!is_view_order_page() || empty($_POST) || !isset($_POST['wpuwooaskinvoice_ask'])) {
            return false;
        }
        $wpuwooaskinvoice_asked = get_post_meta($post->ID, 'wpuwooaskinvoice_asked', 1);
        if ($wpuwooaskinvoice_asked == 'ok') {
            return false;
        }
        update_post_meta($post->ID, 'wpuwooaskinvoice_asked', 'ok');
        wc_add_notice($this->text_success_ask);

        $text_email_title = sprintf($this->text_email_title, $post->ID);
        $text_email_content = sprintf($this->text_email_content, $post->ID);
        wp_mail(get_option('admin_email'), $text_email_title, $text_email_content);
    }

    public function woocommerce_order_details_after_order_table($order) {
        global $post;
        if (!$order || $order->get_status() != 'on-hold') {
            return false;
        }
        $wpuwooaskinvoice_asked = get_post_meta($post->ID, 'wpuwooaskinvoice_asked', 1);
        if ($wpuwooaskinvoice_asked == 'ok') {
            return false;
        }

        echo '<form class="wpuwooaskinvoice-form" action="" method="post"><p><button type="submit" name="wpuwooaskinvoice_ask">' . $this->text_ask . '</button></p></form>';
    }
}

$WPUWooAskInvoice = new WPUWooAskInvoice();
