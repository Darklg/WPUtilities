<?php
/**
 * Plugin Name: Advanced Custom Fields: Extended
 * Description: Enhancement Suite which improves Advanced Custom Fields administration
 * Version:     0.8.7.2
 * Author:      ACF Extended
 * Author URI:  https://www.acf-extended.com
 * Text Domain: acfe
 */

if(!defined('ABSPATH'))
    exit;

if(!class_exists('ACFE')):

class ACFE{
    
    // Vars
    var $version = '0.8.7.2';
    var $acf = false;
    
    /*
     * Construct
     */
    function __construct(){
        // ...
    }
    
    /*
     * Initialize
     */
    function initialize(){
        
        // Constants
        $this->constants(array(
            'ACFE'          => true,
            'ACFE_FILE'     => __FILE__,
            'ACFE_PATH'     => plugin_dir_path(__FILE__),
            'ACFE_VERSION'  => $this->version,
            'ACFE_BASENAME' => plugin_basename(__FILE__),
        ));
        
        // Init
        include_once(ACFE_PATH . 'init.php');
        
        // Load
        add_action('acf/include_field_types', array($this, 'load'));
        
    }
    
    /*
     * Load
     */
    function load(){
        
        if(!$this->has_acf())
            return;
        
        // Vars
        $theme_path = acf_get_setting('acfe/theme_path', get_stylesheet_directory());
        $theme_url = acf_get_setting('acfe/theme_url', get_stylesheet_directory_uri());
        
        // Settings
        $this->settings(array(
            
            // General
            'url'                               => plugin_dir_url(__FILE__),
            'theme_path'                        => $theme_path,
            'theme_url'                         => $theme_url,
            'theme_folder'                      => parse_url($theme_url, PHP_URL_PATH),
            
            // Php
            'php'                               => true,
            'php_save'                          => "{$theme_path}/acfe-php",
            'php_load'                          => array("{$theme_path}/acfe-php"),
            'php_found'                         => false,
            
            // Json
            'json'                              => acf_get_setting('json'),
            'json_save'                         => acf_get_setting('save_json'),
            'json_load'                         => acf_get_setting('load_json'),
            'json_found'                        => false,
            
            // Modules
            'dev'                               => false,
            'modules/author'                    => true,
            'modules/categories'                => true,
            'modules/dynamic_block_types'       => true,
            'modules/dynamic_forms'             => true,
            'modules/dynamic_options_pages'     => true,
            'modules/dynamic_post_types'        => true,
            'modules/dynamic_taxonomies'        => true,
            'modules/multilang'                 => true,
            'modules/options'                   => true,
            'modules/single_meta'               => false,
            'modules/ui'                        => true,
            
        ));
        
        // Includes
        add_action('acf/init',                  array($this, 'includes'), 99);
        
        // AutoSync
        add_action('acf/include_fields',        array($this, 'autosync'), 5);
        
        // Fields
        add_action('acf/include_field_types',   array($this, 'fields'), 99);
        
        // Tools
        add_action('acf/include_admin_tools',   array($this, 'tools'));
        
        // Additional
        acfe_include('includes/core/compatibility.php');
        acfe_include('includes/core/helpers.php');
        acfe_include('includes/core/multilang.php');
        acfe_include('includes/core/settings.php');
	    acfe_include('includes/core/upgrades.php');

    }
    
