WPU TinyMCE Buttons
=================

Add new buttons to TinyMCE

How to install :
---

* Put this folder to your wp-content/plugins/ folder.
* Activate the plugin in "Plugins" admin section.

How to add buttons :
---

```php
add_filter('wputinymce_buttons', 'wputh_set_wputinymce_buttons');
function wputh_set_wputinymce_buttons($buttons) {
    $buttons['insert_table'] = array(
        'title' => 'Insert a table',
        'html' => '<table><thead><tr><th>Entête</th><th>Entête</th></tr></thead><tbody><tr><td>Content</td><td>Content</td></tr></tbody></table>'
    );
    return $buttons;
}
``