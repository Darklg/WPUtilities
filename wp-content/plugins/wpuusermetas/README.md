User Metas
=================

Adds extra fields to the user administration.

How to install :
---

* Put this folder to your wp-content/plugins/ folder.
* Activate the plugin in "Plugins" admin section.

How to add sections :
---

Put the code below in your theme's functions.php file. Add new sections to your convenance.

    add_filter( 'wpu_usermetas_sections', 'set_wpu_usermetas_sections', 10, 3 );
    function set_wpu_usermetas_sections( $sections ) {
        $sections['test-section'] = array(
            'name' => 'Test Section'
        );
        return $sections;
    }

How to add fields :
--

Put the code below in your theme's functions.php file. Add new fields to your convenance.

    add_filter( 'wpu_usermetas_fields', 'set_wpu_usermetas_fields', 10, 3 );
    function set_wpu_usermetas_fields( $fields ) {
        $fields['wpu_user_height'] = array(
            'name' => 'User height',
            'section' => 'test-section'
        );
        return $fields;
    }
