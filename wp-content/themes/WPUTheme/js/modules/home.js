/* ----------------------------------------------------------
  Load more
---------------------------------------------------------- */

var wputh_ajax_load_more = function() {
    if (!$(document.body).hasClass('home') || wputh_pagination_kind != 'load-more') {
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
                'is_ajax': '1'
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