window.addEvent('domready', function() {
    $(document.body).removeClass('no-js');

    /* All */
    $$('[href^=#]').each(function(el) {
        new dkSmoothScroll(el);
    });
    search_form_check();

    /* Home */
    wputh_ajax_load_more();

    /* FAQ */
    wpu_set_faq_accordion();

});