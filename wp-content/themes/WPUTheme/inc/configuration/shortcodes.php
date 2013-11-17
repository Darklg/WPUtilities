<?php

/* ----------------------------------------------------------
  [columns]Text on multiple columns[/columns]
---------------------------------------------------------- */

function wpu_columns_shortcode( $atts, $content = null ) {
    return '<div class="post-content-columns">' . $content . '</div>';
}
add_shortcode( 'columns', 'wpu_columns_shortcode' );

/* ----------------------------------------------------------
  [googlemap]8 Rue de Londres, 75009 Paris, France[/googlemap]
---------------------------------------------------------- */

function wpu_googlemap_shortcode( $atts, $content = null ) {
    $width = isset( $atts['width'] ) ? $atts['width'] : 640;
    $height = isset( $atts['height'] ) ? $atts['height'] : 480;
    return '<iframe width="'.$width.'" height="'.$height.'" src="http://maps.google.com/maps?q='.urlencode( $content ).'&output=embed"></iframe>';
}
add_shortcode( "googlemap", "wpu_googlemap_shortcode" );
