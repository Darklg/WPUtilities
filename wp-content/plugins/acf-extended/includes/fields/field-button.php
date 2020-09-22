<?php

if(!defined('ABSPATH'))
    exit;

if(!class_exists('acfe_field_button')):

class acfe_field_button extends acf_field{
    
    function __construct(){

        $this->name = 'acfe_button';
        $this->label = __('Button', 'acfe');
        $this->category = 'basic';
        $this->defaults = array(
            'button_value' => __('Submit', 'acfe'),
            'button_type' => 'button',
            'button_before' => '',
            'button_after' => '',
            'button_class' => 'button button-secondary',
            'button_id' => '',
        );
        
        add_action('wp_ajax_acfe/fields/button',        array($this, 'ajax_request'), 99);
        add_action('wp_ajax_nopriv_acfe/fields/button', array($this, 'ajax_request'), 99);

        parent::__construct();

    }
    
    function ajax_request(){
        
        /**
         * @bool/string $_POST['post_id'] Current post ID
         * @string      $_POST['field_key'] Button's field key
         * @string      $_POST['field_name'] Button's field name
         */
        
        $field_name = acf_maybe_get_POST('field_name');
        $field_key = acf_maybe_get_POST('field_key');
        $post_id = acf_maybe_get_POST('post_id');
        $field = acf_get_field($field_key);
        
        do_action('acfe/fields/button',                     $field, $post_id);
        do_action('acfe/fields/button/name=' . $field_name, $field, $post_id);
        do_action('acfe/fields/button/key=' . $field_key,   $field, $post_id);
        
        die;
        
    }
      
    function render_field_settings($field){
        
        // Value
        acf_render_field_setting($field, array(
            'label'         => __('Button value', 'acfe'),
            'instructions'  => __('Set a default button value', 'acfe'),
            'type'          => 'text',
            'name'          => 'button_value',
            'default_value' => __('Submit', 'acfe')
        ));
        
        // Type
        acf_render_field_setting($field, array(
            'label'         => __('Button type', 'acfe'),
            'instructions'  => __('Choose the button type', 'acfe'),
            'type'          => 'radio',
            'name'          => 'button_type',
            'default_value' => 'button',
            'layout'        => 'horizontal',
            'choices'       => array(
                'button' => __('Button', 'acfe'),
                'submit' => __('Submit', 'acfe'),
            ),
        ));
        
        // class
        acf_render_field_setting($field, array(
            'label'			=> __('Button attributes','acf'),
            'instructions'	=> '',
            'type'			=> 'text',
            'name'			=> 'button_class',
            'prepend'		=> __('class', 'acf'),
        ));
        
        // id
        acf_render_field_setting($field, array(
            'label'			=> '',
            'instructions'	=> '',
            'type'			=> 'text',
            'name'			=> 'button_id',
            'prepend'		=> __('id', 'acf'),
            '_append'       => 'button_class'
        ));
        
        // Before HTML
        acf_render_field_setting($field, array(
            'label'         => __('Before HTML', 'acfe'),
            'instructions'  => __('Custom HTML before the button', 'acfe'),
            'type'          => 'acfe_code_editor',
            'name'          => 'button_before',
            'rows'          => 4,
        ));
        
        // After HTML
        acf_render_field_setting($field, array(
            'label'         => __('After HTML', 'acfe'),
            'instructions'  => __('Custom HTML after the button', 'acfe'),
            'type'          => 'acfe_code_editor',
            'name'          => 'button_after',
            'rows'          => 4,
        ));
        
        // Ajax
        acf_render_field_setting($field, array(
            'label'         => __('Ajax call', 'acfe'),
            'instructions'  => __('Trigger ajax event on click', 'acfe'),
            'name'          => 'button_ajax',
            'type'			=> 'true_false',
			'ui'			=> 1,
        ));
        
        ob_start();
        ?>
        Write your own Ajax return data using the following hook:<br /><br />
<pre>
add_action('acfe/fields/button/name=my_button', 'my_acf_button_ajax', 10, 2);
function my_acf_button_ajax($field, $post_id){
    
    /**
     * @array       $field      Field array
     * @bool/string $post_id    Current post ID
     */
    
    wp_send_json('Success!');
    
}
</pre>
<br />
You can get access to Javascript ajax call using the following JS hooks:<br /><br />
<pre>
acf.addAction('acfe/fields/button/before/name=my_button', function($el, data){
    
    // $el
    
});

acf.addAction('acfe/fields/button/success/name=my_button', function(response, $el, data){
    
    // response
    // $el
    // data
    
});

acf.addAction('acfe/fields/button/complete/name=my_button', function(response, $el, data){
    
    // response
    // $el
    // data
    
});
</pre>
        <?php
        
        $message = ob_get_clean();
        
        // ajax instructions
        acf_render_field_setting($field, array(
            'label'			=> __('Ajax instructions','acf'),
            'instructions'	=> '',
            'type'			=> 'message',
            'name'			=> 'instructions',
            'message'       => $message,
            'new_lines'     => false,
            'conditional_logic' => array(
                array(
                    array(
                        'field'     => 'button_ajax',
                        'operator'  => '==',
                        'value'     => '1',
                    )
                )
            )
        ));
        
    }
    
    function render_field($field){
        
        // Before
        if(isset($field['button_before']) && !empty($field['button_before'])){
            
            echo $field['button_before'];
            
        }
        
        $ajax = false;
        $button_ajax = $field['button_ajax'];
        
        if($button_ajax)
            $ajax = 'data-ajax="1"';
        
        // Button
        if($field['button_type'] === 'button'){
            
            echo '<button 
                id="' . esc_attr($field['button_id']) . '" 
                class="' . esc_attr($field['button_class']) . '" 
                ' . $ajax . '
                >' . esc_attr($field['button_value']) . '</button>';
        
        // Submit
        }elseif($field['button_type'] === 'submit'){
            
            echo '<input 
                type="submit" 
                id="' . esc_attr($field['button_id']) . '" 
                class="' . esc_attr($field['button_class']) . '" 
                value="' . esc_attr($field['button_value']) . '" 
                ' . $ajax . '
                />';
            
        }
        
        // After
        if(isset($field['button_after']) && !empty($field['button_after'])){
            
            echo $field['button_after'];
            
        }
        
    }
    
}

// initialize
acf_register_field_type('acfe_field_button');

endif;