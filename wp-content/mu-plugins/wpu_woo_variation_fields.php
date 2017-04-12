<?php
/*
Plugin Name: WPU Woo Variations Fields
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Quickly add fields to WooCommerce product variations : handle display & save
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUWooVariationFields {

    private $fields = array();

    public function __construct() {
        add_action('init', array(&$this, 'init'));
    }

    public function init() {

        $this->fields = $this->get_fields();

        // Add Variation Settings
        add_action('woocommerce_product_after_variable_attributes', array(&$this, 'variation_settings_fields'), 10, 3);

        // Save Variation Settings
        add_action('woocommerce_save_product_variation', array(&$this, 'save_variation_settings_fields'), 10, 2);
    }

    public function get_fields() {
        $fields = apply_filters('wpu_woo_variation_fields__fields', array());
        foreach ($fields as $id => $field) {
            /* Default label to ID */
            $field['label'] = !isset($field['label']) ? $id : $field['label'];
            /* Select : default to yes/no */
            if (!isset($field['options']) || !is_array($field['options'])) {
                $field['options'] = array(__('No'), __('Yes'));
            }
            /* Get value */
            $fields[$id] = $field;
        }

        return $fields;
    }

    /**
     * Create new fields for variations
     *
     */
    public function variation_settings_fields($loop, $variation_data, $variation) {

        foreach ($this->fields as $id => $field) {
            $field['id'] = '_' . $id . '[' . $variation->ID . ']';
            $field['value'] = get_post_meta($variation->ID, '_' . $id, true);

            switch ($field['type']) {
            case 'select':
                woocommerce_wp_select($field);
                break;
            case 'checkbox':
                woocommerce_wp_checkbox($field);
                break;
            case 'textarea':
                woocommerce_wp_textarea_input($field);
                break;
            case 'hidden':
                woocommerce_wp_hidden_input($field);
                break;
            default:
                woocommerce_wp_text_input($field);
            }

        }

    }

    /**
     * Save new fields for variations
     *
     */
    public function save_variation_settings_fields($post_id) {

        foreach ($this->fields as $id => $field) {
            $_id = '_' . $id;
            /* For non checkbox : test if field exists */
            if ($field['type'] != 'checkbox') {
                if (!isset($_POST[$_id], $_POST[$_id][$post_id])) {
                    return;
                }
                $_tmp_val = $_POST[$_id][$post_id];
            }

            $_val = false;

            switch ($field['type']) {
            case 'select':
                if (array_key_exists($_tmp_val, $field['options'])) {
                    $_val = $_tmp_val;
                }
                break;
            case 'number':
                if (is_numeric($_tmp_val)) {
                    $_val = $_tmp_val;
                }
                break;
            case 'checkbox':
                $_val = isset($_POST[$_id][$post_id]) ? 'yes' : 'no';
                break;
            case 'hidden':
            case 'text':
            case 'textarea':
                $_val = esc_attr($_tmp_val);
                break;
            default:
            }

            if ($_val !== false) {
                update_post_meta($post_id, $_id, esc_attr($_val));
            }
        }

    }

}

$WPUWooVariationFields = new WPUWooVariationFields();

/*
add_filter('wpu_woo_variation_fields__fields', 'test_wpu_woo_variation_fields__fields', 10, 1);
function test_wpu_woo_variation_fields__fields($values) {
    $values['test_select'] = array(
        'type' => 'select',
        'label' => __('Select test'),
    );
    return $values;
}
*/
