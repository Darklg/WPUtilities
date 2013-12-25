jQuery(document).ready(function($) {
    wputh_taxometas_set_media();
});

/* ----------------------------------------------------------
  Upload files
---------------------------------------------------------- */

var wputaxometas_file_frame,
    wputaxometas_datafor;

var wputh_taxometas_set_media = function() {
    jQuery('.wpu-taxometas-form').on('click', '.wputaxometas_add_media', function(event) {
        event.preventDefault();
        var $this = jQuery(this);

        wputaxometas_datafor = $this.data('for');

        // If the media frame already exists, reopen it.
        if (wputaxometas_file_frame) {
            wputaxometas_file_frame.open();
            return;
        }

        // Create the media frame.
        wputaxometas_file_frame = wp.media.frames.wputaxometas_file_frame = wp.media({
            title: $this.data('uploader_title'),
            button: {
                text: $this.data('uploader_button_text'),
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        wputaxometas_file_frame.on('select', function() {
            // We set multiple to false so only get one image from the uploader
            var attachment = wputaxometas_file_frame.state().get('selection').first().toJSON(),
                $preview = jQuery('#preview-' + wputaxometas_datafor);

            // Set attachment ID
            jQuery('#' + wputaxometas_datafor).attr('value', attachment.id);

            // Set preview image
            $preview.html('<img class="wpu-taxometas-upload-preview" src="' + attachment.url + '" />');

            // Change button label
            $this.html($preview.attr('data-label'));

        });

        // Finally, open the modal
        wputaxometas_file_frame.open();
    });
};