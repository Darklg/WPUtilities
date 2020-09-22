<?php

if(!defined('ABSPATH'))
    exit;

if(!class_exists('acfe_field_user_roles')):

class acfe_field_user_roles extends acf_field{
    
    function __construct(){
        
        $this->name = 'acfe_user_roles';
        $this->label = __('User Roles', 'acfe');
        $this->category = 'relational';
        $this->defaults = array(
            'user_role'             => array(),
            'field_type'            => 'checkbox',
            'multiple'              => 0,
			'allow_null'            => 0,
			'choices'               => array(),
			'default_value'         => '',
			'ui'                    => 0,
			'ajax'                  => 0,
			'placeholder'           => '',
			'search_placeholder'	=> '',
            'layout'                => '',
			'toggle'                => 0,
			'allow_custom'          => 0,
        );
        
        parent::__construct();
        
    }
    
    function render_field_settings($field){
        
        if(isset($field['default_value']))
            $field['default_value'] = acf_encode_choices($field['default_value'], false);
        
        // Allow User Role
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow User Role','acf'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'user_role',
			'choices'		=> acfe_get_roles(),
			'multiple'		=> 1,
			'ui'			=> 1,
			'allow_null'	=> 1,
			'placeholder'	=> __("All user roles",'acf'),
		));
        
        // field_type
        acf_render_field_setting($field, array(
            'label'			=> __('Appearance','acf'),
            'instructions'	=> __('Select the appearance of this field', 'acf'),
            'type'			=> 'select',
            'name'			=> 'field_type',
            'optgroup'		=> true,
            'choices'		=> array(
                'checkbox'  => __('Checkbox', 'acf'),
                'radio'     => __('Radio Buttons', 'acf'),
                'select'    => _x('Select', 'noun', 'acf')
            )
        ));
        
        // default_value
		acf_render_field_setting( $field, array(
			'label'			=> __('Default Value','acf'),
			'instructions'	=> __('Enter each default value on a new line','acf'),
			'name'			=> 'default_value',
			'type'			=> 'textarea',
		));
        
		// Select + Radio: allow_null
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow Null?','acf'),
			'instructions'	=> '',
			'name'			=> 'allow_null',
			'type'			=> 'true_false',
			'ui'			=> 1,
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                ),
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'radio',
                    ),
                ),
            )
		));
        
        // Select: multiple
		acf_render_field_setting( $field, array(
			'label'			=> __('Select multiple values?','acf'),
			'instructions'	=> '',
			'name'			=> 'multiple',
			'type'			=> 'true_false',
			'ui'			=> 1,
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                ),
            )
		));
        
        // Select: ui
		acf_render_field_setting( $field, array(
			'label'			=> __('Stylised UI','acf'),
			'instructions'	=> '',
			'name'			=> 'ui',
			'type'			=> 'true_false',
			'ui'			=> 1,
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                ),
            )
		));
				
		
		// Select: ajax
		acf_render_field_setting( $field, array(
			'label'			=> __('Use AJAX to lazy load choices?','acf'),
			'instructions'	=> '',
			'name'			=> 'ajax',
			'type'			=> 'true_false',
			'ui'			=> 1,
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                    array(
                        'field'     => 'ui',
                        'operator'  => '==',
                        'value'     => 1,
                    ),
                ),
            )
		));
        
        // placeholder
        acf_render_field_setting($field, array(
            'label'			=> __('Placeholder','acf'),
            'instructions'	=> __('Appears within the input','acf'),
            'type'			=> 'text',
            'name'			=> 'placeholder',
            'placeholder'   => _x('Select', 'verb', 'acf'),
            'conditional_logic' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                    array(
                        'field'     => 'allow_null',
                        'operator'  => '==',
                        'value'     => '1',
                    ),
                ),
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                    array(
                        'field'     => 'ui',
                        'operator'  => '==',
                        'value'     => '1',
                    ),
                    array(
                        'field'     => 'allow_null',
                        'operator'  => '==',
                        'value'     => '1',
                    ),
                ),
            )
        ));
        
        // search placeholder
        acf_render_field_setting($field, array(
            'label'			=> __('Search Input Placeholder','acf'),
            'instructions'	=> __('Appears within the search input','acf'),
            'type'			=> 'text',
            'name'			=> 'search_placeholder',
            'placeholder'   => _x('Select', 'verb', 'acf'),
            'conditional_logic' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                    array(
                        'field'     => 'ui',
                        'operator'  => '==',
                        'value'     => '1',
                    ),
                ),
            )
        ));
		
		// Radio: other_choice
		acf_render_field_setting( $field, array(
			'label'			=> __('Other','acf'),
			'instructions'	=> '',
			'name'			=> 'other_choice',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'message'		=> __("Add 'other' choice to allow for custom values", 'acf'),
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'radio',
                    ),
                ),
            )
		));
        
        // Checkbox: layout
		acf_render_field_setting( $field, array(
			'label'			=> __('Layout','acf'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'layout',
			'layout'		=> 'horizontal', 
			'choices'		=> array(
				'vertical'		=> __("Vertical",'acf'), 
				'horizontal'	=> __("Horizontal",'acf')
			),
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'checkbox',
                    ),
                ),
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'radio',
                    ),
                ),
            )
		));
        
        // Checkbox: toggle
        acf_render_field_setting( $field, array(
			'label'			=> __('Toggle','acf'),
			'instructions'	=> __('Prepend an extra checkbox to toggle all choices','acf'),
			'name'			=> 'toggle',
			'type'			=> 'true_false',
			'ui'			=> 1,
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'checkbox',
                    ),
                ),
            )
		));
        
        // Checkbox: other_choice
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow Custom','acf'),
			'instructions'	=> '',
			'name'			=> 'allow_custom',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'message'		=> __("Allow 'custom' values to be added", 'acf'),
            'conditions' => array(
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'checkbox',
                    ),
                ),
                array(
                    array(
                        'field'     => 'field_type',
                        'operator'  => '==',
                        'value'     => 'select',
                    ),
                    array(
                        'field'     => 'ui',
                        'operator'  => '==',
                        'value'     => '1',
                    ),
                )
            )
		));
        
    }
    
    function prepare_field($field){
        
        // Set Field Type
        $field['type'] = $field['field_type'];
        
        // Choices
        $field['choices'] = acfe_get_roles($field['user_role']);
        
        // Allow Custom
        if(acf_maybe_get($field, 'allow_custom')){
            
            if($value = acf_maybe_get($field, 'value')){
                
                $value = acf_get_array($value);
                
                foreach($value as $v){
                    
                    if(isset($field['choices'][$v]))
                        continue;
                    
                    $field['choices'][$v] = $v;
                    
                }
                
            }
            
        }
        
        return $field;
        
    }

}

// initialize
acf_register_field_type('acfe_field_user_roles');

endif;