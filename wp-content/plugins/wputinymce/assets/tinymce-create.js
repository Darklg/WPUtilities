tinymce.create('tinymce.plugins.WPUTinyMCE', {
    init: function(ed, url) {
        'use strict';
        var regexConfirm = /({{[a-z0-9 ]+}})/g;
        for (var i = 0, item, len = wpu_tinymce_items.length; i < len; i++) {
            item = wpu_tinymce_items[i];
            ed.addButton(item.id, {
                title: item.title,
                image: item.image,
                onclick: function() {

                    var html = item.html,
                        replace,
                        match,
                        fullMatches = [],
                        matches = html.match(regexConfirm);

                    // Detect {{ vars }} in tinymce content
                    for (var i = 0, len = matches.length; i < len; i++) {
                        match = matches[i].replace(/[\{\ \}]/g, '');

                        // Check only one time per var
                        if (fullMatches.indexOf(match) >= 0) {
                            continue;
                        }
                        fullMatches.push(match);

                        // Ask a new value
                        replace = prompt('Value for "' + match + '" ?');

                        // Replace all occurrences
                        html = html.replace(new RegExp(matches[i], 'g'), replace);
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