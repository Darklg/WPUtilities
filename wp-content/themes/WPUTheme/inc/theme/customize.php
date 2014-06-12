<?php
class WPUCustomizeTheme
{
    function __construct() {
        $this->settings = array(
            'wpu_link_color' => array(
                'label' => __('Link Color') ,
                'default' => '#6699CC',
                'section' => 'colors',
                'css_selector' => 'a',
                'css_property' => 'color'
            ) ,
            'wpu_link_color_hover' => array(
                'label' => __('Link Color :hover') ,
                'default' => '#336699',
                'section' => 'colors',
                'css_selector' => 'a:hover',
                'css_property' => 'color'
            ) ,
            'wpu_background_color' => array(
                'label' => __('Background Color') ,
                'default' => '#FFFFFF',
                'section' => 'colors',
                'css_selector' => 'body',
                'css_property' => 'background-color'
            )
        );
        add_action('customize_register', array(&$this,
            'wpu_customize_theme'
        ));
        add_action('customize_preview_init', array(&$this,
            'customizer_live_preview'
        ));
        add_action('wp_head', array(&$this,
            'display_mod'
        ));
    }

    // Display theme modifications in front-end
    function display_mod() {
        $content = '';
        foreach ($this->settings as $id => $setting) {
            $mod = strtolower(get_theme_mod($id));
            $def = strtolower($setting['default']);
            $property = trim(esc_attr($setting['css_property']));
            $selector = trim(esc_attr($setting['css_selector']));
            if (!empty($mod) && !empty($property) && !empty($selector) && $mod != $def) {
                $content.= $selector . '{' . $property . ':' . $mod . '}';
            }
        }
        if (!empty($content)) {
            echo '<style>' . $content . '</style>';
        }
    }

    // Register functions
    function wpu_customize_theme($wp_customize) {
        foreach ($this->settings as $id => $setting) {
            $wp_customize->add_setting($id, array(
                'default' => $setting['default'],
                'type' => 'theme_mod',
                'capability' => 'edit_theme_options',
                'transport' => 'postMessage',
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'wputheme_' . $id, array(
                'label' => $setting['label'],
                'section' => $setting['section'],
                'settings' => $id,
                'priority' => 10,
            )));
        }
        return $wp_customize;
    }

    // Enable live preview in admin
    function customizer_live_preview() {
        wp_enqueue_script('wputheme-themecustomizer', get_template_directory_uri() . '/js/admin/theme-customizer.js', array(
            'jquery',
            'customize-preview'
        ) , '', true);
    }
}

$WPUCustomizeTheme = new WPUCustomizeTheme();