    /*
     * Includes
     */
    function includes(){
        
        /*
         * Action
         */
        do_action('acfe/init');
        
        /*
         * Core
         */
        acfe_include('includes/core/enqueue.php');
        acfe_include('includes/core/menu.php');
        
        /*
         * Admin Pages
         */
        acfe_include('includes/admin/options.php');
        acfe_include('includes/admin/plugins.php');
        acfe_include('includes/admin/settings.php');
        
        /*
         * Fields
         */
        acfe_include('includes/fields/field-checkbox.php');
        acfe_include('includes/fields/field-clone.php');
        acfe_include('includes/fields/field-file.php');
        acfe_include('includes/fields/field-flexible-content.php');
        acfe_include('includes/fields/field-group.php');
        acfe_include('includes/fields/field-image.php');
        acfe_include('includes/fields/field-post-object.php');
        acfe_include('includes/fields/field-repeater.php');
        acfe_include('includes/fields/field-select.php');
        acfe_include('includes/fields/field-textarea.php');
        
        /*
         * Fields settings
         */
        acfe_include('includes/fields-settings/bidirectional.php');
        acfe_include('includes/fields-settings/data.php');
        acfe_include('includes/fields-settings/fields.php');
        acfe_include('includes/fields-settings/permissions.php');
        acfe_include('includes/fields-settings/settings.php');
        acfe_include('includes/fields-settings/validation.php');
        
        /*
         * Field Groups
         */
        acfe_include('includes/field-groups/field-group.php');
        acfe_include('includes/field-groups/field-group-category.php');
        acfe_include('includes/field-groups/field-groups.php');
        acfe_include('includes/field-groups/field-groups-local.php');
        
        /*
         * Locations
         */
        acfe_include('includes/locations/post-type-all.php');
        acfe_include('includes/locations/post-type-archive.php');
        acfe_include('includes/locations/post-type-list.php');
        acfe_include('includes/locations/taxonomy-list.php');
        
        /*
         * Modules
         */
        acfe_include('includes/modules/author.php');
        acfe_include('includes/modules/dev.php');
        acfe_include('includes/modules/dynamic-block-type.php');
        acfe_include('includes/modules/dynamic-form.php');
        acfe_include('includes/modules/dynamic-options-page.php');
        acfe_include('includes/modules/dynamic-post-type.php');
        acfe_include('includes/modules/dynamic-taxonomy.php');
        acfe_include('includes/modules/settings.php');
        acfe_include('includes/modules/single-meta.php');
        acfe_include('includes/modules/taxonomy.php');
        acfe_include('includes/modules/user.php');
        
    }
    
    /*
     * AutoSync
     */
    function autosync(){
        
        acfe_include('includes/modules/autosync.php');
        
    }
    
    /*
     * Fields
     */
    function fields(){
        
        acfe_include('includes/fields/field-advanced-link.php');
        acfe_include('includes/fields/field-button.php');
        acfe_include('includes/fields/field-code-editor.php');
        acfe_include('includes/fields/field-column.php');
        acfe_include('includes/fields/field-dynamic-message.php');
        acfe_include('includes/fields/field-forms.php');
        acfe_include('includes/fields/field-hidden.php');
        acfe_include('includes/fields/field-post-statuses.php');
        acfe_include('includes/fields/field-post-types.php');
        acfe_include('includes/fields/field-recaptcha.php');
        acfe_include('includes/fields/field-slug.php');
        acfe_include('includes/fields/field-taxonomies.php');
        acfe_include('includes/fields/field-taxonomy-terms.php');
        acfe_include('includes/fields/field-user-roles.php');
        
    }
    
    /*
     * Tools
     */
    function tools(){
        
        acfe_include('includes/admin/tools/dbt-export.php');
        acfe_include('includes/admin/tools/dbt-import.php');
        acfe_include('includes/admin/tools/dpt-export.php');
        acfe_include('includes/admin/tools/dpt-import.php');
        acfe_include('includes/admin/tools/dt-export.php');
        acfe_include('includes/admin/tools/dt-import.php');
        acfe_include('includes/admin/tools/dop-export.php');
        acfe_include('includes/admin/tools/dop-import.php');
        
        acfe_include('includes/admin/tools/form-export.php');
        acfe_include('includes/admin/tools/form-import.php');
        acfe_include('includes/admin/tools/fg-local.php');
        acfe_include('includes/admin/tools/fg-export.php');
        
    }

	/*
	 * Set Constants
	 */
    function constants($array = array()){
    
        foreach($array as $name => $value){
        
            if(!defined($name))
                define($name, $value);
        
        }
        
    }
    
    /*
	 * Set Settings
	 */
    function settings($array = array()){
        
        foreach($array as $name => $value){
        
            // update
            acf_update_setting("acfe/{$name}", $value);
        
            add_filter("acf/settings/acfe/{$name}", function($value) use($name){
            
                return apply_filters("acfe/settings/{$name}", $value);
            
            }, 5);
        
        }
        
    }
    
    /*
     * Has ACF
     */
    function has_acf(){
        
        if($this->acf)
            return true;
        
        $this->acf = class_exists('ACF') && defined('ACF_PRO') && defined('ACF_VERSION') && version_compare(ACF_VERSION, '5.8', '>=');
        
        return $this->acf;
        
    }
    
}

function acfe(){
    
    global $acfe;
    
    if(!isset($acfe)){
        
        $acfe = new ACFE();
        $acfe->initialize();
        
    }
    
    return $acfe;
    
}

acfe();

endif;