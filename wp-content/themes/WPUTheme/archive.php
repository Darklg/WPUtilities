<?php
include dirname( __FILE__ ) . '/z-protect.php';
get_header();

$shown_title = __( 'Archive', 'wputh' );
if ( is_tag() ) {
    $shown_title = __( 'Tag:', 'wputh' ).' '.single_cat_title( "", false );
}
if ( is_category() ) {
    $shown_title = __( 'Category:', 'wputh' ).' '.single_cat_title( "", false );
}
if ( is_author() ) {
    $curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $author_name ) : get_userdata( intval( $author ) );
    $shown_title = __( 'Author:', 'wputh' ).' '.$curauth->nickname;
}

echo '<div class="main">';
echo '<h1>'.$shown_title.'</h1>';
echo get_the_loop();
echo '</div>';
get_sidebar();
get_footer();
