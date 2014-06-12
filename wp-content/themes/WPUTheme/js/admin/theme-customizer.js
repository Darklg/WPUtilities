/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
(function($) {
    // Update the site title in real time...
    wp.customize('blogname', function(value) {
        value.bind(function(newval) {
            $('.main-title a').html(newval);
        });
    });
    //Update the site description in real time...
    wp.customize('blogdescription', function(value) {
        value.bind(function(newval) {
            $('.site-description').html(newval);
        });
    });
    //Update site background color...
    wp.customize('wpu_background_color', function(value) {
        value.bind(function(newval) {
            $('body').css('background-color', newval);
        });
    });
    //Update site link color in real time...
    wp.customize('wpu_link_color', function(value) {
        value.bind(function(newval) {
            $('a').css('color', newval);
        });
    });
})(jQuery);