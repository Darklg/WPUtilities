Taxo Metas
=================

Adds extra fields to the taxonomy administration.

How to install :
---

* Put this folder to your wp-content/plugins/ folder.
* Activate the plugin in "Plugins" admin section.

How to add fields :
---

Put the code below in your theme's functions.php file. Add new fields to your convenance.

    add_action( 'wputaxometas_fields', 'set_wputaxometas_fields' );
    function set_wputaxometas_fields( $fields ) {
        $fields['category_long_description'] = array(
            'label' => 'Test field',
            'taxonomies' => array( 'category' ),
            'description' => 'a long description',
            'type' => 'textarea'
        );
        return $fields;
    }

Fields parameters :
---

* "label" : String (optional) / Adds a label to the field administration. Default to ID value.
* "taxonomies" : Array (optional) / Set the taxonomies for which the meta will be used. Default to array( 'category' )
* "description" : String (optional) / Add a long description to the field administration to help the user in filling this field.
* "type" : String (optional) / Set a kind of form field. Default to "text".

Fields types :
---

* "text" : input type text.
* "email" : input type email.
* "url" : input type url.
* "textarea" : textarea field.
* "editor" : the WYSIWYG editor used in the content of a post.
