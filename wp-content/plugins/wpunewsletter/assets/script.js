jQuery(document).ready(function() {
    // Special checkbox
    jQuery(".wpunewsletter_element_check").on("change", function(e) {
        jQuery('.wpunewsletter_element, .wpunewsletter_element_check').attr('checked', jQuery(this).is(':checked'));
    });
});