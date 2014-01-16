jQuery(document).ready(function() {
    var attachments_select = jQuery('.wpupostmetas-attachments');
    // Display image preview
    attachments_select.on('change', function() {
        wpupostmetas_setattachmentpreview(jQuery(this));
    });
    attachments_select.each(function() {
        var self = jQuery(this);
        wpupostmetas_setattachmentpreview(self);
        wpupostmetas_setattachmentrefresh(self);
    });
});

/* ----------------------------------------------------------
  Set refresh event
---------------------------------------------------------- */

var wpupostmetas_setattachmentrefresh = function(self) {
    var refresh = jQuery('<span class="wpupostmetas-attachments__refresh"></span>'),
        sparent = self.parent();
    sparent.append(refresh);
    // Refresh on click
    refresh.on('click', function(e) {
        /* Disable click */
        sparent.addClass('is-disabled');
        e.preventDefault();
        jQuery.post(ajaxurl, {
            action: 'wpupostmetas_attachments',
            post_id: self.attr('data-postid'),
            post_value: self.attr('data-postvalue'),
        }, function(response) {
            self.html(response);
            /* Enable click */
            sparent.removeClass('is-disabled');
            /* Reload select */
            wpupostmetas_setattachmentpreview(self);
        });
    });
};

/* ----------------------------------------------------------
  Set attachment preview image
---------------------------------------------------------- */

var wpupostmetas_setattachmentpreview = function(self) {
    var selected_value = self.find(':selected'),
        guid = false,
        img = false,
        prev = false;
    if (!selected_value) {
        selected_value = self.find('[data-guid]');
    }
    guid = selected_value.attr('data-guid');
    if (guid) {
        img = '<img src="' + guid + '" alt="" />';
    }
    if (!img) {
        img = '';
    }
    prev = self.closest('td').find('.preview-img');
    if (prev) {
        prev.html(img);
    }
};