window.addEvent('domready', function() {
    if ($(document.body).hasClass('home')) {
        wputh_ajax_load_more();
    }
    $$('[href^=#]').each(function(el) {
        new dkSmoothScroll(el);
    });
    search_form_check();
    var faq_content = $('faq-content');
    if (faq_content) {
        var faq_togglers = $$('.faq-element__title');
        var faq_elements = $$('.faq-element');
        faq_togglers.each(function(el, i) {
            el.set('data-i', i);
            el.addEvent('click', function(e) {
                faq_elements.addClass('is-hidden');
                var i = el.get('data-i');
                if (faq_elements[i]) {
                    faq_elements[i].removeClass('is-hidden');
                }
            });
        });
        faq_togglers[0].fireEvent('click');
    }
});