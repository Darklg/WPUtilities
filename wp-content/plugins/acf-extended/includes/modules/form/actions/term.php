<?php

if(!defined('ABSPATH'))
    exit;

if(!class_exists('acfe_form_term')):

class acfe_form_term{
    
    function __construct(){
        
        /*
         * Form
         */
        add_filter('acfe/form/load/term',                                           array($this, 'load'), 10, 3);
        add_action('acfe/form/make/term',                                           array($this, 'make'), 10, 3);
        add_action('acfe/form/submit/term',                                         array($this, 'submit'), 10, 5);
        
        /*
         * Admin
         */
        add_filter('acf/prepare_field/name=acfe_form_term_save_meta',               array(acfe()->acfe_form, 'map_fields'));
        add_filter('acf/prepare_field/name=acfe_form_term_load_meta',               array(acfe()->acfe_form, 'map_fields'));
        
        add_filter('acf/prepare_field/name=acfe_form_term_save_target',             array(acfe()->acfe_form, 'map_fields_deep'));
        add_filter('acf/prepare_field/name=acfe_form_term_load_source',             array(acfe()->acfe_form, 'map_fields_deep'));
        
        add_filter('acf/prepare_field/name=acfe_form_term_save_name',               array(acfe()->acfe_form, 'map_fields_deep'));
        add_filter('acf/prepare_field/name=acfe_form_term_save_slug',               array(acfe()->acfe_form, 'map_fields_deep'));
        add_filter('acf/prepare_field/name=acfe_form_term_save_taxonomy',           array(acfe()->acfe_form, 'map_fields_deep'));
        add_filter('acf/prepare_field/name=acfe_form_term_save_parent',             array(acfe()->acfe_form, 'map_fields_deep'));
        add_filter('acf/prepare_field/name=acfe_form_term_save_description',        array(acfe()->acfe_form, 'map_fields_deep'));
        
        add_filter('acf/prepare_field/name=acfe_form_term_map_name',                array(acfe()->acfe_form, 'map_fields_deep_no_custom'));
        add_filter('acf/prepare_field/name=acfe_form_term_map_slug',                array(acfe()->acfe_form, 'map_fields_deep_no_custom'));
        add_filter('acf/prepare_field/name=acfe_form_term_map_taxonomy',            array(acfe()->acfe_form, 'map_fields_deep_no_custom'));
        add_filter('acf/prepare_field/name=acfe_form_term_map_parent',              array(acfe()->acfe_form, 'map_fields_deep_no_custom'));
        add_filter('acf/prepare_field/name=acfe_form_term_map_description',         array(acfe()->acfe_form, 'map_fields_deep_no_custom'));
        
        add_filter('acf/prepare_field/name=acfe_form_term_save_target',             array($this, 'prepare_choices'), 5);
        add_filter('acf/prepare_field/name=acfe_form_term_load_source',             array($this, 'prepare_choices'), 5);
        add_filter('acf/prepare_field/name=acfe_form_term_save_parent',             array($this, 'prepare_choices'), 5);
        
        add_action('acf/render_field/name=acfe_form_term_advanced_load',            array($this, 'advanced_load'));
        add_action('acf/render_field/name=acfe_form_term_advanced_save_args',       array($this, 'advanced_save_args'));
        add_action('acf/render_field/name=acfe_form_term_advanced_save',            array($this, 'advanced_save'));
        
    }
    
