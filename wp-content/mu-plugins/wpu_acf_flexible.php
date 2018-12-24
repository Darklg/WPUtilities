<?php

/*
Plugin Name: WPU ACF Flexible
Description: Quickly generate flexible content in ACF
Version: 0.9.1
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
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => ''
        ),
        '_name' => '',
        '_prepare' => 0,
        'ajax' => 0,
        'allow_null' => 0,
        'append' => '',
        'conditional_logic' => 0,
        'default_value' => '',
        'display' => 'block',
        'filters' => array('search', 'post_type', 'taxonomy'),
        'instructions' => '',
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
        'new_lines' => '',
        'placeholder' => '',
        'prefix' => '',
        'prepend' => '',
        'preview_size' => 'thumbnail',
        'required' => 0,
        'sub_fields' => array(),
        'taxonomy' => array(),
        'ui' => 0
    );

    private $default_content = <<<EOT
<?php
###varsblockid###
?><div class="centered-container cc-block--###testblockid###">
    <div class="block--###testblockid###">
###valuesblockid###
    </div>
</div>
EOT;

    private $default_var_image = <<<EOT
$##ID## = get_sub_field('##ID##');
$##ID##_src = '';
if (is_numeric($##ID##)) {
    $##ID## = wp_get_attachment_image_src($##ID##, 'thumbnail');
    if (is_array($##ID##)) {
        $##ID##_src = $##ID##[0];
    }
}
EOT;

    private $default_value_relationship = <<<EOT
<?php
$##ID## = get_sub_field('##ID##');
if($##ID##):
foreach ($##ID## as \$tmp_post_id):
    \$thumb_url = get_the_post_thumbnail_url(\$tmp_post_id,'thumbnail');
    \$post_title = get_the_title(\$tmp_post_id);
    echo '<a href="'.get_permalink(\$tmp_post_id).'">';
    if(!empty(\$thumb_url)){
        echo '<img src="'.\$thumb_url.'" alt="'.esc_attr(\$post_title).'" />';
    }
    echo \$post_title;
    echo '</a>';
endforeach;
endif;
?>
EOT;

    private $default_value_repeater = <<<EOT
<?php if (get_sub_field('##ID##')): ?>
    <ul>
    <?php while (has_sub_field('##ID##')): ?>
        <li>
##REPEAT##
        </li>
    <?php endwhile;?>
    </ul>
<?php endif; ?>
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

        /* Choices */
        if (!isset($field['choices'])) {
            $field['choices'] = array(__('No'), __('Yes'));
        }

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
            if ($field['type'] == 'editor' || $field['type'] == 'wysiwyg') {
                $field['type'] = 'wysiwyg';
                if (!isset($field['media_upload'])) {
                    $field['media_upload'] = false;
                }
                if (!isset($field['toolbar'])) {
                    $field['toolbar'] = 'basic';
                }
            }
            if ($field['type'] == 'post' || $field['type'] == 'post_object') {
                $field['type'] = 'post_object';
                if (!isset($field['multiple'])) {
                    $field['multiple'] = 0;
                }
                if (!isset($field['return_format'])) {
                    $field['return_format'] = 'id';
                }
                if (!isset($field['ui'])) {
                    $field['ui'] = 1;
                }
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

    public function get_var_content_field($id, $sub_field, $level = 2) {
        if (!isset($sub_field['type'])) {
            $sub_field['type'] = 'text';
        }
        $vars = '';
        switch ($sub_field['type']) {
        case 'image':
            $vars = str_replace('##ID##', $id, $this->default_var_image) . "\n";
            break;
        case 'color':
        case 'color_picker':
        case 'url':
            $vars = '$' . $id . ' = get_sub_field(\'' . $id . '\');' . "\n";
            break;
        default:

        }

        if ($level < 2) {
            $vars = str_replace('get_sub_field', 'get_field', $vars);
        }

        return $vars;
    }

    public function get_value_content_field($id, $sub_field, $level = 2) {
        if (!isset($sub_field['type'])) {
            $sub_field['type'] = 'text';
        }
        $values = '';
        $classname = 'class="field-' . $id . '"';
        switch ($sub_field['type']) {
        case 'image':
            $values = '<img ' . $classname . ' src="<?php echo $' . $id . '_src ?>" alt="" />' . "\n";
            break;
        case 'url':
            $values = '<?php if(!empty($' . $id . ')): ?><a ' . $classname . ' href="<?php echo $' . $id . '; ?>"><?php echo $' . $id . '; ?></a><?php endif; ?>' . "\n";
            break;
        case 'color':
        case 'color_picker':
            $values = '<?php if(!empty($' . $id . ')): ?><div ' . $classname . ' style="background-color:<?php echo $' . $id . ' ?>;"><?php echo $' . $id . '; ?></div><?php endif; ?>' . "\n";
            break;

        case 'relationship':
            $values = str_replace('##ID##', $id, $this->default_value_relationship) . "\n";
            if ($level < 2) {
                $tmp_value = str_replace('get_sub_field', 'get_field', $tmp_value);
            }
            break;
        case 'repeater':
            $tmp_value_content = '';
            foreach ($sub_field['sub_fields'] as $sub_id => $sub_sub_field) {
                $field_value = trim($this->get_var_content_field($sub_id, $sub_sub_field));
                if (!empty($field_value)) {
                    $tmp_value_content .= '<?php ' . $field_value . ' ?>' . "\n";
                }
                $tmp_value_content .= $this->get_value_content_field($sub_id, $sub_sub_field);
            }
            $tmp_value = str_replace('##ID##', $id, $this->default_value_repeater) . "\n";
            if ($level < 2) {
                $tmp_value = str_replace('get_sub_field', 'get_field', $tmp_value);
                $tmp_value = str_replace('has_sub_field', 'has_field', $tmp_value);
            }
            $tmp_value_content = trim($tmp_value_content);
            if (!empty($tmp_value_content)) {
                $values = str_replace('##REPEAT##', $tmp_value_content, $tmp_value) . "\n";
            }

            break;
        default:
            $tag = 'div';
            if ($id == 'title') {
                $tag = 'h2';
            }
            $values = '<' . $tag . ' ' . $classname . '><?php echo ' . ($level < 2 ? 'get_field' : 'get_sub_field') . '(\'' . $id . '\') ?></' . $tag . '>' . "\n";
        }

        return $values;
    }

    public function add_field_group($content_id, $content = array()) {
        $content_name = (isset($content['name']) && !empty($content['name'])) ? $content['name'] : 'Default';
        $post_types = (isset($content['post_types']) && is_array($content['post_types'])) ? $content['post_types'] : array('post');
        $page_ids = (isset($content['page_ids']) && is_array($content['page_ids'])) ? $content['page_ids'] : array();
        $layouts = (isset($content['layouts']) && is_array($content['layouts'])) ? $content['layouts'] : array();
        $fields = (isset($content['fields']) && is_array($content['fields'])) ? $content['fields'] : array();

        /* Build Layouts */
        $base_fields = array();
        if (!empty($fields)) {
            foreach ($fields as $field_id => $field) {
                $field_key = isset($field['key']) ? $field['key'] : md5($content_id . $field_id);
                $base_fields[$field_key] = $this->set_field($field_key, $field, $field_id);
            }
        } else {
            $base_fields = array(
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
                'layouts' => array(),
                'button_label' => __('Add block'),
                'min' => '',
                'max' => ''
            );
            foreach ($layouts as $layout_id => $layout) {
                $layout_key = isset($layout['key']) ? $layout['key'] : md5($content_id . $layout_id);
                $base_fields['layouts'][$layout_key] = $this->set_field($layout_key, $layout, $layout_id);
                unset($base_fields['layouts'][$layout_key]['type']);
            }
            $base_fields = array($base_fields);
        }

        /* Init */
        if (isset($content['init_files']) && $content['init_files']) {
            if (!empty($layouts)) {
                foreach ($layouts as $layout_id => $layout) {
                    $vars = '';
                    $values = '';
                    foreach ($layout['sub_fields'] as $id => $sub_field) {
                        $vars .= $this->get_var_content_field($id, $sub_field);
                        $values .= $this->get_value_content_field($id, $sub_field);
                    }

                    $this->set_file_content($layout_id, $vars, $values);
                }
            }

            if (!empty($fields)) {
                $vars = '';
                $values = '';
                foreach ($fields as $id => $field) {
                    $vars .= $this->get_var_content_field($id, $field, 1);
                    $values .= $this->get_value_content_field($id, $field, 1);
                }
                $this->set_file_content($content_id, $vars, $values);
            }
        }

        $acf_location = array();

        /* Build post types */
        if (!empty($page_ids)) {
            $acf_location_tmp = array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page'
                )
            );

            foreach ($page_ids as $page_id) {
                $acf_location_tmp[] = array(
                    'param' => 'page',
                    'operator' => '==',
                    'value' => $page_id
                );
            }
            $acf_location[] = $acf_location_tmp;
        } else {
            foreach ($post_types as $post_type) {
                $acf_location[] = array(array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type
                ));
            }
        }

        /* Base content */
        $group = array(
            'key' => 'group_' . md5($content_id),
            'title' => $content_name,
            'fields' => $base_fields,
            'location' => $acf_location,
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

    public function set_file_content($layout_id, $vars, $values) {
        $content = str_replace('###varsblockid###', $vars, $this->default_content);
        $content = str_replace('###valuesblockid###', $values, $content);
        $content = str_replace('###testblockid###', $layout_id, $content);

        /* Remove empty */
        $content = preg_replace('/<\?php(\s\n)\?>/isU', '', $content);
        $content = preg_replace('/(?:(?:\r\n|\r|\n)){2}/s', "\n", $content);

        $file_path = $this->get_controller_path();
        $file_id = $file_path . $layout_id . '.php';
        if (!file_exists($file_id)) {
            file_put_contents($file_id, $content);
        }
    }

    public function get_controller_path() {
        $controller_path = apply_filters('wpu_acf_flexible__path', get_stylesheet_directory() . '/tpl/blocks/');
        if (!is_dir($controller_path)) {
            @mkdir($controller_path, 0755);
            @chmod($controller_path, 0755);
        }
        return $controller_path;
    }

    public function get_view_path() {
        return apply_filters('wpu_acf_flexible__path', 'blocks/');
    }
}

$wpu_acf_flexible = new wpu_acf_flexible();

function get_wpu_acf_flexible_content($group = 'blocks') {
    global $post, $wpu_acf_flexible;
    if (!have_rows($group)) {
        return '';
    }

    ob_start();

    while (have_rows($group)):
        the_row();

        /* Load controller or template file */
        $controller_path = $wpu_acf_flexible->get_controller_path();
        $layout_file = $controller_path . get_row_layout() . '.php';
        $context = array();
        if (file_exists($layout_file)) {
            include $layout_file;
        }

        /* Load view file if Timber is installed */
        if (class_exists('TimberPost') && !empty($context)) {
            $view_path = $wpu_acf_flexible->get_view_path();
            $layout_file = $view_path . get_row_layout() . '.twig';
            Timber::render($layout_file, $context);
        }

    endwhile;
    return ob_get_clean();
}

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
