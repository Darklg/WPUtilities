<?php

if(!defined('ABSPATH'))
    exit;

if(acf_version_compare($GLOBALS['wp_version'],  '<', '4.9'))
    return;

if(!class_exists('acfe_field_code_editor')):

class acfe_field_code_editor extends acf_field{
    
    function __construct(){
        
        $this->name = 'acfe_code_editor';
        $this->label = __('Code Editor', 'acfe');
        $this->category = 'content';
        $this->defaults = array(
            'default_value'	=> '',
			'placeholder'   => '',
			'mode'          => 'text/html',
			'lines'         => true,
			'indent_unit'   => 4,
			'maxlength'		=> '',
			'rows'			=> '',
			'max_rows'      => ''
        );
        
        $this->textarea = acf_get_field_type('textarea');
        
        parent::__construct();
        
    }

    function render_field($field){
        
        $wrapper = array(
            'class'             => 'acf-input-wrap acfe-field-code-editor',
            'data-mode'         => $field['mode'],
            'data-lines'        => $field['lines'],
            'data-indent-unit'  => $field['indent_unit'],
            'data-rows'         => $field['rows'],
            'data-max-rows'     => $field['max_rows'],
        );
        
        $field['type'] = 'textarea';
        
        ?>
        <div <?php acf_esc_attr_e($wrapper); ?>>
            <?php $this->textarea->render_field($field); ?>
        </div>
        <?php
        
    }
    
    function render_field_settings($field){
        
        // default_value
        acf_render_field_setting($field, array(
            'label'			=> __('Default Value','acf'),
            'instructions'	=> __('Appears when creating a new post','acf'),
            'type'			=> 'acfe_code_editor',
            'name'			=> 'default_value',
            'rows'          => 4
        ));
        
        // placeholder
        acf_render_field_setting($field, array(
            'label'			=> __('Placeholder','acf'),
            'instructions'	=> __('Appears within the input','acf'),
            'type'			=> 'acfe_code_editor',
            'name'			=> 'placeholder',
            'rows'          => 4
        ));
        
        // Mode
        acf_render_field_setting($field, array(
            'label'			=> __('Editor mode','acf'),
            'instructions'	=> __('Choose the syntax highlight','acf'),
            'type'          => 'select',
            'name'			=> 'mode',
            'choices'       => array(
                'text/html'                 => __('Text/HTML', 'acf'),
                'javascript'                => __('JavaScript', 'acf'),
                'css'                       => __('CSS', 'acf'),
                'application/x-httpd-php'   => __('PHP (mixed)', 'acf'),
                'text/x-php'                => __('PHP (plain)', 'acf'),
            )
        ));
        
        // Lines
        acf_render_field_setting($field, array(
            'label'			=> __('Show Lines', 'acf'),
            'instructions'	=> 'Whether to show line numbers to the left of the editor',
            'type'			=> 'true_false',
            'name'			=> 'lines',
            'ui'            => true,
        ));
        
        // Indent Unit
        acf_render_field_setting($field, array(
            'label'			=> __('Indent Unit', 'acf'),
            'instructions'	=> 'How many spaces a block (whatever that means in the edited language) should be indented',
            'type'			=> 'number',
            'min'			=> 0,
            'name'			=> 'indent_unit',
        ));
        
        // maxlength
        acf_render_field_setting($field, array(
            'label'			=> __('Character Limit','acf'),
            'instructions'	=> __('Leave blank for no limit','acf'),
            'type'			=> 'number',
            'name'			=> 'maxlength',
        ));
        
        // rows
        acf_render_field_setting($field, array(
            'label'			=> __('Rows','acf'),
            'instructions'	=> __('Sets the textarea height','acf'),
            'type'			=> 'number',
            'name'			=> 'rows',
            'placeholder'	=> 8
        ));
        
        // max rows
        acf_render_field_setting($field, array(
            'label'			=> __('Max rows','acf'),
            'instructions'	=> __('Sets the textarea max height','acf'),
            'type'			=> 'number',
            'name'			=> 'max_rows',
            'placeholder'	=> ''
        ));
        
    }
    
    function input_admin_enqueue_scripts(){
    
        wp_enqueue_script('code-editor');
        wp_enqueue_style('code-editor');
        
    }
    
    function validate_value($valid, $value, $field, $input){
        
        return $this->textarea->validate_value($valid, $value, $field, $input);
        
	}

}

// initialize
acf_register_field_type('acfe_field_code_editor');

endif;