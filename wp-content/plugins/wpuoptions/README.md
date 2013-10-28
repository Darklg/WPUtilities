Options
=================

Friendly interface for website options.

How to install :
---

* Put this folder to your wp-content/plugins/ folder.
* Activate the plugin in "Plugins" admin section.

How to add boxes :
---

Put the code below in your theme's functions.php file. Add new boxes to your convenance.

    add_filter( 'wpu_options_boxes', 'set_wpu_options_boxes', 10, 3 );

    function set_wpu_options_boxes( $boxes ) {
        $boxes['special_box'] = array(
            'name' => 'Special box'
        );
        return $boxes;
    }


How to add fields :
--

Put the code below in your theme's functions.php file. Add new fields to your convenance.

    add_filter( 'wpu_options_fields', 'set_wputh_options_fields', 10, 3 );

    function set_wputh_options_fields( $options ) {
        $options['wpu_opt_email'] = array(
            'label' => __( 'Email address', 'wputh' ),
            'box' => 'special_box',
            'type' => 'email'
        );
        return $options;
    }
