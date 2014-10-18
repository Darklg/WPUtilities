<?php

/*
Plugin Name: WPU Generate HTML Form
Plugin URI: https://github.com/WordPressUtilities/wpuvalidateform
Description: Generate HTML Form from a model
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUGenerateHTMLForm
{
    function __construct($fields) {
        $this->add_fields($fields);
    }

    function add_fields($fields) {
        if (is_array($fields)) {
            $this->fields = $fields;
        }
    }

    function display_form() {
        return $this->generate_form($this->fields);
    }

    function generate_form() {
        $html_return = '<form action="" method="post">';
        $html_return.= '<ul class="cssc-form cssc-form--default">';
        foreach ($this->fields as $id => $field) {
            $html_return.= $this->generate_field($id, $field);
        }
        $html_return .= '<li class="box submit-box"><button class="cssc-button cssc-button--default" type="submit">Submit</button></li>';
        $html_return.= '</ul>';
        $html_return.= '</form>';
        return $html_return;
    }

    function generate_field($id, $field) {
        $html = '';

        // Set label
        $label = $id;
        if (isset($field['label'])) {
            $label = $field['label'];
        }
        $html_label = '<label for="item-' . $id . '">' . $label . '</label>';

        // Id / name
        $id_name = 'id="item-'.$id.'" name="'.$id.'" ';

        $html.= '<li class="box box--' . $id . '">';
        $html.= $html_label;
        $html .= '<input '.$id_name.' type="text" value="" />';

        $html.= '</li>';
        return $html;
    }
}
