window.addEvent('domready', function() {
    if ($(document.body).hasClass('home')) {
        wputh_ajax_load_more();
    }
    $$('[href^=#]').each(function(el) {
        new dkSmoothScroll(el);
    });
    search_form_check();
});
