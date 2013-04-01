<?php

/* Configuration
   ----------------------- */

include TEMPLATEPATH . '/inc/configuration/taxonomies.php';
include TEMPLATEPATH . '/inc/configuration/post-types.php';

/* Medias
   ----------------------- */

include TEMPLATEPATH . '/inc/medias/styles.php';

/* Parameters
   ----------------------- */

// Disabling admin bar
add_filter( 'show_admin_bar', '__return_false' );
