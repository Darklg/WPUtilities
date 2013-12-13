<?php
/*
Plugin Name: WPU Taxo Metas
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Simple admin for taxo metas
Version: 0.2.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUTaxoMetas {
    function __construct() {
        if ( is_admin() ) {
            $this->set_options();
            $this->set_admin_hooks();
        }
    }

    function set_admin_hooks() {
        $taxonomies = array();
        // Extract taxonomies
        foreach ( $this->fields as $id => $field ) {
            foreach ( $field['taxonomies'] as $taxo ) {
                $taxonomies[$taxo] = $taxo;
            }
        }

        // Add hook to edit category
        foreach ( $taxonomies as $taxo ) {
            $taxonomy = get_taxonomy( $taxo );
            if ( current_user_can( $taxonomy->cap->edit_terms ) ) {
                add_action ( 'edit_'.$taxo.'_form_fields', array( &$this, 'extra_taxo_field' ) );
                add_action ( 'edited_'.$taxo, array( &$this, 'save_extra_taxo_field' ) );
            }
        }
    }

    function save_extra_taxo_field( $t_id ) {
        global $taxonomy;
        if ( isset( $_POST['term_meta'] ) ) {
            $cat_meta = get_option( "wpu_taxometas_term_" . $t_id );
            if ( !is_array( $cat_meta ) ) {
                $cat_meta = array();
            }
            foreach ( $_POST['term_meta'] as $key => $var ) {
                if ( isset( $this->fields[$key] ) && isset( $taxonomy ) && in_array( $taxonomy, $this->fields[$key]['taxonomies'] ) ) {
                    $cat_meta[$key] = $var;
                }
            }

            //save the option array
            update_option(  "wpu_taxometas_term_" . $t_id , $cat_meta );
        }
    }

    function extra_taxo_field( $tag ) {
        $t_id = $tag->term_id;
        $term_meta = get_option( "wpu_taxometas_term_" . $t_id );
        foreach ( $this->fields as $id => $field ) {
            if ( in_array( $tag->taxonomy, $field['taxonomies'] ) ) {
                // Set value
                $value = '';
                if ( isset( $term_meta[$id] ) ) {
                    $value = stripslashes( $term_meta[$id] );
                }

                // Set ID / Name
                $htmlname = 'term_meta['.$id.']';
                $htmlid = 'term_meta_'.$id;
                $idname = 'name="'.$htmlname.'" id="'.$htmlid.'"';

                echo '<tr class="form-field"><th scope="row" valign="top"><label for="'.$htmlid.'">'.$field['label'].'</label></th>';
                echo '<td>';
                switch ( $field['type'] ) {
                case 'textarea':
                    echo '<textarea rows="5" cols="50" '.$idname.'>'.esc_textarea( $value ).'</textarea>';
                    break;
                case 'editor':
                    wp_editor( $value, $htmlid, array(
                            'textarea_name' => $htmlname,
                            'textarea_rows' => 5
                        ) );
                    break;
                case 'email':
                case 'url':
                    echo '<input type="'.$field['type'].'" '.$idname.' value="'.esc_attr( $value ).'">';
                    break;
                default :
                    echo '<input type="text" '.$idname.' value="'.esc_attr( $value ).'">';
                }
                if ( isset( $field['description'] ) ) {
                    echo '<br /><span class="description">'.esc_html( $field['description'] ).'</span>';
                }
                echo '</td></tr>';

            }
        }
    }

    function set_options() {
        // Get Fields
        $this->fields = apply_filters( 'wputaxometas_fields', array() );

        // Fix Fields
        foreach ( $this->fields as $id => $field ) {
            // Set field type
            if ( !is_array( $field ) ) {
                $this->fields[$id] = array();
            }
            // Set default taxonomies
            if ( !isset( $field['taxonomies'] ) ) {
                $this->fields[$id]['taxonomies'] = array( 'category' );
            }
            // Set default label
            if ( !isset( $field['label'] ) ) {
                $this->fields[$id]['label'] = ucwords( $id );
            }
            // Set default type
            if ( !isset( $field['type'] ) ) {
                $this->fields[$id]['type'] = 'text';
            }
        }
    }
}

add_action( 'init', 'init_WPUTaxoMetas' );
function init_WPUTaxoMetas() {
    $WPUTaxoMetas = new WPUTaxoMetas();
}
