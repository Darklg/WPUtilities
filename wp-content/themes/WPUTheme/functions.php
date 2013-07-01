<?php
include dirname( __FILE__ ) . '/z-protect.php';

/* Globals
   ----------------------- */

define( "THEME_URL", get_template_directory_uri() );

/* Theme
   ----------------------- */

include TEMPLATEPATH . '/inc/theme/support.php';
include TEMPLATEPATH . '/inc/theme/utilities.php';

/* Protection
   ----------------------- */

include TEMPLATEPATH . '/inc/protection/front.php';

/* Configuration
   ----------------------- */

include TEMPLATEPATH . '/inc/configuration/taxonomies.php';
include TEMPLATEPATH . '/inc/configuration/post-types.php';
include TEMPLATEPATH . '/inc/configuration/sidebars.php';
include TEMPLATEPATH . '/inc/configuration/menus.php';
include TEMPLATEPATH . '/inc/configuration/thumbnails.php';
include TEMPLATEPATH . '/inc/configuration/shortcodes.php';

/* Plugins Configuration
   ----------------------- */

include TEMPLATEPATH . '/inc/plugins/wpu-options.php';
include TEMPLATEPATH . '/inc/plugins/wputh_thumbnails.php';

/* Assets
   ----------------------- */

include TEMPLATEPATH . '/inc/assets/styles.php';
include TEMPLATEPATH . '/inc/assets/scripts.php';

/* Langs
   ----------------------- */

add_action( 'after_setup_theme', 'wputh_setup' );
function wputh_setup() {
    load_theme_textdomain( 'wputh', get_template_directory() . '/inc/lang' );
}

/* Parameters
   ----------------------- */

// Disabling admin bar
add_filter( 'show_admin_bar', '__return_false' );
