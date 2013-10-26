<?php
session_start();
include dirname( __FILE__ ) . '/z-protect.php';

/* Globals
-------------------------- */

define( "THEME_URL", get_template_directory_uri() );
define( 'PAGINATION_KIND', 'numbers' ); // load-more || numbers || default

/* Theme
-------------------------- */

include get_template_directory() . '/inc/theme/params.php';
include get_template_directory() . '/inc/theme/utilities.php';
include get_template_directory() . '/inc/theme/protect.php';
include get_template_directory() . '/inc/theme/clean.php';
if ( ! isset( $content_width ) ) $content_width = 680;


/* Configuration
-------------------------- */

include get_template_directory() . '/inc/configuration/activation.php';
include get_template_directory() . '/inc/configuration/taxonomies.php';
include get_template_directory() . '/inc/configuration/post-types.php';
include get_template_directory() . '/inc/configuration/sidebars.php';
include get_template_directory() . '/inc/configuration/menus.php';
include get_template_directory() . '/inc/configuration/thumbnails.php';
include get_template_directory() . '/inc/configuration/shortcodes.php';

/* Plugins Configuration
-------------------------- */

include get_template_directory() . '/inc/plugins/wpu-options.php';
include get_template_directory() . '/inc/plugins/wpu-postmetas.php';

/* Assets
-------------------------- */

include get_template_directory() . '/inc/assets/styles.php';
include get_template_directory() . '/inc/assets/scripts.php';

/* Widgets
-------------------------- */

include get_template_directory() . '/tpl/widgets/widget_post_categories.php';

/* Langs
-------------------------- */

add_action( 'after_setup_theme', 'wputh_setup' );

function wputh_setup() {
    load_theme_textdomain( 'wputh', get_template_directory() . '/inc/lang' );
}

/* Pages IDs
-------------------------- */

$pages_ids = array(
    'ABOUT__PAGE_ID' => 'about__page_id'
);

foreach ( $pages_ids as $constant => $option ) {
    define( $constant, get_option( $option ) );
}

/* Parameters
-------------------------- */

// Disabling admin bar
add_filter( 'show_admin_bar', '__return_false' );
