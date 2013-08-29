/* ----------------------------------------------------------
  Load more
---------------------------------------------------------- */

var wputh_ajax_load_more = function() {
    if (wputh_pagination_kind != 'load-more') {
        return;
    }
    $$('.load-more').addEvent('click', function(e) {
        if (e.shiftKey || e.altKey || e.ctrlKey) {
            return;
        }
        e.preventDefault();
        var self = $(this),
            main = $('main');
        if (self.hasClass('is-clicked')) {
            return;
        }
        var req = new Request.HTML({
            method: 'post',
            url: self.get('href'),
            data: {
                'ajax': '1'
            },
            onRequest: function() {
                self.addClass('is-clicked');
                self.set('disabled', 'disabled');
            },
            onSuccess: function(a, b, responseHTML, responseJS) {
                var elResponse = new Element('div').set('html', responseHTML),
                    posts = elResponse.getElements('.loop-small'),
                    target = $$('.list-loops'),
                    loadmorelink = elResponse.getElements('.load-more');

                // Obtaining datas from page
                eval(responseJS);

                // If we can load new posts
                if (target[0]) {

                    // Pushing new URL
                    if (history.pushState) {
                        history.pushState({}, wputh_page_title, self.get('href'));
                    }
                    // Adopting each post.
                    // "each" is used for an eventual animation.
                    posts.each(function(el) {
                        target[0].adopt(el);
                    });
                }
                // If there is a next page : set link
                if (loadmorelink[0]) {
                    self.removeClass('is-clicked');
                    self.set('href', loadmorelink[0]);
                }
                else {
                    self.remove();
                }

            }
        }).send();
    });
};

/* ----------------------------------------------------------
  Search form
---------------------------------------------------------- */

var search_form_check = function() {
    var el_search_form = $('header-search');
    if (el_search_form) {
        el_search_form.addEvent('submit', function(e) {
            var input = $('header-search__input');
            if (!input || input.get('value').trim() === '') {
                e.preventDefault();
            }
        });
    }
};