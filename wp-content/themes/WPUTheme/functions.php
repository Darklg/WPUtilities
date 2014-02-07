<?php
session_start();
include dirname( __FILE__ ) . '/z-protect.php';

/* Globals
-------------------------- */

define( "THEME_URL", get_template_directory_uri() );
define( 'PAGINATION_KIND', 'numbers' ); // load-more || numbers || default

/* Pages IDs
-------------------------- */

$pages_ids = array(
    'ABOUT__PAGE_ID' => 'about__page_id',
    'MENTIONS__PAGE_ID' => 'mentions__page_id',
);

foreach ( $pages_ids as $constant => $option ) {
    define( $constant, get_option( $option ) );
}

/* Menus
-------------------------- */

register_nav_menus( array(
        'main' => __( 'Main menu', 'wputh' ),
    ) );

/* Post Types
-------------------------- */

add_filter( 'wputh_get_posttypes', 'wputh_set_theme_posttypes' );
function wputh_set_theme_posttypes( $post_types ) {
    $post_types = array(
        'work' => array(
            'menu_icon' => 'http://placehold.it/16x16',
            'name' => __( 'Work', 'wputh' ),
            'plural' => __( 'Works', 'wputh' ),
            'female' => 1
        )
    );
    return $post_types;
}

/* Taxonomies
-------------------------- */

add_filter( 'wputh_get_taxonomies', 'wputh_set_theme_taxonomies' );
function wputh_set_theme_taxonomies( $taxonomies ) {
    $taxonomies = array(
        'work-type' => array(
            'name' => __( 'Work type', 'wputh' )
        )
    );
    return $taxonomies;
}

/* Sidebars
-------------------------- */

register_sidebar( array(
        'name' => __( 'Default Sidebar', 'wputh' ),
        'id' => 'wputh-sidebar',
        'description' => __( 'Default theme sidebar', 'wputh' ),
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ) );


/* Thumbnails
-------------------------- */

// Default thumbnail size
if ( function_exists( 'set_post_thumbnail_size' ) ) {
    set_post_thumbnail_size( 1200, 1200 );
}

if ( function_exists( 'add_image_size' ) ) {
    add_image_size( 'content-thumb', 300, 9999 );
}

/* ----------------------------------------------------------
  Includes
---------------------------------------------------------- */

/* Theme
-------------------------- */

include get_template_directory() . '/inc/theme/params.php';
include get_template_directory() . '/inc/theme/utilities.php';
include get_template_directory() . '/inc/theme/shortcodes.php';
include get_template_directory() . '/inc/theme/activation.php';

if ( ! isset( $content_width ) ) $content_width = 680;

/* Plugins Configuration
-------------------------- */

include get_template_directory() . '/inc/plugins/wpu-options.php';
include get_template_directory() . '/inc/plugins/wpu-postmetas.php';
include get_template_directory() . '/inc/plugins/wpu-usermetas.php';

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

/* Parameters
-------------------------- */

// Disabling admin bar
add_filter( 'show_admin_bar', '__return_false' );
