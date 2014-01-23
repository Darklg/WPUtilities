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
    var options_form = jQuery('.wpu-options-form');
    // Remove media
    options_form.on('click', '.wpu-options-upload-preview .x', function(event) {
        event.preventDefault();
        var $this = jQuery(this),
            $td = $this.closest('td'),
            divLabel = $td.find('[data-defaultlabel]'),
            defaultLabel = divLabel.attr('data-defaultlabel');

        // Asks for confirmation
        var confirm = window.confirm(divLabel.attr('data-confirm'));
        if (!confirm) {
            return false;
        }

        // Remove preview
        $td.find('.wpu-options-upload-preview').remove();

        // Empty value
        $td.find('.hidden-value').val('');

        // Set default text to button
        console.log($td.find('.wpuoptions_add_media'), defaultLabel);
        $td.find('.wpuoptions_add_media').text(defaultLabel);
    });
    // Add media
    options_form.on('click', '.wpuoptions_add_media', function(event) {
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
            $preview.html('<div class="wpu-options-upload-preview"><span class="x">&times;</span><img src="' + attachment.url + '" /></div>');

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