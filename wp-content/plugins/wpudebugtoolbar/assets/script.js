(function(){
    'use strict';
    window.wpudebugtoolbar_r = function(f) {
        /loaded|complete/.test(document.readyState) ? f() : setTimeout("window.wpudebugtoolbar_r(" + f + ")", 9);
    };

    window.wpudebugtoolbar_r(function() {
        var $ = function(id) {
            return document.getElementById(id);
        };

        var toolbar = $('wputh-debug-toolbar');
        if (!$('wputh-debug-display-queries')) {
            return;
        }
        $('wputh-debug-display-queries').onclick = function() {
            toolbar.setAttribute('data-show-queries', '1');
        };
        $('wputh-debug-hide-queries').onclick = function() {
            toolbar.setAttribute('data-show-queries', '');
        };
    });

}());