    function load($form, $current_post_id, $action){
        
        // Form
        $form_name = acf_maybe_get($form, 'form_name');
        $form_id = acf_maybe_get($form, 'form_id');
        
        // Action
        $term_action = get_sub_field('acfe_form_term_action');
        
        // Load values
        $load_values = get_sub_field('acfe_form_term_load_values');
        $load_meta = get_sub_field('acfe_form_term_load_meta');
        
        // Load values
        if(!$load_values)
            return $form;
	
	    $_term_id = get_sub_field('acfe_form_term_load_source');
        $_name = get_sub_field('acfe_form_term_map_name');
        $_slug = get_sub_field('acfe_form_term_map_slug');
        $_taxonomy = get_sub_field('acfe_form_term_map_taxonomy');
        $_parent = get_sub_field('acfe_form_term_map_parent');
        $_description = get_sub_field('acfe_form_term_map_description');
        
        // Map {field:name} {get_field:name} {query_var:name}
        $_term_id = acfe_form_map_field_value_load($_term_id, $current_post_id, $form);
	    $_name = acfe_form_map_field_value_load($_name, $current_post_id, $form);
	    $_slug = acfe_form_map_field_value_load($_slug, $current_post_id, $form);
	    $_taxonomy = acfe_form_map_field_value_load($_taxonomy, $current_post_id, $form);
	    $_parent = acfe_form_map_field_value_load($_parent, $current_post_id, $form);
	    $_description = acfe_form_map_field_value_load($_description, $current_post_id, $form);
        
        $_term_id = apply_filters('acfe/form/load/term_id',                      $_term_id, $form, $action);
        $_term_id = apply_filters('acfe/form/load/term_id/form=' . $form_name,   $_term_id, $form, $action);
        
        if(!empty($action))
            $_term_id = apply_filters('acfe/form/load/term_id/action=' . $action, $_term_id, $form, $action);
        
        // Invalid Term ID
        if(!$_term_id)
            return $form;
        
        // Name
        if(acf_is_field_key($_name)){
            
            $key = array_search($_name, $load_meta);
            
            if($key !== false)
                unset($load_meta[$key]);
	
	        $form['map'][$_name]['value'] = get_term_field('name', $_term_id);
            
        }
        
        // Slug
        if(acf_is_field_key($_slug)){
            
            $key = array_search($_slug, $load_meta);
            
            if($key !== false)
                unset($load_meta[$key]);
	
	        $form['map'][$_slug]['value'] = get_term_field('slug', $_term_id);
            
        }
        
        // Taxonomy
        if(acf_is_field_key($_taxonomy)){
            
            $key = array_search($_taxonomy, $load_meta);
            
            if($key !== false)
                unset($load_meta[$key]);
	
	        $form['map'][$_taxonomy]['value'] = get_term_field('taxonomy', $_term_id);
            
        }
        
        // Parent
        if(acf_is_field_key($_parent)){
            
            $key = array_search($_parent, $load_meta);
            
            if($key !== false)
                unset($load_meta[$key]);
            
            $form['map'][$_parent]['value'] = get_term_field('parent', $_term_id);
            
        }
        
        // Description
        if(acf_is_field_key($_description)){
            
            $key = array_search($_description, $load_meta);
            
            if($key !== false)
                unset($load_meta[$key]);
	
	        $form['map'][$_description]['value'] = get_term_field('description', $_term_id);
            
        }
        
        // Load others values
        if(!empty($load_meta)){
            
            foreach($load_meta as $field_key){
                
                $field = acf_get_field($field_key);
                
                $form['map'][$field_key]['value'] = acf_get_value('term_' . $_term_id, $field);
                
            }
            
        }
        
        return $form;
        
    }
    
