<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

$shown_title = __( 'Archive', 'wputh' );
if ( is_tag() ) {
    $shown_title = __( 'Tag:', 'wputh' ) . ' ' . single_tag_title( "", false );
}
if ( is_category() ) {
    $shown_title = __( 'Category:', 'wputh' ) . ' ' . single_cat_title( "", false );
}
if ( is_author() ) {
    $curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author ) );
    $shown_title = __( 'Author:', 'wputh' ) . ' ' . $curauth->nickname;
}
if ( is_year() ) {
    $shown_title = __( 'Year:', 'wputh' ) . ' ' . get_the_time( __( 'Y', 'wputh' ) );
}
if ( is_month() ) {
    $shown_title = __( 'Month:', 'wputh' ) . ' ' . get_the_time( __( 'F Y', 'wputh' ) );
}
if ( is_day() ) {
    $shown_title = __( 'Day:', 'wputh' ) . ' ' . get_the_time( __( 'F j, Y', 'wputh' ) );
}

echo '<div class="main">';
echo '<h1>'.$shown_title.'</h1>';
if ( is_year() || is_month() || is_day()) {
    include get_template_directory() . '/tpl/archive/dates-navigation.php';
}
echo get_the_loop();
echo '</div>';
get_sidebar();
get_footer();
