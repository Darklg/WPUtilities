<?php

/*
Plugin Name: WPU ACF Flexible
Description: Quickly generate flexible content in ACF
Version: 0.2.0
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
        'ajax' => 0,
        'allow_null' => 0,
        'append' => '',
        'default_value' => '',
        'display' => 'block',
        'library' => 'all',
        'max' => '',
        'max_height' => '',
        'max_size' => '',
        'max_width' => '',
        'maxlength' => '',
        'mime_types' => '',
        'min' => '',
        'min_height' => '',
        'min_size' => '',
        'min_width' => '',
        'multiple' => 0,
        'placeholder' => '',
        'prepend' => '',
        'preview_size' => 'thumbnail',
        'sub_fields' => array(),
        'ui' => 0
    );

    private $default_content = <<<EOT
<?php
###varsblockid###
?><div class="centered-container cc-block--testblockid">
    <div class="block--testblockid">
###valuesblockid###
    </div>
</div>


EOT;

    public function __construct() {
        add_action('init', array(&$this, 'init'));
    }

    public function init() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }
        $contents = apply_filters('wpu_acf_flexible_content', array());
        foreach ($contents as $id => $content) {
            $this->add_field_group($id, $content);
        }
    }

    public function set_field($id, $field, $field_id) {
        $acf_field = $this->base_field;

        /* Label */
        if (!isset($field['label'])) {
            $field['label'] = $id;
        }
        if (!isset($field['title'])) {
            $field['title'] = $field['label'];
        }
        $field['name'] = $field_id;

        /* Return */
        if (isset($field['type'])) {
            if ($field['type'] == 'select' && !isset($field['return_format'])) {
                $field['return_format'] = 'value';
            }
            if ($field['type'] == 'image' && !isset($field['return_format'])) {
                $field['return_format'] = 'id';
            }
            if ($field['type'] == 'color') {
                $field['type'] = 'color_picker';
            }
        }

        foreach ($field as $field_key => $field_value) {
            $acf_field[$field_key] = $field_value;
        }
        $acf_field['key'] = $id;

        if (isset($acf_field['sub_fields']) && is_array($acf_field['sub_fields'])) {
            $sub_fields = array();
            foreach ($acf_field['sub_fields'] as $sub_field_id => $sub_field) {
                $sub_fields[$sub_field_id] = $this->set_field($id . $sub_field_id, $sub_field, $sub_field_id);
            }
            $acf_field['sub_fields'] = $sub_fields;
        }
        return $acf_field;

    }

    public function add_field_group($content_id, $content = array()) {
        $content_name = (isset($content['name']) && !empty($content['name'])) ? $content['name'] : 'Default';
        $post_types = (isset($content['post_types']) && is_array($content['post_types'])) ? $content['post_types'] : array('post');
        $layouts = (isset($content['layouts']) && is_array($content['layouts'])) ? $content['layouts'] : array();

        /* Build Layouts */
        $layouts_acf = array();
        foreach ($layouts as $layout_id => $layout) {
            $layout_key = md5($content_id . $layout_id);
            $layouts_acf[$layout_key] = $this->set_field($layout_key, $layout, $layout_id);
            unset($layouts_acf[$layout_key]['type']);
        }

        /* Init */
        if (isset($content['init_files']) && $content['init_files']) {
            foreach ($layouts as $layout_id => $layout) {

                $vars = '';
                $values = '';
                foreach ($layout['sub_fields'] as $id => $sub_field) {
                    if (!isset($sub_field['type']) || $sub_field['type'] == 'text') {
                        $vars .= '$' . $id . ' = get_sub_field(\'' . $id . '\');' . "\n";
                        $values .= '<div><?php echo $' . $id . ' ?></div>' . "\n";
                    }
                }

                $content = str_replace('###varsblockid###', $vars, $this->default_content);
                $content = str_replace('###valuesblockid###', $values, $content);
                $content = str_replace('testblockid', $layout_id, $content);

                $file_id = get_stylesheet_directory() . '/tpl/blocks/' . $layout_id . '.php';
                if (!file_exists($file_id)) {
                    file_put_contents($file_id, $content);
                }
            }
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