    function make($form, $current_post_id, $action){
        
        // Form
        $form_name = acf_maybe_get($form, 'form_name');
        $form_id = acf_maybe_get($form, 'form_id');
    
        // Prepare
        $prepare = true;
        $prepare = apply_filters('acfe/form/prepare/term',                          $prepare, $form, $current_post_id, $action);
        $prepare = apply_filters('acfe/form/prepare/term/form=' . $form_name,       $prepare, $form, $current_post_id, $action);
    
        if(!empty($action))
            $prepare = apply_filters('acfe/form/prepare/term/action=' . $action,    $prepare, $form, $current_post_id, $action);
    
        if($prepare === false)
            return;
        
        // Action
        $term_action = get_sub_field('acfe_form_term_action');
	
	    // Load values
	    $load_values = get_sub_field('acfe_form_term_load_values');
        
        // Pre-process
        $_description_group = get_sub_field('acfe_form_term_save_description_group');
        $_description = $_description_group['acfe_form_term_save_description'];
        $_description_custom = $_description_group['acfe_form_term_save_description_custom'];
        
        if($_description === 'custom')
            $_description = $_description_custom;
	
	    $map = array();
	
	    if($load_values){
		
		    // Mapping
		    $map = array(
			    'name'        => get_sub_field( 'acfe_form_term_map_name' ),
			    'slug'        => get_sub_field( 'acfe_form_term_map_slug' ),
			    'taxonomy'    => get_sub_field( 'acfe_form_term_map_taxonomy' ),
			    'parent'      => get_sub_field( 'acfe_form_term_map_parent' ),
			    'description' => get_sub_field( 'acfe_form_term_map_description' ),
		    );
		
	    }
        
        // Fields
        $fields = array(
            'target'        => get_sub_field('acfe_form_term_save_target'),
            'name'          => get_sub_field('acfe_form_term_save_name'),
            'slug'          => get_sub_field('acfe_form_term_save_slug'),
            'taxonomy'      => get_sub_field('acfe_form_term_save_taxonomy'),
            'parent'        => get_sub_field('acfe_form_term_save_parent'),
            'description'   => $_description,
        );
        
        $data = acfe_form_map_vs_fields($map, $fields, $current_post_id, $form);
        
        // args
        $args = array();
        
        // Insert term
        $_term_id = 0;
        
        // Update term
        if($term_action === 'update_term'){
            
            $_term_id = $data['target'];
            
            // Invalid Term ID
            if(!$_term_id)
                return;
            
            $args['ID'] = $_term_id;
            
        }
        
        // Name
        if(!empty($data['name'])){
	
	        if(is_array($data['name']))
		        $data['name'] = acfe_array_to_string($data['name']);
            
            $args['name'] = $data['name'];
            
        }
        
        // Slug
        if(!empty($data['slug'])){
	
	        if(is_array($data['name']))
		        $data['name'] = acfe_array_to_string($data['name']);
            
            $args['slug'] = $data['slug'];
            
        }
        
        // Taxonomy
        if(!empty($data['taxonomy'])){
	
	        if(is_array($data['name']))
		        $data['name'] = acfe_array_to_string($data['name']);
            
            $args['taxonomy'] = $data['taxonomy'];
            
        }
        
        // Parent
        if(!empty($data['parent'])){
	
	        if(is_array($data['name']))
		        $data['name'] = acfe_array_to_string($data['name']);
            
            $args['parent'] = $data['parent'];
            
        }
        
        // Description
        if(!empty($data['description'])){
	
	        if(is_array($data['name']))
		        $data['name'] = acfe_array_to_string($data['name']);
            
            $args['description'] = $data['description'];
            
        }
        
        $args = apply_filters('acfe/form/submit/term_args',                     $args, $term_action, $form, $action);
        $args = apply_filters('acfe/form/submit/term_args/form=' . $form_name,  $args, $term_action, $form, $action);
        
        if(!empty($action))
            $args = apply_filters('acfe/form/submit/term_args/action=' . $action, $args, $term_action, $form, $action);
        
        // Insert Term
        if($term_action === 'insert_term'){
            
            if(!isset($args['name']) || !isset($args['taxonomy'])){
                
                $args = false;
                
            }
            
        }
        
        if($args === false)
            return;
        
        // Insert Term
        if($term_action === 'insert_term'){
            
            $_insert_term = wp_insert_term($args['name'], $args['taxonomy'], $args);
            
        }
        
        // Update Term
        elseif($term_action === 'update_term'){
            
            $_insert_term = wp_update_term($args['ID'], $args['taxonomy'], $args);
            
        }
        
        // Term Error
        if(is_wp_error($_insert_term))
            return;
        
        $_term_id = $_insert_term['term_id'];
        
        $args['ID'] = $_term_id;
        
        // Save meta
        do_action('acfe/form/submit/term',                     $_term_id, $term_action, $args, $form, $action);
        do_action('acfe/form/submit/term/name=' . $form_name,  $_term_id, $term_action, $args, $form, $action);
        
        if(!empty($action))
            do_action('acfe/form/submit/term/action=' . $action, $_term_id, $term_action, $args, $form, $action);
        
    }
    
    function submit($_term_id, $term_action, $args, $form, $action){
    
        // Form name
        $form_name = acf_maybe_get($form, 'form_name');
    
        // Get term array
        $term_object = get_term($_term_id, $args['taxonomy'], 'ARRAY_A');
    
        $term_object['permalink'] = get_term_link($_term_id, $term_object['taxonomy']);
        $term_object['admin_url'] = admin_url('term.php?tag_ID=' . $_term_id . '&taxonomy=' . $term_object['taxonomy']);
    
        $term_object = apply_filters('acfe/form/query_var/term',                    $term_object, $_term_id, $term_action, $args, $form, $action);
        $term_object = apply_filters('acfe/form/query_var/term/form=' . $form_name, $term_object, $_term_id, $term_action, $args, $form, $action);
        $term_object = apply_filters('acfe/form/query_var/term/action=' . $action,  $term_object, $_term_id, $term_action, $args, $form, $action);
    
        // Query var
        $query_var = acfe_form_unique_action_id($form, 'term');
    
        if(!empty($action))
            $query_var = $action;
    
        // Set Query Var
        set_query_var($query_var, $term_object);
        
        // Meta save
        $save_meta = get_sub_field('acfe_form_term_save_meta');
        
        if(!empty($save_meta)){
            
            $meta = acfe_form_filter_meta($save_meta, $_POST['acf']);
            
            if(!empty($meta)){
                
                // Backup original acf post data
                $acf = $_POST['acf'];
                
                // Save meta fields
                acf_save_post('term_' . $_term_id, $meta);
                
                // Restore original acf post data
                $_POST['acf'] = $acf;
            
            }
            
        }
        
    }
    
