<?php
$wpu_social_links = unserialize(WPU_SOCIAL_LINKS);
echo '<ul class="header__social">';
foreach ( $wpu_social_links as $id => $name ) {
    $social_link = trim( get_option( 'social_'.$id.'_url' ) );
    if ( !empty( $social_link ) ) {
        echo '<li><a rel="me" href="'.$social_link.'" class="'.$id.'" title="'.sprintf( __( '%s: Follow %s (open in new window)', 'wputh' ), $name, get_bloginfo( 'name' ) ).'" target="_blank">'.$name.'</a></li>';
    }
}
echo '</ul>';
