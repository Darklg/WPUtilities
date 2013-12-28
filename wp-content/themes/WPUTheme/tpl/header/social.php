<?php
$social_links = array(
    'twitter' => 'Twitter',
    'facebook' => 'Facebook',
    'instagram' => 'Instagram',
);
echo '<ul class="header__social">';
foreach ( $social_links as $id => $name ) {
    $social_link = trim( get_option( 'social_'.$id.'_url' ) );
    if ( !empty( $social_link ) ) {
        echo '<li><a rel="me" href="'.$social_link.'" class="'.$id.'" title="'.sprintf( __( '%s: Follow %s (open in new window)', 'wputh' ), $name, get_bloginfo( 'name' ) ).'" target="_blank">'.$name.'</a></li>';
    }
}
echo '</ul>';
