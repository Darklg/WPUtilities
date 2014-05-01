
/* ----------------------------------------------------------
  Search form
---------------------------------------------------------- */

var search_form_check = function() {
    var el_search_form = $('header-search');
    if (el_search_form) {
        el_search_form.addEvent('submit', function(e) {
            var input = $('s');
            if (!input || input.get('value').trim() === '') {
                e.preventDefault();
            }
        });
    }
};