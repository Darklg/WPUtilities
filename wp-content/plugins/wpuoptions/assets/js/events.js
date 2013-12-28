jQuery(document).ready(function($) {
    wputh_options_set_media();
    wputh_options_set_accordion();
    wputh_options_set_editor();
});

/* ----------------------------------------------------------
  Set Editor
---------------------------------------------------------- */

var wputh_options_set_editor = function() {
    jQuery('.wpuoptions-view-editor-switch').on('click', '.edit-link', function(e) {
        var $this = jQuery(this),
            $parent = $this.closest('.wpuoptions-view-editor-switch');

            $this.remove();
            $parent.find('.editor').show();
            $parent.find('.original').remove();
    });
};

/* ----------------------------------------------------------
  Upload files
---------------------------------------------------------- */

var wpuopt_file_frame,
    wpuopt_datafor;

var wputh_options_set_media = function() {
    jQuery('.wpu-options-form').on('click', '.wpuoptions_add_media', function(event) {
        event.preventDefault();
        var $this = jQuery(this);

        wpuopt_datafor = $this.data('for');

        // If the media frame already exists, reopen it.
        if (wpuopt_file_frame) {
            wpuopt_file_frame.open();
            return;
        }

        // Create the media frame.
        wpuopt_file_frame = wp.media.frames.wpuopt_file_frame = wp.media({
            title: $this.data('uploader_title'),
            button: {
                text: $this.data('uploader_button_text'),
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        wpuopt_file_frame.on('select', function() {
            // We set multiple to false so only get one image from the uploader
            var attachment = wpuopt_file_frame.state().get('selection').first().toJSON(),
                $preview = jQuery('#preview-' + wpuopt_datafor);

            // Set attachment ID
            jQuery('#' + wpuopt_datafor).attr('value', attachment.id);

            // Set preview image
            $preview.html('<img class="wpu-options-upload-preview" src="' + attachment.url + '" />');

            // Change button label
            $this.html($preview.attr('data-label'));

        });

        // Finally, open the modal
        wpuopt_file_frame.open();
    });
};

/* ----------------------------------------------------------
  Accordion
---------------------------------------------------------- */

var wputh_options_set_accordion = function() {
    var form = jQuery('.wpu-options-form'),
        boxes = form.find('.wpu-options-form__box');

    boxes.addClass('is-closed');
    boxes.eq(0).removeClass('is-closed');
    form.on('click', 'h3', function() {
        boxes.addClass('is-closed');
        jQuery(this).closest('.wpu-options-form__box').removeClass('is-closed');
    });
};