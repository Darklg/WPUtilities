<?php

include dirname( __FILE__ ) . '/z-protect.php';

/* Globals
-------------------------- */

define( "THEME_URL", get_template_directory_uri() );
define( 'PAGINATION_KIND', 'load-more'); // load-more || numbers || default

/* Theme
-------------------------- */

include get_template_directory() . '/inc/theme/params.php';
include get_template_directory() . '/inc/theme/utilities.php';

/* Protection
-------------------------- */

include get_template_directory() . '/inc/protection/front.php';

/* Configuration
-------------------------- */

include get_template_directory() . '/inc/configuration/taxonomies.php';
include get_template_directory() . '/inc/configuration/post-types.php';
include get_template_directory() . '/inc/configuration/sidebars.php';
include get_template_directory() . '/inc/configuration/menus.php';
include get_template_directory() . '/inc/configuration/thumbnails.php';
include get_template_directory() . '/inc/configuration/shortcodes.php';

/* Plugins Configuration
   ----------------------- */

include get_template_directory() . '/inc/plugins/wpu-options.php';
include get_template_directory() . '/inc/plugins/wputh_thumbnails.php';
include get_template_directory() . '/inc/plugins/wputh-post-metas.php';

/* Assets
-------------------------- */

include get_template_directory() . '/inc/assets/styles.php';
include get_template_directory() . '/inc/assets/scripts.php';

/* Langs
-------------------------- */

add_action( 'after_setup_theme', 'wputh_setup' );

function wputh_setup() {
    load_theme_textdomain( 'wputh', get_template_directory() . '/inc/lang' );
}


/* Parameters
-------------------------- */

// Disabling admin bar
add_filter( 'show_admin_bar', '__return_false' );
