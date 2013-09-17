<?php
/*
Plugin Name: Google Analytics
Description: Display Google Analytics
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

add_action('wp_footer', 'wputh_display_google_analytics_code');

function wputh_display_google_analytics_code() {
    $ua_analytics = get_option('wputh_ua_analytics');
    if ($ua_analytics !== false && !empty($ua_analytics) && !in_array($ua_analytics, array('UA-XXXXX-X'))) {
        // List of vars to send to Google Analytics
        $gaq = array();
        // Analytics code used
        $gaq['account'] = "['_setAccount','".$ua_analytics."']";
        // Default action : page viewed
        $gaq['trackpageview'] = "['_trackPageview']";
        if (is_404()) {
            // Tracking 404 errors | http://www.joshstauffer.com/track-404s-with-google-analytics/
            $gaq['trackpageview'] = "['_trackPageview', '/404?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]";
        }

        echo '<script type="text/javascript">';
        echo 'var _gaq=_gaq||[];';
        foreach ($gaq as $_gaq) {
            echo '_gaq.push('.$_gaq.');';
        }
        echo "(function(){".
            "var ga=document.createElement('script');".
            "ga.type='text/javascript';".
            "ga.async=true;".
            "ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';".
            "var s=document.getElementsByTagName('script')[0];".
            "s.parentNode.insertBefore(ga,s);".
            "})();";
        echo '</script>';
    }

}
