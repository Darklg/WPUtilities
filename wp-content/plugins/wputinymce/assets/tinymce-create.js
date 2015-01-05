tinymce.create('tinymce.plugins.WPUTinyMCE', {
    init: function(ed, url) {
        for (var i = 0, item, len = wpu_tinymce_items.length; i < len; i++) {
            item = wpu_tinymce_items[i];
            ed.addButton(item.id, {
                title: item.title,
                image: item.image,
                onclick: function() {
                    ed.selection.setContent(item.html);
                }
            });
        }
    },
    createControl: function(n, cm) {
        return null;
    },
});
tinymce.PluginManager.add('wpu_tinymce', tinymce.plugins.WPUTinyMCE);