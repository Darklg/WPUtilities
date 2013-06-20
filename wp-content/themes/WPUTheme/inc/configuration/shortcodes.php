<?php

// Columns
function columns_shortcode( $atts, $content = null ) {
   return '<div class="post-content-columns">' . $content . '</div>';
}
add_shortcode( 'columns', 'columns_shortcode' );