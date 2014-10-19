<?php

/*
Plugin Name: WPU Generate HTML Form
Plugin URI: https://github.com/WordPressUtilities/wpuvalidateform
Description: Generate HTML Form from a model
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUGenerateHTMLForm
{
    private $form_settings = array(
        'submit-button-class' => 'cssc-button--default',
        'submit-button-text' => 'Submit',
        'form-class' => 'cssc-form--default',
    );

    function __construct($fields, $settings = array()) {
        $this->add_fields($fields);
        $this->set_settings($settings);
    }

    public function add_fields($fields = array()) {
        if (is_array($fields)) {
            $this->fields = $fields;
        }
    }

    public function set_settings($settings = array()) {
        if (!is_array($settings)) {
            $settings = array();
        }
        $this->form_settings = array_merge($this->form_settings, $settings);
    }

    public function display_form() {
        $html_return = '<form action="" method="post">';
        $html_return.= '<ul class="cssc-form ' . $this->form_settings['form-class'] . '">';
        foreach ($this->fields as $id => $field) {
            $html_return.= $this->generate_field($id, $field);
        }
        $html_return.= '<li class="box submit-box"><button class="cssc-button ' . $this->form_settings['submit-button-class'] . '" type="submit">' . $this->form_settings['submit-button-text'] . '</button></li>';
        $html_return.= '</ul>';
        $html_return.= '</form>';
        return $html_return;
    }

    public function generate_field($id, $field) {
        $html = '';

        // Set label
        $label = $id;
        if (isset($field['label'])) {
            $label = $field['label'];
        }
        $html_label = '<label for="item-' . $id . '">' . $label . '</label>';

        // Id / name
        $id_name = 'id="item-' . $id . '" name="' . $id . '" ';
        if (isset($field['required']) && $field['required']) {
            $id_name.= 'required="required"';
        }

        // Field type
        $box_type = '';
        if (!isset($field['type'])) {
            $field['type'] = 'text';
        }

        // Field datas
        if (!isset($field['datas']) || !is_array($field['datas'])) {
            $field['datas'] = array(
                'No',
                'Yes'
            );
        }

        // Set Field HTML
        $html_field = '';
        switch ($field['type']) {
            case 'checkbox':
                $box_type = 'checked-box';
                $html_field.= '<input ' . $id_name . ' type="checkbox" value="" /> ' . $html_label;
                break;

            case 'select':
                $html_field.= '<select ' . $id_name . '>';
                $html_field.= '<option value="" disabled selected style="display:none;">Select a value</option>';
                foreach ($field['datas'] as $key => $var) {
                    $html_field.= '<option value="' . $key . '">' . $var . '</option>';
                }
                $html_field.= '</select>';
                break;

            case 'text':
            case 'password':
            case 'email':
            case 'url':
                $html_field.= $html_label . '<input ' . $id_name . ' type="' . $field['type'] . '" value="" />';
                break;
        }

        $html.= '<li class="box box--' . $id . ' ' . $box_type . '">';
        $html.= $html_field;
        $html.= '</li>';
        return $html;
    }
}