    /**
     *  Term: Select2 Choices
     */
    function prepare_choices($field){
        
        $field['choices']['current_term'] = 'Current: Term';
        $field['choices']['current_term_parent'] = 'Current: Term Parent';
        
        if(acf_maybe_get($field, 'value')){
            
            $value = $field['value'];
            
            if(is_array($value))
                $value = $value[0];
            
            $term = get_term($value);
            
            if($term){
                
                $field['choices'][$term->term_id] = $term->name;
                
            }
        
        }
        
        return $field;
        
    }
    
    function advanced_load($field){
        
        $form_name = 'my_form';
        
        if(acf_maybe_get($field, 'value'))
            $form_name = get_field('acfe_form_name', $field['value']);
        
        ?>You may use the following hooks:<br /><br />
<pre>
add_filter('acfe/form/load/term_id', 'my_form_term_values_source', 10, 3);
add_filter('acfe/form/load/term_id/form=<?php echo $form_name; ?>', 'my_form_term_values_source', 10, 3);
add_filter('acfe/form/load/term_id/action=my-term-action', 'my_form_term_values_source', 10, 3);
</pre>
<br />
<pre>
/**
 * @int     $term_id    Term ID used as source
 * @array   $form       The form settings
 * @string  $action     The action alias name
 */
add_filter('acfe/form/load/term_id/form=<?php echo $form_name; ?>', 'my_form_term_values_source', 10, 3);
function my_form_term_values_source($term_id, $form, $action){
    
    /**
     * Force to load values from the term ID 45
     */
    $term_id = 45;
    
    
    /**
     * Return
     */
    return $term_id;
    
}
</pre><?php
        
    }
    
    function advanced_save_args($field){
        
        $form_name = 'my_form';
        
        if(acf_maybe_get($field, 'value'))
            $form_name = get_field('acfe_form_name', $field['value']);
        
        ?>You may use the following hooks:<br /><br />
<pre>
add_filter('acfe/form/submit/term_args', 'my_form_term_args', 10, 4);
add_filter('acfe/form/submit/term_args/form=<?php echo $form_name; ?>', 'my_form_term_args', 10, 4);
add_filter('acfe/form/submit/term_args/action=my-term-action', 'my_form_term_args', 10, 4);
</pre>
<br />
<pre>
/**
 * @array   $args   The generated term arguments
 * @string  $type   Action type: 'insert_term' or 'update_term'
 * @array   $form   The form settings
 * @string  $action The action alias name
 */
add_filter('acfe/form/submit/term_args/form=<?php echo $form_name; ?>', 'my_form_term_args', 10, 4);
function my_form_term_args($args, $type, $form, $action){
    
    /**
     * Force specific description if the action type is 'insert_term'
     */
    if($type === 'insert_term'){
        
        $args['description'] = 'My term description';
        
    }
    
    
    /**
     * Get the form input value named 'my_field'
     * This is the value entered by the user during the form submission
     */
    $my_field = get_field('my_field');
    
    
    /**
     * Get the field value 'my_field' from the post ID 145
     */
    $my_post_field = get_field('my_field', 145);
    
    
    /**
     * Return arguments
     * Note: Return false will stop post & meta insert/update
     */
    return $args;
    
}
</pre><?php
        
    }
    
    function advanced_save($field){
        
        $form_name = 'my_form';
        
        if(acf_maybe_get($field, 'value'))
            $form_name = get_field('acfe_form_name', $field['value']);
        
        ?>You may use the following hooks:<br /><br />
<pre>
add_action('acfe/form/submit/term', 'my_form_term_save', 10, 5);
add_action('acfe/form/submit/term/form=<?php echo $form_name; ?>', 'my_form_term_save', 10, 5);
add_action('acfe/form/submit/term/action=my-term-action', 'my_form_term_save', 10, 5);
</pre>
<br />
<pre>
/**
 * @int     $term_id    The targeted term ID
 * @string  $type       Action type: 'insert_term' or 'update_term'
 * @array   $args       The generated term arguments
 * @array   $form       The form settings
 * @string  $action     The action alias name
 *
 * Note: At this point the term is already saved into the database
 */
add_action('acfe/form/submit/term/form=<?php echo $form_name; ?>', 'my_form_term_save', 10, 5);
function my_form_term_save($term_id, $type, $args, $form, $action){

    /**
     * Get the form input value named 'my_field'
     * This is the value entered by the user during the form submission
     */
    $my_field = get_field('my_field');
    
    
    /**
     * Get the field value 'my_field' from the currently saved term
     */
    $my_term_field = get_field('my_field', 'term_' . $term_id);
    
}
</pre><?php
        
    }
    
}

new acfe_form_term();

endif;