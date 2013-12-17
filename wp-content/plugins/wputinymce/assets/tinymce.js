(function() {
    tinymce.create('tinymce.plugins.WPUTinyMCE', {
        init: function(ed, url) {
            ed.addButton('insert_table', {
                title: 'Insert tabl',
                image: url + '/icon-list.png',
                onclick: function() {
                    ed.selection.setContent('<table>' +
                        '<thead><tr><th>Entête</th><th>Entête</th></tr></thead>' +
                        '<tbody><tr><td>Content</td><td>Content</td></tr></tbody>' +
                        '</table>');
                }
            });
        },
        createControl: function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('wpu_tinymce', tinymce.plugins.WPUTinyMCE);
})();