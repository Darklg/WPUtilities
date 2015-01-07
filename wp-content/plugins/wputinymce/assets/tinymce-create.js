tinymce.create('tinymce.plugins.WPUTinyMCE', {
    init: function(ed, url) {
        var regexConfirm = /({{[a-z0-9 ]+}})/g;
        for (var i = 0, item, len = wpu_tinymce_items.length; i < len; i++) {
            item = wpu_tinymce_items[i];
            ed.addButton(item.id, {
                title: item.title,
                image: item.image,
                onclick: function() {
                    var html = item.html,
                        replace,
                        matches = html.match(regexConfirm);

                    for (var i = 0, len = matches.length; i < len; i++) {
                        replace = prompt('Value for ' + matches[i] + ' ?');
                        html = html.replace(matches[i], replace);
                    }

                    ed.selection.setContent(html);
                }
            });
        }
    },
    createControl: function(n, cm) {
        return null;
    },
});
tinymce.PluginManager.add('wpu_tinymce', tinymce.plugins.WPUTinyMCE);