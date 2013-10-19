// Uploading files
var wpuopt_file_frame;

jQuery(document).ready(function() {
    jQuery('.wpu-options-form').on('click', '.wpuoptions_add_media', function(event) {
        event.preventDefault();
        var $this = jQuery(this);

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
            attachment = wpuopt_file_frame.state().get('selection').first().toJSON();

            var datafor = $this.data('for');

            // Set attachment ID
            jQuery('#' + datafor).attr('value', attachment.id);

            // Set preview image
            jQuery('#preview-' + datafor).html('<img class="wpu-options-upload-preview" src="' + attachment.url + '" />');

        });

        // Finally, open the modal
        wpuopt_file_frame.open();
    });
});