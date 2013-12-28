Post Metas
=================

Adds custom fields to the post administration.

How to install :
---

* Put this folder to your wp-content/plugins/ folder.
* Activate the plugin in "Plugins" admin section.

How to add boxes :
---

Put the code below in your theme's functions.php file. Add new boxes to your convenance.

    add_filter( 'wputh_post_metas_boxes', 'set_wputh_post_metas_boxes', 10, 3 );
    function set_wputh_post_metas_boxes( $boxes ) {
        $boxes['box_address'] = array(
            'name' => 'Box name',
            'post_type' => array( 'post', 'page' )
        );
        return $boxes;
    }

How to add fields :
--

Put the code below in your theme's functions.php file. Add new fields to your convenance.

    add_filter( 'wputh_post_metas_fields', 'set_wputh_post_metas_fields', 10, 3 );
    function set_wputh_post_metas_fields( $fields ) {
        $fields['wputh_post_address'] = array(
            'box' => 'box_address',
            'name' => 'Address',
        );
        return $fields;
    }


Thanks
--

* Thanks to Yummygum for his reload icon http://www.iconsweets2.com/