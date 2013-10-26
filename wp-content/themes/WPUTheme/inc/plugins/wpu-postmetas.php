<?php
include dirname( __FILE__ ) . '/../../z-protect.php';

/* ----------------------------------------------------------
  Options for the plugin "WPUTH Post Metas"
---------------------------------------------------------- */

/* Meta boxes
-------------------------- */


add_filter( 'wputh_post_metas_boxes', 'set_wputh_post_metas_boxes', 10, 3 );

function set_wputh_post_metas_boxes( $boxes ) {
    // $boxes['box_address'] = array(
    //     'name' => 'Box name',
    //     'post_type' => array('post', 'page')
    // );
    return $boxes;
}

/* Meta fields
-------------------------- */

add_filter( 'wputh_post_metas_fields', 'set_wputh_post_metas_fields', 10, 3 );
function set_wputh_post_metas_fields( $fields ) {
    // $fields['wputh_post_address'] = array(
    //     'box' => 'box_address',
    //     'name' => 'Address',
    // );
    return $fields;
}
