<?php

/*
Plugin Name: WPU ACF Flexible
Description: Quickly generate flexible content in ACF
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class wpu_acf_flexible {

    /* Base */
    private $base_field = array(
        'key' => 'field_598c51a00af6c',
        'label' => 'Title',
        'name' => 'title',
        'type' => 'text',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => ''
        ),
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => '',
        'min' => '',
        'preview_size' => 'thumbnail',
        'library' => 'all',
        'max' => '',
        'display' => 'block',
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'ajax' => 0,
        'sub_fields' => array()
    );

    public function __construct() {
        add_action('init', array(&$this, 'init'));
    }

    public function init() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $contents = apply_filters('wpu_acf_flexible_content', array());
        foreach ($contents as $id => $content) {
            $this->add_field_group($id, $content['name'], $content['post_types'], $content['layouts']);
        }
    }

    public function set_field($id, $field) {
        $acf_field = $this->base_field;

        /* Label */
        if (!isset($field['label'])) {
            $field['label'] = $id;
        }
        if (!isset($field['title'])) {
            $field['title'] = $field['label'];
        }
        if (!isset($field['name'])) {
            $field['name'] = $field['label'];
        }

        /* Return */
        if (isset($field['type'])) {
            if ($field['type'] == 'select' && !isset($field['return_format'])) {
                $field['return_format'] = 'value';
            }
            if ($field['type'] == 'image' && !isset($field['return_format'])) {
                $field['return_format'] = 'id';
            }
        }

        foreach ($field as $field_key => $field_value) {
            $acf_field[$field_key] = $field_value;
        }
        $acf_field['key'] = $id;

        if (isset($acf_field['sub_fields']) && is_array($acf_field['sub_fields'])) {
            $sub_fields = array();
            foreach ($acf_field['sub_fields'] as $sub_field_id => $sub_field) {
                $sub_fields[$sub_field_id] = $this->set_field($id . $sub_field_id, $sub_field);
            }
            $acf_field['sub_fields'] = $sub_fields;
        }
        return $acf_field;

    }

    public function add_field_group($content_id, $content_name, $post_types = array(), $layouts = array()) {
        /* Build Layouts */
        $layouts_acf = array();
        foreach ($layouts as $layout_id => $layout) {
            $layout_key = md5($content_id . $layout_id);
            $layouts_acf[$layout_key] = $this->set_field($layout_key, $layout);
            unset($layouts_acf[$layout_key]['type']);
        }

        /* Build post types */
        $acf_post_types = array();
        foreach ($post_types as $post_type) {
            $acf_post_types[] = array(array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => $post_type
            ));
        }

        /* Base content */
        $group = array(
            'key' => 'group_' . md5($content_id),
            'title' => $content_name,
            'fields' => array(
                array(
                    'key' => 'field_' . md5($content_id),
                    'label' => $content_name,
                    'name' => $content_id,
                    'type' => 'flexible_content',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => ''
                    ),
                    'layouts' => $layouts_acf,
                    'button_label' => 'Ajouter un bloc',
                    'min' => '',
                    'max' => ''
                )
            ),
            'location' => $acf_post_types,
            'menu_order' => 0,
            'position' => 'acf_after_title',
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array(
                0 => 'the_content'
            ),
            'active' => 1,
            'description' => ''
        );

        acf_add_local_field_group($group);

    }
}

$wpu_acf_flexible = new wpu_acf_flexible();

/*
add_filter('wpu_acf_flexible_content', 'example_wpu_acf_flexible_content', 10, 1);
function example_wpu_acf_flexible_content($contents) {
    $contents['blocks'] = array(
        'post_types' => array(
            'post',
            'page'
        ),
        'name' => 'Blocks',
        'layouts' => array(
            'basique' => array(
                'label' => 'Basique',
                'sub_fields' => array(
                    'title' => array(
                        'label' => 'Titre'
                    ),
                    'content' => array(
                        'label' => 'Contenu',
                        'type' => 'textarea'
                    ),
                    'link_url' => array(
                        'label' => 'URL Bouton',
                        'type' => 'url'
                    ),
                    'link_text' => array(
                        'label' => 'Texte Bouton',
                        'type' => 'url'
                    )
                )
            ),
            'icons' => array(
                'label' => 'Icones',
                'sub_fields' => array(
                    'title' => array(
                        'label' => 'Titre'
                    ),
                    'icons' => array(
                        'label' => 'Icones',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            'icons_title' => array(
                                'label' => 'Titre'
                            ),
                            'icons_image' => array(
                                'label' => 'Image',
                                'type' => 'image'
                            )
                        )
                    )
                )
            )
        )
    );
    return $contents;
}
*/
